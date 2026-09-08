<?php
require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use App\Sistema\models\reportesentradas;
use App\Sistema\models\Usuarios;
use Dompdf\Dompdf;

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Reportes"; 

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['permisos'])) {
    header('Content-Type: application/json');
    echo json_encode([
        "control_total" => $obj_usuario->tienePermiso($modulo_actual, "control_total")
    ]);
    exit();
}

if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtenerDatosGraficos') {
    header('Content-Type: application/json');
    
    $obj_reportes = new reportesentradas();
    
    $inicio = $_GET['fecha_inicio'] ?? null;
    $fin = $_GET['fecha_fin'] ?? null;

    $dataCategorias = $obj_reportes->obtenerEntradasPorCategoria($inicio, $fin);
    $dataProductos = $obj_reportes->obtenerTopProductos($inicio, $fin);

    echo json_encode([
        'categorias' => $dataCategorias,
        'productos' => $dataProductos
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar_pdf_entradas'])) {
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    
    $fecha = trim($_POST['fecha'] ?? '');
    $producto = trim($_POST['producto'] ?? '');

    $obj_reporte = new reportesentradas(); 
    $datos = $obj_reporte->obtenerReporteDetallado($fecha, $producto);

    if (empty($datos)) {
        header("Location: ?pagina=reporteEntradas&error=no_data");
        exit();
    }

    $data_to_pdf = $datos;

    ob_start();
    include "assets/comunes/PDF/PDFReporteEntradas.php";
    $html = ob_get_clean();

    if (headers_sent()) {
        die("Error: Headers ya enviados.");
    }

    $dompdf = new Dompdf();
    $dompdf->set_option('isRemoteEnabled', true);
    $dompdf->loadHtml($html);
    $dompdf->setPaper("A4", "landscape");
    $dompdf->render();

    $nombreArchivo = 'Reporte_Entradas_' . date('Ymd_His') . '.pdf';

    ob_end_clean();
    $dompdf->stream($nombreArchivo, ["Attachment" => false]);
    unset($obj_reporte);
    exit();
}

require_once 'app/views/reporteEntradas.php';
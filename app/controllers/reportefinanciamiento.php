<?php
require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use App\Sistema\models\Reportefinanciamiento;
use App\Sistema\models\Usuarios;
use Dompdf\Dompdf;

if (session_status() === PHP_SESSION_NONE) session_start();

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!$cedula || !$rol) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Reportes";

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['permisos'])) {
    header('Content-Type: application/json');
    echo json_encode([
        "registrar" => $obj_usuario->tienePermiso($modulo_actual, "registrar"),
        "modificar" => $obj_usuario->tienePermiso($modulo_actual, "modificar"),
        "eliminar"  => $obj_usuario->tienePermiso($modulo_actual, "eliminar"),
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
    $obj_reporte = new Reportefinanciamiento();
    
    $filtro_cedula = $_GET['cedula'] ?? null;
    $filtro_estado = $_GET['estado'] ?? null;
    $filtro_desde = $_GET['fecha_desde'] ?? null;
    $filtro_hasta = $_GET['fecha_hasta'] ?? null;
    $filtro_monto_min = $_GET['monto_min'] ?? null;
    $filtro_monto_max = $_GET['monto_max'] ?? null;
    $filtro_ordenar = $_GET['ordenar_por'] ?? 'cantidad';

    $data = [
        'estados' => $obj_reporte->obtenerConteoFinanciamientosPorEstado($filtro_cedula, $filtro_estado, $filtro_desde, $filtro_hasta, $filtro_monto_min, $filtro_monto_max),
        'cuotas'  => $obj_reporte->obtenerCuotasPorMes($filtro_desde, $filtro_hasta),
        'ranking' => $obj_reporte->obtenerReporteAvanzado([
            'cedula'      => $filtro_cedula,
            'estado'      => $filtro_estado,
            'fecha_desde' => $filtro_desde,
            'fecha_hasta' => $filtro_hasta,
            'monto_min'   => $filtro_monto_min,
            'monto_max'   => $filtro_monto_max,
            'ordenar_por' => $filtro_ordenar
        ])
    ];
    echo json_encode($data);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar2'])) {
    $obj_reporte = new Reportefinanciamiento(); 
    
    $datos_financiamiento = $obj_reporte->obtenerReporteFinanciamientoFiltro(
        trim($_POST['cedula'] ?? ''), 
        trim($_POST['estado'] ?? ''), 
        trim($_POST['fecha_desde'] ?? ''), 
        trim($_POST['fecha_hasta'] ?? '')
    );

    if (empty($datos_financiamiento)) {
        $datos_financiamiento = [];
    }

    $data_to_pdf = $datos_financiamiento;
    $ruta_plantilla = dirname(__DIR__, 2) . '/assets/comunes/PDF/PDFFinanciamiento.php';

    ob_start();
    include $ruta_plantilla;
    $html = ob_get_clean();

    $dompdf = new Dompdf(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);
    $dompdf->loadHtml($html);
    $dompdf->setPaper("A4", "portrait");
    $dompdf->render();
    $dompdf->stream('Reporte_Financiamiento_' . date('Ymd_His') . '.pdf', ["Attachment" => false]);
    exit();
}

include 'app/views/reportefinanciamiento.php';
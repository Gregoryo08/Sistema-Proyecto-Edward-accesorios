<?php

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use App\Sistema\models\ReporteVentasOnline;
use App\Sistema\models\Usuarios;
use Dompdf\Dompdf;
use Dompdf\Options;

if (session_status() === PHP_SESSION_NONE) session_start();

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION['rol'] ?? null;
if (!$cedula || !$rol) {
    header('Location: ?pagina=iniciarSesion');
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = 'Administrar Reportes';
if (!$obj_usuario->tienePermiso($modulo_actual, 'listar')) {
    if (($_GET['accion'] ?? '') === 'listar') {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'data' => [],
            'mensaje' => 'No tienes permiso para consultar este reporte.'
        ]);
        exit();
    }
    header('Location: ?pagina=principal');
    exit();
}

$obj_reporte = new ReporteVentasOnline();

$filtros = [
    'buscar'        => trim($_GET['buscar'] ?? ''),
    'estado_pedido' => trim($_GET['estado_pedido'] ?? ''),
    'estado_pago'   => trim($_GET['estado_pago'] ?? ''),
    'metodo_pago'   => trim($_GET['metodo_pago'] ?? ''),
    'fecha_inicio'  => trim($_GET['fecha_inicio'] ?? ''),
    'fecha_fin'     => trim($_GET['fecha_fin'] ?? '')
];

if (($_GET['accion'] ?? '') === 'listar') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'data' => $obj_reporte->listar($filtros),
        'metodos_pago' => $obj_reporte->listarMetodosPago()
    ]);
    exit();
}

if (($_GET['accion'] ?? '') === 'exportarPdf') {
    $data_to_pdf = $obj_reporte->listar($filtros);
    ob_start();
    require dirname(__DIR__, 2) . '/assets/comunes/PDF/PDFReporteVentasOnline.php';
    $html = ob_get_clean();

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('Reporte_Ventas_Online_' . date('Ymd_His') . '.pdf', ['Attachment' => false]);
    exit();
}

require_once 'app/views/reporteVentasOnline.php';
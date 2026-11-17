<?php

namespace app\controllers;
use App\Sistema\models\Usuarios;
use App\Sistema\models\reporteVentas;

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Ventas";
$obj_reporte = new reporteVentas();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['permisos'])) {
    echo json_encode([
        "registrar" => $obj_usuario->tienePermiso($modulo_actual, "registrar"),
        "modificar" => $obj_usuario->tienePermiso($modulo_actual, "modificar"),
        "eliminar"  => $obj_usuario->tienePermiso($modulo_actual, "eliminar")
    ]);
    exit();
}

$accion = $_GET['accion'] ?? $_POST['accion'] ?? null;

if ($accion !== null) {

    if ($accion !== 'exportarPdf') {
        header('Content-Type: application/json');
    }

    switch ($accion) {
        case 'listarVentas':
            $resultado = $obj_reporte->procesarSolicitud($accion);
            echo json_encode([
                "success" => true,
                "data" => $resultado
            ]);
            break;

        case 'detallesVentas':
            $id_venta = $_GET['id_venta'] ?? null;

            if ($id_venta === null) {
                echo json_encode(["success" => false, "mensaje" => "Falta el ID de la venta."]);
                break;
            }

            $resultado = $obj_reporte->procesarSolicitud($accion, $id_venta);
            echo json_encode([
                "success" => true,
                "data" => $resultado
            ]);
            break;

        case 'anularVenta':
            $id_venta = $_POST['id_venta'] ?? null;

            if ($id_venta === null) {
                echo json_encode(["success" => false, "mensaje" => "Falta el ID de la venta para procesar la anulación."]);
                break;
            }

            $datos['cedula_usuario'] = $cedula;

            if (empty($datos['cedula_usuario'])) {
                echo json_encode(["success" => false, "mensaje" => "Error de sesión: No se localizó la cédula del operador de caja."]);
                exit();
            }

            $resultado = $obj_reporte->procesarSolicitud($accion, $id_venta, $datos);

            echo json_encode($resultado);
            break;

        case 'exportarPdf':

            $filtros = [
                'buscar'       => $_GET['buscar'] ?? '',
                'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
                'fecha_fin'    => $_GET['fecha_fin'] ?? '',
                'origen'       => $_GET['origen'] ?? '',
                'estado'       => $_GET['estado'] ?? ''
            ];

            if ($filtros['origen'] === 'todos' || $filtros['origen'] === 'Seleccione Origen') {
                $filtros['origen'] = '';
            }
            if ($filtros['estado'] === 'todos' || $filtros['estado'] === 'Seleccione Estado') {
                $filtros['estado'] = '';
            }

            $data_to_pdf = $obj_reporte->procesarSolicitud('listarVentas', null, $filtros);

            ob_start();
            require_once('assets/comunes/PDF/reporteVentasPDF.php');
            $html = ob_get_clean();

            try {
                $options = new \Dompdf\Options();
                $options->set('isHtml5ParserEnabled', true);
                $options->set('isRemoteEnabled', true);

                $dompdf = new \Dompdf\Dompdf($options);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('letter', 'portrait');
                $dompdf->render();

                $dompdf->stream("Reporte_Ventas_" . date("d-m-Y") . ".pdf", array("Attachment" => false));
                exit();
            } catch (\Exception $e) {
                header("HTTP/1.0 500 Internal Server Error");
                error_log('reporteVentas: ' . $e->getMessage());
                $ruta_vista = '';
                require_once 'app/views/404.php';
            }
            break;

        default:
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "mensaje" => "Acción no permitida en este controlador."]);
            break;
    }
    exit();
}

$ruta_vista = "app/views/reporteVentas.php";

if (file_exists($ruta_vista)) {
    require_once $ruta_vista;
} else {
    header("HTTP/1.0 404 Not Found");
    require_once 'app/views/404.php';
    exit();
}

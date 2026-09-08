<?php

use App\Sistema\models\cliente_financiamiento;
use App\Sistema\models\Usuarios;
use App\Sistema\models\scrape_dolar;
use App\Sistema\models\principal;

$action = $_GET['action'] ?? null;

if ($action === 'DatosDashboardCliente') {
    header('Content-Type: application/json; charset=utf-8');
    
    $cedulaAjax = $_SESSION['username'] ?? $_SESSION['cliente_cedula'] ?? null;
    
    if (!$cedulaAjax) {
        echo json_encode(["error" => "Sesion no encontrada o expirada"]);
        exit();
    }

    $modeloAjax = new principal();
    $datos = $modeloAjax->obtenerDatosDashboardCliente($cedulaAjax);
    echo json_encode($datos);
    exit();
}

$cedula = $_SESSION['username'] ?? $_SESSION['cliente_cedula'] ?? null;
$rol = $_SESSION["rol"] ?? '6';
$_SESSION["rol"] = $rol;

if (!$cedula) {
    header("Location: ?pagina=loginEcommerce");
    exit();
}


$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Pago De Cuotas";

if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

$objeto_cliente = new cliente_financiamiento($cedula);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['permisos'])) {
    header('Content-Type: application/json');
    echo json_encode([
        "listar" => $obj_usuario->tienePermiso($modulo_actual, "listar"),
        "registrar_pago" => $obj_usuario->tienePermiso($modulo_actual, "registrar_pago")
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    if (ob_get_length()) ob_clean(); 
    header('Content-Type: application/json');
    
    $opcion = $_GET['x'] ?? '';

    switch ($opcion) {
        case "listado":
            echo json_encode($objeto_cliente->procesar('listado'));
            break;
        case "metodos":
            echo json_encode($objeto_cliente->procesar('listarMetodos'));
            break;
        case "bancos":
            echo json_encode($objeto_cliente->procesar('listarBancos'));
            break;
        case "historial":
            $id_fin = filter_var($_GET['id_financiamiento'] ?? 0, FILTER_VALIDATE_INT);
            echo json_encode($objeto_cliente->procesar('listarHistorialCuotas', $id_fin));
            break;
        case "tasa":
            $tasaModel = new scrape_dolar();
            echo json_encode($tasaModel->obtenerPrecioDolarBCV());
            break;
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    header('Content-Type: application/json');
    $accion = $_POST['accion'];

    if ($accion === 'registrarPago' && !$obj_usuario->tienePermiso($modulo_actual, "registrar_pago")) {
        echo json_encode(["error" => "No tienes permisos"]);
        exit();
    }

    switch ($accion) {
        case 'registrarPago':
            $id_cuota = filter_var($_POST['id_cuota'] ?? 0, FILTER_VALIDATE_INT);
            $monto = filter_var($_POST['monto'] ?? 0, FILTER_VALIDATE_FLOAT);
            $id_metodo = filter_var($_POST['id_metodo'] ?? 0, FILTER_VALIDATE_INT);
            $id_banco = filter_var($_POST['id_banco'] ?? 0, FILTER_VALIDATE_INT);
            $referencia = filter_var($_POST['referencia'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $fecha = filter_var($_POST['fecha'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

            if (!$id_cuota || !$monto || !$id_metodo || !$id_banco || empty($referencia) || empty($fecha)) {
                echo json_encode(["error" => "Datos incompletos o inválidos"]);
                exit();
            }

            $fecha_limite_minima = date('Y-m-d', strtotime('-30 days'));
            $fecha_hoy = date('Y-m-d');

            if ($fecha > $fecha_hoy || $fecha < $fecha_limite_minima) {
                echo json_encode(["error" => "La fecha de pago no es válida"]);
                exit();
            }

            $datos = [
                'id_cuota' => $id_cuota,
                'monto' => $monto,
                'id_metodo' => $id_metodo,
                'id_banco' => $id_banco,
                'referencia' => $referencia,
                'fecha' => $fecha
            ];
            echo json_encode($objeto_cliente->procesar('registrarPago', $datos));
            break;
    }
    exit();
}

$vista = 'App/views/cliente_financiamiento.php';

if (file_exists($vista)) {
    require_once $vista;
} else {
    require_once 'App/views/error_404.php';
}
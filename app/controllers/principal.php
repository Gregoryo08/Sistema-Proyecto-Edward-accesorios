<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Sistema\models\Usuarios;
use App\Sistema\models\principal;

$action = $_GET['action'] ?? null;

if ($action === 'DatosDashboardCliente') {
    header('Content-Type: application/json; charset=utf-8');
    
    $cedulaAjax = $_SESSION['username'] ?? null;
    
    if (!$cedulaAjax) {
        echo json_encode(["error" => "Sesion no encontrada o expirada"]);
        exit();
    }

    $modeloAjax = new principal();
    $datos = $modeloAjax->obtenerDatosDashboardCliente($cedulaAjax);
    echo json_encode($datos);
    exit();
}

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!$cedula || !$rol) {
    header("Location: ?url=iniciarSesion");
    exit();
}

$modelo = new principal();

if ($action === 'Cargaringreso') {
    header('Content-Type: application/json');
    echo json_encode($modelo->Cargaringreso());
    exit();
}

if ($action === 'VentasRecientes') {
    header('Content-Type: application/json');
    echo json_encode($modelo->VentasRecientes());
    exit();
}

if ($action === 'TotalesHoy') {
    header('Content-Type: application/json');
    echo json_encode($modelo->ObtenerTotalesHoy());
    exit();
}

$obj_usuario = new Usuarios();
$obj_usuario->setCedula(trim($cedula));

$datos_personales = $modelo->obtenerDatosPersonales($cedula);

if ($datos_personales && isset($datos_personales["nombre"], $datos_personales["apellido"])) {
    $nombre = htmlspecialchars($datos_personales["nombre"], ENT_QUOTES, 'UTF-8');
    $apellido = htmlspecialchars($datos_personales["apellido"], ENT_QUOTES, 'UTF-8');
    $_SESSION["nombre_completo"] = $nombre . " " . $apellido;
} else {
    $_SESSION["nombre_completo"] = "USUARIO";
}

$rolCrudo = is_array($rol) ? ($rol['descripcion_rol'] ?? '') : $rol;
$rolLimpio = strtolower(trim((string)$rolCrudo));

if ($rolLimpio === 'cliente' || $rolLimpio === '6') {
    $vista = 'app/views/pagina_cliente.php';
} else {
    $vista = 'app/views/principal.php';
}

if (file_exists($vista)) {
    require_once $vista;
} else {
    require_once 'app/views/error_404.php';
}
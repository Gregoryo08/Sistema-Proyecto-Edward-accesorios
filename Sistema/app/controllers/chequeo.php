<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\chequeo; 

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'enviarSolicitud') {
    $objeto_venta = new chequeo();
    $respuesta = $objeto_venta->registrarSolicitud([
        "nombre"      => trim($_POST['nombre']),
        "telefono"    => trim($_POST['telefono']),
        "direccion"   => trim($_POST['direccion']),
        "metodo_pago" => $_POST['metodo'],
        "total"       => $_POST['total'],
        "productos"   => $_POST['productos'] ?? []
    ]);
    echo json_encode($respuesta);
    exit();
}

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Chequeo"; 

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['permisos'])) {
    echo json_encode([
        "registrar" => $obj_usuario->tienePermiso($modulo_actual, "registrar"),
        "consultar" => $obj_usuario->tienePermiso($modulo_actual, "consultar"),
        "modificar" => $obj_usuario->tienePermiso($modulo_actual, "modificar"),
        "eliminar"  => $obj_usuario->tienePermiso($modulo_actual, "eliminar"),
        "control_total" => $obj_usuario->tienePermiso($modulo_actual, "control_total")
    ]);
    exit();
}

if (!$obj_usuario->tienePermiso($modulo_actual, "consultar")) {
    header("Location: ?pagina=principal");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['accion'] === 'listarSolicitudes') {
    $objeto_venta = new chequeo();
    echo json_encode($objeto_venta->listarSolicitudes());
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'obtenerDetalleProductos') {
    $objeto_venta = new chequeo();
    echo json_encode($objeto_venta->obtenerDetalle($_POST['id']));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'gestionarPedido') {
    $objeto_venta = new chequeo();
    $respuesta = $objeto_venta->gestionarSolicitud($_POST);
    echo json_encode($respuesta);
    exit();
}

include 'app/views/chequeo.php';
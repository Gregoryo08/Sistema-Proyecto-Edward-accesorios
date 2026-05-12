<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\ventas;

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Ventas";

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'verDetalleVenta') {
    $modelo = new ventas();
    echo json_encode($modelo->obtenerDetalleVenta($_POST['id']));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax'])) {
    $modelo = new ventas();
    if ($_GET['accion'] === 'listarVentasRealizadas') {
        echo json_encode($modelo->listarVentasHistorial());
    } else if ($_GET['accion'] === 'buscarProductos') {
        echo json_encode($modelo->buscarProductos($_GET['q'] ?? ''));
    } else if ($_GET['accion'] === 'listarMetodos') {
        echo json_encode($modelo->listarMetodos());
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrarVenta') {
    $modelo = new ventas();
    echo json_encode($modelo->registrarVentaDirecta($_POST));
    exit();
}

include 'app/views/ventas.php';
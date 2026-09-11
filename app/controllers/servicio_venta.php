<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\servicio_venta;

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION['rol'] ?? null;

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Servicio Venta";

if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['permisos'])) {
    echo json_encode([
        "consultar"  => $obj_usuario->tienePermiso($modulo_actual, "consultar"),
        "control_total" => $obj_usuario->tienePermiso($modulo_actual, "control_total")
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['accion'] === 'listarServicioVenta') {
    $modelo = new servicio_venta();
    echo json_encode($modelo->listar());
    exit();
}

include 'app/views/servicio_venta.php';

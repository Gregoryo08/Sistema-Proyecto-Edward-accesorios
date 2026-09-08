<?php

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

use App\Sistema\models\Usuarios;


$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;


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


header('Location: index.php?pagina=cajeraPanel');
exit;
?>
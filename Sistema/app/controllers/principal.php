<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\tazas;


$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?url=iniciarSesion");
    exit();
}


$obj_usuario = new Usuarios();
$obj_usuario->setCedula(trim($cedula)); 


$datos_personales = $obj_usuario->obtenerDatosPersonales();
if ($datos_personales) {
    $_SESSION["nombre_completo"] = $datos_personales["nombre"] . " " . $datos_personales["apellido"];
}




include 'app/views/principal.php';
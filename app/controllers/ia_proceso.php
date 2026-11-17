<?php

use App\Sistema\models\Usuarios;

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

// Solo rol 1 y rol 2 pueden acceder al módulo
if ((int)$rol !== 1 && (int)$rol !== 2) {
    header("Location: ?pagina=principal");
    exit();
}


$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Financiamiento";


if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

require_once 'app/config/ia.php';

require_once 'app/views/ia_proceso.php';

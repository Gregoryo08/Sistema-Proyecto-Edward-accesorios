<?php

use App\Sistema\models\Bitacora;
use App\Sistema\models\Usuarios;

$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar bitacora";

if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    if (isset($_GET['tipo'])) {
        $obj_bitacora = new Bitacora();
        
        if ($_GET['tipo'] === 'consultar_movimientos') {
            $usuario = trim($_POST['usuario'] ?? '');
            $accion = trim($_POST['accionU'] ?? '');
            echo json_encode($obj_bitacora->Consultar_Movimientos($usuario, $accion));
            exit();
        }

        if ($_GET['tipo'] === 'consultar_usuarios') {
            echo json_encode($obj_bitacora->obtenerUsuarios());
            exit();
        }
    }
}

require_once 'app/views/bitacora.php';
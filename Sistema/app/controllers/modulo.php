<?php
//use App\Sistema\models\Notificacion;
use App\Sistema\models\Usuarios;
use App\Sistema\models\modulo;




$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new usuarios();

//---------------------------------------------------------------------

//$obj_notificacion = new Notificacion();
//$obj_notificacion->generarNotificacionesStockBajo();

//--------------------------------------------------------------------------------------------


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    $obj_modulo = new modulo();
    $modulos = $obj_modulo->listar();

    unset($obj_modulo);
    echo json_encode($modulos);
    exit();
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrar') {
    $nombre = trim($_POST["nombre"]);

    $obj_modulo = new modulo();
    $obj_modulo->setNombre_modulo($nombre);
    $respuesta = $obj_modulo->registrar();

    if (isset($respuesta["error"])) {
        unset($obj_modulo);
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }
    if (isset($respuesta["incompleto"])) {
        unset($obj_modulo);
        echo json_encode(["incompleto" => $respuesta["incompleto"]]);
        exit();
    }
    if (isset($respuesta["invalido"])) {
        unset($obj_modulo);
        echo json_encode(["invalido" => $respuesta["invalido"]]);
        exit();
    }

    unset($obj_modulo);
    echo json_encode(["success" => "Rol Registrado con Exito!"]);
    exit();
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificar') {
    $nombre = trim($_POST["nombre"]);
    $id = (int) $_POST["id"];

    $obj_modulo = new modulo();
    $obj_modulo->setId_modulo($id);
    $obj_modulo->setNombre_modulo($nombre);
    $respuesta = $obj_modulo->modificar();

    if (isset($respuesta["error"])) {
        unset($obj_modulo);
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }
    if (isset($respuesta["incompleto"])) {
        unset($obj_modulo);
        echo json_encode(["incompleto" => $respuesta["incompleto"]]);
        exit();
    }
    if (isset($respuesta["invalido"])) {
        unset($obj_modulo);
        echo json_encode(["invalido" => $respuesta["invalido"]]);
        exit();
    }

    unset($obj_modulo);
    echo json_encode(["success" => "Rol Registrado con Exito!"]);
    exit();
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id = (int) $_POST["id"];

    $obj_modulo = new modulo();
    $obj_modulo->setId_modulo($id);
    $respuesta = $obj_modulo->eliminar();

    if (isset($respuesta["error"])) {
        unset($obj_modulo);
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }

    unset($obj_modulo);
    echo json_encode(["success" => "Rol Registrado con Exito!"]);
    exit();
}
include 'app/views/modulo.php';
?>
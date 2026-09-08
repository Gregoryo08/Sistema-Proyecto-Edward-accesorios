<?php
//use App\Sistema\models\Notificacion;
use App\Sistema\models\Usuarios;
use App\Sistema\models\categoria;

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
    $obj_categoria = new categoria();
    $categorias = $obj_categoria->listar();

    unset($obj_categoria);
    echo json_encode($categorias);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrar') {
    $nombre = trim($_POST["nombre"]);

    $obj_categoria = new categoria();
    $obj_categoria->setNombre_categoria($nombre);
    $respuesta = $obj_categoria->registrar();

    if (isset($respuesta["error"])) {
        unset($obj_categoria);
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }
    if (isset($respuesta["incompleto"])) {
        unset($obj_categoria);
        echo json_encode(["incompleto" => $respuesta["incompleto"]]);
        exit();
    }
    if (isset($respuesta["invalido"])) {
        unset($obj_categoria);
        echo json_encode(["invalido" => $respuesta["invalido"]]);
        exit();
    }

    unset($obj_categoria);
    echo json_encode(["success" => "Categoria Registrada con Exito!"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificar') {
    $nombre = trim($_POST["nombre"]);
    $id = (int) $_POST["id"];

    $obj_categoria = new categoria();
    $obj_categoria->setId_categoria($id);
    $obj_categoria->setNombre_categoria($nombre);
    $respuesta = $obj_categoria->modificar();

    if (isset($respuesta["error"])) {
        unset($obj_categoria);
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }
    if (isset($respuesta["incompleto"])) {
        unset($obj_categoria);
        echo json_encode(["incompleto" => $respuesta["incompleto"]]);
        exit();
    }
    if (isset($respuesta["invalido"])) {
        unset($obj_categoria);
        echo json_encode(["invalido" => $respuesta["invalido"]]);
        exit();
    }

    unset($obj_categoria);
    echo json_encode(["success" => "Categoria Modificada con Exito!"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id = (int) $_POST["id"];

    $obj_categoria = new categoria();
    $obj_categoria->setId_categoria($id);
    $respuesta = $obj_categoria->eliminar();

    if (isset($respuesta["error"])) {
        unset($obj_categoria);
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }

    unset($obj_categoria);
    echo json_encode(["success" => "Categoria Eliminada con Exito!"]);
    exit();
}

include 'app/views/categoria.php';
?>
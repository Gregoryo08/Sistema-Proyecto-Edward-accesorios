<?php
use App\Sistema\models\Usuarios;
use App\Sistema\models\especialidad;

$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Especialidad";

if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['permisos'])) {
    echo json_encode([
        "registrar" => $obj_usuario->tienePermiso($modulo_actual, "registrar"),
        "modificar" => $obj_usuario->tienePermiso($modulo_actual, "modificar"),
        "eliminar"  => $obj_usuario->tienePermiso($modulo_actual, "eliminar")
    ]);
    exit();
}









if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    $obj_especialidad = new especialidad();
    $especialidades = $obj_especialidad->listar();
    echo json_encode($especialidades);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrar') {
    if (!$obj_usuario->tienePermiso($modulo_actual, "registrar")) {
        echo json_encode(["error" => "No tienes permisos para registrar en este módulo."]);
        exit();
    }

    $nombre = trim($_POST["nombre"]);
    $obj_especialidad = new especialidad();
    $obj_especialidad->setNombre_especialidad($nombre);
    $respuesta = $obj_especialidad->registrar();

    if (isset($respuesta["error"])) {
        echo json_encode(["error" => $respuesta["error"]]);
    } else if (isset($respuesta["incompleto"])) {
        echo json_encode(["incompleto" => $respuesta["incompleto"]]);
    } else if (isset($respuesta["invalido"])) {
        echo json_encode(["invalido" => $respuesta["invalido"]]);
    } else {
        echo json_encode(["success" => "Especialidad Registrada con Exito!"]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificar') {
    if (!$obj_usuario->tienePermiso($modulo_actual, "modificar")) {
        echo json_encode(["error" => "No tienes permisos para modificar datos."]);
        exit();
    }

    $nombre = trim($_POST["nombre"]);
    $id = (int) $_POST["id"];

    $obj_especialidad = new especialidad();
    $obj_especialidad->setId_especialidad($id);
    $obj_especialidad->setNombre_especialidad($nombre);
    $respuesta = $obj_especialidad->modificar();

    if (isset($respuesta["error"])) {
        echo json_encode(["error" => $respuesta["error"]]);
    } else if (isset($respuesta["incompleto"])) {
        echo json_encode(["incompleto" => $respuesta["incompleto"]]);
    } else if (isset($respuesta["invalido"])) {
        echo json_encode(["invalido" => $respuesta["invalido"]]);
    } else {
        echo json_encode(["success" => "Especialidad Modificada con Exito!"]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    if (!$obj_usuario->tienePermiso($modulo_actual, "eliminar")) {
        echo json_encode(["error" => "No tienes permisos para eliminar registros."]);
        exit();
    }

    $id = (int) $_POST["id"];
    $obj_especialidad = new especialidad();
    $obj_especialidad->setId_especialidad($id);
    $respuesta = $obj_especialidad->eliminar();

    if (isset($respuesta["error"])) {
        echo json_encode(["error" => $respuesta["error"]]);
    } else {
        echo json_encode(["success" => "Especialidad Eliminada con Exito!"]);
    }
    exit();
}

include 'app/views/especialidad.php';
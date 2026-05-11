<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\cargos;

$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new usuarios();


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    $obj_cargos = new cargos();
    $cargos = $obj_cargos->listar();

    unset($obj_cargos);
    echo json_encode($cargos);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'validarNombre') {
    $nombre = trim($_POST["nombre"]);
    
    $obj_cargos = new cargos();
    $obj_cargos->setNombre_cargo($nombre);
    $respuesta = $obj_cargos->validar();

    if(is_array($respuesta)){
        unset($obj_cargos);
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }

    unset($obj_cargos);
    echo json_encode($respuesta);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrar') {
    $nombre = trim($_POST["nombre"]);

    $obj_cargos = new cargos();
    $obj_cargos->setNombre_cargo($nombre);
    $respuesta = $obj_cargos->registrar();

    if (isset($respuesta["error"])) {
        unset($obj_cargos);
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }
    if (isset($respuesta["incompleto"])) {
        unset($obj_cargos);
        echo json_encode(["incompleto" => $respuesta["incompleto"], "input" => $respuesta["input"]]);
        exit();
    }
    if (isset($respuesta["invalido"])) {
        unset($obj_cargos);
        echo json_encode(["invalido" => $respuesta["invalido"], "input" => $respuesta["input"]]);
        exit();
    }

    unset($obj_cargos);
    echo json_encode(["success" => "Cargo Registrado con Exito!"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificar') {
    $nombre = trim($_POST["nombre"]);
    $id = (int) $_POST["id"];

    $obj_cargos = new cargos();
    $obj_cargos->setId_cargo($id);
    $obj_cargos->setNombre_cargo($nombre);
    $respuesta = $obj_cargos->modificar();

    if (isset($respuesta["error"])) {
        unset($obj_cargos);
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }
    if (isset($respuesta["incompleto"])) {
        unset($obj_cargos);
        echo json_encode(["incompleto" => $respuesta["incompleto"], "input" => $respuesta["input"]]);
        exit();
    }
    if (isset($respuesta["invalido"])) {
        unset($obj_cargos);
        echo json_encode(["invalido" => $respuesta["invalido"], "input" => $respuesta["input"]]);
        exit();
    }

    unset($obj_cargos);
    echo json_encode(["success" => "Cargo modificado con Exito!"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id = (int) $_POST["id"];

    $obj_cargos = new cargos();
    $obj_cargos->setId_cargo($id);
    $respuesta = $obj_cargos->eliminar();

    if (isset($respuesta["error"])) {
        unset($obj_cargos);
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }

    unset($obj_cargos);
    echo json_encode(["success" => "Cargo eliminado con Exito!"]);
    exit();
}

include 'app/views/cargos.php';
?>
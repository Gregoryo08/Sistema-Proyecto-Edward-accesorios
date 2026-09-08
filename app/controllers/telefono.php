<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\telefono;

$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}




$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Telefono";

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








$objeto_telefonos = new telefono();


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true' && $_GET['x'] === "marcas") {
    $marcas = $objeto_telefonos->listarMarcas();
    echo json_encode($marcas);
    exit();
}



if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true' && $_GET['x'] === "telefono") {
    $telefonos = $objeto_telefonos->listar();
    echo json_encode($telefonos);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'consultar') {
    $id = (int) trim($_POST["id"]);
    $objeto_telefonos->setId_telefono($id);
    $resultado = $objeto_telefonos->consultarTelefono();
    echo json_encode($resultado);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrarTelefono') {
   
    $objeto_telefonos->setMarca(trim($_POST['marca'] ?? ''));
    $objeto_telefonos->setModelo(trim($_POST['modelo'] ?? ''));
    $objeto_telefonos->setAlmacenamiento(trim($_POST['almacenamiento'] ?? ''));
    $objeto_telefonos->setRam(trim($_POST['ram'] ?? ''));
    $objeto_telefonos->setImei(trim($_POST['imei'] ?? ''));

    $respuesta = $objeto_telefonos->registrar();

    if (isset($respuesta["error"])) {
        echo json_encode(["error" => $respuesta["error"]]);
    } elseif (isset($respuesta["incompleto"])) {
        echo json_encode(["incompleto" => $respuesta["incompleto"], "input" => $respuesta["input"]]);
    } elseif (isset($respuesta["invalido"])) {
        echo json_encode(["invalido" => $respuesta["invalido"], "input" => $respuesta["input"]]);
    } else {
        echo json_encode(["success" => "Teléfono registrado exitosamente."]);
    }
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificarTelefono') {
  
    $objeto_telefonos->setId_telefono((int)($_POST["id"] ?? 0)); 
    $objeto_telefonos->setMarca(trim($_POST['marca'] ?? ''));
    $objeto_telefonos->setModelo(trim($_POST['modelo'] ?? ''));
    $objeto_telefonos->setAlmacenamiento(trim($_POST['almacenamiento'] ?? ''));
    $objeto_telefonos->setRam(trim($_POST['ram'] ?? ''));
    $objeto_telefonos->setImei(trim($_POST['imei'] ?? ''));

    $respuesta = $objeto_telefonos->modificar();

    if (isset($respuesta["error"])) {
        echo json_encode(["error" => $respuesta["error"]]);
    } elseif (isset($respuesta["incompleto"])) {
        echo json_encode(["incompleto" => $respuesta["incompleto"]]);
    } elseif (isset($respuesta["invalido"])) {
        echo json_encode(["invalido" => $respuesta["invalido"], "input" => $respuesta["input"]]);
    } else {
        echo json_encode(["success" => "Teléfono modificado exitosamente."]);
    }
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminarTelefono') {
    $id = (int) trim($_POST["id"]);
    $objeto_telefonos->setId_telefono($id);
    $respuesta = $objeto_telefonos->eliminar();

    if (isset($respuesta["error"])) {
        echo json_encode(["error" => $respuesta["error"]]);
    } else {
        echo json_encode(["success" => "Teléfono eliminado exitosamente."]);
    }
    exit();
}

include 'App/views/telefono.php';
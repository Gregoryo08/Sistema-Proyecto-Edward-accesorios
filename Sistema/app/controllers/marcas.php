<?php

use App\Sistema\models\marcas;
use App\Sistema\models\Usuarios;

$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Marcas";

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










if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true' && $_GET['x'] === "marcas") {
    $objeto_marca = new marcas();
    $marcas = $objeto_marca->listar();
    echo json_encode($marcas);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'consultar') {
    $id = (int) trim($_POST["id"]);
    $objeto_marcas = new marcas();
    $objeto_marcas->setId_marca($id);
    $resultado = $objeto_marcas->consultarMarca();

    echo json_encode($resultado);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrarMarca') {
    $nombre = trim($_POST['nombre']);

    $objeto_marcas = new marcas();
    $objeto_marcas->setNombre_marca($nombre);

    $respuesta = $objeto_marcas->registrar();

    if (isset($respuesta["error"])) {
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }
    if (isset($respuesta["incompleto"])) {
        echo json_encode(["incompleto" => $respuesta["incompleto"], "input" => $respuesta["input"]]);
        exit();
    }
    if (isset($respuesta["invalido"])) {
        echo json_encode(["invalido" => $respuesta["invalido"], "input" => $respuesta["input"]]);
        exit();
    }

    echo json_encode(["success" => "Marca registrada exitosamente."]);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificarMarca') {
    $nombre = trim($_POST['nombre']);
    $id = (int) trim($_POST["id"]);

    $objeto_marcas = new marcas();
    $objeto_marcas->setNombre_marca($nombre);
    $objeto_marcas->setId_marca($id);
    
    $respuesta = $objeto_marcas->modificar();

    if (isset($respuesta["error"])) {
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }
    if (isset($respuesta["incompleto"])) {
        echo json_encode(["incompleto" => $respuesta["incompleto"], "input" => $respuesta["input"]]);
        exit();
    }
    if (isset($respuesta["invalido"])) {
        echo json_encode(["invalido" => $respuesta["invalido"], "input" => $respuesta["input"]]);
        exit();
    }

    echo json_encode(["success" => "Marca modificada exitosamente."]);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminarMarca') {
    $id = (int) trim($_POST["id"]);

    $objeto_marcas = new marcas();
    $objeto_marcas->setId_marca($id);
    $respuesta = $objeto_marcas->eliminar();

    if (isset($respuesta["error"])) {
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }

    echo json_encode(["success" => "Marca eliminada exitosamente."]);
    exit();
}

function procesarRespuesta($respuesta, $mensajeExito) {
    if ($respuesta === true) {
        echo json_encode(["success" => $mensajeExito]);
    } else if (is_array($respuesta)) {
       
        echo json_encode($respuesta);
    } else {
        echo json_encode(["error" => "Error desconocido en el servidor."]);
    }
    exit();
}

include 'app/views/marcas.php';
?>
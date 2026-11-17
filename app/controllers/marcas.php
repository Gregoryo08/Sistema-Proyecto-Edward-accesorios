<?php

use App\Sistema\models\marcas;
use App\Sistema\models\Usuarios;
use App\Sistema\models\Respuesta;

$modeloRespuesta = new Respuesta();

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
    
    $modeloRespuesta->json($respuesta, "Marca registrada exitosamente.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificarMarca') {
    $nombre = trim($_POST['nombre']);
    $id = (int) trim($_POST["id"]);
    $objeto_marcas = new marcas();
    $objeto_marcas->setNombre_marca($nombre);
    $objeto_marcas->setId_marca($id);
    
    $respuesta = $objeto_marcas->modificar();
    
    $modeloRespuesta->json($respuesta, "Marca modificada exitosamente.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminarMarca') {
    $id = (int) trim($_POST["id"]);
    $objeto_marcas = new marcas();
    $objeto_marcas->setId_marca($id);
    
    $respuesta = $objeto_marcas->eliminar();
    
    $modeloRespuesta->json($respuesta, "Marca eliminada exitosamente.");
}

include 'app/views/marcas.php';
?>
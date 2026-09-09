<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\metodo;

$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Metodos de Pago";

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
    if (ob_get_length()) ob_clean();
    error_reporting(0);
    $metodo = new metodo();
    $Metodopagos = $metodo->listar();

    unset($metodo);
    echo json_encode($Metodopagos);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrar') {
    if (ob_get_length()) ob_clean();
    error_reporting(0);
    $nombre_metodopago = trim($_POST['nombre'] ?? '');
    $moneda = trim($_POST['moneda'] ?? '');
    $tipoCuenta = trim($_POST['cuenta'] ?? '0');
    $estado = trim($_POST['estado'] ?? '1'); 

    $metodo = new metodo(); 
    if (!$metodo->setNombreMetodoPago($nombre_metodopago)) { 
        echo json_encode(["invalido" => $metodo->getMensaje()]);
        exit();
    }
    
    if (!$metodo->setMoneda($moneda)) {
        echo json_encode(["invalido" => $metodo->getMensaje()]);
        exit();
    }

    $metodo->setCuenta($tipoCuenta); 
    $metodo->setEstado($estado);
    $respuesta = $metodo->registrar(); 

    if (isset($respuesta["invalido"])) {
        unset($metodo);
        echo json_encode(["invalido" => $respuesta["invalido"]]);
        exit();
    }
    if (isset($respuesta["incompleto"])) {
        unset($metodo);
        echo json_encode(["incompleto" => $respuesta["incompleto"]]);
        exit();
    }
    if (isset($respuesta["error"])) {
        unset($metodo);
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }

    unset($metodo);
    echo json_encode(["success" => "Método de pago agregado exitosamente."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificar') {
    if (ob_get_length()) ob_clean();
    error_reporting(0);
    $id_metodopago = trim($_POST['id'] ?? '');
    $nombre_metodopago = trim($_POST['nombre'] ?? '');
    $moneda = trim($_POST['moneda'] ?? '');
    $tipoCuenta = trim($_POST['cuenta'] ?? '0'); 
    $estado = trim($_POST['estado'] ?? '1');

    $metodo = new metodo(); 
    if (!$metodo->setIdMetodoPago($id_metodopago)) { 
        echo json_encode(["invalido" => $metodo->getMensaje()]);
        exit();
    }
    if (!$metodo->setNombreMetodoPago($nombre_metodopago)) { 
        echo json_encode(["invalido" => $metodo->getMensaje()]);
        exit();
    }
    if (!$metodo->setMoneda($moneda)) {
        echo json_encode(["invalido" => $metodo->getMensaje()]);
        exit();
    }
    
    $metodo->setCuenta($tipoCuenta);
    $metodo->setEstado($estado);
    $respuesta = $metodo->modificar();

    if (isset($respuesta["invalido"])) {
        unset($metodo);
        echo json_encode(["invalido" => $respuesta["invalido"]]);
        exit();
    }
    if (isset($respuesta["incompleto"])) {
        unset($metodo);
        echo json_encode(["incompleto" => $respuesta["incompleto"]]);
        exit();
    }
    if (isset($respuesta["error"])) {
        unset($metodo);
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }

    unset($metodo);
    echo json_encode(["success" => "Método de pago modificado exitosamente."]); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    if (ob_get_length()) ob_clean();
    error_reporting(0);
    $id_metodopago = $_POST['id'] ?? '';

    $metodo = new metodo(); 
    if (!$metodo->setIdMetodoPago($id_metodopago)) { 
        echo json_encode(["invalido" => $metodo->getMensaje()]);
        exit();
    }

    $respuesta = $metodo->eliminar();

    if (isset($respuesta['invalido'])) {
        unset($metodo);
        echo json_encode(["invalido" => $respuesta['invalido']]);
        exit();
    }

    if (isset($respuesta['error'])) {
        unset($metodo);
        echo json_encode(["error" => $respuesta['error']]);
        exit();
    }

    unset($metodo);
    echo json_encode(["success" => "Método de pago eliminado exitosamente."]); 
    exit();
}

require_once 'app/views/metodo.php';
?>
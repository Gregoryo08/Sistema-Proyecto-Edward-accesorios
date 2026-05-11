<?php

//use App\SistemaHotelero\models\Notificacion;
use App\Sistema\models\Usuarios;
use App\Sistema\models\metodo;

$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$datos = $obj_usuario->validarPermisos($rol);

$modulo = "Administrar Metodos de Pago";
$modulosPermitidos = array_column($datos, "nombre_modulo");

$tieneAcceso = false;

if (is_array($datos) && !empty($datos)) {
    $permisosEncontrados = array_filter($datos, function ($permiso) use ($modulo) {
        $nombreModulo = is_object($permiso) ? $permiso->nombre_modulo : (is_array($permiso) ? $permiso['nombre_modulo'] : null);
        return $nombreModulo === $modulo;
    });
    $permisos_array = !empty($permisosEncontrados) ? array_values($permisosEncontrados)[0] : null;

    if (!empty($permisos_array)) {
        if (is_array($permisos_array)) {
            if ($permisos_array["control_total"] === 1 || $permisos_array["registrar"] === 1 || (($permisos_array["listar"] === 1) && (($permisos_array["consultar"] === 1) || ($permisos_array["modificar"] === 1) || ($permisos_array["eliminar"] === 1)))) {
                $tieneAcceso = true;
            }
        } elseif (is_object($permisos_array)) {
            if ($permisos_array->control_total === 1 || $permisos_array->registrar === 1 || (($permisos_array->listar === 1) && (($permisos_array->consultar === 1) || ($permisos_array->modificar === 1) || ($permisos_array->eliminar === 1)))) {
                $tieneAcceso = true;
            }
        }
    }
}

if (!$tieneAcceso) {
    header("Location: ?pagina=principal");
    exit();
}

//---------------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'permisos') {
    if (ob_get_length()) ob_clean();
    echo json_encode(["array" => $datos, "modulo" => $modulo, "usuario" => $cedula]);
    exit();
}

//---------------------------------------------------------------------

//$obj_notificacion = new Notificacion();
//$obj_notificacion->generarNotificacionesStockBajo();

//--------------------------------------------------------------------------------------------

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
    $tipoCuenta = trim($_POST['cuenta'] ?? '0');

    $metodo = new metodo(); 
    if (!$metodo->setNombreMetodoPago($nombre_metodopago)) { 
        echo json_encode(["invalido" => $metodo->getMensaje()]);
        exit();
    }
    
    $metodo->setCuenta($tipoCuenta); 
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
    $tipoCuenta = trim($_POST['cuenta'] ?? '0'); // Arreglo para PHP 8.2

    $metodo = new metodo(); 
    if (!$metodo->setIdMetodoPago($id_metodopago)) { 
        echo json_encode(["invalido" => $metodo->getMensaje()]);
        exit();
    }
    if (!$metodo->setNombreMetodoPago($nombre_metodopago)) { 
        echo json_encode(["invalido" => $metodo->getMensaje()]);
        exit();
    }
    
    $metodo->setCuenta($tipoCuenta);
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

include 'app/views/metodo.php';
?>
<?php
//use App\SistemaHotelero\models\Notificacion;
use App\Sistema\models\Usuarios;
use App\Sistema\models\Empleados;
use App\Sistema\models\cargos;



$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new usuarios();
$datos = $obj_usuario->validarPermisos($rol);

$modulo = "Administrar Empleados";
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
    echo json_encode(["array" => $datos, "modulo" => $modulo, "usuario" => $cedula]);
    exit();
}

//---------------------------------------------------------------------

//$obj_notificacion = new Notificacion();
//$obj_notificacion->generarNotificacionesStockBajo();

//--------------------------------------------------------------------------------------------



if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true' && !(isset($_GET['x']))) {
    $empleado = new empleados();
    $empleado->setCedula($cedula);
    $resultado = $empleado->datos();
    
    unset($empleado);
    echo json_encode($resultado); 
    exit(); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'buscarCargos') {
    $obj_cargos = new cargos();
    $resultado = $obj_cargos->listar();
    
    unset($obj_cargos);
    echo json_encode($resultado); 
    exit(); 
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true' && isset($_GET['x'])) {
    $empleado = new empleados();
    $inactivos = $empleado->consultaInactivos(); 

    unset($empleado);
    echo json_encode($inactivos); 
    exit(); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'validarC') {
    $cedula = $_POST['cedula'];

    $empleado = new empleados();
    $empleado->setCedula($cedula);
    $question = $empleado->validarCedula();

    if(isset($question['error'])){
        unset($empleado);
        echo json_encode(["error" => $question['error']]);
        exit();
    }

    unset($empleado);
    echo json_encode(["data" => $question]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'consultar') {
    $cedula = $_POST['cedula'];

    $empleado = new empleados();
    $empleado->setCedula($cedula);
    $question = $empleado->consultar();

    unset($empleado);
    echo json_encode($question);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrar') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $cedula = trim($_POST['cedula']);
    $cargo = (int) trim($_POST['cargo']);

    $empleado = new empleados();
    $empleado->setNombre($nombre);
    $empleado->setApellido($apellido);
    $empleado->setCedula($cedula);
    $empleado->setCel($telefono);
    $empleado->setDireccion($direccion);
    $empleado->setCorreo($correo);
    $empleado->setCargo($cargo);
    $respuesta = $empleado->registroEmpleado();
    
    if(isset($respuesta["error"])){
        unset($empleado);
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }
    if(isset($respuesta["incompleto"])){
        unset($empleado);
        echo json_encode(["incompleto" => $respuesta["incompleto"], "input" => $respuesta["input"]]);
        exit();
    }
    if(isset($respuesta["invalido"])){
        unset($empleado);
        echo json_encode(["invalido" => $respuesta["invalido"], "input" => $respuesta["input"]]);
        exit();
    }

    unset($empleado);
    echo json_encode(["success" => "Cliente agregado exitosamente."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificar') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $cedula_nueva = trim($_POST['cedula_nueva']);
    $cargo = (int) trim($_POST['cargo']);
    $cedula_vieja = trim($_POST["cedula_vieja"]);
    
    $empleado = new empleados();
    $empleado->setNombre($nombre);
    $empleado->setApellido($apellido);
    $empleado->setCorreo($correo);
    $empleado->setCel($telefono);
    $empleado->setDireccion($direccion);
    $empleado->setCargo($cargo);
    $empleado->setCedula($cedula_nueva);
    $respuesta = $empleado->ModificarEmpleado($cedula_vieja);
    
    if(isset($respuesta["error"])){
        unset($empleado);
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }
    if(isset($respuesta["incompleto"])){
        unset($empleado);
        echo json_encode(["incompleto" => $respuesta["incompleto"], "input" => $respuesta["input"]]);
        exit();
    }
    if(isset($respuesta["invalido"])){
        unset($empleado);
        echo json_encode(["invalido" => $respuesta["invalido"], "input" => $respuesta["input"]]);
        exit();
    }

    unset($empleado);
    echo json_encode(["success" => $respuesta]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id_eliminar = $_POST['id'];
    $estado = $_POST['estado'];

    $empleado = new empleados();
    $empleado->setCedula($id_eliminar);
    $empleado->setEstado($estado);
    $respuesta = $empleado->eliminarEmpleado();
   
    if (isset($respuesta["error"])) {
        unset($empleado);
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }

    unset($empleado);
    echo json_encode(["success" => "Cliente eliminado exitosamente."]);
    exit();
}
include 'app/views/empleado.php';
?>
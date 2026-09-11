<?php

use App\Sistema\models\Empleados;
use App\Sistema\models\Usuarios;
use App\Sistema\models\Cargos;

if (!function_exists('procesarRespuesta')) {
    function procesarRespuesta($respuesta, $mensajeExito)
    {
        if ($respuesta === true) {
            echo json_encode(["success" => $mensajeExito]);
        } else if (is_array($respuesta)) {
            echo json_encode($respuesta);
        } else {
            echo json_encode(["error" => "Error desconocido en el servidor."]);
        }
        exit();
    }
}

$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Empleados";

if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['permisos'])) {
    echo json_encode([
        "registrar" => $obj_usuario->tienePermiso($modulo_actual, "registrar"),
        "modificar" => $obj_usuario->tienePermiso($modulo_actual, "modificar"),
        "eliminar"  => $obj_usuario->tienePermiso($modulo_actual, "eliminar"),
        "consultar" => $obj_usuario->tienePermiso($modulo_actual, "consultar")
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    $empleado = new Empleados();
    
    if (ob_get_length()) ob_clean();
    
    header('Content-Type: application/json; charset=utf-8');
    
    echo json_encode($empleado->listarEmpleados(), JSON_UNESCAPED_UNICODE);
    
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $empleado = new Empleados();

    switch ($_POST['accion']) {
        case 'buscarCargos':
            $obj_cargos = new Cargos();
            echo json_encode($obj_cargos->listar());
            break;

        case 'validarC':
            $empleado->setCedula($_POST['cedula']);
            echo json_encode(["data" => $empleado->validarCedula()]);
            break;

        case 'consultar':
            $empleado->setCedula($_POST['cedula']);
            echo json_encode($empleado->consultar());
            break;

        case 'registrar':
            $empleado->setNombre(trim($_POST['nombre'] ?? ''));
            $empleado->setApellido(trim($_POST['apellido'] ?? ''));
            $empleado->setCorreo(trim($_POST['correo'] ?? ''));
            $empleado->setCel(trim($_POST['telefono'] ?? ''));
            $empleado->setCedula(trim($_POST['cedula'] ?? ''));
            $empleado->setCargo((int)($_POST['cargo'] ?? 0));
            $empleado->setEdad(trim($_POST['fecha_nacimiento'] ?? ''));
            $empleado->setSexo(trim($_POST['sexo'] ?? ''));
            $empleado->setDireccion(trim($_POST['direccion'] ?? ''));

            procesarRespuesta($empleado->registroEmpleado(), "Empleado registrado exitosamente.");
            break;

        case 'modificar':
            if (!isset($_POST['cedula_vieja']) || empty($_POST['cedula_vieja'])) {
                echo json_encode(["error" => "Cédula original no proporcionada"]);
                break;
            }

            $empleado->setNombre(trim($_POST['nombre'] ?? ''));
            $empleado->setApellido(trim($_POST['apellido'] ?? ''));
            $empleado->setCorreo(trim($_POST['correo'] ?? ''));
            $empleado->setCel(trim($_POST['telefono'] ?? ''));
            $empleado->setCedula(trim($_POST['cedula_nueva'] ?? ''));
            $empleado->setCargo((int)($_POST['cargo'] ?? 0));
            
            $fechaInput = trim($_POST['fecha_nacimiento_real'] ?? ''); 
            $empleado->setEdad(empty($fechaInput) ? null : $fechaInput);
            
            $empleado->setSexo(trim($_POST['sexo'] ?? ''));
            $empleado->setDireccion(trim($_POST['direccion'] ?? ''));
            
            procesarRespuesta($empleado->ModificarEmpleado(trim($_POST["cedula_vieja"])), "Empleado modificado exitosamente.");
            break;

        case 'eliminar':
            $empleado->setCedula($_POST['id']);
            $empleado->setEstado($_POST['estado']);
            procesarRespuesta($empleado->eliminarEmpleado(), "Empleado eliminado exitosamente.");
            break;
    }
    exit();
}

require_once 'app/views/empleado.php';
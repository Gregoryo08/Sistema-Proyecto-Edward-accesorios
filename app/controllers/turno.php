<?php
ob_start();

use App\Sistema\models\Usuarios;
use App\Sistema\models\Turno;

$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    $obj_turno = new Turno();
    $turnos = $obj_turno->listar();
    unset($obj_turno);
    echo json_encode($turnos);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'consultarTurno') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    $id_turno = isset($_POST["id"]) ? (int) trim($_POST["id"]) : 0;
    $obj_turno = new Turno();
    $obj_turno->setId_turno($id_turno);
    $turnos = $obj_turno->consultar();
    if (isset($turnos["error"])) {
        echo json_encode(["error" => $turnos["error"]]);
        exit();
    }
    unset($obj_turno);
    echo json_encode($turnos);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'validarTurno') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    $fecha = isset($_POST["fecha"]) ? trim($_POST["fecha"]) : '';
    if (empty($fecha)) {
        echo json_encode(["error" => "La fecha es requerida."]);
        exit();
    }
    $obj_turno = new Turno();
    $respuesta = $obj_turno->obtenerTurno($fecha);
    unset($obj_turno);
    echo json_encode(['existe' => (isset($respuesta['conteo']) && $respuesta['conteo'] > 0)]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'listarEmpleados') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    $obj_turno = new Turno();
    $respuesta = $obj_turno->listarEmpleados();
    $empleados_para_select2 = [];
    if (is_array($respuesta)) {
        foreach ($respuesta as $empleado) {
            $empleados_para_select2[] = [
                'id' => $empleado['id'],
                'text' => $empleado['nombre'] . ' ' . $empleado['apellido'] . ' (' . $empleado['cargo'] . ')',
                'nombre' => $empleado['nombre'],
                'apellido' => $empleado['apellido'],
                'cargo' => $empleado['cargo']
            ];
        }
    }
    unset($obj_turno);
    echo json_encode($empleados_para_select2);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrar') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    $fecha_turno = isset($_POST["fecha"]) ? trim($_POST["fecha"]) : "";
    $cedula_persona = isset($_POST["cedulas_persona"]) ? trim($_POST["cedulas_persona"]) : "";
    $hora_entrada = isset($_POST["hora_entrada"]) ? trim($_POST["hora_entrada"]) : "";
    $hora_salida = isset($_POST["hora_salida"]) ? trim($_POST["hora_salida"]) : "";
    $list_obs = isset($_POST["obs"]) ? $_POST["obs"] : [];

    $obj_turno = new Turno();
    $obj_turno->setFecha_turno($fecha_turno);
    $obj_turno->setCedula_persona($cedula_persona);
    $obj_turno->setHora_entrada($hora_entrada);
    $obj_turno->setHora_salida($hora_salida);
    $obj_turno->setObs($list_obs);
    error_log("Cédulas recibidas: " . $cedula_persona);
    $respuesta = $obj_turno->registrar();
    
    if (isset($respuesta["error"]) || isset($respuesta["incompleto"]) || isset($respuesta["invalido"])) {
        echo json_encode($respuesta);
        exit();
    }
    
    unset($obj_turno);
    echo json_encode(["success" => "Turno registrado exitosamente."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificar') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    $id_turno = isset($_POST["id_turno"]) ? (int) trim($_POST["id_turno"]) : 0;
    $fecha = isset($_POST["fecha"]) ? trim($_POST["fecha"]) : "";
    $cedulas = isset($_POST["cedulas_persona"]) ? trim($_POST["cedulas_persona"]) : "";
    $h_entrada = isset($_POST["hora_entrada"]) ? trim($_POST["hora_entrada"]) : "";
    $h_salida = isset($_POST["hora_salida"]) ? trim($_POST["hora_salida"]) : "";
    $observaciones = isset($_POST["obs"]) ? $_POST["obs"] : [];

    $obj_turno = new Turno();
    $obj_turno->setId_turno($id_turno);
    $obj_turno->setFecha_turno($fecha);
    $obj_turno->setCedula_persona($cedulas);
    $obj_turno->setHora_entrada($h_entrada);
    $obj_turno->setHora_salida($h_salida);
    $obj_turno->setObs($observaciones);

    $respuesta = $obj_turno->modificar();

    if (isset($respuesta["error"]) || isset($respuesta["incompleto"]) || isset($respuesta["invalido"])) {
        echo json_encode($respuesta);
        exit();
    }
    
    unset($obj_turno);
    echo json_encode(["success" => "Turno modificado exitosamente."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    $id_turno = isset($_POST["id"]) ? (int) trim($_POST["id"]) : 0;
    $obj_turno = new Turno();
    $obj_turno->setId_turno($id_turno);
    $respuesta = $obj_turno->eliminar();
    if (isset($respuesta["error"]) || isset($respuesta["incompleto"]) || isset($respuesta["invalido"])) {
        echo json_encode($respuesta);
        exit();
    }
    unset($obj_turno);
    echo json_encode(["success" => "Turno eliminado exitosamente."]);
    exit();
}

include 'app/views/turno.php';
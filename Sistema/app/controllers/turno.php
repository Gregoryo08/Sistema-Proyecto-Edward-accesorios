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

$obj_usuario = new usuarios();





if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    $obj_turno = new turno();
    $turnos = $obj_turno->listar();
    unset($obj_turno);
    echo json_encode($turnos);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'consultarTurno') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    $id_turno = isset($_POST["id"]) ? (int) trim($_POST["id"]) : 0;
    $obj_turno = new turno();
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
    $obj_turno = new turno();
    $respuesta = $obj_turno->obtenerTurno($fecha);
    unset($obj_turno);
    echo json_encode(['existe' => (isset($respuesta['conteo']) && $respuesta['conteo'] > 0)]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'listarEmpleados') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    $obj_turno = new turno();
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
    $fecha_turno = trim($_POST["fecha"]); 
    $cedula_empleados = trim($_POST["cedulas_empleados"]);
    $hora_entrada = trim($_POST['hora_entrada']);
    $hora_salida = trim($_POST['hora_salida']);
    $list_obs = isset($_POST["obs"]) ? $_POST["obs"] : [];
    $obj_turno = new turno();
    $obj_turno->setFecha_turno($fecha_turno);
    $obj_turno->setCedula_empleado($cedula_empleados);
    $obj_turno->setHora_entrada($hora_entrada);
    $obj_turno->setHora_salida($hora_salida);
    $obj_turno->setObs($list_obs);
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
    $id_turno = (int) trim($_POST["id_turno"]);
    $obj_turno = new turno();
    $obj_turno->setId_turno($id_turno);
    $obj_turno->setFecha_turno(trim($_POST["fecha"]));
    $obj_turno->setCedula_empleado(trim($_POST["cedulas_empleados"]));
    $obj_turno->setHora_entrada(trim($_POST['hora_entrada']));
    $obj_turno->setHora_salida(trim($_POST['hora_salida']));
    $obj_turno->setObs(isset($_POST["obs"]) ? $_POST["obs"] : []);
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
    $obj_turno = new turno();
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
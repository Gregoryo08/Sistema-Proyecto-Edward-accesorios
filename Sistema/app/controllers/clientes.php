<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\Cliente;
use App\Sistema\models\scrape_dolar;

$cedula_session = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!(isset($cedula_session) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Clientes";

if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['obtener_tasa'])) {
    $tasa = scrape_dolar::obtenerPrecioDolarBCV();
    if ($tasa) {
        echo json_encode(["tasa" => $tasa]);
    } else {
        echo json_encode(["error" => "No se pudo obtener la tasa"]);
    }
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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true' && !(isset($_GET['x']))) {
    $cliente = new Cliente();
    $resultado = $cliente->datosClientesActivos();
    echo json_encode($resultado);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true' && isset($_GET['x']) && $_GET['x'] === 'inactivos') {
    $cliente = new Cliente();
    $inactivos = $cliente->consultaInactivos();
    echo json_encode($inactivos);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'validarC') {
    $cedula = $_POST['cedula'];
    $cliente = new Cliente();
    $cliente->setCedula($cedula); 
    $question = $cliente->validarCedula();

    if(isset($question['error'])){
        echo json_encode(["error" => $question['error']]);
        exit();
    }
    echo json_encode(["data" => $question]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'consultar') {
    $cedula = $_POST['id'];
    $cliente = new Cliente();
    $cliente->setCedula($cedula); 
    $respuesta = $cliente->consultarCliente();

    if(isset($respuesta["error"])){
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }
    echo json_encode($respuesta);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrar') {
    $cliente = new Cliente();
    
    $cliente->setCedula(trim($_POST['cedula'] ?? ''));
    $cliente->setNombre(trim($_POST['nombre'] ?? ''));
    $cliente->setApellido(trim($_POST['apellido'] ?? ''));
    $cliente->setCorreo(trim($_POST['correo'] ?? ''));
    $cliente->setCel(trim($_POST['telefono'] ?? ''));
    $cliente->setDireccion(trim($_POST['direccion'] ?? ''));
    $cliente->setSexo(trim($_POST['sexo'] ?? ''));
    $cliente->setEdad(trim($_POST['fecha'] ?? '')); 
    $cliente->setIngresos(trim($_POST['ingresos_mensuales'] ?? '0')); 
    
    $cliente->setResidenciaTipo(trim($_POST['tipo_residencia'] ?? 'No especificado'));
    $cliente->setCargaFamiliar(trim($_POST['carga_familiar'] ?? '0'));
    $cliente->setEstadoCivil(trim($_POST['estado_civil'] ?? 'Soltero/a'));
    $cliente->setProfesion(trim($_POST['profesion'] ?? 'No especificado'));
    $cliente->setOcupacion(trim($_POST['ocupacion'] ?? 'No especificado'));

    $respuesta = $cliente->registroCliente();

    if($respuesta === true){
        echo json_encode(["success" => "Cliente registrado exitosamente."]);
    } else {
        echo json_encode($respuesta);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificar') {
    $cliente = new Cliente();
    
    $cliente->setCedula(trim($_POST['cedula'] ?? '')); 
    $cliente->setNombre(trim($_POST['nombre'] ?? ''));
    $cliente->setApellido(trim($_POST['apellido'] ?? ''));
    $cliente->setCorreo(trim($_POST['correo'] ?? ''));
    $cliente->setCel(trim($_POST['telefono'] ?? ''));
    $cliente->setDireccion(trim($_POST['direccion'] ?? ''));
    $cliente->setSexo(trim($_POST['sexo'] ?? ''));
    $cliente->setIngresos(trim($_POST['ingresos_mensuales'] ?? '0')); 

    $cliente->setResidenciaTipo(trim($_POST['tipo_residencia'] ?? 'No especificado'));
    $cliente->setCargaFamiliar(trim($_POST['carga_familiar'] ?? '0'));
    $cliente->setEstadoCivil(trim($_POST['estado_civil'] ?? 'Soltero/a'));
    $cliente->setProfesion(trim($_POST['profesion'] ?? 'No especificado'));
    $cliente->setOcupacion(trim($_POST['ocupacion'] ?? 'No especificado'));

    $respuesta = $cliente->ModificarCliente(); 

    if($respuesta === true){
        echo json_encode(["success" => "Cliente modificado exitosamente."]);
    } else {
        echo json_encode($respuesta);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $cliente = new Cliente();
    $cliente->setCedula($_POST['id']); 
    $respuesta = $cliente->eliminarClientes($_POST['estado']);

    if ($respuesta === true) {
        echo json_encode(["success" => "Estado del cliente actualizado exitosamente."]);
    } else {
        echo json_encode($respuesta);
    }
    exit();
}

include 'app/views/clientes.php';
?>
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Sistema\models\Usuarios;
use App\Sistema\models\Cliente;
use App\Sistema\models\scrape_dolar;
use App\Sistema\models\TasaModel;

$cedula_session = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!(isset($cedula_session) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Clientes";
$obj_tasa = new TasaModel();

if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

if (!function_exists('obtenerTasaOptimizada')) {
    function obtenerTasaOptimizada(): float
    {
        $cacheTtl = 3600;

        $tasa = scrape_dolar::obtenerPrecioDolarBCV();

        if ($tasa !== null && $tasa > 0) {
            $_SESSION['tasa_bcv'] = $tasa;
            $_SESSION['tasa_bcv_time'] = time();
            return (float) $tasa;
        }

        if (
            isset($_SESSION['tasa_bcv'], $_SESSION['tasa_bcv_time']) &&
            (time() - $_SESSION['tasa_bcv_time']) < $cacheTtl
        ) {
            return (float) $_SESSION['tasa_bcv'];
        }

        return 0.0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['obtener_tasa'])) {
    header('Content-Type: application/json; charset=utf-8');
    
    $tasa = obtenerTasaOptimizada();
    $fecha = date('d/m/Y');

    if ($tasa > 0) {
        $idTasaDB = null;
    } else {
        $tasaDB = $obj_tasa->obtenerTasaActual();
        if ($tasaDB && !empty($tasaDB['tasa'])) {
            $tasa = (float) $tasaDB['tasa'];
            $fecha = date('d/m/Y', strtotime($tasaDB['fecha_actualizacion']));
        }
    }

    if ($tasa <= 0) {
        $obj_tasa->registrarNotificacionTasaNoDisponible();
    }

    echo json_encode([
        "success" => $tasa > 0,
        "tasa" => $tasa,
        "fecha" => $fecha,
        "mensaje" => $tasa > 0 ? "Tasa obtenida con éxito" : "No se pudo obtener la tasa de cambio"
    ]);
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
    $resultado = $cliente->datosClientes();
    echo json_encode($resultado);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'validarC') {
    $cedula = $_POST['cedula'];
    $cliente = new Cliente();
    $cliente->setCedula($cedula); 
    $question = $cliente->procesarSolicitud('validarCedula');

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
    $respuesta = $cliente->procesarSolicitud('consultar');

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
    $fechaNacimiento = trim($_POST['fecha'] ?? $_POST['fecha_nacimiento'] ?? '');
    $cliente->setEdad($fechaNacimiento);
    $cliente->setIngresos(trim($_POST['ingresos_mensuales'] ?? '0')); 

    $cliente->setResidenciaTipo(trim($_POST['tipo_residencia'] ?? 'No especificado'));
    $cliente->setCargaFamiliar(trim($_POST['carga_familiar'] ?? '0'));
    $cliente->setEstadoCivil(trim($_POST['estado_civil'] ?? 'Soltero/a'));
    $cliente->setProfesion(trim($_POST['profesion'] ?? 'No especificado'));
    $cliente->setOcupacion(trim($_POST['ocupacion'] ?? 'No especificado'));

    $respuesta = $cliente->procesarSolicitud('registrar');

    if($respuesta === true){
        echo json_encode(["success" => "Cliente registrado exitosamente."]);
    } else {
        echo json_encode($respuesta);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrarPerfil') {
    $cliente = new Cliente();
    
   $cliente->setCedula(trim($_POST['cedula'] ?? ''));

    $cliente->setIngresos(trim($_POST['ingresos_mensuales'] ?? '0'));
    $cliente->setResidenciaTipo(trim($_POST['tipo_residencia'] ?? 'Familiar'));
    $cliente->setCargaFamiliar(trim($_POST['carga_familiar'] ?? '0'));
    $cliente->setEstadoCivil(trim($_POST['estado_civil'] ?? 'Soltero/a'));
    $cliente->setProfesion(trim($_POST['profesion'] ?? 'Empleado'));
    $cliente->setOcupacion(trim($_POST['ocupacion'] ?? ''));

    $respuesta = $cliente->procesarSolicitud('registrarPerfil');

    if($respuesta === true){
        echo json_encode(["success" => "Perfil financiero registrado exitosamente."]);
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
    
    $ingresos = trim($_POST['ingresos_mensuales'] ?? '');
    $cliente->setIngresos($ingresos === '' ? '0' : $ingresos); 

    $cliente->setResidenciaTipo(trim($_POST['tipo_residencia'] ?? 'No especificado'));
    
    $cargas = trim($_POST['carga_familiar'] ?? '');
    $cliente->setCargaFamiliar($cargas === '' ? '0' : $cargas);
    
    $cliente->setEstadoCivil(trim($_POST['estado_civil'] ?? 'Soltero/a'));
    $cliente->setProfesion(trim($_POST['profesion'] ?? 'No especificado'));
    $cliente->setOcupacion(trim($_POST['ocupacion'] ?? 'No especificado'));

    $respuesta = $cliente->procesarSolicitud('modificar'); 

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
    $respuesta = $cliente->procesarSolicitud('eliminar', ['estado' => $_POST['estado']]);

    if ($respuesta === true) {
        echo json_encode(["success" => "Estado del cliente actualizado exitosamente."]);
    } else {
        echo json_encode($respuesta);
    }
    exit();
}

$tasaCambioActual = obtenerTasaOptimizada();
if ($tasaCambioActual <= 0) {
    $tasaDB = $obj_tasa->obtenerTasaActual();
    $tasaCambioActual = ($tasaDB && !empty($tasaDB['tasa'])) ? (float)$tasaDB['tasa'] : 0.0;
}

if ($tasaCambioActual <= 0) {
    $obj_tasa->registrarNotificacionTasaNoDisponible();
}

$vista = 'app/views/clientes.php';

if (file_exists($vista)) {
    require_once $vista;
} else {
    require_once 'app/views/error_404.php';
}
?>
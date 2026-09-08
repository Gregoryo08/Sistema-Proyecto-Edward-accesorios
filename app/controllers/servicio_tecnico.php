<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\servicio_tecnico;

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION['rol'] ?? null;

if (!(isset($cedula) && isset($rol))) {
    if (isset($_GET['ajax']) || isset($_GET['permisos']) || isset($_GET['x'])) {
        echo json_encode(["error" => "No autorizado"]);
        exit();
    }
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Servicio Tecnico";

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

$obj_servicio = new servicio_tecnico();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($obj_servicio->procesarSolicitud('listar'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['x'])) {
    header('Content-Type: application/json; charset=utf-8');
    $respuesta = [];
    switch ($_GET['x']) {
        case 'clientes':
            $respuesta = $obj_servicio->listarClientes();
            break;
        case 'marcas':
            $respuesta = $obj_servicio->listarMarca();
            break;
        case 'especialidades':
            $respuesta = $obj_servicio->listarEspecialidad();
            break;
        case 'productos':
    $filtro = $_GET['q'] ?? ''; 
    $respuesta = $obj_servicio->listarProductos($filtro); 
    header('Content-Type: application/json; charset=utf-8'); // Asegura formato
    echo json_encode($respuesta); 
    exit(); 
            break;
        case 'metodos_pago':
            $respuesta = $obj_servicio->listarMetodosPago();
            break;
        case 'consultar_productos':
            $obj_servicio->setId_servicio((int)($_GET['id'] ?? 0));
            $respuesta = $obj_servicio->procesarSolicitud('consultar_productos');
            break;
    }
    echo json_encode($respuesta);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    if (ob_get_length()) ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    
    $accion = $_POST['accion'];
    $accionesEscritura = ['registrar', 'modificar', 'eliminar', 'agregar_producto', 'cobrar'];
    
    if (in_array($accion, $accionesEscritura) && !$obj_usuario->tienePermiso($modulo_actual, $accion === 'agregar_producto' || $accion === 'cobrar' ? 'modificar' : $accion)) {
        echo json_encode(["error" => "No tienes permisos"]);
        exit();
    }

    switch ($accion) {
        case 'consultar':
            $obj_servicio->setId_servicio((int)($_POST['id'] ?? 0));
            echo json_encode($obj_servicio->procesarSolicitud('consultar'));
            break;
        case 'registrar':
    $datos = [
        'cedula' => trim($_POST['cedula_persona'] ?? ''),
        'equipo' => trim($_POST['equipo'] ?? ''),        
        'falla'  => trim($_POST['falla'] ?? ''),
        'especialidad' => (int)($_POST['especialidad'] ?? 0) 
    ];
    echo json_encode($obj_servicio->procesarSolicitud('registrar', $datos));
    break;
       case 'modificar':
    $datos = [
        'id'          => (int)($_POST['id'] ?? 0),
        'equipo'      => trim($_POST['equipo'] ?? ''),
        'falla'       => trim($_POST['falla'] ?? ''),
        'diagnostico' => trim($_POST['diagnostico'] ?? ''),
        'estado'      => trim($_POST['estado'] ?? ''),
        'monto'       => (float)($_POST['monto'] ?? 0),
        'productos'   => isset($_POST['productos']) ? json_decode($_POST['productos'], true) : []
    ];
    echo json_encode($obj_servicio->procesarSolicitud('modificar', $datos));
    break;
        case 'cobrar':
            $datos = [
                'id'           => (int)($_POST['id_servicio_cobro'] ?? 0),
                'monto'        => trim($_POST['monto_total_cobro'] ?? '0'),
                'diagnostico'  => trim($_POST['diagnostico_cobro'] ?? ''),
                'nota_tecnico' => trim($_POST['nota_tecnico_cobro'] ?? '')
            ];
            echo json_encode($obj_servicio->procesarSolicitud('cobrar', $datos));
            break;
        case 'eliminar':
            $obj_servicio->setId_servicio((int)($_POST['id'] ?? 0));
            echo json_encode($obj_servicio->procesarSolicitud('eliminar'));
            break;
        case 'agregar_producto':
            echo json_encode($obj_servicio->agregarProductoServicio(
                (int)$_POST['id_servicio'],
                (int)$_POST['id_producto'],
                (int)$_POST['cantidad']
            ));
            break;
    }
    exit();
}

include 'app/views/servicio_tecnico.php';
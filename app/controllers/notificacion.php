<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\notificacion;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_GET['action'] ?? null;
$usuario_sesion = $_SESSION['username'] ?? $_SESSION['cliente_cedula'] ?? null;

if ($action === 'DatosDashboardCliente') {
    header('Content-Type: application/json; charset=utf-8');
    
    if (!$usuario_sesion) {
        echo json_encode(["error" => "Sesion no encontrada o expirada"]);
        exit();
    }
}

$cedula = $usuario_sesion;
$rol = $_SESSION["rol"] ?? '6';
$_SESSION["rol"] = $rol;

if (!$cedula) {
    header("Location: ?pagina=loginEcommerce");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Notificacion";

if (isset($_GET['permisos'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "listar" => $obj_usuario->tienePermiso($modulo_actual, "listar"),
        "marcar_leida" => $obj_usuario->tienePermiso($modulo_actual, "marcar_leida")
    ]);
    exit();
}

if (isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    if (ob_get_length()) ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    header("Cache-Control: no-cache, must-revalidate");
    
    try {
        $objeto = new notificacion();
        $objeto->setCedula_usuario($usuario_sesion);
        
        if (isset($_GET['x']) && $_GET['x'] === "listar") {
            $historial = (isset($_GET['historial']) && $_GET['historial'] === 'true');
            echo json_encode(["data" => $objeto->listar(!$historial)]);
        } elseif (isset($_GET['x']) && $_GET['x'] === "marcar_leida") {
            $id = $_POST['id'] ?? null;
            if ($id && $obj_usuario->tienePermiso($modulo_actual, "marcar_leida")) {
                $objeto->setId_notificacion((int)$id);
                echo json_encode(["success" => $objeto->marcarLeida()]);
            } else {
                echo json_encode(["success" => false]);
            }
        } elseif (isset($_GET['x']) && $_GET['x'] === "marcar_todas_leidas") {
            if ($obj_usuario->tienePermiso($modulo_actual, "marcar_leida")) {
                $pendientes = $objeto->listar(true);
                $exito = true;
                
                if (!empty($pendientes)) {
                    foreach ($pendientes as $notif) {
                        $objeto->setId_notificacion((int)$notif['id_notificacion']);
                        if (!$objeto->marcarLeida()) {
                            $exito = false;
                        }
                    }
                }
                echo json_encode(["success" => $exito]);
            } else {
                echo json_encode(["success" => false]);
            }
        }
    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit();
}

$rolCrudo = is_array($rol) ? ($rol['descripcion_rol'] ?? '') : $rol;
$rolLimpio = strtolower(trim((string)$rolCrudo));

if ($rolLimpio === 'cliente' || $rolLimpio === '6') {
    $vista = 'app/views/notificacion_cliente.php';
} else {
    $vista = 'app/views/notificacion.php';
}

if (file_exists($vista)) {
    require_once $vista;
} else {
    require_once 'app/views/error_404.php';
}
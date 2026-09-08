<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\orden;
use App\Sistema\models\chequeo_orden;
use App\Sistema\models\servicio_venta;

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION['rol'] ?? null;

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Chequeo Orden";

if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['permisos'])) {
    echo json_encode([
        "registrar"  => $obj_usuario->tienePermiso($modulo_actual, "registrar"),
        "modificar"  => $obj_usuario->tienePermiso($modulo_actual, "modificar"),
        "eliminar"   => $obj_usuario->tienePermiso($modulo_actual, "eliminar")
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    $obj_orden = new chequeo_orden();
    $ordenes = $obj_orden->listarPendientes();
    echo json_encode($ordenes);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'consultar') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(["error" => "ID de orden inválido."]);
        exit();
    }

    $obj_orden = new orden();
    $orden = $obj_orden->consultar($id);

    if (!$orden || (is_array($orden) && isset($orden['error']))) {
        $mensaje = is_array($orden) && isset($orden['error']) ? $orden['error'] : 'Orden no encontrada.';
        echo json_encode(["error" => $mensaje]);
        exit();
    }

    echo json_encode($orden);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'procesar') {
    if (!$obj_usuario->tienePermiso($modulo_actual, "modificar")) {
        echo json_encode(["error" => "No tienes permisos para procesar esta orden."]);
        exit();
    }

    $id = (int)($_POST['id'] ?? 0);
    $referencia = trim($_POST['pago_referencia'] ?? '');
    $metodo_pago = trim($_POST['metodo_pago'] ?? '');

    if ($id <= 0) {
        echo json_encode(["error" => "ID de orden inválido."]);
        exit();
    }

    if ($referencia === '') {
        echo json_encode(["error" => "Debes ingresar la referencia de pago."]);
        exit();
    }

    $obj_orden = new chequeo_orden();
    $obj_orden->setId_orden($id);
    $obj_orden->setEstado(1);
    $respuesta = $obj_orden->procesarPago($referencia, $metodo_pago);

    if (isset($respuesta["error"])) {
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }

    if (isset($respuesta["invalido"])) {
        echo json_encode(["error" => $respuesta["invalido"]]);
        exit();
    }

    $ordenModel = new orden();
    $ordenData = $ordenModel->consultar($id);
    if ($ordenData && !isset($ordenData['error'])) {
        $servicioVenta = new servicio_venta();
        $registro = $servicioVenta->registrar(
            $ordenData['cliente'] ?: 'Cliente Servicio Técnico',
            $metodo_pago !== '' ? $metodo_pago : 'No definido',
            $referencia,
            floatval($ordenData['total_orden'] ?? 0)
        );

        if (isset($registro['error'])) {
            echo json_encode(["error" => "Pago procesado, pero no se guardó el control del servicio: " . $registro['error']]);
            exit();
        }
    }

    echo json_encode(["success" => "Pago procesado y orden movida a Reparación."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    if (!$obj_usuario->tienePermiso($modulo_actual, "modificar")) {
        echo json_encode(["error" => "No tienes permisos para eliminar esta orden."]);
        exit();
    }

    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(["error" => "ID de orden inválido."]);
        exit();
    }

    $obj_orden = new orden();
    $obj_orden->setId_orden($id);
    $respuesta = $obj_orden->eliminar();

    if (isset($respuesta["error"])) {
        echo json_encode(["error" => $respuesta["error"]]);
    } elseif (isset($respuesta["invalido"])) {
        echo json_encode(["error" => $respuesta["invalido"]]);
    } else {
        echo json_encode(["success" => "Orden eliminada correctamente."]);
    }
    exit();
}

include 'app/views/chequeo_orden.php';

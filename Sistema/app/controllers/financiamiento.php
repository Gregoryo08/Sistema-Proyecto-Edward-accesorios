<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\financiamiento;
use App\Sistema\models\scrape_dolar;

$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Financiamiento";

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

$objeto_finan = new financiamiento();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    if (ob_get_length()) ob_clean(); 
    header('Content-Type: application/json');
    
    $opcion = $_GET['x'] ?? '';

    switch ($opcion) {
        case "listado":
            echo json_encode($objeto_finan->actualizarSaldosYMoras());
            break;
        case "clientes":
            echo json_encode($objeto_finan->listarClientes());
            break;
        case "telefonos_disponibles":
            echo json_encode($objeto_finan->listarTelefonosDisponibles());
            break;
        case "metodos":
            echo json_encode($objeto_finan->listarMetodos());
            break;
        case "bancos":
            echo json_encode($objeto_finan->listarBancos());
            break;
        case "tasa_bcv":
            echo json_encode(["tasa" => scrape_dolar::obtenerPrecioDolarBCV()]);
            break;
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    header('Content-Type: application/json');
    $accion = $_POST['accion'];

    if ($accion === 'anularFinanciamiento') {
        $objeto_finan->setId_financiamiento((int)$_POST['id']);
        echo json_encode($objeto_finan->anularFinanciamiento());
        exit();
    }

    if ($accion === 'consultarCuotas') {
        $id = (int) trim($_POST["id"]);
        $objeto_finan->setId_financiamiento($id);
        echo json_encode($objeto_finan->consultarCuotas());
        exit();
    }

    if ($accion === 'registrarPagoCuota') {
        $id_cuota = $_POST['id_cuota'];
        $monto = $_POST['monto_pagado'];
        $metodo = $_POST['id_metodopago'];
        $respuesta = $objeto_finan->registrarPago($id_cuota, $monto, $metodo);
        echo json_encode(isset($respuesta["success"]) ? ["success" => true] : ["error" => $respuesta["error"]]);
        exit();
    }

    if ($accion === 'registrarFinanciamiento') {
        $objeto_finan->setCedula_cliente(trim($_POST['cedula_cliente'] ?? ''));
        $objeto_finan->setId_telefono((int)($_POST['id_telefono'] ?? 0));
        $objeto_finan->setMonto_total((float)($_POST['monto_total'] ?? 0));
        $objeto_finan->setPago_inicial((float)($_POST['pago_inicial'] ?? 0));
        $objeto_finan->setCantidad_cuotas((int)($_POST['cantidad_cuotas'] ?? 0));
        $objeto_finan->setDia_pago((int)($_POST['dia_pago'] ?? 0));
        $objeto_finan->setFecha_inicio(trim($_POST['fecha_inicio'] ?? ''));
        echo json_encode($objeto_finan->registrar());
        exit();
    }

    if ($accion === 'cambiarEstadoEquipo') {
        $objeto_finan->setId_financiamiento((int)$_POST['id']);
        $res = $objeto_finan->cambiarEstadoEquipo(trim($_POST['estado']));
        echo json_encode(["success" => $res]);
        exit();
    }

    if ($accion === 'finalizarContrato') {
        $objeto_finan->setId_financiamiento((int)$_POST['id']);
        $res = $objeto_finan->finalizarContratoManualmente();
        echo json_encode(["success" => $res]);
        exit();
    }
}

include 'App/views/financiamiento.php';
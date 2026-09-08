<?php
// /src/app/controllers/verPedido.php

use App\Sistema\models\PedidoModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['es_ecommerce']) || $_SESSION['es_ecommerce'] !== true) {
    header('Location: ?pagina=loginTienda');
    exit;
}

$id_pedido = intval($_GET['id'] ?? 0);

if ($id_pedido <= 0) {
    header('Location: ?pagina=misPedidos');
    exit;
}

try {
    $pedidoModel = new PedidoModel();
    $cedula = $_SESSION['cliente_cedula'];

    // ✅ Obtener pedido con datos del cliente
    $pedido = $pedidoModel->obtenerPorId($id_pedido);

    // ✅ Verificar que el pedido existe y pertenece al cliente
    if (!$pedido || ($pedido['cedula_persona'] ?? '') !== $cedula) {
        header('Location: ?pagina=misPedidos');
        exit;
    }

    // ✅ Obtener detalle de productos
    $detalle = $pedidoModel->obtenerDetalle($id_pedido);

} catch (Exception $e) {
    $pedido = null;
    $detalle = [];
    error_log("❌ Error en verPedido: " . $e->getMessage());
}

if (!$pedido) {
    header('Location: ?pagina=misPedidos');
    exit;
}

// ✅ Cargar la vista (pasar $pedidoModel para usar getEstadoData)
require_once __DIR__ . '/../views/verPedido.php';
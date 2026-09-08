<?php
// /src/app/controllers/misPedidos.php

use App\Sistema\models\PedidoModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['es_ecommerce']) || $_SESSION['es_ecommerce'] !== true) {
    header('Location: ?pagina=loginTienda');
    exit;
}

try {
    $pedidoModel = new PedidoModel();
    $cedula = $_SESSION['cliente_cedula'];
    $pedidos = $pedidoModel->obtenerPorCliente($cedula);
    
} catch (Exception $e) {
    $pedidos = [];
    error_log("❌ Error en misPedidos: " . $e->getMessage());
}

// ✅ Cargar la vista (pasar $pedidoModel para usar getEstadoData)
require_once __DIR__ . '/../views/misPedidos.php';
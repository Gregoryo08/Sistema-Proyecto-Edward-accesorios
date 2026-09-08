<?php
// /src/app/controllers/confirmacion.php
// =============================================
// CONFIRMACIÓN DE PEDIDO - CONTROLADOR
// =============================================

namespace App\Sistema\Controllers;

use App\Sistema\models\PedidoModel;
use App\Sistema\models\TasaCambioModel;

// La sesión ya fue iniciada por el FrontController
// No es necesario volver a iniciarla

try {
    // 1. Obtener el ID del pedido de la URL
    $id_pedido = intval($_GET['id'] ?? 0);
    
    if ($id_pedido <= 0) {
        header('Location: ?pagina=web_Catalogo');
        exit;
    }
    
    // 2. Verificar que el cliente está logueado
    $cedula = $_SESSION['cliente_cedula'] ?? '';
    if (empty($cedula)) {
        header('Location: ?pagina=loginTienda');
        exit;
    }
    
    // 3. Obtener datos del pedido usando el modelo
    $pedidoModel = new PedidoModel();
    $pedido = $pedidoModel->obtenerPorIdYCliente($id_pedido, $cedula);
    
    if (!$pedido) {
        header('Location: ?pagina=web_Catalogo');
        exit;
    }
    
    // 4. Obtener los detalles del pedido (productos)
    $detalle = $pedidoModel->obtenerDetalle($id_pedido);
    
    // 5. Obtener datos de la tasa de cambio
    $tasaModel = new TasaCambioModel();
    $tasa = $tasaModel->obtener();
    $tasa_fecha = $tasaModel->obtenerFechaActualizacion();
    $vigencia = $tasaModel->vigencia();
    
    // 6. Título de la página
    $titulo = 'Confirmación de Pedido';
    
} catch (Exception $e) {
    $pedido = null;
    $detalle = [];
    $tasaModel = new TasaCambioModel();
    $tasa = $tasaModel->obtener();
    $tasa_fecha = $tasaModel->obtenerFechaActualizacion();
    $vigencia = $tasaModel->vigencia();
    $titulo = 'Error en Confirmación';
    error_log("Error en confirmacion: " . $e->getMessage());
}

// 6. Si no hay pedido, redirigir
if (!$pedido) {
    header('Location: ?pagina=web_Catalogo');
    exit;
}

// 7. Cargar la vista
require_once __DIR__ . '/../views/confirmacion.php';
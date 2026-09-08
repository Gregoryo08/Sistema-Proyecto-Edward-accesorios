<?php

use App\Sistema\models\Checkout;
use App\Sistema\models\PedidoModel;
use App\Sistema\models\TasaCambioModel;
use App\Sistema\models\BancoReceptorModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $id_pedido = intval($_GET['pedido'] ?? 0);
    $metodo = $_GET['metodo'] ?? 'transferencia';

    if ($id_pedido <= 0) {
        echo "Pedido no encontrado";
        exit;
    }

    if (!in_array($metodo, ['transferencia', 'pago_movil'])) {
        echo "Método de pago inválido";
        exit;
    }

    $pedidoModel = new PedidoModel();
    $pedido = $pedidoModel->obtenerPorIdYCliente($id_pedido, $_SESSION['cliente_cedula'] ?? '');

    if (!$pedido) {
        echo "Pedido no encontrado";
        exit;
    }

    $tasaModel = new TasaCambioModel();
    $tasa = $tasaModel->obtener();
    $total_bs = $tasaModel->convertir($pedido['total']);
    $vigencia = $tasaModel->vigencia();
    $fecha_tasa = $tasaModel->obtenerFechaActualizacion();

// =============================================
    // BANCO RECEPTOR → viene de constantes en BancoReceptorModel
    // (NO hay tabla en BD; la estructura firmada prohíbe tablas).
    // Antes estaba quemado en Checkout.php (CÓDIGO DURO).
    // =============================================
    $receptorModel = new BancoReceptorModel();
    $datos_pago = $receptorModel->obtenerDatosPago($metodo);

    // Respaldo de emergencia: si no hay configuración, se usan los datos
    // que antes estaban quemados en Checkout (comportamiento previo).
    if (!$datos_pago) {
        error_log("datosPago: sin banco receptor configurado en BancoReceptorModel para método '$metodo' — usando respaldo de Checkout");
        $checkout = new Checkout();
        if ($metodo === 'transferencia') {
            $datos_pago = $checkout->ejecutar('obtenerDatosBanco', ['banco_id' => null]);
        } else {
            $datos_pago = $checkout->ejecutar('obtenerDatosPagoMovil');
        }
    }

    if ($metodo === 'transferencia') {
        $titulo = "Transferencia Bancaria";
    } else {
        $titulo = "Pago M�vil";
    }

} catch (Exception $e) {
    error_log("Error en datosPago: " . $e->getMessage());
    echo "Error al cargar datos de pago";
    exit;
}

require_once __DIR__ . '/../views/datosPago.php';

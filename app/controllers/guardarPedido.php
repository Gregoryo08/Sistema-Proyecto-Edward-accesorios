<?php
// /src/app/controllers/guardarPedido.php
namespace App\Sistema\Controllers;

use App\Sistema\models\PedidoModel;
use App\Sistema\models\ProductoModel;
use App\Sistema\models\ClienteEcommerce;

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Verificar que el cliente está logueado
    $clienteEcommerce = new ClienteEcommerce();
    if (!$clienteEcommerce->ejecutar('estaLogueado')) {
        echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión']);
        exit;
    }

    // Obtener datos del POST
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['carrito'])) {
        echo json_encode(['success' => false, 'error' => 'Carrito vacío']);
        exit;
    }

    // Datos del cliente desde sesión
    $cedula = $_SESSION['cliente_cedula'];
    $nombre = $_SESSION['cliente_nombre'] ?? 'Cliente';
    $telefono = $_SESSION['cliente_telefono'] ?? '';
    $email = $_SESSION['cliente_correo'] ?? '';

    // Datos del pedido
    $carrito = $data['carrito'];
    $_SESSION['carrito'] = $carrito;
    $total = floatval($data['total'] ?? 0);
    $metodo_pago = $data['metodo_pago'] ?? 'transferencia';
    $banco_emisor = $data['banco_emisor'] ?? null;
    $tipo_entrega = $data['entrega'] ?? 'retiro';
    $direccion_entrega = $data['direccion'] ?? null;
    $costoEnvio = floatval($data['costo_envio'] ?? 0);

    // Validar
    if ($total <= 0) {
        echo json_encode(['success' => false, 'error' => 'Total inválido']);
        exit;
    }
    if ($tipo_entrega === 'delivery' && empty($direccion_entrega)) {
        echo json_encode(['success' => false, 'error' => 'Dirección de entrega requerida']);
        exit;
    }

    // Validar montos contra BD
    $productoModel = new ProductoModel();
    $subtotal = 0;
    foreach ($carrito as $item) {
        $producto = $productoModel->ejecutar('obtenerPorId', ['id' => $item['id']]);
        if (!$producto) {
            echo json_encode(['success' => false, 'error' => 'Producto no encontrado: ' . $item['id']]);
            exit;
        }
        $precioEsperado = floatval($producto['precio_detal']);
        $precioRecibido = floatval($item['precio']);
        if (abs($precioEsperado - $precioRecibido) > 0.01) {
            echo json_encode(['success' => false, 'error' => 'Precio incorrecto para: ' . $producto['nombre_producto']]);
            exit;
        }
        $cantidad = intval($item['cantidad']);
        if ($cantidad < 1) {
            echo json_encode(['success' => false, 'error' => 'Cantidad inválida para: ' . $producto['nombre_producto']]);
            exit;
        }
        $subtotal += $precioEsperado * $cantidad;
    }
    // Mismo criterio que CarritoModel::calcularTotal (envío gratis desde $100)
    if ($tipo_entrega === 'delivery') {
        $costoEnvio = $subtotal >= 100 ? 0 : 10;
    } else {
        $costoEnvio = 0;
    }
    $totalEsperado = $subtotal + $costoEnvio;
    if (abs($total - $totalEsperado) > 0.01) {
        echo json_encode(['success' => false, 'error' => 'El total no coincide con los productos' . ' (esperado: ' . number_format($totalEsperado, 2) . ')']);
        exit;
    }

    // Guardar pedido
    $pedidoModel = new PedidoModel();
    $pedidoModel->iniciarTransaccion();

    $id_pedido = $pedidoModel->crear(
        $cedula,
        $nombre,
        $telefono,
        $email,
        $subtotal,
        $costoEnvio,
        $totalEsperado,
        $metodo_pago
    );

    if (!$id_pedido) {
        throw new \Exception('Error al crear el pedido');
    }

    // Si es delivery, actualizar dirección
    if ($tipo_entrega === 'delivery' && !empty($direccion_entrega)) {
        $pedidoModel->actualizarDireccionEntrega($id_pedido, $direccion_entrega);
    }

    // Guardar detalles
    foreach ($carrito as $item) {
            $pedidoModel->agregarDetalle(
                $id_pedido,
                $item['id'],
                $item['cantidad'],
                $item['precio'],
                $item['precio'] * $item['cantidad']
             );
    }

    // Guardar datos de pago
    $datosPago = [
        'metodo' => $metodo_pago,
        'banco_emisor' => $banco_emisor,
        'tipo_entrega' => $tipo_entrega,
        'fecha' => date('Y-m-d H:i:s')
    ];
    $pedidoModel->actualizarDatosPago($id_pedido, json_encode($datosPago));

    $pedidoModel->confirmarTransaccion();

    echo json_encode(['success' => true, 'id_pedido' => $id_pedido]);

} catch (\Exception $e) {
    if (isset($pedidoModel)) {
        $pedidoModel->revertirTransaccion();
    }
    error_log("Error en guardarPedido: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
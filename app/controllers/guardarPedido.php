<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Forzar cabecera JSON obligatoria para checkout.js
header('Content-Type: application/json');

use App\Sistema\models\EcommerceModel;

// 2. Validar sesión del cliente
$cedulaSesion = $_SESSION['cedula'] ?? $_SESSION['cliente_cedula'] ?? $_SESSION['cliente_logueado'] ?? null;

if (empty($cedulaSesion)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Debe iniciar sesión para procesar el pedido.'
    ]);
    exit();
}

// 3. Leer el JSON enviado por AJAX desde checkout.js
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

if (empty($data) && !empty($_POST)) {
    $data = $_POST;
}

if (empty($data)) {
    echo json_encode([
        'success' => false, 
        'error' => 'No se recibieron datos válidos del formulario.'
    ]);
    exit();
}

// 4. Extraer los datos enviados por el frontend
$cliente     = $data['cliente'] ?? [];
$nombre      = $cliente['nombre'] ?? $data['nombre'] ?? 'Cliente';
$email       = $cliente['email'] ?? $data['email'] ?? '';
$telefono    = $cliente['telefono'] ?? $data['telefono'] ?? '';
$cedula      = !empty($cliente['cedula']) ? $cliente['cedula'] : (!empty($data['cedula']) ? $data['cedula'] : $cedulaSesion);
$tipoEntrega = $data['entrega'] ?? 'retiro';
$direccion   = $data['direccion'] ?? '';
$costoEnvio  = floatval($data['costo_envio'] ?? 0);
$subtotal    = 0;

$carrito     = $data['carrito'] ?? [];
foreach ($carrito as $item) {
    $subtotal += (floatval($item['precio']) * intval($item['cantidad']));
}

$total       = $subtotal + $costoEnvio;
if (isset($data['total']) && floatval($data['total']) > 0) {
    $total = floatval($data['total']);
}

$metodoPago  = $data['metodo_pago'] ?? 'transferencia';

try {
    $modelo = new EcommerceModel();
    
    // Iniciar transacción usando las funciones del modelo[cite: 4]
    $modelo->PedidoiniciarTransaccion();

    // 5. Usar el método Pedidocrear del modelo que sí incluye nombre, teléfono, correo y subtotal[cite: 4]
    $idPedidoGenerado = $modelo->Pedidocrear(
        $cedula,
        $nombre,
        $telefono,
        $email,
        $subtotal,
        $costoEnvio,
        $total,
        $metodoPago
    );

    if ($idPedidoGenerado && is_numeric($idPedidoGenerado)) {
        
        // Si hay dirección de delivery, guardarla con el método del modelo[cite: 4]
        if ($tipoEntrega === 'delivery' && !empty($direccion)) {
            $modelo->PedidoactualizarDireccionEntrega($idPedidoGenerado, $direccion);
        }

        // 6. Registrar cada ítem del carrito usando el método PedidoagregarDetalle[cite: 4]
        foreach ($carrito as $item) {
            $idProducto = $item['id'] ?? $item['id_producto'] ?? 0;
            $cantidad   = intval($item['cantidad'] ?? 1);
            $precio     = floatval($item['precio'] ?? 0);
            $subtItem   = $cantidad * $precio;

            if ($idProducto > 0) {
                $modelo->PedidoagregarDetalle($idPedidoGenerado, $idProducto, $cantidad, $precio, $subtItem);
            }
        }

        // Confirmar transacción de forma segura[cite: 4]
        $modelo->PedidoconfirmarTransaccion();

        // Responder con éxito y el ID del pedido para que checkout.js redirija limpiamente
        echo json_encode([
            'success'   => true,
            'id_pedido' => $idPedidoGenerado
        ]);
    } else {
        $modelo->PedidorevertirTransaccion();
        echo json_encode([
            'success' => false, 
            'error'   => 'No se pudo registrar el pedido en la base de datos.'
        ]);
    }
} catch (\Exception $e) {
    if (isset($modelo)) {
        $modelo->PedidorevertirTransaccion();
    }
    echo json_encode([
        'success' => false, 
        'error'   => 'Error en el servidor: ' . $e->getMessage()
    ]);
}
exit();
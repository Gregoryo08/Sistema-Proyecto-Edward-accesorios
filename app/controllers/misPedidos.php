<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Sistema\models\EcommerceModel;

// 1. Validar que el cliente haya iniciado sesión
$cedulaCliente = $_SESSION['cedula'] ?? $_SESSION['cliente_cedula'] ?? $_SESSION['cliente_logueado'] ?? null;

if (empty($cedulaCliente)) {
    $_SESSION['destino_after_login'] = 'checkout';
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$modelo = new EcommerceModel();
$pedidos = [];

try {
    // 2. Intentar obtener los pedidos del cliente usando el método del modelo si existe
    if (method_exists($modelo, 'PedidosobtenerPorCliente')) {
        $pedidos = $modelo->PedidosobtenerPorCliente($cedulaCliente);
    } elseif (method_exists($modelo, 'obtenerPedidosPorCliente')) {
        $pedidos = $modelo->obtenerPedidosPorCliente($cedulaCliente);
    }
} catch (\Exception $e) {
    $pedidos = [];
}

// 3. Respaldo de seguridad directo por PDO si el modelo no devolvió resultados o no tiene el método
if (empty($pedidos)) {
    try {
        $conn = \App\Sistema\config\Conexion::getShared('sistema_edward')->getConexion();
        $stmt = $conn->prepare("SELECT * FROM pedidos WHERE cedula = ? ORDER BY id_pedido DESC");
        $stmt->execute([$cedulaCliente]);
        $pedidos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\Exception $ex) {
        $pedidos = [];
    }
}

// 4. Cargar la vista correspondiente de mis pedidos
$vista = __DIR__ . '/../views/mispedidos.php';

if (file_exists($vista)) {
    require_once $vista;
} else {
    echo "Error: No se encuentra la vista 'misPedidos.php'.";
}
exit();
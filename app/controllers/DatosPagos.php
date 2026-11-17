<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Sistema\models\EcommerceModel;
use App\Sistema\models\TasaCambioModel;

// 1. Validar que el cliente haya iniciado sesión
$cedulaCliente = $_SESSION['cedula'] ?? $_SESSION['cliente_cedula'] ?? $_SESSION['cliente_logueado'] ?? null;

if (empty($cedulaCliente)) {
    $_SESSION['destino_after_login'] = 'checkout';
    header("Location: ?pagina=iniciarSesion");
    exit();
}

// 2. Obtener parámetros de la URL
$id_pedido = $_GET['pedido'] ?? $_GET['id'] ?? $_GET['id_pedido'] ?? null;
$metodoRaw = $_GET['metodo'] ?? $_GET['metodo_pago'] ?? 'pago_movil';

$metodo = strtolower(trim($metodoRaw));
if (strpos($metodo, 'transf') !== false) {
    $metodo = 'transferencia';
} else {
    $metodo = 'pago_movil';
}

if (empty($id_pedido)) {
    header("Location: ?pagina=checkout");
    exit();
}

// 3. Instanciar modelos
$modelo = new EcommerceModel();
$tasaModel = new TasaCambioModel();

$pedido = null;
try {
    // Intentar buscar el pedido por ID y cliente
    if (method_exists($modelo, 'PedidoobtenerPorIdYCliente')) {
        $pedido = $modelo->PedidoobtenerPorIdYCliente($id_pedido, $cedulaCliente);
    }
    
    // Si no lo encuentra, intentar por ID directo
    if (!$pedido && method_exists($modelo, 'PedidoobtenerPorId')) {
        $pedido = $modelo->PedidoobtenerPorId($id_pedido);
    }
} catch (\Exception $e) {
    $pedido = null;
}

// 4. Respaldo de seguridad directo por PDO si el modelo falla o el objeto viene vacío
if (!$pedido) {
    try {
        $conn = \App\Sistema\config\Conexion::getShared('sistema_edward')->getConexion();
        $stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = ? LIMIT 1");
        $stmt->execute([$id_pedido]);
        $pedido = $stmt->fetch(\PDO::FETCH_ASSOC);
    } catch (\Exception $ex) {
        $pedido = ['total' => 0.00];
    }
}

// 5. Calcular valores de moneda y tasa
$tasa = $tasaModel->obtener();
$vigencia = $tasaModel->vigencia();

$totalDolares = floatval($pedido['total'] ?? $pedido['monto'] ?? 0);
$totalBs = $totalDolares * floatval($tasa);

// Opcional: Actualizar el total en bolívares en la base de datos si existe el método
if (method_exists($modelo, 'PedidoactualizarTotalBs') && $tasa > 0) {
    try {
        $modelo->PedidoactualizarTotalBs($id_pedido, $totalBs, $tasa);
    } catch (\Exception $e) {}
}

// 6. Cargar la vista correspondiente
$vista = __DIR__ . '/../views/datosPago.php';

if (file_exists($vista)) {
    require_once $vista;
} else {
    echo "Error: No se encuentra la vista de datos de pago.";
}
exit();
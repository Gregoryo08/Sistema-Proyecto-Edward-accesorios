<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Sistema\models\EcommerceModel;
use App\Sistema\models\TasaCambioModel;

// 1. Validar sesión del cliente
$cedulaCliente = $_SESSION['cedula'] ?? $_SESSION['cliente_cedula'] ?? $_SESSION['cliente_logueado'] ?? null;

if (empty($cedulaCliente)) {
    $_SESSION['destino_after_login'] = 'checkout';
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$modelo = new EcommerceModel();
$tasaModel = new TasaCambioModel();

// 2. Obtener parámetros (pedido y método)
$id_pedido = $_GET['pedido'] ?? $_GET['id'] ?? $_GET['id_pedido'] ?? $_POST['pedido'] ?? null;
$metodo = $_GET['metodo'] ?? $_GET['metodo_pago'] ?? $_POST['metodo'] ?? 'pago_movil';

if (empty($id_pedido)) {
    header("Location: ?pagina=carrito");
    exit();
}

// 3. Obtener información del pedido
$pedido = null;
try {
    if (method_exists($modelo, 'PedidoobtenerPorIdYCliente')) {
        $pedido = $modelo->PedidoobtenerPorIdYCliente($id_pedido, $cedulaCliente);
    }
    if (!$pedido && method_exists($modelo, 'PedidoobtenerPorId')) {
        $pedido = $modelo->PedidoobtenerPorId($id_pedido);
    }
} catch (\Exception $e) {
    $pedido = null;
}

// Respaldo de seguridad directo por PDO si el modelo falla
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

// 4. Obtener tasa actual y calcular montos
$tasa = $tasaModel->obtener();
$totalDolares = floatval($pedido['total'] ?? $pedido['monto'] ?? 0);
$totalBs = $totalDolares * floatval($tasa);

// 5. Procesar si el formulario fue enviado (Método POST)
$error = null;
$exito = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bancoOrigen = $_POST['banco_origen'] ?? '';
    $telefonoOrigen = $_POST['telefono_origen'] ?? '';
    $referencia = $_POST['referencia'] ?? '';
    $montoReportado = floatval($_POST['monto_reportado'] ?? 0);
    $cedulaReporte = $_POST['cedula_origen'] ?? $cedulaCliente;

    // Validaciones básicas
    if (empty($referencia) || empty($bancoOrigen)) {
        $error = "Por favor, completa los campos obligatorios (Banco y Número de Referencia).";
    } else {
        try {
            // Guardar el reporte de pago en la base de datos (asegúrate de que tu modelo tenga un método afín, o haz el insert por PDO)
            if (method_exists($modelo, 'guardarReportePago')) {
                $modelo->guardarReportePago([
                    'id_pedido' => $id_pedido,
                    'banco' => $bancoOrigen,
                    'telefono' => $telefonoOrigen,
                    'referencia' => $referencia,
                    'monto' => $montoReportado,
                    'cedula' => $cedulaReporte,
                    'metodo' => $metodo
                ]);
            } else {
                // Inserción de respaldo por PDO si el método no existe en el modelo
                $conn = \App\Sistema\config\Conexion::getShared('sistema_edward')->getConexion();
                $stmt = $conn->prepare("INSERT INTO pagos_reportados (id_pedido, banco_origen, telefono_origen, referencia, monto, cedula, metodo, fecha) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$id_pedido, $bancoOrigen, $telefonoOrigen, $referencia, $montoReportado, $cedulaReporte, $metodo]);
            }

            $exito = "¡Pago reportado con éxito! Tu pedido pasará a verificación.";
          
        } catch (\Exception $e) {
            $error = "Error al guardar el reporte: " . $e->getMessage();
        }
    }
}

// 6. Cargar la vista de reporte
$vista = __DIR__ . '/../views/reportarPago.php';

if (file_exists($vista)) {
    require_once $vista;
} else {
    echo "Error: No se encuentra la vista 'reportarPago.php'.";
}
exit();
<?php
// controllers/getEstadisticasCliente.php
header('Content-Type: application/json');

$cedula = $_GET['cedula'] ?? '';

if (!$cedula) {
    echo json_encode(['error' => 'Cédula requerida']);
    exit;
}

// ============================================
// USAR NAMESPACE CORRECTAMENTE
// ============================================
require_once __DIR__ . '/../config/Conexion.php';

use App\Sistema\config\Conexion;

try {
    $conexion = new Conexion('src');
    $conn = $conexion->getConexion();
    
    // Total pedidos y gastado
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_pedidos,
            COALESCE(SUM(total), 0) as total_gastado,
            COALESCE(AVG(total), 0) as promedio_pedido,
            MAX(fecha) as ultima_compra
        FROM pedidos 
        WHERE cedula_persona = ? AND estado = 'pagado'
    ");
    $stmt->execute([$cedula]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Si no hay datos
    if (!$stats || $stats['total_pedidos'] == 0) {
        echo json_encode([
            'total_pedidos' => 0,
            'total_gastado' => '0.00',
            'promedio_pedido' => '0.00',
            'producto_favorito' => 'Sin compras',
            'metodo_preferido' => 'N/A',
            'ultima_compra' => 'N/A',
            'metodos_pago' => [],
            'ventas_mensuales' => []
        ]);
        exit;
    }
    
    // Producto favorito
    $stmt = $conn->prepare("
        SELECT dp.nombre_producto, SUM(dp.cantidad) as total
        FROM detalle_pedido dp
        JOIN pedidos p ON dp.id_pedido = p.id_pedido
        WHERE p.cedula_persona = ? AND p.estado = 'pagado'
        GROUP BY dp.nombre_producto
        ORDER BY total DESC LIMIT 1
    ");
    $stmt->execute([$cedula]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Método preferido
    $stmt = $conn->prepare("
        SELECT metodo_pago, COUNT(*) as total
        FROM pedidos 
        WHERE cedula_persona = ? AND estado = 'pagado'
        GROUP BY metodo_pago
        ORDER BY total DESC LIMIT 1
    ");
    $stmt->execute([$cedula]);
    $metodo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Métodos de pago para gráfico
    $stmt = $conn->prepare("
        SELECT 
            CASE 
                WHEN metodo_pago = 'transferencia' THEN 'Transferencia'
                WHEN metodo_pago = 'pago_movil' THEN 'Pago Móvil'
                ELSE metodo_pago
            END as nombre,
            COUNT(*) as total
        FROM pedidos 
        WHERE cedula_persona = ? AND estado = 'pagado'
        GROUP BY metodo_pago
    ");
    $stmt->execute([$cedula]);
    $metodos_pago = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ventas por mes
    $stmt = $conn->prepare("
        SELECT 
            DATE_FORMAT(fecha, '%b %Y') as mes,
            COALESCE(SUM(total), 0) as total
        FROM pedidos 
        WHERE cedula_persona = ? AND estado = 'pagado' AND fecha >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(fecha, '%Y-%m')
        ORDER BY fecha ASC
    ");
    $stmt->execute([$cedula]);
    $ventas_mensuales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'total_pedidos' => (int)$stats['total_pedidos'],
        'total_gastado' => number_format($stats['total_gastado'], 2),
        'promedio_pedido' => number_format($stats['promedio_pedido'], 2),
        'producto_favorito' => $producto['nombre_producto'] ?? 'N/A',
        'metodo_preferido' => $metodo['metodo_pago'] ?? 'N/A',
        'ultima_compra' => $stats['ultima_compra'] ? date('d/m/Y', strtotime($stats['ultima_compra'])) : 'N/A',
        'metodos_pago' => $metodos_pago,
        'ventas_mensuales' => $ventas_mensuales
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
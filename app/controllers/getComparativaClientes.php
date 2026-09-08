<?php
// controllers/getComparativaClientes.php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/Conexion.php';

use App\Sistema\config\Conexion;

try {
    $conexion = new Conexion('src');
    $conn = $conexion->getConexion();
    
    $stmt = $conn->prepare("
        SELECT 
            p.cedula_persona as cedula,
            per.nombre as nombre,
            COUNT(*) as total_pedidos,
            COALESCE(SUM(p.total), 0) as total_gastado
        FROM pedidos p
        JOIN persona per ON p.cedula_persona = per.cedula_persona
        WHERE p.estado = 'pagado'
        GROUP BY p.cedula_persona, per.nombre
        ORDER BY total_gastado DESC
    ");
    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($clientes);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
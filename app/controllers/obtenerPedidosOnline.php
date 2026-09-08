<?php
// /src/app/controllers/obtenerPedidosOnline.php
// =============================================
// OBTENER PEDIDOS ONLINE PARA EL PANEL DE CAJERA
// =============================================

namespace App\Sistema\Controllers;

use App\Sistema\models\PedidoModel;
use App\Sistema\models\Usuarios;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Verificar autenticación (solo cajeras/admin)
$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION['rol'] ?? null;
session_write_close();

if (!isset($cedula) || !isset($rol)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$obj_usuario = new Usuarios();
if (!$obj_usuario->tienePermiso('Administrar Ventas Online', 'listar')) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Sin permisos']);
    exit;
}

try {
    $pedidoModel = new PedidoModel();
    $pedidos = $pedidoModel->obtenerTodosConPago();

    header('Content-Type: application/json');
    echo json_encode($pedidos);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}

exit;
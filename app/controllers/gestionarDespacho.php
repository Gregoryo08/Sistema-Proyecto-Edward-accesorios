<?php
// /src/app/controllers/gestionarDespacho.php
// =============================================
// GESTIONAR DESPACHO (EN_RUTA, ENTREGADO, CANCELADO)
// =============================================

namespace App\Sistema\Controllers;

use App\Sistema\models\EnvioModel;
use App\Sistema\models\PedidoModel;
use App\Sistema\models\bitacora;
use App\Sistema\models\Usuarios;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =============================================
// 1. VERIFICAR AUTENTICACIÓN
// =============================================
$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION['rol'] ?? null;
session_write_close();

if (!isset($cedula) || !isset($rol)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$obj_usuario = new Usuarios();
if (!$obj_usuario->tienePermiso('Administrar Ventas Online', 'gestionar_despacho')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Sin permisos']);
    exit;
}

// =============================================
// 2. OBTENER DATOS DEL POST
// =============================================
$data = json_decode(file_get_contents('php://input'), true);
$id_pedido = intval($data['id_pedido'] ?? 0);
$accion = $data['accion'] ?? '';

if ($id_pedido <= 0 || empty($accion)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// =============================================
// 3. MAPEAR ACCIONES A ESTADOS
// pedidos y despachos usan vocabularios distintos:
// - pedidos.estado:      pendiente|revision|aprobado|enviado|entregado|rechazado|cancelado
// - despachos.estado_despacho: pendiente|asignado|en_ruta|entregado|cancelado
// =============================================
$mapa_accion_estado = [
    'en_ruta'   => ['pedido' => 'enviado',   'despacho' => 'en_ruta'],
    'entregado' => ['pedido' => 'entregado', 'despacho' => 'entregado'],
    'cancelado' => ['pedido' => 'cancelado', 'despacho' => 'cancelado'],
];

if (!isset($mapa_accion_estado[$accion])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    exit;
}

$nuevo_estado_pedido = $mapa_accion_estado[$accion]['pedido'];
$nuevo_estado_despacho = $mapa_accion_estado[$accion]['despacho'];

// =============================================
// 4. PROCESAR LA ACCIÓN
// =============================================
header('Content-Type: application/json');

try {
    $envioModel = new EnvioModel();
    $pedidoModel = new PedidoModel();

    // 4.1. Obtener el despacho por ID de pedido
    $despachos = $envioModel->ejecutar('obtenerPorPedido', ['id_pedido' => $id_pedido]);
    if (empty($despachos)) {
        echo json_encode(['success' => false, 'message' => 'No hay despacho asignado para este pedido']);
        exit;
    }

    $despacho = $despachos[0];
    $id_despacho = $despacho['id_despacho'];

    // 4.2. Actualizar estado del despacho (vocabulario de despachos)
    $resultado = $envioModel->ejecutar('actualizarEstado', ['id_despacho' => $id_despacho, 'estado' => $nuevo_estado_despacho]);
    
    if (!$resultado) {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el despacho']);
        exit;
    }

    // 4.3. Actualizar estado del pedido según la acción
    $pedidoModel->actualizarEstado($id_pedido, $nuevo_estado_pedido);

    // =============================================
    // 5. REGISTRAR EN BITÁCORA
    // =============================================
    try {
        $bitacora = new bitacora();
        $accion_bitacora = strtoupper($accion);
        $bitacora->registrar(
            'despachos',
            $accion_bitacora,
            'Ventas Online',
            $cedula,
            $id_pedido
        );
    } catch (Exception $e) {
        error_log("Error al registrar en bitácora: " . $e->getMessage());
    }

    echo json_encode([
        'success' => true,
        'message' => 'Estado actualizado correctamente',
        'estado_pedido' => $nuevo_estado_pedido,
        'estado_despacho' => $nuevo_estado_despacho
    ]);

} catch (Exception $e) {
    error_log("Error en gestionarDespacho: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
}

exit;
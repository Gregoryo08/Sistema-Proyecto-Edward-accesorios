<?php
// /src/app/controllers/procesarVerificacion.php
// =============================================
// PROCESAR VERIFICACIÓN DE PAGO (APROBAR/RECHAZAR)
// =============================================

use App\Sistema\models\PagoOnlineModel;
use App\Sistema\models\PedidoModel;
use App\Sistema\models\ProductoModel;
use App\Sistema\models\bitacora;
use App\Sistema\models\Usuarios;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =============================================
// 1. VERIFICAR AUTENTICACIÓN Y PERMISOS
// =============================================
$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION['rol'] ?? null;
session_write_close();

if (!isset($cedula) || !isset($rol)) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$obj_usuario = new Usuarios();
if (!$obj_usuario->tienePermiso('Administrar Ventas Online', 'aprobar_pago')) {
    echo json_encode(['success' => false, 'message' => 'Sin permisos para verificar pagos']);
    exit;
}

// =============================================
// 2. OBTENER DATOS DEL POST (JSON)
// =============================================
$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['id_reporte']) || empty($data['accion'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$id_reporte = intval($data['id_reporte']);
$accion = $data['accion'];
$id_pedido = intval($data['id_pedido'] ?? 0);

$pagoModel = new PagoOnlineModel();

// Si el frontend no envió id_pedido, se deduce del reporte de pago
if ($id_pedido <= 0) {
    $reporte = $pagoModel->ejecutar('obtener_por_id', ['id' => $id_reporte]);
    if (!$reporte || empty($reporte['id_pedido'])) {
        echo json_encode(['success' => false, 'message' => 'ID de pedido no proporcionado']);
        exit;
    }
    $id_pedido = intval($reporte['id_pedido']);
}

if ($accion !== 'aprobar' && $accion !== 'rechazar') {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    exit;
}

$motivo = trim((string)($data['motivo'] ?? ''));
if ($accion === 'rechazar' && $motivo === '') {
    $motivo = 'Pago no verificado por el administrador';
}

// gmgmgmgmgmgmgmgmgmgmgmgmgmgmgmggmgmgmgmgmg
// 3. INSTANCIAR MODELOS (UNA SOLA VEZ)
// gmgmgmgmgmgmgmgmgmgmgmgmgmgmgmggmgmgmgmgmg
$pedidoModel = new PedidoModel();

// gmgmgmgmgmgmgmgmgmgmgmgmgmgmgmggmgmgmgmgmg
// 4. PROCESAR VERIFICACIÓN
// gmgmgmgmgmgmgmgmgmgmgmgmgmgmgmggmgmgmgmgmg
try {
    // 4.1. Procesar la verificación en pago_online
    $resultado = $pagoModel->ejecutar('procesar_verificacion', [
        'id_reporte' => $id_reporte,
        'accion' => $accion,
        'verificado_por' => $cedula,
        'motivo_rechazo' => $motivo
    ]);

    // 4.2. Si fue exitoso, actualizar el pedido
    if ($resultado['success']) {
        
        if ($accion === 'aprobar') {
            $pedidoActualizado = $pedidoModel->actualizarEstado($id_pedido, 'aprobado');
            
            if ($pedidoActualizado) {
                $detalles = $pedidoModel->obtenerDetalle($id_pedido);
                if (!empty($detalles)) {
                    $productoModel = new ProductoModel();
                    foreach ($detalles as $detalle) {
                        $descontado = $productoModel->ejecutar('descontarStock', [
                            'id' => $detalle['id_producto'],
                            'cantidad' => $detalle['cantidad']
                        ]);
                        if (!$descontado) {
                            error_log("⚠️ No se pudo descontar stock del Producto #{$detalle['id_producto']}");
                        }
                    }
                }
            } else {
                error_log("⚠️ No se pudo actualizar el pedido #{$id_pedido}");
            }
            
        } elseif ($accion === 'rechazar') {
            // ❌ Cambiar estado a 'rechazado'
            $pedidoModel->actualizarEstado($id_pedido, 'rechazado', $motivo);
            error_log("❌ Pedido #{$id_pedido} rechazado por cajera {$cedula}");
        }

        // 4.3.  REGISTRAR EN BITÁCORA
        try {
            $bitacora = new bitacora();
            $accion_bitacora = $accion === 'aprobar' ? 'APROBAR_PAGO' : 'RECHAZAR_PAGO';
            $bitacora->registrar(
                'pago_online',
                $accion_bitacora,
                'Ventas Online',
                $cedula,
                $id_reporte
            );
        } catch (Exception $e) {
            error_log("Error al registrar en bitácora: " . $e->getMessage());
        }

        // 4.4.  RESPONDER ÉXITO
        echo json_encode([
            'success' => true,
            'message' => $accion === 'aprobar' ? 'Pago aprobado correctamente' : 'Pago rechazado',
            'pedido_actualizado' => true
        ]);
        
    } else {
        // 4.5.  RESPONDER ERROR DEL MODELO
        echo json_encode([
            'success' => false,
            'message' => $resultado['error'] ?? 'Error al procesar la verificación'
        ]);
    }

} catch (Exception $e) {
    // 4.6.  ERROR INTERNO
    error_log("Error en procesarVerificacion: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error interno: ' . $e->getMessage()
    ]);
}

exit;
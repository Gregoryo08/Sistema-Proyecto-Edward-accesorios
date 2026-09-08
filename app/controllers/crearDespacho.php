<?php
// /src/app/controllers/crearDespacho.php
// =============================================
// CREAR DESPACHO (CON BITÁCORA Y VALIDACIONES)
// =============================================

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
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$obj_usuario = new Usuarios();
if (!$obj_usuario->tienePermiso('Administrar Ventas Online', 'crear_despacho')) {
    echo json_encode(['success' => false, 'message' => 'Sin permisos para crear despachos']);
    exit;
}

// =============================================
// 2. OBTENER DATOS
// =============================================
$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['id_pedido'])) {
    echo json_encode(['success' => false, 'message' => 'ID de pedido requerido']);
    exit;
}

$id_pedido = intval($data['id_pedido']);
$tipo = $data['tipo'] ?? 'delivery';
$motorizado_nombre = trim($data['motorizado_nombre'] ?? '');
$motorizado_telefono = trim($data['motorizado_telefono'] ?? '');
$tiempo_estimado = intval($data['tiempo_estimado'] ?? 60);

// =============================================
// 3. INSTANCIAR MODELOS
// =============================================
$envioModel = new EnvioModel();
$pedidoModel = new PedidoModel();

// =============================================
// 4. VERIFICAR QUE EL PEDIDO EXISTE
// =============================================
$pedido = $pedidoModel->obtenerPorId($id_pedido);
if (!$pedido) {
    echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
    exit;
}

// =============================================
// 5. CREAR DESPACHO (usando el modelo mejorado)
// =============================================
$fecha_estimada = date('Y-m-d', strtotime('+' . ceil($tiempo_estimado / 60) . ' days'));

$resultado = $envioModel->ejecutar('crear', [
    'id_pedido' => $id_pedido,
    'tipo_despacho' => $tipo,
    'fecha_despacho' => date('Y-m-d'),
    'despachador_nombre' => $motorizado_nombre,
    'despachador_telefono' => $motorizado_telefono,
    'instrucciones_entrega' => $pedido['direccion_entrega'] ?? '',
    'fecha_entrega_estimada' => $fecha_estimada
]);

// =============================================
// 6. ACTUALIZAR ESTADO DEL PEDIDO A 'enviado'
// =============================================
if ($resultado['success'] ?? false) {
    $pedidoModel->actualizarEstado($id_pedido, 'enviado');
}

// =============================================
// 7. REGISTRAR EN BITÁCORA (SOLO SI FUE EXITOSO)
// =============================================
if ($resultado['success'] ?? false) {
    try {
        $bitacora = new bitacora();
        $bitacora->registrar(
            'despachos',
            'ASIGNAR_DESPACHO',
            'Ventas Online',
            $cedula,
            $id_pedido
        );
    } catch (Exception $e) {
        error_log("Error al registrar en bitácora: " . $e->getMessage());
    }
}

// =============================================
// 8. RESPONDER
// =============================================
echo json_encode([
    'success' => $resultado['success'] ?? false,
    'message' => ($resultado['success'] ?? false) ? 'Despacho creado correctamente' : ($resultado['error'] ?? 'Error al crear despacho'),
    'id_despacho' => $resultado['id_despacho'] ?? null,
    'id_pedido' => $id_pedido
]);
exit;
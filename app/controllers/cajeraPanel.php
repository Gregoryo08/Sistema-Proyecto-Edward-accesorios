<?php
// /src/app/controllers/cajeraPanel.php
// =============================================
// PANEL DE CAJERA - VENTAS ONLINE
// =============================================

use App\Sistema\models\PagoOnlineModel;
use App\Sistema\models\EnvioModel;
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

if (!isset($cedula) || !isset($rol)) {
    header('Location: ?pagina=iniciarSesion');
    exit;
}

$obj_usuario = new Usuarios();
if (!$obj_usuario->tienePermiso('Administrar Ventas Online', 'listar')) {
    header('Location: ?pagina=principal');
    exit;
}

// =============================================
// 2. REGISTRAR ACCESO EN BITÁCORA
// =============================================
try {
    $bitacora = new bitacora();
    $bitacora->registrar(
        'cajera_panel',
        'ACCESO',
        'Ventas Online',
        $cedula,
        0
    );
} catch (Exception $e) {
    error_log("Error al registrar acceso en bitácora: " . $e->getMessage());
}

// =============================================
// 3. INSTANCIAR MODELOS
// =============================================
$pagoModel = new PagoOnlineModel();
$envioModel = new EnvioModel();

// =============================================
// 4. OBTENER DATOS CON LOS MODELOS CORRECTOS
// =============================================

// 4.1. Pagos pendientes (usando pago_online)
$pagos_pendientes = $pagoModel->ejecutar('obtener_pendientes');
if (isset($pagos_pendientes['error'])) {
    $pagos_pendientes = [];
}

// 4.2. Pagos aprobados sin despacho (usando pago_online)
$pagos_aprobados = $pagoModel->ejecutar('obtener_aprobados_sin_despacho');
if (isset($pagos_aprobados['error'])) {
    $pagos_aprobados = [];
}

// 4.3. Despachos activos (usando despachos)
$despachos_activos = $envioModel->ejecutar('obtenerActivos');
if (isset($despachos_activos['error'])) {
    $despachos_activos = [];
}

// =============================================
// 5. ESTADÍSTICAS (CORREGIDAS)
// =============================================
$stats = [
    'pendientes' => count($pagos_pendientes),
    'aprobados' => count($pagos_aprobados),
    'en_ruta' => count($despachos_activos)
];

// =============================================
// 6. DEBUG (para verificar que hay datos)
// =============================================
error_log("📊 CajeraPanel - Pendientes: " . $stats['pendientes']);
error_log("📊 CajeraPanel - Aprobados: " . $stats['aprobados']);
error_log("📊 CajeraPanel - En Ruta: " . $stats['en_ruta']);

// =============================================
// 7. CARGAR VISTA
// =============================================
require_once __DIR__ . '/../views/admin/panelCajera.php';
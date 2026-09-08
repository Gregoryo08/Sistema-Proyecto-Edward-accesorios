<?php
// /src/app/controllers/listarBancos.php
// =============================================
// LISTAR BANCOS ACTIVOS (AJAX)
// Por defecto SOLO lectura de BD (rápido, sin llamadas externas).
// - ?forzar=1 fuerza la sincronización desde la API (uso ocasional/admin)
// =============================================

namespace App\Sistema\Controllers;

use App\Sistema\models\BancoModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cedula = $_SESSION['cliente_cedula'] ?? $_SESSION['username'] ?? null;

if (empty($cedula)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

header('Content-Type: application/json');

try {
    $forzar = (isset($_GET['forzar']) && $_GET['forzar'] === '1');
    $bancoModel = new BancoModel();

    // Por defecto SOLO lectura de BD (sin fallback a API): evita que el AJAX de
    // las páginas de pago se congele hasta 16s si la API de GitHub no responde.
    // El sync del catálogo queda reservado a sincronizarBancos.php (admin).
    if ($forzar) {
        $bancos = $bancoModel->obtenerActivos(true);
        $fuente = 'bd_api';
    } else {
        $bancos = $bancoModel->obtenerActivosSoloBd();
        $fuente = 'bd';
    }

    echo json_encode([
        'success' => true,
        'bancos' => $bancos,
        'fuente' => $fuente
    ]);

} catch (\Exception $e) {
    error_log("Error en listarBancos: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

exit;
<?php
// /src/app/controllers/sincronizarBancos.php
// =============================================
// UTILIDAD DE SINCRONIZACIÓN DE BANCOS DESDE LA API
// -------------------------------------------------
// ¿Cómo funciona? Se consulta el listado público de bancos de Venezuela
// (JSon en GitHub, fuente SUDEBAN/BCV) y se INYECTA en la tabla `bancos`
// los que falten (INSERT ... WHERE NOT EXISTS por nombre).
//
// → Permite mantener la tabla `bancos` al día SIN crear un módulo CRUD.
//
// USO (requiere sesión de ADMINISTRADOR, rol = 1):
//   ?pagina=sincronizarBancos                        → ejecuta la sincronización (JSON)
//   ?pagina=sincronizarBancos&sql=1                  → SOLO genera el SQL (para revisarlo o ejecutarlo manual)
//   ?pagina=sincronizarBancos&sql=1&fuerza=1         → genera SQL incluso con datos existentes
// =============================================

namespace App\Sistema\Controllers;

use App\Sistema\models\BancoModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

// =============================================
// PROTECCIÓN: solo administrador
// =============================================
$rol = $_SESSION['rol'] ?? null;
if ((int)$rol !== 1) {
    echo json_encode([
        'success' => false,
        'error'   => 'No autorizado: se requiere sesión de administrador',
    ]);
    exit;
}

try {
    $bancoModel = new BancoModel();
    $forzarSql = (isset($_GET['sql']) && $_GET['sql'] === '1');
    $forzar = (isset($_GET['fuerza']) && $_GET['fuerza'] === '1');

    if ($forzarSql) {
        // ============================================
        // MODO "GENERAR SQL": imprime la sentencia para
        // revisión o ejecución manual (phpMyAdmin, CLI...)
        // ============================================
        $apiBancos = $bancoModel->obtenerDeApi();
        if (count($apiBancos) === 0) {
            echo json_encode(['success' => false, 'error' => 'No se pudo obtener el listado desde la API']);
            exit;
        }

        $totalActivos = $bancoModel->obtenerActivos(false);
        $sql = [];
        $sql[] = "-- ============================================================";
        $sql[] = "-- SINCRONIZACIÓN DE BANCOS (generado: " . date('Y-m-d H:i:s') . ")";
        $sql[] = "-- Fuente: " . BancoModel::API_BANCOS_URL;
        $sql[] = "-- Bancos que devuelve la API: " . count($apiBancos);
        $sql[] = "-- Bancos activos actuales en BD: " . count($totalActivos);
        $sql[] = "-- Uso: ejecutar en la BD 'sistema_edward'";
        $sql[] = "-- ============================================================";
        $sql[] = "";

        foreach ($apiBancos as $b) {
            $nombre = str_replace("'", "''", $b['nombre']);
            $codigo = ($b['codigo'] !== null && $b['codigo'] !== '') ? "'" . str_replace("'", "''", $b['codigo']) . "'" : "NULL";
            $sql[] = "INSERT INTO bancos (nombre_banco, codigo_banco)";
            $sql[] = "SELECT '$nombre', $codigo";
            $sql[] = "WHERE NOT EXISTS (SELECT 1 FROM bancos WHERE LOWER(nombre_banco) = LOWER('$nombre'));";
        }
        $sql[] = "";
        $sql[] = "-- Fin de la sincronización";

        header('Content-Type: text/plain; charset=utf-8');
        echo implode("\n", $sql);
        exit;
    }

    // ============================================
    // MODO "EJECUTAR SINCRONIZACIÓN"
    // ============================================
    // Fuerza siempre que se pida ?fuerza=1; si no, solo cuando la BD esté vacía
    $resultado = null;
    if ($forzar) {
        $resultado = $bancoModel->sincronizarDesdeApi();
    } else {
        $activos = $bancoModel->obtenerActivos(false);
        if (count($activos) === 0) {
            $resultado = $bancoModel->sincronizarDesdeApi();
        } else {
            $resultado = ['success' => true, 'insertados' => 0, 'error' => null, 'omitido' => true];
        }
    }

    $totalFinal = count($bancoModel->obtenerActivos(false));

    echo json_encode([
        'success'     => $resultado['success'],
        'insertados'  => $resultado['insertados'],
        'total_bancos' => $totalFinal,
        'omitido'     => $resultado['omitido'] ?? false,
        'forzar'      => $forzar,
        'error'       => $resultado['error'],
    ], JSON_UNESCAPED_UNICODE);

} catch (\Exception $e) {
    error_log("Error en sincronizarBancos: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

exit;
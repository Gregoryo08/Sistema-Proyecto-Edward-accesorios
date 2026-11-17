<?php

namespace App\Sistema\Controllers;

use App\Sistema\models\BancoModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

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

        $apiBancos = $bancoModel->obtenerDeApi();
        if (count($apiBancos) === 0) {
            echo json_encode(['success' => false, 'error' => 'No se pudo obtener el listado desde la API']);
            exit;
        }

        $script = $bancoModel->generarScriptSincronizacion($apiBancos);

        header('Content-Type: text/plain; charset=utf-8');
        echo $script;
        exit;
    }

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

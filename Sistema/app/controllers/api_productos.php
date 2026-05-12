<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// No necesitas session_start porque el FrontController ya lo hizo

use App\Sistema\models\productos;

try {
    $obj = new productos();
    $lista = $obj->listar();

    $productos_publicos = array_filter($lista, function($p) {
        return isset($p['estado']) && $p['estado'] == 1;
    });

    echo json_encode(array_values($productos_publicos));
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
exit; // Importante para que no se cargue nada más del sistema
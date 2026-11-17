<?php

header('Content-Type: application/json');

// Solo para entorno local
$host = $_SERVER['HTTP_HOST'] ?? '';
$hostLower = strtolower($host);
$esLocal = (strpos($hostLower, 'localhost') !== false || strpos($hostLower, '127.0.0.1') !== false);

if (!$esLocal) {
    echo json_encode(['ok' => false, 'msg' => 'Solo disponible en local']);
    exit;
}

// El .bat vive en la raiz del proyecto (app/controllers -> raiz).
$bat = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'iniciar_servidor_IA.bat';

if (!file_exists($bat)) {
    echo json_encode(['ok' => false, 'msg' => 'No se encuentra iniciar_servidor_IA.bat']);
    exit;
}

// Arranca el .bat en segundo plano sin mostrar ventana
pclose(popen('start /B "" "' . $bat . '" > NUL 2>&1', 'r'));

echo json_encode(['ok' => true, 'msg' => 'Intentando iniciar el microservicio de IA...']);
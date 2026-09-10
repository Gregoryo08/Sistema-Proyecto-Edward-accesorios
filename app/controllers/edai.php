<?php

use App\Sistema\models\EdAiModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

$model = new EdAiModel();
$action = $_GET['action'] ?? 'enviar';
$contexto = $_POST['contexto'] ?? $_GET['contexto'] ?? 'intranet';
$contexto = ($contexto === 'ecommerce') ? 'ecommerce' : 'intranet';

if ($contexto === 'intranet') {
    $usuarioId = $_SESSION['username'] ?? '';
    $token = 'intra_' . ($usuarioId !== '' ? $usuarioId : 'anon_' . session_id());
    if ($usuarioId === '') {
        echo json_encode(['ok' => false, 'message' => 'Sesión no iniciada']);
        exit;
    }
} else {
    $usuarioId = $_SESSION['cliente_cedula'] ?? '';
    $token = 'ecom_' . ($usuarioId !== '' ? $usuarioId : session_id());
}

switch ($action) {
    case 'registrar':
        $res = $model->registrarActivo($token, $usuarioId, $contexto);
        echo json_encode([
            'ok' => $res['ok'],
            'lleno' => $res['lleno'],
            'activos' => $res['activos'],
            'max' => EdAiModel::MAX_SIMULTANEOS,
            'message' => $res['lleno']
                ? "El asistente esta lleno ahora mismo ({$res['activos']}/" . EdAiModel::MAX_SIMULTANEOS . "). Intenta en unos minutos."
                : "Chat disponible ({$res['activos']}/" . EdAiModel::MAX_SIMULTANEOS . ").",
        ]);
        exit;

    case 'liberar':
        $model->liberarActivo($token, $contexto);
        echo json_encode(['ok' => true]);
        exit;

    case 'enviar':
    default:
        $mensaje = trim($_POST['mensaje'] ?? '');
        if ($mensaje === '') {
            echo json_encode(['ok' => true, 'respuesta' => 'Escribe tu duda para ayudarte.']);
            exit;
        }

        $res = $model->registrarActivo($token, $usuarioId, $contexto);
        if (!$res['ok']) {
            echo json_encode([
                'ok' => false,
                'message' => "El asistente esta lleno ahora mismo. Intenta en unos minutos.",
            ]);
            exit;
        }

        $respuesta = $model->responder($mensaje, $contexto);
        echo json_encode([
            'ok' => true,
            'respuesta' => $respuesta,
            'activos' => $res['activos'],
        ]);
        exit;
}
<?php

$destino = $_GET['destino'] ?? 'checkout';

$_SESSION['redirect_after_login'] = '?pagina=' . $destino;

header('Content-Type: application/json');
echo json_encode(['success' => true]);
exit;

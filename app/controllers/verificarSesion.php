<?php

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$logueado = isset($_SESSION['es_ecommerce']) && $_SESSION['es_ecommerce'] === true;

echo json_encode(['logueado' => $logueado]);
exit;

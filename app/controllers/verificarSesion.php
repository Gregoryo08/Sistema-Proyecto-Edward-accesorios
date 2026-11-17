<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');


$logueado = !empty($_SESSION['cliente_cedula']) 
         || !empty($_SESSION['cliente_nombre']) 
         || !empty($_SESSION['cliente_logueado'])
         || !empty($_SESSION['es_ecommerce'])
         || !empty($_SESSION['username']);

$nombre = $_SESSION['cliente_nombre'] ?? $_SESSION['username'] ?? $_SESSION['cliente_cedula'] ?? '';

echo json_encode([
    'logueado' => (bool)$logueado,
    'logged'   => (bool)$logueado,
    'success'  => (bool)$logueado,
    'nombre'   => $nombre,
    'user'     => $nombre,
    'usuario'  => $nombre
]);
exit();
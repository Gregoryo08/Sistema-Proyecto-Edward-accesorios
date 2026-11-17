<?php

use App\Sistema\models\ClienteEcommerce;


$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'obtener') {
    header('Content-Type: application/json');
    $model = new ClienteEcommerce();
    echo json_encode(['success' => true, 'carrito' => $_SESSION['carrito'] ?? []]);
    exit();
}

$vista = __DIR__ . '/../views/carrito.php';

if (file_exists($vista)) {
    require_once $vista;
} else {
    require_once __DIR__ . '/../views/error_404.php';
}
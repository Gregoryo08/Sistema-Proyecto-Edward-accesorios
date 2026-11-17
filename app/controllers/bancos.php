<?php

use App\Sistema\models\EcommerceModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$modelo = new EcommerceModel();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    $productos = $modelo->ejecutar('producto.obtenerActivos');
    echo json_encode(is_array($productos) ? $productos : []);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['categorias']) && $_GET['categorias'] === 'true') {
    $categorias = $modelo->ejecutar('producto.obtenerCategorias');
    echo json_encode(is_array($categorias) ? $categorias : []);
    exit();
}

$mostrarOpcionesCliente = false;
$nombreCliente = '';

if (!empty($_SESSION['cliente_cedula']) || !empty($_SESSION['cliente_nombre']) || !empty($_SESSION['cliente_logueado'])) {
    $mostrarOpcionesCliente = true;
    $nombreCliente = $_SESSION['cliente_nombre'] ?? $_SESSION['cliente_cedula'] ?? '';
}

$productos = $modelo->ejecutar('producto.obtenerActivos');
if (empty($productos)) {
    $productos = [];
}

$categorias = $modelo->ejecutar('producto.obtenerCategorias');
if (!is_array($categorias)) {
    $categorias = [];
}

$vista = 'app/views/catalogo.php';

if (file_exists($vista)) {
    require_once $vista;
} else {
    require_once 'app/views/error_404.php';
}
?>
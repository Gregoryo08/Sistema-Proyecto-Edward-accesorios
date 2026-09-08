<?php
// controllers/loginTienda.php
//gmgmgmgmgmgmgmgmgmgmg - LOGIN INDEPENDIENTE PARA CLIENTES - GUSTAVO MIGUEL GIMENEZ

require_once __DIR__ . '/../models/ClienteEcommerce.php';

$clienteEcommerce = new ClienteEcommerce();
$error = '';

// Si ya está logueado
if ($clienteEcommerce->estaLogueado()) {
    header('Location: /Sistema/index.php?pagina=checkout');
    exit;
}

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $resultado = $clienteEcommerce->login($email, $password);
    
    if ($resultado['success']) {
        // Redirigir a checkout o a la página guardada
        $destino = $_SESSION['redirect_after_login'] ?? '/Sistema/index.php?pagina=checkout';
        unset($_SESSION['redirect_after_login']);
        header('Location: ' . $destino);
        exit;
    } else {
        $error = $resultado['message'];
    }
}

require_once __DIR__ . '/../views/ecommerce/loginChequeo.php';
?>
<?php
// controllers/registroTienda.php
//gmgmgmgmgmgmgmgmgmgmg - REGISTRO DE CLIENTES - GUSTAVO MIGUEL GIMENEZ

require_once __DIR__ . '/../models/ClienteEcommerce.php';

$clienteEcommerce = new ClienteEcommerce();
$error = '';


if ($clienteEcommerce->estaLogueado()) {
    header('Location: /src/Sistema/index.php?pagina=checkout');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreCompleto = explode(' ', trim($_POST['nombre_apellido'] ?? ''), 2);
    
    $datos = [
        'cedula_cliente' => trim($_POST['cedula']),
        'nombre' => $nombreCompleto[0],
        'apellido' => $nombreCompleto[1] ?? '',
        'correo' => trim($_POST['correo']),
        'telefono' => trim($_POST['telefono']),
        'password' => trim($_POST['password'])
    ];
    
    $resultado = $clienteEcommerce->registrar($datos);
    
    if ($resultado['success']) {
        header('Location: /Sistema/index.php?pagina=loginTienda&registro=exitoso');
        exit;
    } else {
        $error = $resultado['message'];
    }
}

require_once __DIR__ . '/../views/ecommerce/registroCliente.php';
?>
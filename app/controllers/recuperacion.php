<?php

use App\Sistema\Models\recuperacion;


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'solicitarRecuperacion') {
    header('Content-Type: application/json');
    
    $email = trim($_POST['email']);
    $recuperacion = new recuperacion();
    $recuperacion->setEmail($email);
    
    $respuesta = $recuperacion->generarTokenRecuperacion();
    unset($recuperacion);
    
    echo json_encode($respuesta);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'restablecerClave') {
    header('Content-Type: application/json');
    
    $token = trim($_POST['token']);
    $nueva_clave = trim($_POST['nueva_clave']);
    $repetir_clave = trim($_POST['repetir_clave']);

    $recuperacion = new recuperacion();
    $recuperacion->setToken($token);
    $recuperacion->setClave($nueva_clave);
    $recuperacion->setClaveRepetir($repetir_clave);
    
    $respuesta = $recuperacion->validarTokenYRestablecerClave();
    unset($recuperacion);
   
    if (isset($respuesta['success'])) {
        $respuesta['redirect'] = "index.php?pagina=iniciarSesion";
    }
    
    echo json_encode($respuesta);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'validarToken') {
    header('Content-Type: application/json');
    
    $token = trim($_GET['token'] ?? '');
    $recuperacion = new recuperacion();
    $recuperacion->setToken($token);
    
    $respuesta = $recuperacion->validarTokenExistente();
    unset($recuperacion);
    
    echo json_encode($respuesta);
    exit();
}
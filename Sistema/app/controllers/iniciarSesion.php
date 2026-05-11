<?php

use App\Sistema\models\login;
use App\Sistema\models\recuperacion;

if(isset($_SESSION["username"])){
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(["data" => "?pagina=principal"]);
        exit();
    }
    header("Location: ?pagina=principal"); 
    exit();
}




//recuperacion


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



//











$_SESSION["intentos"] = $_SESSION["intentos"] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'logearse') {
    
    $usuario = $_POST['usuario'] ?? '';
    $clave = $_POST['clave'] ?? '';
    $intento_actual = $_SESSION["intentos"];
    
    $modeloLogin = new login();
    $modeloLogin->setUsuario($usuario);
    $modeloLogin->setClave($clave);
    $modeloLogin->setIntentos($intento_actual);

    $respuesta = $modeloLogin->logearse();

    header('Content-Type: application/json');

    if (isset($respuesta["success"])) {
        $_SESSION["username"] = $respuesta["success"]["cedula_usuario"];
        $_SESSION["rol"] = $respuesta["success"]["idRol"];
        unset($_SESSION["intentos"]);
        echo json_encode(["data" => "?pagina=principal"]);
    } else {
        if (isset($respuesta["password"]) || isset($respuesta["notFound"]) || isset($respuesta["incorrect"])) {
            $_SESSION["intentos"]++;
        }
        if (isset($respuesta["disabled"])) {
            unset($_SESSION["intentos"]);
        }
        $respuesta["new_intento"] = $_SESSION["intentos"] ?? 0; 
        echo json_encode($respuesta);
    }

    exit(); 
}

include 'app/views/iniciarSesion.php';

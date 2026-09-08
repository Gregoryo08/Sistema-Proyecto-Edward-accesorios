<?php

use App\Sistema\models\login;
use App\Sistema\models\recuperacion;

if(isset($_SESSION["username"])){
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(["success" => true, "redirect" => "?pagina=principal"]);
        exit();
    }
    header("Location: ?pagina=principal"); 
    exit();
}

header('Content-Type: application/json');

$accion = $_POST['accion'] ?? ($_GET['accion'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($accion)) {
    
    if ($accion === 'solicitarRecuperacion') {
        $recuperacion = new recuperacion();
        $recuperacion->setEmail(trim($_POST['email'] ?? ''));
        echo json_encode($recuperacion->generarTokenRecuperacion());
        exit();
    }

    if ($accion === 'restablecerClave') {
        $recuperacion = new recuperacion();
        $recuperacion->setToken(trim($_POST['token'] ?? ''));
        $recuperacion->setClave(trim($_POST['nueva_clave'] ?? ''));
        $recuperacion->setClaveRepetir(trim($_POST['repetir_clave'] ?? ''));
        $res = $recuperacion->validarTokenYRestablecerClave();
        if (isset($res['success'])) $res['redirect'] = "?pagina=loginEcommerce";
        echo json_encode($res);
        exit();
    }

    if ($accion === 'registrar') {
        $datos = [
            'cedula' => trim($_POST['cedula'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'fecha_nacimiento' => trim($_POST['fecha_nacimiento'] ?? ''),
            'sexo' => trim($_POST['sexo'] ?? ''),
            'residencia' => trim($_POST['residencia'] ?? ''),
            'clave' => trim($_POST['clave'] ?? '')
        ];

        if (empty($datos['cedula']) || empty($datos['nombre'])) {
            echo json_encode(["success" => false, "message" => "Complete los campos obligatorios."]);
            exit();
        }

        $modeloLogin = new login();
        echo json_encode($modeloLogin->registrarCliente($datos));
        exit();
    }

    if ($accion === 'logearse') {
        $ip = $_SERVER['REMOTE_ADDR'];
        $modeloLogin = new login();

        if ($modeloLogin->verificarBloqueoIP($ip) >= 5) {
            echo json_encode(["success" => false, "message" => "Demasiados intentos. Intente en 15 minutos."]);
            exit();
        }

        $usuario = trim($_POST['usuario'] ?? '');
        $clave = trim($_POST['clave'] ?? '');

        if (empty($usuario) || empty($clave)) {
            echo json_encode(["success" => false, "message" => "Datos incompletos"]);
            exit();
        }

        $modeloLogin->setUsuario($usuario);
        $modeloLogin->setClave($clave);
        $modeloLogin->setIntentos((int)($_POST['intento'] ?? 0));
        
        $res = $modeloLogin->logearse();

        if (isset($res["success"])) {
            $_SESSION["username"] = $res["success"]["cedula_usuario"];
            $_SESSION["rol"] = $res["success"]["idRol"];
            $modeloLogin->limpiarIntentos($ip);
            
            $destino = (!empty($_POST['destino']) && $_POST['destino'] !== 'null') ? "?pagina=" . $_POST['destino'] : "?pagina=web_Catalogo";
            echo json_encode(["success" => true, "redirect" => $destino]);
        } else {
            if (isset($res["password"]) || isset($res["notFound"]) || isset($res["incorrect"])) {
                $modeloLogin->registrarIntentoFail($ip);
            }
            echo json_encode(["success" => false, "message" => $res["error"] ?? $res["notFound"] ?? $res["password"] ?? $res["incorrect"] ?? "Error al ingresar"]);
        }
        exit();
    }
}

include 'app/views/loginEcommerce.php';
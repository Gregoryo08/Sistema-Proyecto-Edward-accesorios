<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Sistema\models\login;
use App\Sistema\models\recuperacion;

if (!defined('RECAPTCHA_SECRET_KEY')) {
    define('RECAPTCHA_SECRET_KEY', '6LfgnqstAAAAAGojQcisRuxXeftouDQ7FP_brXKn');
}

if (!defined('RECAPTCHA_VERIFY_URL')) {
    define('RECAPTCHA_VERIFY_URL', 'https://www.google.com/recaptcha/api/siteverify');
}

$ip = $_SERVER['REMOTE_ADDR'] ?? '';

if (!function_exists('validarRecaptcha')) {
    function validarRecaptcha($token, $ip) {
        if (empty($token)) {
            return false;
        }
        $url = RECAPTCHA_VERIFY_URL . "?secret=" . RECAPTCHA_SECRET_KEY . "&response=" . $token . "&remoteip=" . $ip;
        $response = @file_get_contents($url);
        $responseData = json_decode($response, true);
        return isset($responseData['success']) && $responseData['success'] === true;
    }
}

if (isset($_SESSION["username"]) && !empty($_SESSION["username"])) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(["data" => "?pagina=principal"]);
        exit();
    }
    header("Location: ?pagina=principal"); 
    exit();
}

$accion = $_POST['accion'] ?? ($_GET['accion'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($accion)) {
    header('Content-Type: application/json');

    if ($accion === 'solicitarRecuperacion') {
        $recuperacion = new recuperacion();
        $recuperacion->setEmail(trim($_POST['email'] ?? ''));
        $respuesta = $recuperacion->generarTokenRecuperacion();
        unset($recuperacion);
        echo json_encode($respuesta);
        exit();
    }

    if ($accion === 'restablecerClave') {
        $token = trim($_POST['token'] ?? '');
        $nueva_clave = trim($_POST['nueva_clave'] ?? '');
        $repetir_clave = trim($_POST['repetir_clave'] ?? '');
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

    if ($accion === 'consultarCedula') {
        $cedula = trim($_POST['cedula'] ?? '');
        $modeloLogin = new login();
        $respuesta = $modeloLogin->consultarCedula($cedula);
        echo json_encode($respuesta);
        exit();
    }

    if ($accion === 'verificarTelefono') {
        $cedula = trim($_POST['cedula'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $modeloLogin = new login();
        $respuesta = $modeloLogin->verificarTelefonoCliente($cedula, $telefono);
        echo json_encode($respuesta);
        exit();
    }

    if ($accion === 'activarClave') {
        $recaptchaToken = $_POST['g-recaptcha-response'] ?? '';
        if (!validarRecaptcha($recaptchaToken, $ip)) {
            echo json_encode(["recaptcha" => "Por favor, completa la verificación del captcha correctamente."]);
            exit();
        }
        $datos = [
            'cedula' => trim($_POST['cedula'] ?? ''),
            'clave' => trim($_POST['clave'] ?? ''),
            'confirmar_clave' => trim($_POST['confirmar_clave'] ?? '')
        ];
        if (empty($datos['cedula']) || empty($datos['clave'])) {
            echo json_encode(["error" => "Por favor, complete todos los campos obligatorios."]);
            exit();
        }
        if ($datos['clave'] !== $datos['confirmar_clave']) {
            echo json_encode(["error" => "Las contraseñas no coinciden."]);
            exit();
        }
        $modeloLogin = new login();
        $respuesta = $modeloLogin->activarClave($datos);
        echo json_encode($respuesta);
        exit();
    }

    if ($accion === 'registrar') {
        $recaptchaToken = $_POST['g-recaptcha-response'] ?? '';
        if (!validarRecaptcha($recaptchaToken, $ip)) {
            echo json_encode(["recaptcha" => "Por favor, completa la verificación del captcha correctamente."]);
            exit();
        }
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
        if (empty($datos['cedula']) || empty($datos['nombre']) || empty($datos['apellido'])) {
            echo json_encode(["error" => "Por favor, complete todos los campos obligatorios."]);
            exit();
        }
        $modeloLogin = new login();
        $respuesta = $modeloLogin->registrarCliente($datos);
        echo json_encode($respuesta);
        exit();
    }

    if ($accion === 'logearse') {
        $recaptchaToken = $_POST['g-recaptcha-response'] ?? '';
        if (!validarRecaptcha($recaptchaToken, $ip)) {
            echo json_encode(["recaptcha" => "Por favor, completa la verificación del captcha correctamente."]);
            exit();
        }

        $modeloLogin = new login();
        if ($modeloLogin->verificarBloqueoIP($ip) >= 5) {
            echo json_encode(["disabled" => "Demasiados intentos. Intente en 15 minutos."]);
            exit();
        }

        $usuario = trim($_POST['usuario'] ?? '');
        $clave = trim($_POST['clave'] ?? '');

        if (empty($usuario) || empty($clave)) {
            echo json_encode(["error" => "Datos incompletos"]);
            exit();
        }

        $modeloLogin->setUsuario($usuario);
        $modeloLogin->setClave($clave);
        $respuesta = $modeloLogin->logearse();

        if (isset($respuesta["success"])) {
            $_SESSION["username"] = $respuesta["success"]["cedula_usuario"];
            $_SESSION["rol"] = $respuesta["success"]["idRol"] ?? $respuesta["success"]["id_rol"];
            $modeloLogin->limpiarIntentos($ip);

            
            $destino = (!empty($_POST['destino']) && $_POST['destino'] !== 'null') ? "?pagina=" . $_POST['destino'] : "?pagina=principal";
            
            echo json_encode(["data" => $destino]);
        } else {
            if (isset($respuesta["password"]) || isset($respuesta["notFound"]) || isset($respuesta["incorrect"])) {
                $modeloLogin->registrarIntentoFail($ip);
            }
            echo json_encode($respuesta);
        }
        exit();
    }
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

include 'app/views/loginEcommerce.php';
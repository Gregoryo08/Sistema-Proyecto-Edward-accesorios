<?php

use App\Sistema\config\Acceso;
use App\Sistema\Helpers\LoginHelper;
use App\Sistema\models\ClienteEcommerce;
use App\Sistema\models\Vista;
use App\Sistema\Models\recuperacion;

require_once __DIR__ . '/../config/captcha.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$model = new ClienteEcommerce();
$vistas = new Vista(__DIR__ . '/../views');
$recuperacion = new recuperacion();

$action = $_GET['action'] ?? $_GET['pagina'] ?? 'index';

function pideFormularioRegistro()
{
    if (isset($_GET['register'])) {
        return true;
    }

    $ruta = strtolower((string) ($_GET['pagina'] ?? $_GET['controller'] ?? ''));

    return strpos($ruta, 'registro') !== false;
}

function responderJson(callable $accion, $conRedireccion = false)
{
    header('Content-Type: application/json');

    $resultado = $accion();

    if ($conRedireccion && !empty($resultado['success'])) {
        $resultado['redirect'] = '?pagina=' . Acceso::destinoLoginTienda($_SESSION['destino_after_login'] ?? '');
    }

    echo json_encode($resultado);
    exit;
}

function exigirCaptcha($model)
{
    if ($model->verificarCaptcha($_POST['g-recaptcha-response'] ?? '')) {
        return;
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Completa la verificación del captcha correctamente.'
    ]);
    exit;
}

function normalizarCedula($valor)
{
    return LoginHelper::normalizarCedula($valor);
}

function validarCampos($datos)
{
    $obligatorios = [
        'cedula_persona' => 'la cédula',
        'nombre'         => 'el nombre',
        'apellido'       => 'el apellido',
        'correo'         => 'el correo',
        'telefono'       => 'el teléfono',
        'password'       => 'la contraseña'
    ];

    foreach ($obligatorios as $campo => $etiqueta) {
        if (($datos[$campo] ?? null) === null || $datos[$campo] === '') {
            return 'Debe completar ' . $etiqueta . '.';
        }
    }

    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        return 'El correo no tiene un formato válido.';
    }

    if (strlen($datos['password']) < 4) {
        return 'La contraseña debe tener al menos 4 caracteres.';
    }

    return validarNacimiento($datos['fecha_nacimiento']);
}

function validarNacimiento($fecha)
{
    if (empty($fecha)) {
        return null;
    }

    $marca = strtotime($fecha);

    if ($marca === false) {
        return 'La fecha de nacimiento no es válida.';
    }

    if (date('Y-m-d', $marca) === date('Y-m-d')) {
        return 'La fecha de nacimiento no puede ser la fecha actual.';
    }

    $anio = (int) date('Y', $marca);

    if ($anio < 1955 || $anio > 2008) {
        return 'La fecha de nacimiento debe estar entre '
            . 1955 . ' y ' . 2008 . '.';
    }

    return null;
}

switch ($action) {
    case 'login_principal':
    case 'index':
        if (isset($_GET['destino'])) {
            $_SESSION['destino_after_login'] = $_GET['destino'];
        }

        if ($model->ejecutar('estaLogueado')) {
            header('Location: ?pagina=' . Acceso::destinoLoginTienda($_SESSION['destino_after_login'] ?? ''));
            exit;
        }

        $mostrarRegistro = pideFormularioRegistro();

        $vistas->mostrar('login_principal.php');
        break;

    case 'procesar':
        $accion = strtolower((string) ($_POST['accion'] ?? $_GET['accion'] ?? ''));

        if ($accion === 'login' || $accion === 'logearse') {
            // redirige a login
            $_GET['action'] = 'login';
            // re-evaluar no es posible directo, caemos a login abajo
        }

        if ($accion === 'registrar' || $accion === 'registro') {
            $_GET['action'] = 'registro';
        }

        if (pideFormularioRegistro()) {
            $_GET['action'] = 'registro';
        }

        $_GET['action'] = 'login';
        // no break, se deja caer? mejor usar include? no, usamos goto o reasignar y seguir
        // Para simplicidad, asignamos y dejamos que el switch continúe? No, switch ya entró.
        // Volvemos a evaluar manualmente:
        $accion = strtolower((string) ($_POST['accion'] ?? $_GET['accion'] ?? ''));
        if ($accion === 'login' || $accion === 'logearse') {
            $_GET['action'] = 'login';
        } elseif ($accion === 'registrar' || $accion === 'registro' || pideFormularioRegistro()) {
            $_GET['action'] = 'registro';
        } else {
            $_GET['action'] = 'login';
        }
        // re-dispatch manual
        $action = $_GET['action'];
        // Usamos include propio para no duplicar todo el switch
        break;

    case 'login':
        responderJson(function () use ($model) {
            exigirCaptcha($model);

            $cedula   = trim((string) ($_POST['cedula'] ?? ''));
            $password = trim((string) ($_POST['password'] ?? ''));

            if ($cedula === '' || $password === '') {
                return ['success' => false, 'message' => 'Datos incompletos'];
            }

            return $model->ejecutar('login', [
                'cedula'   => $cedula,
                'password' => $password
            ]);
        }, true);
        break;

    case 'registro':
    case 'registrar':
        responderJson(function () use ($model) {
            exigirCaptcha($model);

            $cedula = normalizarCedula($_POST['cedula_persona'] ?? '');

            if ($cedula === null) {
                return ['success' => false, 'message' => 'La cédula no tiene un formato válido.'];
            }

            $datos = [
                'cedula_persona'     => $cedula,
                'nombre'             => trim((string) ($_POST['nombre'] ?? '')),
                'apellido'           => trim((string) ($_POST['apellido'] ?? '')),
                'correo'             => trim((string) ($_POST['correo'] ?? '')),
                'telefono'           => trim((string) ($_POST['telefono'] ?? '')),
                'direccion'          => trim((string) ($_POST['direccion'] ?? '')),
                'password'           => (string) ($_POST['clave'] ?? ''),
                'fecha_nacimiento'   => $_POST['fecha_nacimiento'] ?? null,
                'sexo'               => $_POST['sexo'] ?? null,
                'token_confirmacion' => trim((string) ($_POST['token_confirmacion'] ?? ''))
            ];

            $error = validarCampos($datos);

            if ($error !== null) {
                return ['success' => false, 'message' => $error];
            }

            return $model->ejecutar('registrar', $datos);
        }, true);
        break;

    case 'verificar':
        responderJson(function () use ($model) {
            exigirCaptcha($model);

            $cedula = normalizarCedula($_POST['cedula_persona'] ?? '');

            if ($cedula === null) {
                return ['success' => false, 'message' => 'La cédula no tiene un formato válido.'];
            }

            return $model->ejecutar('verificarCedula', ['cedula' => $cedula]);
        });
        break;

    case 'logout':
    case 'cerrarsesioncliente':
        Acceso::salir();
        break;

    case 'recuperacion':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'solicitarRecuperacion') {
            header('Content-Type: application/json');
            $email = trim($_POST['email']);
            $recuperacion->setEmail($email);
            $respuesta = $recuperacion->generarTokenRecuperacion();
            echo json_encode($respuesta);
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'restablecerClave') {
            header('Content-Type: application/json');
            $token = trim($_POST['token']);
            $nueva_clave = trim($_POST['nueva_clave']);
            $repetir_clave = trim($_POST['repetir_clave']);
            $recuperacion->setToken($token);
            $recuperacion->setClave($nueva_clave);
            $recuperacion->setClaveRepetir($repetir_clave);
            $respuesta = $recuperacion->validarTokenYRestablecerClave();
            if (isset($respuesta['success'])) {
                $respuesta['redirect'] = "index.php?pagina=iniciarSesion";
            }
            echo json_encode($respuesta);
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'validarToken') {
            header('Content-Type: application/json');
            $token = trim($_GET['token'] ?? '');
            $recuperacion->setToken($token);
            $respuesta = $recuperacion->validarTokenExistente();
            echo json_encode($respuesta);
            exit();
        }
        break;

    default:
        header('Location: ?pagina=login_principal');
        exit;
}

exit;

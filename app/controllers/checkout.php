<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Sistema\models\EcommerceModel;

$modelo = new EcommerceModel();

$cedulaCliente = $_SESSION['cedula'] ?? $_SESSION['cliente_cedula'] ?? $_SESSION['cliente_logueado'] ?? null;

if (empty($cedulaCliente)) {
    $_SESSION['destino_after_login'] = 'checkout';
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$cliente_nombre   = $_SESSION['cliente_nombre']   ?? '';
$cliente_correo   = $_SESSION['cliente_correo']   ?? $_SESSION['email'] ?? '';
$cliente_telefono = $_SESSION['cliente_telefono'] ?? $_SESSION['telefono'] ?? '';

try {
    
    $datosPersona = $modelo->obtenerDatosPersona($cedulaCliente);
    
    if (is_array($datosPersona) && !empty($datosPersona)) {
        $nombreCompleto   = trim(($datosPersona['nombres'] ?? '') . ' ' . ($datosPersona['apellidos'] ?? ''));
        $cliente_nombre   = !empty($nombreCompleto) ? $nombreCompleto : ($datosPersona['nombres'] ?? $cliente_nombre);
        $cliente_correo   = $datosPersona['correo'] ?? $cliente_correo;
        $cliente_telefono = $datosPersona['telefono'] ?? $cliente_telefono;
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

$vista = 'app/views/checkout.php';

if (file_exists($vista)) {
    require_once $vista;
} else {
    require_once 'app/views/error_404.php';
}
exit();
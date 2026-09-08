<?php
// /src/app/controllers/checkout.php
// Controlador de Checkout

use App\Sistema\models\ClienteEcommerce;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $clienteEcommerce = new ClienteEcommerce();

    // ✅ VERIFICAR SESIÓN
    if (!$clienteEcommerce->ejecutar('estaLogueado')) {
        $_SESSION['redirect_after_login'] = '?pagina=checkout';
        header('Location: ?pagina=loginTienda');
        exit;
    }

    // ✅ OBTENER DATOS DEL CLIENTE
    $cliente_cedula = $_SESSION['cliente_cedula'] ?? '';
    $cliente_nombre = $_SESSION['cliente_nombre'] ?? '';
    $cliente_correo = $_SESSION['cliente_correo'] ?? '';
    $cliente_telefono = $_SESSION['cliente_telefono'] ?? '';

    // ✅ COMPLETAR DATOS FALTANTES
    if (empty($cliente_nombre)) {
        $datos = $clienteEcommerce->ejecutar('obtenerPorCedula', ['cedula' => $cliente_cedula]);
        if ($datos) {
            $cliente_nombre = trim(($datos['nombre'] ?? '') . ' ' . ($datos['apellido'] ?? ''));
            $cliente_correo = $datos['correo'] ?? '';
            $cliente_telefono = $datos['telefono'] ?? '';

            $_SESSION['cliente_nombre'] = $cliente_nombre;
            $_SESSION['cliente_correo'] = $cliente_correo;
            $_SESSION['cliente_telefono'] = $cliente_telefono;
        }
    }

    $cedula_persona_checkout = $cliente_cedula;
    
    // ✅ CARGAR VISTA
    require_once __DIR__ . '/../views/checkout.php';

} catch (Exception $e) {
    error_log("Error en checkout: " . $e->getMessage());
    header('Location: ?pagina=web_Catalogo');
    exit;
}
<?php
// /src/app/controllers/cerrarSesionCliente.php
// Controlador para cerrar sesión del cliente

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destruir todas las variables de sesión del e-commerce
unset($_SESSION['es_ecommerce']);
unset($_SESSION['cliente_cedula']);
unset($_SESSION['cliente_nombre']);
unset($_SESSION['cliente_correo']);
unset($_SESSION['cliente_telefono']);
unset($_SESSION['cliente_direccion']);
unset($_SESSION['destino_after_login']);

// Si quieres destruir completamente la sesión (opcional, pero recomendado)
// session_destroy();

// Redirigir al catálogo (página pública)
header('Location: ?pagina=web_Catalogo');
exit;
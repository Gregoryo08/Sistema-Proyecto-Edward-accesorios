<?php

/**
 * Configuracion de reCAPTCHA.
 *
 * Este archivo NO contiene claves y se versiona en git.
 * Las claves se resuelven en este orden:
 *
 *   1. Variables de entorno (Alwaysdata -> "Variables ambientales"):
 *      RECAPTCHA_SITE_KEY, RECAPTCHA_SECRET_KEY.
 *   2. Archivo local app/config/captcha-local.php (esta en .gitignore).
 *   3. Sin claves: el captcha queda deshabilitado y se avisa en el log.
 *
 * Plantilla del archivo local: app/config/captcha-local.php.ejemplo
 */

$captchaLocal = __DIR__ . '/captcha-local.php';
$captchaLocalDatos = array();
if (is_file($captchaLocal)) {
    $cargado = require $captchaLocal;
    if (is_array($cargado)) {
        $captchaLocalDatos = $cargado;
    }
}

$buscar = function ($clave, $alternativa) use ($captchaLocalDatos) {
    $entorno = getenv($clave);
    if ($entorno !== false && $entorno !== '') {
        return $entorno;
    }
    if (isset($captchaLocalDatos[$clave]) && $captchaLocalDatos[$clave] !== '') {
        return $captchaLocalDatos[$clave];
    }
    return $alternativa;
};

if (!defined('RECAPTCHA_SITE_KEY')) {
    define('RECAPTCHA_SITE_KEY', $buscar('RECAPTCHA_SITE_KEY', ''));
}

if (!defined('RECAPTCHA_SECRET_KEY')) {
    define('RECAPTCHA_SECRET_KEY', $buscar('RECAPTCHA_SECRET_KEY', ''));
}

if (!defined('RECAPTCHA_VERIFY_URL')) {
    define('RECAPTCHA_VERIFY_URL', $buscar('RECAPTCHA_VERIFY_URL', 'https://www.google.com/recaptcha/api/siteverify'));
}

if (!defined('RECAPTCHA_HABILITADO')) {
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
    $hostLower = strtolower($host);
    $esLocal = (
        strpos($hostLower, 'localhost') !== false ||
        strpos($hostLower, '127.0.0.1') !== false ||
        strpos($hostLower, '::1') !== false ||
        strpos($hostLower, '[::1]') !== false
    );

    if ($esLocal) {
        // Si tenemos claves en captcha-local.php (claves de prueba), habilitamos el captcha
        if (RECAPTCHA_SITE_KEY !== '' && RECAPTCHA_SECRET_KEY !== '') {
            define('RECAPTCHA_HABILITADO', true);
            return;
        }
        // Si no hay claves locales, lo deshabilitamos para evitar el error de dominios
        define('RECAPTCHA_HABILITADO', false);
        return;
    }

    $interruptor = strtolower(trim((string) $buscar('RECAPTCHA_HABILITADO', '')));
    $hayClaves = RECAPTCHA_SITE_KEY !== '' && RECAPTCHA_SECRET_KEY !== '';

    if (!$hayClaves) {
        error_log('reCAPTCHA: sin claves configuradas (RECAPTCHA_SITE_KEY / RECAPTCHA_SECRET_KEY). El captcha queda deshabilitado.');
    }

    define('RECAPTCHA_HABILITADO', $hayClaves && $interruptor !== 'false' && $interruptor !== '0');
}
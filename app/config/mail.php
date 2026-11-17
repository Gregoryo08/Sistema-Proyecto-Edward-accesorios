<?php

$mailLocal = __DIR__ . '/mail-local.php';
$mailLocalDatos = array();
if (is_file($mailLocal)) {
    $cargado = require $mailLocal;
    if (is_array($cargado)) {
        $mailLocalDatos = $cargado;
    }
}

$buscar = function ($clave, $alternativa) use ($mailLocalDatos) {
    $entorno = getenv($clave);
    if ($entorno !== false && $entorno !== '') {
        return $entorno;
    }
    if (isset($mailLocalDatos[$clave]) && $mailLocalDatos[$clave] !== '') {
        return $mailLocalDatos[$clave];
    }
    return $alternativa;
};

if (!defined('MAIL_HOST')) {
    define('MAIL_HOST', $buscar('MAIL_HOST', 'smtp.gmail.com'));
}

if (!defined('MAIL_PORT')) {
    define('MAIL_PORT', (int) $buscar('MAIL_PORT', '465'));
}

if (!defined('MAIL_SECURIDAD')) {
    define('MAIL_SECURIDAD', $buscar('MAIL_SECURIDAD', 'ssl'));
}

if (!defined('MAIL_USERNAME')) {
    define('MAIL_USERNAME', $buscar('MAIL_USERNAME', 'gustmenrepositorio@gmail.com'));
}

if (!defined('MAIL_PASSWORD')) {
    define('MAIL_PASSWORD', $buscar('MAIL_PASSWORD', ''));
}

if (!defined('MAIL_FROM_NAME')) {
    define('MAIL_FROM_NAME', $buscar('MAIL_FROM_NAME', 'Sistema Edward Accesorios'));
}

if (!defined('MAIL_CONFIGURADO')) {
    define('MAIL_CONFIGURADO', MAIL_USERNAME !== '' && MAIL_PASSWORD !== '');
}

<?php

/**
 * Configuracion del microservicio de logica difusa (RCI-01).
 *
 * Este archivo NO contiene secretos y se versiona en git.
 * La direccion del microservicio se resuelve en este orden:
 *
 *   1. Variables de entorno (Alwaysdata -> "Variables ambientales"):
 *      IA_SERVICIO_URL.
 *   2. Archivo local app/config/ia-local.php (esta en .gitignore).
 *   3. Por defecto: http://127.0.0.1:5000 (el servicio corre en la maquina
 *      del evaluador; asi ya funciona el modulo de financiamiento).
 *
 * Plantilla del archivo local: app/config/ia-local.php.ejemplo
 */

$iaLocal = __DIR__ . '/ia-local.php';
$iaLocalDatos = array();
if (is_file($iaLocal)) {
    $cargado = require $iaLocal;
    if (is_array($cargado)) {
        $iaLocalDatos = $cargado;
    }
}

$buscarIa = function ($clave, $alternativa) use ($iaLocalDatos) {
    $entorno = getenv($clave);
    if ($entorno !== false && $entorno !== '') {
        return $entorno;
    }
    if (isset($iaLocalDatos[$clave]) && $iaLocalDatos[$clave] !== '') {
        return $iaLocalDatos[$clave];
    }
    return $alternativa;
};

if (!defined('IA_SERVICIO_URL')) {
    $url = rtrim(trim((string) $buscarIa('IA_SERVICIO_URL', 'http://127.0.0.1:5000')), '/');
    define('IA_SERVICIO_URL', $url);
}
// PRUEBA_WIN_SCP_22_10_2026
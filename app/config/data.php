<?php

$host = 'localhost';
$username = 'root';
$password = '';

// Detección de Docker con caché en disco: evita ejecutar gethostbyname
// (resolución DNS lenta ~2s en XAMPP) en cada petición/conexión.
// Se guarda el resultado en un archivo cache y solo se vuelve a resolver si el
// entorno cambia (fallo de conexión). Así la página de ventas deja de pagar 2s
// por cada request AJAX.
$cacheFile = __DIR__ . '/db_host.cache';
$isDocker = false;

// Mantener coherencia dentro del mismo request/lifecycle PHP
$globalKey = '__DB_IS_DOCKER__';
if (isset($GLOBALS[$globalKey])) {
    $isDocker = $GLOBALS[$globalKey];
} else {
    $cacheValido = false;
    if (is_file($cacheFile)) {
        $contenido = @file_get_contents($cacheFile);
        if ($contenido === 'docker') {
            $isDocker = true;
            $cacheValido = true;
        } elseif ($contenido === 'local') {
            $isDocker = false;
            $cacheValido = true;
        }
    }

    // Si no hay caché (primera ejecución), resolver con DNS (una sola vez)
    if (!$cacheValido) {
        $isDocker = false;
        if (function_exists('gethostbyname')) {
            $resolved = @gethostbyname('db_edward');
            if ($resolved && $resolved !== 'db_edward') {
                $isDocker = true;
            }
        }
        @file_put_contents($cacheFile, $isDocker ? 'docker' : 'local');
    }

    $GLOBALS[$globalKey] = $isDocker;
}

if ($isDocker) {
    $host = 'db_edward';
    $password = '12345';
}

return [
    'host'     => $host,
    'dbname1'  => 'sistema_edward',
    'dbname2'  => 'sistema_edward_usuario',
    'username' => $username,
    'password' => $password,
];

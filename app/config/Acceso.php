<?php

namespace App\Sistema\config;

use App\Sistema\Models\Usuarios;
use App\Sistema\models\ClienteTienda;

class Acceso
{
    const ROL_CLIENTE = 6;

    private static $clienteTiendaCache = [];

    const DOMINIO_TIENDA = 'tienda';

    const DOMINIO_INTRANET = 'intranet';

    const DOMINIO_LOCAL = 'local';

    const HOST_TIENDA = 'edwardaccesorios.alwaysdata.net';

    const HOST_INTRANET = 'edwardaccesorios-intranet.alwaysdata.net';

    const URL_TIENDA = 'https://edwardaccesorios.alwaysdata.net/';

    const URL_INTRANET = 'https://edwardaccesorios-intranet.alwaysdata.net/';

    public static function iniciarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function cedula()
    {
        $cedula = $_SESSION['username'] ?? ($_SESSION['cliente_cedula'] ?? '');

        return is_string($cedula) ? trim($cedula) : '';
    }

    public static function rol()
    {
        $rol = $_SESSION['rol'] ?? 0;

        return is_numeric($rol) ? (int) $rol : 0;
    }

    public static function esCliente()
    {
        return self::rol() === self::ROL_CLIENTE;
    }

    public static function esSuperusuario()
    {
        return self::rol() === 1;
    }

    public static function haySesionTienda()
    {
        return isset($_SESSION['es_ecommerce'])
            && $_SESSION['es_ecommerce'] === true
            && self::cedulaTienda() !== '';
    }

    public static function haySesionIntranet()
    {
        return !empty($_SESSION['username']) && self::rol() > 0;
    }

    public static function cedulaTienda()
    {
        $cedula = $_SESSION['cliente_cedula'] ?? '';

        return is_string($cedula) ? trim($cedula) : '';
    }

    public static function nombreTienda()
    {
        $nombre = $_SESSION['cliente_nombre'] ?? '';

        return is_string($nombre) ? trim($nombre) : '';
    }

    public static function esClienteEcommerce()
    {
        return self::clasificarClienteTienda('ecommerce');
    }

    public static function esClienteFinanciamiento()
    {
        return self::clasificarClienteTienda('financiamiento');
    }

    private static function clasificarClienteTienda($tipo)
    {
        if (!self::haySesionTienda()) {
            return false;
        }

        $cedula = self::cedulaTienda();

        if ($cedula === '') {
            return false;
        }

        $clave = $tipo . '|' . $cedula;

        if (array_key_exists($clave, self::$clienteTiendaCache)) {
            return self::$clienteTiendaCache[$clave];
        }

        $cliente = new ClienteTienda($cedula);
        self::$clienteTiendaCache[$clave] = ($tipo === 'ecommerce')
            ? $cliente->tieneRegistroEcommerce()
            : $cliente->tieneFinanciamientos();

        return self::$clienteTiendaCache[$clave];
    }

    public static function dominio()
    {
        if (self::esCliente()) {
            return self::DOMINIO_TIENDA;
        }

        return self::DOMINIO_INTRANET;
    }

    public static function hostActual()
    {
        $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '');
        $host = strtolower(trim((string) $host));

        // IPv6 literal entre corchetes, por ejemplo "[::1]:8080".
        if (strpos($host, '[') === 0) {
            $fin = strpos($host, ']');

            return $fin !== false ? substr($host, 1, $fin - 1) : $host;
        }

        $host = explode(':', $host);

        return $host[0];
    }

    /**
     * Indica si se esta accediendo por una direccion IP (127.0.0.1, ::1...).
     * Google reCAPTCHA no admite IPs: en ese caso el login debe pasar por
     * "localhost".
     */
    public static function hostEsIp()
    {
        $host = trim(self::hostActual(), '[]');

        if ($host === '') {
            return false;
        }

        return filter_var($host, FILTER_VALIDATE_IP) !== false;
    }

    public static function puertoActual()
    {
        $host = (string) ($_SERVER['HTTP_HOST'] ?? '');

        if (preg_match('/:(\d+)$/', $host, $coincidencia)) {
            return $coincidencia[1];
        }

        return '';
    }

    /**
     * Construye la URL equivalente usando "localhost" en lugar de la IP,
     * preservando el puerto y la ruta actual. Sirve para que el reCAPTCHA
     * funcione en local (Google rechaza 127.0.0.1/::1).
     */
    public static function urlCanonicaLocal($pagina = null)
    {
        $esHttps = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
            || ((string) ($_SERVER['SERVER_PORT'] ?? '') === '443');

        $esquema = $esHttps ? 'https' : 'http';
        $puerto  = self::puertoActual();

        $ruta = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $ruta = strtok($ruta, '?');

        if ($ruta === false || $ruta === '') {
            $ruta = '/';
        }

        $url = $esquema . '://localhost' . ($puerto !== '' ? ':' . $puerto : '') . $ruta;

        if ($pagina !== null) {
            $url .= '?pagina=' . preg_replace('/[^A-Za-z0-9_]/', '', (string) $pagina);
        }

        return $url;
    }

    public static function dominioActual()
    {
        $host = self::hostActual();

        if ($host === self::HOST_TIENDA || $host === 'www.' . self::HOST_TIENDA) {
            return self::DOMINIO_TIENDA;
        }

        if ($host === self::HOST_INTRANET) {
            return self::DOMINIO_INTRANET;
        }

        return self::DOMINIO_LOCAL;
    }

    public static function etiquetaLoginTienda()
    {
        return self::dominioActual() === self::DOMINIO_TIENDA ? 'Acceso Clientes' : 'Acceso Usuarios';
    }

    public static function urlDominio($dominio, $pagina = '')
    {
        $base = ($dominio === self::DOMINIO_INTRANET) ? self::URL_INTRANET : self::URL_TIENDA;
        $pagina = preg_replace('/[^A-Za-z0-9_]/', '', (string) $pagina);

        if ($pagina === '') {
            return $base;
        }

        return $base . '?pagina=' . $pagina;
    }

    public static function gatingDominio($ruta)
    {
        $dominio = self::dominioActual();

        if ($dominio === self::DOMINIO_LOCAL) {
            return null;
        }

        $original = preg_replace('/[^A-Za-z0-9_]/', '', (string) $ruta);
        $ruta = strtolower(trim((string) $ruta));

        if ($dominio === self::DOMINIO_TIENDA) {
            if (self::esRutaSistema($ruta)) {
                return self::urlDominio(self::DOMINIO_INTRANET, $original);
            }

            return null;
        }

        if ($ruta === '' || $ruta === 'pagina_principal' || $ruta === 'home') {
            return '?pagina=principal';
        }

        if (self::esRutaEscaparate($ruta)) {
            return self::urlDominio(self::DOMINIO_TIENDA, $original);
        }

        return null;
    }

    public static function esRutaEscaparate($ruta)
    {
        return in_array(strtolower(trim((string) $ruta)), self::rutasEscaparate(), true);
    }

    public static function esRutaSistema($ruta)
    {
        return in_array(strtolower(trim((string) $ruta)), self::rutasSistema(), true);
    }

    private static function rutasEscaparate()
    {
        return array(
            'pagina_principal',
            'home',
            'menu',
            'catalogo',
            'web_catalogo',
            'webcatalogo',
            'carrito',
            'checkout',
            'guardarpedido',
            'guardardestino',
            'verificarsesion',
            'listarbancos',
            'reportarpago',
            'procesarreporte',
            'datospago',
            'mispedidos',
            'verpedido',
            'tasaecommerce',
            'principal_cliente',
            'cliente_financiamiento',
            'login_principal',
            'registroecommerce',
            'registro_tienda',
            'registrotienda',
            'cerrarsesioncliente',
        );
    }

    private static function rutasSistema()
    {
        return array(
            'iniciarsesion',
            'principal',
            'bancos',
            'basedatos_1',
            'basedatos_2',
            'bitacora',
            'cargos',
            'categoria',
            'chequeo',
            'chequeo_orden',
            'clientes',
            'empleado',
            'entradas_productos',
            'especialidad',
            'financiamiento',
            'ia_proceso',
            'marcas',
            'metodo',
            'modulo',
            'productos',
            'proveedores',
            'reporteentradas',
            'reporteventas',
            'reportefinanciamiento',
            'reporteservicio_tecnico',
            'roles',
            'servicio_tecnico',
            'servicio_venta',
            'telefono',
            'turno',
            'usuarios',
            'ventas',
            'sincronizarbancos',
            'tasacambio',
            'cajerapanel',
            'getdashboarddata',
            'procesarverificacion',
            'creardespacho',
            'gestionardespacho',
        );
    }

    public static function esDestinoDeTienda($destino)
    {
        return in_array(strtolower(trim((string) $destino)), self::destinosTienda(), true);
    }

    public static function destinoTienda()
    {
        return self::destinosTienda();
    }

    public static function destinoSegunDominio($destino = '')
    {
        if (!self::esCliente() && self::haySesionIntranet()) {
            return 'principal';
        }

        $destino = trim((string) $destino);

        if ($destino !== '' && self::esDestinoDeTienda($destino)) {
            return $destino;
        }

        if (self::esCliente()) {
            return 'pagina_principal';
        }

        return 'web_Catalogo';
    }

    /**
     * Destino posterior al login unificado.
     *
     * - Cliente (rol 6): portal externo / ecommerce.
     * - Personal (rol distinto de 6): frontal de la INTRANET ("principal").
     *
     * Un destino forzado de la tienda (carrito, checkout...) se respeta para
     * no romper el flujo de compra.
     */
    public static function destinoLoginTienda($destino = '')
    {
        $destino = trim((string) $destino);

        if ($destino !== '' && self::esDestinoDeTienda($destino)) {
            return $destino;
        }

        return self::esCliente() ? 'pagina_principal' : 'principal';
    }

    public static function registrarIngresoTienda($cedula, $nombre, $correo, $telefono, $rol)
    {
        $_SESSION['es_ecommerce']   = true;
        $_SESSION['cliente_cedula'] = $cedula;
        $_SESSION['cliente_nombre'] = $nombre;
        $_SESSION['cliente_correo'] = $correo;
        $_SESSION['cliente_telefono'] = $telefono;
        $_SESSION['rol'] = (int) $rol;
    }

    public static function registrarIngresoIntranet($cedula, $rol, $nombreCompleto = '')
    {
        if ((int) $rol === self::ROL_CLIENTE) {
            return false;
        }

        $_SESSION['username'] = $cedula;
        $_SESSION['rol'] = (int) $rol;

        if ($nombreCompleto !== '') {
            $_SESSION['nombre_completo'] = $nombreCompleto;
        }

        return true;
    }

    public static function destruir()
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public static function salir()
    {
        $dominio = self::dominioActual();
        $destino = ($dominio === self::DOMINIO_TIENDA) ? '?pagina=web_Catalogo' : '?pagina=iniciarSesion';

        if (self::haySesionIntranet()) {
            self::destruir();
            header('Location: ' . $destino);
            exit;
        }

        unset(
            $_SESSION['es_ecommerce'],
            $_SESSION['cliente_cedula'],
            $_SESSION['cliente_nombre'],
            $_SESSION['cliente_correo'],
            $_SESSION['cliente_telefono'],
            $_SESSION['destino_after_login'],
            $_SESSION['carrito'],
            $_SESSION['rol']
        );

        header('Location: ?pagina=web_Catalogo');
        exit;
    }

    private static function destinosTienda()
    {
        return array(
            'carrito',
            'catalogo',
            'checkout',
            'cerrarsesioncliente',
            'datospago',
            'guardardestino',
            'listarbancos',
            'mispedidos',
            'principal_cliente',
            'procesarreporte',
            'registroecommerce',
            'registrotienda',
            'reportarpago',
            'tasaecommerce',
            'verificarsesion',
            'verpedido',
            'web_catalogo',
        );
    }

    public static function exigirSesionTienda()
    {
        self::iniciarSesion();

        if (self::haySesionTienda()) {
            return true;
        }

        $_SESSION['destino_after_login'] = self::rutaActual();

        header('Location: ?pagina=login_principal&destino=' . urlencode($_SESSION['destino_after_login']));
        exit;
    }

    public static function exigirSesionIntranet()
    {
        self::iniciarSesion();

        if (self::haySesionIntranet() && !self::esCliente()) {
            return true;
        }

        if (self::esCliente()) {
            header('Location: ?pagina=web_Catalogo');
            exit;
        }

        header('Location: ?pagina=iniciarSesion');
        exit;
    }

    public static function tienePermiso($modulo, $accion = 'listar')
    {
        if (self::esCliente()) {
            return false;
        }

        $usuario = new Usuarios();

        return $usuario->tienePermiso($modulo, $accion);
    }

    public static function rutaActual()
    {
        $ruta = $_GET['pagina'] ?? $_GET['controller'] ?? 'pagina_principal';

        return preg_replace('/[^A-Za-z0-9_]/', '', (string) $ruta);
    }
}
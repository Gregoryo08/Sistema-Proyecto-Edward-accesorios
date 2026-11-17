<?php

namespace App\Sistema\Controllers;

use App\Sistema\config\Acceso;

class FrontController {

    private const CONTROLADORES = [
       
    ];

    private const RUTAS_TIENDA = [
        'carrito', 'catalogo', 'web_catalogo', 'webcatalogo', 'web_catalago', 'web_Catalago', 'catalago',
        'checkout', 'guardarpedido', 'guardardestino', 'verificarsesion',
        'listarbancos', 'reportarpago', 'procesarreporte', 'datospago',
        'mispedidos', 'verpedido', 'tasaecommerce',
        'cajerapanel', 'getdashboarddata',
        'procesarverificacion', 'creardespacho', 'gestionardespacho',
        'principal_cliente', 'miperfil', 'notificacion',
        'cliente_financiamiento',
    ];

    private const RUTAS_PUBLICAS = [
        'pagina_principal', 'home', 'menu', 'soporte', 'documentacion',
        'iniciarsesion', 'tasaCambio',
        'api_productos', 'edai',
        'registroecommerce', 'registro_tienda', 'registrotienda',
        'recuperacion', 'carrito', 'catalogo', 'web_catalogo', 'web_catalago', 'web_Catalago', 'catalago',
        'checkout', 'guardarpedido', 'guardardestino', 'verificarsesion',
        'listarbancos', 'reportarpago', 'procesarreporte', 'datospago',
        'mispedidos', 'verpedido', 'tasaecommerce',
    ];

    private const RUTAS_CUOTAS_CLIENTE = [
        'principal_cliente', 'cliente_financiamiento',
    ];

    private const RUTAS_PEDIDOS_CLIENTE = [
        'mispedidos', 'verpedido',
    ];

    public function __construct() {
        $this->dispatch();
    }

    protected function dispatch() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Acceso::iniciarSesion();

        $route = $_GET['pagina'] ?? $_GET['controller'] ?? 'pagina_principal';
        $routeLower = strtolower(trim((string) $route));

        $this->unificarAccesoLocal($routeLower);

        if ($routeLower === 'salida') {
            Acceso::destruir();
            $destinoSalida = (Acceso::dominioActual() === Acceso::DOMINIO_TIENDA)
                ? '?pagina=web_Catalogo'
                : '?pagina=iniciarSesion';
            header('Location: ' . $destinoSalida);
            exit;
        }

        if ($routeLower === 'cerrarsesioncliente') {
            Acceso::salir();
        }

        $this->aplicarGatingDominio($route);

        if (!in_array($routeLower, self::RUTAS_TIENDA, true)
            && !in_array($routeLower, self::RUTAS_PUBLICAS, true)) {
            Acceso::exigirSesionIntranet();
        }

        $this->aplicarInhibicionCliente($routeLower);

        $clase = self::CONTROLADORES[$routeLower] ?? null;

        if ($clase !== null) {
            $this->dispatchControlador($clase, $route);
        }

        $this->dispatchLegado($route);
    }

    protected function unificarAccesoLocal($routeLower)
    {
        if (Acceso::dominioActual() !== Acceso::DOMINIO_LOCAL) {
            return;
        }

        $esLogin = in_array($routeLower, ['iniciarsesion'], true);

        if ($esLogin && Acceso::hostEsIp() && $_SERVER['REQUEST_METHOD'] === 'GET') {
            header('Location: ' . Acceso::urlCanonicaLocal('iniciarsesion'));
            exit;
        }
    }

    protected function aplicarGatingDominio($route)
    {
        $destino = Acceso::gatingDominio($route);

        if ($destino !== null) {
            header('Location: ' . $destino);
            exit;
        }
    }

    protected function aplicarInhibicionCliente($routeLower)
    {
        if (!Acceso::haySesionTienda()) {
            return;
        }

        $destino = self::destinoInhibidoCliente(
            $routeLower,
            Acceso::esClienteEcommerce(),
            Acceso::esClienteFinanciamiento()
        );

        if ($destino !== null) {
            header('Location: ' . $destino);
            exit;
        }
    }

    public static function destinoInhibidoCliente($routeLower, $esEcommerce, $esFinanciamiento)
    {
        if (in_array($routeLower, self::RUTAS_CUOTAS_CLIENTE, true) && !$esFinanciamiento) {
            return '?pagina=miperfil';
        }

        if (in_array($routeLower, self::RUTAS_PEDIDOS_CLIENTE, true) && !$esEcommerce) {
            return '?pagina=web_Catalogo';
        }

        return null;
    }

    protected function dispatchControlador($clase, $route)
    {
        $fullClass = "App\\Sistema\\Controllers\\$clase";

        if (!class_exists($fullClass)) {
            return;
        }

        $controlador = new $fullClass();
        $action = $_GET['action'] ?? '';
        $action = is_string($action) ? $action : '';

        if ($action !== '' && method_exists($controlador, $action)) {
            $controlador->$action();
        } elseif (method_exists($controlador, $route)) {
            $controlador->$route();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && method_exists($controlador, 'procesar')) {
            $controlador->procesar();
        } elseif (method_exists($controlador, 'index')) {
            $controlador->index();
        }
        exit;
    }

    protected function dispatchLegado($route)
    {
        $controllerName = str_replace(['.', '/'], '', (string) $route);
        $controllerFile = $this->buscarArchivoControlador($controllerName);

        if ($controllerFile !== null && is_file($controllerFile)) {
            require_once $controllerFile;
            exit;
        }

        $vistaFile = __DIR__ . "/../views/" . $controllerName . ".php";
        if (file_exists($vistaFile)) {
            require_once $vistaFile;
            exit;
        }

        require_once __DIR__ . "/../controllers/pagina_principal.php";
    }

    protected function buscarArchivoControlador($controllerName) {
        $directorio = __DIR__ . '/../controllers/';
        $archivoDirecto = $directorio . $controllerName . '.php';
        if (file_exists($archivoDirecto)) {
            return $archivoDirecto;
        }
        $patron = '/^' . preg_quote($controllerName, '/') . '\.php$/i';
        foreach (scandir($directorio) as $archivo) {
            if (preg_match($patron, $archivo)) {
                return $directorio . $archivo;
            }
        }
        return null;
    }
}
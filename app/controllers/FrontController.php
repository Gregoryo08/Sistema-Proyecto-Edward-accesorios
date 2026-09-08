<?php

namespace App\Sistema\Controllers;

class FrontController {

    public function __construct() {
        $this->dispatch();
    }

    protected function dispatch() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $route = $_GET['pagina'] ?? $_GET['controller'] ?? 'pagina_principal';
        
        $controllerName = str_replace(['.', '/'], '', $route);
        $controllerFile = __DIR__ . "/../controllers/{$controllerName}.php";

        // ============================================================
        // RUTAS DEL E-COMMERCE (públicas, requieren sesión de tienda)
        // ============================================================
        $paginas_ecommerce = [
            // === AUTH ===
            'loginTiendaController', 'logintiendacontroller', 'loginTienda', 'login_tienda',
            'registroTiendaController', 'registroTienda', 'registro_tienda', 'registroEcommerce',
            'Loginecommerce', 'loginEcommerce',
            
            // === CARRITO ===
            'carrito',
            'verificarSesion', 'guardarDestino',
            
            // === CHECKOUT ===
            'checkout', 'guardarPedido', 'confirmacion', 'listarBancos',
            
            // === PAGOS ===
            'reportarPago', 'pagos_online', 'procesarReporte', 'datosPago',
            
            // === PEDIDOS ===
            'misPedidos', 'verPedido',
            
            // === CATÁLOGO ===
            'catalogo', 'web_catalogo', 'web_Catalogo',
            
            // === PANEL CAJERA ===
            'cajeraPanel', 'getDashboardData', 'procesarVerificacion',
            'crearDespacho', 'gestionarDespacho', 'getEstadisticasGraficos',
            
            // === RECUPERACIÓN ===
            'recuperacion_tienda', 'recuperacion',
            
            // === TASA DE CAMBIO ===
            'tasaCambio',
            'tasaEcommerce',

            // === CERRAR SESIÓN ===
            'cerrarSesionCliente',
        ];

        // Mapa de rutas pagina= a clases controladoras OOP
        $paginaControllerMap = [
            'loginTiendaController' => 'LoginTiendaController',
            'logintiendacontroller' => 'LoginTiendaController',
            'loginTienda'           => 'LoginTiendaController',
            'login_tienda'          => 'LoginTiendaController',
            'registroTiendaController' => 'RegistroTiendaController',
            'registroTienda'        => 'RegistroTiendaController',
            'registro_tienda'       => 'RegistroTiendaController',
            'registroEcommerce'     => 'RegistroTiendaController',
            'Loginecommerce'        => 'LoginTiendaController',
            'loginEcommerce'        => 'LoginTiendaController',
            'misPedidos'            => 'MisPedidosController',
            'verPedido'             => 'VerPedidoController',
            'tasaCambio'            => 'TasaCambioController',
            'tasaEcommerce'         => 'TasaEcommerceController',
            'carrito'               => 'CarritoController',
            'verificarSesion'       => 'CarritoController',
            'guardarDestino'        => 'CarritoController',
            'cerrarSesionCliente'   => 'LoginTiendaController',
        ];

        // ============================================================
        // PROCESAMIENTO DE RUTAS
        // ============================================================

        // 1. Si es ruta del e-commerce
        if (in_array($route, $paginas_ecommerce)) {
            // Intentar cargar como clase controladora OOP via paginaControllerMap
            $className = $paginaControllerMap[$route] ?? null;
            if ($className) {
                $fullClass = "App\\Sistema\\Controllers\\$className";
                if (class_exists($fullClass)) {
                    $controlador = new $fullClass();
                    $action = $_GET['action'] ?? '';
                    if ($action && method_exists($controlador, $action)) {
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
            }
            // Fallback: cargar como archivo procedural
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                exit;
            }
            header("HTTP/1.0 404 Not Found");
            echo "Error 404: Controlador '$route' no encontrado.";
            exit;
        }

        // 2. Rutas públicas de la intranet (sin autenticación)
        $paginas_publicas = [
            'iniciarSesion',
            'api_productos',
            'notificacion',
        ];

        if (in_array($route, $paginas_publicas)) {
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
            }
            exit;
        }

        // 3. Rutas de la intranet (cargar directamente, los controladores manejan su propia auth)
        // Si la ruta apunta a un controlador OOP sin mapeo, NO incluirlo en silencio (evita páginas en blanco)
        $classGuess = "App\\Sistema\\Controllers\\" . ucfirst($controllerName);
        if (class_exists($classGuess)) {
            header("HTTP/1.0 404 Not Found");
            echo "Error 404: Página no encontrada.";
            exit;
        }
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            exit;
        }

        // 4. Fallback por defecto (página principal)
        require_once __DIR__ . "/../controllers/pagina_principal.php";
    }
}
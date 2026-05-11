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

        
        $route = $_GET['pagina'] ?? 'iniciarSesion'; 

        
        $controllerName = str_replace(['.', '/'], '', $route);
        $controllerFile = "app/controllers/{$controllerName}.php";

        
        if (file_exists($controllerFile)) {
            require $controllerFile;
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Error 404: Página o controlador no encontrado.";
        }
    }
}

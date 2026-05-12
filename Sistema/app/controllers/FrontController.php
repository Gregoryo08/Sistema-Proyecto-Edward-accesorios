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
    $controllerFile = __DIR__ . "/../controllers/{$controllerName}.php";

    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        
        if ($route === 'api_productos') {
            exit; 
        }
    } else {
       
        require_once __DIR__ . "/../controllers/iniciarSesion.php";
    }
}
}
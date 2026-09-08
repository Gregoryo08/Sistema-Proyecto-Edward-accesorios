<?php
namespace App\Sistema\Controllers;

use App\Sistema\models\ClienteEcommerce;

class LoginTiendaController {
    private $model;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new ClienteEcommerce();
    }
    
    public function index() {
        if (isset($_GET['destino'])) {
            $_SESSION['destino_after_login'] = $_GET['destino'];
        }
        
        if ($this->model->ejecutar('estaLogueado')) {
            $destino = $_SESSION['destino_after_login'] ?? 'web_Catalogo';
            header('Location: ?pagina=' . $destino);
            exit;
        }
        
        require_once __DIR__ . '/../views/loginEcommerce.php';
    }
    
    public function procesar() {
        $cedula = trim($_POST['cedula'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        if (empty($cedula) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }
        
        $resultado = $this->model->ejecutar('login', ['cedula' => $cedula, 'password' => $password]);
        
        if ($resultado['success']) {
            $destino = $_SESSION['destino_after_login'] ?? 'web_Catalogo';
            $resultado['redirect'] = '?pagina=' . $destino;
        }
        
        echo json_encode($resultado);
        exit;
    }
    
    public function logout() {
        $this->cerrarSesionCliente();
    }

    public function cerrarSesionCliente() {
        unset($_SESSION['es_ecommerce']);
        unset($_SESSION['cliente_cedula']);
        unset($_SESSION['cliente_nombre']);
        unset($_SESSION['cliente_correo']);
        unset($_SESSION['cliente_telefono']);
        unset($_SESSION['cliente_direccion']);
        unset($_SESSION['destino_after_login']);
        unset($_SESSION['carrito']);

        header('Location: ?pagina=web_Catalogo');
        exit;
    }
}

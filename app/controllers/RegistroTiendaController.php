<?php
namespace App\Sistema\Controllers;

use App\Sistema\models\ClienteEcommerce;

class RegistroTiendaController {
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

        $mostrarRegistro = true;

        require_once __DIR__ . '/../views/loginEcommerce.php';
    }
    
    public function procesar() {
        $datos = [
            'cedula_persona'   => $_POST['cedula_persona'] ?? '',
            'nombre'           => $_POST['nombre'] ?? '',
            'apellido'         => $_POST['apellido'] ?? '',
            'correo'           => $_POST['correo'] ?? '',
            'telefono'         => $_POST['telefono'] ?? '',
            'direccion'        => $_POST['direccion'] ?? '',
            'password'         => $_POST['clave'] ?? '',
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
            'sexo'             => $_POST['sexo'] ?? null
        ];
        
        $camposObligatorios = ['cedula_persona', 'nombre', 'correo', 'telefono', 'password'];
        foreach ($camposObligatorios as $campo) {
            if (empty($datos[$campo])) {
                echo json_encode(['success' => false, 'message' => 'El campo ' . str_replace('_', ' ', $campo) . ' es obligatorio']);
                exit;
            }
        }
        
        if (empty($datos['cedula_persona'])) {
            echo json_encode(['success' => false, 'message' => 'Cédula requerida']);
            exit;
        }
        
        if (!empty($datos['fecha_nacimiento'])) {
            $anio = date('Y', strtotime($datos['fecha_nacimiento']));
            $fechaNac = date('Y-m-d', strtotime($datos['fecha_nacimiento']));
            if ($fechaNac === date('Y-m-d')) {
                echo json_encode(['success' => false, 'message' => 'La fecha de nacimiento no puede ser la fecha actual']);
                exit;
            }
            if ($anio < 1955 || $anio > 2008) {
                echo json_encode(['success' => false, 'message' => 'La fecha de nacimiento debe ser entre 1955 y 2008']);
                exit;
            }
        }
        
        $resultado = $this->model->ejecutar('registrar', $datos);
        
        if ($resultado['success']) {
            $_SESSION['cliente_cedula'] = $datos['cedula_persona'];
            $_SESSION['cliente_nombre'] = $datos['nombre'] . ' ' . $datos['apellido'];
            $_SESSION['cliente_correo'] = $datos['correo'];
            $_SESSION['cliente_telefono'] = $datos['telefono'];
            $_SESSION['es_ecommerce'] = true;

            $destino = $_SESSION['destino_after_login'] ?? 'web_Catalogo';
            $resultado['redirect'] = '?pagina=' . $destino;
        }
        
        echo json_encode($resultado);
        exit;
    }
}

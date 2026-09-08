<?php
// app/controllers/Pagos_online_Controller.php
namespace App\Sistema\Controllers;

use App\Sistema\models\PagoOnlineModel;

class Pagos_online_Controller {
    private $pagoModel;
    
    public function __construct() {
        $this->pagoModel = new PagoOnlineModel();
    }
    
    /**
     * Mostrar el formulario de reporte de pago
     */
    public function index() {
        // Obtener datos de la URL
        $id_pedido = $_GET['pedido'] ?? 0;
        $metodo = $_GET['metodo'] ?? 'transferencia';
        
        // Verificar que el pedido existe y pertenece al cliente
        if ($id_pedido > 0) {
            // Aquí puedes validar que el pedido existe
            // y que pertenece al cliente logueado
        }
        
        // Cargar la vista
        require_once __DIR__ . '/../views/reportarPago.php';
    }
    
    /**
     * Procesar el reporte de pago (AJAX)
     */
    public function procesar() {
        // Verificar que sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        // Validar sesión de cliente
        if (!isset($_SESSION['cliente_id']) && !isset($_SESSION['cliente_cedula'])) {
            return $this->jsonResponse(['success' => false, 'message' => 'Debes iniciar sesión'], 401);
        }
        
        // Recoger datos del formulario
        $datos = [
            'id_pedido' => $_POST['id_pedido'] ?? 0,
            'cedula_persona' => $_SESSION['cliente_cedula'] ?? null,
            'referencia' => $_POST['referencia'] ?? null,
            'banco_emisor' => $_POST['banco_emisor'] ?? null,
            'banco_receptor' => $_POST['banco_receptor'] ?? 'Banesco',
            'telefono' => $_POST['telefono'] ?? null,
            'monto' => $_POST['monto'] ?? null,
            'fecha_transferencia' => date('Y-m-d'),
            'nombre_pagador' => $_POST['nombre_pagador'] ?? null,
            'cedula_pagador' => $_POST['cedula_pagador'] ?? null,
            'comprobante' => null // Por ahora no hay subida de archivos
        ];
        
        // ✅ VALIDACIONES BACKEND
        if (empty($datos['id_pedido']) || $datos['id_pedido'] <= 0) {
            return $this->jsonResponse(['success' => false, 'message' => 'ID de pedido inválido']);
        }
        
        if (empty($datos['referencia']) || strlen($datos['referencia']) < 5) {
            return $this->jsonResponse(['success' => false, 'message' => 'La referencia debe tener al menos 5 caracteres']);
        }
        
        if (empty($datos['banco_emisor']) || strlen($datos['banco_emisor']) < 3) {
            return $this->jsonResponse(['success' => false, 'message' => 'El banco emisor es obligatorio']);
        }
        
        if (empty($datos['telefono']) || strlen($datos['telefono']) < 10) {
            return $this->jsonResponse(['success' => false, 'message' => 'El teléfono debe tener al menos 10 dígitos']);
        }
        
        if (empty($datos['monto']) || $datos['monto'] <= 0) {
            return $this->jsonResponse(['success' => false, 'message' => 'El monto debe ser mayor a 0']);
        }
        
        // Verificar que no exista ya un pago para este pedido
        $existe = $this->pagoModel->ejecutar('existe_por_pedido', ['id_pedido' => $datos['id_pedido']]);
        if ($existe === true) {
            return $this->jsonResponse(['success' => false, 'message' => 'Ya existe un reporte de pago para este pedido']);
        }
        
        // Guardar en la base de datos
        $resultado = $this->pagoModel->ejecutar('crear', $datos);
        
        if (isset($resultado['error'])) {
            return $this->jsonResponse(['success' => false, 'message' => $resultado['error']]);
        }
        
        return $this->jsonResponse([
            'success' => true,
            'message' => 'Pago reportado correctamente',
            'id_reporte' => $resultado['id_reporte'] ?? null
        ]);
    }
    
    /**
     * Respuesta JSON
     */
    private function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
}
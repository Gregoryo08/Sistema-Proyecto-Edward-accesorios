<?php

namespace App\Sistema\Controllers;

use App\Sistema\models\TasaCambioModel;

/**
 * Controlador exclusivo del e-commerce para mantener la tasa de cambio al día.
 * Solo accesible por clientes logueados (es_ecommerce === true). Dispara el
 * scrape automático del BCV a través de TasaCambioModel; el cliente NUNCA
 * escribe un valor manual: la tasa siempre proviene del BCV/APIs.
 * No reutiliza el controlador de admin (TasaCambioController) ni su flujo de
 * clave, manteniendo los permisos de administración separados.
 */
class TasaEcommerceController
{
    private $model;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new TasaCambioModel();
    }

    /**
     * Endpoint AJAX: actualiza la tasa desde el BCV.
     * Requiere sesión de e-commerce válida (cliente logueado).
     */
    public function index()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['es_ecommerce']) || $_SESSION['es_ecommerce'] !== true) {
            echo json_encode(['success' => false, 'message' => 'No autorizado: debe iniciar sesión como cliente']);
            exit;
        }

        $tasaNueva = $this->model->actualizarDesdeBCV();

        if ($tasaNueva === null || $tasaNueva <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo obtener la tasa del BCV en este momento. Intenta nuevamente.'
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'tasa' => $tasaNueva,
            'tasa_formateada' => number_format($tasaNueva, 2),
            'vigencia' => $this->model->vigencia(),
            'fecha' => $this->model->obtenerFechaActualizacion(),
            'fuente' => $this->model->obtenerFuente()
        ]);
        exit;
    }
}

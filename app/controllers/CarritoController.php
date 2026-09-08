<?php

namespace App\Sistema\Controllers;

class CarritoController {

    public function index() {
        require_once __DIR__ . '/../views/carrito.php';
    }

    public function verificarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $logueado = isset($_SESSION['es_ecommerce']) && $_SESSION['es_ecommerce'] === true;
        header('Content-Type: application/json');
        echo json_encode(['logueado' => $logueado]);
        exit;
    }

    public function guardarDestino() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['destino_after_login'] = $_GET['destino'] ?? 'checkout';
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}

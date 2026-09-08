<?php
// /src/app/controllers/web_Catalogo.php
// =============================================
// CONTROLADOR DEL CATÁLOGO
// =============================================

namespace App\Sistema\Controllers;  // ← AGREGAR ESTO

use App\Sistema\models\ProductoModel;

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =============================================
// 1. VERIFICAR SESIÓN DE CLIENTE
// =============================================
$mostrarOpcionesCliente = false;
$nombreCliente = '';

if (isset($_SESSION['cliente_cedula']) && isset($_SESSION['cliente_nombre'])) {
    $mostrarOpcionesCliente = true;
    $nombreCliente = $_SESSION['cliente_nombre'];
}

// =============================================
// 2. OBTENER PRODUCTOS (Usa nombres REALES)
// =============================================
$productoModel = new ProductoModel();
$productos = $productoModel->ejecutar('obtenerActivos');

//  Depuración (opcional, eliminar en producción)
if (empty($productos)) {
    error_log("⚠️ web_Catalogo: No se encontraron productos");
    $productos = [];
}

// =============================================
// 2.1 OBTENER CATEGORÍAS (para el filtro del catálogo)
// =============================================
$categorias = $productoModel->ejecutar('obtenerCategorias');
if (!is_array($categorias)) {
    $categorias = [];
}

// =============================================
// 3. CARGAR VISTA
// =============================================
require_once __DIR__ . '/../views/catalogo.php';
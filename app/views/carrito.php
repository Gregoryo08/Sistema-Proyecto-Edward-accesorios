<?php require_once('assets/comunes/modal_carrito.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Carrito | Edward Accesorios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/ecommerce/temaBase.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Edward<span class="text-primary">Accesorios</span></a>
        <span class="text-white"><i class="fas fa-shopping-cart"></i> Mi Carrito</span>
    </div>
</nav>

<div class="container my-5">
    <h2 class="mb-4">Mi Carrito</h2>
    <div id="carrito-contenido">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    </div>
</div>

<script src="assets/Library/SweetAlerts/sweetalert2.all.js"></script>
<script src="assets/Library/JQuery/jquery-3.7.0.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/ecommerce/validaciones.js"></script>
<script src="assets/js/ecommerce/carrito.js"></script>

<button id="themeToggle" class="theme-toggle-btn" title="Cambiar tema" data-icon-oscuro="fas fa-sun" data-icon-claro="fas fa-moon">
    <i class="fas fa-sun" id="themeIcon"></i>
</button>
<script src="assets/js/ecommerce/temaBoton.js"></script>

</body>
</html>
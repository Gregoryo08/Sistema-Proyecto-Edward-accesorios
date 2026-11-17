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
        <a class="navbar-brand" href="?pagina=web_catalogo">Edward<span class="text-primary">Accesorios</span></a>
        
        <div class="d-flex align-items-center gap-3">
            <span class="text-white"><i class="fas fa-shopping-cart"></i> Mi Carrito</span>

          
            <div id="user-menu-container" class="dropdown">
                <a href="#" class="text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false" style="display: none;">
                    <i class="fas fa-user-circle"></i> <span id="nombre-usuario">Usuario</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser">
                    <li><a class="dropdown-item" href="?pagina=mispedidos">Mis Pedidos</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="?pagina=salida">Cerrar Sesión</a></li>
                </ul>
            </div>

            
            <a href="?pagina=iniciarSesion" id="btn-login" class="btn btn-outline-primary btn-sm" style="display: inline-block;">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
        </div>
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

</body>
</html>
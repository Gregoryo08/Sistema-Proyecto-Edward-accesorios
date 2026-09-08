<?php require_once('assets/comunes/modal_mispedidos.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Pedidos | Edward Accesorios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/ecommerce/temaBase.css">
</head>
<body>

<!-- ============================================= -->
<!-- NAVBAR COMPLETA (NAVEGACIÓN) -->
<!-- ============================================= -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="?pagina=web_Catalogo">
            <i class="fas fa-gem"></i> Edward Accesorios
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- ✅ CATÁLOGO -->
                <li class="nav-item">
                    <a class="nav-link" href="?pagina=web_Catalogo">
                        <i class="fas fa-store"></i> Catálogo
                    </a>
                </li>
                
                <!-- ✅ CARRITO -->
                <li class="nav-item">
                    <a class="nav-link" href="?pagina=carrito">
                        <i class="fas fa-shopping-cart"></i> Carrito
                        <span class="badge bg-danger rounded-pill" id="cartCount">0</span>
                    </a>
                </li>
                                            
                <!-- ✅ CERRAR SESIÓN -->
                <li class="nav-item">
                    <a class="nav-link text-danger" href="?pagina=cerrarSesionCliente">
                        <i class="fas fa-sign-out-alt"></i> Salir
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ============================================= -->
<!-- CONTENIDO PRINCIPAL -->
<!-- ============================================= -->
<div class="container my-5">
    <h2>📦 Mis Pedidos</h2>
    <p class="text-muted">Bienvenido, <?= htmlspecialchars($_SESSION['cliente_nombre'] ?? 'Cliente') ?></p>

   <?php if (empty($pedidos)): ?>
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-box-open fa-3x mb-3"></i>
            <h4>No tienes pedidos aún</h4>
            <p class="mb-3">Explora nuestro catálogo y encuentra lo que más te guste.</p>
            <a href="?pagina=web_Catalogo" class="btn btn-primary">
                <i class="fas fa-store"></i> Ir al Catálogo
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($pedidos as $pedido): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <span><strong>Pedido #<?= $pedido['id_pedido'] ?></strong></span>
                        <span>Fecha: <?= date('d/m/Y H:i', strtotime($pedido['fecha'])) ?></span>
                        
                        <!-- ========================================== -->
                        <!-- BADGE DE ESTADO (usando getEstadoData) -->
                        <!-- ========================================== -->
                        <?php 
                        // ✅ Obtener datos del estado para este pedido
                        $estadoData = $pedidoModel->getEstadoData(
                            $pedido['estado'], 
                            $pedido['motivo_rechazo'] ?? null
                        );
                        ?>
                        <span class="badge bg-<?= $estadoData['color'] ?> p-2">
                            <i class="fas <?= $estadoData['icono'] ?> me-1"></i>
                            <?= ucfirst($estadoData['estado']) ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['nombre'] . ' ' . $pedido['apellido']) ?></p>
                    <p><strong>Teléfono:</strong> <?= $pedido['telefono'] ?></p>
                    <p><strong>Correo:</strong> <?= $pedido['correo'] ?></p>
                    <p><strong>Total:</strong> $<?= number_format($pedido['total'], 2) ?></p>
                    <p><strong>Método de Pago:</strong> <?= ucfirst($pedido['metodo_pago'] ?? 'No especificado') ?></p>

                    <!-- ========================================== -->
                    <!-- NOTIFICACIÓN DE RECHAZO (si aplica) -->
                    <!-- ========================================== -->
                    <?php if ($pedido['estado'] == 'rechazado' && !empty($pedido['motivo_rechazo'])): ?>
                        <div class="alert alert-danger mt-2 py-2">
                            <i class="fas fa-exclamation-circle"></i> 
                            <strong>Pago Rechazado:</strong> <?= htmlspecialchars($pedido['motivo_rechazo']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- ========================================== -->
                    <!-- NOTIFICACIÓN DE PAGO INCOMPLETO (si aplica) -->
                    <!-- ========================================== -->
                    <?php if ($pedido['estado'] == 'pendiente pago' && !empty($pedido['motivo_rechazo'])): ?>
                        <div class="alert alert-warning mt-2 py-2">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>Pago incompleto:</strong> <?= htmlspecialchars($pedido['motivo_rechazo']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- ✅ BOTÓN "VER DETALLE" (SIEMPRE VISIBLE) -->
                    <a href="?pagina=verPedido&id=<?= $pedido['id_pedido'] ?>" 
                       class="btn btn-outline-primary btn-sm mt-2">
                        <i class="fas fa-eye"></i> Ver Detalle
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- ============================================= -->
<!-- SCRIPTS -->
<!-- ============================================= -->
<script src="assets/Library/JQuery/jquery-3.7.0.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/ecommerce/carritoContador.js"></script>

<button id="themeToggle" class="theme-toggle-btn" title="Cambiar tema" data-icon-oscuro="fas fa-sun" data-icon-claro="fas fa-moon">
    <i class="fas fa-sun" id="themeIcon"></i>
</button>
<script src="assets/js/ecommerce/temaBoton.js"></script>

</body>
</html>
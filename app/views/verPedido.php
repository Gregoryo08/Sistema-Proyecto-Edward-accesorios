<?php require_once('assets/comunes/modal_verpedido.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Pedido | Edward Accesorios</title>
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
                    <li class="nav-item">
                        <a class="nav-link" href="?pagina=web_Catalogo">
                            <i class="fas fa-store"></i> Catálogo
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?pagina=carrito">
                            <i class="fas fa-shopping-cart"></i> Carrito
                            <span class="badge bg-danger rounded-pill" id="cartCount">0</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="?pagina=cerrarSesionCliente">
                            <i class="fas fa-sign-out-alt"></i> Salir
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2>🧾 Detalle del Pedido #<?= $pedido['id_pedido'] ?></h2>
        <a href="?pagina=misPedidos" class="btn btn-secondary mb-3">← Volver a Mis Pedidos</a>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Datos del Pedido</h5>
            </div>
            <div class="card-body">
                <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['nombre'] . ' ' . $pedido['apellido']) ?></p>
                <p><strong>Teléfono:</strong> <?= $pedido['telefono'] ?></p>
                <p><strong>Correo:</strong> <?= $pedido['correo'] ?></p>
                <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha'])) ?></p>
                <p><strong>Dirección:</strong> <?= htmlspecialchars($pedido['direccion_entrega'] ?? 'No especificada') ?></p>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white">
                <h5>Productos</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalle as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['nombre_producto']) ?></td>
                                <td><?= $item['cantidad'] ?></td>
                                <td>$<?= number_format($item['precio_unitario'], 2) ?></td>
                                <td>$<?= number_format($item['subtotal'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong>$<?= number_format($pedido['total'], 2) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- gmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgm -->
        <!-- ESTADO DEL PEDIDO (USANDO getEstadoData() DEL MODELO) -->
        <!-- gmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgm -->
        <?php 
        
            $estadoData = $pedidoModel->getEstadoData(
            $pedido['estado'], 
            $pedido['motivo_rechazo'] ?? null
        );
        ?>

        <div class="card shadow-sm">
            <div class="card-header bg-<?= $estadoData['color'] ?> text-white">
                <h5>Estado del Pedido</h5>
            </div>
            <div class="card-body">
                <!-- BADGE DE ESTADO -->
                <span class="badge bg-<?= $estadoData['color'] ?> p-2 mb-3" style="font-size: 1rem;">
                    <i class="fas <?= $estadoData['icono'] ?>"></i> 
                    <?= ucfirst($estadoData['estado']) ?>
                </span>

                <!-- ALERTA DE ESTADO -->
                <div class="alert alert-<?= $estadoData['clase_alerta'] ?> mt-2">
                    <i class="fas <?= $estadoData['icono'] ?>"></i> 
                    <strong><?= $estadoData['mensaje']['titulo'] ?></strong>
                    <p class="mb-0"><?= $estadoData['mensaje']['cuerpo'] ?></p>
                </div>

                <!-- BOTÓN (SOLO SI APLICA) -->
                <?php if ($estadoData['mostrar_boton']): ?>
                    <a href="?pagina=reportarPago&pedido=<?= $pedido['id_pedido'] ?>" 
                       class="btn btn-<?= $estadoData['boton_estilo'] ?> btn-lg mt-2">
                        <i class="fas <?= $estadoData['boton_icono'] ?>"></i> 
                        <?= $estadoData['texto_boton'] ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
</div>

<script src="assets/Library/JQuery/jquery-3.7.0.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/ecommerce/carritoContador.js"></script>

<button id="themeToggle" class="theme-toggle-btn" title="Cambiar tema" data-icon-oscuro="fas fa-sun" data-icon-claro="fas fa-moon">
    <i class="fas fa-sun" id="themeIcon"></i>
</button>
<script src="assets/js/ecommerce/temaBoton.js"></script>

</body>
</html>
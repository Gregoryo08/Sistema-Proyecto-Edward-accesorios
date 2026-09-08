<div class="modal fade" id="modalCatalogo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuestros Productos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php if (empty($productos)): ?>
                    <div class="alert alert-info">No hay productos disponibles en este momento.</div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($productos as $producto): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 shadow-sm">
                                    <?php $rutaWeb = 'assets/img/productos/' . ($producto['imagen_principal'] ?? 'default.jpg'); ?>
                                    <img src="<?= $rutaWeb ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?= htmlspecialchars($producto['nombre_producto'] ?? 'Producto') ?>" onerror="this.src='assets/img/productos/default.jpg'">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($producto['nombre_producto'] ?? 'Sin nombre') ?></h5>
                                        <p class="card-text text-muted small"><?= htmlspecialchars(substr($producto['descripcion'] ?? '', 0, 80)) ?>...</p>
                                        <p class="precio">$<?= number_format($producto['precio_detal'] ?? 0, 2) ?></p>
                                        <p class="text-muted small">Stock: <?= $producto['stock_actual'] ?? 0 ?> unidades</p>
                                        <button class="btn btn-comprar w-100 agregar-carrito" data-id="<?= $producto['id_producto'] ?>" data-nombre="<?= htmlspecialchars($producto['nombre_producto'] ?? 'Producto') ?>" data-precio="<?= number_format($producto['precio_detal'] ?? 0, 2, '.', '') ?>"><i class="fas fa-cart-plus"></i> Agregar al Carrito</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

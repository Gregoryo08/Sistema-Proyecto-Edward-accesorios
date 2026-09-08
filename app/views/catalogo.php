<?php include 'assets/comunes/menu_principal.php'; ?>
<link rel="stylesheet" href="assets/css/catalogo.css">

    <main class="catalog-page">
        <header class="catalog-header">
            <div class="container text-center">
                <h1>Catálogo de Productos</h1>
                <p class="subtitle-text">Explora nuestra amplia gama de productos y encuentra lo que necesitas.</p>
            </div>
        </header>

        <div class="container catalog-container">
            <h2 class="section-title">Nuestros Productos</h2>

            <div class="search-wrapper">
                <div class="search-box">
                    <span class="search-icon"><i class="fas fa-search"></i></span>
                    <input type="text"
                        id="buscadorProductos"
                        class="search-input"
                        placeholder="Buscar productos...">
                </div>

                <!-- Select de Categorías -->
                <div class="category-box">
                    <select id="selectCategoria" class="category-select">
                        <option value="">Todas las categorías</option>
                        <?php if (!empty($categorias)): ?>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['id_categoria']) ?>">
                                    <?= htmlspecialchars($cat['nombre_categoria']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <?php if (empty($productos)): ?>
                <div class="alert-info">No hay productos disponibles en este momento.</div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($productos as $producto): ?>
                        <!-- Añadido el atributo data-categoria -->
                        <div class="product-card" data-categoria="<?= htmlspecialchars($producto['id_categoria'] ?? '') ?>">
                            <?php $rutaWeb = 'assets/img/productos/' . ($producto['imagen_principal'] ?? 'default.jpg'); ?>
                            <div class="card-img-wrapper">
                                <img src="<?= $rutaWeb ?>"
                                    class="product-img"
                                    alt="<?= htmlspecialchars($producto['nombre_producto'] ?? 'Producto') ?>"
                                    onerror="this.src='assets/img/productos/default.jpg'">
                            </div>

                            <div class="product-body">
                                <h3 class="product-title"><?= htmlspecialchars($producto['nombre_producto'] ?? 'Sin nombre') ?></h3>
                                <p class="product-desc">
                                    <?= htmlspecialchars(substr($producto['descripcion'] ?? '', 0, 80)) ?>...
                                </p>
                                <p class="precio">$<?= number_format($producto['precio_detal'] ?? 0, 2) ?></p>
                                <p class="product-stock">Stock: <?= $producto['stock_actual'] ?? 0 ?> unidades</p>

                                <button class="btn-comprar agregar-carrito"
                                    data-id="<?= $producto['id_producto'] ?>"
                                    data-nombre="<?= htmlspecialchars($producto['nombre_producto'] ?? 'Producto') ?>"
                                    data-precio="<?= number_format($producto['precio_detal'] ?? 0, 2, '.', '') ?>">
                                    <i class="fas fa-cart-plus"></i> Agregar al Carrito
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Mensaje cuando el filtro no arroja resultados -->
                <div id="sinResultados" class="alert-info">No se encontraron productos que coincidan con la búsqueda.</div>
            <?php endif; ?>
        </div>

        <script src="assets/Library/SweetAlerts/sweetalert2.all.js"></script>
        <script src="assets/js/ecommerce/catalogo.js?v=3"></script>
    </main>
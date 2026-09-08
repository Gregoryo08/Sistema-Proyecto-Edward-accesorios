<?php require_once('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modal_productos.php'); ?>

<main class="main mt-4" id="main">
    <section id="hero" class="hero section mt-2" style="height: auto;">
        <div class="container">
            <h2 class="text-center mb-4">Administrar Inventario de Productos</h2>

            <div class="text-start mb-3">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRegistroProducto">
                    <i class="bi bi-plus-circle"></i> Registrar Producto
                </button>
                <small class="text-muted ms-3"><i class="bi bi-info-circle"></i> Para agregar o cambiar imagen, haga clic en el ícono 🖼️ o en la foto del producto</small>
            </div>

            <div class="table-responsive">
                <input type="hidden" id="btn_deleteProducto">
                <table id="tablaProductos" class="table table-striped table-bordered text-center w-100">
                    <thead class="thead-dark">
                        <tr>
                            <th style="display: none;">ID</th>
                            <th>Imagen</th>
                            <th>Producto</th>
                            <th>Marca</th>
                            <th>Categoría</th>
                            <th>Stock Actual</th> <th>Precio Detal</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<?php require_once('assets/comunes/footer.php'); ?>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>


<script src="assets/js/validaciones/productos/productos.js"></script>
<script src="assets/js/validaciones/productos/productos2.js"></script>

</body>
</html>
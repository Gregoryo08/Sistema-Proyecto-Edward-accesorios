<?php require_once('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modal_entradas_productos.php'); ?>

<main class="main mt-4" id="main">
    <section id="hero" class="hero section mt-2" style="height: auto;">
        <div class="container">
            <h2 class="text-center mb-4">Administrar Entradas de Productos</h2>

            <div class="mb-3">
                <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm" id="btnAgregarEntrada" data-bs-toggle="modal" data-bs-target="#modalReponerProducto" style="display: none;">
                    <i class="bi bi-cart-plus me-1 fs-5"></i> Reponer Productos
                </button>
            </div>
            
            <div class="table-responsive">
                <div class="table-container">
                    <table id="tablaEntradasProductos" class="table table-striped table-bordered text-center w-100">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nombre del Proveedor</th>
                                <th>Fecha de Entrada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once('assets/comunes/footer.php'); ?>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
</a>

<style>
    .select2-container--open {
        z-index: 99999 !important;
    }
    .select2-container {
        width: 100% !important;
    }
    .select2-selection--single {
        height: 38px !important;
        padding: 6px !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
    }
</style>


<script src="assets/js/validaciones/entradas_productos/entradas_productos.js"></script>
<script src="assets/js/validaciones/entradas_productos/entradas_productos2.js"></script>

</body>
</html>
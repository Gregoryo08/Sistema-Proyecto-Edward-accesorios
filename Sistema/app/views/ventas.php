<?php require_once('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modal_ventas.php'); ?>

<main class="main" id="main">
    <section id="hero" class="hero section" style="height: auto;">
        <div class="container">
            <h2 class="text-center mb-4">Administrar Ventas (Tienda)</h2>

            <div class="text-end mb-3">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRegistroVenta">
                    <i class="bi bi-plus-circle"></i> Registrar Venta
                </button>
            </div>

            <div class="table-responsive">
                <table id="tablaVentas" class="table table-striped table-bordered text-center w-100">
                    <thead class="thead-dark">
                        <tr>
                            <th style="display: none;">ID</th>
                            <th>Fecha</th>
                            <th>Cliente / Origen</th>
                            <th>Método de Pago</th>
                            <th>Referencia</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<?php require_once('assets/comunes/footer.php'); ?>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/validaciones/ventas/ventas.js"></script>

</body>
</html>
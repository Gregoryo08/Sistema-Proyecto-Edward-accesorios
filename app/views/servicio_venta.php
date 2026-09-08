<?php require_once('assets/comunes/menu.php'); ?>

<main class="main" id="main">
    <section id="hero" class="hero section" style="height: auto;">
        <div class="container">
            <h2 class="text-center mb-4">Servicio Venta</h2>

            <div class="table-responsive">
                <table id="tablaServicioVenta" class="table table-striped table-bordered text-center w-100">
                    <thead class="thead-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Cliente / Origen</th>
                            <th>Método de Pago</th>
                            <th>Referencia</th>
                            <th>Total</th>
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
<script src="assets/js/validaciones/servicio_venta/servicio_venta.js"></script>

</body>
</html>

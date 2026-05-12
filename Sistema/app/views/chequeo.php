<?php require_once('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modal_chequeo.php'); ?>

<main class="main" id="main">
    <section id="hero" class="hero section" style="height: auto;">
        <div class="container">
            <h2 class="text-center mb-4">Administrar Solicitudes de Chequeo</h2>

            <div class="table-responsive">
                <table id="tablaChequeo" class="table table-striped table-bordered text-center w-100">
                    <thead class="thead-dark">
                        <tr>
                            <th style="display: none;">ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Método de Pago</th>
                            <th>Total</th>
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

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/validaciones/chequeo/chequeo.js"></script>

</body>
</html>
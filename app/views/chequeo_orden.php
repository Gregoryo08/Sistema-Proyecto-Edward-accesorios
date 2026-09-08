<?php

       require_once('assets/comunes/menu.php');
?>

<?php require_once('assets/comunes/modal_ver_orden.php'); ?>
<?php require_once('assets/comunes/modal_chequeo_orden.php'); ?>

<main class="main" id="main">

    <section id="hero" class="hero section" style="height: auto;">
        <div>
            <h2 class="text-center">Chequeo de Órdenes Pendientes</h2>

            <div class="table-responsive mt-4">
                <div class="table-container">
                    <table id="chequeoOrdenTabla" class="table table-striped table-bordered text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Equipo</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Fecha</th>
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

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/Validaciones/orden/chequeo_orden.js"></script>

</body>

</html>

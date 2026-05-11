<?php require_once('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modalBancos.php'); ?>

<main class="main" id="main">
    <section id="hero" class="hero section" style="height: auto;">
        
        <div>
            <h2 class="text-center">Administrar Bancos</h2>

            <div class="text-right">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalBancos">
                    Registrar
                </button>

            </div>

            <div class="table-responsive">
                <div class="table-container">
                    <input type="hidden" id="btn_delete">

                    <table id="tablaBancos" class="table table-striped table-bordered text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nombre del Banco</th>
                                <th>Telefono</th>
                                <th>Cedula / RIF</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                </td>
                                <td></td>
                                <td>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>

</main>

<?php require_once('assets/comunes/footer.php'); ?>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/validaciones/bancos/bancos.js"></script>
<script src="assets/js/validaciones/bancos/bancos2.js"></script>

</body>

</html>
<?php require_once('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modal_turnos.php'); ?>

<main class="main" id="main">

    <section id="hero" class="hero section" style="height: auto;">
        <div>
            <h2 class="text-center">Administrar Turnos</h2>

            <div class="text-right">
                <button type="button" class="btn btn-success" id="btn_turno">
                    Registrar Turno
                </button>
            </div>

            <div class="table-responsive">
                <div class="table-container">
                    <table id="tablaTurnos" class="table table-striped table-bordered text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th style="display: none;">Id Turno</th>
                                <th>Fecha del Turno</th>
                                <th>Jornada del Turno</th>
                                <th>Empleados del Turno</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
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
<script src="assets/js/validaciones/turno/turno.js"></script>
<script src="assets/js/validaciones/turno/turno2.js"></script>

</body>

</html>
<style>
    #btn_turno {
    display: block !important;
}
</style>
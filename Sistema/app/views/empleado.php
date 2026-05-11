<?php require_once('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modalEmpleado.php'); ?>

<main class="main" id="main">

    <section id="hero" class="hero section" style="height: auto;">
        
        <div>
            <?php require_once('assets/comunes/tablaEmpleadosInactivos.php'); ?>
            <h2 class="text-center">Administrar Empleados</h2>

            <div class="text-right" style="display: flex; justify-content: space-between;">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" id="registrarEmpleados">
                    Registrar Empleado
                </button>

                <button type="button" class="btn btn-success" id="btn_verInactivos">
                    Inactivos
                </button>
            </div>

            <div class="table-responsive">
                <div class="table-container">
                    <table id="tablaEmpleados" class="table table-striped table-bordered text-center">
                        <thead class="thead-dark">
                            <tr style="text-align: center;">
                                <th>Cedula</th>
                                <th>Nombre y Apellido</th>
                                <th>Cargo</th>
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
<script src="assets/js/validaciones/empleados/empleados.js"></script>
<script src="assets/js/validaciones/empleados/empleados2.js"></script>

</body>

</html>
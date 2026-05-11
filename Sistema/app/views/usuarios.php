<?php require_once('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modalEmpleado.php'); ?>

<main class="main" id="main">

    <section id="hero" class="hero section" style="height: auto;">
        <?php require_once('assets/comunes/tablaPerfilesInactivos.php'); ?>

        <div>
            <h2 class="text-center">Crear Perfiles de Usuario</h2>

            <div class="text-right" style="display: flex; justify-content: space-between;">

                <button type="button" class="btn btn-success" id="btn_verInactivos">
                    <i class="bi bi-person-x"></i> Inactivos
                </button>
            </div>

            <div class="table-responsive">
                <div class="table-container">
                    <table id="tablaPerfilados" class="table table-striped table-bordered text-center">
                        <thead class="thead-dark">
                            <tr style="text-align: center;">
                                <th>Cedula</th>
                                <th>Nombre y Apellido</th>
                                <th>Cargo</th>
                                <th>Perfil</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
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

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/validaciones/usuarios/usuarios1.js"></script>
<script src="assets/js/validaciones/usuarios/usuarios2.js"></script>

</body>

</html>
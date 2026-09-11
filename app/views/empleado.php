<?php require_once('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modalEmpleado.php'); ?>

<main class="main m-4" id="main">
    <section id="hero" class="hero section p-4" style="height: auto;">
            <h2 class="text-center">Administrar Empleados</h2>

            <div class="text-end mb-3">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" id="registrarEmpleados">
                    <i class="bi bi-person-add" style="font-size: 1.2rem;"></i> Registrar Empleado
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
                                <th>Estado</th>
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
</section>
</main>

<?php require_once('assets/comunes/footer.php'); ?>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>


<script src="assets/js/validaciones/empleados/empleados.js"></script>
<script src="assets/js/validaciones/empleados/empleados2.js"></script>

</body>

</html>
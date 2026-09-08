<?php require_once("assets/comunes/menu.php")?>
<?php require_once('assets/comunes/modalEspecialidad.php'); ?>

<main class="main m-4" id="main">
    <section id="hero" class="hero section m-2" style="height: auto;">

        <div class="container-fluid">
            <h2 class="text-center">Administrar Especialidades del Sistema</h2>

            <div class="text-right">
                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                    data-bs-target="#modalRegistroEspecialidad" style="display: none;">
                    Registrar Especialidad
                </button>
            </div>

            <div class="table-responsive">
                <div class="table-container">
                    <table id="tablaEspecialidades" class="table table-striped table-bordered text-center" style="width: 100%;">
                        <thead class="thead-dark">
                            <tr>
                                <th style="display: none;">ID</th>
                                <th>Nombre de la Especialidad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </section>
</main>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
</a>



<script src="assets/js/validaciones/especialidad/especialidad.js"></script>

</body>
</html>
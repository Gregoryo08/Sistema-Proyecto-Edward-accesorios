<?php require_once("assets/comunes/menu.php")?>
<?php require_once('assets/comunes/modalModulo.php'); ?>

<main class="main" id="main">
    <section id="hero" class="hero section" style="height: auto;">

        <div>
            <h2 class="text-center">Administrar Modulos del Sistema</h2>

            <div class="text-right">
                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                    data-bs-target="#modalRegistroModulo">
                    Registrar Modulo
                </button>
            </div>

            <div class="table-responsive">
                <div class="table-container">
                    <table id="tablaModulos" class="table table-striped table-bordered text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th style="display: none;">ID</th>
                                <th>Nombre del Modulo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </section>

</main>



<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>

<script src="assets/js/validaciones/modulo/modulo.js"></script>
<script src="assets/js/validaciones/modulo/modulo2.js"></script>

</body>

</html>


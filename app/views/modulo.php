<?php require_once("assets/comunes/menu.php")?>
<?php require_once('assets/comunes/modalModulo.php'); ?>

<main class="main m-4" id="main">
    <section id="hero" class="hero section" style="height: auto;">
        <div class="w-75 mx-auto">
            <h2 class="text-center" style="font-weight: bold; font-size: 35px;">Administrar Modulos del Sistema</h2>

            <div class="text-end mb-3">
                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                    data-bs-target="#modalRegistroModulo">
                    <i class="bi bi-plus-circle"></i> Registrar Modulo
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
<?php require_once('assets/comunes/footer.php'); ?>


<script src="assets/js/validaciones/modulo/modulo.js"></script>
<script src="assets/js/validaciones/modulo/modulo2.js"></script>

</body>

</html>


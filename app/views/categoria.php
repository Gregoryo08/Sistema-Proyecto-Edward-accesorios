<?php require_once("assets/comunes/menu.php")?>
<?php require_once('assets/comunes/modalCategoria.php'); ?>

<main class="main m-4" id="main">
    <section id="hero" class="hero section m-2" style="height: auto;">

        <div class="w-75 mx-auto">
            <h2 class="text-center" style="font-size: 45px;">Administrar Categorías</h2>

            <div class="text-end m-3">
                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                    data-bs-target="#modalRegistroCategoria">
                    <i class="bi bi-plus-circle"></i> Registrar Categoría
                </button>
            </div>

            <div class="table-responsive">
                <div class="table-container">
                    <table id="tablaCategorias" class="table table-striped table-bordered text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th style="display: none;">ID</th>
                                <th>Nombre de la Categoría</th>
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



<script src="assets/js/validaciones/categorias/categoria.js"></script>
<script src="assets/js/validaciones/categorias/categoria2.js"></script>

</body>

</html>
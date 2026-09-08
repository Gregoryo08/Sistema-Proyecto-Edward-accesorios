<?php require_once('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modalBitacora.php'); ?>



<main class="main" id="main">
    <section id="hero" class="hero section" style="height: auto;">
        <div>
            <h2 class="text-center">Consultar Bitacora</h2>

            <div class="mb-3 text-center">
                <label for="filtroRoles" class="fw-bold">Filtrar por Roles:</label>
                <select id="filtroRoles" class="form-select d-inline-block w-auto ms-2">
                    <option value="">Todos los Roles</option>
                </select>
            </div>

            <div class="table-responsive">
                <table id="tablaBitacora" class="table table-striped table-bordered text-center">
                    </table>
            </div>
        </div>
    </section>


    <div class="modal fade" id="modalDetallesBitacora" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Cambio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Dato Antiguo:</h6>
                <p id="modalAntiguo" class="bg-light p-2"></p>
                <hr>
                <h6>Dato Nuevo:</h6>
                <p id="modalNuevo" class="bg-light p-2"></p>
            </div>
        </div>
    </div>
</div>
</main>

<?php require_once('assets/comunes/footer.php'); ?>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>


<script src="assets/js/validaciones/bitacora/administrar_bitacora.js"></script>
</body>
</html>
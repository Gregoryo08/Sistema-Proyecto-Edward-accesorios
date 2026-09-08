<?php require_once('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modal_proveedores.php'); ?>

<main class="main mt-4" id="main">
    <section id="hero" class="hero section mt-2" style="height: auto;">
        <div class="container">
            <h2 class="text-center mb-4">Administrar Proveedores</h2>

            <div class="text-start mb-3">
                <button type="button" class="btn btn-success" id="btn_nuevo_proveedor" data-bs-toggle="modal" data-bs-target="#modalRegistroProveedor">
                    <i class="bi bi-plus-circle"></i> Registrar Proveedor
                </button>
            </div>

            <div class="table-responsive">
                <input type="hidden" id="rif_proveedor">
                <table id="tablaProveedores" class="table table-striped table-bordered text-center w-100">
                    <thead class="thead-dark">
                        <tr>
                            <th>Rif</th>
                            <th>Nombre Proveedor</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<?php require_once('assets/comunes/footer.php'); ?>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>


<script src="assets/js/validaciones/proveedores/proveedores.js"></script>

</body>
</html>




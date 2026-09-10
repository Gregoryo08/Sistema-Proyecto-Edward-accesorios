<?php
require_once("assets/comunes/menu.php")
?>
<?php require_once('assets/comunes/modalRoles.php'); ?>

<main class="main m-4" id="main">
<section id="hero" class="hero section p-4" style="height: auto;">
    <div class="w-75 mx-auto">
            <h2 class="text-center" style="font-weight: bold; font-size: 40px;">Administrar Roles de Usuario</h2>
            <div class="text-end mb-3">
                <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistroRol" id="btn_registro_rol">
                    <i class="bi bi-plus-circle"></i> Registrar Rol
                </button>
            </div>

            <div class="table-responsive">
                <div class="table-container">
                    <table id="tablaRoles" class="table table-striped table-bordered text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th style="display: none;">ID </th>
                                <th style="text-align: center; font-weight: bold; font-size: 14px;">Nombre del Rol</th>
                                <th style="text-align: center; font-weight: bold; font-size: 14px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="numero"></td>
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
<script src="assets/js/validaciones/roles/roles1.js"></script>
<script src="assets/js/validaciones/roles/roles2.js"></script>

</body>

</html>
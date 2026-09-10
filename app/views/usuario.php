<?php require_once('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modal_usuarios.php'); ?>

<main class="main m-4" id="main">
    <section id="hero" class="hero section p-4" style="height: auto;">
            <h2 class="text-center">Administrar Usuarios</h2>
            <div class="text-end mb-3">
                <button type="button" id="btn_nuevo_usuario" class="btn btn-success rounded-pill px-4 shadow-sm" style="display: none;">
                    <i class="bi bi-person-add" style="font-size: 1.2rem;"></i> Registrar usuario
                </button>
            </div>
            
            <div class="table-responsive">
                <div class="table-container">
                    <table id="tablaPerfilados" class="table table-striped table-bordered text-center">
                        <thead class="thead-dark">
                            <tr style="text-align: center;">
                                <th style="width: 20%; text-align: center; font-weight: bold; font-size: 14px;">Cedula</th>
                                <th style="text-align: center; font-weight: bold; font-size: 14px;">Nombre y Apellido</th>
                                <th style="text-align: center; font-weight: bold; font-size: 14px;">Cargo</th>
                                <th style="text-align: center; font-weight: bold; font-size: 14px;">Perfil</th>
                                <th style="text-align: center; font-weight: bold; font-size: 14px;">Acciones</th>
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


<script src="assets/js/validaciones/usuarios/usuarios1.js"></script>
<script src="assets/js/validaciones/usuarios/usuarios2.js"></script>

</body>

</html>
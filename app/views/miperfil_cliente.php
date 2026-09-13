<?php include ('assets/comunes/menu_cliente.php'); ?>
<?php require_once('assets/comunes/modalPerfil.php'); ?>

<main class="main m-4 pt-4" id="main">
    <section class="content_data pt-4" id="hero" style="height: auto;">
        <div class="div_data mt-4 card border-0 shadow bg-transparent">
            <div class="card-header bg-secondary py-3">
                <h2 class="mb-0 fs-4 text-center" style="color: white !important;">MIS DATOS PERSONALES</h2>
            </div>
            <div class="card-body p-4 bg-transparent">
                <div>
                    <div>
                        <div class="row mb-3">
                            <div class="col-5 small fw-bold text-label-custom">Nombre:</div>
                            <div class="col-7 fw-semibold text-value-custom" id="d_nombre"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 small fw-bold text-label-custom">Apellido:</div>
                            <div class="col-7 fw-semibold text-value-custom" id="d_apellido"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 small fw-bold text-label-custom">Cédula:</div>
                            <div class="col-7 fw-semibold text-value-custom" id="d_cedula"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 small fw-bold text-label-custom">Teléfono:</div>
                            <div class="col-7 fw-semibold text-value-custom" id="d_telefono"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 small fw-bold text-label-custom">Correo:</div>
                            <div class="col-7 fw-semibold text-value-custom" id="d_correo"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 small fw-bold text-label-custom">Dirección:</div>
                            <div class="col-7 fw-semibold text-value-custom" id="d_direccion"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 small fw-bold text-label-custom">Fecha Nac:</div>
                            <div class="col-7 fw-semibold text-value-custom" id="d_fecha_nacimiento"></div>
                        </div>
                    </div>
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
<script src="assets/js/validaciones/perfil/perfiles.js"></script>
<script src="assets/js/validaciones/perfil/perfil2.js"></script>

</body>

</html>

<style>
    /* Estilos personalizados para garantizar contraste dinámico */
    .text-label-custom {
        color: #6c757d !important;
    }
    .text-value-custom {
        color: #212529 !important;
    }

    [data-theme="dark"] .text-label-custom,
    body.dark-mode .text-label-custom {
        color: #adb5bd !important; /* Gris claro legible para las etiquetas */
    }

    [data-theme="dark"] .text-value-custom,
    body.dark-mode .text-value-custom {
        color: #ffffff !important; /* Blanco puro para los datos del usuario */
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const dropdownToggle = document.querySelector('.dropdown-toggle, [data-bs-toggle="dropdown"]');
    if (dropdownToggle) {
        dropdownToggle.addEventListener('click', function (e) {
            e.preventDefault();
            const parent = this.closest('.dropdown');
            if (parent) {
                parent.classList.toggle('show');
                const menu = parent.querySelector('.dropdown-menu');
                if (menu) {
                    menu.classList.toggle('show');
                }
            }
        });

        document.addEventListener('click', function (e) {
            if (!dropdownToggle.contains(e.target)) {
                const parent = dropdownToggle.closest('.dropdown');
                if (parent) {
                    parent.classList.remove('show');
                    const menu = parent.querySelector('.dropdown-menu');
                    if (menu) {
                        menu.classList.remove('show');
                    }
                }
            }
        });
    }
});
</script>
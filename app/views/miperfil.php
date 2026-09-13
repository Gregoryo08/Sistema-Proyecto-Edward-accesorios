<?php include ('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modalPerfil.php'); ?>

<main class="main m-4 pt-4" id="main">
    <section class="content_data pt-4" id="hero" style="height: auto;">
        <div class="div_data mt-4 card border-0 shadow bg-transparent">
            <div class="card-header bg-secondary  py-3">
                <h2 class=" mb-0 fs-4 text-center" style="color: white;">MIS DATOS PERSONALES</h2>
            </div>
            <div >
                <div>
                    <div >
                        <div class="row mb-3">
                            <div class="col-5 text-secondary small fw-bold"><i class="bi bi-person" style="padding: 0px 20px; font-size: 1.25rem;"></i>Nombre:</div>
                            <div class="col-7 fw-semibold text-body" id="d_nombre"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 text-secondary small fw-bold"><i class="bi bi-person" style="padding: 0px 20px; font-size: 1.25rem;"></i>Apellido:</div>
                            <div class="col-7 fw-semibold text-body" id="d_apellido"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 text-secondary small fw-bold"><i class="bi bi-credit-card" style="padding: 0px 20px; font-size: 1.25rem;"></i>Cédula:</div>
                            <div class="col-7 fw-semibold text-body" id="d_cedula"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 text-secondary small fw-bold"><i class="bi bi-telephone" style="padding: 0px 20px; font-size: 1.25rem;"></i>Teléfono:</div>
                            <div class="col-7 fw-semibold text-body" id="d_telefono"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 text-secondary small fw-bold"><i class="bi bi-envelope-at" style="padding: 0px 20px; font-size: 1.25rem;"></i>Correo:</div>
                            <div class="col-7 fw-semibold text-body" id="d_correo"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 text-secondary small fw-bold"><i class="bi bi-geo-alt" style="padding: 0px 20px; font-size: 1.25rem;"></i>Dirección:</div>
                            <div class="col-7 fw-semibold text-body" id="d_direccion"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 text-secondary small fw-bold"><i class="bi bi-calendar" style="padding: 0px 20px; font-size: 1.25rem;"></i>Fecha Nac:</div>
                            <div class="col-7 fw-semibold text-body" id="d_fecha_nacimiento"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
    body.dark-mode .div_data.card,
body.dark-mode .card {
    background-color: #1a1a1a !important;
    border: 1px solid #333 !important;
}

body.dark-mode .card-body {
    background-color: #1a1a1a !important;
}

body.dark-mode .card .text-secondary {
    color: #9ca3af !important;
}

body.dark-mode .card .text-body,
body.dark-mode .card div:not(.card-header),
body.dark-mode .card span,
body.dark-mode .card i {
    color: #f8f9fa !important;
}
</style>


<?php require_once('assets/comunes/footer.php'); ?>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

   <script src="assets/Library/JQuery/jquery-3.7.0.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/validaciones/perfil/perfiles.js"></script>


</body>

</html>
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
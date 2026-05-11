<?php
require_once('assets/comunes/menu.php');
require_once('assets/comunes/modal_financiamiento.php');
?>
<main class="main" id="main">
    <section id="hero" class="hero section" style="height: auto;">
        <div class="container">
            <h2 class="text-center mb-4">Administrar Financiamiento</h2>
            
            <div class="text-right mb-3">
                <button type="button" id="btn_nuevo_financiamiento" class="btn btn-success rounded-pill px-4 shadow-sm" style="display: none;">
                    <i class="bi bi-plus-circle me-1"></i> Registrar Financiamiento
                </button>
            </div>

            <div class="table-responsive">
                <div class="table-container">
                    <table id="financiamientotabla" class="table table-striped table-bordered text-center w-100">
                        <thead class="thead-dark">
                            <tr>
                                <th style="display: none;">Id</th>
                                <th>Cliente</th>
                                <th>Teléfono (IMEI)</th>
                                <th>Monto Total</th>
                                <th>Cuotas</th>
                                <th>Próximo Pago</th>
                                <th>Estado Equipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once('assets/comunes/footer.php'); ?>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
</a>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/validaciones/financiamiento/financiamiento_componente.js"></script>
<script src="assets/js/validaciones/financiamiento/financiamiento.js"></script>
<script src="assets/js/validaciones/financiamiento/financiamiento2.js"></script>


</body>
</html>
<?php require_once('assets/comunes/menu.php'); ?>

<main class="main mt-4" id="main">
    <section id="hero" class="hero section" style="height: auto;">
        <div class="container">
            <h2 class="text-center mb-4">Notificaciones del Sistema</h2>

            <div class="d-flex align-items-center mb-3 p-2 bg-light border rounded">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="toggleHistorial" style="cursor: pointer;">
                    <label class="form-check-label fw-bold" for="toggleHistorial">
                        <i class="bi bi-clock-history"></i> Ver historial de notificaciones leídas
                    </label>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tablaNotificaciones" class="table table-striped table-bordered text-center align-items-center w-100">
                    <thead class="thead-dark">
                        <tr>
                            <th style="display: none;">ID</th>
                            <th style="width: 45%; text-align: center; font-weight: bold; font-size: 14px;">Mensaje</th>
                            <th style="width: 15%; text-align: center; font-weight: bold; font-size: 14px;">Tipo</th>
                            <th style="width: 20%; text-align: center; font-weight: bold; font-size: 14px;">Fecha</th>
                            <th style="width: 30%; text-align: center; font-weight: bold; font-size: 14px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody >
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<style>
    body.dark-mode .btn-info {
    background-color: #0dcaf0 !important; 
    border-color: #0dcaf0 !important;
    color: #000 !important; 
}


body.dark-mode .table .btn-info {
    background-color: transparent !important;
    color: #38bdf8 !important;
    border: 1px solid #38bdf8 !important;
}

body.dark-mode .table .btn-info:hover {
    background-color: #38bdf8 !important;
    color: #0f172a !important;
}
</style>

<?php require_once('assets/comunes/footer.php'); ?>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
<script src="assets/js/notificacion.js"></script>

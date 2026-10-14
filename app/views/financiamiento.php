<?php
require_once('assets/comunes/menu.php');
require_once('assets/comunes/modal_financiamiento.php');
?>

<main class="main" id="main" style="margin-top: 70px;">
    <section class="dashboard-section py-4">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-semibold text-dark m-0" style="letter-spacing: -0.5px; font-size: 1.75rem;">Financiamientos</h2>
                    <p class="text-muted small m-0 mt-1">Gestiona los contratos, estados y cuotas activas</p>
                </div>
                <div>
                    <button type="button" id="btn_nuevo_financiamiento" class="btn btn-primary rounded-pill px-4 shadow-sm" style="display: none;">
                        <i class="bi bi-plus-lg me-1"></i> Nuevo Financiamiento
                    </button>
                </div>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table id="financiamientotabla" class="table w-100 align-middle">
                        <thead>
                            <tr>
                                <th style="display: none;">Id</th>
                                <th>Cliente</th>
                                <th>Teléfono (IMEI)</th>
                                <th>Monto Total</th>
                                <th>Por Pagar</th>
                                <th>Cuotas</th>
                                <th>Próximo Pago</th>
                                <th>Estado Equipo</th>
                                <th>Estado Contrato</th>
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

<style>
.table-container {
    background: #ffffff;
    border-radius: 20px;
    padding: 24px 28px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    border: 1px solid #eaeaea;
}

table#financiamientotabla {
    width: 100% !important;
    border-collapse: separate;
    border-spacing: 0;
}

.table-responsive {
    overflow-x: auto !important;
}

#financiamientotabla thead th {
    font-size: 0.72rem !important;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #8e8e93 !important;
    font-weight: 600 !important;
    border-bottom: 1px solid #f0f0f2 !important;
    padding: 14px 12px !important;
    background: transparent !important;
}

#financiamientotabla tbody td {
    padding: 16px 12px !important;
    border-bottom: 1px solid #f5f5f7 !important;
    font-size: 0.875rem;
}

#financiamientotabla tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.01) !important;
}

.dataTables_wrapper {
    padding-top: 4px;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 20px !important;
    color: #6e6e73 !important;
    font-size: 0.85rem;
}

.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #e5e5ea !important;
    border-radius: 12px !important;
    padding: 6px 14px !important;
    background-color: #fbfbfd !important;
    outline: none !important;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #0071e3 !important;
    background-color: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.12) !important;
}

.dataTables_wrapper .dataTables_length select {
    border: 1px solid #e5e5ea !important;
    border-radius: 10px !important;
    padding: 4px 8px !important;
    background-color: #fbfbfd !important;
    outline: none !important;
}

.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    margin-top: 20px !important;
    color: #8e8e93 !important;
    font-size: 0.825rem;
}

#financiamientotabla .badge, 
#financiamientotabla span[class*="bg-"] {
    font-weight: 500 !important;
    padding: 6px 14px !important;
    border-radius: 30px !important;
    font-size: 0.75rem !important;
    box-shadow: none !important;
}

#financiamientotabla .bg-success, 
#financiamientotabla .btn-success {
    background-color: #e6f4ea !important;
    color: #1e7e34 !important;
    border: none !important;
}

#financiamientotabla .bg-primary, 
#financiamientotabla .btn-primary {
    background-color: #e8f0fe !important;
    color: #1a73e8 !important;
    border: none !important;
}

.badge-cuotas {
    background-color: #f2f2f7 !important;
    color: #48484a !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="assets/js/validaciones/financiamiento/financiamiento_componente.js"></script>
<script src="assets/js/validaciones/financiamiento/financiamiento.js"></script>
<script src="assets/js/validaciones/financiamiento/financiamiento2.js"></script>
</body>
</html>
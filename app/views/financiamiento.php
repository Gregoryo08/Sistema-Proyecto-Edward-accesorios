<?php
require_once('assets/comunes/menu.php');
require_once('assets/comunes/modal_financiamiento.php');
?>

<main class="main" id="main">
    <section id="hero" class="hero section" style="height: auto;">
        <div class="container">
            <h2 class="text-center mb-4">Administrar Financiamiento</h2>

            <div class="text-end mb-3">
                <button type="button" id="btn_nuevo_financiamiento" class="btn btn-success rounded-pill px-4 shadow-sm" style="display: none;">
                    <i class="bi bi-plus-circle me-1"></i> Registrar Financiamiento
                </button>
            </div>

            <div class="table-responsive">
                <div class="table-container">
                    <table id="financiamientotabla" class="table w-100">
                        <thead style="background: transparent !important;">
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

  
/* Forzar color blanco usando el ID exacto del elemento */
body.dark-mode #financiamientotabla_info,
body.dark-mode #financiamientotabla_paginate,
body.dark-mode #financiamientotabla_length,
body.dark-mode #financiamientotabla_filter {
    color: #ffffff !important;
}

/* Asegurar visibilidad de etiquetas y texto dentro de la tabla */
body.dark-mode .table, 
body.dark-mode .table td, 
body.dark-mode .table th {
    color: #ffffff !important;
}

/* Ajuste de los botones de paginación que a veces heredan colores oscuros */
body.dark-mode .dt-paging-button {
    color: #ffffff !important;
}


/* 1. MODO OSCURO GLOBAL - FORZADO */
body.dark-mode .dataTables_wrapper .dataTables_info,
body.dark-mode .dataTables_wrapper .dataTables_length,
body.dark-mode .dataTables_wrapper .dataTables_filter,
body.dark-mode .dataTables_wrapper .dataTables_paginate,
body.dark-mode .dataTables_wrapper .dataTables_paginate .paginate_button {
    color: #ffffff !important;
}

/* 2. Paginación en Modo Oscuro */
body.dark-mode .page-link {
    background-color: #1a1a1a !important;
    border-color: #333 !important;
    color: #ffffff !important;
}

body.dark-mode .page-item.active .page-link {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
    color: #ffffff !important;
}

/* 3. Estilos de tabla y filas */
body.dark-mode table.table {
    background-color: #1a1a1a !important;
    color: #e0e0e0 !important;
}

body.dark-mode .table > :not(caption) > * > * {
    background-color: transparent !important;
    color: #e0e0e0 !important;
    border-color: #333 !important;
}

/* 4. Inputs y Selects */
body.dark-mode .dataTables_wrapper .dataTables_filter input,
body.dark-mode .dataTables_wrapper .dataTables_length select {
    background-color: #1a1a1a !important;
    color: #ffffff !important;
    border: 1px solid #444 !important;
}

/* 5. Estilo Badge Cuotas (Modo Oscuro) */
body.dark-mode .badge-cuotas {
    background-color: #2c3e50 !important;
    color: #bdc3c7 !important;
}

/* MODO CLARO (Sobrescritura básica) */
body:not(.dark-mode) .badge-cuotas {
    background-color: #e9ecef !important;
    color: #495057 !important;
}

/* Ajustes generales */
.table-responsive { overflow-x: hidden !important; }
table#financiamientotabla { width: 100% !important; }
</style>








<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="assets/js/validaciones/financiamiento/financiamiento_componente.js"></script>
<script src="assets/js/validaciones/financiamiento/financiamiento.js"></script>
<script src="assets/js/validaciones/financiamiento/financiamiento2.js"></script>


</body>

</html>
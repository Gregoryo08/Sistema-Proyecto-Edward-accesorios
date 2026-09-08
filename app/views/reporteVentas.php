<?php require_once('assets/comunes/menu.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="main-wrapper mb-4">
    <div class="container-fluid pt-3">
        
       
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4" style="margin-top: 70px !important;">
            <div class="row g-3 align-items-center">
            
                <div class="col-12 col-md-4 col-xl-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Buscar por Cliente, Cédula, ID...">
                    </div>
                </div>
                
               
                <div class="col-12 col-md-5 col-xl-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small fw-bold text-nowrap"><i class="fa-regular fa-calendar-days me-1"></i>Período</span>
                        <input type="date" id="dateInicioInput" class="form-control form-control-sm" title="Fecha Inicio">
                        <span class="text-muted small">al</span>
                        <input type="date" id="dateFinInput" class="form-control form-control-sm" title="Fecha Fin">
                    </div>
                </div>

                <!-- Origen de Venta -->
                <div class="col-12 col-sm-6 col-md-3 col-xl-2">
                    <div class="d-flex align-items-center gap-2">
                        <label for="origenContainer" class="text-muted small fw-bold text-nowrap mb-0">Origen</label>
                        <select class="form-select text-capitalize" id="origenContainer">
                            <!-- Opciones dinámicas -->
                        </select>
                    </div>
                </div>

                <!-- Estado de la Venta -->
                <div class="col-12 col-sm-6 col-md-3 col-xl-3">
                    <div class="d-flex align-items-center gap-2">
                        <label for="categoriesContainer" class="text-muted small fw-bold text-nowrap mb-0">Estado</label>
                        <select class="form-select text-capitalize" id="categoriesContainer">
                            <!-- Opciones dinámicas -->
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- TARJETAS -->
        <div class="row g-3 mb-4">
            <!-- Ingresos Totales -->
            <div class="col-12 col-sm-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-primary border-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Monto Total Filtrado</span>
                            <h3 class="fw-bold mb-0 text-dark" id="kpiMontoTotal">$0.00</h3>
                        </div>
                        <div class="bg-primary-subtle text-primary rounded-circle p-3 fs-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ventas Realizadas -->
            <div class="col-12 col-sm-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-success border-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Ventas Procesadas</span>
                            <h3 class="fw-bold mb-0 text-dark" id="kpiTotalVentas">0</h3>
                        </div>
                        <div class="bg-success-subtle text-success rounded-circle p-3 fs-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ventas Anuladas -->
            <div class="col-12 col-sm-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-danger border-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Ventas Anuladas</span>
                            <h3 class="fw-bold mb-0 text-dark" id="kpiVentasAnuladas">0</h3>
                        </div>
                        <div class="bg-danger-subtle text-danger rounded-circle p-3 fs-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRÁFICOS -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-secondary mb-0"><i class="fa-solid fa-chart-bar me-2"></i>Ingresos por Fecha</h6>
                        <span class="badge bg-light text-muted border">Dinámico</span>
                    </div>
                    <div style="position: relative; height:250px; width:100%;">
                        <canvas id="chartTendenciaVentas"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-secondary mb-0"><i class="fa-solid fa-chart-pie me-2"></i>Ventas por Origen</h6>
                    </div>
                    <div style="position: relative; height:250px; width:100%;" class="d-flex align-items-center justify-content-center">
                        <canvas id="chartOrigenVentas"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLA DE DETALLES -->
        <div class="card border-0 shadow-sm rounded-4 p-0 overflow-hidden">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold text-secondary"><i class="fa-solid fa-list me-2"></i>Registros Detallados</h6>
            </div>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0 text-center w-100">
                    <thead class="table-dark sticky-top" style="z-index: 1;">
                        <tr>
                            <th style="width: 10%;">N° Venta</th>
                            <th style="width: 15%;">Fecha / Hora</th>
                            <th style="width: 15%;">Origen Venta</th>
                            <th style="width: 15%;">Cliente</th>
                            <th style="width: 15%;">Operador</th>
                            <th style="width: 10%;">Monto Total</th>
                            <th style="width: 10%;">Estado</th>
                            <th style="width: 10%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablareporteVentas">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- BOTÓN DE ACCIONES -->
        <div class="d-flex justify-content-end mt-3 gap-2">
            <button type="button" class="btn btn-danger rounded-3" id="btnDescargarPDF">
                <i class="fa-solid fa-file-pdf me-1"></i> Descargar Reporte PDF
            </button>
        </div>

    </div>
    <?php require_once('assets/comunes/modalDetalleVenta.php'); ?>
</main>

<script src="assets/js/validaciones/reportes/reporteVentas.js"></script>


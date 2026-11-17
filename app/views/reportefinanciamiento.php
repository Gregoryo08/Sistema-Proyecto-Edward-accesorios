<?php require_once('assets/comunes/menu.php'); ?>

<main class="main-wrapper" style="padding-top: 92px;">
    <div class="container-fluid pt-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Reporte de Financiamiento</h3>
                <button type="button" class="btn btn-danger" id="btnDescargarReporteFinanciamiento">
                    <i class="fa-solid fa-file-pdf me-1"></i> PDF
                </button>
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="reporteBuscar" class="form-label">Buscar</label>
                    <input id="reporteBuscar" class="form-control" placeholder="Cédula o cliente">
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="reporteEstado" class="form-label">Estado</label>
                    <select id="reporteEstado" class="form-select">
                        <option value="">Todos</option>
                        <option value="vigente">Vigente</option>
                        <option value="finalizado">Finalizado</option>
                        <option value="anulado">Anulado</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label for="reporteDesde" class="form-label">Desde</label>
                    <input type="date" id="reporteDesde" class="form-control">
                </div>
                <div class="col-6 col-md-2">
                    <label for="reporteHasta" class="form-label">Hasta</label>
                    <input type="date" id="reporteHasta" class="form-control">
                </div>
                <div class="col-12 col-md-2">
                    <label for="reporteOrden" class="form-label">Ordenar</label>
                    <select id="reporteOrden" class="form-select">
                        <option value="cantidad">Más financiamientos</option>
                        <option value="monto">Mayor monto</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label for="reporteMontoMin" class="form-label">Monto mínimo</label>
                    <input type="number" id="reporteMontoMin" class="form-control" min="0" step="0.01" placeholder="0.00">
                </div>
                <div class="col-12 col-md-3">
                    <label for="reporteMontoMax" class="form-label">Monto máximo</label>
                    <input type="number" id="reporteMontoMax" class="form-control" min="0" step="0.01" placeholder="0.00">
                </div>
                <div class="col-12 col-md-6 d-flex align-items-end">
                    <button type="button" class="btn btn-primary me-2" id="btnAplicarFiltrosFinanciamiento">
                        <i class="fa-solid fa-filter me-1"></i> Aplicar filtros
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btnLimpiarFiltrosFinanciamiento">
                        Limpiar
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-start border-primary border-4 shadow-sm p-3">
                    <span class="text-muted">Financiamientos</span>
                    <strong class="fs-3" id="kpiFinanciamientos">0</strong>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-start border-success border-4 shadow-sm p-3">
                    <span class="text-muted">Monto total</span>
                    <strong class="fs-3" id="kpiMontoFinanciamiento">$0.00</strong>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-start border-warning border-4 shadow-sm p-3">
                    <span class="text-muted">Vigentes</span>
                    <strong class="fs-3" id="kpiVigentes">0</strong>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-5">
                <div class="card shadow-sm border-0 p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="mb-1">Estado de financiamientos</h5>
                            <small class="text-muted">Distribución del período filtrado</small>
                        </div>
                        <span class="badge rounded-pill text-bg-light" id="totalEstadosFinanciamiento">0 financiamientos</span>
                    </div>
                    <div style="height: 280px;">
                        <canvas id="graficoEstadosFinanciamiento"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-7">
                <div class="card shadow-sm border-0 p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="mb-1">Cuotas por mes</h5>
                            <small class="text-muted">Cantidad acumulada por periodo</small>
                        </div>
                        <span class="badge rounded-pill text-bg-success" id="totalMesesFinanciamiento">0 cuotas</span>
                    </div>
                    <div style="height: 280px;">
                        <canvas id="graficoFechasFinanciamiento"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle text-center mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Cédula</th>
                            <th>Cliente</th>
                            <th>Financiamientos</th>
                            <th>Monto acumulado</th>
                            <th>Productos</th>
                        </tr>
                    </thead>
                    <tbody id="tablaReporteFinanciamiento"></tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once('assets/comunes/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/validaciones/reportes/reporteFinanciamiento.js"></script>
</body>
</html>
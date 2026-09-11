<?php require_once('assets/comunes/menu.php'); ?>

<main class="main-wrapper" style="padding-top: 92px;">
<div class="container-fluid pt-3">
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Reporte de Servicio Técnico</h3>
            <button type="button" class="btn btn-danger" id="btnDescargarReporteServicio"><i class="fa-solid fa-file-pdf me-1"></i> PDF</button>
        </div>
        <div class="row g-3">
            <div class="col-12 col-md-4"><label for="reporteBuscar" class="form-label">Buscar</label><input id="reporteBuscar" class="form-control" placeholder="cédula, cliente, equipo o falla"></div>
            <div class="col-12 col-sm-6 col-md-2"><label for="reporteEstado" class="form-label">Estado</label><select id="reporteEstado" class="form-select"><option value="">Todos</option><option>Pendiente</option><option>Reparado</option><option>Entregado</option><option>Cobrado</option></select></div>
            <div class="col-12 col-sm-6 col-md-2"><label for="reporteEspecialidad" class="form-label">Especialidad</label><select id="reporteEspecialidad" class="form-select"><option value="">Todas</option></select></div>
            <div class="col-6 col-md-2"><label for="reporteDesde" class="form-label">Desde</label><input type="date" id="reporteDesde" class="form-control"></div>
            <div class="col-6 col-md-2"><label for="reporteHasta" class="form-label">Hasta</label><input type="date" id="reporteHasta" class="form-control"></div>
            <div class="col-12 col-md-4 d-flex align-items-end"><button type="button" class="btn btn-primary" id="btnAplicarFiltrosServicio"><i class="fa-solid fa-filter me-1"></i> Aplicar filtros</button><button type="button" class="btn btn-outline-secondary ms-2" id="btnLimpiarFiltrosServicio">Limpiar</button></div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4"><div class="card border-start border-primary border-4 shadow-sm p-3"><span class="text-muted">Servicios encontrados</span><strong class="fs-3" id="kpiServicios">0</strong></div></div>
        <div class="col-12 col-md-4"><div class="card border-start border-success border-4 shadow-sm p-3"><span class="text-muted">Monto total</span><strong class="fs-3" id="kpiMontoServicio">$0.00</strong></div></div>
        <div class="col-12 col-md-4"><div class="card border-start border-warning border-4 shadow-sm p-3"><span class="text-muted">Servicios pendientes</span><strong class="fs-3" id="kpiPendientes">0</strong></div></div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-5"><div class="card shadow-sm border-0 p-3 h-100"><div class="d-flex justify-content-between align-items-start mb-2"><div><h5 class="mb-1">Servicios por estado</h5><small class="text-muted">Distribución del período filtrado</small></div><span class="badge rounded-pill text-bg-light" id="totalEstadosServicio">0 servicios</span></div><div style="height:280px"><canvas id="graficoEstadosServicio"></canvas></div></div></div>
        <div class="col-12 col-lg-7"><div class="card shadow-sm border-0 p-3 h-100"><div class="d-flex justify-content-between align-items-start mb-2"><div><h5 class="mb-1">Servicios e ingresos por mes</h5><small class="text-muted">Monto acumulado y cantidad de servicios</small></div><span class="badge rounded-pill text-bg-success" id="totalMesesServicio">$0.00 · 0 servicios</span></div><div style="height:280px"><canvas id="graficoFechasServicio"></canvas></div></div></div>
    </div>

    <div class="card shadow-sm border-0"><div class="table-responsive"><table class="table table-striped table-hover align-middle text-center mb-0"><thead class="table-dark"><tr><th>Fecha</th><th>Cliente</th><th>Equipo</th><th>Falla</th><th>Especialidad</th><th>Estado</th><th>Monto</th></tr></thead><tbody id="tablaReporteServicio"></tbody></table></div></div>
</div>
</main>

<?php require_once('assets/comunes/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/validaciones/reportes/reporteServicioTecnico.js"></script>
</body>
</html>

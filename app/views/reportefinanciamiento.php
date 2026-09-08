<?php require_once("assets/comunes/menu.php"); ?>

<body>
<div style="margin-top: 100px;">

    <!-- SECCIÓN DE FILTROS Y GRÁFICOS -->
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header text-white ">
                    <h3 class="text-center font-weight-light my-2">Filtros de Reportes Financieros</h3>
                </div>
                <div class="card-body">
                    <form id="form_filtros_graficos">
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3">
                                <label for="filtro_cedula" class="form-label">Cédula Cliente</label>
                                <input class="form-control" type="text" id="filtro_cedula" name="cedula" placeholder="Ingrese cédula">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="filtro_estado" class="form-label">Estado Financiamiento</label>
                                <select class="form-select" id="filtro_estado" name="estado">
                                    <option value="">Todos los estados</option>
                                    <option value="vigente">Vigente</option>
                                    <option value="finalizado">Finalizado</option>
                                    <option value="anulado">Anulado</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="filtro_orden" class="form-label">Ordenar Por</label>
                                <select class="form-select" id="filtro_orden" name="ordenar_por">
                                    <option value="cantidad">Más Financiamientos</option>
                                    <option value="monto">Mayor Monto</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="filtro_desde" class="form-label">Fecha Desde</label>
                                <input class="form-control" type="date" id="filtro_desde" name="fecha_desde">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="filtro_hasta" class="form-label">Fecha Hasta</label>
                                <input class="form-control" type="date" id="filtro_hasta" name="fecha_hasta">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="filtro_monto_min" class="form-label">Monto Mínimo ($)</label>
                                <input class="form-control" type="number" step="0.01" id="filtro_monto_min" name="monto_min" placeholder="0.00">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="filtro_monto_max" class="form-label">Monto Máximo ($)</label>
                                <input class="form-control" type="number" step="0.01" id="filtro_monto_max" name="monto_max" placeholder="0.00">
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            <button type="button" class="btn btn-warning" id="aplicarFiltrosGraficos">
                                <i class="fa-solid fa-chart-line"></i> Actualizar Gráficos y Ranking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN DE GRÁFICOS -->
    <div class="row justify-content-center my-4" id="main">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header text-white ">
                    <h3 class="text-center font-weight my-2">Estado Financiamientos</h3>
                </div>
                <div class="card-body">
                    <div id="graficoPastelFinanciamiento" style="height: 400px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header text-white ">
                    <h3 class="text-center font-weight-light my-2">Cuotas por Mes</h3>
                </div>
                <div class="card-body">
                    <div id="graficoBarraFinanciamiento" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN DE REPORTE PDF DETALLADO -->
    <div class="row justify-content-center my-4">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header text-white">
                    <h3 class="text-center font-weight-light my-2">Generar Reporte Detallado</h3>
                </div>
                <div class="card-body">
                    <form id="form_generar_pdf" method="POST" action="?pagina=reporteFinanciamiento" target="_blank">
                        <div class="row mb-3">
                            <div class="col-md-3 mb-3">
                                <label for="cedula_pdf" class="form-label">Cédula Cliente</label>
                                <input class="form-control" type="text" id="cedula_pdf" name="cedula" placeholder="Cédula">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="estado_pdf" class="form-label">Estado</label>
                                <select class="form-select" id="estado_pdf" name="estado">
                                    <option value="">Seleccione...</option>
                                    <option value="vigente">Vigente</option>
                                    <option value="finalizado">Finalizado</option>
                                    <option value="anulado">Anulado</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="desde_pdf" class="form-label">Fecha Desde</label>
                                <input class="form-control" type="date" id="desde_pdf" name="fecha_desde">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="hasta_pdf" class="form-label">Fecha Hasta</label>
                                <input class="form-control" type="date" id="hasta_pdf" name="fecha_hasta">
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            <button type="submit" name="generar2" class="btn btn-dark text-warning">
                                <i class="fa-solid fa-file-pdf"></i> Generar PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('assets/comunes/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="assets/js/validaciones/reportes/reporteFinanciamiento.js"></script>
</body>
</html>
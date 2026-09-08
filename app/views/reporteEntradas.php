<?php require_once("assets/comunes/menu.php"); ?>

<body>

<div class="container-fluid">

    <div class="row justify-content-center" style="margin-top: 20px;">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header text-white" style="background-color: black;">
                    <h3 class="text-center font-weight-light" style="font-weight: bold; margin: 10px 0;">Filtros de Reportes de Entradas</h3>
                </div>
                <div class="card-body">
                    <form id="form_filtros_entradas">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="filtro_fecha_inicio" class="form-label">Fecha Inicio</label>
                                <input class="form-control" type="date" id="filtro_fecha_inicio" name="fecha_inicio">
                            </div>
                            <div class="col-md-6">
                                <label for="filtro_fecha_fin" class="form-label">Fecha Fin</label>
                                <input class="form-control" type="date" id="filtro_fecha_fin" name="fecha_fin">
                            </div>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <button type="button" class="btn btn-custom" id="aplicarFiltrosEntradas">
                                <i class="fa-solid fa-chart-line"></i> Actualizar Gráficos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center" id="main" style="margin: 0;">
        
        <div class="col-md-5" id="hero" style="height: auto;">
            <div class="card shadow-lg border-0 rounded-lg" style="margin: 20px 0 ;">
                <div class="card-header text-white" style="background-color: black;">
                    <h3 class="text-center font-weight-light" style="font-weight: bold; margin: 10px 0;">Entradas por Categoría</h3>
                </div>
                <div class="card-body">
                    <div id="graficoPastelCategorias" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-lg" style="margin: 20px 0 ;">
                <div class="card-header text-white" style="background-color: black;">
                    <h3 class="text-center font-weight-light" style="font-weight: bold; margin: 10px 0;">Top 10 Productos</h3>
                </div>
                <div class="card-body">
                    <div id="graficoBarraProductos" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                </div>
            </div>
        </div>

    </div>

         <div class="row justify-content-center" style="margin: 20px 0;">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header text-white" style="background-color: black;">
                    <h3 class="text-center font-weight-light" style="font-weight: bold; margin: 10px 0;">Generar Reporte Detallado</h3>
                </div>
                <div class="card-body">
                    <form id="form_generar_pdf_entradas" method="POST" action="?pagina=reporteEntradas" target="_blank">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Fecha (Opcional)</label>
                                <input class="form-control" type="date" name="fecha" id="pdf_fecha">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Producto (Opcional)</label>
                                <select class="form-select" name="producto" id="pdf_producto">
                                    <option value="">Todos los productos</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            <button type="submit" name="generar_pdf_entradas" class="btn" style="background-color: black; color:yellow;">
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

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/Library/Highcharts/code/highcharts.js"></script>
<script src="assets/Library/Highcharts/code/modules/exporting.js"></script>
<script src="assets/Library/Highcharts/code/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script src="assets/js/validaciones/reportes/reporteEntradas.js"></script>
</body>
</html>
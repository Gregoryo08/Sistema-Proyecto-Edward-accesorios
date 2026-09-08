$(document).ready(function () {

    function showLoadingOverlay(containerId) {
        const container = $(`#${containerId}`);
        if(container.find('.loading-overlay').length === 0) {
            container.append(`
                <div class="loading-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.8); display: flex; justify-content: center; align-items: center; z-index: 1000;">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            `);
            container.css('position', 'relative');
        }
    }

    function hideLoadingOverlay(containerId) {
        $(`#${containerId} .loading-overlay`).remove();
    }

    function renderCharts(data) {
        if (typeof Highcharts === 'undefined') {
            return;
        }

        Highcharts.chart("graficoBarraFinanciamiento", {
            chart: { type: "column" },
            title: { text: "Cuotas por Mes" },
            xAxis: { categories: (data.cuotas || []).map(item => item.mes) },
            yAxis: { title: { text: "Cantidad" } },
            series: [{ 
                name: "Cuotas", 
                data: (data.cuotas || []).map(item => parseFloat(item.total_cuotas)), 
                color: "#FFD700" 
            }],
            credits: { enabled: false }
        });

        Highcharts.chart("graficoPastelFinanciamiento", {
            chart: { type: "pie", options3d: { enabled: true, alpha: 45 } },
            title: { text: "Estado de Financiamientos" },
            series: [{
                name: "Cantidad",
                colorByPoint: true,
                data: (data.estados || []).map(item => ({ name: item.estado, y: parseFloat(item.total) }))
            }],
            credits: { enabled: false }
        });
    }

    function fetchAndRenderCharts(filters = {}) {
        showLoadingOverlay("graficoBarraFinanciamiento");
        showLoadingOverlay("graficoPastelFinanciamiento");

        const urlParams = new URLSearchParams();
        urlParams.append('accion', 'obtenerDatosGraficos');
        if (filters.cedula) urlParams.append('cedula', filters.cedula);
        if (filters.estado) urlParams.append('estado', filters.estado);
        if (filters.fecha_desde) urlParams.append('fecha_desde', filters.fecha_desde);
        if (filters.fecha_hasta) urlParams.append('fecha_hasta', filters.fecha_hasta);
        if (filters.monto_min && parseFloat(filters.monto_min) > 0) urlParams.append('monto_min', filters.monto_min);
        if (filters.monto_max && parseFloat(filters.monto_max) > 0) urlParams.append('monto_max', filters.monto_max);
        if (filters.ordenar_por) urlParams.append('ordenar_por', filters.ordenar_por);

        fetch(`?pagina=reporteFinanciamiento&ajax=true&${urlParams.toString()}`)
            .then(response => response.json())
            .then(data => {
                hideLoadingOverlay("graficoBarraFinanciamiento");
                hideLoadingOverlay("graficoPastelFinanciamiento");
                renderCharts(data);
            })
            .catch(error => {
                hideLoadingOverlay("graficoBarraFinanciamiento");
                hideLoadingOverlay("graficoPastelFinanciamiento");
                console.error("Error:", error);
            });
    }

    $('#aplicarFiltrosGraficos').on('click', function () {
        fetchAndRenderCharts({
            cedula: $('#filtro_cedula').val(),
            estado: $('#filtro_estado').val(),
            fecha_desde: $('#filtro_desde').val(),
            fecha_hasta: $('#filtro_hasta').val(),
            monto_min: $('#filtro_monto_min').val(),
            monto_max: $('#filtro_monto_max').val(),
            ordenar_por: $('#filtro_orden').val()
        });
    });

    fetchAndRenderCharts({
        fecha_desde: $('#filtro_desde').val(),
        fecha_hasta: $('#filtro_hasta').val()
    });
});
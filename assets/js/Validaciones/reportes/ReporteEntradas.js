$(document).ready(function () {

    function showLoadingOverlay(containerId) {
        const container = $(`#${containerId}`);
        if (container.find('.loading-overlay').length === 0) {
            container.append(`
                <div class="loading-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); display: flex; justify-content: center; align-items: center; z-index: 10;">
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
            console.error("Highcharts no está cargado aún.");
            return;
        }

        const totalEntradas = data.productos.reduce((sum, item) => sum + parseFloat(item.total), 0);

        Highcharts.chart("graficoPastelCategorias", {
            chart: { type: "pie", options3d: { enabled: true, alpha: 45 } },
            title: { text: "Entradas por Categoría" },
            plotOptions: {
                pie: {
                    innerSize: 60,
                    depth: 45,
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: { color: '#333', fontSize: '12px' }
                    }
                }
            },
            series: [{
                name: "Cantidad",
                colorByPoint: true,
                data: data.categorias.map((item, index) => {
                    const colors = ['#FFD700', '#FFA500', '#FF6347', '#40E0D0', '#EE82EE', '#9ACD32', '#1E90FF'];
                    return { 
                        name: item.nombre, 
                        y: parseFloat(item.total),
                        color: colors[index % colors.length]
                    };
                })
            }],
            credits: { enabled: false }
        });

        Highcharts.chart("graficoBarraProductos", {
            chart: { type: "bar" },
            title: { text: "Top 10 Productos con Más Entradas" },
            subtitle: {
                text: "Total Entrado: " + totalEntradas.toLocaleString() + " unidades",
                style: { color: '#28a745', fontSize: '14px', fontWeight: 'bold' }
            },
            xAxis: { 
                categories: data.productos.map(item => item.nombre),
                labels: { style: { fontSize: '10px' } }
            },
            yAxis: { 
                title: { text: "Cantidad Entrada" },
                allowDecimals: false
            },
            series: [{ 
                name: "Cantidad", 
                data: data.productos.map(item => parseFloat(item.total)),
                color: "#198754",
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '10px', color: '#198754' },
                    format: '{point.y}'
                }
            }],
            legend: { enabled: false },
            credits: { enabled: false }
        });
    }

    function fetchAndRenderCharts(filters = {}) {
        showLoadingOverlay("graficoPastelCategorias");
        showLoadingOverlay("graficoBarraProductos");

        const urlParams = new URLSearchParams();
        urlParams.append('accion', 'obtenerDatosGraficos');
        if (filters.fecha_inicio) urlParams.append('fecha_inicio', filters.fecha_inicio);
        if (filters.fecha_fin) urlParams.append('fecha_fin', filters.fecha_fin);

        fetch(`?pagina=reporteEntradas&ajax=true&${urlParams.toString()}`)
            .then(response => response.json())
            .then(data => {
                hideLoadingOverlay("graficoPastelCategorias");
                hideLoadingOverlay("graficoBarraProductos");
                renderCharts(data);
            })
            .catch(error => {
                hideLoadingOverlay("graficoPastelCategorias");
                hideLoadingOverlay("graficoBarraProductos");
                console.error("Error:", error);
                alert("Error al cargar los datos de los gráficos");
            });
    }

    $('#aplicarFiltrosEntradas').on('click', function () {
        fetchAndRenderCharts({
            fecha_inicio: $('#filtro_fecha_inicio').val(),
            fecha_fin: $('#filtro_fecha_fin').val()
        });
    });

    fetchAndRenderCharts({});

    function cargarProductosPDF() {
        $.get("?pagina=entradas_productos&ajax=true&x=productos", function(res) {
            let productos = typeof res === 'string' ? JSON.parse(res) : res;
            let select = $("#pdf_producto");
            select.empty().append('<option value="">Todos los productos</option>');
            
            if (Array.isArray(productos)) {
                productos.forEach(p => {
                    select.append(`<option value="${p.id_producto}">${p.nombre_producto}</option>`);
                });
            }
        });
    }

    $('#aplicarFiltrosEntradas').on('click', function () {
        var fechaInicio = $('#filtro_fecha_inicio').val();
        var fechaFin = $('#filtro_fecha_fin').val();
        
        if (fechaInicio || fechaFin) {
            $('#pdf_fecha').val(fechaInicio || fechaFin);
        }
    });

    $('#filtro_fecha_inicio, #filtro_fecha_fin').on('change', function () {
        var fecha = $(this).val();
        if (fecha) {
            $('#pdf_fecha').val(fecha);
        }
    });

    cargarProductosPDF();
});
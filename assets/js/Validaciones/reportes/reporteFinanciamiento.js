$(document).ready(function () {
    let graficoEstados;
    let graficoMeses;
    const coloresEstados = ['#2563eb', '#f59e0b', '#16a34a', '#dc2626', '#7c3aed', '#0891b2', '#64748b'];
    const formatoMoneda = new Intl.NumberFormat('es-VE', { style: 'currency', currency: 'USD' });
    const nombresMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    function filtros() {
        return {
            cedula: $('#reporteBuscar').val().trim(),
            estado: $('#reporteEstado').val(),
            fecha_desde: $('#reporteDesde').val(),
            fecha_hasta: $('#reporteHasta').val(),
            monto_min: $('#reporteMontoMin').val(),
            monto_max: $('#reporteMontoMax').val(),
            ordenar_por: $('#reporteOrden').val()
        };
    }

    function cargarReporte() {
        const filtrosActuales = filtros();

        $.ajax({
            url: '?pagina=reporteFinanciamiento',
            data: {
                accion: 'obtenerDatosGraficos',
                cedula: filtrosActuales.cedula,
                estado: filtrosActuales.estado,
                fecha_desde: filtrosActuales.fecha_desde,
                fecha_hasta: filtrosActuales.fecha_hasta,
                monto_min: filtrosActuales.monto_min,
                monto_max: filtrosActuales.monto_max,
                ordenar_por: filtrosActuales.ordenar_por
            },
            dataType: 'json',
            success: function (respuesta) {
                pintarReporte(respuesta);
            },
            error: function () {
                pintarReporte({ estados: [], cuotas: [], ranking: [] });
            }
        });
    }

    function pintarReporte(data) {
        let montoTotal = 0;
        let financiamientos = 0;
        let vigentes = 0;
        const estados = {};
        const meses = {};
        let filas = '';

        (data.ranking || []).forEach(function (item) {
            const monto = parseFloat(item.monto_acumulado) || 0;
            const cantidad = parseInt(item.total_financiamientos, 10) || 0;
            montoTotal += monto;
            financiamientos += cantidad;
            if ((item.estado || '').toLowerCase() === 'vigente') vigentes += cantidad;

            filas += `
                <tr>
                    <td>${item.cedula_persona || ''}</td>
                    <td>${item.cliente || ''}</td>
                    <td>${cantidad}</td>
                    <td>${formatoMoneda.format(monto)}</td>
                    <td>${(item.productos || '').slice(0, 60) || 'Sin productos'}</td>
                </tr>
            `;
        });

        (data.estados || []).forEach(function (item) {
            const estado = (item.estado || 'Sin estado').trim();
            estados[estado] = parseInt(item.total, 10) || 0;
        });

        (data.cuotas || []).forEach(function (item) {
            const mes = item.mes || '';
            meses[mes] = parseInt(item.total_cuotas, 10) || 0;
        });

        if (!filas) {
            filas = '<tr><td colspan="5" class="text-center text-muted py-4">No se encontraron financiamientos con los criterios seleccionados.</td></tr>';
        }

        $('#tablaReporteFinanciamiento').html(filas);
        $('#kpiFinanciamientos').text(financiamientos);
        $('#kpiMontoFinanciamiento').text(formatoMoneda.format(montoTotal));
        $('#kpiVigentes').text(vigentes);
        dibujarGraficos(estados, meses, financiamientos, montoTotal);
    }

    function dibujarGraficos(estados, meses, totalFinanciamientos, montoTotal) {
        if (graficoEstados) graficoEstados.destroy();
        if (graficoMeses) graficoMeses.destroy();

        const labelsEstados = Object.keys(estados);
        const dataEstados = Object.values(estados);
        const totalEstado = dataEstados.reduce((sum, value) => sum + value, 0);
        $('#totalEstadosFinanciamiento').text(totalEstado + (totalEstado === 1 ? ' financiamiento' : ' financiamientos'));

        const totalCuotas = Object.values(meses).reduce((sum, value) => sum + value, 0);
        $('#totalMesesFinanciamiento').text(totalCuotas + (totalCuotas === 1 ? ' cuota' : ' cuotas'));

        const centroDoughnut = {
            id: 'centroDoughnutFinanciamiento',
            afterDraw: function (chart) {
                if (!totalEstado) return;
                const context = chart.ctx;
                const centroX = (chart.chartArea.left + chart.chartArea.right) / 2;
                const centroY = (chart.chartArea.top + chart.chartArea.bottom) / 2;
                context.save();
                context.textAlign = 'center';
                context.fillStyle = '#1f2937';
                context.font = '700 26px sans-serif';
                context.fillText(totalEstado, centroX, centroY + 5);
                context.fillStyle = '#64748b';
                context.font = '12px sans-serif';
                context.fillText('financiamientos', centroX, centroY + 24);
                context.restore();
            }
        };

        graficoEstados = new Chart(document.getElementById('graficoEstadosFinanciamiento'), {
            type: 'doughnut',
            data: {
                labels: labelsEstados,
                datasets: [{
                    data: dataEstados,
                    backgroundColor: coloresEstados,
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 8
                }]
            },
            plugins: [centroDoughnut],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16 } },
                    tooltip: { callbacks: { label: function (context) { return ' ' + context.label + ': ' + context.raw + ' (' + ((context.raw / totalEstado) * 100 || 0).toFixed(1) + '%)'; } } }
                }
            }
        });

        const mesesOrdenados = Object.keys(meses).sort();
        const etiquetasMeses = mesesOrdenados.map(function (mes) {
            const partes = mes.split('-');
            if (partes.length === 2) {
                const anio = partes[0];
                const indiceMes = parseInt(partes[1], 10) - 1;
                return (nombresMeses[indiceMes] || mes) + ' ' + anio;
            }
            return mes;
        });

        graficoMeses = new Chart(document.getElementById('graficoFechasFinanciamiento'), {
            type: 'bar',
            data: {
                labels: etiquetasMeses,
                datasets: [{
                    label: 'Cuotas',
                    data: mesesOrdenados.map(function (mes) { return meses[mes]; }),
                    backgroundColor: '#2563eb',
                    borderRadius: 8,
                    maxBarThickness: 46
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'bottom', labels: { usePointStyle: true, padding: 16 } },
                    tooltip: { callbacks: { label: function (context) { return ' ' + context.dataset.label + ': ' + context.raw + ' cuotas'; } } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#64748b' } },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#e5e7eb' },
                        ticks: { color: '#64748b', precision: 0, stepSize: 1 }
                    }
                }
            }
        });
    }

    $('#btnAplicarFiltrosFinanciamiento').on('click', cargarReporte);
    $('#btnLimpiarFiltrosFinanciamiento').on('click', function () {
        $('#reporteBuscar, #reporteDesde, #reporteHasta, #reporteMontoMin, #reporteMontoMax').val('');
        $('#reporteEstado, #reporteOrden').val('');
        cargarReporte();
    });

    $('#reporteBuscar').on('keypress', function (evento) {
        if (evento.key === 'Enter') cargarReporte();
    });

    $('#btnDescargarReporteFinanciamiento').on('click', function () {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '?pagina=reporteFinanciamiento';
        form.target = '_blank';

        const campos = {
            generar2: '1',
            cedula: $('#reporteBuscar').val().trim(),
            estado: $('#reporteEstado').val(),
            fecha_desde: $('#reporteDesde').val(),
            fecha_hasta: $('#reporteHasta').val(),
            monto_min: $('#reporteMontoMin').val(),
            monto_max: $('#reporteMontoMax').val()
        };

        Object.keys(campos).forEach(function (clave) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = clave;
            input.value = campos[clave] || '';
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
        form.remove();
    });

    cargarReporte();
});
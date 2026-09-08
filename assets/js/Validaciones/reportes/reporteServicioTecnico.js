$(document).ready(function () {
    let servicios = [];
    let graficoEstados;
    let graficoFechas;
    const coloresEstados = ['#2563eb', '#f59e0b', '#16a34a', '#dc2626', '#7c3aed', '#0891b2', '#64748b'];
    const formatoMoneda = new Intl.NumberFormat('es-VE', { style: 'currency', currency: 'USD' });
    const nombresMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    function filtros() {
        return {
            buscar: $('#reporteBuscar').val().trim(),
            estado: $('#reporteEstado').val(),
            especialidad: $('#reporteEspecialidad').val(),
            fecha_desde: $('#reporteDesde').val(),
            fecha_hasta: $('#reporteHasta').val()
        };
    }

    function cargarReporte() {
        $.ajax({
            url: '?pagina=reporteservicio_tecnico&accion=listar',
            data: filtros(),
            dataType: 'json',
            success: function (respuesta) {
                servicios = Array.isArray(respuesta.data) ? respuesta.data : [];
                if ($('#reporteEspecialidad option').length === 1) {
                    (respuesta.especialidades || []).forEach(function (especialidad) {
                        $('#reporteEspecialidad').append($('<option>', { value: especialidad.id_especialidad, text: especialidad.nombre_especialidad }));
                    });
                }
                pintarReporte();
            },
            error: function (xhr) {
                servicios = [];
                pintarReporte();
                const mensaje = xhr.responseJSON && xhr.responseJSON.mensaje
                    ? xhr.responseJSON.mensaje
                    : 'No se encontraron servicios técnicos con los criterios seleccionados.';
                $('#tablaReporteServicio').html('<tr><td colspan="8" class="text-center text-muted py-4">' + mensaje + '</td></tr>');
            }
        });
    }

    function pintarReporte() {
        let monto = 0;
        let pendientes = 0;
        const estados = {};
        const meses = {};
        let html = '';

        servicios.forEach(function (servicio) {
            const estado = servicio.estado || 'Sin estado';
            const fecha = (servicio.fecha_registro || '').split(' ')[0] || 'Sin fecha';
            const valor = parseFloat(servicio.monto_total) || 0;
            monto += valor;
            if (estado.toLowerCase() === 'pendiente') pendientes++;
            estados[estado] = (estados[estado] || 0) + 1;
            if (fecha !== 'Sin fecha') {
                const partesFecha = fecha.split('-');
                const mes = partesFecha.length >= 2 ? partesFecha[0] + '-' + partesFecha[1] : fecha;
                if (!meses[mes]) meses[mes] = { monto: 0, servicios: 0 };
                meses[mes].monto += valor;
                meses[mes].servicios++;
            }
            html += `<tr><td>${servicio.id_servicio}</td><td>${fecha}</td><td>${servicio.cliente.trim() || servicio.cedula_persona}</td><td>${servicio.equipo_descripcion || ''}</td><td>${servicio.falla_inicial || ''}</td><td>${servicio.especialidad || ''}</td><td><span class="badge bg-secondary">${estado}</span></td><td>$${valor.toFixed(2)}</td></tr>`;
        });

        if (!html) html = '<tr><td colspan="8" class="text-center text-muted py-4">No se encontraron servicios técnicos con los criterios seleccionados.</td></tr>';
        $('#tablaReporteServicio').html(html);
        $('#kpiServicios').text(servicios.length);
        $('#kpiMontoServicio').text(formatoMoneda.format(monto));
        $('#kpiPendientes').text(pendientes);
        dibujarGraficos(estados, meses, monto);
    }

    function dibujarGraficos(estados, meses, montoTotal) {
        if (graficoEstados) graficoEstados.destroy();
        if (graficoFechas) graficoFechas.destroy();
        const estadosLabels = Object.keys(estados);
        const totalServicios = Object.values(estados).reduce((total, cantidad) => total + cantidad, 0);
        $('#totalEstadosServicio').text(totalServicios + (totalServicios === 1 ? ' servicio' : ' servicios'));
        const serviciosPorMes = Object.values(meses).reduce((total, mes) => total + mes.servicios, 0);
        $('#totalMesesServicio').text(formatoMoneda.format(montoTotal) + ' · ' + serviciosPorMes + (serviciosPorMes === 1 ? ' servicio' : ' servicios'));

        const centroDoughnut = {
            id: 'centroDoughnutServicio',
            afterDraw: function (chart) {
                if (!totalServicios) return;
                const contexto = chart.ctx;
                const centroX = (chart.chartArea.left + chart.chartArea.right) / 2;
                const centroY = (chart.chartArea.top + chart.chartArea.bottom) / 2;
                contexto.save();
                contexto.textAlign = 'center';
                contexto.fillStyle = '#1f2937';
                contexto.font = '700 26px sans-serif';
                contexto.fillText(totalServicios, centroX, centroY + 5);
                contexto.fillStyle = '#64748b';
                contexto.font = '12px sans-serif';
                contexto.fillText('servicios', centroX, centroY + 24);
                contexto.restore();
            }
        };
        graficoEstados = new Chart(document.getElementById('graficoEstadosServicio'), {
            type: 'doughnut',
            data: { labels: estadosLabels, datasets: [{ data: Object.values(estados), backgroundColor: coloresEstados, borderColor: '#ffffff', borderWidth: 3, hoverOffset: 8 }] },
            plugins: [centroDoughnut],
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '68%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16 } },
                    tooltip: { callbacks: { label: contexto => ' ' + contexto.label + ': ' + contexto.raw + ' (' + ((contexto.raw / totalServicios) * 100).toFixed(1) + '%)' } }
                }
            }
        });

        const mesesOrdenados = Object.keys(meses).sort();
        const etiquetasMeses = mesesOrdenados.map(mes => {
            const partes = mes.split('-');
            return partes.length === 2 ? nombresMeses[parseInt(partes[1], 10) - 1] + ' ' + partes[0] : mes;
        });
        graficoFechas = new Chart(document.getElementById('graficoFechasServicio'), {
            type: 'bar',
            data: { labels: etiquetasMeses, datasets: [
                { label: 'Ingresos', data: mesesOrdenados.map(mes => meses[mes].monto), backgroundColor: '#2563eb', borderRadius: 8, maxBarThickness: 46, yAxisID: 'y' },
                { type: 'line', label: 'Servicios técnicos', data: mesesOrdenados.map(mes => meses[mes].servicios), borderColor: '#f59e0b', backgroundColor: '#f59e0b', pointBackgroundColor: '#ffffff', pointBorderColor: '#f59e0b', pointBorderWidth: 3, pointRadius: 5, pointHoverRadius: 7, borderWidth: 3, tension: 0.35, yAxisID: 'yServicios' }
            ] },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: true, position: 'bottom', labels: { usePointStyle: true, padding: 16 } }, tooltip: { callbacks: { label: contexto => contexto.dataset.yAxisID === 'yServicios' ? ' ' + contexto.dataset.label + ': ' + contexto.raw : ' ' + contexto.dataset.label + ': ' + formatoMoneda.format(contexto.raw) } } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#64748b' } },
                    y: { beginAtZero: true, grid: { color: '#e5e7eb' }, ticks: { color: '#64748b', callback: valor => formatoMoneda.format(valor) } },
                    yServicios: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { color: '#f59e0b', precision: 0, stepSize: 1 } }
                }
            }
        });
    }

    $('#btnAplicarFiltrosServicio').on('click', cargarReporte);
    $('#btnLimpiarFiltrosServicio').on('click', function () { $('#reporteBuscar, #reporteDesde, #reporteHasta').val(''); $('#reporteEstado, #reporteEspecialidad').val(''); cargarReporte(); });
    $('#reporteBuscar').on('keypress', function (evento) { if (evento.key === 'Enter') cargarReporte(); });
    $('#btnDescargarReporteServicio').on('click', function () {
        const parametros = new URLSearchParams(filtros());
        window.open('?pagina=reporteservicio_tecnico&accion=exportarPdf&' + parametros.toString(), '_blank');
    });

    cargarReporte();
});

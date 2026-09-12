$(document).ready(function () {
    let pedidos = [];
    let chartBarrasInstance = null;
    let chartDonaInstance = null;

    obtenerPedidos();

    $('#searchInput').on('input', () => filtrarPedidos());
    $('#dateInicioInput').on('change', () => filtrarPedidos());
    $('#dateFinInput').on('change', () => filtrarPedidos());
    $('#estadoPagoContainer').on('change', () => filtrarPedidos());
    $('#estadoPedidoContainer').on('change', () => filtrarPedidos());
    $('#metodoPagoContainer').on('change', () => filtrarPedidos());

    $('#btnDescargarPDF').on('click', function () {
        const busqueda = $('#searchInput').val().trim();
        const fInicio = $('#dateInicioInput').val();
        const fFin = $('#dateFinInput').val();
        const estadoPago = $('#estadoPagoContainer').val();
        const estadoPedido = $('#estadoPedidoContainer').val();
        const metodoPago = $('#metodoPagoContainer').val();

        let urlExportar = `?pagina=reporteVentasOnline&accion=exportarPdf`;
        if (busqueda) urlExportar += `&buscar=${encodeURIComponent(busqueda)}`;
        if (fInicio) urlExportar += `&fecha_inicio=${fInicio}`;
        if (fFin) urlExportar += `&fecha_fin=${fFin}`;
        if (estadoPago) urlExportar += `&estado_pago=${estadoPago}`;
        if (estadoPedido) urlExportar += `&estado_pedido=${estadoPedido}`;
        if (metodoPago) urlExportar += `&metodo_pago=${metodoPago}`;

        window.open(urlExportar, '_blank');
    });

    function obtenerPedidos() {
        renderizarTabla([], true);
        $.ajax({
            url: '?pagina=reporteVentasOnline&accion=listar',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    pedidos = res.data;
                    cargarSelectMetodosPago(res.metodos_pago || []);
                    filtrarPedidos();
                } else {
                    console.error("Error al obtener pedidos: ", res.mensaje);
                }
            },
            error: function (err) {
                if (err.status === 403) {
                    Swal.fire('Sin permisos', 'No tienes permiso para consultar este reporte.', 'error');
                } else {
                    console.error("Error de conexión en AJAX pedidos.", err);
                }
            }
        });
    }

    function filtrarPedidos() {
        const busqueda = $('#searchInput').val().toLowerCase().trim();
        const estadoPagoSel = $('#estadoPagoContainer').val();
        const estadoPedidoSel = $('#estadoPedidoContainer').val();
        const metodoPagoSel = $('#metodoPagoContainer').val();
        const fechaInicio = $('#dateInicioInput').val();
        const fechaFin = $('#dateFinInput').val();

        const pedidosFiltrados = pedidos.filter(p => {
            const cumpleEstadoPago = !estadoPagoSel ||
                String(p.estado_pago || 'sin Pago').toLowerCase().replace(' ', '_') === estadoPagoSel.toLowerCase().replace(' ', '_');

            const cumpleEstadoPedido = !estadoPedidoSel ||
                String(p.estado_pedido || '').toLowerCase() === estadoPedidoSel;

            const cumpleMetodo = !metodoPagoSel ||
                String(p.metodo_pago || '').toLowerCase() === metodoPagoSel.toLowerCase();

            let cumpleFecha = true;
            if (p.fecha) {
                const fechaLimpia = p.fecha.split(' ')[0];
                if (fechaInicio && fechaLimpia < fechaInicio) cumpleFecha = false;
                if (fechaFin && fechaLimpia > fechaFin) cumpleFecha = false;
            } else if (fechaInicio || fechaFin) {
                cumpleFecha = false;
            }

            const textoBusqueda = [
                p.id_pedido,
                p.nombre_cliente,
                p.cedula_persona,
                p.telefono_cliente,
                p.referencia
            ].filter(Boolean).map(v => String(v).toLowerCase()).join(' ');

            const cumpleBusqueda = !busqueda || textoBusqueda.includes(busqueda);

            return cumpleEstadoPago && cumpleEstadoPedido && cumpleMetodo && cumpleFecha && cumpleBusqueda;
        });

        renderizarTabla(pedidosFiltrados);
        actualizarKPIsYGraficos(pedidosFiltrados);
    }

    function actualizarKPIsYGraficos(datosFiltrados) {
        let montoTotal = 0;
        let pedidosValidos = 0;
        let pagosAprobados = 0;

        const ingresosPorFecha = {};
        const pagosPorEstado = {};

        datosFiltrados.forEach(p => {
            const estadoPedido = String(p.estado_pedido || '').toLowerCase();
            const estadoPago = String(p.estado_pago || 'sin pago').toLowerCase().replace(' ', '_');
            const fecha = p.fecha ? p.fecha.split(' ')[0] : 'Sin Fecha';

            if (estadoPedido === 'rechazado' || estadoPedido === 'cancelado') {
                return;
            }

            montoTotal += parseFloat(p.total || 0);
            pedidosValidos++;

            if (estadoPago === 'aprobado') {
                pagosAprobados++;
            }

            ingresosPorFecha[fecha] = (ingresosPorFecha[fecha] || 0) + parseFloat(p.total || 0);
        });

        datosFiltrados.forEach(p => {
            const estadoPago = String(p.estado_pago || 'sin pago').toLowerCase().replace(' ', '_');
            pagosPorEstado[estadoPago] = (pagosPorEstado[estadoPago] || 0) + 1;
        });

        $('#kpiMontoTotal').text(`$${montoTotal.toFixed(2)}`);
        $('#kpiTotalPedidos').text(pedidosValidos);
        $('#kpiPagosAprobados').text(pagosAprobados);

        const ctxBarras = document.getElementById('chartTendenciaIngresos');
        if (ctxBarras) {
            if (chartBarrasInstance) chartBarrasInstance.destroy();

            const fechasOrdenadas = Object.keys(ingresosPorFecha).sort();
            const montosOrdenados = fechasOrdenadas.map(f => ingresosPorFecha[f]);

            chartBarrasInstance = new Chart(ctxBarras.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: fechasOrdenadas.map(f => {
                        const partes = f.split('-');
                        return partes.length === 3 ? `${partes[2]}/${partes[1]}` : f;
                    }),
                    datasets: [{
                        label: 'Ingresos ($)',
                        data: montosOrdenados,
                        backgroundColor: 'rgba(13, 110, 253, 0.85)',
                        borderColor: '#0d6efd',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        const ctxDona = document.getElementById('chartPagosEstado');
        if (ctxDona) {
            if (chartDonaInstance) chartDonaInstance.destroy();

            const etiquetas = Object.keys(pagosPorEstado).map(e => e.charAt(0).toUpperCase() + e.slice(1));
            const valores = Object.values(pagosPorEstado);

            chartDonaInstance = new Chart(ctxDona.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: etiquetas,
                    datasets: [{
                        data: valores,
                        backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545', '#6c757d']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    }

    function cargarSelectMetodosPago(metodos) {
        let opcionesHtml = '<option value="">Todos</option>';
        metodos.forEach(met => {
            if (met) opcionesHtml += `<option value="${met.toLowerCase()}">${met.charAt(0).toUpperCase() + met.slice(1)}</option>`;
        });
        $('#metodoPagoContainer').html(opcionesHtml);
    }

    function renderizarTabla(pedidosAMostrar, cargando = false) {
        let html = '';

        if (cargando) {
            for (let i = 0; i < 5; i++) {
                html += `
                    <tr class="placeholder-glow">
                        <td class="align-middle text-center"><span class="placeholder col-6 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle text-center"><span class="placeholder col-8 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle text-start"><span class="placeholder col-10 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle text-center"><span class="placeholder col-7 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle text-center"><span class="placeholder col-5 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle text-center"><span class="placeholder col-5 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle text-center"><span class="placeholder col-6 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle text-center"><span class="placeholder col-6 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle text-end"><span class="placeholder col-6 bg-success opacity-25 rounded-2"></span></td>
                    </tr>`;
            }
            $('#tablaReporteVentasOnline').html(html);
            return;
        }

        if (pedidosAMostrar.length === 0) {
            html = `<tr><td colspan="9" class="text-muted py-4 text-center">No se encontraron pedidos online con los filtros aplicados</td></tr>`;
        } else {
            pedidosAMostrar.forEach(p => {
                const cliente = p.nombre_cliente || `<span class="text-muted text-opacity-50"><em>Sin registrar</em></span>`;

                const estPago = String(p.estado_pago || 'Sin Pago').toLowerCase().replace(' ', '_');
                let badgePago = 'bg-secondary';
                if (estPago === 'aprobado') badgePago = 'bg-success';
                else if (estPago === 'rechazado') badgePago = 'bg-danger';
                else if (estPago === 'pendiente') badgePago = 'bg-warning text-dark';
                else if (estPago === 'en_revision') badgePago = 'bg-info text-dark';

                const estPedido = String(p.estado_pedido || '').toLowerCase();
                let badgePedido = 'bg-secondary';
                if (estPedido === 'aprobado' || estPedido === 'entregado') badgePedido = 'bg-success';
                else if (estPedido === 'revision' || estPedido === 'enviado') badgePedido = 'bg-info text-dark';
                else if (estPedido === 'pendiente') badgePedido = 'bg-warning text-dark';
                else if (estPedido === 'rechazado' || estPedido === 'cancelado') badgePedido = 'bg-danger';

                const despacho = p.estado_despacho
                    ? `<span class="badge bg-secondary text-capitalize">${p.estado_despacho}</span>`
                    : `<span class="text-muted"><em>Sin despacho</em></span>`;

                const metodo = p.metodo_pago || 'No registrado';

                html += `
                    <tr>
                        <td class="text-center align-middle fw-medium">#${p.id_pedido}</td>
                        <td class="text-center align-middle small">${p.fecha}</td>
                        <td class="text-start align-middle">${cliente}</td>
                        <td class="text-center align-middle">${p.telefono_cliente || '-'}</td>
                        <td class="text-center align-middle text-capitalize">${metodo}</td>
                        <td class="text-center align-middle"><span class="badge ${badgePago} text-capitalize">${p.estado_pago || 'Sin Pago'}</span></td>
                        <td class="text-center align-middle"><span class="badge ${badgePedido} text-capitalize">${p.estado_pedido || '-'}</span></td>
                        <td class="text-center align-middle">${despacho}</td>
                        <td class="text-end align-middle fw-bold text-success">$${parseFloat(p.total || 0).toFixed(2)}</td>
                    </tr>`;
            });
        }

        $('#tablaReporteVentasOnline').html(html);
    }
});
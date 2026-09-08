$(document).ready(function () {
    let ventas = [];
    let permisos = { registrar: false, modificar: false, eliminar: false };
    
    let chartBarrasInstance = null;
    let chartDonaInstance = null;

    $.get("?pagina=reporteVentas&permisos=true", function (data) {
        permisos = JSON.parse(data);
        if (permisos.eliminar) {
            $(".btn-anular").show();
        } else {
            $(".btn-anular").hide();
        }
    });

    obtenerVentas();

    $('#searchInput').on('input', () => filtrarVentas());
    $('#dateInicioInput').on('change', () => filtrarVentas());
    $('#dateFinInput').on('change', () => filtrarVentas());
    $('#origenContainer').on('change', () => filtrarVentas());
    $('#categoriesContainer').on('change', () => filtrarVentas());

    $('#btnDescargarPDF').on('click', function () {
        const busqueda = $('#searchInput').val().trim();
        const fInicio = $('#dateInicioInput').val();
        const fFin = $('#dateFinInput').val();
        const origen = $('#origenContainer').val();
        const estado = $('#categoriesContainer').val();

        // Construir la URL de exportación con los filtros aplicados en el cliente
        let urlExportar = `?pagina=reporteVentas&accion=exportarPdf`;
        if (busqueda) urlExportar += `&buscar=${encodeURIComponent(busqueda)}`;
        if (fInicio) urlExportar += `&fecha_inicio=${fInicio}`;
        if (fFin) urlExportar += `&fecha_fin=${fFin}`;
        if (origen) urlExportar += `&origen=${origen}`;
        if (estado) urlExportar += `&estado=${estado}`;

        window.open(urlExportar, '_blank');
    });

    $('#tablareporteVentas').on('click', '.btn-ver-detalle', function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        const idVenta = $(this).attr('data-id'); 
        if (idVenta) {
            verDetalleVenta(idVenta);
        }
    });

    function obtenerVentas() {
        renderizarTabla([], true);
        $.ajax({
            url: '?pagina=reporteVentas&accion=listarVentas',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    ventas = res.data;
                    cargarSelectEstados(ventas);
                    cargarSelectOrigenes(ventas);
                    filtrarVentas(); // Llama a filtrar para procesar KPIs y Gráficos inicialmente
                } else {
                    console.error("Error al obtener ventas: ", res.mensaje);
                }
            },
            error: function (err) {
                console.error("Error de conexión en AJAX ventas.", err);
            }
        });
    }

    function filtrarVentas() {
        const busqueda = $('#searchInput').val().toLowerCase().trim();
        const estadoSeleccionado = $('#categoriesContainer').val();
        const origenSeleccionado = $('#origenContainer').val();
        const fechaInicio = $('#dateInicioInput').val();
        const fechaFin = $('#dateFinInput').val();

        const ventasFiltradas = ventas.filter(v => {
            const cumpleEstado = !estadoSeleccionado ||
                String(v.estado_venta || v.estado || '').toLowerCase() === estadoSeleccionado;

            const cumpleOrigen = !origenSeleccionado ||
                String(v.origen_venta || 'Mostrador').toLowerCase() === origenSeleccionado;

            // Lógica de rango de fechas (Inicio y Fin)
            let cumpleFecha = true;
            if (v.fecha_venta) {
                const fechaVentaLimpia = v.fecha_venta.split(' ')[0]; // Extrae YYYY-MM-DD
                if (fechaInicio && fechaVentaLimpia < fechaInicio) cumpleFecha = false;
                if (fechaFin && fechaVentaLimpia > fechaFin) cumpleFecha = false;
            } else if (fechaInicio || fechaFin) {
                cumpleFecha = false;
            }

            const idVenta = String(v.id_venta).toLowerCase();
            const cedulaCliente = String(v.cedula_cliente || '').toLowerCase();
            const cedulaEmpleado = String(v.cedula_empleado || '').toLowerCase();
            const nombreCliente = `${v.nombre_cliente || ''} ${v.apellido_cliente || ''}`.toLowerCase();
            const nombreEmpleado = `${v.nombre_empleado || ''} ${v.apellido_empleado || ''}`.toLowerCase();

            const cumpleBusqueda = idVenta.includes(busqueda) ||
                cedulaCliente.includes(busqueda) ||
                cedulaEmpleado.includes(busqueda) ||
                nombreCliente.includes(busqueda) ||
                nombreEmpleado.includes(busqueda);

            return cumpleEstado && cumpleOrigen && cumpleFecha && cumpleBusqueda;
        });

        renderizarTabla(ventasFiltradas);
        actualizarKPIsYGraficos(ventasFiltradas);
    }

    function actualizarKPIsYGraficos(datosFiltrados) {
        let montoTotal = 0;
        let ventasProcesadas = 0;
        let ventasAnuladas = 0;

        // Estructuras para acumular datos de gráficos
        const ingresosPorFecha = {};
        const distribucionOrigen = {};

        datosFiltrados.forEach(v => {
            const estado = String(v.estado_venta || v.estado || '').toLowerCase();
            const origen = v.origen_venta || 'Mostrador';
            const fecha = v.fecha_venta ? v.fecha_venta.split(' ')[0] : 'Sin Fecha';

            // Contadores de auditoría y KPIs
            if (estado === 'anulada' || estado === 'cancelada') {
                ventasAnuladas++;
            } else {
                montoTotal += parseFloat(v.total_venta || 0);
                ventasProcesadas++;

                // Agrupación para gráfico de Barras (solo ventas válidas)
                ingresosPorFecha[fecha] = (ingresosPorFecha[fecha] || 0) + parseFloat(v.total_venta || 0);
            }

            // Agrupación para gráfico de Dona
            distribucionOrigen[origen] = (distribucionOrigen[origen] || 0) + 1;
        });

        // Actualizar valores en los contenedores HTML del DOM
        $('#kpiMontoTotal').text(`$${montoTotal.toFixed(2)}`);
        $('#kpiTotalVentas').text(ventasProcesadas);
        $('#kpiVentasAnuladas').text(ventasAnuladas);

        // --- RENDERIZADO DEL GRÁFICO DE BARRAS ---
        const ctxBarras = document.getElementById('chartTendenciaVentas');
        if (ctxBarras) {
            if (chartBarrasInstance) chartBarrasInstance.destroy(); // Evita solapamientos

            // Ordenar fechas cronológicamente para el eje X
            const fechasOrdenadas = Object.keys(ingresosPorFecha).sort();
            const montosOrdenados = fechasOrdenadas.map(f => ingresosPorFecha[f]);

            chartBarrasInstance = new Chart(ctxBarras.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: fechasOrdenadas.map(f => {
                        // Cambiar formato YYYY-MM-DD a DD/MM para que sea más legible en el eje
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

        // --- RENDERIZADO DEL GRÁFICO DE DONA ---
        const ctxDona = document.getElementById('chartOrigenVentas');
        if (ctxDona) {
            if (chartDonaInstance) chartDonaInstance.destroy();

            const origenesLabels = Object.keys(distribucionOrigen);
            const origenesValores = origenesLabels.map(o => distribucionOrigen[o]);

            chartDonaInstance = new Chart(ctxDona.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: origenesLabels,
                    datasets: [{
                        data: origenesValores,
                        backgroundColor: ['#198754', '#0dcaf0', '#ffc107', '#6c757d']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    }

    function verDetalleVenta(idVenta) {
        const ventaObj = ventas.find(v => v.id_venta == idVenta);
        if (!ventaObj) return;

        $('#detIdVenta').text(`#${ventaObj.id_venta}`);
        $('#detFecha').text(ventaObj.fecha_venta);
        $('#detOrigen').text(ventaObj.origen_venta || 'Mostrador');
        $('#detTotal').text(`$${parseFloat(ventaObj.total_venta).toFixed(2)}`);

        let cliente = (ventaObj.nombre_cliente) ? `${ventaObj.nombre_cliente} ${ventaObj.apellido_cliente}` : 'Sin registrar';
        let operador = (ventaObj.nombre_empleado) ? `${ventaObj.nombre_empleado} ${ventaObj.apellido_empleado}` : 'Sin registrar';
        $('#detCliente').text(cliente);
        $('#detOperador').text(operador);

        const est = (ventaObj.estado_venta || ventaObj.estado || 'Completada');
        $('#detEstado').html(`<span class="badge ${est.toLowerCase() === 'completada' ? 'bg-success' : 'bg-danger'} text-capitalize">${est}</span>`);

        $('#tablaDetallesProductos').html(`<tr><td colspan="4" class="text-center py-3"><div class="spinner-border spinner-border-sm text-secondary" role="status"></div> Cargando productos...</td></tr>`);

        $.ajax({
            url: `?pagina=reporteVentas&accion=detallesVentas&id_venta=${idVenta}`,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.success && res.data.length > 0) {
                    let htmlProductos = '';
                    res.data.forEach(prod => {
                        let precio = parseFloat(prod.precio_unitario).toFixed(2);
                        let subtotal = parseFloat(prod.subtotal).toFixed(2);

                        htmlProductos += `
                            <tr>
                                <td class="text-start ps-3">${prod.nombre_producto || 'Producto #' + prod.id_producto}</td>
                                <td>${prod.cantidad}</td>
                                <td>$${precio}</td>
                                <td class="text-end pe-3 fw-medium">$${subtotal}</td>
                            </tr>`;
                    });
                    $('#tablaDetallesProductos').html(htmlProductos);
                } else {
                    $('#tablaDetallesProductos').html(`<tr><td colspan="4" class="text-muted py-3 text-center">No se encontraron productos en esta venta.</td></tr>`);
                }
            },
            error: function () {
                $('#tablaDetallesProductos').html(`<tr><td colspan="4" class="text-danger py-3 text-center">Error al conectar con el servidor.</td></tr>`);
            }
        });
    }

    function cargarSelectEstados(listaVentas) {
        const estados = [...new Set(listaVentas.map(v => v.estado_venta || v.estado || 'Completada'))];
        let opcionesHtml = '<option value="">Todos</option>';
        estados.forEach(est => {
            if (est) opcionesHtml += `<option value="${est.toLowerCase()}">${est.charAt(0).toUpperCase() + est.slice(1)}</option>`;
        });
        $('#categoriesContainer').html(opcionesHtml);
    }

    function cargarSelectOrigenes(listaVentas) {
        const origenes = [...new Set(listaVentas.map(v => v.origen_venta || 'Mostrador'))];
        let opcionesHtml = '<option value="">Todos</option>';
        origenes.forEach(orig => {
            if (orig) opcionesHtml += `<option value="${orig.toLowerCase()}">${orig.charAt(0).toUpperCase() + orig.slice(1)}</option>`;
        });
        $('#origenContainer').html(opcionesHtml);
    }

    function renderizarTabla(ventasAMostrar, cargando = false) {
        let html = '';

        if (cargando) {
            for (let i = 0; i < 6; i++) {
                html += `
                    <tr class="placeholder-glow">
                        <td class="align-middle text-center"><span class="placeholder col-6 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle text-center"><span class="placeholder col-8 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle text-center"><span class="placeholder col-5 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle text-start"><span class="placeholder col-10 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle text-start"><span class="placeholder col-10 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle text-end"><span class="placeholder col-6 bg-success opacity-25 rounded-2"></span></td>
                        <td class="align-middle text-center"><span class="placeholder col-5 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle text-center"><span class="placeholder col-6 bg-secondary opacity-25 rounded-2"></span></td>
                    </tr>`;
            }
            $('#tablareporteVentas').html(html);
            return;
        }

        if (ventasAMostrar.length === 0) {
            html = `<tr><td colspan="8" class="text-muted py-4 text-center">No se encontraron ventas coincidentes con los filtros aplicados</td></tr>`;
        } else {
            ventasAMostrar.forEach(p => {
                let cliente = (p.nombre_cliente) ? `${p.nombre_cliente} ${p.apellido_cliente}` : `<span class="text-muted text-opacity-50"><em>Sin registrar</em></span>`;
                let empleado = (p.nombre_empleado) ? `${p.nombre_empleado} ${p.apellido_empleado}` : `<span class="text-muted text-opacity-50"><em>Sin registrar</em></span>`;

                const estLimpio = String(p.estado_venta || p.estado || 'Completada').toLowerCase();
                let badgeEstado = 'bg-success';
                if (estLimpio === 'anulada' || estLimpio === 'cancelada') badgeEstado = 'bg-danger';
                else if (estLimpio === 'pendiente') badgeEstado = 'bg-warning text-dark';

                const origen = String(p.origen_venta || 'Mostrador').toLowerCase();
                const esPresencial = origen === 'presencial' || origen === 'mostrador';

                html += `
                    <tr class="item-venta" style="cursor:pointer">
                        <td class="text-center align-middle fw-medium">#${p.id_venta}</td>
                        <td class="text-center align-middle small">${p.fecha_venta}</td>
                        <td class="text-center align-middle"><span class="badge bg-secondary text-capitalize">${p.origen_venta || 'Mostrador'}</span></td>
                        <td class="text-start align-middle">${cliente}</td>
                        <td class="text-start align-middle">${empleado}</td>
                        <td class="text-end align-middle fw-bold text-success">$${parseFloat(p.total_venta).toFixed(2)}</td>
                        <td class="text-center align-middle"><span class="badge ${badgeEstado} text-capitalize">${p.estado_venta || p.estado || 'Completada'}</span></td>
                        <td class="text-center align-middle">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-outline-primary btn-ver-detalle" 
                                    title="Ver Detalle" 
                                    data-id="${p.id_venta}" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalDetalleVenta">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                
                                ${esPresencial && estLimpio !== 'anulada' ? `
                                    <button class="btn btn-sm btn-outline-danger btn-anular" title="Anular Venta" data-id="${p.id_venta}">
                                        <i class="bi bi-ban"></i>
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>`;
            });
        }
        $('#tablareporteVentas').html(html);

        // Ocultar o mostrar botones de anular según los permisos asignados inicialmente
        if (!permisos.eliminar) {
            $(".btn-anular").hide();
        }
    }

    // Lógica para procesar la anulación vía AJAX
    $('#tablareporteVentas').on('click', '.btn-anular', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const idVenta = $(this).attr('data-id');
        if (!idVenta) return;

        Swal.fire({
            title: `¿Estás seguro de anular la venta #${idVenta}?`,
            text: "Esta acción devolverá los productos al inventario y cambiará el estado a 'Anulada'. No se puede revertir.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, anular venta',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Restableciendo inventario y actualizando estados',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                $.ajax({
                    url: '?pagina=reporteVentas&accion=anularVenta',
                    type: 'POST',
                    data: { id_venta: idVenta },
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            Swal.fire('¡Anulada!', res.mensaje, 'success');
                            obtenerVentas(); 
                        } else {
                            Swal.fire('Error', res.mensaje, 'error');
                        }
                    },
                    error: function (err) {
                        console.error("Error en AJAX anularVenta", err);
                        Swal.fire('Error técnico', 'No se pudo comunicar con el servidor.', 'error');
                    }
                });
            }
        });
    });
});
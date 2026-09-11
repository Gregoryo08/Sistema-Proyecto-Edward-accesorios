$(document).ready(function () {
    function formatCurrency(amount) {
        if (typeof amount !== 'number') {
            amount = parseFloat(amount);
        }
        if (isNaN(amount)) {
            return '$0.00';
        }
        return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
    }

    function CargarTotalesHoy() {
        $.ajax({
            url: '?pagina=principal&action=TotalesHoy',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.ingresosHoy !== undefined) {
                    $('#ingresosHoy').text(formatCurrency(data.ingresosHoy));
                }
                if (data.ventasCompletadas !== undefined) {
                    $('#ventasCompletadas').text(data.ventasCompletadas);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error(textStatus, errorThrown);
            }
        });
    }

    function CargarIndicadores() {
        $.ajax({
            url: '?pagina=principal&action=Cargaringreso',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.error) {
                    console.error(data.error);
                    return;
                }
                
                $('#ingresosHoy').text(formatCurrency(data.ingresosHoy));
                $('#ingresosSemana').text(formatCurrency(data.ingresosSemana));
                $('#ingresosMes').text(formatCurrency(data.ingresosMes));
                $('#stockBajo').text(data.productosBajosStock);
                $('#serviciosPendientes').text(data.serviciosPendientes);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error(textStatus, errorThrown);
            }
        });
    }

    function CargarActividadReciente() {
        $.ajax({
            url: '?pagina=principal&action=VentasRecientes',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                const $contenedorActividad = $('.placeholder-graph');
                $contenedorActividad.empty();
                
                $contenedorActividad.css({
                    'display': 'block',
                    'height': 'auto',
                    'background': 'transparent',
                    'border': 'none',
                    'padding': '0'
                });

                if (data.error) {
                    $contenedorActividad.html('<div class="alert alert-danger">Error al cargar la actividad.</div>');
                    return;
                }

                if (data.length > 0) {
                    let tablaHTML = `
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col"># Venta</th>
                                        <th scope="col">Cliente</th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    data.forEach(function (venta) {
                        let nombre = venta.nombre ? venta.nombre : '';
                        let apellido = venta.apellido ? venta.apellido : '';
                        let cliente = (nombre || apellido) ? `${nombre} ${apellido}`.trim() : 'Cliente General';
                        
                        let fechaObj = new Date(venta.fecha_venta);
                        let fechaFormateada = fechaObj.toLocaleDateString('es-VE', { 
                            day: '2-digit', 
                            month: 'short', 
                            year: 'numeric' 
                        });

                        tablaHTML += `
                            <tr>
                                <td><span class="badge bg-light text-dark border">V-${venta.id_venta}</span></td>
                                <td><span class="fw-medium">${cliente}</span></td>
                                <td>${fechaFormateada}</td>
                                <td class="fw-bold text-success">${formatCurrency(venta.total_venta)}</td>
                            </tr>
                        `;
                    });

                    tablaHTML += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    
                    $contenedorActividad.append(tablaHTML);
                } else {
                    $contenedorActividad.html('<div class="text-center text-muted py-4"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No hay ventas registradas recientemente.</div>');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error(textStatus, errorThrown);
            }
        });
    }

    CargarTotalesHoy();
    CargarIndicadores();
    CargarActividadReciente();
});
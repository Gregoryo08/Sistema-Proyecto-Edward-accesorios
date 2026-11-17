
function toggleSection(sectionId) {
    let section = document.getElementById(sectionId);
    if (section) {
        section.classList.toggle('show');
    }
}

function actualizarDashboard() {
    fetch('?pagina=getDashboardData')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const pendientes = document.getElementById('pendientesCount');
                const aprobados = document.getElementById('aprobadosCount');
                const ruta = document.getElementById('rutaCount');
                const total = document.getElementById('totalPedidosCount');
                if (pendientes) pendientes.innerText = data.pendientes || 0;
                if (aprobados) aprobados.innerText = data.aprobados || 0;
                if (ruta) ruta.innerText = data.en_ruta || 0;
                if (total) total.innerText = data.total_pedidos || 0;

                const contPendientes = document.querySelector('#pendientesSection .card-body');
                const contAprobados = document.querySelector('#aprobadosSection .card-body');
                const contDespachos = document.querySelector('#despachosSection .card-body');
                if (contPendientes) contPendientes.innerHTML = data.html_pendientes;
                if (contAprobados) contAprobados.innerHTML = data.html_aprobados;
                if (contDespachos) contDespachos.innerHTML = data.html_despachos;

                cargarGraficoDespachosMensuales();
            }
        })
        .catch(err => console.error('Error al actualizar dashboard:', err));
}

function cargarGraficos() {
    fetch('?pagina=getDashboardData&tipo=ventas_diarias')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Highcharts.chart('chartVentasDiarias', {
                    chart: { type: 'column' },
                    title: { text: 'Ventas Aprobadas (Últimos 7 Días)', style: { fontSize: '12px' } },
                    xAxis: { categories: data.categorias, title: { text: 'Fecha' } },
                    yAxis: { title: { text: 'Monto ($)' }, min: 0 },
                    series: [{ name: 'Ventas', data: data.valores, color: '#28a745' }],
                    credits: { enabled: false }
                });
            }
        })
        .catch(err => console.error('Error al cargar ventas diarias:', err));

    fetch('?pagina=getDashboardData&tipo=ventas_mensuales')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Highcharts.chart('chartVentasMensuales', {
                    chart: { type: 'line' },
                    title: { text: 'Ventas Acumuladas del Mes', style: { fontSize: '12px' } },
                    xAxis: { categories: data.categorias, title: { text: 'Semana' } },
                    yAxis: { title: { text: 'Monto ($)' }, min: 0 },
                    series: [{ name: 'Acumulado', data: data.valores, color: '#007bff' }],
                    credits: { enabled: false }
                });
            }
        })
        .catch(err => console.error('Error al cargar ventas mensuales:', err));

    fetch('?pagina=getDashboardData&tipo=ventas_no_concretadas')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Highcharts.chart('chartVentasNoConcretadas', {
                    chart: { type: 'pie' },
                    title: { text: 'Ventas No Concretadas', style: { fontSize: '12px' } },
                    series: [{ name: 'Motivo', data: data.datos }],
                    credits: { enabled: false }
                });
            }
        })
        .catch(err => console.error('Error al cargar ventas no concretadas:', err));

    fetch('?pagina=getDashboardData&tipo=metodos_pago')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Highcharts.chart('chartMetodosPago', {
                    chart: { type: 'pie' },
                    title: { text: 'Métodos de Pago', style: { fontSize: '14px' } },
                    series: [{ name: 'Pedidos', data: data.datos }],
                    plotOptions: { pie: { dataLabels: { enabled: true, format: '{point.name}: {point.y}' } } },
                    credits: { enabled: false }
                });
            }
        })
        .catch(err => console.error('Error al cargar métodos de pago:', err));

    fetch('?pagina=getDashboardData&tipo=top_clientes')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Highcharts.chart('chartTopClientes', {
                    chart: { type: 'bar' },
                    title: { text: 'Top 5 Mejores Clientes', style: { fontSize: '14px' } },
                    xAxis: { categories: data.categorias, title: { text: 'Cliente' } },
                    yAxis: { title: { text: 'Total Gastado ($)' }, min: 0 },
                    series: [{ name: 'Compras', data: data.valores, color: '#ffc107' }],
                    credits: { enabled: false }
                });
            }
        })
        .catch(err => console.error('Error al cargar top clientes:', err));

    fetch('?pagina=getDashboardData&tipo=top_productos')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Highcharts.chart('chartTopProductos', {
                    chart: { type: 'bar' },
                    title: { text: 'Top 5 Productos Más Vendidos', style: { fontSize: '14px' } },
                    xAxis: { categories: data.categorias, title: { text: 'Producto' } },
                    yAxis: { title: { text: 'Cantidad Vendida' }, min: 0 },
                    series: [{ name: 'Ventas', data: data.valores, color: '#28a745' }],
                    credits: { enabled: false }
                });
            }
        })
        .catch(err => console.error('Error al cargar top productos:', err));

    cargarGraficoDespachosMensuales();
}

let despachosChart = null;

function cargarGraficoDespachosMensuales() {
    fetch('?pagina=getDashboardData&tipo=despachos_mensuales')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (despachosChart) {
                    despachosChart.update({
                        xAxis: { categories: data.categorias },
                        series: [{ name: 'Despachos', data: data.valores, color: '#17a2b8' }]
                    });
                } else {
                    despachosChart = Highcharts.chart('chartDespachosMensuales', {
                        chart: { type: 'column' },
                        title: { text: 'Despachos del Mes', style: { fontSize: '12px' } },
                        xAxis: { categories: data.categorias, title: { text: 'Semana' } },
                        yAxis: { title: { text: 'Cantidad' }, min: 0 },
                        series: [{ name: 'Despachos', data: data.valores, color: '#17a2b8' }],
                        credits: { enabled: false }
                    });
                }
            }
        })
        .catch(err => console.error('Error al cargar despachos:', err));
}

let timeoutActualizacion = null;

function programarActualizacion() {
    if (timeoutActualizacion) clearTimeout(timeoutActualizacion);
    if (document.hidden) return;
    timeoutActualizacion = setTimeout(function() {
        actualizarDashboard();
        programarActualizacion();
    }, 30000);
}

function iniciarActualizacion() {
    if (timeoutActualizacion) clearTimeout(timeoutActualizacion);
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            if (timeoutActualizacion) clearTimeout(timeoutActualizacion);
            timeoutActualizacion = null;
        } else {
            actualizarDashboard();
            programarActualizacion();
        }
    });
    programarActualizacion();
}


function aprobarPago(id_reporte, id_pedido) {
    Swal.fire({ 
        title: '¿Aprobar este pago?', 
        icon: 'question', 
        showCancelButton: true, 
        confirmButtonText: 'Sí, aprobar', 
        cancelButtonText: 'Cancelar' 
    })
    .then((result) => {
        if (result.isConfirmed) {
            fetch('?pagina=procesarVerificacion', {
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    id_reporte: id_reporte, 
                    id_pedido: id_pedido, 
                    accion: 'aprobar' 
                })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    const el = document.getElementById('pendiente-' + id_reporte);
                    if (el) el.remove();
                    Swal.fire('¡Aprobado!', 'Pago aprobado correctamente', 'success');
                    actualizarDashboard();
                } else {
                    Swal.fire('Error', d.message || 'No se pudo aprobar', 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Error de conexión', 'error'));
        }
    });
}

function rechazarPago(id_reporte, id_pedido) {
    Swal.fire({ 
        title: '¿Rechazar este pago?', 
        input: 'text',
        inputLabel: 'Motivo del rechazo (el cliente lo verá)',
        inputPlaceholder: 'Ej: Los datos del pago no coinciden',
        showCancelButton: true, 
        confirmButtonText: 'Sí, rechazar', 
        cancelButtonText: 'Cancelar' 
    })
    .then((result) => {
        if (result.isConfirmed) {
            fetch('?pagina=procesarVerificacion', {
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    id_reporte: id_reporte, 
                    id_pedido: id_pedido,
                    accion: 'rechazar',
                    motivo: result.value || ''
                })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    const el = document.getElementById('pendiente-' + id_reporte);
                    if (el) el.remove();
                    Swal.fire('Rechazado', 'Pago rechazado', 'info');
                    actualizarDashboard();
                } else {
                    Swal.fire('Error', d.message || 'No se pudo rechazar', 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Error de conexión', 'error'));
        }
    });
}

function mostrarFormularioDespacho(id_reporte, id_pedido, cedula, nombre, telefono, direccion) {
    Swal.fire({
        title: 'Asignar Despacho',
        html: `
            <input type="text" id="motorizado_nombre" class="swal2-input" placeholder="Nombre del motorizado" required>
            <input type="text" id="motorizado_telefono" class="swal2-input" placeholder="Teléfono del motorizado" required>
            <select id="tipo_despacho" class="swal2-input">
                <option value="tienda">📦 Retiro en Tienda</option>
                <option value="delivery">🚚 Delivery</option>
            </select>
            <input type="number" id="tiempo_estimado" class="swal2-input" placeholder="Tiempo estimado (min)" value="60">
        `,
        showCancelButton: true, 
        confirmButtonText: 'Asignar', 
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            let nom = document.getElementById('motorizado_nombre').value.trim();
            let tel = document.getElementById('motorizado_telefono').value.trim();
            let tipo = document.getElementById('tipo_despacho').value;
            let tiempo = document.getElementById('tiempo_estimado').value || 60;
            
            if (!nom || !tel) { 
                Swal.showValidationMessage('Complete todos los datos del motorizado'); 
                return false; 
            }
            return { 
                motorizado_nombre: nom, 
                motorizado_telefono: tel, 
                tipo_despacho: tipo, 
                tiempo_estimado: parseInt(tiempo) 
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('?pagina=crearDespacho', {
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    id_pedido: id_pedido, 
                    tipo: result.value.tipo_despacho,
                    motorizado_nombre: result.value.motorizado_nombre, 
                    motorizado_telefono: result.value.motorizado_telefono, 
                    tiempo_estimado: result.value.tiempo_estimado 
                })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    const el = document.getElementById('aprobado-' + id_reporte);
                    if (el) el.remove();
                    Swal.fire('¡Despacho asignado!', '', 'success');
                    actualizarDashboard();
                } else {
                    Swal.fire('Error', d.message || 'No se pudo asignar el despacho', 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Error de conexión', 'error'));
        }
    });
}

function actualizarEstadoDespacho(id_pedido, accion) {
    let titulo = accion === 'en_ruta' ? 'Iniciar Ruta' : 
                 (accion === 'entregado' ? 'Marcar Entregado' : 'Cancelar Despacho');
    let icono = accion === 'en_ruta' ? 'info' : 
                (accion === 'entregado' ? 'success' : 'warning');
    
    Swal.fire({ 
        title: titulo, 
        icon: icono, 
        showCancelButton: true, 
        confirmButtonText: 'Sí, confirmar', 
        cancelButtonText: 'Cancelar' 
    })
    .then((result) => {
        if (result.isConfirmed) {
            fetch('?pagina=gestionarDespacho', {
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    id_pedido: id_pedido, 
                    accion: accion 
                })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire('¡Actualizado!', '', 'success');
                    actualizarDashboard();
                    if (accion === 'entregado' || accion === 'cancelado') {
                        const fila = document.getElementById('despacho-row-' + id_pedido);
                        if (fila) fila.remove();
                    }
                } else {
                    Swal.fire('Error', d.message || 'No se pudo actualizar', 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Error de conexión', 'error'));
        }
    });
}


function registrarBitacora(tabla, accion, modulo, id_modulo) {
    fetch('?pagina=registrarBitacora', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            tabla: tabla,
            accion: accion,
            modulo: modulo,
            id_modulo: id_modulo
        })
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) {
            console.warn('Error al registrar en bitácora:', d.message);
        }
    })
    .catch(err => console.error('Error de conexión con bitácora:', err));
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 Panel de Cajera inicializado');
    actualizarDashboard();
    cargarGraficos();
    iniciarActualizacion();

    var navmenu = document.getElementById('navmenu');
    var mainContent = document.querySelector('.main-content');
    if (navmenu && mainContent) {
        navmenu.addEventListener('mouseenter', function() {
            mainContent.style.marginLeft = '260px';
            mainContent.style.width = 'calc(100% - 260px)';
        });
        navmenu.addEventListener('mouseleave', function() {
            mainContent.style.marginLeft = '65px';
            mainContent.style.width = 'calc(100% - 65px)';
        });
    }
});
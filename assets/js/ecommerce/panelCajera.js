// /src/assets/js/admin/panelCajera.js
// =============================================
// PANEL DE CAJERA - FUNCIONES UNIFICADAS
// =============================================

// =============================================
// SECCIONES RETRÁCTILES
// =============================================
function toggleSection(sectionId) {
    let section = document.getElementById(sectionId);
    if (section) {
        section.classList.toggle('show');
    }
}

// =============================================
// ACTUALIZAR SECCIONES (AJAX) — pendientes / aprobados / despachos
// =============================================
function actualizarDashboard() {
    fetch('?pagina=getDashboardData')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const contPendientes = document.querySelector('#pendientesSection .card-body');
                const contAprobados = document.querySelector('#aprobadosSection .card-body');
                const contDespachos = document.querySelector('#despachosSection .card-body');
                if (contPendientes) contPendientes.innerHTML = data.html_pendientes;
                if (contAprobados) contAprobados.innerHTML = data.html_aprobados;
                if (contDespachos) contDespachos.innerHTML = data.html_despachos;
            }
        })
        .catch(err => console.error('Error al actualizar panel:', err));
}

// =============================================
// ACTUALIZACIÓN EN TIEMPO REAL
// =============================================
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

// =============================================
// FUNCIONES DE APROBAR / RECHAZAR / DESPACHO
// =============================================

/**
 * Aprobar un pago desde el panel de cajera
 * @param {number} id_reporte - ID del reporte de pago
 * @param {number} id_pedido - ID del pedido asociado
 */
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

/**
 * Rechazar un pago desde el panel de cajera
 * @param {number} id_reporte - ID del reporte de pago
 * @param {number} id_pedido - ID del pedido asociado
 */
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

/**
 * Mostrar formulario para asignar despacho
 * @param {number} id_reporte - ID del reporte de pago
 * @param {number} id_pedido - ID del pedido
 * @param {string} cedula - Cédula del cliente
 * @param {string} nombre - Nombre del cliente
 * @param {string} telefono - Teléfono del cliente
 * @param {string} direccion - Dirección de entrega
 */
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
            <div style="text-align:left; margin:4px 0 2px; font-size:.85em; color:#6c757d;">Tiempo estimado de entrega (minutos)</div>
            <input type="number" id="tiempo_estimado" class="swal2-input" placeholder="60 a 120 minutos" min="60" max="120" step="5" value="60">
        `,
        showCancelButton: true, 
        confirmButtonText: 'Asignar', 
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            let nom = document.getElementById('motorizado_nombre').value.trim();
            let tel = document.getElementById('motorizado_telefono').value.trim();
            let tipo = document.getElementById('tipo_despacho').value;
            let tiempo = parseInt(document.getElementById('tiempo_estimado').value, 10);
            
            if (!nom || !tel) { 
                Swal.showValidationMessage('Complete todos los datos del motorizado'); 
                return false; 
            }
            if (isNaN(tiempo) || tiempo < 60 || tiempo > 120) {
                Swal.showValidationMessage('El tiempo debe estar entre 60 y 120 minutos');
                return false;
            }
            return { 
                motorizado_nombre: nom, 
                motorizado_telefono: tel, 
                tipo_despacho: tipo, 
                tiempo_estimado: tiempo 
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

/**
 * Actualizar estado de un despacho (en_ruta, entregado, cancelado)
 * @param {number} id_pedido - ID del pedido
 * @param {string} accion - Acción a realizar (en_ruta, entregado, cancelado)
 */
function actualizarEstadoDespacho(id_pedido, accion) {
    let titulo = accion === 'en_ruta' ? 'Iniciar Ruta' : 
                 (accion === 'entregado' ? 'Marcar Entregado' : 'Eliminar Despacho');
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

// =============================================
// FUNCIONES DE BITÁCORA (llamadas desde el controlador)
// =============================================

/**
 * Registrar acción en bitácora (desde el frontend)
 * @param {string} tabla - Tabla afectada
 * @param {string} accion - Acción realizada
 * @param {string} modulo - Módulo del sistema
 * @param {number} id_modulo - ID del registro afectado
 */
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

// =============================================
// INICIALIZACIÓN
// =============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 Panel de Cajera inicializado');
    actualizarDashboard();
    iniciarActualizacion();

    // Sincronizar margen del contenido con el ancho del sidebar
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
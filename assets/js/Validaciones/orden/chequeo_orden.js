document.addEventListener('DOMContentLoaded', function () {
    function formatCurrency(value) {
        return '$ ' + Number(value || 0).toFixed(2);
    }

    function estadoLabel(estado) {
        if (estado === 1 || estado === '1') return 'Reparación';
        if (estado === 0 || estado === '0') return 'Pendiente';
        return estado || '-';
    }

    function mostrarDetallesOrden(data) {
        document.getElementById('orden_id').textContent = data.id_orden || '-';
        document.getElementById('orden_fecha').textContent = data.fecha_registro || '-';
        document.getElementById('orden_estado').textContent = estadoLabel(data.estado);
        document.getElementById('orden_cliente').textContent = data.cliente || '-';
        document.getElementById('orden_observaciones').textContent = data.observaciones || '-';
        document.getElementById('orden_repuestos_total').textContent = formatCurrency(data.total_repuestos || 0);
        document.getElementById('orden_reparacion_total').textContent = formatCurrency(data.monto_reparacion || 0);
        document.getElementById('orden_total').textContent = formatCurrency(data.total_orden || 0);

        const itemsBody = document.getElementById('orden_items');
        itemsBody.innerHTML = '';

        const repuestos = Array.isArray(data.repuestos) ? data.repuestos : [];
        if (repuestos.length === 0) {
            itemsBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No hay repuestos o servicios registrados.</td></tr>';
            return;
        }

        repuestos.forEach(item => {
            const cantidad = Number(item.cantidad || 0);
            const precio = Number(item.precio || 0);
            const subtotal = cantidad * precio;
            itemsBody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td>${item.nombre || '-'}</td>
                    <td class="text-end">${cantidad}</td>
                    <td class="text-end">${formatCurrency(precio)}</td>
                    <td class="text-end">${formatCurrency(subtotal)}</td>
                </tr>
            `);
        });
    }

    function alertas(accion, texto = 'Proceso ejecutado con éxito!', titulo = 'Listo!') {
        if (typeof Swal === 'undefined') {
            alert(texto);
            return;
        }

        if (accion === 'error' || accion === 'errorC') {
            Swal.fire({
                title: titulo,
                text: texto,
                icon: 'error',
                color: 'white',
                showConfirmButton: true,
                confirmButtonColor: 'rgb(238, 191, 0)',
                background: '#000910',
            });
        } else if (accion === 'warning') {
            Swal.fire({
                title: titulo,
                text: texto,
                icon: 'warning',
                color: 'white',
                showConfirmButton: true,
                confirmButtonColor: 'rgb(238, 191, 0)',
                background: '#000910',
            });
        } else {
            Swal.fire({
                title: titulo,
                text: texto,
                icon: 'success',
                color: 'white',
                showConfirmButton: false,
                confirmButtonColor: 'rgb(238, 191, 0)',
                background: '#000910',
                timer: 1500,
            });
        }
    }

    function consultarOrden(id) {
        if (!id) {
            alertas('error', 'ID de orden inválido para consultar.', 'Error');
            return;
        }

        $.ajax({
            type: 'POST',
            url: '',
            data: {
                id: id,
                accion: 'consultar'
            },
            dataType: 'json',
            success: function (data) {
                if (data.error) {
                    alertas('error', data.error, 'Error');
                    return;
                }

                mostrarDetallesOrden(data);
                const modalEl = document.getElementById('modalVerOrden');
                if (modalEl) {
                    const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    bsModal.show();
                }
            },
            error: function (xhr, status, error) {
                console.error('Error al consultar orden:', error);
                alertas('error', 'No se pudo obtener el detalle de la orden.', 'Error');
            }
        });
    }

    function renderProductosModal(repuestos) {
        const lista = document.getElementById('lista_productos_solicitud');
        if (!lista) {
            return;
        }

        if (!Array.isArray(repuestos) || repuestos.length === 0) {
            lista.innerHTML = '<div class="text-muted">No hay repuestos o servicios registrados.</div>';
            return;
        }

        lista.innerHTML = '';
        repuestos.forEach(item => {
            const nombre = item.nombre || item.nombre_producto || '-';
            const cantidad = Number(item.cantidad || 0);
            const precio = Number(item.precio || item.precio_unitario || 0);
            const subtotal = cantidad * precio;
            lista.insertAdjacentHTML('beforeend', `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded" style="background-color: rgba(13, 110, 253, 0.08);">
                    <span>${nombre}</span>
                    <span>${cantidad} x ${formatCurrency(precio)} = ${formatCurrency(subtotal)}</span>
                </div>
            `);
        });
    }

    function abrirModalProcesarOrden(id) {
        if (!id) {
            alertas('error', 'ID de orden inválido para procesar.', 'Error');
            return;
        }

        const modalElement = document.getElementById('modalProcesarSolicitud');
        if (!modalElement) {
            alertas('error', 'No se encontró el modal de procesamiento.', 'Error');
            return;
        }

        document.getElementById('id_solicitud_input').value = id;
        document.getElementById('num_solicitud_modal').textContent = id;
            document.getElementById('pago_referencia').value = '';
            if (document.getElementById('metodo_pago')) document.getElementById('metodo_pago').value = 'Efectivo';
        document.getElementById('total_modal_ver').textContent = 'Cargando...';
        renderProductosModal([]);

        const modal = bootstrap?.Modal ? bootstrap.Modal.getOrCreateInstance(modalElement) : null;
        if (modal) {
            modal.show();
        } else {
            alertas('error', 'Bootstrap no está disponible para abrir el modal.', 'Error');
            return;
        }

        fetch('?pagina=chequeo_orden', {
            method: 'POST',
            body: new URLSearchParams({
                accion: 'consultar',
                id: id
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Respuesta del servidor no válida.');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alertas('error', data.error, 'Error');
                return;
            }

            document.getElementById('total_modal_ver').textContent = formatCurrency(data.total_orden || 0);
            renderProductosModal(data.repuestos);
        })
        .catch(error => {
            console.error('Error al obtener orden para procesar:', error);
            alertas('error', 'No se pudo obtener la orden para procesar.', 'Error');
        });
    }

    function enviarPagoOrden() {
        const id = document.getElementById('id_solicitud_input').value;
        const referencia = document.getElementById('pago_referencia').value.trim();
        const metodoPago = document.getElementById('metodo_pago') ? document.getElementById('metodo_pago').value : '';

        if (!referencia) {
            alertas('error', 'La referencia de pago es obligatoria.', 'Atención');
            return;
        }

        fetch('?pagina=chequeo_orden', {
            method: 'POST',
            body: new URLSearchParams({
                accion: 'procesar',
                id: id,
                pago_referencia: referencia,
                metodo_pago: metodoPago
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alertas('success', result.success, 'Éxito');
                const modalElement = document.getElementById('modalProcesarSolicitud');
                bootstrap.Modal.getInstance(modalElement)?.hide();
                if (typeof $ !== 'undefined' && $.fn.DataTable) {
                    $('#chequeoOrdenTabla').DataTable().ajax.reload(null, false);
                } else {
                    window.location.reload();
                }
            } else {
                alertas('error', result.error || 'No se pudo procesar la orden.', 'Error');
            }
        })
        .catch(error => {
            console.error('Error al procesar pago de orden:', error);
            alertas('error', 'Error de comunicación con el servidor.', 'Error');
        });
    }

    function inicializarTablaChequeo() {
        if (typeof $ === 'undefined' || !$.fn.DataTable) {
            return;
        }

        $('#chequeoOrdenTabla').DataTable({
            destroy: true,
            ajax: {
                url: '?pagina=chequeo_orden&ajax=true',
                dataSrc: ''
            },
            columns: [
                { data: 'id_orden' },
                { data: 'cliente' },
                { data: 'equipo' },
                {
                    data: 'estado',
                    render: function (data) {
                        return estadoLabel(data);
                    }
                },
                {
                    data: null,
                    render: function (data) {
                        return formatCurrency(data.total_orden || 0);
                    }
                },
                { data: 'fecha_registro' },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        return `<div class="btn-group" role="group">
                            <button type="button" class="btn btn-info btn-sm btn-ver-orden" data-id="${data.id_orden ?? ''}"><i class="bi bi-eye-fill"></i></button>
                            <button type="button" class="btn btn-success btn-sm btn-procesar-orden" data-id="${data.id_orden ?? ''}">Procesar</button>
                            <button type="button" class="btn btn-danger btn-sm btn-eliminar-orden" data-id="${data.id_orden ?? ''}">Eliminar</button>
                        </div>`;
                    }
                }
            ],
            pageLength: 5,
            lengthMenu: [[5, 10, 15], ['5', '10', '15']],
            columnDefs: [{ className: 'dt-head-center', targets: '_all' }],
            language: {
    search: "Buscar:",
    lengthMenu: "Mostrar _MENU_ registros por página",
    info: "Mostrando de _START_ a _END_ de _TOTAL_ registros",
    infoEmpty: "Mostrando 0 a 0 de 0 registros",
    zeroRecords: "No se encontraron resultados",
    emptyTable: "No hay datos disponibles en la tabla",
    paginate: { 
        first: "Primero", 
        last: "Último", 
        next: "Siguiente", 
        previous: "Anterior" 
    }
}
        });
    }

    $(document).on('click', '.btn-ver-orden', function () {
        const id = $(this).data('id');
        consultarOrden(id);
    });

    $(document).on('click', '.btn-procesar-orden', function () {
        const id = $(this).data('id');
        console.log('Procesar orden clic', id);
        abrirModalProcesarOrden(id);
    });

    const btnFinalizarPedido = document.getElementById('btnFinalizarPedido');
    if (btnFinalizarPedido) {
        btnFinalizarPedido.addEventListener('click', function () {
            enviarPagoOrden();
        });
    }

    $(document).on('click', '.btn-eliminar-orden', function () {
        const id = $(this).data('id');
        if (!id) {
            alertas('error', 'ID de orden inválido.', 'Error');
            return;
        }

        Swal.fire({
            title: 'Eliminar orden',
            text: '¿Deseas eliminar esta orden pendiente? Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            color: 'white',
            background: '#000910',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            fetch('?pagina=chequeo_orden', {
                method: 'POST',
                body: new URLSearchParams({
                    accion: 'eliminar',
                    id: id
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alertas('success', result.success, 'Éxito');
                    if (typeof $ !== 'undefined' && $.fn.DataTable) {
                        $('#chequeoOrdenTabla').DataTable().ajax.reload(null, false);
                    } else {
                        window.location.reload();
                    }
                } else {
                    alertas('error', result.error || 'No se pudo eliminar la orden.', 'Error');
                }
            })
            .catch(error => {
                console.error('Error al eliminar orden:', error);
                alertas('error', 'Error de comunicación con el servidor.', 'Error');
            });
        });
    });

    inicializarTablaChequeo();
});

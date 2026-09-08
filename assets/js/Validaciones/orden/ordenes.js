$(document).ready(function () {
    let permisos = { registrar: false, modificar: false, eliminar: false, consultar: false, control_total: false };

    $.get("?pagina=orden&permisos=true", function (data) {
        try { permisos = (typeof data === 'string') ? JSON.parse(data) : data; } catch (e) { permisos = data; }

        if (permisos.control_total || permisos.registrar) {
            $("#btnRegistrarOrden").show();
        } else {
            $("#btnRegistrarOrden").hide();
        }

        cargarTablaOrdenes();
    });

    function cargarTablaOrdenes() {
        if (!$.fn.DataTable) {
            console.error('DataTables no está cargado en la página.');
            return;
        }
        $("#ordenTabla").DataTable({
            destroy: true,
            ajax: {
                url: "?pagina=orden&ajax=true",
                dataType: 'json',
                dataSrc: function (json) {
                    if (Array.isArray(json)) return json;
                    if (json && typeof json === 'object' && Array.isArray(json.data)) return json.data;
                    console.error('ordenTabla: respuesta AJAX inesperada', json);
                    return [];
                },
                error: function (xhr, error, thrown) {
                    console.error('ordenTabla AJAX error', error, thrown, xhr.responseText);
                }
            },
            columns: [
                { data: "id_orden" },
                { data: "cliente" },
                { data: "equipo" },
                {
                    data: "horas_estimadas",
                    render: function (data) { return data ? data : '-'; }
                },
                {
                    data: "estado",
                    render: function (data) {
                        if (data === 1 || data === '1') return 'Reparación';
                        if (data === 0 || data === '0') return 'Pendiente';
                        return data || '-';
                    }
                },
                {
                    data: "total_orden",
                    render: function (data) { return '$ ' + (Number(data||0)).toFixed(2); }
                },
                { data: "fecha_registro" },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (row) {
                        var btns = `<div class="btn-group" role="group">`;
                        if (permisos.control_total || permisos.consultar) {
                            btns += `<button type="button" class="btn btn-info btn-verOrden" data-id="${row.id_orden}"><i class="bi bi-eye-fill"></i></button>`;
                        }
                        if (permisos.control_total || permisos.modificar) {
                            btns += `<button type="button" class="btn btn-warning btn-editarOrden" data-id="${row.id_orden}"><i class="fa-solid fa-pen-to-square"></i></button>`;
                        }
                        if (permisos.control_total || permisos.eliminar) {
                            btns += `<button type="button" class="btn btn-danger btn-eliminarOrden" data-id="${row.id_orden}"><i class="fa-solid fa-trash-can"></i></button>`;
                        }
                        btns += `</div>`;
                        return btns === `<div class="btn-group" role="group"></div>` ? '<span class="badge bg-secondary">Solo lectura</span>' : btns;
                    }
                }
            ],
            pageLength: 6,
            lengthMenu: [[6, 12, 24], ['6', '12', '24']],
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

    $(document).on('click', '.btn-verOrden', function () {
        const id = $(this).data('id');
        if (!id) return;
        $.post('?pagina=orden', { id: id, accion: 'consultar' }, function (res) {
            try { res = (typeof res === 'string') ? JSON.parse(res) : res; } catch (e) {}
            if (res.error) { alert(res.error); return; }
            // usar modal existente para mostrar detalles
            $('#orden_id').text(res.id_orden || '-');
            $('#orden_fecha').text(res.fecha_registro || '-');
            $('#orden_horas_estimadas').text(res.horas_estimadas || '-');
            $('#orden_estado').text(res.estado==1? 'Reparación':'Pendiente');
            $('#orden_cliente').text(res.cliente || '-');
            $('#orden_observaciones').text(res.observaciones || '-');
            $('#orden_repuestos_total').text('$ ' + (Number(res.total_repuestos||0)).toFixed(2));
            $('#orden_reparacion_total').text('$ ' + (Number(res.monto_reparacion||0)).toFixed(2));
            $('#orden_total').text('$ ' + (Number(res.total_orden||0)).toFixed(2));
            var items = Array.isArray(res.repuestos)?res.repuestos:[];
            var body = $('#orden_items'); body.empty();
            if (items.length===0) body.append('<tr><td colspan="4" class="text-center text-muted">No hay repuestos o servicios registrados.</td></tr>');
            else items.forEach(function(item){ body.append(`<tr><td>${item.nombre||'-'}</td><td class="text-end">${Number(item.cantidad||0)}</td><td class="text-end">$ ${(Number(item.precio||0)).toFixed(2)}</td><td class="text-end">$ ${(Number(item.cantidad||0)*Number(item.precio||0)).toFixed(2)}</td></tr>`); });
            var modal = new bootstrap.Modal(document.getElementById('modalVerOrden'));
            modal.show();
        });
    });

    // Nuevo registro: limpiar id oculto antes de abrir modal
    $(document).on('click', '#btnRegistrarOrden', function () {
        try {
            $('#orden_id_tecnico').val('');
            $('#formularioOrdenTecnico')[0].reset();
            $('#orden_marca_tecnico').val('');
            $('#orden_repuestos_rows').empty();
            $('#orden_repuestos_rows').append(`<tr class="orden-repuesto-row"><td><input type="text" class="form-control form-control-sm repuesto-nombre" placeholder="Ej: Pantalla"></td><td><input type="number" min="1" value="1" class="form-control form-control-sm repuesto-cantidad"></td><td><input type="number" min="0" step="0.01" value="0.00" class="form-control form-control-sm repuesto-precio"></td><td class="text-end repuesto-subtotal">$ 0.00</td><td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-repuesto">x</button></td></tr>`);
        } catch (e) { console.warn(e); }
    });

    // Editar orden: cargar en modal para modificar
    $(document).on('click', '.btn-editarOrden', function () {
        const id = $(this).data('id');
        if (!id) return;
        $.post('?pagina=orden', { id: id, accion: 'consultar' }, function (res) {
            try { res = (typeof res === 'string') ? JSON.parse(res) : res; } catch (e) {}
            if (res.error) { Swal.fire({ title: 'Error', text: res.error, icon: 'error', color: 'white', background: '#000910' }); return; }
            // rellenar modal tecnico
            $('#orden_id_tecnico').val(res.id_orden || '');
            $('#orden_cliente_tecnico').val(res.cliente || res.cedula_cliente || '');
            $('#orden_marca_tecnico').val(res.id_marca || '');
            $('#orden_estado_tecnico').val(res.estado);
            $('#orden_equipo_tecnico').val(res.equipo || '');
            $('#orden_imei_tecnico').val(res.imei || '');
            $('#orden_horas_estimadas_tecnico').val(res.horas_estimadas || '00:00');
            $('#orden_observaciones_tecnico').val(res.observaciones || '');

            // repuestos
            const rows = $('#orden_repuestos_rows');
            rows.empty();
            const items = Array.isArray(res.repuestos) ? res.repuestos : [];
            let repuestosTotal = 0;
            if (items.length === 0) {
                rows.append(`<tr class="orden-repuesto-row"><td><input type="text" class="form-control form-control-sm repuesto-nombre" placeholder="Ej: Pantalla"></td><td><input type="number" min="1" value="1" class="form-control form-control-sm repuesto-cantidad"></td><td><input type="number" min="0" step="0.01" value="0.00" class="form-control form-control-sm repuesto-precio"></td><td class="text-end repuesto-subtotal">$ 0.00</td><td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-repuesto">x</button></td></tr>`);
            } else {
                items.forEach(function (it) {
                    const cantidad = Number(it.cantidad || 0);
                    const precio = Number(it.precio || 0);
                    repuestosTotal += cantidad * precio;
                    rows.append(`<tr class="orden-repuesto-row"><td><input type="text" class="form-control form-control-sm repuesto-nombre" value="${(it.nombre||'')}"></td><td><input type="number" min="1" value="${cantidad}" class="form-control form-control-sm repuesto-cantidad"></td><td><input type="number" min="0" step="0.01" value="${precio.toFixed(2)}" class="form-control form-control-sm repuesto-precio"></td><td class="text-end repuesto-subtotal">$ ${(cantidad*precio).toFixed(2)}</td><td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-repuesto">x</button></td></tr>`);
                });
            }

            // totales
            $('#orden_repuestos_total_tecnico').val('$ ' + (repuestosTotal).toFixed(2));
            $('#orden_total_tecnico').val('$ ' + (Number(res.total_orden || 0)).toFixed(2));

            // mostrar modal
            var modal = new bootstrap.Modal(document.getElementById('modalOrdenTecnico'));
            modal.show();
        }).fail(function () { Swal.fire({ title: 'Error', text: 'No se pudo obtener la orden', icon: 'error', color: 'white', background: '#000910' }); });
    });

    // Eliminar orden
    $(document).on('click', '.btn-eliminarOrden', function () {
        const id = $(this).data('id');
        if (!id) return;
        Swal.fire({
            title: 'Confirmar eliminación',
            text: '¿Estás seguro de eliminar esta orden? Esta acción no se puede deshacer.',
            icon: 'warning',
            color: 'white',
            background: '#000910',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('?pagina=orden', { id: id, accion: 'eliminar' }, function (res) {
                    try { res = (typeof res === 'string') ? JSON.parse(res) : res; } catch (e) {}
                    if (res.success) {
                        Swal.fire({ title: 'Eliminado', text: res.success, icon: 'success', color: 'white', background: '#000910', timer: 1200, showConfirmButton: false });
                        try { $('#ordenTabla').DataTable().ajax.reload(); } catch (e) { }
                    } else {
                        Swal.fire({ title: 'Error', text: res.error || 'No se pudo eliminar', icon: 'error', color: 'white', background: '#000910' });
                    }
                }).fail(function () { Swal.fire({ title: 'Error', text: 'Error en la comunicación', icon: 'error', color: 'white', background: '#000910' }); });
            }
        });
    });

});

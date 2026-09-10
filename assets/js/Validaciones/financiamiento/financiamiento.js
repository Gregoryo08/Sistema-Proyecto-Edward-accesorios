$(document).ready(function () {
    let permisos = { registrar: false, modificar: false, eliminar: false, registrar_pago: false };

    $.get("?pagina=financiamiento&permisos=true", function (data) {
        permisos = JSON.parse(data);
        if (permisos.registrar) {
            $("#btn_nuevo_financiamiento").show();
        }
        inicializarTablaFinanciamiento();
    });

   function inicializarTablaFinanciamiento() {
    $("#financiamientotabla").DataTable({
        destroy: true,
        ajax: {
            url: "?pagina=financiamiento&ajax=true&x=listado",
            dataSrc: ""
        },
        columns: [
            { data: "id_financiamiento", visible: false },
            {
                data: null,
                render: function (data, type, row) {
                    return `${row.nombre} ${row.apellido} <br><small class="text-muted">${row.cedula_persona}</small>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `${row.nombre_producto} <br><small class="text-muted">${row.imei}</small>`;
                }
            },
            {
                data: "monto_total",
                render: function (data) { return `$${parseFloat(data).toFixed(2)}`; }
            },
            {
                data: "saldo_pendiente",
                render: function (data) {
                    let monto = parseFloat(data).toFixed(2);
                    let color = monto > 0 ? 'text-danger fw-bold' : 'text-success fw-bold';
                    return `<span class="${color}">$${monto}</span>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    let montoCuota = parseFloat(row.monto_cuota).toFixed(2);
                    return `<b class="text-primary">$${montoCuota}</b> <br> 
                    <span class="badge badge-cuotas">${row.pagadas} / ${row.cantidad_cuotas}</span>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    if (!row.proximo_vencimiento) return '<span class="badge bg-success">PAGADO</span>';
                    let dias = parseInt(row.dias_restantes);
                    let clase = "text-dark";
                    let texto = `En ${dias} días`;

                    if (dias < 0) {
                        clase = "text-danger fw-bold";
                        texto = `ATRASADO (${Math.abs(dias)} d)`;
                    } else if (dias === 0) {
                        clase = "text-warning fw-bold";
                        texto = "VENCE HOY";
                    }
                    return `<span class="${clase}">${texto}</span><br><small class="text-muted">${row.proximo_vencimiento}</small>`;
                }
            },
            {
                data: "estado_equipo",
                render: function (data) {
                    let color = (data === 'activo') ? 'success' : 'danger';
                    return `<span class="badge bg-${color}">${data.toUpperCase()}</span>`;
                }
            },
            {
                data: "estado_financiamiento",
                render: function (data) {
                    let color = (data === 'vigente') ? 'primary' : (data === 'finalizado' ? 'success' : (data === 'anulado' ? 'secondary' : 'warning'));
                    return `<span class="badge bg-${color}">${data.toUpperCase()}</span>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    let btnSeguimiento = permisos.registrar_pago ? 
                        `<button class="btn btn-primary btn-sm btn-seguimiento" data-id="${row.id_financiamiento}"><i class="bi bi-list-check"></i></button>` : '';

                    let btnModificar = permisos.modificar ? 
                        `<button class="btn btn-outline-primary btn-sm btn-modificar" data-id="${row.id_financiamiento}" title="Modificar"><i class="bi bi-pencil-square"></i></button>` : '';

                    let btnBloqueo = permisos.modificar ? 
                        `<button class="btn btn-outline-${row.estado_equipo === 'activo' ? 'danger' : 'success'} btn-sm btn-cambiar-estado" data-id="${row.id_financiamiento}" data-estado="${row.estado_equipo === 'activo' ? 'bloqueado' : 'activo'}" title="${row.estado_equipo === 'activo' ? 'Bloquear' : 'Desbloquear'}"><i class="bi ${row.estado_equipo === 'activo' ? 'bi-lock-fill' : 'bi-unlock-fill'}"></i></button>` : '';

                    let btnFinalizar = permisos.modificar && (row.estado_financiamiento === 'vigente') ?
                        `<button class="btn btn-outline-dark btn-sm btn-finalizar-contrato" data-id="${row.id_financiamiento}" title="Finalizar"><i class="bi bi-check-all"></i></button>` : '';

                    let btnAnular = permisos.modificar && (row.pagadas == 0 && row.estado_financiamiento === 'vigente') ?
                        `<button class="btn btn-outline-danger btn-sm btn-anular" data-id="${row.id_financiamiento}" title="Anular"><i class="bi bi-trash"></i></button>` : '';

                    let acciones = [];
                    if (btnSeguimiento) acciones.push(btnSeguimiento);
                    if (btnModificar) acciones.push(btnModificar);
                    if (btnBloqueo) acciones.push(btnBloqueo);
                    if (btnFinalizar) acciones.push(btnFinalizar);
                    if (btnAnular) acciones.push(btnAnular);

                    return `<div class="btn-group" role="group">
                                ${acciones.join('')}
                            </div>`;
                }
            }
        ],
        language: {
            "decimal": "",
            "emptyTable": "No hay datos disponibles en la tabla",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron registros coincidentes",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": activar para ordenar la columna ascendente",
                "sortDescending": ": activar para ordenar la columna descendente"
            }
        }
    });
}



    let listaClientesGlobal = [];
   

   function cargarCatalogos() {
$.get("?pagina=financiamiento&ajax=true&x=clientes", function (res) {
    const clientes = typeof res === 'string' ? JSON.parse(res) : res;
    listaClientesGlobal = clientes;
    
    if ($("#cedula_persona").hasClass("select2-hidden-accessible")) {
        $("#cedula_persona").select2('destroy');
    }
    
    let opt = '<option value="" selected disabled>Seleccionar Cliente</option>';
    
    
    clientes.forEach(c => {
        opt += `<option value="${c.cedula_persona}">${c.cedula_persona} - ${c.nombre} ${c.apellido}</option>`;
    });
    
    $("#cedula_persona").html(opt);
    
    $("#cedula_persona").select2({
        width: '100%',
        placeholder: 'Seleccionar Cliente',
        allowClear: true,
        dropdownParent: $('#modalRegistroFinanciamiento'),
        language: {
            noResults: function() { return "No se encontraron resultados"; },
            searching: function() { return "Buscando..."; }
        }
    });
});

    $.get("?pagina=financiamiento&ajax=true&x=telefonos_disponibles", function (res) {
        const equipos = typeof res === 'string' ? JSON.parse(res) : res;
        if ($("#id_telefono").hasClass("select2-hidden-accessible")) {
            $("#id_telefono").select2('destroy');
        }
        let opt = '<option value="" selected disabled>Seleccionar Teléfono</option>';
        equipos.forEach(e => {
            opt += `<option value="${e.id_unidad}" data-precio="${e.precio_detal}" data-id-producto="${e.id_producto}">${e.nombre_producto} (${e.imei})</option>`;
        });
        $("#id_telefono").html(opt);
        $("#id_telefono").select2({
            width: '100%',
            placeholder: 'Seleccionar Teléfono',
            allowClear: true,
            dropdownParent: $('#modalRegistroFinanciamiento'),
            language: {
                noResults: function() { return "No se encontraron resultados"; },
                searching: function() { return "Buscando..."; }
            }
        });
    });

    $("#id_telefono").on("change", function () {
        const precio = $(this).find(":selected").data("precio");
        if (precio) {
            const numeroLimpio = parseFloat(precio);
            $("#monto_total").val(numeroLimpio).trigger("input");
            const total = parseFloat($("#monto_total").val()) || 0;
            const inicial = parseFloat($("#pago_inicial").val()) || 0;
            const cuotas = parseInt($("#cantidad_cuotas").val()) || 0;
            if (cuotas > 0) {
                const cuota = (total - inicial) / cuotas;
                $("#cuota_estimada").text(`$ ${cuota.toFixed(2)}`);
            }
        } else {
            $("#monto_total").val("").trigger("input");
            $("#cuota_estimada").text("$ 0.00");
        }
    });

    $.get("?pagina=financiamiento&ajax=true&x=metodos", function (res) {
        const metodos = typeof res === 'string' ? JSON.parse(res) : res;
        let select = $("#id_metodopago");
        select.empty().append('<option value="" selected disabled>Seleccione método...</option>');
        metodos.forEach(m => select.append(`<option value="${m.id_metodopago}">${m.nombre_metodopago}</option>`));
    });

    $.get("?pagina=financiamiento&ajax=true&x=bancos", function (res) {
        const bancos = typeof res === 'string' ? JSON.parse(res) : res;
        let select = $("#id_banco");
        select.empty().append('<option value="" selected disabled>Seleccione banco...</option>');
        bancos.forEach(b => select.append(`<option value="${b.id_banco}">${b.nombre_banco}</option>`));
    });
}


$(document).on('select2:select select2:close', 'select', function (e) {
    const $el = $(this);
    const id = $el.attr('id');
    
    
    if (id === 'cedula_persona' || id === 'id_telefono') return;

    const valor = $el.val();
    
    if (valor === "" || valor === null || valor.length === 0) {
        gestionarError(id, true);
    } else {
        gestionarError(id, false);
    }
});

    $(document).on('change', '#cedula_persona', function () {
        const cedulaSeleccionada = $(this).val();
        const clienteEncontrado = listaClientesGlobal.find(c => c.cedula_persona == cedulaSeleccionada);

        if (clienteEncontrado) {
            if (clienteEncontrado.ingresos_mensuales === null || parseFloat(clienteEncontrado.ingresos_mensuales) === 0) {
                $('#seccion_perfil_crediticio').removeClass('d-none');
                $('#btn_evaluar_directo').addClass('d-none');
                $('#btn_guardar_perfil_primero').removeClass('d-none');

                $('#tipo_residencia').val('Familiar');
                $('#carga_familiar').val(0);
                $('#estado_civil').val('Soltero');
                $('#profesion').val('Empleado');
                $('#ocupacion').val('');
                $('#ingresos_mensuales').val('');
            } else {
                $('#seccion_perfil_crediticio').addClass('d-none');
                $('#btn_evaluar_directo').removeClass('d-none');
                $('#btn_guardar_perfil_primero').addClass('d-none');

                $('#tipo_residencia').val(clienteEncontrado.tipo_residencia);
                $('#carga_familiar').val(clienteEncontrado.carga_familiar);
                $('#estado_civil').val(clienteEncontrado.estado_civil);
                $('#profesion').val(clienteEncontrado.profesion);
                $('#ocupacion').val(clienteEncontrado.ocupacion);
                $('#ingresos_mensuales').val(clienteEncontrado.ingresos_mensuales);
            }
        }
    });

$(document).on("click", ".btn-seguimiento", function () {
    const id = $(this).data("id");
    const fila = $("#financiamientotabla").DataTable().row($(this).parents('tr')).data();
    $("#id_finan_pago").val(id);
    
    cargarCatalogos();
    
    $.post("?pagina=financiamiento", { accion: "consultarCuotas", id: id }, function (res) {
        const cuotas = typeof res === 'string' ? JSON.parse(res) : res;
        let html = "";
        
        cuotas.forEach(c => {
            const estado = (c.estado_cuota || "").toLowerCase();
            let badge = estado === 'pagado' ? 'success' : (estado === 'en_revision' ? 'danger' : 'warning');
            let acciones = '';
            
            
             if (estado === 'en_revision') {
                acciones = `
                    <button class="btn btn-sm btn-success me-1" onclick="gestionarPago(${c.id_cuota}, 'aprobarPago')">Aprobar</button>
                    <button class="btn btn-sm btn-danger" onclick="gestionarPago(${c.id_cuota}, 'negarPago')">Negar</button>
                `;
            }

            let detallePago = (estado === 'pagado' || estado === 'en_revision') ?
                `<small>
                    <b>${c.nombre_metodopago || 'N/A'}</b>
                    ${c.nombre_banco ? '<br>' + c.nombre_banco : ''}
                    ${c.referencia ? '<br><span class="text-muted">Ref: ' + c.referencia + '</span>' : ''}
                </small>` : '-';

            html += `<tr>
                <td>${c.numero_cuota}</td>
                <td>${c.fecha_vencimiento}</td>
                <td>$${parseFloat(c.monto_pagado || 0).toFixed(2)}</td>
                <td><span class="badge bg-${badge}">${estado.toUpperCase()}</span></td>
                <td>${detallePago}</td>
                <td>${c.fecha_pago_realizado || '-'}</td>
                <td>${acciones}</td>
            </tr>`;
        });
        
        $("#cuerpoSeguimiento").html(html);
        new bootstrap.Modal('#modalSeguimientoPagos').show();
    });
});

window.gestionarPago = function(id_cuota, accion) {
    $.post("?pagina=financiamiento", { accion: accion, id_cuota: id_cuota }, function(res) {
        if (res.success) {
            $("#financiamientotabla").DataTable().ajax.reload();
            $("#modalSeguimientoPagos").modal('hide');
        } else {
            alert(res.error || "Error al procesar");
        }
    }, 'json');
};

    $(document).on("click", ".btn-abrir-pago", function () {
        const d = $(this).data();
        $("#id_cuota_pago").val(d.id);
        $("#monto_pago_input").val(d.monto);
        $("#detalle_cuota_pago").html(`Cuota Nro: <b>${d.numero}</b> | Monto Fijo: <b>$${d.monto}</b>`);
        $.get("?pagina=financiamiento&ajax=true&x=tasa_bcv", function (res) {
            const data = typeof res === 'string' ? JSON.parse(res) : res;
            if (data.tasa) { $("#tasa_dia").val(data.tasa); calcularConversion(); }
        });
        new bootstrap.Modal('#modalRegistrarPago').show();
    });

    $(document).on("click", ".btn-cambiar-estado", function () {
        const d = $(this).data();
        procesarPeticion(`id=${d.id}&estado=${d.estado}&accion=cambiarEstadoEquipo`, "ninguno");
    });

    $(document).on("click", ".btn-finalizar-contrato", function () {
        const id = $(this).data("id");
        Swal.fire({
            title: '¿Finalizar Contrato?',
            text: "El equipo quedará activo y el financiamiento se marcará como completado.",
            icon: 'warning',
            background: "#000910",
            color: "white",
            showCancelButton: true,
            confirmButtonColor: '#198754',
            confirmButtonText: 'Sí, finalizar'
        }).then((result) => {
            if (result.isConfirmed) procesarPeticion(`id=${id}&accion=finalizarContrato`, "ninguno");
        });
    });

    $(document).on("click", ".btn-anular", function () {
        const id = $(this).data("id");
        Swal.fire({
            title: '¿Anular Financiamiento?',
            text: "Esta acción liberará el equipo y cancelará el contrato permanentemente.",
            icon: 'error',
            background: "#000910",
            color: "white",
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, anular'
        }).then((result) => {
            if (result.isConfirmed) procesarPeticion(`id=${id}&accion=anularFinanciamiento`, "ninguno");
        });
    });

    $("#btn_confirmar_pago").click(function () {
        const datos = $("#formularioPagoCuota").serialize() + "&accion=registrarPagoCuota";
        procesarPeticion(datos, "#modalRegistrarPago");
    });

   function procesarPeticion(datos, modalId) {
    if (modalId !== "ninguno") {
        const form = $(modalId + " form");
        let esValido = true;
        let mensajeError = "";

        

        ['cedula_persona', 'id_telefono'].forEach(id => {
            const val = form.find('#' + id).val();
            const existe = form.find('#' + id + ' option[value="' + val + '"]').length > 0;
            
            if (val && !existe) {
                esValido = false;
                mensajeError = "Se ha detectado una manipulación en los campos del formulario.";
            }
        });

        if (!esValido) {
            Swal.fire({
                title: "Acceso Denegado",
                text: mensajeError,
                icon: "error",
                background: "#000910",
                color: "white"
            });
            return;
        }
    }

    const SWAL_CFG = { background: "#000910", color: "white", confirmButtonColor: "#0d6efd" };
    Swal.fire({ title: "Procesando", ...SWAL_CFG, didOpen: () => Swal.showLoading() });
    
    $.ajax({
        type: "POST",
        url: window.location.href,
        data: datos,
        success: function (response) {
            Swal.close();
            let res;
            try {
                res = typeof response === 'object' ? response : JSON.parse(response);
                if (res.success) {
                    Swal.fire({ ...SWAL_CFG, title: "Éxito", icon: "success", timer: 1000, showConfirmButton: false });
                    if (modalId !== "ninguno") $(modalId).modal('hide');
                    if (modalId === "#modalRegistrarPago") {
                        $("#modalRegistrarPago").modal('hide');
                        $("#modalSeguimientoPagos").modal('hide');
                    }
                    if (typeof cargarCatalogos === "function") cargarCatalogos();
                    if ($.fn.DataTable.isDataTable("#financiamientotabla")) {
                        $("#financiamientotabla").DataTable().ajax.reload(null, false);
                    }
                } else {
                    Swal.fire({ ...SWAL_CFG, title: "Error", text: res.error || "Error", icon: "error" });
                }
            } catch (e) {
                Swal.fire({ ...SWAL_CFG, title: "Error", text: "Respuesta inválida del servidor", icon: "error" });
            }
        },
        error: function() {
            Swal.fire({ ...SWAL_CFG, title: "Error", text: "Error de conexión con el servidor", icon: "error" });
        }
    });
}

    function calcularConversion() {
        let tasa = parseFloat($("#tasa_dia").val()) || 0;
        let dolar = parseFloat($("#monto_pago_input").val()) || 0;
        let totalBs = tasa * dolar;
        $("#monto_bs_calculado").text(totalBs.toLocaleString('es-VE', { minimumFractionDigits: 2 }) + " Bs");
    }

    $(document).on("input", "#tasa_dia, #monto_pago_input", calcularConversion);

$("#btn_registrar").click(function () {
    const formulario = $("#formularioRegistroFinanciamiento");
    
    if ($("#id_telefono").val() === null || $("#id_telefono").val() === "") {
        Swal.fire({
            title: "Error",
            text: "Debe seleccionar un equipo de la lista",
            icon: "warning",
            background: "#000910",
            color: "white"
        });
        return;
    }

    const idProducto = $("#id_telefono").find(':selected').data('id-producto');
    $("#input_id_producto").val(idProducto);

    const datos = formulario.serialize() + "&accion=registrarFinanciamiento";
    
    procesarPeticion(datos, "#modalRegistroFinanciamiento");
});

    $("#monto_total, #pago_inicial, #cantidad_cuotas").on("input", function () {
        const total = parseFloat($("#monto_total").val()) || 0;
        const inicial = parseFloat($("#pago_inicial").val()) || 0;
        const cuotas = parseInt($("#cantidad_cuotas").val()) || 0;
        $("#cuota_estimada").text(`$ ${cuotas > 0 ? ((total - inicial) / cuotas).toFixed(2) : '0.00'}`);
    });

    $(document).on("click", "#btn_nuevo_financiamiento", function () {
   
    if ($("#cedula_persona option").length <= 1) {
        cargarCatalogos();
    }
    new bootstrap.Modal('#modalRegistroFinanciamiento').show();
});

    $("#id_metodopago").change(function () {
        const texto = $(this).find('option:selected').text().toLowerCase();
        (texto.includes("pago movil") || texto.includes("transferencia")) ? $("#contenedor_banco").fadeIn() : $("#contenedor_banco").fadeOut();
    });

    cargarCatalogos();



   function calcularCuotaEstimada() {
    const total = parseFloat($("#mod_monto_total").val()) || 0;
    const inicial = parseFloat($("#mod_pago_inicial").val()) || 0;
    const cuotas = parseInt($("#mod_cantidad_cuotas").val()) || 0;

    if (cuotas > 0) {
        const resultado = (total - inicial) / cuotas;
        $("#mod_cuota_estimada").text("$ " + resultado.toFixed(2));
    } else {
        $("#mod_cuota_estimada").text("$ 0.00");
    }
}

$("#mod_monto_total, #mod_pago_inicial, #mod_cantidad_cuotas").on("input", calcularCuotaEstimada);

$("#btn_guardar_modificacion").on("click", function() {
    const formulario = $("#formularioModificarFinanciamiento");
    const datos = formulario.serialize() + "&accion=actualizarFinanciamiento";
    
    if (typeof procesarPeticion === "function") {
        procesarPeticion(datos, "#modalModificarFinanciamiento");
    } else {
        $.post("?pagina=financiamiento", datos, function(res) {
            if (res.success) {
                alert("Actualizado correctamente");
                $("#modalModificarFinanciamiento").modal("hide");
                location.reload(); 
            } else {
                alert(res.error || "Error al actualizar");
            }
        }, "json");
    }
});

$(document).on("click", ".btn-modificar", function () {
    const id = $(this).data("id");
    
    $.post("?pagina=financiamiento", { accion: "consultarUno", id: id }, function (res) {
        const data = typeof res === 'string' ? JSON.parse(res) : res;
        
        if (data) {
            $("#formularioModificarFinanciamiento")[0].reset();
            
            $("#mod_id_financiamiento").val(data.id_financiamiento);
            $("#mod_monto_total").val(data.monto_total);
            $("#mod_pago_inicial").val(data.pago_inicial);
            $("#mod_cantidad_cuotas").val(data.cantidad_cuotas);
            $("#mod_dia_pago").val(data.dia_pago);
            $("#mod_id_unidad").val(data.id_unidad);
            
            if (data.fecha_inicio) {
                const fecha_formateada = data.fecha_inicio.split(' ')[0];
                $("#mod_fecha_inicio").val(fecha_formateada);
            }

            $("#mod_info_cliente").val(`${data.cedula_persona} - ${data.nombre} ${data.apellido}`);

            $.get("?pagina=financiamiento&ajax=true&x=telefonos_disponibles", function(productos) {
                let options = `<option value="${data.id_productos}" selected>Mantener actual</option>`;
                productos.forEach(p => {
                    options += `<option value="${p.id_producto}">${p.nombre_producto} - ${p.imei}</option>`;
                });
                $("#mod_id_producto").html(options);
            }, "json");

            calcularCuotaEstimada();
            new bootstrap.Modal('#modalModificarFinanciamiento').show();
        } else {
            Swal.fire({
                title: "Error",
                text: "No se pudieron cargar los datos",
                icon: "error",
                background: "#000910",
                color: "white"
            });
        }
    });
});



    
});
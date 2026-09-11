$(document).ready(function () {
let permisos = { registrar: false, modificar: false, eliminar: false, consultar: false };
let carritoProductos = [];

$.get("?pagina=servicio_tecnico&permisos=true", function (data) {
    permisos = typeof data === 'string' ? JSON.parse(data) : data;
    if (permisos.registrar) $("#btnRegistrarServicio").show();
    inicializarTablaServicio();
});

$('#modalAgregarProducto').on('shown.bs.modal', function () {
    cargarProductos();
});

$('#modalRegistrarServicio').on('shown.bs.modal', function () {
    cargarClientes();
    cargarMarcas();
    cargarEspecialidades();
});

$('#modalAgregarProducto').on('shown.bs.modal', function () {
    if ($('#selectProductos').hasClass("select2-hidden-accessible")) {
        $('#selectProductos').select2('destroy');
    }
    $('#selectProductos').select2({
        dropdownParent: $('#modalAgregarProducto'),
        placeholder: "Buscar producto...",
        ajax: {
            url: '?pagina=servicio_tecnico&x=productos',
            dataType: 'json',
            delay: 250,
            data: (params) => ({ q: params.term || '' }),
            processResults: (data) => ({
                results: (data || []).map(p => ({
                    id: p.id_producto,
                    text: p.nombre_producto + ' (Stock: ' + p.stock_actual + ')'
                }))
            }),
            cache: true
        },
        width: '100%'
    });
    $('#selectProductos').select2('open');
});

function formatearMoneda(valor) {
    const numero = Number(valor || 0);
    return new Intl.NumberFormat('es-VE', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
    }).format(numero);
}

function formatearFecha(valor) {
    if (!valor) return 'Sin fecha';
    const fecha = new Date(valor);
    if (Number.isNaN(fecha.getTime())) return valor;
    return fecha.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function inicializarTablaServicio() {
    $("#servicioTabla").DataTable({
        destroy: true,
        ajax: { url: "?pagina=servicio_tecnico&ajax=true", dataSrc: "" },
        pageLength: 8,
        ordering: true,
        responsive: true,
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        columns: [
            { data: "id_servicio", visible: false },
            { data: "cedula_persona" },
            { data: "nombre", defaultContent: "" },
            { data: "apellido", defaultContent: "" },
            { data: "equipo_descripcion" },
            { data: "falla_inicial" },
            { data: "estado" },
            {
                data: "monto_total",
                render: function (data) {
                    return formatearMoneda(data);
                }
            },
            {
                data: "fecha_registro",
                render: function (data) {
                    return formatearFecha(data);
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    let acciones = [];
                    if (permisos.modificar) {
                        acciones.push(`<button class="btn btn-warning btn-sm btn-modern btn-action btn-modificar" data-id="${row.id_servicio}" title="Editar servicio"><i class="bi bi-pencil-square"></i></button>`);

                        if (row.estado === 'Cobrado') {
                            acciones.push(`<button class="btn btn-primary btn-sm btn-modern btn-action btn-consultar-productos" data-id="${row.id_servicio}" title="Ver productos"><i class="bi bi-box-seam"></i></button>`);
                        }

                        if (row.estado !== 'Cobrado') {
                            if (parseFloat(row.monto_total) > 0) {
                                acciones.push(`<button class="btn btn-success btn-sm btn-modern btn-action btn-cobrar" data-id="${row.id_servicio}" data-monto="${row.monto_total}" title="Cobrar servicio"><i class="bi bi-cash-coin"></i></button>`);
                            } else {
                                acciones.push(`<button class="btn btn-info btn-sm btn-modern btn-action btn-agregar-producto" data-id="${row.id_servicio}" title="Agregar repuestos"><i class="bi bi-plus-lg"></i></button>`);
                            }
                        }
                    }
                    if (permisos.eliminar) {
                        acciones.push(`<button class="btn btn-danger btn-sm btn-modern btn-action btn-eliminar" data-id="${row.id_servicio}" title="Eliminar servicio"><i class="bi bi-trash"></i></button>`);
                    }
                    return `<div class="d-flex flex-wrap justify-content-center gap-2">${acciones.join('')}</div>`;
                }
            }
        ]
    });
}

$('#servicioTabla').on('click', '.btn-cobrar', function() {
    let id = $(this).data('id');
    let monto = $(this).data('monto');
    $('#idServicioCobro').val(id);
    $('#montoTotalCobro').val(monto);
    $('#precioRepuestos').text(monto);
    $('#modalCobro').modal('show');
});

function recalcularMontoTotal() {
    let total = 0;
    $("#tablaProductosModificar tbody tr").each(function() {
        let precio = parseFloat($(this).data("precio")) || 0;
        let cantidad = parseInt($(this).find(".cantidad").val()) || 0;
        total += (precio * cantidad);
    });
    $("#mod_monto").val(total.toFixed(2));
}

$(document).on("click", ".btn-modificar", function () {
    const id = $(this).data("id");
    $.post("?pagina=servicio_tecnico", { accion: "consultar", id: id }, function (res) {
        if (res && !res.error) {
            $("#mod_id_servicio").val(res.id_servicio);
            $("#mod_equipo").val(res.equipo_descripcion);
            $("#mod_falla").val(res.falla_inicial);
            $("#mod_diagnostico").val(res.diagnostico);
            $("#mod_estado").val(res.estado);
            $("#mod_monto").val(res.monto_total);

            $.get("?pagina=servicio_tecnico", { x: "consultar_productos", id: id }, function (productos) {
                let html = "";
                if (Array.isArray(productos) && productos.length > 0) {
                    productos.forEach(p => {
                        let precio = parseFloat(p.precio_unitario);
                        html += `<tr data-id-producto="${p.id_producto}" data-precio="${precio}"><td>${p.nombre_producto}</td><td><input type="number" class="form-control form-control-sm cantidad" value="${p.cantidad}" min="1"></td><td><button type="button" class="btn btn-danger btn-sm btn-eliminar-producto">X</button></td></tr>`;
                    });
                } else {
                    html = `<tr><td colspan="3" class="text-center text-muted">No hay productos cargados</td></tr>`;
                }
                $("#tablaProductosModificar tbody").html(html);
                recalcularMontoTotal();
                
                $.get("?pagina=servicio_tecnico", { x: "productos" }, function (lista) {
                    let options = '<option value="">Seleccione un producto...</option>';
                    if (Array.isArray(lista)) {
                        lista.forEach(p => {
                            let precio = parseFloat(p.precio_detal || 0);
                            options += `<option value="${p.id_producto}" data-nombre="${p.nombre_producto}" data-precio="${precio}">${p.nombre_producto} ($${precio.toFixed(2)})</option>`;
                        });
                    }
                    $("#selectAgregarProducto").html(options);
                    new bootstrap.Modal('#modalModificarServicio').show();
                }, 'json');
            }, 'json');
        } else {
            Swal.fire({ title: "Error", text: "Error al cargar los datos", icon: "error", background: "#000910", color: "white", confirmButtonColor: "#0d6efd" });
        }
    }, 'json');
});

$("#selectAgregarProducto").change(function() {
    let opt = $(this).find(':selected');
    let id = opt.val();
    let nombre = opt.data('nombre');
    let precio = opt.data('precio');
    if (id && $("#tablaProductosModificar tr[data-id-producto='" + id + "']").length === 0) {
        if ($("#tablaProductosModificar tbody tr td").hasClass("text-muted")) {
            $("#tablaProductosModificar tbody").empty();
        }
        $("#tablaProductosModificar tbody").append(`<tr data-id-producto="${id}" data-precio="${precio}"><td>${nombre}</td><td><input type="number" class="form-control form-control-sm cantidad" value="1" min="1"></td><td><button type="button" class="btn btn-danger btn-sm btn-eliminar-producto">X</button></td></tr>`);
        recalcularMontoTotal();
    }
});

$(document).on("input", ".cantidad", function() {
    recalcularMontoTotal();
});

$(document).on("click", ".btn-eliminar-producto", function () {
    $(this).closest("tr").remove();
    if ($("#tablaProductosModificar tbody tr").length === 0) {
        $("#tablaProductosModificar tbody").html(`<tr><td colspan="3" class="text-center text-muted">No hay productos cargados</td></tr>`);
    }
    recalcularMontoTotal();
});

$("#btnGuardarModificacion").off("click").on("click", function () {
    const equipo = $("#mod_equipo").val().trim();
    const falla = $("#mod_falla").val().trim();
    const diagnostico = $("#mod_diagnostico").val().trim();
    const estado = $("#mod_estado").val();
    const monto = $("#mod_monto").val();

    $("#formularioModificarServicio").find('input[required], select[required], textarea[required]').trigger('change').trigger('input').trigger('blur');

    if (!equipo) {
        Swal.fire({ icon: 'warning', title: 'Equipo/Modelo requerido', text: 'Ingresa el modelo o equipo.', background: "#000910", color: "white" });
        $("#mod_equipo").focus();
        return;
    }

    if (!falla) {
        Swal.fire({ icon: 'warning', title: 'Falla requerida', text: 'Ingresa la falla del equipo.', background: "#000910", color: "white" });
        $("#mod_falla").focus();
        return;
    }

    if (!diagnostico) {
        Swal.fire({ icon: 'warning', title: 'Diagnóstico requerido', text: 'Ingresa el diagnóstico del equipo', background: "#000910", color: "white" });
        $("#mod_diagnostico").focus();
        return;
    }

    if (!estado || monto === '' || Number(monto) < 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos requeridos',
            text: 'Completa todos los campos obligatorios antes de guardar los cambios.',
            confirmButtonText: 'Aceptar',
            background: "#000910",
            color: "white"
        });
        return;
    }

    let productos = [];
    $("#tablaProductosModificar tbody tr").each(function() {
        let id = $(this).data("id-producto");
        if (id) {
            productos.push({
                id_producto: id,
                cantidad: $(this).find(".cantidad").val()
            });
        }
    });

    let datos = {
        accion: "modificar",
        id: $("#mod_id_servicio").val(),
        equipo: equipo,
        falla: falla,
        diagnostico: $("#mod_diagnostico").val().trim(),
        estado: estado,
        monto: monto,
        productos: JSON.stringify(productos)
    };

    procesarPeticion(datos, "#modalModificarServicio", "modificar");
});

$('#servicioTabla').on('click', '.btn-consultar-productos', function() {
    let id = $(this).data('id');

    $.get("?pagina=servicio_tecnico&x=consultar_productos&id=" + id, function(data) {
        let htmlInfo = "";
        let htmlTabla = `
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-center">Precio</th>
                    </tr>
                </thead>
                <tbody>`;

        if (data && data.length > 0) {
            htmlInfo = `
                <div class="mb-3">
                    <p class="mb-1"><strong>Diagnóstico:</strong> ${data[0].diagnostico || 'N/A'}</p>
                    <p class="mb-1"><strong>Nota Técnico:</strong> ${data[0].nota_tecnico || 'N/A'}</p>
                    <p class="mb-1"><strong>Especialidad:</strong> ${data[0].nombre_especialidad || 'N/A'}</p>
                </div>`;

            data.forEach(p => {
                htmlTabla += `<tr>
                    <td>${p.nombre_producto}</td>
                    <td class="text-center">${p.cantidad}</td>
                    <td class="text-center">${p.precio_unitario}</td>
                </tr>`;
            });
        } else {
            htmlTabla += `<tr><td colspan="3" class="text-center">No se han registrado productos</td></tr>`;
        }

        htmlTabla += `</tbody></table>`;
        
        $('#cuerpoVerServicio').html(htmlInfo + htmlTabla);
        $('#modalVerServicio').modal('show');
    }, 'json');
});

// EVENTO DE GUARDADO REGISTRO
$("#btnGuardarRegistro").off("click").on("click", function () {
    let form = $("#formularioRegistrarServicio");

    // 1. Forzar validación visual en tiempo real para mostrar textos en rojo (.msg-error)
    form.find('select, input, textarea').trigger('change').trigger('input').trigger('blur');

    let cedulaLimpia = $("#reg_cedula").val();
    let equipo = $("#reg_equipo").length ? $("#reg_equipo").val().trim() : $('input[name="equipo"]').val().trim();
    let falla = $("#reg_falla").length ? $("#reg_falla").val().trim() : $('textarea[name="falla"]').val().trim();
    let telefono = $('input[name="telefono"]').val().trim();

    // 2. Evaluaciones para disparar alertas de SweetAlert
    if (!cedulaLimpia) {
        Swal.fire({ icon: 'warning', title: 'Cliente requerido', text: 'Selecciona un cliente válido antes de registrar el servicio.', background: "#000910", color: "white" });
        $("#reg_cedula").focus();
        return;
    }

    if (!$('#reg_marca').val()) {
        Swal.fire({ icon: 'warning', title: 'Marca requerida', text: 'Selecciona la marca del equipo.', background: "#000910", color: "white" });
        $('#reg_marca').focus();
        return;
    }

    if (!$('#reg_especialidad').val()) {
        Swal.fire({ icon: 'warning', title: 'Especialidad requerida', text: 'Selecciona la especialidad del servicio.', background: "#000910", color: "white" });
        $('#reg_especialidad').focus();
        return;
    }

    if (!equipo) {
        Swal.fire({ icon: 'warning', title: 'Modelo/Equipo requerido', text: 'Ingresa el modelo o equipo.', background: "#000910", color: "white" });
        $('input[name="equipo"], #reg_equipo').focus();
        return;
    }

    if (!/^[0-9]{7,11}$/.test(telefono)) {
        Swal.fire({ icon: 'warning', title: 'Teléfono inválido', text: 'El teléfono debe contener solo números y tener entre 7 y 11 dígitos.', background: "#000910", color: "white" });
        $('input[name="telefono"]').focus();
        return;
    }

    if (!falla) {
        Swal.fire({ icon: 'warning', title: 'Diagnóstico/Falla requerida', text: 'Describe el problema reportado.', background: "#000910", color: "white" });
        $('textarea[name="falla"], #reg_falla').focus();
        return;
    }

    let formData = form.serializeArray();
    formData = formData.map(field => {
        if (field.name === "cedula_persona") {
            field.value = cedulaLimpia;
        }
        return field;
    });

    let datos = $.param(formData) + "&accion=registrar";
    procesarPeticion(datos, "#modalRegistrarServicio", "registrar");
});

function cargarClientes() {
    $.get("?pagina=servicio_tecnico&x=clientes", function (res) {
        const data = typeof res === 'string' ? JSON.parse(res) : res;
        if (!Array.isArray(data)) throw new Error(data?.error || 'Respuesta inválida al cargar clientes');
        let $el = $("#reg_cedula");
        if ($el.hasClass("select2-hidden-accessible")) $el.select2('destroy');
        
        $el.empty().append('<option value="" selected disabled>Buscar cliente...</option>');
        
        data.forEach(item => {
            $el.append(`<option value="${item.cedula_persona}">${item.cedula_persona} - ${item.nombre} ${item.apellido}</option>`);
        });
        
        $el.select2({ width: '100%', dropdownParent: $('#modalRegistrarServicio') });
    }).fail(function (xhr) {
        Swal.fire({ icon: 'error', title: 'No se pudieron cargar los clientes', text: 'Verifica la conexión con la base de datos e inténtalo nuevamente.' });
    });
}

function cargarProductos() {
    $.get("?pagina=servicio_tecnico&x=productos", function (res) {
        const data = Array.isArray(res) ? res : [];
        let $el = $("#selectProductos"); 
        
        if ($el.hasClass("select2-hidden-accessible")) $el.select2('destroy');
        
        $el.empty().append('<option value="" selected disabled>Buscar producto...</option>');
        
        data.forEach(item => {
            $el.append(`<option value="${item.id_producto}">${item.nombre_producto} (Stock: ${item.stock_actual})</option>`);
        });
        
        $el.select2({ 
            width: '100%', 
            dropdownParent: $('#modalAgregarProducto') 
        });
    }).fail(function() {
        Swal.fire({ icon: 'error', title: 'No se pudieron cargar los productos', text: 'Verifica la conexión con la base de datos e inténtalo nuevamente.' });
    });
}

function cargarMarcas() {
    $.get("?pagina=servicio_tecnico&x=marcas", function (res) {
        const data = typeof res === 'string' ? JSON.parse(res) : res;
        if (!Array.isArray(data)) throw new Error(data?.error || 'Respuesta inválida al cargar marcas');
        let $el = $("#reg_marca");
        if ($el.hasClass("select2-hidden-accessible")) $el.select2('destroy');
        $el.empty().append('<option value="" selected disabled>Seleccionar marca...</option>');
        data.forEach(item => {
            $el.append(`<option value="${item.id_marca}">${item.nombre_marca}</option>`);
        });
        $el.select2({ width: '100%', dropdownParent: $('#modalRegistrarServicio') });
    }).fail(function (xhr) {
        Swal.fire({ icon: 'error', title: 'No se pudieron cargar las marcas', text: 'Verifica la conexión con la base de datos e inténtalo nuevamente.' });
    });
}

function cargarEspecialidades() {
    $.get("?pagina=servicio_tecnico&x=especialidades", function (res) {
        const data = typeof res === 'string' ? JSON.parse(res) : res;
        if (!Array.isArray(data)) throw new Error(data?.error || 'Respuesta inválida al cargar especialidades');
        let $el = $("#reg_especialidad");
        if ($el.hasClass("select2-hidden-accessible")) $el.select2('destroy');
        $el.empty().append('<option value="" selected disabled>Seleccionar especialidad...</option>');
        data.forEach(item => {
            $el.append(`<option value="${item.id_especialidad}">${item.nombre_especialidad}</option>`);
        });
        $el.select2({ width: '100%', dropdownParent: $('#modalRegistrarServicio') });
    }).fail(function (xhr) {
        Swal.fire({ icon: 'error', title: 'No se pudieron cargar las especialidades', text: 'Verifica la conexión con la base de datos e inténtalo nuevamente.' });
    });
}

$(document).on("click", ".btn-eliminar", function () {
    const id = $(this).data("id");
    Swal.fire({
        title: '¿Eliminar servicio?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        background: "#000910",
        color: "white",
        confirmButtonColor: '#d63031'
    }).then((result) => {
        if (result.isConfirmed) {
            procesarPeticion(`id=${id}&accion=eliminar`, "ninguno", "eliminar");
        }
    });
});

$(document).on("click", ".btn-agregar-producto", function() {
    const id = $(this).data("id");
    abrirModalProducto(id);
});

function abrirModalProducto(idServicio) {
    $('#idServicioActual').val(idServicio);
    $('#modalAgregarProducto').modal('show');
}

$('#btnAgregarALista').click(function() {
    let id = $('#selectProductos').val();
    let text = $('#selectProductos option:selected').text();
    let cant = parseInt($('#cantidadProducto').val(), 10) || 1;

    if (!id) {
        Swal.fire({ icon: 'warning', title: 'Producto faltante', text: 'Selecciona un producto para agregar al servicio.', background: "#000910", color: "white" });
        return;
    }

    if (cant < 1) {
        Swal.fire({ icon: 'warning', title: 'Cantidad inválida', text: 'La cantidad debe ser mayor a cero.', background: "#000910", color: "white" });
        return;
    }

    carritoProductos.push({ id: id, nombre: text, cantidad: cant });
    $('#listaProductosTemporal').append(`
        <tr>
            <td>${text}</td>
            <td>${cant}</td>
            <td><button class="btn btn-danger btn-sm btn-remover" title="Quitar producto"><i class="bi bi-x-lg"></i></button></td>
        </tr>
    `);
    $('#contadorProductos').text(carritoProductos.length);
    $('#selectProductos').val(null).trigger('change');
    $('#cantidadProducto').val(1);
});

$(document).on('click', '.btn-remover', function() {
    let index = $(this).closest('tr').index();
    carritoProductos.splice(index, 1);
    $(this).closest('tr').remove();
    $('#contadorProductos').text(carritoProductos.length);
});

$('#btnGuardarCarrito').click(function() {
    if(carritoProductos.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'El carrito está vacío',
            background: "#000910",
            color: "white"
        });
        return;
    }
    let idServicio = $('#idServicioActual').val();
    let contador = 0;
    let errores = [];
    carritoProductos.forEach(function(item) {
        $.ajax({
            type: "POST",
            url: "?pagina=servicio_tecnico",
            data: {
                accion: 'agregar_producto',
                id_servicio: idServicio,
                id_producto: item.id,
                cantidad: item.cantidad
            },
            dataType: "json",
            success: function(res) {
                contador++;
                if(!res.success) errores.push(item.nombre + ": " + res.mensaje);
                if(contador === carritoProductos.length) {
                    if(errores.length > 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errores al guardar',
                            text: errores.join(", "),
                            background: "#000910",
                            color: "white"
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Productos guardados correctamente',
                            timer: 2000,
                            showConfirmButton: false,
                            background: "#000910",
                            color: "white"
                        });
                    }
                    
                    $('#btnGuardarCarrito').hide();
                    $('#btnAbrirCobro').show(); 
                    carritoProductos = [];
                    $('#listaProductosTemporal').empty();
                    $('#modalAgregarProducto').modal('hide');
                    $("#servicioTabla").DataTable().ajax.reload();
                }
            }
        });
    });
});

$('#btnAbrirCobro').click(function() {
    let totalRepuestos = carritoProductos.reduce((acc, item) => acc + (item.precio * item.cantidad), 0);
    $('#precioRepuestos').text(totalRepuestos.toFixed(2));
    $('#montoTotalCobro').val(totalRepuestos.toFixed(2)); 
    $('#modalCobro').modal('show');
});

$('#btnConfirmarCobro').on('click', function() {
    const montoCobro = $('#montoTotalCobro').val().trim();
    const diagnostico = $('#diagnostico_cobro').val().trim();
    const notaTecnico = $('#nota_tecnico_cobro').val().trim();

    $('#montoTotalCobro').trigger('input').trigger('blur');
    $('#diagnostico_cobro, #nota_tecnico_cobro').trigger('input').trigger('blur');

    if (!montoCobro || Number.isNaN(Number(montoCobro)) || Number(montoCobro) < 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Monto requerido',
            text: 'Ingresa un monto válido para procesar el cobro.',
            background: "#000910",
            color: "white"
        });
        $('#montoTotalCobro').focus();
        return;
    }

    if (!diagnostico) {
        Swal.fire({
            icon: 'warning',
            title: 'Diagnóstico requerido',
            text: 'Ingresa el diagnóstico del equipo',
            background: "#000910",
            color: "white"
        });
        $('#diagnostico_cobro').focus();
        return;
    }

    if (!notaTecnico) {
        Swal.fire({
            icon: 'warning',
            title: 'Nota requerida',
            text: 'Ingresa la nota del técnico',
            background: "#000910",
            color: "white"
        });
        $('#nota_tecnico_cobro').focus();
        return;
    }

    let datos = {
        accion: 'cobrar',
        id_servicio_cobro: $('#idServicioCobro').val(),
        monto_total_cobro: $('#montoTotalCobro').val(),
        diagnostico_cobro: $('#diagnostico_cobro').val(), 
        nota_tecnico_cobro: $('#nota_tecnico_cobro').val()
    };

    $.post('?pagina=servicio_tecnico', datos, function(respuesta) {
        if (respuesta.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Cobro registrado exitosamente',
                confirmButtonText: 'Aceptar',
                background: "#000910",
                color: "white"
            }).then(() => {
                $('#modalCobro').modal('hide');
                location.reload(); 
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: respuesta.mensaje || "No se pudo procesar",
                background: "#000910",
                color: "white"
            });
        }
    }, 'json');
});

function procesarPeticion(datos, modalId, tipoOperacion) {
    if (modalId && modalId !== "ninguno") {
        $(modalId).modal('hide');
    }

    Swal.fire({ 
        title: "Procesando", 
        background: "#000910", 
        color: "white", 
        didOpen: () => Swal.showLoading(), 
        allowOutsideClick: false 
    });
    
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
                    var titulo = '', texto = '';

                    if (tipoOperacion === 'registrar') {
                        titulo = '¡Servicio Registrado!';
                        texto = 'Servicio técnico registrado exitosamente.';
                    } else if (tipoOperacion === 'modificar') {
                        titulo = '¡Servicio Modificado!';
                        texto = 'Servicio técnico modificado exitosamente.';
                    } else if (tipoOperacion === 'eliminar') {
                        titulo = '¡Servicio Eliminado!';
                        texto = 'Servicio eliminado exitosamente.';
                    }

                    if (modalId && modalId !== "ninguno" && $(modalId + " form").length) {
                        $(modalId + " form")[0].reset();
                    }

                    $("select").val("").trigger("change.select2");

                    if ($.fn.DataTable.isDataTable("#servicioTabla")) {
                        $("#servicioTabla").DataTable().ajax.reload(null, false);
                    }

                    Swal.fire({ 
                        background: "#000910", 
                        color: "white", 
                        title: titulo, 
                        text: texto, 
                        icon: "success", 
                        timer: 1500, 
                        showConfirmButton: false 
                    });
                } else {
                    if (modalId && modalId !== "ninguno") $(modalId).modal('show');
                    Swal.fire({ title: "Error", text: res.error || "No se pudo procesar la solicitud.", icon: "error", background: "#000910", color: "white", confirmButtonColor: "#0d6efd" });
                }
            } catch (e) {
                if (modalId && modalId !== "ninguno") $(modalId).modal('show');
                Swal.fire({ title: "Error", text: "Respuesta inválida del servidor", icon: "error", background: "#000910", color: "white", confirmButtonColor: "#0d6efd" });
            }
        },
        error: function() {
            if (modalId && modalId !== "ninguno") $(modalId).modal('show');
            Swal.fire({ title: "Ups!", text: "Error de comunicación con el servidor.", icon: "error", background: "#000910", color: "white", confirmButtonColor: "#0d6efd" });
        }
    });
}
});
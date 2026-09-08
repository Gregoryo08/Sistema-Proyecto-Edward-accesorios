$(document).ready(function () {
    let permisos = { listar: false, registrar_pago: false };
    let tasaCambio = 0;
    const modalPago = new bootstrap.Modal(document.getElementById('modal_pagar_cuota'));

    $('#btn_confirmar_pago').prop('disabled', true);

    $.get("?pagina=cliente_financiamiento&permisos=true", function (data) {
        permisos = typeof data === 'string' ? JSON.parse(data) : data;
        if (permisos.listar) inicializarTabla();
    });

    $.get("?pagina=cliente_financiamiento&ajax=true&x=tasa", function (res) {
        tasaCambio = parseFloat(res) || 0;
    });

    const validaciones = {
        'id_banco_pago': { regex: /.+/, error: 'Seleccione un banco' },
        'referencia': { regex: /^[0-9]{4,20}$/, error: 'Debe ser numérico (4-20 dígitos)' },
        'fecha_pago': { regex: /.+/, error: 'Seleccione una fecha' }
    };

    function gestionarEstado(id, esValido, mensaje = "") {
        const $el = $("#" + id);
        const $errorDiv = $("#error_" + id).length ? $("#error_" + id) : $(`<div class="msg-error text-danger small" id="error_${id}"></div>`).insertAfter($el);
        if (esValido) {
            $el.removeClass('is-invalid').addClass('is-valid');
            $errorDiv.hide().text("");
        } else {
            $el.removeClass('is-valid').addClass('is-invalid');
            $errorDiv.text(mensaje).show();
        }
        verificarFormulario();
    }

    function verificarFormulario() {
        const campos = ['id_banco_pago', 'referencia', 'fecha_pago'];
        let esValido = true;
        campos.forEach(id => {
            if (!$('#' + id).hasClass('is-valid')) esValido = false;
        });
        $('#btn_confirmar_pago').prop('disabled', !esValido);
    }

    function actualizarConversion(monto) {
        if (tasaCambio > 0) {
            const totalBs = (parseFloat(monto) * tasaCambio).toFixed(2);
            if ($("#monto_bs").length === 0) {
                $("#contenedor_monto_bs").html(`<div class="mt-2 text-muted small">Equivalente: <strong id="monto_bs">${totalBs}</strong> Bs</div>`);
            } else {
                $("#monto_bs").text(totalBs);
            }
        }
    }

    $('#referencia').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    function inicializarTabla() {
        $("#tabla_mis_financiamientos").DataTable({
            destroy: true,
            ajax: { url: "?pagina=cliente_financiamiento&ajax=true&x=listado", dataSrc: "" },
            language: {
                decimal: "",
                emptyTable: "No hay información disponible en la tabla",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "Mostrar _MENU_ registros",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                search: "Buscar:",
                zeroRecords: "No se encontraron resultados",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            },
            columns: [
                { data: "nombre_producto" },
                { data: "monto_total", render: function (data) { return `$${parseFloat(data).toFixed(2)}`; } },
                { data: "saldo_pendiente", render: function (data) { return `$${parseFloat(data).toFixed(2)}`; } },
                { data: "proximo_vencimiento" },
                {
                    data: null,
                    render: function (data, type, row) {
                        let botones = "";
                        if (permisos.registrar_pago && parseFloat(row.saldo_pendiente) > 0) {
                            botones = `<button class="btn btn-primary btn-sm btn-pagar" data-id="${row.id_cuota}" data-monto="${row.monto_cuota}">Pagar</button>`;
                        }
                        botones += ` <button class="btn btn-info btn-sm btn-historial" data-id="${row.id_financiamiento}">Historial</button>`;
                        return botones;
                    }
                }
            ]
        });
    }

    $(document).on("click", ".btn-pagar", function () {
        const d = $(this).data();
        $("#id_cuota_pago").val(d.id);
        $("#monto_pago").val(d.monto);
        actualizarConversion(d.monto);

        $.get("?pagina=cliente_financiamiento&ajax=true&x=bancos", function (res) {
            const bancos = typeof res === 'string' ? JSON.parse(res) : res;
            let selectBanco = $("#id_banco_pago");
            if (selectBanco.children().length <= 1) {
                selectBanco.empty().append('<option value="" selected disabled>Seleccione banco origen...</option>');
                bancos.forEach(b => selectBanco.append(`<option value="${b.id_banco}">${b.nombre_banco}</option>`));
            }
            
            const hoy = new Date().toISOString().split('T')[0];
            $("#fecha_pago").attr('max', hoy);
            modalPago.show();
        });
    });

    $('[data-bs-dismiss="modal"]').on('click', function() {
        $('#formularioPagoCuota')[0].reset();
        $('.form-control, .form-select').removeClass('is-valid is-invalid');
        $('.msg-error').hide();
        $("#contenedor_monto_bs").empty();
        $('#btn_confirmar_pago').prop('disabled', true);
    });

    $('#id_banco_pago, #referencia, #fecha_pago').on('input change', function() {
        const id = $(this).attr('id');
        let val = $(this).val();
        
        if (id === 'fecha_pago') {
            const hoy = new Date().toISOString().split('T')[0];
            const limite = new Date();
            limite.setDate(limite.getDate() - 30);
            const fechaLimite = limite.toISOString().split('T')[0];

            if (val === "" || val > hoy || val < fechaLimite) {
                gestionarEstado(id, false, "Fecha inválida");
                return;
            }
        }

        const config = validaciones[id];
        if (!val || val === "") gestionarEstado(id, false, "Campo requerido");
        else if (config.regex.test(val)) gestionarEstado(id, true);
        else gestionarEstado(id, false, config.error);
    });

    $(document).on("click", ".btn-historial", function () {
        const id_financiamiento = $(this).data('id');
        $.get("?pagina=cliente_financiamiento&ajax=true&x=historial&id_financiamiento=" + id_financiamiento, function (res) {
            const data = typeof res === 'string' ? JSON.parse(res) : res;
            let tbody = $("#tablaHistorial tbody");
            tbody.empty();
            data.forEach((c, index) => {
                tbody.append(`<tr>
                    <td>${index + 1}</td>
                    <td>${c.fecha_vencimiento}</td>
                    <td>${c.estado_cuota}</td>
                    <td>${c.monto_pagado || '0.00'}</td>
                    <td>${c.fecha_pago_realizado || '-'}</td>
                    <td>${c.nombre_metodopago || 'N/A'}</td>
                    <td>${c.nombre_banco || 'N/A'}</td>
                </tr>`);
            });
            new bootstrap.Modal(document.getElementById('modalHistorialCuotas')).show();
        });
    });

    $("#btn_confirmar_pago").click(function () {
        if ($(this).prop('disabled')) return;
        const datos = $("#formularioPagoCuota").serialize() + "&accion=registrarPago";
        const SWAL_CFG = { background: "#000910", color: "white", confirmButtonColor: "#0d6efd" };
        Swal.fire({ title: "Procesando", ...SWAL_CFG, didOpen: () => Swal.showLoading() });

        $.ajax({
            type: "POST",
            url: "?pagina=cliente_financiamiento",
            data: datos,
            success: function (response) {
                Swal.close();
                let res = typeof response === 'object' ? response : JSON.parse(response);
                if (res.success) {
                    Swal.fire({ ...SWAL_CFG, title: "Solicitud enviada", icon: "success", timer: 1500, showConfirmButton: false });
                    modalPago.hide();
                    $('#formularioPagoCuota')[0].reset();
                    $("#contenedor_monto_bs").empty();
                    $("#tabla_mis_financiamientos").DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire({ ...SWAL_CFG, title: "Error", text: res.error || "Error", icon: "error" });
                }
            }
        });
    });
});
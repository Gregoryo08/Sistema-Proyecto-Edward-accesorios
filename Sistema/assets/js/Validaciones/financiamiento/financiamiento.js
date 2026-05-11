$(document).ready(function () {
    let permisos = { registrar: false, modificar: false, eliminar: false };

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
                    render: function(data, type, row) { return `${row.nombre} ${row.apellido}`; }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return `${row.modelo} <br><small class="text-muted">${row.imei}</small>`;
                    }
                },
                { 
                    data: "monto_total",
                    render: function(data) { return `$${parseFloat(data).toFixed(2)}`; }
                },
                { 
                    data: "saldo_pendiente",
                    render: function(data) { 
                        let monto = parseFloat(data).toFixed(2);
                        let color = monto > 0 ? 'text-danger fw-bold' : 'text-success fw-bold';
                        return `<span class="${color}">$${monto}</span>`;
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        let montoCuota = parseFloat(row.monto_cuota).toFixed(2);
                        return `<b class="text-primary">$${montoCuota}</b> <br> 
                                <span class="badge bg-info">${row.pagadas} / ${row.cantidad_cuotas}</span>`;
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
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
                    render: function(data) {
                        let color = (data === 'activo') ? 'success' : 'danger';
                        return `<span class="badge bg-${color}">${data.toUpperCase()}</span>`;
                    }
                },
                { 
                    data: "estatus_financiamiento",
                    render: function(data) {
                        let color = (data === 'vigente') ? 'primary' : (data === 'finalizado' ? 'success' : (data === 'anulado' ? 'secondary' : 'warning'));
                        return `<span class="badge bg-${color}">${data.toUpperCase()}</span>`;
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        let btnBloqueo = row.estado_equipo === 'activo' ? 
                            `<button class="btn btn-outline-danger btn-sm btn-cambiar-estado" data-id="${row.id_financiamiento}" data-estado="bloqueado" title="Bloquear"><i class="bi bi-lock-fill"></i></button>` :
                            `<button class="btn btn-outline-success btn-sm btn-cambiar-estado" data-id="${row.id_financiamiento}" data-estado="activo" title="Desbloquear"><i class="bi bi-unlock-fill"></i></button>`;
                        
                        let btnFinalizar = (row.estatus_financiamiento === 'vigente') ? 
                            `<button class="btn btn-outline-dark btn-sm btn-finalizar-contrato" data-id="${row.id_financiamiento}" title="Finalizar"><i class="bi bi-check-all"></i></button>` : '';

                        let btnAnular = (row.pagadas == 0 && row.estatus_financiamiento === 'vigente') ? 
                            `<button class="btn btn-outline-warning btn-sm btn-anular" data-id="${row.id_financiamiento}" title="Anular"><i class="bi bi-trash"></i></button>` : '';

                        return `<div class="btn-group" role="group">
                            <button class="btn btn-primary btn-sm btn-seguimiento" data-id="${row.id_financiamiento}"><i class="bi bi-list-check"></i></button>
                            ${btnBloqueo}
                            ${btnFinalizar}
                            ${btnAnular}
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

    function cargarCatalogos() {
        $.get("?pagina=financiamiento&ajax=true&x=clientes", function (res) {
            const clientes = typeof res === 'string' ? JSON.parse(res) : res;
            let opt = '<option value="" selected disabled>Seleccionar Cliente...</option>';
            clientes.forEach(c => opt += `<option value="${c.cedula_cliente}">${c.cedula_cliente} - ${c.nombre}</option>`);
            $("#cedula_cliente").html(opt);
        });

        $.get("?pagina=financiamiento&ajax=true&x=telefonos_disponibles", function (res) {
            const equipos = typeof res === 'string' ? JSON.parse(res) : res;
            let opt = '<option value="" selected disabled>Seleccionar Teléfono...</option>';
            equipos.forEach(e => opt += `<option value="${e.id_telefono}">${e.modelo} (${e.imei})</option>`);
            $("#id_telefono").html(opt);
        });

        $.get("?pagina=financiamiento&ajax=true&x=metodos", function(res) {
            const metodos = typeof res === 'string' ? JSON.parse(res) : res;
            let select = $("#id_metodopago");
            select.empty().append('<option value="" selected disabled>Seleccione método...</option>');
            metodos.forEach(m => select.append(`<option value="${m.id_metodopago}">${m.nombre_metodopago}</option>`));
        });

        $.get("?pagina=financiamiento&ajax=true&x=bancos", function(res) {
            const bancos = typeof res === 'string' ? JSON.parse(res) : res;
            let select = $("#id_banco");
            select.empty().append('<option value="" selected disabled>Seleccione banco...</option>');
            bancos.forEach(b => select.append(`<option value="${b.id_banco}">${b.nombre_banco}</option>`));
        });
    }

    $(document).on("click", ".btn-seguimiento", function () {
        const id = $(this).data("id");
        const fila = $("#financiamientotabla").DataTable().row($(this).parents('tr')).data();
        $("#id_finan_pago").val(id);
        cargarCatalogos();
        $.post("?pagina=financiamiento", { accion: "consultarCuotas", id: id }, function (res) {
            const cuotas = typeof res === 'string' ? JSON.parse(res) : res;
            let html = "";
            cuotas.forEach(c => {
                let badge = c.estado_cuota === 'pagado' ? 'success' : 'warning';
                let btnPagar = c.estado_cuota === 'pendiente' ? 
                    `<button class="btn btn-sm btn-success btn-abrir-pago" data-id="${c.id_cuota}" data-monto="${parseFloat(fila.monto_cuota).toFixed(2)}" data-numero="${c.numero_cuota}">Pagar</button>` : '';
                
                let detallePago = c.estado_cuota === 'pagado' ? 
                    `<small><b>${c.nombre_metodopago || 'N/A'}</b>${c.nombre_banco ? '<br>'+c.nombre_banco : ''}</small>` : '-';

                html += `<tr><td>${c.numero_cuota}</td><td>${c.fecha_vencimiento}</td><td>$${parseFloat(c.monto_pagado || 0).toFixed(2)}</td><td><span class="badge bg-${badge}">${c.estado_cuota.toUpperCase()}</span></td><td>${detallePago}</td><td>${c.fecha_pago_realizado || '-'}</td><td>${btnPagar}</td></tr>`;
            });
            $("#cuerpoSeguimiento").html(html);
            new bootstrap.Modal('#modalSeguimientoPagos').show();
        });
    });

    $(document).on("click", ".btn-abrir-pago", function() {
        const d = $(this).data();
        $("#id_cuota_pago").val(d.id);
        $("#monto_pago_input").val(d.monto);
        $("#detalle_cuota_pago").html(`Cuota Nro: <b>${d.numero}</b> | Monto Fijo: <b>$${d.monto}</b>`);
        $.get("?pagina=financiamiento&ajax=true&x=tasa_bcv", function(res) {
            const data = typeof res === 'string' ? JSON.parse(res) : res;
            if(data.tasa) { $("#tasa_dia").val(data.tasa); calcularConversion(); }
        });
        new bootstrap.Modal('#modalRegistrarPago').show();
    });

    $(document).on("click", ".btn-cambiar-estado", function() {
        const d = $(this).data();
        procesarPeticion(`id=${d.id}&estado=${d.estado}&accion=cambiarEstadoEquipo`, "ninguno");
    });

    $(document).on("click", ".btn-finalizar-contrato", function() {
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

    $(document).on("click", ".btn-anular", function() {
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

    $("#btn_confirmar_pago").click(function() {
        const datos = $("#formularioPagoCuota").serialize() + "&accion=registrarPagoCuota";
        procesarPeticion(datos, "#modalRegistrarPago");
    });

    function procesarPeticion(datos, modalId) {
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
                        if(modalId !== "ninguno") $(modalId).modal('hide');
                        if(modalId === "#modalRegistrarPago") {
                            $("#modalRegistrarPago").modal('hide');
                            $("#modalSeguimientoPagos").modal('hide');
                        }
                        cargarCatalogos();
                        $("#financiamientotabla").DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire({ ...SWAL_CFG, title: "Error", text: res.error || "Error", icon: "error" });
                    }
                } catch(e) {
                    Swal.fire({ ...SWAL_CFG, title: "Error", text: "Respuesta inválida del servidor", icon: "error" });
                }
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
        const datos = $("#formularioRegistroFinanciamiento").serialize() + "&accion=registrarFinanciamiento";
        procesarPeticion(datos, "#modalRegistroFinanciamiento");
    });

    $("#monto_total, #pago_inicial, #cantidad_cuotas").on("input", function() {
        const total = parseFloat($("#monto_total").val()) || 0;
        const inicial = parseFloat($("#pago_inicial").val()) || 0;
        const cuotas = parseInt($("#cantidad_cuotas").val()) || 0;
        $("#cuota_estimada").text(`$ ${cuotas > 0 ? ((total - inicial) / cuotas).toFixed(2) : '0.00'}`);
    });

    $(document).on("click", "#btn_nuevo_financiamiento", function () {
        $("#formularioRegistroFinanciamiento")[0].reset();
        $("#cuota_estimada").text(`$ 0.00`);
        cargarCatalogos();
        new bootstrap.Modal('#modalRegistroFinanciamiento').show();
    });

    $("#id_metodopago").change(function() {
        const texto = $(this).find('option:selected').text().toLowerCase();
        (texto.includes("pago movil") || texto.includes("transferencia")) ? $("#contenedor_banco").fadeIn() : $("#contenedor_banco").fadeOut();
    });

    cargarCatalogos();
});
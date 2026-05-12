$(document).ready(function () {
    let permisos = { registrar: false, consultar: false, modificar: false, eliminar: false, control_total: false };

    $.get("?pagina=chequeo&permisos=true", function (data) {
        permisos = JSON.parse(data);
        cargarTablaChequeo();
    });

    function cargarTablaChequeo() {
        $("#tablaChequeo").DataTable({
            destroy: true,
            ajax: {
                url: "?pagina=chequeo&ajax=true&accion=listarSolicitudes",
                dataSrc: "",
            },
            columns: [
                { data: "id_venta", visible: false },
                { 
                    data: "fecha",
                    render: function(data) { return new Date(data).toLocaleString(); }
                },
                { data: "direccion" }, 
                { data: "telefono" },
                { data: "metodo_pago" },
                { 
                    data: "total",
                    render: function(data) { return `<b>$${parseFloat(data).toFixed(2)}</b>`; }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        let botones = '<div class="btn-group" role="group">';
                        
                        botones += `
                            <button type="button" class="btn btn-info btn-ver-detalles" 
                                data-id="${row.id_venta}" data-total="${row.total}">
                                <i class="fa-solid fa-eye"></i>
                            </button>`;

                        if (permisos.control_total || permisos.registrar) {
                            botones += `
                                <button type="button" class="btn btn-success btn-abrir-gestion" 
                                    data-id="${row.id_venta}" data-total="${row.total}">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-rechazarPedido" 
                                    data-id="${row.id_venta}">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>`;
                        }
                        botones += '</div>';
                        return botones;
                    },
                },
            ],
            pageLength: 8,
            columnDefs: [{ className: "dt-head-center", targets: "_all" }],
            language: {
                search: "Buscar:",
                zeroRecords: "No hay solicitudes pendientes",
                paginate: { next: "Sig", previous: "Ant" }
            }
        });
    }

    $(document).on("click", ".btn-ver-detalles", function() {
        let id = $(this).data("id");
        let total = $(this).data("total");

        $("#num_solicitud_modal").text(id);
        $("#total_modal_ver").text("$" + parseFloat(total).toFixed(2));
        
        $("#formConfirmarVenta").hide();
        $("#btnFinalizarPedido").hide();

        obtenerDetalles(id);
    });

    $(document).on("click", ".btn-abrir-gestion", function() {
        let id = $(this).data("id");
        let total = $(this).data("total");

        $("#num_solicitud_modal").text(id);
        $("#id_solicitud_input").val(id);
        $("#total_modal_ver").text("$" + parseFloat(total).toFixed(2));

        $("#formConfirmarVenta").show();
        $("#btnFinalizarPedido").show();
        
        obtenerDetalles(id);
    });

    function obtenerDetalles(id) {
        $.ajax({
            type: "POST",
            url: "?pagina=chequeo",
            data: { accion: 'obtenerDetalleProductos', id: id },
            success: function(response) {
                let productos = JSON.parse(response);
                let html = "";
                productos.forEach(p => {
                    html += `
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-dark rounded">
                        <span>${p.cantidad}x ${p.nombre_producto}</span>
                        <span class="text-white-50">$${(p.cantidad * p.precio_unitario).toFixed(2)}</span>
                    </div>`;
                });
                $("#lista_productos_solicitud").html(html);
                new bootstrap.Modal('#modalProcesarSolicitud').show();
            }
        });
    }

    $("#btnFinalizarPedido").click(function() {
        let datos = {
            accion: 'gestionarPedido',
            tipo: 'aceptar',
            id: $("#id_solicitud_input").val(),
            referencia: $("#pago_referencia").val(),
            envio: $("#envio_tipo").val(),
            notas: $("#envio_notas").val()
        };

        if(!datos.referencia) {
            mensaje("errorCustom", "Debes ingresar la referencia del pago");
            return;
        }

        gestionar(datos);
    });

    $(document).on("click", ".btn-rechazarPedido", function () {
        let id = $(this).data("id");
        mensaje("deshabilitar", "esto devolverá los productos al stock actual", function() {
            gestionar({ accion: 'gestionarPedido', tipo: 'cancelar', id: id });
        });
    });

    function gestionar(datos) {
        Swal.fire({
            title: "Procesando!",
            timer: 800,
            color: "white",
            background: "#000910",
            didOpen: () => { Swal.showLoading(); },
        }).then(() => {
            $.ajax({
                type: "POST",
                url: "?pagina=chequeo",
                data: datos,
                success: function (response) {
                    var res = JSON.parse(response);
                    if (res.success) {
                        bootstrap.Modal.getInstance('#modalProcesarSolicitud')?.hide();
                        mensaje("success");
                        $("#tablaChequeo").DataTable().ajax.reload();
                    } else {
                        mensaje("error");
                    }
                }
            });
        });
    }

    function mensaje(accion, texto, funcion) {
        let config = { color: "white", background: "#000910", confirmButtonColor: "rgb(238, 191, 0)" };

        if (accion == "error") {
            Swal.fire({ ...config, title: "Ups!", text: "Error en el Servidor!", icon: "error" });
        } else if (accion == "errorCustom") {
            Swal.fire({ ...config, title: "Atención", text: texto, icon: "warning" });
        } else if (accion == "deshabilitar") {
            Swal.fire({ ...config, title: "Rechazar Pedido", text: texto, icon: "warning", showCancelButton: true, confirmButtonText: "Si, Rechazar", confirmButtonColor: "#d33" }).then((r) => { if (r.isConfirmed) funcion(); });
        } else {
            Swal.fire({ ...config, title: "Listo!", text: "Operación Exitosa!", icon: "success", showConfirmButton: false, timer: 1500 });
        }
    }
});
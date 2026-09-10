$(document).ready(function () {
    let permisos = { listar: false, marcar_leida: false };

    $.get("?pagina=notificacion&permisos=true", function (data) {
        permisos = data;
        cargarTabla(false);
    });

    $("#toggleHistorial").on("change", function() {
        let mostrarHistorial = $(this).is(":checked");
        $("#tablaNotificaciones").DataTable().ajax.url(`?pagina=notificacion&ajax=true&x=listar&historial=${mostrarHistorial}`).load();
    });

    function cargarTabla(historial) {
        $("#tablaNotificaciones").DataTable({
            destroy: true,
            ajax: {
                url: `?pagina=notificacion&ajax=true&x=listar&historial=${historial}`,
                dataSrc: "data",
            },
            columns: [
                { data: "id_notificacion", visible: false },
                { data: "mensaje" },
                { 
                    data: "tipo",
                    render: function (data, type, row) {
                        let tipoMinus = data ? data.toLowerCase() : "";
                        switch (tipoMinus) {
                            case "alerta":
                                return '<td><span class="badge bg-danger text-capitalize">Alerta</span></td>';
                            case "aviso":
                                return '<td><span class="badge bg-warning text-capitalize">Aviso</span></td>';
                            case "info":
                                return '<td><span class="badge bg-info text-capitalize">Info</span></td>';
                            case "pago":
                                return '<td><span class="badge bg-success text-capitalize">Pago</span></td>';
                            case "recordatorio":
                                return '<td><span class="badge bg-primary text-capitalize">Recordatorio</span></td>';
                            case "registro":
                                return '<td><span class="badge bg-secondary text-capitalize">Registro</span></td>';
                            case "pago_cuota":
                                return '<td><span class="badge bg-success text-capitalize">Pago Cuota</span></td>';
                            case "stock":
                                return '<td><span class="badge bg-dark text-capitalize">Stock</span></td>';
                            case "eliminacion":
                                return '<td><span class="badge bg-danger text-capitalize">Eliminación</span></td>';
                            case "modificacion":
                                return '<td><span class="badge bg-warning text-capitalize">Modificación</span></td>';
                            default:
                                return data;
                        }
                    }
                },
                { data: "fecha_creacion" },
                {
                    data: null,
                    render: function (data, type, row) {
                        if (row.leida == 1) {
                            return '<span class="badge bg-success text-capitalize">Leída</span>';
                        }
                        return `<button type="button" class="btn btn-info btn-marcar" data-id="${row.id_notificacion}">Marcar como leída</button>`;
                    },
                },
            ],
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _PAGE_ de _PAGES_",
                paginate: { next: "Siguiente", previous: "Anterior" }
            }
        });
    }

    $(document).on("click", ".btn-marcar", function () {
        let id = $(this).data("id");
        mensaje("pregunta", "¿Marcar como leída?", function() { marcarLeida(id); });
    });

    function marcarLeida(id) {
        $.ajax({
            type: "POST",
            url: "?pagina=notificacion&ajax=true&x=marcar_leida",
            data: { id: id },
            success: function (res) {
                if (res.success) {
                    mensaje("success");
                    $("#tablaNotificaciones").DataTable().ajax.reload(null, false);
                    
                    if (typeof window.parent.actualizarNotificaciones === "function") {
                        window.parent.actualizarNotificaciones();
                    } else if (typeof actualizarNotificaciones === "function") {
                        actualizarNotificaciones();
                    }
                } else {
                    mensaje("error", "Error al actualizar");
                }
            }
        });
    }

    function mensaje(accion, mensaje, funcion, title) {
        const configBase = { color: "white", background: "#000910", confirmButtonColor: "rgb(238, 191, 0)" };
        if (accion == "pregunta") {
            Swal.fire({ ...configBase, title: mensaje, icon: "question", showCancelButton: true })
            .then((result) => { if (result.isConfirmed && funcion) funcion(); });
        } else {
            Swal.fire({ ...configBase, title: "Proceso Exitoso", icon: "success", timer: 1500 });
        }
    }
});
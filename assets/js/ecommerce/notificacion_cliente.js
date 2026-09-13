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
                { data: "tipo" },
                { data: "fecha_creacion" },
                {
                    data: null,
                    render: function (data, type, row) {
                        if (row.leida == 1) {
                            return '<span class="badge badge-success">Leída</span>';
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
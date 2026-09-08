function cargarBitacora(usuario = "", accionU = "") {
    $.ajax({
        url: "?pagina=bitacora&ajax=true&tipo=consultar_movimientos",
        type: "POST",
        data: { usuario: usuario, accionU: accionU },
        dataType: "json",
        success: function(data) {
            if ($.fn.DataTable.isDataTable("#tablaBitacora")) {
                $("#tablaBitacora").DataTable().destroy();
            }

            let tbody = "";
            if (Array.isArray(data) && data.length > 0) {
                tbody = data.map(v => {
                    let accionTexto = "";
                    switch (v.accion) {
                        case "Iniciar Sesion": accionTexto = "El usuario ha Iniciado Sesión"; break;
                        case "Cerrar Sesion": accionTexto = "El usuario ha Cerrado Sesión"; break;
                        case "Registrar": accionTexto = `Registro en ${v.modulo}`; break;
                        case "Modificar": accionTexto = `Modificación en ${v.modulo}`; break;
                        case "Eliminar": accionTexto = `Eliminación en ${v.modulo}`; break;
                        case "Consultar": accionTexto = `Consulta en ${v.modulo}`; break;
                        case "Acceder": accionTexto = `Accedió a ${v.modulo}`; break;
                        default: accionTexto = (v.accion && v.accion.includes("Acceder")) ? `Accedió a ${v.modulo}` : v.accion;
                    }
                    return `<tr>
                        <td>${v.rol || 'Sin Rol'}</td>
                        <td>${v.usuario_info || 'Sin datos'}</td>
                        <td>${accionTexto}</td>
                        <td>${v.modulo || ''}</td>
                        <td>${v.fecha_registro || ''}</td>
                        <td>
                            <button class="btn btn-sm btn-info ver-detalles" 
                                data-antiguo='${v.valor_antiguo || 'Sin datos'}' 
                                data-nuevo='${v.valor_nuevo || 'Sin datos'}'>
                                Ver Detalles
                            </button>
                        </td>
                    </tr>`;
                }).join('');
            }

            $("#tablaBitacora").html(`<thead><tr><th>Rol</th><th>Usuario</th><th>Acción</th><th>Módulo</th><th>Fecha</th><th>Detalles</th></tr></thead><tbody>${tbody}</tbody>`);

            $("#tablaBitacora").DataTable({
                pageLength: 16,
                lengthMenu: [[8, 16, 32], ["8", "16", "32"]],
                order: [[4, "desc"]],
                language: {
                    processing: "Procesando...",
                    lengthMenu: "Mostrar _MENU_ registros",
                    zeroRecords: "No se encontraron resultados",
                    info: "Mostrando _START_ al _END_ de _TOTAL_ registros",
                    search: "Buscar:",
                    paginate: { first: "Primero", last: "Último", next: "Siguiente", previous: "Anterior" }
                }
            });
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}

$(document).ready(function() {
    cargarBitacora();

    $(document).on("click", ".ver-detalles", function() {
        $("#modalAntiguo").text($(this).data("antiguo"));
        $("#modalNuevo").text($(this).data("nuevo"));
        $("#modalDetallesBitacora").modal("show");
    });

    $("#btnFiltrar").on("click", function() {
        let u = $("#selectUsuario").val();
        let a = $("#selectAccion").val();
        cargarBitacora(u, a);
    });
});
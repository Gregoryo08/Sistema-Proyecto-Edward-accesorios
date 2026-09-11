let bitacoraMasterData = [];

function inicializarSelect2() {
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        allowClear: true
    });
}

function escapeHtml(str) {
    if (!str) return 'Sin datos';
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function cargarBitacora() {
    $.ajax({
        url: "?pagina=bitacora&ajax=true&tipo=consultar_movimientos",
        type: "POST",
        dataType: "json",
        success: function(data) {
            if (Array.isArray(data)) {
                bitacoraMasterData = data;
                poblacionesSelects(bitacoraMasterData);
                aplicarFiltrosYRenderizar();
            }
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}

function aplicarFiltrosYRenderizar() {
    let u = $("#selectUsuario").val() || "";
    let a = $("#selectAccion").val() || "";
    let r = $("#filtroRoles").val() || "";

    let datosFiltrados = bitacoraMasterData.filter(v => {
        let matchRol = true;
        if (r !== "") {
            let rolRegistro = (v.rol || "").trim().toLowerCase();
            matchRol = (rolRegistro === r.trim().toLowerCase());
        }

        let matchUsuario = true;
        if (u !== "") {
            let uLower = u.toLowerCase();
            let userInfo = (v.usuario_info || "").toLowerCase();
            let userId = (v.usuario_id || v.cedula || v.id_usuario || "").toString().toLowerCase();
            matchUsuario = userInfo.includes(uLower) || userId.includes(uLower);
        }

        let matchAccion = true;
        if (a !== "") {
            let aLower = a.toLowerCase();
            let accionOriginal = (v.accion || "").toLowerCase();
            matchAccion = accionOriginal.includes(aLower);
        }

        return matchRol && matchUsuario && matchAccion;
    });

    renderizarTabla(datosFiltrados);
}

function renderizarTabla(data) {
    if ($.fn.DataTable.isDataTable("#tablaBitacora")) {
        $("#tablaBitacora").DataTable().clear().destroy();
        $("#tablaBitacora").empty();
    }

    let tbody = "";
    if (Array.isArray(data) && data.length > 0) {
        tbody = data.map(v => {
            let moduloInfo = v.modulo || '';
            let accionTexto = "";
            switch (v.accion) {
                case "Iniciar Sesion": accionTexto = "El usuario ha Iniciado Sesión"; break;
                case "Cerrar Sesion": accionTexto = "El usuario ha Cerrado Sesión"; break;
                case "Registrar": accionTexto = `Registro en ${moduloInfo}`; break;
                case "Modificar": accionTexto = `Modificación en ${moduloInfo}`; break;
                case "Eliminar": accionTexto = `Eliminación en ${moduloInfo}`; break;
                case "Consultar": accionTexto = `Consulta en ${moduloInfo}`; break;
                case "Acceder": accionTexto = `Accedió a ${moduloInfo}`; break;
                default: accionTexto = (v.accion && v.accion.includes("Acceder")) ? `Accedió a ${moduloInfo}` : (v.accion || '');
            }

            let fechaOriginal = v.fecha_registro || '';
            let timestampOrden = fechaOriginal;
            if (fechaOriginal.includes('/')) {
                let partes = fechaOriginal.split(' ');
                let fechaPartes = partes[0].split('/');
                if (fechaPartes.length === 3) {
                    let dia = fechaPartes[0].padStart(2, '0');
                    let mes = fechaPartes[1].padStart(2, '0');
                    let anio = fechaPartes[2];
                    timestampOrden = `${anio}-${mes}-${dia} ${partes[1] || '00:00:00'}`;
                }
            }

            let valAntiguo = escapeHtml(v.valor_antiguo);
            let valNuevo = escapeHtml(v.valor_nuevo);

            return `<tr>
                <td>${escapeHtml(v.rol || 'Sin Rol')}</td>
                <td>${escapeHtml(v.usuario_info || 'Sin datos')}</td>
                <td>${escapeHtml(accionTexto)}</td>
                <td>${escapeHtml(moduloInfo)}</td>
                <td data-order="${timestampOrden}">${escapeHtml(fechaOriginal)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-info ver-detalles" 
                        data-antiguo="${valAntiguo}" 
                        data-nuevo="${valNuevo}">
                        Ver Detalles
                    </button>
                </td>
            </tr>`;
        }).join('');
    }

    $("#tablaBitacora").html(`<thead><tr><th style="text-align: center; font-weight: bold;">Rol</th><th style="text-align: center; font-weight: bold;">Usuario</th><th style="text-align: center; font-weight: bold;">Acción</th><th style="text-align: center; font-weight: bold;">Módulo</th><th style="text-align: center; font-weight: bold;">Fecha</th><th style="text-align: center; font-weight: bold;">Detalles</th></tr></thead><tbody>${tbody}</tbody>`);

    $("#tablaBitacora").DataTable({
        pageLength: 16,
        lengthMenu: [[8, 16, 32], ["8", "16", "32"]],
        order: [[4, "desc"]],
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            info: "Mostrando _START_ al _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            search: "Buscar:",
            paginate: { first: "Primero", last: "Último", next: "Siguiente", previous: "Anterior" }
        }
    });
}

function poblacionesSelects(data) {
    if (!Array.isArray(data)) return;

    if ($("#filtroRoles option").length <= 1) {
        let rolesUnicos = [...new Set(data.map(item => item.rol ? item.rol.trim() : '').filter(r => r && r !== 'N/A' && r !== 'Sin Rol'))];
        rolesUnicos.sort().forEach(rol => {
            $("#filtroRoles").append(new Option(rol, rol, false, false));
        });
        $("#filtroRoles").trigger('change.select2');
    }

    if ($("#selectUsuario option").length <= 1) {
        let mapUsuarios = new Map();
        data.forEach(item => {
            let userVal = item.usuario_info || item.usuario_id || item.cedula;
            if (userVal && userVal !== 'Sin datos' && !mapUsuarios.has(userVal)) {
                mapUsuarios.set(userVal, userVal);
            }
        });

        Array.from(mapUsuarios.keys()).sort().forEach(uText => {
            $("#selectUsuario").append(new Option(uText, uText, false, false));
        });
        $("#selectUsuario").trigger('change.select2');
    }

    if ($("#selectAccion option").length <= 1) {
        let accionesUnicas = [...new Set(data.map(item => item.accion ? item.accion.trim() : '').filter(a => a))];
        accionesUnicas.sort().forEach(acc => {
            $("#selectAccion").append(new Option(acc, acc, false, false));
        });
        $("#selectAccion").trigger('change.select2');
    }
}

$(document).ready(function() {
    inicializarSelect2();
    cargarBitacora();

    $(document).on("click", ".ver-detalles", function() {
        let antiguo = $(this).attr("data-antiguo");
        let nuevo = $(this).attr("data-nuevo");

        let txtContainer = document.createElement("textarea");
        txtContainer.innerHTML = antiguo;
        let antiguoDecoded = txtContainer.value;

        txtContainer.innerHTML = nuevo;
        let nuevoDecoded = txtContainer.value;

        $("#modalAntiguo").text(antiguoDecoded);
        $("#modalNuevo").text(nuevoDecoded);
        $("#modalDetallesBitacora").modal("show");
    });

    $("#btnFiltrar").on("click", function() {
        aplicarFiltrosYRenderizar();
    });

    $("#btnLimpiar").on("click", function() {
        $("#selectUsuario").val("").trigger("change");
        $("#selectAccion").val("").trigger("change");
        $("#filtroRoles").val("").trigger("change");
        aplicarFiltrosYRenderizar();
    });
});
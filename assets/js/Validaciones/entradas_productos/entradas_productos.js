$(document).ready(function () {
    let permisos = { registrar: false, modificar: false, eliminar: false, consultar: false };
    let categoriaRepuestos = null;
    let categoriaTelefono = null;
    let listo = false;

    $.get("?pagina=entradas_productos&permisos=true", function (data) {
        permisos = typeof data === 'string' ? JSON.parse(data) : data;
        if (permisos.registrar) {
            $("#btnAgregarEntrada").show();
        }
        inicializarTablaEntradas();
        cargarCatalogos();
    });

    function inicializarTablaEntradas() {
        $("#tablaEntradasProductos").DataTable({
            destroy: true,
            ajax: {
                url: "?pagina=entradas_productos&ajax=true&x=listado",
                dataSrc: ""
            },
            columns: [
                { data: "nom_proveedor", title: "Proveedor" },
                { data: "fecha_formateada", title: "Fecha" },
                {
                    data: null,
                    title: "Acciones",
                    render: function(data, type, row) {
                        let btnVer = `<button type="button" class="btn btn-info btn-sm btn-ver-detalles" data-id="${row.id_entrada}" title="Ver Detalles">
                            <i class="fa-solid fa-eye"></i>
                        </button> `;

                        let btnModificar = '';
                        if (permisos.modificar) {
                            btnModificar = `<button type="button" class="btn btn-warning btn-sm btn-editar-entrada" data-id="${row.id_entrada}" title="Modificar Renglones">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button> `;
                        }

                        let btnEliminar = '';
                        if (permisos.eliminar) {
                            btnEliminar = `<button type="button" class="btn btn-danger btn-sm btn-eliminar-entrada" data-id="${row.id_entrada}" title="Eliminar Entrada">
                                <i class="fa-solid fa-trash"></i></button>`;
                        }

                        return `<div class="text-center">${btnVer}${btnModificar}${btnEliminar}</div>`;
                    }
                }
            ],
            pageLength: 4,
            lengthMenu: [[4, 8, 12, 16], ["4", "8", "12", "16"]],
            columnDefs: [
                { className: "dt-head-center dt-body-center", targets: "_all" }
            ],
            language: {
                "decimal": "",
                "emptyTable": "No hay datos disponibles en la tabla",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron registros coincidentes",
                "paginate": { "first": "Primero", "last": "Último", "next": "Siguiente", "previous": "Anterior" }
            }
        });
    }

    function limpiarEstadoModales() {
        $('.modal').each(function() {
            if ($(this).data('bs.modal')) {
                $(this).modal('hide');
            }
        });
        $(".modal-backdrop").remove();
        $("body").removeClass("modal-open").css({ "overflow": "", "padding-right": "" });
    }

    $('.modal').on('hidden.bs.modal', function () {
        if ($("#proveedorSelect").data('select2')) $("#proveedorSelect").select2('close');
        if ($("#productoSelect").data('select2')) $("#productoSelect").select2('close');
    });

    function cargarCatalogos() {
        listo = false;
        
        $.get("?pagina=entradas_productos&ajax=true&x=proveedores", function(res) {
            let proveedores = typeof res === 'string' ? JSON.parse(res) : res;
            let select = $("#proveedorSelect");
            
            if (select.hasClass("select2-hidden-accessible")) select.select2('destroy'); 
            
            let opt = '<option value="">Selecciona un Proveedor</option>';
            if (Array.isArray(proveedores)) {
                proveedores.forEach(p => opt += `<option value="${p.rif_proveedor}">${p.nombre_proveedor}</option>`);
            }
            select.html(opt);
            if (select.length) select.select2({ dropdownParent: $("#modalReponerProducto") });
        });

        $.get("?pagina=entradas_productos&ajax=true&x=productos", function(res) {
            let productos = typeof res === 'string' ? JSON.parse(res) : res;
            let select = $("#productoSelect");
            
            if (select.hasClass("select2-hidden-accessible")) select.select2('destroy'); 
            
            let opt = '<option value="">Selecciona un producto</option>';
            if (Array.isArray(productos)) {
                productos.forEach(p => {
                    let idCat = parseInt(p.id_categoria);
                    opt += `<option value="${p.id_producto}" data-categoria="${idCat}">${p.nombre_producto}</option>`;
                    
                    if (p.nombre_categoria && p.nombre_categoria.toLowerCase() === 'repuestos') {
                        categoriaRepuestos = idCat;
                    }
                    if (p.nombre_categoria && p.nombre_categoria.toLowerCase() === 'telefono') {
                        categoriaTelefono = idCat;
                    }
                });
            }
            select.html(opt);
            if (select.length) select.select2({ dropdownParent: $("#modalReponerProducto") });
            listo = true;
        });
    }

    $(document).on("change", "#productoSelect", function() {
        if (!listo) { return; }
        
        var id_producto = $(this).val();
        if (!id_producto) return;
        var productoNombre = $("#productoSelect option:selected").text();
        var id_categoria = parseInt($("#productoSelect option:selected").data("categoria"));

        var yaExiste = false;
        $("#tablaEntradaProductos tbody tr").each(function() {
            if ($(this).find(".id-producto").text().trim() === id_producto) { yaExiste = true; return false; }
        });

        if (yaExiste) { $(this).val("").trigger("change.select2"); return; }

        var esRepuesto = (categoriaRepuestos !== null && id_categoria === categoriaRepuestos);
        var esTelefono = (categoriaTelefono !== null && id_categoria === categoriaTelefono);

        var columnaGarantia = esRepuesto ? `
            <td style="text-align: center; vertical-align: middle;">
                <div class="form-check form-switch d-flex justify-content-center align-items-center gap-2">
                    <input type="checkbox" class="form-check-input toggle-garantia" role="switch" style="width: 40px; height: 20px; cursor: pointer;">
                    <input type="number" class="form-control input-garantia d-none" style="width: 70px;" placeholder="Días" min="1" disabled>
                    <span class="badge bg-secondary badge-garantia d-none">
                        <i class="fa-solid fa-shield-halved me-1"></i><span class="dias-texto">0</span> días
                    </span>
                </div>
            </td>
        ` : `<td class="d-none"><input type="text" class="d-none" disabled></td>`;

        var celdaCantidad = esTelefono
            ? `<td style="text-align: center;"><textarea class="form-control input-imeis" rows="2" style="width: 240px; text-align: left;" placeholder="IMEIs (uno por línea)"></textarea></td>`
            : `<td style="text-align: center;"><input type="text" class="form-control input-cantidad" style="width: 80px; text-align: center;" placeholder="Cant"></td>`;

        var nuevaFila = `<tr data-categoria="${id_categoria}">
            <td style="text-align: center;" class="id-producto">${id_producto}</td>
            <td style="text-align: center;">${productoNombre}</td>
            ${celdaCantidad}
            ${columnaGarantia}
            <td style="text-align: center;"><button type="button" class="btn btn-danger btn-sm eliminar-fila">X</button></td>
        </tr>`;

        $("#tablaEntradaProductos tbody").append(nuevaFila);
        $(this).val("").trigger("change.select2");
        validarBotones();
    });

    $(document).on("click", ".eliminar-fila", function() { $(this).closest("tr").remove(); validarBotones(); });
    $(document).on("input", ".input-cantidad", function() { $(this).val($(this).val().replace(/[^0-9.]/g, '')); validarBotones(); });
    $(document).on("input", ".input-imeis", function() { validarBotones(); });

    $(document).on("change", ".toggle-garantia", function() {
        var estaActivo = $(this).is(":checked");
        var fila = $(this).closest("tr");
        var inputDias = fila.find(".input-garantia");
        var badge = fila.find(".badge-garantia");
        var diasTexto = fila.find(".dias-texto");

        if (estaActivo) {
            inputDias.removeClass("d-none").prop("disabled", false).focus();
            badge.removeClass("d-none").removeClass("bg-secondary").addClass("bg-success");
            diasTexto.text("0");
        } else {
            inputDias.addClass("d-none").prop("disabled", true).val("");
            badge.addClass("d-none").removeClass("bg-success").addClass("bg-secondary");
            diasTexto.text("0");
        }
        validarBotones();
    });

    $(document).on("input", ".input-garantia", function() {
        var dias = $(this).val() || "0";
        var fila = $(this).closest("tr");
        var badge = fila.find(".badge-garantia");
        var diasTexto = fila.find(".dias-texto");
        diasTexto.text(dias);
        badge.removeClass(dias > 0 ? "bg-secondary" : "bg-success").addClass(dias > 0 ? "bg-success" : "bg-secondary");
        validarBotones();
    });

    function obtenerImeisFila(fila) {
        var texto = fila.find(".input-imeis").val() || "";
        return texto.split(/\r?\n/).map(function(s) { return s.trim(); }).filter(function(s) { return s !== ""; });
    }

    function validarBotones() {
        if (typeof window.verificarFormularioEntrada === "function") {
            window.verificarFormularioEntrada();
            return;
        }

        var filas = $("#tablaEntradaProductos tbody tr");
        var hayFilas = filas.length > 0;
        var valido = true;
        var imeisVistos = [];
        var hayImeisRepetidos = false;

        filas.each(function() {
            var fila = $(this);
            var tieneImeis = fila.find(".input-imeis").length > 0;

            if (tieneImeis) {
                var imeis = obtenerImeisFila(fila);
                if (imeis.length === 0) { valido = false; }
                imeis.forEach(function(imei) {
                    if (!/^[0-9]{10,25}$/.test(imei)) { valido = false; }
                    if (imeisVistos.indexOf(imei) !== -1) { hayImeisRepetidos = true; }
                    imeisVistos.push(imei);
                });
            } else {
                var cant = fila.find(".input-cantidad").val();
                if (!cant || parseFloat(cant) <= 0) { valido = false; }
            }

            var toggleGarantia = fila.find(".toggle-garantia");
            if (toggleGarantia.length > 0 && toggleGarantia.is(":checked")) {
                var dias = parseInt(fila.find(".input-garantia").val()) || 0;
                if (dias <= 0) { valido = false; }
            }
        });

        if (hayImeisRepetidos) { valido = false; }

        $("#guardarEntrada").prop("disabled", !hayFilas || !valido);
    }

    $(document).on("click", ".btn-ver-detalles, .btn-editar-entrada", function(e) {
        e.preventDefault();
        var id_entrada = $(this).data("id");
        if (!id_entrada) return;

        $.ajax({
            url: "?pagina=entradas_productos", 
            method: "POST", 
            data: { accion: "ver_detalles", id: id_entrada }, 
            dataType: "json",
            success: function(respuesta) {
                var html = "";
                if (respuesta && respuesta.length > 0) {
                    respuesta.forEach(function(p) {
                        var garantiaBadge = p.dias_garantia 
                            ? `<span class="badge bg-success"><i class="fa-solid fa-shield-halved me-1"></i>${p.dias_garantia} días</span>`
                            : `<span class="badge bg-secondary">Sin garantía</span>`;

                        var esTelefono = parseInt(p.es_telefono) === 1;
                        var imeis = Array.isArray(p.imeis) ? p.imeis : [];
                        var celdaCantidad = esTelefono
                            ? `<div>${imeis.length} unidad(es)</div><div style="font-size: 0.72rem; font-family: monospace; word-break: break-all;">${imeis.join('<br>')}</div>`
                            : `${p.cantidad_entrada}`;
                        var dataImeis = JSON.stringify(imeis).replace(/"/g, '&quot;');

                        html += `<tr>
                            <td>${p.nombre_producto}</td>
                            <td>${celdaCantidad}</td>
                            <td>${garantiaBadge}</td>
                            <td><button type="button" class="btn btn-warning btn-sm btn-modificar-detalle"
                                data-id_entrada="${p.id_entrada_fk}" data-id_producto="${p.id_producto_fk}"
                                data-cantidad="${p.cantidad_entrada}" data-garantia="${p.dias_garantia}"
                                data-es-telefono="${esTelefono ? 1 : 0}" data-imeis="${dataImeis}">
                                <i class="fa-solid fa-pen-to-square"></i></button></td>
                        </tr>`;
                    });
                } else { html = '<tr><td colspan="4">No se encontraron productos</td></tr>'; }
                
                $("#detalleEntrada tbody").html(html);
                limpiarEstadoModales();
                $("#modalDetalleEntrada").modal("show");
            }
        });
    });

    $(document).on("click", ".btn-modificar-detalle", function() {
        var garantia = $(this).data("garentia") || $(this).data("garantia");
        var esTelefono = parseInt($(this).attr("data-es-telefono")) === 1;
        var imeis = $(this).attr("data-imeis");
        if (typeof imeis === "string") { try { imeis = JSON.parse(imeis); } catch (e) { imeis = []; } }
        if (!Array.isArray(imeis)) { imeis = []; }

        $("#id_entrada").val($(this).data("id_entrada"));
        $("#producto_entrada").val($(this).data("id_producto"));
        $("#cantidadOLD_entrada").val($(this).data("cantidad"));
        $("#garantiaOLD_entrada").val(garantia || "");
        $("#garantiaEntradaM").val(garantia || "");

        if (esTelefono) {
            $("#campoCantidadModificar").hide();
            $("#cantidadEntradaM").val(imeis.length).removeClass("is-valid is-invalid");
            $("#imeisEntradaM").val(imeis.join("\n")).removeClass("is-valid is-invalid");
            $("#campoImeisModificar").show();
        } else {
            $("#campoImeisModificar").hide();
            $("#imeisEntradaM").val("");
            $("#cantidadEntradaM").val($(this).data("cantidad")).removeClass("is-valid is-invalid");
            $("#campoCantidadModificar").show();
        }

        $("#modalDetalleEntrada").modal("hide");
        setTimeout(() => { $("#modalModificarEntrada").modal("show"); }, 300);
    });

    $(document).on("click", "#modificarDatosE2", function() {
        var esTelefono = $("#campoImeisModificar").is(":visible");
        var imeis = [];

        if (esTelefono) {
            imeis = ($("#imeisEntradaM").val() || "").split(/\r?\n/)
                .map(function(s) { return s.trim(); })
                .filter(function(s) { return s !== ""; });

            var vistos = [];
            var valido = imeis.length > 0;
            imeis.forEach(function(i) {
                if (!/^[0-9]{10,25}$/.test(i)) { valido = false; }
                if (vistos.indexOf(i) !== -1) { valido = false; }
                vistos.push(i);
            });

            if (!valido) {
                Swal.fire({ title: "Atención", text: "Revise los IMEI: entre 10 y 25 dígitos, sin repetidos.", icon: "warning", background: "#000910", color: "white" });
                return;
            }
        }

        var datos = {
            accion: "modificar_cantidad",
            id_entrada: $("#id_entrada").val(),
            id_producto: $("#producto_entrada").val(),
            id_producto_fk: $("#producto_entrada").val(),
            cantidadOLD: $("#cantidadOLD_entrada").val(),
            cantidadNEW: esTelefono ? imeis.length : $("#cantidadEntradaM").val(),
            garantiaNEW: $("#garantiaEntradaM").val()
        };
        if (esTelefono) { datos.imeis = imeis; }

        Swal.fire({
            title: '¿Guardar Cambios?',
            text: esTelefono
                ? "Se actualizará la lista de IMEI de este teléfono en la entrada."
                : "Se modificará la cantidad y garantía de este producto en la entrada.",
            icon: 'question',
            background: '#000910',
            color: '#ffffff',
            showCancelButton: true,
            confirmButtonColor: '#f39c12',
            cancelButtonColor: '#d63031',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                procesarPeticion(datos, "#modalModificarEntrada", "modificar");
            }
        });
    });

    $("#guardarEntrada").click(function() {
        var rif_proveedor = $("#proveedorSelect").val();
        var productos = [];

        $("#tablaEntradaProductos tbody tr").each(function() {
            var fila = $(this);
            var id_p = fila.find(".id-producto").text().trim();
            var toggle = fila.find(".toggle-garantia");
            var garantiaValue = (toggle.length > 0 && toggle.is(":checked")) ? parseInt(fila.find(".input-garantia").val()) || null : null;

            if (fila.find(".input-imeis").length > 0) {
                var imeis = obtenerImeisFila(fila);
                if (id_p && imeis.length > 0) {
                    productos.push({ id_producto: parseInt(id_p), cantidad: imeis.length, dias_garantia: garantiaValue, imeis: imeis });
                }
            } else {
                var cant = fila.find(".input-cantidad").val();
                if (id_p && cant) {
                    productos.push({ id_producto: parseInt(id_p), cantidad: parseFloat(cant), dias_garantia: garantiaValue });
                }
            }
        });

        if (!rif_proveedor || productos.length === 0) {
            Swal.fire({ title: "Atención", text: "Complete los campos obligatorios", icon: "warning", background: "#000910", color: "white" });
            return;
        }

        var datos = { accion: "registrar_entrada", rif_proveedor: rif_proveedor, productos: productos };

        Swal.fire({
            title: '¿Registrar Entrada?',
            text: "Se registrarán los productos y se actualizará el inventario.",
            icon: 'info',
            background: '#000910',
            color: '#ffffff',
            showCancelButton: true,
            confirmButtonColor: '#27ae60',
            cancelButtonColor: '#d63031',
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                procesarPeticion(datos, "#modalReponerProducto", "registrar");
            }
        });
    });

    $(document).on("click", ".btn-eliminar-entrada", function() {
        var id_entrada = $(this).data("id");

        Swal.fire({
            title: '¿Eliminar Entrada?',
            text: "Se eliminará la entrada y se revertirá el inventario cargado. Esta acción no se puede deshacer.",
            icon: 'warning',
            background: '#000910',
            color: '#ffffff',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#636e72',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                var datos = { accion: "eliminar_entrada", id: id_entrada };
                procesarPeticion(datos, "ninguno", "eliminar");
            }
        });
    });

    function procesarPeticion(datos, modalId, tipoOperacion) {
     
        if (modalId !== "ninguno" && modalId === "#modalReponerProducto") {
            const form = $(modalId + " form");
            let esValido = true;
            const valProveedor = form.find('#proveedorSelect').val();
            const existeProveedor = form.find('#proveedorSelect option[value="' + valProveedor + '"]').length > 0;
            
            if (valProveedor && !existeProveedor) esValido = false;

            if (!esValido) {
                Swal.fire({
                    title: "Acceso Denegado",
                    text: "Se ha detectado una manipulación en los campos del formulario.",
                    icon: "error",
                    background: '#000910',
                    color: '#ffffff',
                    confirmButtonColor: '#d63031'
                });
                return;
            }
        }

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
                            titulo = '¡Entrada Registrada!';
                            texto = 'La entrada de productos se ha registrado exitosamente.';
                        } else if (tipoOperacion === 'modificar') {
                            titulo = '¡Entrada Modificada!';
                            texto = 'Los cambios se han guardado exitosamente.';
                        } else if (tipoOperacion === 'eliminar') {
                            titulo = '¡Entrada Eliminada!';
                            texto = 'La entrada de productos ha sido eliminada exitosamente.';
                        }

                        limpiarEstadoModales();

                        if (modalId && modalId !== "ninguno" && $(modalId + " form").length) {
                            $(modalId + " form")[0].reset();
                        }
                        if ($("#formReponerProducto").length) {
                            $("#formReponerProducto")[0].reset();
                        }

                        $("select").val("").trigger("change.select2");
                        $("#tablaEntradaProductos tbody").empty();

                        if (typeof cargarCatalogos === "function") cargarCatalogos();
                        if ($.fn.DataTable.isDataTable("#tablaEntriesProductos")) {
                            $("#tablaEntradasProductos").DataTable().ajax.reload(null, false);
                        } else {
                            $("#tablaEntradasProductos").DataTable().ajax.reload(null, false);
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

    $(document).on("click", "#btnAgregarEntrada", function () {
        limpiarEstadoModales();
        $("#modalReponerProducto").modal('show');
    });
});
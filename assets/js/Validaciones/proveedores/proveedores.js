$(document).ready(function () {
    let permisos = { registrar: false, modificar: false, eliminar: false, control_total: false };

    $.get("?pagina=proveedores&permisos=true", function (data) {
        permisos = JSON.parse(data);
        if (permisos.control_total || permisos.registrar) {
            $("#btn_nuevo_proveedor").show();
        } else {
            $("#btn_nuevo_proveedor").hide();
        }
        cargarTablaProveedores();
    });

    function cargarTablaProveedores() {
        $("#tablaProveedores").DataTable({
            destroy: true,
            ajax: { 
                url: "?pagina=proveedores&ajax=true&x=proveedores", 
                dataSrc: "" 
            },
            columns: [
                { data: "rif_proveedor" },
                { data: "nombre_proveedor" },
                { data: "telefono_proveedor" },
                {
                    data: null,
                    render: function (data, type, row) {
                        let b = '<div class="btn-group">';
                        
                        b += '<button type="button" class="btn btn-info btn-sm btn_verProveedor" data-rif="' + row.rif_proveedor + '" data-nombre="' + row.nombre_proveedor + '" data-telefono="' + row.telefono_proveedor + '" data-correo="' + encodeURIComponent(row.correo_proveedor || '') + '" data-ubicacion="' + encodeURIComponent(row.ubicacion_proveedor || '') + '"><i class="bi bi-eye"></i></button>';
                        
                        if (permisos.control_total || permisos.modificar) {
                            b += '<button type="button" class="btn btn-warning btn-sm btn_modificarProveedor" data-rif="' + row.rif_proveedor + '" data-nombre="' + row.nombre_proveedor + '" data-telefono="' + row.telefono_proveedor + '" data-correo="' + encodeURIComponent(row.correo_proveedor || '') + '" data-ubicacion="' + encodeURIComponent(row.ubicacion_proveedor || '') + '"><i class="fa-solid fa-pen-to-square"></i></button>';
                        }
                        
                        if (permisos.control_total || permisos.eliminar) {
                            b += '<button type="button" class="btn btn-danger btn-sm btn-eliminar-proveedor" data-rif="' + row.rif_proveedor + '" data-nombre="' + row.nombre_proveedor + '"><i class="fa-solid fa-trash"></i></button>';
                        }
                        
                        return b + '</div>';
                    }
                }
            ],
            language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" }
        });
    }

    // FUNCIÓN LIMPIAR
    function limpiarFormulario() {
        $("#formularioRegistroProveedor")[0].reset();
        $("#prefijo").val("J-");
        $("#codTelefono").val("0414");
    }

    // VER DETALLES
    $(document).on("click", ".btn_verProveedor", function() {
        let btn = $(this);
        $("#consulta_rif").text(btn.data("rif"));
        $("#consulta_proveedor").text(btn.data("nombre"));
        $("#consulta_telefono").text(btn.data("telefono"));
        $("#consulta_correo").text(decodeURIComponent(btn.data("correo")) || "No registrado");
        $("#consulta_ubicacion").text(decodeURIComponent(btn.data("ubicacion")) || "No registrado");
        $("#modalConsultarProveedor").modal("show");
    });

    // REGISTRAR
    $("#btn_registrar").on("click", function() {
        if ($("#proveedor").val().trim() !== "" && $("#rif").val().trim() !== "") {
            showSweetAlert("pregunta1");
        } else {
            showSweetAlert("errorC");
        }
    });

    window.registerData = function() {
        let prefijo = $("#prefijo").val() || "";
        let rifInput = $("#rif").val() || "";
        let rifCompleto = prefijo + rifInput;
        
        let codTel = $("#codTelefono").val() || "";
        let tel = $("#telefono").val() || "";
        let telefonoCompleto = codTel + tel;

        const d = {
            rif: rifCompleto,
            proveedor: $("#proveedor").val(),
            telefono: telefonoCompleto,
            correo: $("#correo").val(),
            ubicacion: $("#ubicacion").val(),
            accion: "registrarProveedor"
        };
        ejecutarAjax(d, "#modalRegistroProveedor");
    };

    // MODIFICAR
    $(document).on("click", ".btn_modificarProveedor", function () {
        let btn = $(this);
        $("#rif_modificar").val(btn.data("rif"));
        $("#proveedor_modificar").val(btn.data("nombre"));
        
        let telefono = btn.data("telefono") || "";
        if (telefono.length >= 4) {
            $("#codTelefonoM").val(telefono.substring(0, 4));
            $("#telefono_modificar").val(telefono.substring(4));
        } else {
            $("#codTelefonoM").val("0414");
            $("#telefono_modificar").val(telefono);
        }
        
        $("#correo_modificar").val(decodeURIComponent(btn.data("correo")) || "");
        $("#ubicacion_modificar").val(decodeURIComponent(btn.data("ubicacion")) || "");
        
        $("#modalModificarProveedor").modal("show");
    });

    $("#btn_modificar").on("click", function() {
        if ($("#proveedor_modificar").val().trim() !== "") {
            showSweetAlert("pregunta2");
        } else {
            showSweetAlert("errorC");
        }
    });

    window.modifyData = function() {
        let codTel = $("#codTelefonoM").val() || "";
        let tel = $("#telefono_modificar").val() || "";
        let telefonoCompleto = codTel + tel;

        const d = {
            rif: $("#rif_modificar").val(),
            proveedor: $("#proveedor_modificar").val(),
            telefono: telefonoCompleto,
            correo: $("#correo_modificar").val(),
            ubicacion: $("#ubicacion_modificar").val(),
            accion: "modificarProveedor"
        };
        ejecutarAjax(d, "#modalModificarProveedor");
    };

    // ELIMINAR
    $(document).on("click", ".btn-eliminar-proveedor", function() {
        $("#rif_proveedor").val($(this).data("rif"));
        window.nombreProveedorEliminar = $(this).data("nombre");
        showSweetAlert("pregunta3");
    });

    window.deleteData = function() {
        const d = { rif: $("#rif_proveedor").val(), accion: "eliminarProveedor" };
        ejecutarAjax(d, null);
    };

    // FUNCIONES AUXILIARES
    function ejecutarAjax(datos, modalId) {
        showProcessingAlert();
        $.ajax({
            type: "POST", 
            url: window.location.href, 
            data: datos, 
            dataType: "json",
            success: function(res) {
                Swal.close();
                if (res.success === true || res.success === "Operación realizada" || res.success === "Eliminado") {
                    if (modalId) $(modalId).modal("hide");
                    $(".modal-backdrop").remove();
                    $("body").removeClass("modal-open");
                    
                    // LIMPIAR después de éxito
                    if (modalId === "#modalRegistroProveedor") {
                        limpiarFormulario();
                    }
                    
                    showSweetAlert("success").then(() => { 
                        $("#tablaProveedores").DataTable().ajax.reload(null, false);
                    });
                } else if (res.invalido) {
                    showSweetAlert("invalido", res.invalido);
                } else {
                    showSweetAlert("invalido", res.error || "Error");
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                showSweetAlert("error");
            }
        });
    }

    const commonSwalMixin = Swal.mixin({ color: "white", background: "#000910", confirmButtonColor: "rgb(238, 191, 0)" });

    function showProcessingAlert() {
        Swal.fire({ title: "Procesando!", timerProgressBar: true, didOpen: () => { Swal.showLoading(); }, background: "#000910", color: "white", allowOutsideClick: false });
    }

    function showSweetAlert(action, message = "") {
        const config = {
            pregunta1: { title: "¿Registrar?", text: "¿Desea guardar este proveedor?", icon: "question", cb: registerData },
            pregunta2: { title: "¿Modificar?", text: "¿Desea guardar los cambios?", icon: "question", cb: modifyData },
            pregunta3: { title: "¿Eliminar?", text: "¿Desea eliminar al proveedor " + window.nombreProveedorEliminar + "?", icon: "warning", cb: deleteData }
        };
        if (config[action]) {
            return commonSwalMixin.fire({ title: config[action].title, text: config[action].text, icon: config[action].icon, showCancelButton: true, confirmButtonText: "Sí, confirmar" }).then((result) => { if (result.isConfirmed) config[action].cb(); });
        }
        const simple = {
            success: { title: "¡Listo!", icon: "success", timer: 1500, showConfirmButton: false },
            errorC: { title: "Campos incompletos", icon: "error" },
            error: { title: "Error de servidor", icon: "error" },
            invalido: { title: "Atención", text: message, icon: "warning" }
        };
        return commonSwalMixin.fire(simple[action]);
    }
});
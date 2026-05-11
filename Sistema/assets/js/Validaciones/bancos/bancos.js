function capitalizarPalabras(cadena) {
    if (!cadena) return "";
    var resultado = cadena.charAt(0).toUpperCase() + cadena.slice(1).toLowerCase();
    resultado = resultado.replace(/(-\w)/g, function (match) {
        return match.toUpperCase();
    });
    return resultado;
}

window.obtenerPermisos = function () {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", window.location.href, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const respuesta = JSON.parse(xhr.responseText);
                    var permisos = respuesta.array.find(
                        (registro) => registro.nombre_modulo === respuesta.modulo
                    );

                    $.ajax({
                        type: "POST",
                        url: "models/registroBitacora.php",
                        data: "accion=bitacora&modulo=" + permisos.nombre_modulo + "&usuario=" + respuesta.usuario,
                        success: function (response) {
                            console.log(response.error ? "no se registro en bitacora" : "se registro en bitacora");
                        },
                    });
                    resolve(permisos);
                } catch (e) {
                    console.error("Error al parsear JSON:", e);
                }
            } else {
                console.error("La solicitud falló con el estado:", xhr.status);
            }
        };
        xhr.send("accion=" + encodeURIComponent("permisos"));
    });
};

obtenerPermisos().then((permisosObtenidos) => {
    if (permisosObtenidos && (permisosObtenidos.control_total === 1 || permisosObtenidos.registrar === 1)) {
        $(".text-right button").css("display", "block");
    }

    if (permisosObtenidos && (permisosObtenidos.control_total === 1 || permisosObtenidos.listar === 1)) {
        $("#tablaBancos").DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            ajax: {
                url: "?pagina=bancos&ajax=true",
                dataSrc: "",
            },
            columns: [
                { data: "nombre_banco" },
                { data: "telefono" },
                { data: "cedula_banco" },
                {
                    data: null,
                    render: function (data, type, row) {
                        var color = row.estatus == "activo" ? "rgb(14, 184, 37)" : "rgb(158, 3, 3)";
                        return `<span class="interruptor" style="background: ${color};">${capitalizarPalabras(row.estatus)}</span>`;
                    },
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        var botonesHTML = "";
                        var btnEliminar = row.estatus == "activo"
                            ? ` <button type="button" class="btn btn-danger btn-eliminar" data-id="${row.id_banco}"><i class="fa-solid fa-trash-can"></i></button>`
                            : ` <button type="button" class="btn btn-success btn-habilitar" data-id="${row.id_banco}"><i class="bi bi-recycle"></i></button>`;

                        if (permisosObtenidos.control_total === 1) {
                            botonesHTML = `<button type="button" class="btn btn-warning btn_modificar" data-toggle="modal" data-target="#modalModificar" data-cedula_banco="${row.id_banco}"><i class="fa-solid fa-pen-to-square"></i></button>` + btnEliminar;
                        } else {
                            if (permisosObtenidos.modificar === 1) {
                                botonesHTML += `<button type="button" class="btn btn-warning btn_modificar" data-toggle="modal" data-target="#modalModificar" data-cedula_banco="${row.id_banco}"><i class="fa-solid fa-pen-to-square"></i></button>`;
                            }
                            if (permisosObtenidos.eliminar === 1) {
                                botonesHTML += btnEliminar;
                            }
                        }
                        return botonesHTML;
                    },
                },
            ],
            pageLength: 4,
            language: { url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" }
        });
    } else {
        $(".table-responsive").css("display", "none");
    }
});

$(document).ready(function () {
    $("#registrar").click(function () {
        mensaje("pregunta", "¿Estás seguro de los datos ingresados?", registrar);
    });

    function registrar() {
        var datos = "nombre=" + $("#nombre").val() + "&numero=" + $("#numero").val() +
                    "&cedula=" + $("#cedula").val() + "&telefono=" + $("#telefono").val() + "&accion=registrar";

        Swal.fire({
            title: "Procesando!",
            color: "white",
            background: "#000910",
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); },
        });

        $.ajax({
            type: "POST",
            url: "",
            data: datos,
            success: function (response) {
                Swal.close();
                var res = JSON.parse(response);

                if (res.success || res === true) {
                    $("#modalBancos").modal("hide");
                    $("#formRegistroBancos")[0].reset();
                    $(".modal-backdrop").remove();
                    mensaje("success");
                    limpiarEstilos("#formRegistroBancos");
                    $("#tablaBancos").DataTable().ajax.reload();
                } else if (res.error) {
                    mensaje("error");
                } else if (res.incompleto) {
                    mensaje("errorC");
                    marcarErrores("#formRegistroBancos", res.input);
                } else if (res.invalido) {
                    mensaje("invalido", res.invalido);
                    marcarErrores("#formRegistroBancos", res.input);
                }
            },
            error: function () {
                Swal.close();
                mensaje("error");
            }
        });
    }

    $(document).on("click", ".btn_modificar", function () {
        var id = $(this).data("cedula_banco");
        $.ajax({
            type: "POST",
            url: "",
            data: { id: id, accion: "consultar" },
            success: function (response) {
                var res = JSON.parse(response);
                $("#id_banco").val(res.id_banco);
                $("#nombre_modificar").val(res.nombre_banco);
                $("#numero_modificar").val(res.numero_cuenta);
                $("#cedula_modificar").val(res.cedula_banco);
                $("#telefono_modificar").val(res.telefono);
                $("#modalModificar").modal("show");
            }
        });
    });

    $("#modificar").click(function () {
        mensaje("pregunta", "¿Estás seguro de modificar los datos?", modificar);
    });

    function modificar() {
        var datos = "nombre=" + $("#nombre_modificar").val() + "&numero=" + $("#numero_modificar").val() +
                    "&cedula=" + $("#cedula_modificar").val() + "&telefono=" + $("#telefono_modificar").val() +
                    "&id=" + $("#id_banco").val() + "&accion=modificar";

        Swal.fire({
            title: "Procesando!",
            color: "white",
            background: "#000910",
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); },
        });

        $.ajax({
            type: "POST",
            url: "",
            data: datos,
            success: function (response) {
                Swal.close();
                var res = JSON.parse(response);
                if (res.success || res === true) {
                    $("#modalModificar").modal("hide");
                    mensaje("success");
                    limpiarEstilos("#formModificar");
                    $("#tablaBancos").DataTable().ajax.reload();
                } else {
                    mensaje("error");
                }
            }
        });
    }

    
    function limpiarEstilos(formId) {
        $(formId + " input, " + formId + " select").css({"border": "1px solid #ced4da", "box-shadow": "none"});
    }

    function marcarErrores(formId, campos) {
        limpiarEstilos(formId);
        var array = campos.replace(/-$/, "").split("-");
        $.each(array, function (i, val) {
            $("#" + val).css({"border": "1px solid rgb(158, 3, 3)", "box-shadow": "0 0 10px rgb(158, 3, 3)"});
        });
    }

    $(document).on("click", ".btn-eliminar", function () {
        mensaje("deshabilitar");
        $("#btn_delete").val($(this).data("id"));
    });

    $(document).on("click", ".btn-habilitar", function () {
        mensaje("habilitar");
        $("#btn_delete").val($(this).data("id"));
    });

    function eliminarAccion(tipo) {
        var datos = "id=" + $("#btn_delete").val() + "&accion=eliminar&tipo=" + tipo;
        $.ajax({
            type: "POST",
            url: "",
            data: datos,
            success: function (response) {
                var res = JSON.parse(response);
                if (res.success || res === true) {
                    mensaje("success");
                    $("#tablaBancos").DataTable().ajax.reload();
                } else {
                    mensaje("error");
                }
            }
        });
    }

    function mensaje(accion, texto, funcion) {
        const configBase = { color: "white", background: "#000910", confirmButtonColor: "rgb(238, 191, 0)" };

        if (accion == "success") {
            Swal.fire({ ...configBase, title: "¡Listo!", text: "Proceso Ejecutado con Éxito!", icon: "success", timer: 1500, showConfirmButton: false });
        } else if (accion == "errorC") {
            Swal.fire({ ...configBase, title: "¡Ups!", text: "Debes completar todos los campos!", icon: "error" });
        } else if (accion == "error") {
            Swal.fire({ ...configBase, title: "¡Ups!", text: "Error en el Servidor!", icon: "error" });
        } else if (accion == "pregunta" || accion == "deshabilitar" || accion == "habilitar") {
            let t = texto || (accion == "deshabilitar" ? "¿Seguro de eliminar?" : "¿Seguro de habilitar?");
            let f = funcion || (accion == "deshabilitar" ? () => eliminarAccion('deshabilitar') : () => eliminarAccion('habilitar'));
            
            Swal.fire({
                ...configBase,
                title: "¡Estas Seguro!",
                text: t,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar"
            }).then((result) => { if (result.isConfirmed) f(); });
        }
    }
});
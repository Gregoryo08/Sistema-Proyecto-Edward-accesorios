function capitalizarPalabras(cadena) {
    if (!cadena) return "";
    var resultado = cadena.charAt(0).toUpperCase() + cadena.slice(1).toLowerCase();
    resultado = resultado.replace(/(-\w)/g, function (match) {
        return match.toUpperCase();
    });
    return resultado;
}

$(document).ready(function () {
    function validarCampoBanco(id, mensaje) {
        const $campo = $(id);
        const valor = $campo.val().trim();
        const $error = $("#error_" + id.substring(1));
        const valido = valor !== "";

        $campo.removeClass("is-valid is-invalid");
        $error.hide().text("");

        if (!valido) {
            $campo.addClass("is-invalid");
            $error.text(mensaje).show();
        } else {
            $campo.addClass("is-valid");
        }
        return valido;
    }

    function validarFormularioBanco(modificar) {
        const sufijo = modificar ? "_modificar" : "";
        const resultados = [
              validarCampoBanco("#nombre" + sufijo, "El nombre del banco es obligatorio."),
              validarCampoBanco("#numero" + sufijo, "El número de la cuenta es obligatorio."),
              validarCampoBanco("#cedula" + sufijo, "La cédula o RIF es obligatoria."),
              validarCampoBanco("#telefono" + sufijo, "El número de teléfono es obligatorio.")
        ];

        if (resultados.every(Boolean)) return true;

        const titulos = ["Nombre requerido", "Número de cuenta requerido", "Cédula/RIF requerido", "Teléfono requerido"];
        const indicaciones = [
              "Indique el nombre del banco.",
              "Indique el número de la cuenta.",
              "Indique una cédula o RIF válido.",
              "Indique el número de teléfono."
        ];
        Swal.fire({
            title: titulos[resultados.indexOf(false)] || "Campos requeridos",
              text: indicaciones[resultados.indexOf(false)] || "Indique los campos requeridos.",
            icon: "warning",
            color: "white",
            background: "#000910"
        });
        return false;
    }

    let permisos = { registrar: false, modificar: false, eliminar: false, control_total: false };

    $.get("?pagina=bancos&permisos=true", function (data) {
        permisos = JSON.parse(data);
        if (permisos.control_total || permisos.registrar) {
            $(".text-right button").show();
        } else {
            $(".text-right button").hide();
        }
        cargarTablaBancos();
    });

    window.cargarTablaBancos = function() {
        if ($.fn.DataTable.isDataTable("#tablaBancos")) {
            $("#tablaBancos").DataTable().destroy();
        }

        $("#tablaBancos").DataTable({
            ajax: {
                url: "?pagina=bancos&ajax=true&x=bancos",
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

                        if (permisos.control_total || permisos.modificar) {
                            botonesHTML += `<button type="button" class="btn btn-warning btn_modificar" data-toggle="modal" data-target="#modalModificar" data-cedula_banco="${row.id_banco}"><i class="fa-solid fa-pen-to-square"></i></button>`;
                        }
                        if (permisos.control_total || permisos.eliminar) {
                            botonesHTML += btnEliminar;
                        }
                        return botonesHTML === "" ? '<span class="badge bg-secondary">Solo lectura</span>' : botonesHTML;
                    },
                },
            ],
            pageLength: 4,
            language: {
    processing: "Procesando...",
    search: "Buscar:",
    lengthMenu: "Mostrar _MENU_ registros por página",
    zeroRecords: "No se encontraron resultados",
    emptyTable: "Ningún dato disponible en esta tabla",
    info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
    infoFiltered: "(filtrado de un total de _MAX_ registros)",
    infoPostFix: "",
    thousands: ",",
    loadingRecords: "Cargando...",
    paginate: {
        first: "Primero",
        last: "Último",
        next: "Siguiente",
        previous: "Anterior"
    },
    aria: {
        sortAscending: ": Activar para ordenar la columna de manera ascendente",
        sortDescending: ": Activar para ordenar la columna de manera descendente"
    }
}
        });
    };

    $("#registrar").click(function () {
        if (validarFormularioBanco(false)) {
            mensaje("pregunta", "¿Estás seguro de los datos ingresados?", registrar);
        }
    });

    function registrar() {
        var datos = "nombre=" + $("#nombre").val() + "&numero=" + $("#numero").val() +
                    "&cedula=" + $("#cedula").val() + "&telefono=" + $("#telefono").val() + "&accion=registrar";

        Swal.fire({ title: "Procesando!", color: "white", background: "#000910", allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

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
                    cargarTablaBancos();
                } else if (res.error) { mensaje("error"); } else if (res.incompleto) {
                    mensaje("errorC");
                    marcarErrores("#formRegistroBancos", res.input);
                } else if (res.invalido) {
                    mensaje("invalido", res.invalido);
                    marcarErrores("#formRegistroBancos", res.input);
                }
            },
            error: function () { Swal.close(); mensaje("error"); }
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
        if (validarFormularioBanco(true)) {
            mensaje("pregunta", "¿Estás seguro de modificar los datos?", modificar);
        }
    });

    function modificar() {
        var datos = "nombre=" + $("#nombre_modificar").val() + "&numero=" + $("#numero_modificar").val() +
                    "&cedula=" + $("#cedula_modificar").val() + "&telefono=" + $("#telefono_modificar").val() +
                    "&id=" + $("#id_banco").val() + "&accion=modificar";

        Swal.fire({ title: "Procesando!", color: "white", background: "#000910", allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

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
                    cargarTablaBancos();
                } else { mensaje("error"); }
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
                    cargarTablaBancos();
                } else { mensaje("error"); }
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
            Swal.fire({ ...configBase, title: "¡Estas Seguro!", text: t, icon: "question", showCancelButton: true, confirmButtonText: "Confirmar", cancelButtonText: "Cancelar" }).then((result) => { if (result.isConfirmed) f(); });
        }
    }
});
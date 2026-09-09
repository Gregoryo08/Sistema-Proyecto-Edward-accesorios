$(document).ready(function () {
    let permisos = { registrar: false, modificar: false, eliminar: false, control_total: false };

    $.get("?pagina=metodo&permisos=true", function (data) {
        permisos = JSON.parse(data);
        if (permisos.control_total || permisos.registrar) {
            $("#btn_registro_metodo").show();
        } else {
            $("#btn_registro_metodo").hide();
        }
        cargarTablaMetodos();
    });

    window.cargarTablaMetodos = function() {
        if ($.fn.DataTable.isDataTable("#Metodopagotabla")) {
            $("#Metodopagotabla").DataTable().destroy();
        }

        window.metodoPagosTable = $("#Metodopagotabla").DataTable({
            ajax: {
                url: "?pagina=metodo&ajax=true&x=metodo",
                dataSrc: "",
            },
            columns: [
                { data: "id_metodopago", visible: false },
                { data: "nombre_metodopago" },
                { data: "moneda" }, 
                {
                    data: "estado",
                    render: function (data) {
                        return data == 1 
                            ? '<span class="badge bg-success">Activo</span>' 
                            : '<span class="badge bg-danger">Inactivo</span>';
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        let botones = '<div class="btn-group" role="group">';
                        if (permisos.control_total || permisos.modificar) {
                            botones += `
                                <button type="button" class="btn btn-warning btn-modificar-metodo" 
                                    data-bs-toggle="modal" data-bs-target="#modalModificar" 
                                    data-nombre="${row.nombre_metodopago}" 
                                    data-moneda="${row.moneda}"
                                    data-cuenta="${row.cuenta}"
                                    data-estado="${row.estado}"
                                    data-id="${row.id_metodopago}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button> `;
                        }
                        if (permisos.control_total || permisos.eliminar) {
                            botones += `
                                <button type="button" class="btn btn-danger btn-eliminar-metodo" 
                                    data-id="${row.id_metodopago}">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>`;
                        }
                        botones += '</div>';
                        return (botones === '<div class="btn-group" role="group"></div>') 
                            ? '<span class="badge bg-secondary">Solo lectura</span>' 
                            : botones;
                    },
                },
            ],
            pageLength: 4,
            lengthMenu: [[4, 8, 12, 16], ["4", "8", "12", "16"]],
            columnDefs: [{ className: "dt-head-center", targets: "_all" }],
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros por página",
                info: "Mostrando página _PAGE_ de _PAGES_",
                zeroRecords: "No se encontraron resultados",
                paginate: { 
                    first: "Primero", 
                    last: "Último", 
                    next: "Siguiente", 
                    previous: "Anterior" 
                },
            }
        });
    };

    const MIN_SIZE = 3;
    const MAX_SIZE = 20;

    function validateInput(inputElement, buttonElement, feedbackElement, initialLoad = false) {
        let value = inputElement.val();
        if (!initialLoad) {
            feedbackElement.text('');
            inputElement.removeClass("is-valid is-invalid");
        }
        const regex = /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g;
        const cleanValue = value.replace(regex, "");
        if (value !== cleanValue) inputElement.val(cleanValue);
        value = cleanValue.trim();
        if (value.length >= MIN_SIZE && value.length <= MAX_SIZE) {
            inputElement.addClass("is-valid");
            buttonElement.prop("disabled", false);
            return true;
        } else {
            inputElement.addClass("is-invalid");
            buttonElement.prop("disabled", false);
            if (value.length === 0) feedbackElement.text("Este campo es obligatorio.");
            else feedbackElement.text(`Debe tener entre ${MIN_SIZE} y ${MAX_SIZE} caracteres.`);
            return false;
        }
    }

    const $registroInput = $('#nombre_metodopago');
    const $modificarInput = $('#nombreModificar');

    $(document).on("click", ".btn-modificar-metodo", function() {
        const id = $(this).data("id");
        const nombre = $(this).data("nombre");
        const moneda = $(this).data("moneda");
        const cuenta = $(this).data("cuenta");
        const estado = $(this).data("estado");

        $("#idModificar").val(id);
        $("#nombreModificar").val(nombre);
        $("#monedaModificar").val(moneda); // Carga la moneda en el modal de modificar
        $("#estadoModificar").val(estado); // Carga el estado actual

        // Para seleccionar la cuenta si es radiobutton
        if ($(`input[name='tipoCuentaModificar']`).length > 0) {
            $(`input[name='tipoCuentaModificar'][value='${cuenta}']`).prop('checked', true);
        }

        $("#modalModificar").modal("show");
        validateInput($modificarInput, $("#modificarDatos"), $("#metodoModificarFeedback"), true);
    });

    $registroInput.on('input', () => validateInput($registroInput, $("#guardarMetodopago"), $("#metodoFeedback")));
    $modificarInput.on('input', () => validateInput($modificarInput, $("#modificarDatos"), $("#metodoModificarFeedback")));

    $("#guardarMetodopago").on("click", function() {
        setTimeout(() => {
            const checkedCuenta = $("input[name='tipoCuenta']:checked").length > 0;
            const selectMoneda = $("#moneda").val() !== "";

            if ($registroInput.hasClass('is-valid') && checkedCuenta && selectMoneda) {
                showSweetAlert("pregunta1");
            }
        }, 0);
    });

    window.registerData = function() {
        const data = {
            nombre: $registroInput.val().trim(),
            moneda: $("#moneda").val(), // Envía la moneda seleccionada
            cuenta: $("input[name='tipoCuenta']:checked").val(),
            estado: 1, // Registro inicial activo por defecto
            accion: "registrar"
        };
        ejecutarAjax(data, "#modalRegistroMetodopago");
    };

    $("#modificarDatos").on("click", function() {
        setTimeout(() => {
            const selectMonedaModificar = $("#monedaModificar").val() !== "";
            if ($modificarInput.hasClass('is-valid') && selectMonedaModificar) {
                showSweetAlert("pregunta2");
            }
        }, 0);
    });

    window.modifyData = function() {
        const data = {
            id: $("#idModificar").val(),
            nombre: $modificarInput.val().trim(),
            moneda: $("#monedaModificar").val(), // Envía la nueva moneda modificada
            cuenta: $("input[name='tipoCuentaModificar']:checked").val() || "0",
            estado: $("#estadoModificar").val() || "1",
            accion: "modificar"
        };
        ejecutarAjax(data, "#modalModificar");
    };

    $(document).on("click", ".btn-eliminar-metodo", function() {
        const id = $(this).data("id");
        $("#id_MetodoPago_delete").val(id);
        showSweetAlert("pregunta3");
    });

    window.deleteData = function() {
        const data = {
            id: $("#id_MetodoPago_delete").val(),
            accion: "eliminar"
        };
        ejecutarAjax(data, null);
    };

    function ejecutarAjax(datos, modalId) {
        showProcessingAlert();
        $.ajax({
            type: "POST",
            url: "?pagina=metodo",
            data: datos,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    if (modalId) $(modalId).modal("hide");
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    showSweetAlert("success").then(() => {
                        cargarTablaMetodos();
                    });
                } else {
                    showSweetAlert("invalido", res.invalido || res.error || "Error desconocido");
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                showSweetAlert("error");
            }
        });
    }

    const commonSwalMixin = Swal.mixin({
        color: "white",
        background: "#000910",
        confirmButtonColor: "rgb(238, 191, 0)",
    });

    function showProcessingAlert() {
        Swal.fire({
            title: "Procesando!",
            timerProgressBar: true,
            didOpen: () => { Swal.showLoading(); },
            background: "#000910",
            color: "white",
            allowOutsideClick: false
        });
    }

    function showSweetAlert(action, message = "") {
        const config = {
            pregunta1: { title: "¿Registrar?", text: "¿Desea guardar este método?", icon: "question", cb: registerData },
            pregunta2: { title: "¿Modificar?", text: "¿Desea guardar los cambios?", icon: "question", cb: modifyData },
            pregunta3: { title: "¿Eliminar?", text: "¿Desea eliminar este registro?", icon: "warning", cb: deleteData }
        };
        if (config[action]) {
            return commonSwalMixin.fire({
                title: config[action].title,
                text: config[action].text,
                icon: config[action].icon,
                showCancelButton: true,
                confirmButtonText: "Sí, confirmar"
            }).then((result) => { if (result.isConfirmed) config[action].cb(); });
        }
        const simpleAlerts = {
            success: { title: "¡Listo!", icon: "success", timer: 1500, showConfirmButton: false },
            errorC: { title: "Campos incompletos", icon: "error" },
            error: { title: "Error de servidor", icon: "error" },
            invalido: { title: "Atención", text: message, icon: "warning" }
        };
        return commonSwalMixin.fire(simpleAlerts[action]);
    }
});
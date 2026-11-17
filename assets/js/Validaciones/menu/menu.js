let tablaMenu;

window.obtenerPermisos = function() {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", window.location.href, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const respuesta = JSON.parse(xhr.responseText);
                    const permisos = respuesta.array.find(
                        (registro) => registro.nombre_modulo === respuesta.modulo
                    );

                    if (permisos) {
                        $.ajax({
                            type: "POST",
                            url: "models/registroBitacora.php",
                            data: {
                                accion: "bitacora",
                                modulo: permisos.nombre_modulo,
                                usuario: respuesta.usuario
                            },
                            success: function(response) {
                                if (response.error) {} else {}
                            },
                            error: function() {}
                        });
                        resolve(permisos);
                    } else {
                        reject(new Error("Permisos no encontrados para el módulo."));
                    }
                } catch (e) {
                    reject(new Error("Respuesta inválida del servidor al obtener permisos."));
                }
            } else {
                reject(new Error("La solicitud de permisos falló con el estado: " + xhr.status));
            }
        };
        xhr.onerror = function() {
            reject(new Error("Error de red al intentar la solicitud."));
        };
        xhr.send("accion=" + encodeURIComponent("permisos"));
    });
};

$(document).ready(function() {
    function mensaje(tipo, texto) {
        console.log(`Mensaje ${tipo}: ${texto}`);
    }

    const MAX_TEXT_LENGTH = 20;
    const MIN_TEXT_LENGTH = 3;
    const MAX_PRICE_LENGTH = 8;

    function validateTextInput(inputElement, feedbackElement, isInitialLoad = false) {
        let dato = inputElement.val();
        let errorMessage = '';

        feedbackElement.text('');

        if (dato.length > MAX_TEXT_LENGTH) {
            dato = dato.substring(0, MAX_TEXT_LENGTH);
            inputElement.val(dato);
        }

        const expresion = /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g;
        const datoSinInvalidos = dato.replace(expresion, "");

        if (dato !== datoSinInvalidos) {
            errorMessage = "Solo se permiten letras, espacios y tildes.";
            inputElement.val(datoSinInvalidos);
            inputElement.removeClass("is-valid").addClass("is-invalid");
            feedbackElement.text(errorMessage).show();
            return false;
        }

        if (isInitialLoad) {
            inputElement.removeClass("is-valid is-invalid");
            feedbackElement.text('');
            if (datoSinInvalidos.trim().length >= MIN_TEXT_LENGTH && datoSinInvalidos.trim().length <= MAX_TEXT_LENGTH) {
                inputElement.addClass("is-valid");
                return true;
            }
            return false;
        }

        if (dato.trim().length === 0) {
            errorMessage = "Este campo no puede estar vacío.";
            inputElement.removeClass("is-valid").addClass("is-invalid");
            feedbackElement.text(errorMessage).show();
            return false;
        } else if (dato.trim().length < MIN_TEXT_LENGTH) {
            errorMessage = `Mínimo ${MIN_TEXT_LENGTH} caracteres requeridos. Sólo Letras`;
            inputElement.removeClass("is-valid").addClass("is-invalid");
            feedbackElement.text(errorMessage).show();
            return false;
        } else if (dato.trim().length > MAX_TEXT_LENGTH) {
            errorMessage = `Máximo ${MAX_TEXT_LENGTH} caracteres permitidos.`;
            inputElement.removeClass("is-valid").addClass("is-invalid");
            feedbackElement.text(errorMessage).show();
            return false;
        } else {
            inputElement.removeClass("is-invalid").addClass("is-valid");
            feedbackElement.hide();
            return true;
        }
    }

    function validatePriceInput(inputElement, feedbackElement, isInitialLoad = false) {
        let value = inputElement.val().trim();
        let errorMessage = '';

        feedbackElement.text('');

        if (value.length > MAX_PRICE_LENGTH) {
            value = value.substring(0, MAX_PRICE_LENGTH);
            inputElement.val(value);
        }

        let cleanedValue = value.replace(/[^0-9.]/g, "");
        let parts = cleanedValue.split('.');
        if (parts.length > 2) {
            cleanedValue = parts[0] + '.' + parts.slice(1).join('');
        }
        if (parts.length === 2 && parts[1].length > 2) {
            cleanedValue = parts[0] + '.' + parts[1].substring(0, 2);
        }
        inputElement.val(cleanedValue);

        const price = parseFloat(cleanedValue);

        if (isInitialLoad) {
            inputElement.removeClass("is-valid is-invalid");
            feedbackElement.text('');
            if (cleanedValue !== "" && !isNaN(price) && price > 0) {
                inputElement.addClass("is-valid");
                return true;
            }
            return false;
        }

        if (cleanedValue === "") {
            inputElement.removeClass("is-valid").addClass("is-invalid");
            errorMessage = "Este campo es requerido.";
            feedbackElement.text(errorMessage).show();
            return false;
        } else if (isNaN(price) || price <= 0) {
            inputElement.removeClass("is-valid").addClass("is-invalid");
            errorMessage = "El precio debe ser un número válido y positivo.";
            feedbackElement.text(errorMessage).show();
            return false;
        } else {
            inputElement.removeClass("is-invalid").addClass("is-valid");
            feedbackElement.hide();
            return true;
        }
    }

    function validateSelect(selectElement, feedbackElement, isInitialLoad = false) {
        feedbackElement.text('');

        if (isInitialLoad) {
            selectElement.removeClass("is-valid is-invalid");
            feedbackElement.text('');
            if (selectElement.val()) {
                selectElement.addClass("is-valid");
                return true;
            }
            return false;
        }

        if (!selectElement.val()) {
            selectElement.removeClass("is-valid").addClass("is-invalid");
            feedbackElement.text("Debes seleccionar una opción.").show();
            return false;
        } else {
            selectElement.removeClass("is-invalid").addClass("is-valid");
            feedbackElement.hide();
            return true;
        }
    }

    function updateRegisterButtonState() {
        const isNameValid = validateTextInput($("#nombre_menu"), $("#mensajeMenuRegistro"));
        const isPriceValid = validatePriceInput($("#precioMenu"), $("#mensajePreciosRegistro"));
        const isClassificationValid = true;

        $("#registroMenu").prop("disabled", !(isNameValid && isPriceValid && isClassificationValid));
    }

    function updateModifyButtonState() {
        const isNameValid = validateTextInput($("#ModificarNombreMenu"), $("#mensajeMenuModificar"));
        const isPriceValid = validatePriceInput($("#ModificarPrecioMenu"), $("#mensajePreciosModificar"));
        const isClassificationValid = true;

        $("#modificarMenu").prop("disabled", !(isNameValid && isPriceValid && isClassificationValid));
    }

    $("#nombre_menu").on("input paste blur", function() {
        validateTextInput($(this), $("#mensajeMenuRegistro"));
        updateRegisterButtonState();
    });
    $("#precioMenu").on("input paste blur", function() {
        validatePriceInput($(this), $("#mensajePreciosRegistro"));
        updateRegisterButtonState();
    });

    $("#ModificarNombreMenu").on("input paste blur", function() {
        validateTextInput($(this), $("#mensajeMenuModificar"));
        updateModifyButtonState();
    });
    $("#ModificarPrecioMenu").on("input paste blur", function() {
        validatePriceInput($(this), $("#mensajePreciosModificar"));
        updateModifyButtonState();
    });


    $("#registroMenu").on("click", function(e) {
        e.preventDefault();

        let formIsValid = true;
        if (!validateTextInput($("#nombre_menu"), $("#mensajeMenuRegistro"))) {
            formIsValid = false;
        }
        if (!validatePriceInput($("#precioMenu"), $("#mensajePreciosRegistro"))) {
            formIsValid = false;
        }

        if (!formIsValid) {
            mensaje("error", "Por favor, completa y valida todos los campos correctamente.");
            return;
        }

        const nombre_menu = $("#nombre_menu").val().trim();
        const precios = $("#precioMenu").val().trim();

        Swal.fire({
            title: "¿Estás seguro de registrar este menú?",
            text: "Confirma para agregar este nuevo menú.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "rgb(238, 191, 0)",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, registrar",
            cancelButtonText: "Cancelar",
            background: "#000910",
            color: "white",
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Procesando...",
                    html: "Por favor espera mientras registramos el menú.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    background: "#000910",
                    color: "white",
                });
                $.ajax({
                    url: "?pagina=menu",
                    type: "POST",
                    data: {
                        accion: "registro_menu",
                        nombre_menu: nombre_menu,
                        precios: precios,
                    },
                    dataType: "json",
                    success: function(response) {
                        Swal.close();
                        if (response.success && response.success.includes("Ya existe un menú con este nombre.")) {
                            mensaje("duplicate", response.success);
                        } else if (response.success) {
                            $("#modalRegistroMenu").modal("hide");
                            $("#formRegistroMenu")[0].reset();
                            $("#nombre_menu").removeClass("is-valid is-invalid");
                            $("#precioMenu").removeClass("is-valid is-invalid");

                            $("#mensajeMenuRegistro").text("").hide();
                            $("#mensajePreciosRegistro").text("").hide();

                            mensaje("success", response.success);
                            if (tablaMenu) tablaMenu.ajax.reload(null, false);
                        } else if (response.error) {
                            mensaje("error", response.error || "Error al registrar menú.");
                        }
                    },
                    error: function() {
                        Swal.close();
                        mensaje("error", "Error de comunicación con el servidor al registrar menú.");
                    },
                    complete: function() {
                        removeBackdrop();
                    },
                });
            }
        });
    });

    $(document).on("click", ".btn-modificar-menu", function() {
        const id_menu = $(this).data("id");

        Swal.fire({
            title: "Cargando menú...",
            html: "Obteniendo datos del menú, por favor espera.",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
            background: "#000910",
            color: "white",
        });

        $.ajax({
            url: `?pagina=menu`,
            type: "GET",
            data: {
                accion: "get_menu",
                id_menu: id_menu
            },
            dataType: "json",
            success: function(data) {
                Swal.close();
                if (data && !data.error) {
                    $("#id_menu").val(data.id_menu);
                    $("#ModificarNombreMenu").val(data.nombre_menu);
                    $("#ModificarPrecioMenu").val(parseFloat(data.precios).toFixed(2));

                    $("#ModificarNombreMenu").removeClass("is-valid is-invalid");
                    $("#ModificarPrecioMenu").removeClass("is-valid is-invalid");

                    $("#mensajeMenuModificar").text("").hide();
                    $("#mensajePreciosModificar").text("").hide();

                    setTimeout(() => {
                        validateTextInput($("#ModificarNombreMenu"), $("#mensajeMenuModificar"), true);
                        validatePriceInput($("#ModificarPrecioMenu"), $("#mensajePreciosModificar"), true);
                        updateModifyButtonState();
                    }, 150);

                    $("#modalModificarMenu").modal("show");

                } else {
                    mensaje("error", data.error || "Menú no encontrado o error al cargar.");
                }
            },
            error: function() {
                Swal.close();
                mensaje("error", "Error de comunicación con el servidor al cargar datos del menú.");
            },
        });
    });

    $("#modificarMenu").on("click", function(e) {
        e.preventDefault();

        let formIsValid = true;

        if (!validateTextInput($("#ModificarNombreMenu"), $("#mensajeMenuModificar"))) {
            formIsValid = false;
        }
        if (!validatePriceInput($("#ModificarPrecioMenu"), $("#mensajePreciosModificar"))) {
            formIsValid = false;
        }

        if (!formIsValid) {
            mensaje("error", "Por favor, completa y valida todos los campos correctamente.");
            return;
        }

        const id_menu = $("#id_menu").val();
        const nombre_menu = $("#ModificarNombreMenu").val().trim();
        const precios = $("#ModificarPrecioMenu").val().trim();

        Swal.fire({
            title: "¿Estás seguro de los cambios?",
            text: "Confirma para actualizar este menú.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "rgb(238, 191, 0)",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, guardar cambios",
            cancelButtonText: "Cancelar",
            background: "#000910",
            color: "white",
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Procesando...",
                    html: "Por favor espera mientras actualizamos el menú.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    background: "#000910",
                    color: "white",
                });
                $.ajax({
                    url: "?pagina=menu",
                    type: "POST",
                    data: {
                        accion: "modificar_menu",
                        id_menu: id_menu,
                        nombre_menu: nombre_menu,
                        precios: precios,
                    },
                    dataType: "json",
                    success: function(response) {
                        Swal.close();
                        if (response.success && response.success.includes("Ya existe otro elemento del menú con el mismo nombre.")) {
                            mensaje("duplicate", response.success);
                        } else if (response.success) {
                            $("#modalModificarMenu").modal("hide");
                            $("#formModificarMenu")[0].reset();
                            $("#ModificarNombreMenu").removeClass("is-valid is-invalid");
                            $("#ModificarPrecioMenu").removeClass("is-valid is-invalid");

                            $("#mensajeMenuModificar").text("").hide();
                            $("#mensajePreciosModificar").text("").hide();

                            mensaje("success", response.success);
                            if (tablaMenu) tablaMenu.ajax.reload(null, false);
                        } else if (response.error) {
                            mensaje("error", response.error || "Error al modificar menú.");
                        }
                    },
                    error: function() {
                        Swal.close();
                        mensaje("error", "Error de comunicación con el servidor al modificar menú.");
                    },
                    complete: function() {
                        removeBackdrop();
                    },
                });
            }
        });
    });

    $(document).on("click", ".btn-eliminar-menu", function() {
        const id_menu = $(this).data("id");
        const nombre_menu = $(this).data("nombre");

        Swal.fire({
            title: `¿Eliminar "${nombre_menu}"?`,
            text: "Confirma para eliminar este menú permanentemente. Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "rgb(238, 191, 0)",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
            background: "#000910",
            color: "white",
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Procesando...",
                    html: "Eliminando menú, por favor espera.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    background: "#000910",
                    color: "white",
                });
                $.ajax({
                    url: "?pagina=menu",
                    type: "POST",
                    data: {
                        accion: "eliminar_menu",
                        id_menu: id_menu,
                    },
                    dataType: "json",
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            mensaje("success", response.success);
                            if (tablaMenu) tablaMenu.ajax.reload(null, false);
                        } else {
                            mensaje("error", response.error || "Error al eliminar el elemento del menú.");
                        }
                    },
                    error: function() {
                        Swal.close();
                        mensaje("error", "Error de comunicación con el servidor al eliminar menú.");
                    },
                    complete: function() {
                        removeBackdrop();
                    },
                });
            }
        });
    });

    function mensaje(accion, message = "") {
        let config = {
            color: "white",
            confirmButtonColor: "rgb(238, 191, 0)",
            background: "#000910",
            customClass: {
                popup: 'my-swal-popup',
                title: 'my-swal-title',
                content: 'my-swal-content',
                confirmButton: 'my-swal-confirm-button'
            }
        };

        if (accion === "error") {
            Swal.fire({
                ...config,
                title: "¡Error!",
                text: message || "Ha ocurrido un error inesperado. Por favor, inténtalo de nuevo.",
                icon: "error",
            });
        } else if (accion === "duplicate") {
            Swal.fire({
                ...config,
                title: "¡Atención!",
                text: message || "El registro que intentas crear/modificar ya existe.",
                icon: "warning",
                confirmButtonColor: "#3498db"
            });
        } else if (accion === "success") {
            Swal.fire({
                ...config,
                title: "¡Éxito!",
                text: message || "La operación se ha completado correctamente.",
                icon: "success",
            });
        } else {
            Swal.fire({
                ...config,
                title: "Información",
                text: message || "Mensaje informativo.",
                icon: "info",
            });
        }
    }

    function removeBackdrop() {
        setTimeout(function() {
            const backdrops = $(".modal-backdrop");
            if (backdrops.length) {
                backdrops.remove();
            }
            if ($("body").hasClass("modal-open")) {
                $("body").removeClass("modal-open").css("padding-right", "");
            }
        }, 100);
    }

    $("#modalRegistroMenu").on("hidden.bs.modal", function() {
        $("#formRegistroMenu")[0].reset();
        $("#nombre_menu").removeClass("is-invalid is-valid");
        $("#precioMenu").removeClass("is-invalid is-valid");

        $("#mensajeMenuRegistro").text("").hide();
        $("#mensajePreciosRegistro").text("").hide();

        $("#registroMenu").prop("disabled", true);
    });

    $("#modalModificarMenu").on("hidden.bs.modal", function() {
        $("#formModificarMenu")[0].reset();
        $("#ModificarNombreMenu").removeClass("is-invalid is-valid");
        $("#ModificarPrecioMenu").removeClass("is-invalid is-valid");

        $("#mensajeMenuModificar").text("").hide();
        $("#mensajePreciosModificar").text("").hide();

        $("#modificarMenu").prop("disabled", true);
    });

    $("#modalRegistroMenu").on("show.bs.modal", function() {
        $("#nombre_menu").val('');
        $("#precioMenu").val('');

        $("#nombre_menu").removeClass("is-valid is-invalid");
        $("#precioMenu").removeClass("is-valid is-invalid");

        $("#mensajeMenuRegistro").text("").hide();
        $("#mensajePreciosRegistro").text("").hide();

        $("#registroMenu").prop("disabled", true);
    });

    $("#modalModificarMenu").on("show.bs.modal", function() {
        $("#ModificarNombreMenu").removeClass("is-valid is-invalid");
        $("#ModificarPrecioMenu").removeClass("is-invalid is-valid");

        $("#mensajeMenuModificar").text("").hide();
        $("#mensajePreciosModificar").text("").hide();

        $("#modificarMenu").prop("disabled", true);
    });

    let tablaMenu;

    obtenerPermisos()
        .then((permisosObtenidos) => {
            if (permisosObtenidos && (permisosObtenidos.control_total === 1 || permisosObtenidos.registrar === 1)) {
                $("#btn_registro_menu").css("display", "block");
            } else {
                $("#btn_registro_menu").css("display", "none");
            }

            if (permisosObtenidos && (permisosObtenidos.control_total === 1 || permisosObtenidos.listar === 1)) {
                tablaMenu = $("#tablaMenu").DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    responsive: true,
                    ajax: {
                        url: "?pagina=menu",
                        type: "POST",
                        data: {
                            accion: "consulta_menu"
                        },
                        dataSrc: "",
                        error: function(xhr, error, thrown) {
                            mensaje(
                                "error",
                                "Error al cargar los elementos del menú. Intenta recargar la página."
                            );
                        },
                    },
                    columns: [{
                        data: "id_menu"
                    }, {
                        data: "nombre_menu"
                    }, {
                        data: "precios",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        },
                    }, {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            let botonesHTML = "";
                            if (permisosObtenidos && permisosObtenidos.control_total === 1) {
                                botonesHTML += `
                                <button type="button" class="btn btn-warning btn-sm btn-modificar-menu mr-2" data-id="${row.id_menu}" title="Modificar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm btn-eliminar-menu" data-id="${row.id_menu}" data-nombre="${row.nombre_menu}" title="Eliminar">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                                `;
                            } else {
                                if (permisosObtenidos && permisosObtenidos.modificar === 1) {
                                    botonesHTML += `
                                    <button type="button" class="btn btn-warning btn-sm btn-modificar-menu mr-2" data-id="${row.id_menu}" title="Modificar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    `;
                                }
                                if (permisosObtenidos && permisosObtenidos.eliminar === 1) {
                                    botonesHTML += `
                                    <button type="button" class="btn btn-danger btn-sm btn-eliminar-menu" data-id="${row.id_menu}" data-nombre="${row.nombre_menu}" title="Eliminar">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                    `;
                                }
                            }
                            return botonesHTML;
                        },
                    }, ],
                    pageLength: 5,
                    lengthMenu: [
                        [5, 10, 25, 50, -1],
                        ["5", "10", "25", "50", "Todos"],
                    ],
                    columnDefs: [{
                        className: "dt-head-center",
                        targets: "_all",
                    }, {
                        targets: [0],
                        width: "5%"
                    }, {
                        targets: [-1],
                        width: "15%",
                        className: "dt-body-center"
                    }],
                    language: {
                        lengthMenu: "Mostrar _MENU_ registros por página",
                        zeroRecords: "No se encontraron resultados",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        infoEmpty: "No hay registros disponibles",
                        infoFiltered: "(filtrado de _MAX_ registros totales)",
                        search: "Buscar:",
                        paginate: {
                            first: "Primero",
                            last: "Último",
                            next: "Siguiente",
                            previous: "Anterior",
                        },
                        processing: "Procesando...",
                        aria: {
                            sortAscending: ": activar para ordenar la columna de manera ascendente",
                            sortDescending: ": activar para ordenar la columna de manera descendente",
                        },
                    },
                });
            } else {
                $(".table-responsive").css("display", "none");
            }
        })
        .catch(() => {
            $(".table-responsive").css("display", "none");
            $(".text-right button").css("display", "none");
        });
});
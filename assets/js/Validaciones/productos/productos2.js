$(document).ready(function () {

    function gestionarEstado(id, esValido, mensaje = "") {
        const $el = $("#" + id);
        if (!$el.length) return;

        let $errorDiv = $el.siblings('.msg-error, .text-danger');
        if (!$errorDiv.length) {
            $el.after(`<small class="msg-error text-danger" style="display:none;"></small>`);
            $errorDiv = $el.siblings('.msg-error');
        }

        if (esValido) {
            $el.removeClass('is-invalid').addClass('is-valid');
            $errorDiv.hide().text("");
        } else {
            $el.removeClass('is-valid').addClass('is-invalid');
            $errorDiv.text(mensaje).show();
        }
        
        verificarFormularios();
    }

    function verificarFormularios() {
        let registroValido = true;
        const camposRegistro = [
            '#nombre', '#id_marca', '#id_categoria', 
            '#stock_actual', '#precio'
        ];

        if ($('#stock_minimo').is(':visible')) {
            camposRegistro.push('#stock_minimo');
        }
        if ($('#stock_maximo').is(':visible')) {
            camposRegistro.push('#stock_maximo');
        }

        camposRegistro.forEach(selector => {
            if ($(selector).length && !$(selector).hasClass('is-valid')) {
                registroValido = false;
            }
        });
        $("#btnRegistrarProducto").prop('disabled', !registroValido);

        let modificarValido = true;
        const camposModificar = [
            '#nombreModificar', '#marcaModificar', '#categoriaModificar', 
            '#stock_actualModificar', '#precioModificar'
        ];

        if ($('#stock_minimoModificar').is(':visible')) {
            camposModificar.push('#stock_minimoModificar');
        }
        if ($('#stock_maximoModificar').is(':visible')) {
            camposModificar.push('#stock_maximoModificar');
        }

        camposModificar.forEach(selector => {
            const $campo = $(selector);
            if (
                $campo.length &&
                (!$campo.val() || $campo.hasClass('is-invalid'))
            ) {
                modificarValido = false;
            }
        });
        $("#btnModificarProducto").prop('disabled', !modificarValido);
    }

    function capitalizarPalabras(cadena) {
        if (!cadena) return "";
        return cadena.replace(/\b\w+/g, w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase());
    }

    function limitarDescripcion(selector) {
        $(selector).on('input', function () {
            $(this).val($(this).val().slice(0, 40));
        });
    }

    function limitarNumeros(selector, permiteDecimal = false) {
        $(selector).on('input', function () {
            let valor = $(this).val().replace(/[^0-9.]/g, '');

            if (permiteDecimal) {
                const partes = valor.split('.');
                const entero = partes[0].slice(0, 10);
                const decimalesDisponibles = 10 - entero.length;
                const decimales = partes.length > 1
                    ? partes[1].slice(0, decimalesDisponibles)
                    : '';

                valor = entero;
                if (partes.length > 1 && decimalesDisponibles > 0) {
                    valor += '.' + decimales;
                }
            } else {
                valor = valor.replace(/\./g, '').slice(0, 10);
            }

            $(this).val(valor);
        });
    }

    limitarDescripcion('#descripcion, #descripcionModificar');
    limitarNumeros('#stock_minimo, #stock_maximo, #stock_actual');
    limitarNumeros('#stock_minimoModificar, #stock_maximoModificar, #stock_actualModificar');
    limitarNumeros('#precio, #precioModificar', true);

    function validarCampoProducto(id) {
    let $input = $("#" + id);
    if (!$input.length) return;
    
    let entrada = $input.val() || "";

    if (id === "nombre" || id === "nombreModificar") {
        let soloPermitidos = entrada.replace(/[^A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\s-]/g, "");
        let capitalizado = capitalizarPalabras(soloPermitidos);
        
        if (capitalizado.length > 30) {
            capitalizado = capitalizado.substring(0, 30);
        }

        if (entrada !== capitalizado) {
            let pos = $input.prop("selectionStart");
            $input.val(capitalizado);
            $input.prop("selectionStart", pos).prop("selectionEnd", pos);
        }

        let valorTrim = capitalizado.trim();
        if (valorTrim.length === 0) {
            gestionarEstado(id, false, "El nombre del producto es obligatorio");
        } else if (valorTrim.length < 3 || valorTrim.length > 30) {
            gestionarEstado(id, false, "Debe tener entre 3 y 30 caracteres");
        } else {
            gestionarEstado(id, true);
        }
    }

    if (id === "descripcion" || id === "descripcionModificar") {
        let valorTrim = entrada.trim();
        if (entrada.length > 40) {
            entrada = entrada.slice(0, 40);
            $input.val(entrada);
            valorTrim = entrada.trim();
        }

        if (valorTrim.length > 0) {
            if (valorTrim.length < 4 || valorTrim.length > 40) {
                gestionarEstado(id, false, "La descripción debe tener entre 4 y 40 caracteres");
            } else {
                gestionarEstado(id, true);
            }
        } else {
            $input.removeClass('is-invalid is-valid');
            $input.siblings('.msg-error, .text-danger').hide().text("");
        }
    }

    if (id === "id_marca" || id === "marcaModificar" || id === "id_categoria" || id === "categoriaModificar") {
        if (entrada === "" || entrada === null || entrada === "0" || entrada === 0) {
            gestionarEstado(id, false, "Debe seleccionar una opción obligatoriamente");
        } else {
            gestionarEstado(id, true);
        }
    }

    if (id === "imei" || id === "imeiModificar") {
        let limpio = entrada.replace(/[^0-9]/g, "");
        if (limpio.length > 17) {
            limpio = limpio.substring(0, 17);
        }
        if (entrada !== limpio) {
            let pos = $input.prop("selectionStart");
            $input.val(limpio);
            $input.prop("selectionStart", pos).prop("selectionEnd", pos);
        }

        if (limpio.length === 0) {
            $input.removeClass('is-invalid is-valid');
            $input.siblings('.msg-error, .text-danger').hide().text("");
        } else if (limpio.length < 16 || limpio.length > 17) {
            gestionarEstado(id, false, "El IMEI debe tener entre 16 y 17 dígitos");
        } else {
            gestionarEstado(id, true);
        }
    }

    if (id === "ram" || id === "ramModificar" || id === "almacenamiento" || id === "almacenamientoModificar") {
        if (entrada === "" || entrada === null) {
            $input.removeClass('is-invalid is-valid');
            $input.siblings('.msg-error, .text-danger').hide().text("");
        } else {
            gestionarEstado(id, true);
        }
    }

    if (id.includes('stock')) {
        let limpio = entrada.replace(/[^0-9]/g, "");
        limpio = limpio.slice(0, 10);
        if (entrada !== limpio) $input.val(limpio);

        const esModificar = id.includes('Modificar');
        const sufijo = esModificar ? 'Modificar' : '';
        
        const $min = $('#stock_minimo' + sufijo);
        const $max = $('#stock_maximo' + sufijo);
        const $act = $('#stock_actual' + sufijo);

        const tieneMin = $min.length && $min.is(':visible') && $min.val() !== "";
        const tieneMax = $max.length && $max.is(':visible') && $max.val() !== "";
        const tieneAct = $act.length && $act.is(':visible') && $act.val() !== "";

        const valMin = tieneMin ? parseInt($min.val()) : null;
        const valMax = tieneMax ? parseInt($max.val()) : null;
        const valAct = tieneAct ? parseInt($act.val()) : null;

        if (limpio === "") {
            gestionarEstado(id, false, "Este campo es requerido");
        } else if (parseInt(limpio) < 0) {
            gestionarEstado(id, false, "No se permiten valores negativos");
        } else {
            gestionarEstado(id, true);
        }

        if (tieneMin && tieneMax && valMin > valMax) {
            gestionarEstado('stock_minimo' + sufijo, false, "El mínimo no puede ser mayor al máximo");
            gestionarEstado('stock_maximo' + sufijo, false, "El máximo debe ser mayor al mínimo");
        }

        if (tieneAct) {
            if (tieneMin && valAct < valMin) {
                gestionarEstado('stock_actual' + sufijo, false, "El actual no puede ser menor al mínimo");
            } else if (tieneMax && valAct > valMax) {
                gestionarEstado('stock_actual' + sufijo, false, "El actual no puede superar el máximo");
            }
        }
    }

    if (id === "precio" || id === "precioModificar") {
        let limpio = entrada.replace(/[^0-9.]/g, "");
        const partes = limpio.split(".");
        if (partes.length > 2) {
            limpio = partes[0] + "." + partes.slice(1).join("").slice(0, 2);
        }
        const partesPrecio = limpio.split('.');
        const enteroPrecio = partesPrecio[0].slice(0, 10);
        const disponiblesPrecio = 10 - enteroPrecio.length;
        limpio = enteroPrecio;
        if (partesPrecio.length > 1 && disponiblesPrecio > 0) {
            limpio += '.' + partesPrecio[1].slice(0, disponiblesPrecio);
        }
        if (entrada !== limpio) $input.val(limpio);

        if (limpio === "" || isNaN(limpio) || parseFloat(limpio) <= 0) {
            gestionarEstado(id, false, "Ingrese un precio válido mayor a 0");
        } else {
            gestionarEstado(id, true);
        }
    }
}

    const camposMonitoreados = [
        "nombre", "nombreModificar", 
        "descripcion", "descripcionModificar",
        "id_marca", "marcaModificar", 
        "id_categoria", "categoriaModificar",
        "imei", "imeiModificar",
        "ram", "ramModificar",
        "almacenamiento", "almacenamientoModificar",
        "stock_minimo", "stock_minimoModificar", 
        "stock_maximo", "stock_maximoModificar", 
        "stock_actual", "stock_actualModificar",
        "precio", "precioModificar"
    ];

    camposMonitoreados.forEach(id => {
        $("#" + id).on("input change blur", () => validarCampoProducto(id));
    });

    $('#modalModificarProducto').on('shown.bs.modal', function () {
        verificarFormularios();
    });

    $('.modal').on('hidden.bs.modal', function () {
        if ($(this).find('form').length && $(this).find('form')[0]) {
            $(this).find('form')[0].reset();
        }
        $(this).find('.form-control, select, .form-select').removeClass('is-valid is-invalid');
        $(this).find('.msg-error, .text-danger').hide().text("");
        
        $("#btnRegistrarProducto, #btnModificarProducto").prop('disabled', true);
        
        if (!$('.modal.show').length) {
            $('body').removeClass('modal-open').css('overflow', '');
            $('.modal-backdrop').remove();
        }
    });

    $("#btnRegistrarProducto, #btnModificarProducto").prop('disabled', true);

    window.gestionarEstado = gestionarEstado;
    window.verificarFormularios = verificarFormularios;
    window.validarCampoProducto = validarCampoProducto;
});
$(document).ready(function () {

    function gestionarEstado(id, esValido, mensaje = "") {
        const $el = $("#" + id);
        let idError = (id === "nombre_metodopago") ? "#metodoFeedback" : "#metodoModificarFeedback";
        const $errorDiv = $(idError);
        
        if (esValido) {
            $el.removeClass('is-invalid').addClass('is-valid');
            $errorDiv.hide().text("");
        } else {
            $el.removeClass('is-valid').addClass('is-invalid');
            $errorDiv.removeClass('text-success').addClass('text-danger').text(mensaje).show();
        }
    }

    function gestionarMoneda(id, esValida) {
        const $select = $("#" + id);
        const $error = $select.siblings('.invalid-feedback');

        if (esValida) {
            $select.removeClass('is-invalid').addClass('is-valid');
            $error.hide().text('');
        } else {
            $select.removeClass('is-valid').addClass('is-invalid');
            $error.removeClass('text-success').addClass('text-danger')
                .text('Por favor, seleccione una moneda.').show();
        }
    }

    function verificarRadios() {
        const check = document.getElementsByName("tipoCuenta");
        for (var i = 0; i < check.length; i++) {
            if (check[i].checked) {
                return true;
            }
        }
        return false;
    }

    function validarMetodoPago(id) {
        let $input = $("#" + id);
        let entrada = $input.val();
        let idBoton = (id === "nombre_metodopago") ? "#guardarMetodopago" : "#modificarDatos";
        
        if (/[0-9]/.test(entrada)) {
            let sinNumeros = entrada.replace(/[0-9]/g, "");
            $input.val(sinNumeros);
            gestionarEstado(id, false, "Este campo no acepta números");
            return; 
        }
        
        let soloLetras = entrada.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, "");
        if (entrada !== soloLetras) {
            $input.val(soloLetras);
            gestionarEstado(id, false, "Este campo permite solo letras");
            return;
        }

        let capitalizado = soloLetras.replace(/\b\w+/g, w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase());
        
        var cursorPosicion = $input.prop("selectionStart");
        $input.val(capitalizado);
        $input.prop("selectionStart", cursorPosicion);
        $input.prop("selectionEnd", cursorPosicion);

        let valorTrim = capitalizado.trim();

        if (valorTrim.length === 0) {
            gestionarEstado(id, false, "El nombre del método de pago es obligatorio");
            return;
        } 
        
        if (valorTrim.length < 3 || valorTrim.length > 20) {
            gestionarEstado(id, false, "Debe tener entre 3 y 20 letras");
            $(idBoton).prop('disabled', true);
            return;
        }

        if (id === "nombre_metodopago") {
            if (!verificarRadios()) {
                gestionarEstado(id, true); 
                return;
            }
        }

        gestionarEstado(id, true);
        $(idBoton).prop('disabled', false);
    }

    $("#nombre_metodopago, #nombreModificar").on("keypress", function (e) {
        let chr = String.fromCharCode(e.which);
        let id = $(this).attr("id");
        let idBoton = (id === "nombre_metodopago") ? "#guardarMetodopago" : "#modificarDatos";

        if (/[0-9]/.test(chr)) {
            e.preventDefault(); 
            gestionarEstado(id, false, "Este campo no acepta números");
            $(idBoton).prop('disabled', true);
        }
       
        else if (/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/.test(chr)) {
            e.preventDefault(); // Evita que se escriba
            gestionarEstado(id, false, "Este campo permite solo letras");
            $(idBoton).prop('disabled', true);
        }
    });

    $("#nombre_metodopago").on("input", () => validarMetodoPago("nombre_metodopago"));
    $("#nombreModificar").on("input", () => validarMetodoPago("nombreModificar"));

    $("#moneda, #monedaModificar").on("change blur", function () {
        gestionarMoneda(this.id, Boolean($(this).val()));
    });

    function validarFormulario(idFormulario, esModificacion) {
        const nombreId = esModificacion ? "nombreModificar" : "nombre_metodopago";
        const monedaId = esModificacion ? "monedaModificar" : "moneda";
        const $boton = esModificacion ? $("#modificarDatos") : $("#guardarMetodopago");

        $("#" + nombreId + ", #" + monedaId).trigger("blur").trigger("change");
        validarMetodoPago(nombreId);
        gestionarMoneda(monedaId, Boolean($("#" + monedaId).val()));

        const valido = $("#" + nombreId).hasClass('is-valid') && $("#" + monedaId).hasClass('is-valid');
        if (!valido) {
            $boton.prop('disabled', false);
            const nombreVacio = !$("#" + nombreId).val().trim();
            Swal.fire({
                icon: 'warning',
                title: nombreVacio ? 'Nombre requerido' : 'Moneda requerida',
                text: nombreVacio
                    ? 'Indique el nombre del método de pago.'
                    : 'Seleccione una moneda válida.',
                background: '#000910',
                color: 'white'
            });
        }
        return valido;
    }

    $("#guardarMetodopago").on("click", function (e) {
        if (!validarFormulario("formRegistroMetodopago", false)) {
            e.preventDefault();
            return false;
        }
    });

    $("#modificarDatos").on("click", function (e) {
        if (!validarFormulario("formModificar", true)) {
            e.preventDefault();
            return false;
        }
    });

    $(".radios").change(function () { 
        validarMetodoPago("nombre_metodopago");
    });

    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $(this).find('.form-control').removeClass('is-valid is-invalid');
        $("#metodoFeedback, #metodoModificarFeedback").hide().text("");
        $("#guardarMetodopago, #modificarDatos").prop('disabled', false);
    });
});
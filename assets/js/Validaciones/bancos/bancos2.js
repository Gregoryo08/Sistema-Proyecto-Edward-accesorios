$(document).ready(function () {

    function gestionarEstado(id, esValido, mensaje = "") {
        const $el = $("#" + id);
        const $errorDiv = $("#error_" + id);
        
        if (esValido) {
            $el.removeClass('is-invalid').addClass('is-valid');
            $errorDiv.hide().text("");
        } else {
            $el.removeClass('is-valid').addClass('is-invalid');
            $errorDiv.text(mensaje).show();
        }
    }

    function verificarFormulario(tipo) {
        const sufijo = tipo === "registro" ? "" : "_modificar";
        const boton = tipo === "registro" ? "#registrar" : "#modificar";

        if (tipo === "modificar") {
            $(boton).prop('disabled', false);
            return;
        }

        const todoValido = $(`#nombre${sufijo}`).hasClass('is-valid') &&
                           $(`#numero${sufijo}`).hasClass('is-valid') &&
                           $(`#cedula${sufijo}`).hasClass('is-valid') &&
                           $(`#telefono${sufijo}`).hasClass('is-valid');

        $(boton).prop('disabled', !todoValido);
    }

    function limitarCampo(selector, maximo, patron) {
        $(selector).attr("maxlength", maximo).on("input", function () {
            let valor = $(this).val();
            if (patron) valor = valor.replace(patron, "");
            $(this).val(valor.slice(0, maximo));
        });
    }

    function validarNombreBanco(id, tipo) {
        let $input = $("#" + id);
        let entrada = $input.val();

        if (/[0-9]/.test(entrada)) {
            gestionarEstado(id, false, "Este campo no acepta números");
            $input.val(entrada.replace(/[0-9]/g, ""));
            verificarFormulario(tipo);
            return;
        }

        let soloLetras = entrada.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, "");
        let capitalizado = soloLetras.replace(/\b\w+/g, w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase());
        $input.val(capitalizado);

        let valorTrim = capitalizado.trim();

        if (valorTrim.length === 0) {
            gestionarEstado(id, false, "Este campo es obligatorio");
        } else if (valorTrim.length < 4 || valorTrim.length > 40) {
            gestionarEstado(id, false, "Debe tener entre 4 y 40 letras");
        } else {
            gestionarEstado(id, true);
        }
        verificarFormulario(tipo);
    }

    function validarNumeroCuenta(id, tipo) {
        let $input = $("#" + id);
        let entrada = $input.val();

        if (/[^0-9]/.test(entrada)) {
            gestionarEstado(id, false, "Este campo solo acepta números");
            $input.val(entrada.replace(/[^0-9]/g, ""));
            verificarFormulario(tipo);
            return;
        }

        if (entrada.length === 0) {
            gestionarEstado(id, false, "Este campo es obligatorio");
        } else if (entrada.length < 8) {
            gestionarEstado(id, false, "El valor ingresado debe de ser mayor a 8 digitos!");
        } else if (entrada.length > 20) {
            gestionarEstado(id, false, "El número de cuenta debe tener entre 8 y 20 dígitos");
        } else {
            gestionarEstado(id, true);
        }
        verificarFormulario(tipo);
    }

    function validarCedulaRif(id, tipo) {
        let $input = $("#" + id);
        let entrada = $input.val();

        if (/[^0-9]/.test(entrada)) {
            gestionarEstado(id, false, "Este campo solo acepta números");
            $input.val(entrada.replace(/[^0-9]/g, ""));
            verificarFormulario(tipo);
            return;
        }

        if (entrada.length === 0) {
            gestionarEstado(id, false, "Este campo es obligatorio");
        } else if (entrada.length < 6 || entrada.length > 9) {
            gestionarEstado(id, false, "El valor ingresado debe de tener entre 6 y 9 numeros!");
        } else {
            
            gestionarEstado(id, true);
        }
        verificarFormulario(tipo);
    }

    function validarTelefono(id, tipo) {
        let $input = $("#" + id);
        let entrada = $input.val();

        if (/[^0-9]/.test(entrada)) {
            gestionarEstado(id, false, "Este campo solo acepta números");
            $input.val(entrada.replace(/[^0-9]/g, ""));
            verificarFormulario(tipo);
            return;
        }

        if (entrada.length === 0) {
            gestionarEstado(id, false, "Este campo es obligatorio");
        } else if (entrada.length > 11) {
            gestionarEstado(id, false, "El teléfono no debe superar los 11 dígitos.");
        } else {
            gestionarEstado(id, true);
        }
        verificarFormulario(tipo);
    }
    
    limitarCampo("#nombre, #nombre_modificar", 40, /[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g);
    limitarCampo("#numero, #numero_modificar", 20, /[^0-9]/g);
    limitarCampo("#cedula, #cedula_modificar", 9, /[^0-9]/g);
    limitarCampo("#telefono, #telefono_modificar", 11, /[^0-9]/g);

    $("#nombre").on("input", () => validarNombreBanco("nombre", "registro"));
    $("#numero").on("input", () => validarNumeroCuenta("numero", "registro"));
    $("#cedula").on("input", () => validarCedulaRif("cedula", "registro"));
    $("#telefono").on("input", () => validarTelefono("telefono", "registro"));

    $("#nombre_modificar").on("input", () => validarNombreBanco("nombre_modificar", "modificar"));
    $("#numero_modificar").on("input", () => validarNumeroCuenta("numero_modificar", "modificar"));
    $("#cedula_modificar").on("input", () => validarCedulaRif("cedula_modificar", "modificar"));
    $("#telefono_modificar").on("input", () => validarTelefono("telefono_modificar", "modificar"));

        $("#nombre, #nombre_modificar").on("keydown", function (e) {
            if (e.key.length === 1 && /[0-9]/.test(e.key)) {
                e.preventDefault();
                gestionarEstado(this.id, false, "Este campo solo puede aceptar letras!");
                Swal.fire({
                    title: "Nombre inválido",
                    text: "Este campo solo puede aceptar letras!",
                    icon: "warning",
                    color: "white",
                    background: "#000910"
                });
            }
        });

    $("#numero, #cedula, #telefono, #numero_modificar, #cedula_modificar, #telefono_modificar").on("keydown", function (e) {
        if (e.key.length === 1 && !/[0-9]/.test(e.key)) {
            e.preventDefault();
            $(this).removeClass("is-valid").addClass("is-invalid");
            $("#error_" + this.id).text("Este campo solo puede aceptar números.").show();
            Swal.fire({
                title: this.id.indexOf("telefono") !== -1 ? "Teléfono inválido" : "Campo inválido",
                text: "Este campo solo puede aceptar números.",
                icon: "warning",
                color: "white",
                background: "#000910"
            });
        }
    });

    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $(this).find('.form-control').removeClass('is-valid is-invalid');
        $(this).find('.invalid-feedback').hide().text("");

        $("#registrar, #modificar").prop('disabled', false);
    });
});
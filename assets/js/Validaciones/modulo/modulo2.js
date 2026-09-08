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

    function validarCampo(id) {
        let $input = $("#" + id);
        let entrada = $input.val();
        let idBoton = (id === "modulo") ? "#registro" : "#modificar";

        if (/[0-9]/.test(entrada)) {
            gestionarEstado(id, false, "Este campo no acepta números");
            $(idBoton).prop('disabled', false);
            $input.val(entrada.replace(/[0-9]/g, ""));
            return;
        }
        
        let soloLetras = entrada.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, "");
        
        let capitalizado = soloLetras.replace(/\b\w+/g, w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase());
        $input.val(capitalizado);

        let valorTrim = capitalizado.trim();
        let esValido = valorTrim.length >= 4 && valorTrim.length <= 30;

        if (valorTrim.length === 0) {
            gestionarEstado(id, false, "El nombre del módulo es obligatorio");
            $(idBoton).prop('disabled', false);
        } else if (!esValido) {
            gestionarEstado(id, false, "Debe tener entre 4 y 30 letras");
            $(idBoton).prop('disabled', true);
        } else {
            gestionarEstado(id, true);
            $(idBoton).prop('disabled', false);
        }
    }

    $("#modulo").on("input", () => validarCampo("modulo"));
    $("#modulo_modificar").on("input", () => validarCampo("modulo_modificar"));

    $("#modulo, #modulo_modificar").on("keydown", function (e) {
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

    $('.modal').on('hidden.bs.modal', function () {
        if ($(this).find('form').length && $(this).find('form')[0]) {
            $(this).find('form')[0].reset();
        }
        $(this).find('.form-control').removeClass('is-valid is-invalid');
        $(this).find('.msg-error').hide().text("");
        
        $("#registro, #modificar").prop('disabled', false);
        
        if (!$('.modal.show').length) {
            $('body').removeClass('modal-open').css('overflow', '');
            $('.modal-backdrop').remove();
        }
    });

    $("#registro, #modificar").prop('disabled', false);
});
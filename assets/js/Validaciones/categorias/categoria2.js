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
        let idBoton = (id === "categoria") ? "#registro" : "#modificar";

        if (/[0-9]/.test(entrada)) {
            gestionarEstado(id, false, "Este campo solo puede aceptar letras!");
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
            gestionarEstado(id, false, "El nombre de la categoría es obligatorio");
            $(idBoton).prop('disabled', false);
        } else if (!esValido) {
            gestionarEstado(id, false, "Debe tener entre 4 y 30 letras");
            $(idBoton).prop('disabled', true);
        } else {
            gestionarEstado(id, true);
            $(idBoton).prop('disabled', false);
        }
    }

    $("#categoria").on("input", () => validarCampo("categoria"));
    $("#categoria_modificar").on("input", () => validarCampo("categoria_modificar"));

    $("#categoria, #categoria_modificar").on("keypress", function (e) {
        if (/[0-9]/.test(String.fromCharCode(e.which))) {
            e.preventDefault();
            const mensaje = $("#error_" + this.id);
            $(this).removeClass("is-valid").addClass("is-invalid");
            mensaje.text("Este campo solo puede aceptar letras!").show();
            Swal.fire({
                title: "Campo inválido",
                text: "Este campo solo puede aceptar letras!",
                icon: "warning",
                color: "white",
                background: "#000910"
            });
        }
    });

    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $(this).find('.form-control').removeClass('is-valid is-invalid');
        $(this).find('.msg-error').hide();

        $("#registro, #modificar").prop('disabled', false);
    });

    $("#registro, #modificar").prop('disabled', false);
});
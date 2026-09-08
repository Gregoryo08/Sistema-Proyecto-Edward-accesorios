$(document).ready(function () {

    $("#registro, #modificar").prop('disabled', false);

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

    function validarCargo(id) {
        let $input = $("#" + id);
        let entrada = $input.val();
        let idBoton = (id === "cargo") ? "#registro" : "#modificar";
        
        if (/[0-9]/.test(entrada)) {
            var cursorPosicion = $input.prop("selectionStart");
            
            let sinNumeros = entrada.replace(/[0-9]/g, "");
            $input.val(sinNumeros);
            
            $input.prop("selectionStart", cursorPosicion - 1);
            $input.prop("selectionEnd", cursorPosicion - 1);

            gestionarEstado(id, false, "Este campo no acepta números");
            $(idBoton).prop('disabled', true);
            return; 
        }
        
        let soloLetras = entrada.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, "");
        
        if (entrada !== soloLetras) {
            var cursorPosicion = $input.prop("selectionStart");
            $input.val(soloLetras);
            $input.prop("selectionStart", cursorPosicion - 1);
            $input.prop("selectionEnd", cursorPosicion - 1);
            
            gestionarEstado(id, false, "Este campo permite solo letras");
            $(idBoton).prop('disabled', true);
            return;
        }

        let capitalizado = soloLetras.replace(/\b\w+/g, w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase());
        
        var cursorPosicion = $input.prop("selectionStart");
        $input.val(capitalizado);
        $input.prop("selectionStart", cursorPosicion);
        $input.prop("selectionEnd", cursorPosicion);

        let valorTrim = capitalizado.trim();

        if (valorTrim.length === 0) {
            gestionarEstado(id, false, "El nombre del cargo es obligatorio");
            $(idBoton).prop('disabled', false);
            return;
        } 
        
        if (valorTrim.length < 4 || valorTrim.length > 30) {
            gestionarEstado(id, false, "Debe tener entre 4 y 30 letras");
            $(idBoton).prop('disabled', true);
            return;
        }

        if (id === "cargo_modificar" && window.cargo_Modificar === capitalizado) {
            if ($("#precio_modificar").hasClass('is-invalid')) {
                $(idBoton).prop('disabled', true);
            } else {
                gestionarEstado(id, true);
                $(idBoton).prop('disabled', false);
            }
            return;
        }

        $.ajax({
            type: "POST",
            url: "?pagina=cargos", 
            data: {
                nombre: capitalizado,
                accion: "validarNombre",
            },
            success: function (response) {
                if (response >= 1) {
                    gestionarEstado(id, false, "¡Este cargo ya se encuentra registrado!");
                    $(idBoton).prop('disabled', true);
                } else {
                    gestionarEstado(id, true);

                    
                    if (!$("#precio_modificar").hasClass('is-invalid')) {
                        $(idBoton).prop('disabled', false);
                    }
                }
            },
            error: function () {
                gestionarEstado(id, false, "Error de comunicación con el servidor");
                $(idBoton).prop('disabled', true);
            }
        });
    }

    function validarPrecio(id) {
        let $input = $("#" + id);
        let entrada = $input.val();
        
        let soloNumeros = entrada.replace(/[^0-9.]/g, "");

        if ((soloNumeros.match(/\./g) || []).length > 1) {
            soloNumeros = soloNumeros.replace(/\.+$/, "");
        }

        if (entrada !== soloNumeros) {
            $input.val(soloNumeros);
            gestionarEstado(id, false, "Este campo permite solo números");
            $("#modificar").prop('disabled', true);
            return;
        }

        let valorTrim = soloNumeros.trim();

        if (valorTrim.length === 0) {
            $input.removeClass('is-valid is-invalid');
            $("#error_" + id).hide().text("");
            
            if ($("#cargo_modificar").hasClass('is-valid')) {
                $("#modificar").prop('disabled', false);
            }
            return;
        }

        if (isNaN(parseFloat(valorTrim))) {
            gestionarEstado(id, false, "Ingrese un precio numérico válido");
            $("#modificar").prop('disabled', true);
        } else {
            gestionarEstado(id, true);

            if ($("#cargo_modificar").hasClass('is-valid')) {
                $("#modificar").prop('disabled', false);
            }
        }
    }

    $("#cargo").on("input", () => validarCargo("cargo"));
    $("#cargo_modificar").on("input", () => validarCargo("cargo_modificar"));
    $("#precio_modificar").on("input", () => validarPrecio("precio_modificar"));

    $("#cargo, #cargo_modificar").on("keypress", function (e) {
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
        $(this).find('.msg-error, .invalid-feedback').hide().text("");
        $("#registro, #modificar").prop('disabled', false);
    });
});
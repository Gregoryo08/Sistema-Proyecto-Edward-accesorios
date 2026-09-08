$(document).ready(function () {
    function verificarFormulario(form) {
        let inputs = form.find('input[required], select[required]');
        let todosValidos = true;
        inputs.each(function() {
            if (!$(this).hasClass('is-valid')) {
                todosValidos = false;
            }
        });
        form.find('button[type="submit"]').prop('disabled', !todosValidos);
    }

    $(document).on('click', '#toggleLogin, #toggleReg, #toggleConf', function() {
        let targetId = $(this).attr('id') === 'toggleLogin' ? '#Contraseña' : 
                       ($(this).attr('id') === 'toggleReg' ? '#reg_pass' : '#conf_pass');
        let input = $(targetId);
        let icon = $(this);
        let type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        icon.toggleClass('bx-show bx-hide');
    });

    function esMayorDeEdad(fechaString) {
        let fechaNacimiento = new Date(fechaString);
        let fechaActual = new Date();
        let edad = fechaActual.getFullYear() - fechaNacimiento.getFullYear();
        let mes = fechaActual.getMonth() - fechaNacimiento.getMonth();
        if (mes < 0 || (mes === 0 && fechaActual.getDate() < fechaNacimiento.getDate())) {
            edad--;
        }
        return edad >= 18;
    }

    function validarFormato(input) {
        let valor = input.val();
        let name = input.attr('name');
        let esValido = true;
        let mensajeError = "Formato inválido";
        let container = input.closest('.input-box');
        let icon = container.find('i').not('#toggleLogin, #toggleReg, #toggleConf');
        let errorElement = container.find('.error-msg');

        if (name === 'usuario') {
            esValido = valor && valor.length > 0 && valor.length <= 20;
            mensajeError = "Máximo 20 caracteres";
        } else if (name === 'clave') {
            esValido = valor && valor.length >= 6 && valor.length <= 30;
            mensajeError = "Mínimo 6 caracteres";
        } else if (name === 'confirmar_clave') {
            esValido = valor === $('#reg_pass').val() && valor.length >= 6;
            mensajeError = "No coinciden";
        } else if (name === 'cedula') {
            esValido = /^[VE]-\d{1,9}$/.test(valor);
            mensajeError = "Formato: V- o E- seguido de números";
        } else if (name === 'telefono') {
            esValido = /^\d{1,13}$/.test(valor);
            mensajeError = "Solo números";
        } else if (name === 'nombre' || name === 'apellido') {
            esValido = /^[a-zA-ZáéíóúÁÉÍÓÚ\s]{1,30}$/.test(valor);
            mensajeError = "Solo letras";
        } else if (name === 'correo') {
            esValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor) && valor.length <= 30;
            mensajeError = "Correo inválido";
        } else if (name === 'residencia') {
            esValido = valor && valor.length > 0 && valor.length <= 30;
            mensajeError = "Campo requerido";
        } else if (name === 'fecha_nacimiento') {
            esValido = valor !== "" && esMayorDeEdad(valor);
            mensajeError = "Debes ser mayor de 18 años";
        } else {
            esValido = valor !== null && valor !== undefined && valor.trim() !== "";
            mensajeError = "Campo obligatorio";
        }

        if (esValido) {
            input.removeClass('is-invalid').addClass('is-valid');
            icon.removeClass('error-icon').addClass('success-icon');
            errorElement.hide();
        } else {
            input.removeClass('is-valid').addClass('is-invalid');
            icon.removeClass('success-icon').addClass('error-icon');
            errorElement.text(mensajeError).show();
        }
        
        verificarFormulario(input.closest('form'));
        return esValido;
    }

    $('#reg_pass, #conf_pass').on('input', function () {
        validarFormato($(this));
    });

    $('#loginForm input, #formRegistro input').on('input', function () {
        let name = $(this).attr('name');
        
        if (name === 'cedula') {
            let val = $(this).val().toUpperCase().replace(/[^VE0-9]/g, '');
            if (val.length > 0) {
                let letra = val[0];
                if (letra !== 'V' && letra !== 'E') {
                    Swal.fire({
                        title: "Atención",
                        text: "La cédula debe comenzar con V o E",
                        icon: "warning",
                        background: "#000910",
                        color: "white"
                    });
                    val = '';
                } else {
                    let nums = val.substring(1).replace(/[^0-9]/g, '');
                    val = letra + '-' + nums;
                }
            }
            $(this).val(val.slice(0, 11));
        } else if (name === 'telefono') {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13);
        } else if (name === 'nombre' || name === 'apellido') {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚ\s]/g, '').slice(0, 30);
        } else if (name === 'correo' || name === 'residencia') {
            if(this.value.length > 30) this.value = this.value.slice(0, 30);
        }
        validarFormato($(this));
    });

    $('select[name="sexo"], input[type="date"]').on('change', function () {
        validarFormato($(this));
    });

    $('input, select').on('input change', function() {
        $(this).toggleClass('has-text', $(this).val() !== null && $(this).val().length > 0);
    });

    $('#formRegistro, #loginForm').on('submit', function (e) {
        let valido = true;
        $(this).find('input[required], select[required]').each(function () {
            if (!validarFormato($(this))) valido = false;
        });
        if (!valido) e.preventDefault();
    });
});
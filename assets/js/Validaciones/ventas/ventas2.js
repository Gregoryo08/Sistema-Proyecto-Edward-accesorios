function aplicarRestriccionesCliente() {
    $('#cedula').on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 8);
    });

    $('#nombre, #apellido').on('input', function () {
        this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '').slice(0, 40);
    });

    $('#telefono').on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 7);
    });
}

function validarFormularioCliente() {
    let esValido = true;

    let $cedula = $('#cedula');
    let $nombre = $('#nombre');
    let $apellido = $('#apellido');
    let $telefono = $('#telefono');

    const regexCedula = /^\d{6,8}$/;
    const regexTexto = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,40}$/;
    const regexTelefono = /^\d{7}$/;

    $('.invalid-feedback-custom').remove();
    $('.is-invalid').removeClass('is-invalid');

    let valCedula = $cedula.val().trim();
    if (!valCedula) {
        mostrarErrorInput($cedula, 'Por favor ingresa la cédula.');
        esValido = false;
    } else if (!regexCedula.test(valCedula)) {
        mostrarErrorInput($cedula, 'Debe tener entre 6 y 8 dígitos.');
        esValido = false;
    }

    let valNombre = $nombre.val().trim();
    if (!valNombre) {
        mostrarErrorInput($nombre, 'Por favor ingresa el nombre.');
        esValido = false;
    } else if (!regexTexto.test(valNombre)) {
        mostrarErrorInput($nombre, 'Debe tener entre 3 y 40 caracteres.');
        esValido = false;
    }

    let valApellido = $apellido.val().trim();
    if (!valApellido) {
        mostrarErrorInput($apellido, 'Por favor ingresa el apellido.');
        esValido = false;
    } else if (!regexTexto.test(valApellido)) {
        mostrarErrorInput($apellido, 'Debe tener entre 3 y 40 caracteres.');
        esValido = false;
    }

    let valTelefono = $telefono.val().trim();
    if (valTelefono && !regexTelefono.test(valTelefono)) {
        mostrarErrorInput($telefono, 'Debe tener exactamente 7 dígitos.');
        esValido = false;
    }

    return esValido;
}

function mostrarErrorInput($input, mensaje) {
    $input.addClass('is-invalid');
    
    let $contenedor = $input.closest('.input-group').length 
        ? $input.closest('.input-group') 
        : $input;

    $contenedor.after(`<small class="invalid-feedback-custom text-danger font-weight-bold d-block mt-1">${mensaje}</small>`);
}

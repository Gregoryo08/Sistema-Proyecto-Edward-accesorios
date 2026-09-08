$(function() {

    function capitalizarPalabras(str) {
        return str.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
    }

    function validarFechaNacimiento(fecha) {
        if (!fecha) return false;
        var anio = new Date(fecha).getFullYear();
        var cumple = new Date(fecha);
        var hoy = new Date();
        if (cumple.toDateString() === hoy.toDateString()) return false;
        return anio >= 1955 && anio <= 2008;
    }

    function validarCedulaCompleta() {
        var tipo = $('#tipo_cedula').val();
        var numero = $('#cedula').val().replace(/[^0-9]/g, '');
        var completa = tipo + '-' + numero;
        return /^(V|E|J|P)-\d{7,12}$/.test(completa);
    }

    $('#cedula').on('keyup', function() {
        var soloNumeros = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(soloNumeros);
        var valida = validarCedulaCompleta();
        var css = valida ? {'border': '2px solid #28a745'} : (soloNumeros.length > 0 ? {'border': '2px solid #dc3545'} : {'border': '1px solid #ced4da'});
        $('#cedula, #tipo_cedula').css(css);
    });

    $('#tipo_cedula').on('change', function() {
        $('#cedula').trigger('keyup');
    });

    $('#nombre_apellido').on('blur', function() {
        $(this).val(capitalizarPalabras($(this).val()));
    });

    $('#clienteRegistroForm').on('submit', function(e) {
        e.preventDefault();

        var tipoCedula = $('#tipo_cedula').val();
        var numeroCedula = $('#cedula').val().replace(/[^0-9]/g, '');
        var nombreCompleto = $('#nombre_apellido').val().trim();
        
        var partesNombre = nombreCompleto.split(' ');
        var nombre = partesNombre[0] || '';
        var apellido = partesNombre.slice(1).join(' ') || '';

        var correo = $('#correo').val().trim();
        var telefono = $('#telefono').val().replace(/[^0-9]/g, '');
        var clave = $('#clave').val();
        var confirmar = $('#confirmar_clave').val();
        var direccion = $('#direccion').val().trim();
        var fechaNac = $('#fecha_nacimiento').val();
        var sexo = $('#sexo').val();

        if (!validarCedulaCompleta()) {
            Swal.fire('Error', 'Cédula inválida', 'warning');
            return;
        }
        if (partesNombre.length < 2) {
            Swal.fire('Error', 'Ingrese nombre y apellido', 'warning');
            return;
        }
        if (clave !== confirmar || clave.length < 4) {
            Swal.fire('Error', 'Las contraseñas no coinciden o son muy cortas', 'warning');
            return;
        }
        if (!validarFechaNacimiento(fechaNac)) {
            Swal.fire('Error', 'Fecha de nacimiento inválida. Debe ser entre 1955 y 2008 y no puede ser la fecha actual.', 'warning');
            return;
        }

        var btn = $('#btnRegistro');
        btn.prop('disabled', true).html('Registrando...');

        $.ajax({
            url: '?pagina=registroEcommerce&action=procesar',
            type: 'POST',
            data: {
                cedula_persona: tipoCedula + '-' + numeroCedula,
                nombre: nombre,
                apellido: apellido,
                correo: correo,
                telefono: telefono,
                direccion: direccion,
                clave: clave,
                fecha_nacimiento: fechaNac,
                sexo: sexo
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    Swal.fire('¡Éxito!', res.message, 'success').then(() => {
                        window.location.href = res.redirect || '?pagina=web_Catalogo';
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                    btn.prop('disabled', false).html('Registrarse');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error de conexión', 'error');
                btn.prop('disabled', false).html('Registrarse');
            }
        });
    });
});
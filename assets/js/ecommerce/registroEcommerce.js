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

    function cédulaCompleta() {
        var tipo = $('#tipo_cedula').val();
        var numero = $('#cedula').val().replace(/[^0-9]/g, '');
        return tipo + '-' + numero;
    }

    function validarCedulaCompleta() {
        return /^[VEJP]-\d{7,12}$/.test(cédulaCompleta());
    }

    var registroCedulaVerificada = false;
    var tokenConfirmacion = '';

    function limpiarEstadoCedula() {
        registroCedulaVerificada = false;
        tokenConfirmacion = '';
        $('#datosExistentes').addClass('d-none').empty();
        $('.wrapper').removeClass('mostrar-confirmacion');
        $('#confirmar_datos').val('0');
    }

    var pending = localStorage.getItem('pendingRegistration');
    if (pending) {
        try {
            var d = JSON.parse(pending);
            if (d.cedula_persona) {
                var parts = d.cedula_persona.split('-');
                if (parts.length === 2) {
                    $('#tipo_cedula').val(parts[0]);
                    $('#cedula').val(parts[1]);
                }
            }
            if (d.nombre || d.apellido) {
                $('#nombre_apellido').val(((d.nombre || '') + ' ' + (d.apellido || '')).trim());
            }
            if (d.correo)    $('#correo').val(d.correo);
            if (d.telefono)  $('#telefono').val(d.telefono);
            if (d.direccion) $('#direccion').val(d.direccion);
            localStorage.removeItem('pendingRegistration');
            $('.wrapper').addClass('active');
            $('.wrapper').removeClass('show-recover');
        } catch (e) {
            localStorage.removeItem('pendingRegistration');
        }
    }

    $('#cedula').on('keyup', function() {
        var soloNumeros = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(soloNumeros);
        var valida = validarCedulaCompleta();
        var css = valida ? {'border': '2px solid #28a745'} : (soloNumeros.length > 0 ? {'border': '2px solid #dc3545'} : {'border': '1px solid #ced4da'});
        $('#cedula, #tipo_cedula').css(css);
        if (!valida) limpiarEstadoCedula();
    });

    $('#tipo_cedula').on('change', function() {
        $('#cedula').trigger('keyup');
    });

    $('#nombre_apellido').on('blur', function() {
        $(this).val(capitalizarPalabras($(this).val()));
    });

    function getCaptchaToken() {
        if (typeof RECAPTCHA_HABILITADO !== 'undefined' && RECAPTCHA_HABILITADO === true) {
            if (typeof grecaptcha === 'undefined' || !window.recaptchaWidgets || typeof window.recaptchaWidgets.registro !== 'number') return '';
            return grecaptcha.getResponse(window.recaptchaWidgets.registro);
        }
        return '';
    }

    function resetCaptcha() {
        if (typeof RECAPTCHA_HABILITADO !== 'undefined' && RECAPTCHA_HABILITADO === true) {
            if (typeof grecaptcha !== 'undefined' && window.recaptchaWidgets && typeof window.recaptchaWidgets.registro === 'number') {
                grecaptcha.reset(window.recaptchaWidgets.registro);
            }
        }
    }

    function verificarCedula(tokenCaptcha) {
        var cedula = cédulaCompleta();

        Swal.fire({ title: 'Verificando cédula...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });

        var data = { cedula_persona: cedula };
        if (typeof RECAPTCHA_HABILITADO !== 'undefined' && RECAPTCHA_HABILITADO === true && tokenCaptcha) {
            data['g-recaptcha-response'] = tokenCaptcha;
        }

        return $.ajax({
            url: '?pagina=registroEcommerce&action=verificar',
            type: 'POST',
            data: data,
            dataType: 'json'
        });
    }

    function mostrarConfirmacion(res) {
        var d = res.datos || {};
        var html = '<div class="text-start">' +
            '<p class="mb-2">Ya tenemos datos registrados con esta cédula. Revisa que la información sea correcta:</p>' +
            '<table class="table table-sm table-bordered mb-0">' +
            '<tr><th class="w-25">Cédula</th><td>' + (d.cedula_persona || '') + '</td></tr>' +
            '<tr><th>Nombre</th><td>' + (d.nombre || '') + ' ' + (d.apellido || '') + '</td></tr>' +
            '<tr><th>Correo</th><td>' + (d.correo || '') + '</td></tr>' +
            '<tr><th>Teléfono</th><td>' + (d.telefono || '') + '</td></tr>' +
            '<tr><th>Dirección</th><td>' + (d.direccion || '') + '</td></tr>' +
            '</table></div>';

        Swal.fire({
            title: '¿Son correctos tus datos?',
            html: html,
            showCancelButton: true,
            confirmButtonText: 'Sí, son correctos',
            cancelButtonText: 'No, corregirlos',
            confirmButtonColor: '#0077b6',
            cancelButtonColor: '#dc3545',
            input: 'checkbox',
            inputValue: '1',
            inputPlaceholder: 'Confirmo que los datos mostrados son míos',
            inputAttributes: { required: 'checked' }
        }).then(function (result) {
            if (result.isConfirmed) {
                if (d.nombre) $('#nombre_apellido').val((d.nombre + ' ' + (d.apellido || '')).trim());
                if (d.correo) $('#correo').val(d.correo);
                if (d.telefono) $('#telefono').val(d.telefono);
                if (d.direccion) $('#direccion').val(d.direccion);

                registroCedulaVerificada = true;
                tokenConfirmacion = res.tokenConfirmacion || '';
                $('#confirmar_datos').val('1');
                $('#datosExistentes').removeClass('d-none').html(
                    '<div class="alert alert-success mb-0"><strong>Datos confirmados.</strong> Revisa la información, elige tu contraseña y acepta el captcha para finalizar.</div>'
                );
                $('.wrapper').addClass('mostrar-confirmacion');
                Swal.fire({ icon: 'success', title: 'Datos confirmados', text: 'Completa la contraseña y el captcha para crear tu acceso.' });
            } else {
                limpiarEstadoCedula();
                Swal.fire({ icon: 'info', title: 'Corrige tus datos', text: 'Completa el formulario con la información correcta.' });
            }
        });
    }

    function verificarYMostrar() {
        if (!validarCedulaCompleta()) {
            Swal.fire('Error', 'Cédula inválida. Debe ser tipo V, E, J o P seguido de 7 a 12 dígitos.', 'warning');
            return;
        }

        var token = getCaptchaToken();
        if ((typeof RECAPTCHA_HABILITADO !== 'undefined' && RECAPTCHA_HABILITADO === true) && !token) {
            Swal.fire('Verificación necesaria', 'Resuelve el captcha antes de verificar tu cédula.', 'warning');
            return;
        }

        verificarCedula(token).then(function (res) {
            if (res.needsReset) {
                resetCaptcha();
                Swal.fire({
                    icon: 'error',
                    title: 'Ya tienes cuenta',
                    text: res.message,
                    showCancelButton: true,
                    confirmButtonText: 'Entendido',
                    cancelButtonText: 'Recuperar contraseña',
                    confirmButtonColor: '#0077b6',
                    cancelButtonColor: '#dc3545'
                }).then(function (r) {
                    if (r.dismiss === Swal.DismissReason.cancel) {
                        window.location.href = '?pagina=login_principal&recover=1';
                    }
                });
                return;
            }

            if (!res.success) {
                resetCaptcha();
                Swal.fire('Error', res.message, 'warning');
                return;
            }

            if (res.requiereConfirmacion) {
                mostrarConfirmacion(res);
            } else {
                Swal.close();
                registroCedulaVerificada = true;
                tokenConfirmacion = '';
                $('#confirmar_datos').val('1');
                $('#datosExistentes').removeClass('d-none').html(
                    '<div class="alert alert-success mb-0"><strong>Cédula verificada.</strong> Completa tus datos para registrarte.</div>'
                );
            }
        }).catch(function () {
            resetCaptcha();
            Swal.fire('Error', 'No se pudo verificar la cédula. Intenta de nuevo.', 'error');
        });
    }

    $('#btnVerificarCedula').on('click', function(e) {
        e.preventDefault();
        verificarYMostrar();
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
        var confirmaDatos = $('#confirmar_datos').val() === '1';

        if (!validarCedulaCompleta()) {
            Swal.fire('Error', 'Cédula inválida', 'warning');
            return;
        }
        if (!confirmaDatos) {
            Swal.fire('Verificación pendiente', 'Primero debes verificar tu cédula con el botón "Verificar cédula".', 'warning');
            return;
        }
        if (partesNombre.length < 2) {
            Swal.fire('Error', 'Ingrese nombre y apellido', 'warning');
            return;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(correo)) {
            Swal.fire('Error', 'El correo no es válido', 'warning');
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

        if (!registroCedulaVerificada) {
            Swal.fire('Verificación pendiente', 'Primero debes verificar tu cédula.', 'warning');
            return;
        }

        var btn = $('#btnRegistro');
        btn.prop('disabled', true).html('Registrando...');

        var payload = {
            cedula_persona: tipoCedula + '-' + numeroCedula,
            nombre: nombre,
            apellido: apellido,
            correo: correo,
            telefono: telefono,
            direccion: direccion,
            clave: clave,
            fecha_nacimiento: fechaNac,
            sexo: sexo,
            token_confirmacion: tokenConfirmacion
        };

        if (typeof RECAPTCHA_HABILITADO !== 'undefined' && RECAPTCHA_HABILITADO === true) {
            var tokenRegistro = getCaptchaToken();
            if (tokenRegistro) {
                payload['g-recaptcha-response'] = tokenRegistro;
            }
        }

        $.ajax({
            url: '?pagina=registroEcommerce&action=procesar',
            type: 'POST',
            data: payload,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    Swal.fire('¡Éxito!', res.message, 'success').then(function() {
                        window.location.href = res.redirect || '?pagina=web_Catalogo';
                    });
                } else {
                    if (res.estado === 'requiere_confirmacion') {
                        limpiarEstadoCedula();
                        mostrarConfirmacion(res);
                        btn.prop('disabled', false).html('Registrarse');
                        return;
                    }

                    var necesitaRecuperar = res.needsReset === true;
                    Swal.fire({
                        icon: 'error',
                        title: necesitaRecuperar ? 'Ya tienes cuenta' : 'Error',
                        text: res.message,
                        showCancelButton: necesitaRecuperar,
                        confirmButtonText: 'Aceptar',
                        cancelButtonText: 'Recuperar contraseña',
                        confirmButtonColor: '#0077b6',
                        cancelButtonColor: '#dc3545'
                    }).then(function(result) {
                        if (necesitaRecuperar && result.dismiss === Swal.DismissReason.cancel) {
                            window.location.href = '?pagina=login_principal&recover=1';
                        } else {
                            btn.prop('disabled', false).html('Registrarse');
                        }
                    });
                }
            },
            error: function() {
                Swal.fire('Error', 'Error de conexión', 'error');
                btn.prop('disabled', false).html('Registrarse');
            }
        });
    });
});

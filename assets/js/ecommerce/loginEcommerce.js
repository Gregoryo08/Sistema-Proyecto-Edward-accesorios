// =============================================
// LOGIN ECOMMERCE - FUNCIONALIDAD COMPLETA
// =============================================

$(function() {

    // Campos en blanco al cargar: el navegador solo sugiere cédulas/claves
    // guardadas al pulsar el campo (autocomplete=username/current-password).
    // El atributo readonly se habilita al enfocar para impedir el autofill
    // del navegador sobre el campo vacío.
    $('#cedula_cliente, #pass_cliente').val('');

    $(document).on('focus', '#cedula_cliente, #pass_cliente', function () {
        $(this).removeAttr('readonly');
    });

    function limpiarAutofillResidual() {
        var $c = $('#cedula_cliente');
        var $p = $('#pass_cliente');
        if (document.activeElement !== $c[0]) $c.val('');
        if (document.activeElement !== $p[0]) $p.val('');
    }

    $(window).on('load', function () {
        setTimeout(limpiarAutofillResidual, 500);
        setTimeout(limpiarAutofillResidual, 1500);
    });

    // =============================================
    // 1. TOGGLE ENTRE LOGIN / REGISTRO / RECUPERAR
    // =============================================
    $('.register-link').on('click', function(e) {
        e.preventDefault();
        $('.wrapper').addClass('active');
        $('.wrapper').removeClass('show-recover');
    });

    $('.login-link, .back-to-login').on('click', function(e) {
        e.preventDefault();
        $('.wrapper').removeClass('active');
        $('.wrapper').removeClass('show-recover');
    });

    $('.recover-link').on('click', function(e) {
        e.preventDefault();
        $('.wrapper').addClass('show-recover');
        $('.wrapper').removeClass('active');
    });

    // Auto-mostrar registro si viene con ?register=1 o desde el controlador de registro
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('register') === '1' || document.body.classList.contains('mostrar-registro')) {
        $('.wrapper').addClass('active');
        $('.wrapper').removeClass('show-recover');
    }

  // =============================================
// 2. LOGIN - ENVÍO AJAX
// =============================================
$('#clienteLoginForm').on('submit', function(e) {
    e.preventDefault();

    var btn = $(this).find('button[type="submit"]');
    var cedula = $('input[name="cedula"]').val().trim();
    var password = $('input[name="clave"]').val();

    if (!cedula || !password) {
        Swal.fire('Error', 'Todos los campos son obligatorios', 'warning');
        return;
    }

    btn.prop('disabled', true);
    btn.html('<span class="spinner-border spinner-border-sm"></span> Ingresando...');

    $.ajax({
        url: '?pagina=loginEcommerce&action=procesar',
        type: 'POST',
        data: {
            
            cedula: cedula, 
            password: password
        },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Bienvenido!',
                    text: res.message || 'Inicio de sesión exitoso',
                    timer: 2000,
                    showConfirmButton: false
                }).then(function() {
                    window.location.href = res.redirect || '?pagina=web_Catalogo';
                });
            } else {
                Swal.fire('Error', res.message || 'Credenciales incorrectas', 'error');
                btn.prop('disabled', false);
                btn.html('Ingresar');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error de conexión con el servidor', 'error');
            btn.prop('disabled', false);
            btn.html('Ingresar');
        }
    });
});
    // =============================================
    // 3. RECUPERAR CONTRASEÑA - SOLICITAR
    // =============================================
    $('#formSolicitarRecuperacion').on('submit', function(e) {
        e.preventDefault();

        var email = $('#email_recuperacion').val().trim();
        if (!email) {
            Swal.fire('Error', 'Ingresa tu correo electrónico', 'warning');
            return;
        }

        $.ajax({
            url: '?pagina=recuperacion',
            type: 'POST',
            data: {
                accion: 'solicitarRecuperacion',
                email: email
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    Swal.fire('Éxito', res.message || 'Revisa tu correo electrónico', 'success');
                } else {
                    Swal.fire('Error', res.message || 'No se pudo procesar la solicitud', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        });
    });

    // =============================================
    // 4. MOSTRAR / OCULTAR CONTRASEÑA
    // (login, registro y recuperación)
    // =============================================
    $('.input-box i[style*="cursor"]').each(function () {
        var $icon = $(this);
        var $input = $icon.siblings('input');
        if (!$input.length) return;

        $icon.on('click', function () {
            var esClave = $input.attr('type') === 'password';
            $input.attr('type', esClave ? 'text' : 'password');

            if (esClave) {
                $icon.removeClass('bx-lock-alt').addClass('bx-lock-open-alt');
            } else {
                $icon.removeClass('bx-lock-open-alt').addClass('bx-lock-alt');
            }
        });
    });

    // =============================================
    // 5. RESTABLECER CONTRASEÑA (token vía GET)
    // =============================================
    var urlParams = new URLSearchParams(window.location.search);
    var token = urlParams.get('token');
    if (token) {
        $('#tokenHiddenInput').val(token);
        $('#sectionSolicitar').hide();
        $('#sectionRestablecer').show();
    }

    $('#formRestablecerClave').on('submit', function(e) {
        e.preventDefault();

        var nueva = $('#nueva_clave').val();
        var repetir = $('#repetir_clave').val();

        if (nueva.length < 4) {
            Swal.fire('Error', 'Mínimo 4 caracteres', 'warning');
            return;
        }
        if (nueva !== repetir) {
            Swal.fire('Error', 'Las contraseñas no coinciden', 'warning');
            return;
        }

        $.ajax({
            url: '?pagina=recuperacion',
            type: 'POST',
            data: {
                accion: 'restablecerClave',
                token: token,
                nueva_clave: nueva,
                repetir_clave: repetir
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    Swal.fire('Éxito', res.message || 'Contraseña restablecida', 'success').then(function() {
                        window.location.href = '?pagina=loginEcommerce';
                    });
                } else {
                    Swal.fire('Error', res.message || 'No se pudo restablecer', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        });
    });

});

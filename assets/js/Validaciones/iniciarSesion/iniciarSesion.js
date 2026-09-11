$(document).ready(function() {
    let conteo = 1;

    function validarCampos() {
        const user = $('#login').val().trim();
        const pass = $('#Contraseña').val().trim();

        if (user !== "" && pass.length > 5) {
            $("#acceder").prop("disabled", false).css({ "opacity": "1", "cursor": "pointer" });
        } else {
            $("#acceder").prop("disabled", true).css({ "opacity": "0.5", "cursor": "not-allowed" });
        }
    }

    function mensajeBienvenido() {
        Swal.fire({
            title: "¡Bienvenido!",
            icon: "success",
            background: "#000910",
            color: "white",
            showConfirmButton: false,
            timer: 1500
        });
    }

    function mensaje(accion, texto) {
        Swal.fire({
            title: "Aviso",
            text: texto,
            icon: accion === "notFound" ? "warning" : "error",
            background: "#000910",
            color: "white",
            confirmButtonColor: "rgb(238, 191, 0)"
        });
        
        if (accion === "notFound") {
            conteo++;
        }
    }

    validarCampos();
    $('#login, #Contraseña').on('input', validarCampos);

    $("#loginForm").on("submit", function (e) {
        e.preventDefault();

        let recaptchaResponse = $(this).find('[name="g-recaptcha-response"]').val() || "";
        if (recaptchaResponse.length === 0) {
            Swal.fire({
                title: "Verificación Requerida",
                text: "Por favor, marca la casilla del reCAPTCHA.",
                icon: "warning",
                background: "#000910",
                color: "white",
                confirmButtonColor: "rgb(238, 191, 0)"
            });
            return false;
        }

        let datos = {
            "usuario": $("#login").val(),
            "clave": $("#Contraseña").val(),
            "intento": conteo,
            "accion": "logearse",
            "g-recaptcha-response": recaptchaResponse
        };

        $.ajax({
            type: "POST",
            url: "index.php?pagina=iniciarsesion",
            data: datos,
            dataType: "json",
            success: function (res) {
                if (res.data) {
                    mensajeBienvenido();
                    setTimeout(() => {
                        window.location.href = res.data;
                    }, 1500);
                } else {
                    let texto = res.notFound || res.password || res.disabled || res.incorrect || res.recaptcha || "Credenciales incorrectas";
                    let tipo = res.notFound ? "notFound" : "error";
                    
                    if (res.new_intento) {
                        conteo = res.new_intento;
                    }

                    mensaje(tipo, texto);

                    if (typeof grecaptcha !== "undefined") {
                        grecaptcha.reset();
                    }
                }
            },
            error: function (xhr) {
                console.error("Respuesta del servidor:", xhr.responseText);
                mensaje("error", "Error de comunicación con el servidor. Revise la consola.");
                
                if (typeof grecaptcha !== "undefined") {
                    grecaptcha.reset();
                }
            }
        });
    });

    const $cedula = $('#cedula');
    const $btnValidar = $('#btnValidarCedula');
    const $formRegistro = $('#formRegistro');
    const $todosInputs = $formRegistro.find('.input-box:not(:first-child)');
    
    $todosInputs.not(':has(.g-recaptcha)').hide();
    $todosInputs.not(':has(.g-recaptcha)').find('input, select').prop('disabled', true);

    let esActivacion = false;

    $btnValidar.on('click', function() {
        const cedulaVal = $cedula.val().trim();
        if (!cedulaVal) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo vacío',
                text: 'Por favor, ingrese un número de cédula.'
            });
            return;
        }

        $.ajax({
            url: '?pagina=iniciarSesion',
            type: 'POST',
            data: { accion: 'consultarCedula', cedula: cedulaVal },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'YA_REGISTRADO') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Cuenta existente',
                        text: response.msj,
                        confirmButtonText: 'Ir al Login'
                    }).then(() => {
                        $('.login-link').click();
                    });
                } else if (response.status === 'SOLICITAR_TELEFONO') {
                    Swal.fire({
                        title: 'Verificación de seguridad',
                        text: 'Ingrese su número de teléfono asociado (Formato: ' + response.telefono_mascara + ')',
                        input: 'text',
                        inputAttributes: {
                            autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Verificar',
                        cancelButtonText: 'Cancelar',
                        showLoaderOnConfirm: true,
                        preConfirm: (telefono) => {
                            return $.ajax({
                                url: '?pagina=iniciarSesion',
                                type: 'POST',
                                data: { accion: 'verificarTelefono', cedula: cedulaVal, telefono: telefono },
                                dataType: 'json'
                            }).then(res => {
                                if (res.status !== 'TELEFONO_CORRECTO') {
                                    throw new Error(res.msj || 'Teléfono incorrecto');
                                }
                                return res;
                            }).catch(error => {
                                Swal.showValidationMessage(`Error: ${error.message || error}`);
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            esActivacion = true;
                            Swal.fire({
                                icon: 'success',
                                title: '¡Verificado!',
                                text: 'Establezca su contraseña para activar su cuenta.'
                            });
                            
                            $cedula.prop('readOnly', true);
                            $btnValidar.prop('disabled', true);
                            
                            $formRegistro.find('.input-box').each(function() {
                                const $box = $(this);
                                const $inp = $box.find('input, select');
                                const idInp = $inp.attr('id');
                                
                                if (idInp === 'reg_pass' || idInp === 'conf_pass' || $box.find('.g-recaptcha').length > 0) {
                                    $box.show();
                                    $inp.prop('disabled', false);
                                    if ($box.find('.g-recaptcha').length > 0 && typeof grecaptcha !== 'undefined') {
                                        grecaptcha.reset();
                                    }
                                } else if (idInp !== 'cedula') {
                                    $box.hide();
                                    $inp.prop('disabled', true).prop('required', false);
                                }
                            });
                            
                            $formRegistro.find('button[type="submit"]').prop('disabled', false);
                        }
                    });
                } else if (response.status === 'NO_ENCONTRADO') {
                    esActivacion = false;
                    Swal.fire({
                        icon: 'info',
                        title: 'Nuevo Registro',
                        text: 'Cédula no encontrada. Complete todos los datos del formulario.'
                    });
                    
                    $cedula.prop('readOnly', true);
                    $btnValidar.prop('disabled', true);
                    
                    $formRegistro.find('.input-box').show();
                    $todosInputs.find('input, select').prop('disabled', false);
                    if (typeof grecaptcha !== 'undefined') {
                        grecaptcha.reset();
                    }
                    $formRegistro.find('button[type="submit"]').prop('disabled', false);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.msj || 'Ocurrió un error inesperado.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de servidor',
                    text: 'No se pudo procesar la solicitud.'
                });
            }
        });
    });

    $("#formRegistro").on("submit", function (e) {
        e.preventDefault();
        
        let recaptchaResponse = $(this).find('[name="g-recaptcha-response"]').val() || "";

        if (recaptchaResponse.length === 0) {
            Swal.fire({
                title: "Verificación Requerida",
                text: "Por favor, marca la casilla del reCAPTCHA para registrarte.",
                icon: "warning",
                background: "#000910",
                color: "white",
                confirmButtonColor: "rgb(238, 191, 0)"
            });
            return false;
        }

        const formData = new FormData(this);
        formData.append('cedula', $cedula.val());
        formData.append('accion', esActivacion ? 'activarClave' : 'registrar');

        $.ajax({
            url: 'index.php?pagina=iniciarsesion',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: "¡Éxito!",
                        text: response.success,
                        icon: "success",
                        background: "#000910",
                        color: "white"
                    }).then(() => {
                        window.location.href = "index.php?pagina=iniciarsesion";
                    });
                } else {
                    const mensajeError = response.error || response.recaptcha || 'Ocurrió un error.';
                    Swal.fire({
                        title: 'Atención',
                        text: mensajeError,
                        icon: 'error',
                        background: "#000910",
                        color: "white"
                    });
                    if (typeof grecaptcha !== 'undefined') {
                        grecaptcha.reset();
                    }
                }
            },
            error: function(xhr) {
                console.error("Respuesta del servidor:", xhr.responseText);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo procesar el formulario.',
                    icon: 'error',
                    background: "#000910",
                    color: "white"
                });
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.reset();
                }
            }
        });
    });
});
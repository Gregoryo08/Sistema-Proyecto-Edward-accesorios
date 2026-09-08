$(document).ready(function () {
    let conteo = 1;

    validarCampos();

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

    function validarCampos() {
        const user = $('#login').val().trim();
        const pass = $('#Contraseña').val().trim();

        if (user !== "" && pass.length > 5) {
            $("#acceder").prop("disabled", false).css({ "opacity": "1", "cursor": "pointer" });
        } else {
            $("#acceder").prop("disabled", true).css({ "opacity": "0.5", "cursor": "not-allowed" });
        }
    }

    $('#login, #Contraseña').on('input', validarCampos);

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

        let datos = {
            "cedula": $(this).find('input[name="cedula"]').val(),
            "nombre": $(this).find('input[name="nombre"]').val(),
            "apellido": $(this).find('input[name="apellido"]').val(),
            "correo": $(this).find('input[name="correo"]').val(),
            "telefono": $(this).find('input[name="telefono"]').val(),
            "fecha_nacimiento": $(this).find('input[name="fecha_nacimiento"]').val(),
            "sexo": $(this).find('select[name="sexo"]').val(),
            "residencia": $(this).find('input[name="residencia"]').val(),
            "clave": $(this).find('input[name="clave"]').val(),
            "accion": "registrar",
            "g-recaptcha-response": recaptchaResponse
        };

        $.ajax({
            type: "POST",
            url: "index.php?pagina=iniciarsesion",
            data: datos,
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        title: "¡Éxito!",
                        text: res.success,
                        icon: "success",
                        background: "#000910",
                        color: "white"
                    }).then(() => {
                        window.location.href = "index.php?pagina=iniciarsesion";
                    });
                } else {
                    let errorTexto = res.recaptcha || res.error || "No se pudo completar el registro";

                    Swal.fire({
                        title: "Error",
                        text: errorTexto,
                        icon: "error",
                        background: "#000910",
                        color: "white"
                    });

                    if (typeof grecaptcha !== "undefined") {
                        grecaptcha.reset();
                    }
                }
            },
            error: function (xhr) {
                console.error("Respuesta del servidor:", xhr.responseText);
                Swal.fire({
                    title: "Error",
                    text: "Error de comunicación con el servidor.",
                    icon: "error",
                    background: "#000910",
                    color: "white"
                });

                if (typeof grecaptcha !== "undefined") {
                    grecaptcha.reset();
                }
            }
        });
    });
});
$(document).ready(function () {
    var conteo = 1;
    var urlParams = new URLSearchParams(window.location.search);
    var destino = urlParams.get('destino');

    validarCampos();

    $("#loginForm").on("submit", function (e) {
        e.preventDefault();

        var datos = {
            "usuario": $("#login").val(),
            "clave": $("#input_password").val(),
            "intento": conteo,
            "accion": "logearse",
            "destino": destino
        };

        $.ajax({
            type: "POST",
            url: "",
            data: datos,
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    mensajeBienvenido();
                    setTimeout(() => {
                        window.location.href = res.redirect || "?pagina=web_Catalogo";
                    }, 1500);
                } else {
                    let texto = res.message || "Credenciales incorrectas";
                    mensaje("error", texto);
                }
            },
            error: function () {
                mensaje("error", "Error de comunicación con el servidor.");
            }
        });
    });

    $("#formRegistro").on("submit", function (e) {
        e.preventDefault();

        var datos = {
            "cedula": $(this).find('input[name="cedula"]').val(),
            "nombre": $(this).find('input[name="nombre"]').val(),
            "apellido": $(this).find('input[name="apellido"]').val(),
            "correo": $(this).find('input[name="correo"]').val(),
            "telefono": $(this).find('input[name="telefono"]').val(),
            "direccion": $(this).find('input[name="direccion"]').val(),
            "fecha_nacimiento": $(this).find('input[name="fecha_nacimiento"]').val(),
            "sexo": $(this).find('select[name="sexo"]').val(),
            "clave": $(this).find('input[name="clave"]').val(),
            "accion": "registrar"
        };

        $.ajax({
            type: "POST",
            url: "?pagina=registroEcommerce&action=procesar",
            data: datos,
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        title: "¡Éxito!",
                        text: res.message || "Registro completado",
                        icon: "success",
                        background: "#000910",
                        color: "white"
                    }).then(() => {
                        window.location.href = res.redirect || "?pagina=web_Catalogo";
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: res.message || "No se pudo completar el registro",
                        icon: "error",
                        background: "#000910",
                        color: "white"
                    });
                }
            },
            error: function () {
                Swal.fire({
                    title: "Error",
                    text: "Error de comunicación con el servidor.",
                    icon: "error",
                    background: "#000910",
                    color: "white"
                });
            }
        });
    });

    $('#btnValidarCedula').on('click', function () {
        var cedula = $('#cedula').val();
        if (!cedula) {
            Swal.fire({
                title: "Atención",
                text: "Por favor, ingrese una cédula primero.",
                icon: "warning",
                background: "#000910",
                color: "white"
            });
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('Validando...');

        $.ajax({
            type: "POST",
            url: "",
            data: { "accion": "consultarCedula", "cedula": cedula },
            dataType: "json",
            success: function (res) {
                if (res.existe) {
                    Swal.fire({
                        title: "Cédula registrada",
                        text: "Esta cédula ya se encuentra en el sistema.",
                        icon: "info",
                        background: "#000910",
                        color: "white"
                    });
                    $btn.prop('disabled', false).text('Validar');
                } else {
                    Swal.fire({
                        title: "Disponible",
                        text: "La cédula está disponible para el registro.",
                        icon: "success",
                        background: "#000910",
                        color: "white"
                    });
                    $btn.text('Validado');
                }
            },
            error: function () {
                Swal.fire({
                    title: "Error",
                    text: "Error de comunicación con el servidor.",
                    icon: "error",
                    background: "#000910",
                    color: "white"
                });
                $btn.prop('disabled', false).text('Validar');
            }
        });
    });

    function validarCampos() {
        const user = $('#login').val() ? $('#login').val().trim() : "";
        const pass = $('#input_password').val() ? $('#input_password').val().trim() : "";
        if (user !== "" && pass.length > 0) {
            $("#acceder").prop("disabled", false).css({ "opacity": "1", "cursor": "pointer" });
        } else {
            $("#acceder").prop("disabled", true).css({ "opacity": "0.5", "cursor": "not-allowed" });
        }
    }

    $('#login, #input_password').on('input', validarCampos);

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
            confirmButtonColor: "#2C74B3"
        });
    }
});
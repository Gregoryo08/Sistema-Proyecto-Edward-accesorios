$(document).ready(function () {
    var conteo = 1;

    validarCampos();

    $("#loginForm").on("submit", function (e) {
        e.preventDefault();

        var datos = {
            "usuario": $("#login").val(),
            "clave": $("#Contraseña").val(),
            "intento": conteo,
            "accion": "logearse"
        };

        $.ajax({
            type: "POST",
            url: "index.php",
            data: datos,
            dataType: "json",
            success: function (res) {
                if (res.data) {
                    mensajeBienvenido();
                    setTimeout(() => {
                        window.location.href = res.data;
                    }, 1500);
                } else {
                    let texto = res.notFound || res.password || res.disabled || res.incorrect || "Credenciales incorrectas";
                    let tipo = res.notFound ? "notFound" : "error";
                    
                    if (res.new_intento) {
                        conteo = res.new_intento;
                    }

                    mensaje(tipo, texto);
                }
            },
            error: function (xhr) {
                console.error("Respuesta del servidor:", xhr.responseText);
                mensaje("error", "Error de comunicación con el servidor. Revise la consola.");
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
});
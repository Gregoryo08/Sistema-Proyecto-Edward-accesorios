$(document).ready(function () {

    $("#especialidad").on("input", function () {
        let entrada = $(this).val();

        let soloLetras = entrada.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, "");

        const capitalizado = soloLetras.replace(/\b\w+/g, function (palabra) {
            return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
        });

        $(this).val(capitalizado); 

        const mensaje = $("#mensajeEspecialidad");
        if (capitalizado.length >= 4) {
            $("#registro").show();
            $(this).removeClass("is-invalid").addClass("is-valid");
            mensaje.text("").hide();
        } else {
            $("#registro").show();
            $(this).removeClass("is-valid").addClass("is-invalid");
            if (capitalizado.length > 0) {
                mensaje.text("El nombre debe tener al menos 4 letras.").show();
            } else {
                mensaje.text("El nombre de la especialidad es obligatorio.").show();
            }
        }
    });

    $("#especialidad_modificar").on("input", function () {
        let entrada = $(this).val();

        let soloLetras = entrada.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, "");

        const capitalizado = soloLetras.replace(/\b\w+/g, function (palabra) {
            return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
        });

        $(this).val(capitalizado);

        const mensajeMod = $("#mensajeEspecialidadModificar");
        if (capitalizado.length >= 4) {
            $("#modificar").show();
            $(this).removeClass("is-invalid").addClass("is-valid");
            mensajeMod.text("").hide();
        } else {
            $("#modificar").show();
            $(this).removeClass("is-valid").addClass("is-invalid");
            if (capitalizado.length > 0) {
                mensajeMod.text("El nombre debe tener al menos 4 letras.").show();
            } else {
                mensajeMod.text("El nombre de la especialidad es obligatorio.").show();
            }
        }
    });

    $("#especialidad, #especialidad_modificar").on("keypress", function (e) {
        if (/[0-9]/.test(String.fromCharCode(e.which))) {
            e.preventDefault();
            const mensaje = this.id === "especialidad"
                ? $("#mensajeEspecialidad")
                : $("#mensajeEspecialidadModificar");
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

});
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
            mensaje.text("").hide();
        } else {
            $("#registro").hide();
            if (capitalizado.length > 0) {
                mensaje.text("El nombre debe tener al menos 4 letras.").show();
            } else {
                mensaje.hide();
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
            mensajeMod.text("").hide();
        } else {
            $("#modificar").hide();
            if (capitalizado.length > 0) {
                mensajeMod.text("El nombre debe tener al menos 4 letras.").show();
            } else {
                mensajeMod.hide();
            }
        }
    });

});
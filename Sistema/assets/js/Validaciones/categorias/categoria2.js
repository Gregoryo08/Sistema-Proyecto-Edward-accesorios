$(document).ready(function () {

    $("#categoria").on("input", function () {
        let entrada = $(this).val();

        let soloLetras = entrada.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, "");

        const capitalizado = soloLetras.replace(/\b\w+/g, function (palabra) {
            return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
        });

        $(this).val(capitalizado);

        const mensaje = $("#mensajeCategoria");
        const valorTrim = capitalizado.trim();

       
        if (valorTrim.length >= 4) {
            $("#registro").fadeIn(200); 
            mensaje.text("").hide();
        } else {
            $("#registro").hide(); 
            
            if (valorTrim.length > 0) {
                mensaje.text("El nombre debe tener al menos 4 letras.").show();
            } else {
                mensaje.hide(); 
            }
        }
    });

  
    $("#categoria_modificar").on("input", function () {
        let entrada = $(this).val();

        let soloLetras = entrada.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, "");

        const capitalizado = soloLetras.replace(/\b\w+/g, function (palabra) {
            return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
        });

        $(this).val(capitalizado);

        const mensaje = $("#mensajeCategoriaModificar");
        const valorTrim = capitalizado.trim();

        if (valorTrim.length >= 4) {
            $("#modificar").fadeIn(200);
            mensaje.text("").hide();
        } else {
            $("#modificar").hide();
            
            if (valorTrim.length > 0) {
                mensaje.text("El nombre debe tener al menos 4 letras.").show();
            } else {
                mensaje.hide();
            }
        }
    });

    
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $("#registro, #modificar").hide();
        $("#mensajeCategoria, #mensajeCategoriaModificar").hide();
    });

});
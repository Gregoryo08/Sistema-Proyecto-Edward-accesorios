$(document).ready(function () {

    function aplicarEstilo(elemento, esValido) {
        if (esValido) {
            $(elemento)
                .css("border", "1px solid rgb(14, 184, 37)")
                .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
        } else {
            $(elemento)
                .css("border", "1px solid rgb(158, 3, 3)")
                .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
        }
    }

    $("#modelo, #modelo_modificar").keypress(function (event) {
        if (this.value.length >= 15) {
            return false;
        }
    });

    $("#almacenamiento, #ram, #almacenamiento_modificar, #ram_modificar").keypress(function (event) {
        var codigo = event.which || event.keyCode;
        var letra = String.fromCharCode(codigo).toUpperCase();
        var letrasPermitidas = ["G", "B", "T"];

        if (this.value.length >= 5) {
            return false;
        }

        if (!((codigo >= 48 && codigo <= 57) || letrasPermitidas.includes(letra))) {
            return false;
        }
    });

    $("#almacenamiento, #ram, #almacenamiento_modificar, #ram_modificar").on("input", function () {
        var regex = /[^0-9GBTgbt]/g;
        var valor = $(this).val();

        if (regex.test(valor)) {
            $(this).val(valor.replace(regex, ""));
            valor = $(this).val();
        }

        if (valor.length > 5) {
            $(this).val(valor.slice(0, 5));
            valor = $(this).val();
        }

        if (valor.length === 0) {
            $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");
        } else {
            var regexFormato = /^[0-9]+[GBTgbt]{1,2}$/;
            aplicarEstilo(this, regexFormato.test(valor));
        }
    });

    $("#modelo, #modelo_modificar").on("input", function () {
        var valor = $(this).val();
        if (valor.length === 0) {
            $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");
        } else {
            aplicarEstilo(this, valor.length >= 2 && valor.length <= 10);
        }
    });

    $("#marca, #marca_modificar").change(function () {
        aplicarEstilo(this, $(this).val() != "");
    });

    $("#imei, #imei_modificar").keypress(function (event) {
        var codigo = event.which || event.keyCode;
        if (codigo < 48 || codigo > 57 || this.value.length >= 15) {
            return false;
        }
    });

    $("#imei, #imei_modificar").on("input", function () {
        var valor = $(this).val();
        if (valor.length === 0) {
            $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");
            $("#mensajeModulo").css("display", "none").text("");
            return;
        }

        if (valor.length < 14) {
            if ($(this).attr("id") === "imei") {
                $("#mensajeModulo").css("display", "block").text("Mínimo 14 dígitos");
            }
            aplicarEstilo(this, false);
        } else {
            $("#mensajeModulo").css("display", "none").text("");
            aplicarEstilo(this, true);
        }
    });
});
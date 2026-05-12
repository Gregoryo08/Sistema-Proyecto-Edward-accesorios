$(document).ready(function () {
    function capitalizarPalabras(cadena) {
        if (!cadena) return "";
        var resultado = cadena.charAt(0).toUpperCase() + cadena.slice(1).toLowerCase();
        resultado = resultado.replace(/(-\w)/g, function (match) {
            return match.toUpperCase();
        });
        return resultado;
    }

    function soloNumeros(input, permiteDecimal = false) {
        var valor = input.val();
        var regex = permiteDecimal ? /[^0-9.]/g : /[^0-9]/g;
        if (regex.test(valor)) {
            input.val(valor.replace(regex, ""));
        }
    }

    $("#nombre, #nombreModificar").keyup(function () {
        var isModificar = $(this).attr("id") === "nombreModificar";
        var btn = isModificar ? $("#btnModificarProducto") : $("#btnRegistrarProducto");
        var msg = isModificar ? $("#texto_mensaje_nombre_modificar") : $("#texto_mensaje_nombre");
        
        var valor = $(this).val();
        var valorCap = capitalizarPalabras(valor);

        msg.hide().text("");
        $(this).css({"border": "1px solid #ced4da", "box-shadow": "none"});

        if (valor !== valorCap) {
            var pos = $(this).prop("selectionStart");
            $(this).val(valorCap);
            $(this).prop("selectionStart", pos).prop("selectionEnd", pos);
        }

        if (valor.length == 0) {
            btn.hide();
            return;
        }

        if (valorCap.length <= 2) {
            msg.show().text("¡Mínimo 3 caracteres!");
            $(this).css({"border": "1px solid rgb(158, 3, 3)", "box-shadow": "0 0 10px rgb(158, 3, 3)"});
            btn.hide();
        } else {
            $(this).css({"border": "1px solid rgb(14, 184, 37)", "box-shadow": "0 0 10px rgb(14, 184, 37)"});
            btn.show();
        }
    });

    $("#stock_minimo, #stock_maximo, #stock_minimoModificar, #stock_maximoModificar").on("input", function () {
        soloNumeros($(this), false);
    });

    $("#precio, #precioModificar").on("input", function () {
        soloNumeros($(this), true);
        var valor = $(this).val();
        if ((valor.match(/\./g) || []).length > 1) {
            $(this).val(valor.replace(/\.+$/, ""));
        }
    });

    function campos_llenos() {
        return $("#nombre").val().length > 2 && $("#id_categoria").val() !== "" && $("#precio").val() !== "";
    }
});
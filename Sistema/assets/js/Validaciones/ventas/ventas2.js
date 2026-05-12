$(document).ready(function () {
  function capitalizarPalabras(cadena) {
    if (!cadena) return "";

    var resultado =
      cadena.charAt(0).toUpperCase() + cadena.slice(1).toLowerCase();

    resultado = resultado.replace(/(-\w)/g, function (match) {
      return match.toUpperCase();
    });

    return resultado;
  }

  function numerico(input) {
    var valorOriginal = input.val();
    var valorLimpio = "";
    var contieneLetras = false;

    for (var i = 0; i < valorOriginal.length; i++) {
      var char = valorOriginal[i];

      if (/[a-zA-ZñÑáÁeÉiÍoÓuUúÚüÜ]/.test(char)) {
        valorLimpio += "";
        contieneLetras = true;
      } else if (/[0-9.]/.test(char)) {
        valorLimpio += char;
      } else {
        valorLimpio += ".";
      }
    }

    if (contieneLetras) {
      var cursorPosicion = input.prop("selectionStart");
      input.val(valorLimpio);
      input.prop("selectionStart", cursorPosicion);
      input.prop("selectionEnd", cursorPosicion);

      return false;
    } else {
      if (valorOriginal !== valorLimpio) {
        var cursorPosicion = input.prop("selectionStart");
        input.val(valorLimpio);
        input.prop("selectionStart", cursorPosicion);
        input.prop("selectionEnd", cursorPosicion);
      }

      return true;
    }
  }

  $("#nombre").keyup(function () {
    var expresion = /^[a-zA-ZñÑáÁeÉiÍoÓuUúÚüÜ-\s]*$/;
    var valor = $(this).val();
    var valorCapitalizado = capitalizarPalabras(valor);

    $("#texto_mensaje_nombre").css("display", "none").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor !== valorCapitalizado) {
      var cursorPosicion = $(this).prop("selectionStart");
      $(this).val(valorCapitalizado);
      $(this).prop("selectionStart", cursorPosicion);
      $(this).prop("selectionEnd", cursorPosicion);
    }

    if (valor.length == 0) {
      $("#btnRegistrarMarca").css("display", "none");
      return;
    } else {
      $(this).css("border", "1px solid rgb(14, 184, 37)").css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (valorCapitalizado.length <= 2) {
      $("#texto_mensaje_nombre").css("display", "block").text(
        "El nombre tiene que tener mas de 2 caracteres!"
      );
      $(this).css("border", "1px solid rgb(158, 3, 3)").css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      $("#btnRegistrarMarca").css("display", "none");
      return;
    }

    if (!expresion.test(valorCapitalizado)) {
      $("#texto_mensaje_nombre").css("display", "block").text("Este campo solo puede aceptar letras!");
      $(this).css("border", "1px solid rgb(158, 3, 3)").css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      $("#btnRegistrarMarca").css("display", "none");
      return;
    }

    $("#btnRegistrarMarca").css("display", "block");
  });

  $("#nombreModificar").keyup(function () {
    var expresion = /^[a-zA-ZñÑáÁeÉiÍoÓuUúÚüÜ-\s]*$/;
    var valor = $(this).val();
    var valorCapitalizado = capitalizarPalabras(valor);

    $("#texto_mensaje_nombre_modificar").css("display", "none").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor !== valorCapitalizado) {
      var cursorPosicion = $(this).prop("selectionStart");
      $(this).val(valorCapitalizado);
      $(this).prop("selectionStart", cursorPosicion);
      $(this).prop("selectionEnd", cursorPosicion);
    }

    if (valor.length == 0) {
      $("#btnModificarMarca").css("display", "none");
      return;
    }

    if (valorCapitalizado.length <= 2) {
      $("#texto_mensaje_nombre_modificar").css("display", "block").text(
        "El nombre tiene que tener mas de 2 caracteres!"
      );
      $(this).css("border", "1px solid rgb(158, 3, 3)").css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      $("#btnModificarMarca").css("display", "none");
      return;
    }

    if (!expresion.test(valorCapitalizado)) {
      $("#texto_mensaje_nombre_modificar").css("display", "block").text("Este campo solo puede aceptar letras!");
      $(this).css("border", "1px solid rgb(158, 3, 3)").css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      $("#btnModificarMarca").css("display", "none");
      return;
    }

    $("#btnModificarMarca").css("display", "block");
  });

  function campos_llenos() {
    return $("#nombre").val().length > 0;
  }

  function campos_llenos_modificar() {
    return $("#nombreModificar").val().length > 0;
  }
});
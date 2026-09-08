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
    $(this).removeClass("is-valid is-invalid");

    if (valor !== valorCapitalizado) {
      var cursorPosicion = $(this).prop("selectionStart");
      $(this).val(valorCapitalizado);
      $(this).prop("selectionStart", cursorPosicion);
      $(this).prop("selectionEnd", cursorPosicion);
    }

    if (valor.length == 0) {
      $(this).addClass("is-invalid");
      $("#texto_mensaje_nombre").css("display", "block").text("El nombre de la marca es obligatorio.");
      $("#btnRegistrarMarca").css("display", "block");
      return;
    } else {
      $(this).addClass("is-valid");
    }

    if (valorCapitalizado.length <= 2) {
      $("#texto_mensaje_nombre").css("display", "block").text(
        "El nombre tiene que tener mas de 2 caracteres!"
      );
      $(this).addClass("is-invalid");
      $("#btnRegistrarMarca").css("display", "none");
      return;
    }

    if (!expresion.test(valorCapitalizado)) {
      $("#texto_mensaje_nombre").css("display", "block").text("Este campo solo puede aceptar letras!");
      $(this).addClass("is-invalid");
      $("#btnRegistrarMarca").css("display", "none");
      return;
    }

    $(this).addClass("is-valid");
    $("#btnRegistrarMarca").css("display", "block");
  });

  $("#nombreModificar").keyup(function () {
    var expresion = /^[a-zA-ZñÑáÁeÉiÍoÓuUúÚüÜ-\s]*$/;
    var valor = $(this).val();
    var valorCapitalizado = capitalizarPalabras(valor);

    $("#texto_mensaje_nombre_modificar").css("display", "none").text("");
      $(this).removeClass("is-valid is-invalid");

    if (valor !== valorCapitalizado) {
      var cursorPosicion = $(this).prop("selectionStart");
      $(this).val(valorCapitalizado);
      $(this).prop("selectionStart", cursorPosicion);
      $(this).prop("selectionEnd", cursorPosicion);
    }

    if (valor.length == 0) {
      $(this).addClass("is-invalid");
      $("#texto_mensaje_nombre_modificar").css("display", "block").text("El nombre de la marca es obligatorio.");
      $("#btnModificarMarca").css("display", "block");
      return;
    }

    if (valorCapitalizado.length <= 2) {
      $("#texto_mensaje_nombre_modificar").css("display", "block").text(
        "El nombre tiene que tener mas de 2 caracteres!"
      );
        $(this).addClass("is-invalid");
      $("#btnModificarMarca").css("display", "none");
      return;
    }

    if (!expresion.test(valorCapitalizado)) {
      $("#texto_mensaje_nombre_modificar").css("display", "block").text("Este campo solo puede aceptar letras!");
        $(this).addClass("is-invalid");
      $("#btnModificarMarca").css("display", "none");
      return;
    }

    $(this).addClass("is-valid");
    $("#btnModificarMarca").css("display", "block");
  });

  $("#nombre, #nombreModificar").on("keypress", function (e) {
    if (/[0-9]/.test(String.fromCharCode(e.which))) {
      e.preventDefault();
      const feedbackId = this.id === "nombre"
        ? "#texto_mensaje_nombre"
        : "#texto_mensaje_nombre_modificar";
      $(this).removeClass("is-valid").addClass("is-invalid");
      $(feedbackId).text("Este campo solo puede aceptar letras!").show();
      Swal.fire({
        title: "Campo inválido",
        text: "Este campo solo puede aceptar letras!",
        icon: "warning",
        color: "white",
        background: "#000910"
      });
    }
  });

  function campos_llenos() {
    return $("#nombre").val().length > 0;
  }

  function campos_llenos_modificar() {
    return $("#nombreModificar").val().length > 0;
  }
});
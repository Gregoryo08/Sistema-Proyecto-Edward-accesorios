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

      if (!/[0-9]/.test(char)) {
        valorLimpio += "";
        contieneLetras = true;
      } else {
        valorLimpio += char;
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

  // -------------------------------------------------------------

  $("#nombre").keyup(function () {
    var expresion = /^[a-zA-ZñÑáÁeÉiÍoÓuUúÚüÜ-\s]*$/;
    var valor = $(this).val();
    var valorCapitalizado = capitalizarPalabras(valor);

    $("#texto_mensaje_nombre").css("display", "none");
    $("#texto_mensaje_nombre").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor !== valorCapitalizado) {
      var cursorPosicion = $(this).prop("selectionStart");

      $(this).val(valorCapitalizado);
      $(this).prop("selectionStart", cursorPosicion);
      $(this).prop("selectionEnd", cursorPosicion);
    }

    if (valor.length == 0) {
      $("#texto_mensaje_nombre").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (valorCapitalizado.length <= 3) {
      $("#texto_mensaje_nombre")
        .css("display", "block")
        .text("El nombre tiene que tener mas de 3 caracteres!");

        $("#registrar").css("display", "none");
      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (!expresion.test(valorCapitalizado)) {
      $("#texto_mensaje_nombre").css("display", "block");
      $("#texto_mensaje_nombre").text("Este campo solo puede aceptar letras!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#registrar").css("display", "none");
      return;
    }

    if (!campos_llenos()) {
      $("#registrar").css("display", "none");
    } else {
      $("#registrar").css("display", "block");
    }
  });

  // -------------------------------------------------------------

  $("#numero").keyup(function () {
    var valor = $("#numero").val();
    var esValido = numerico($(this));

    $("#texto_mensaje_numero").css("display", "none");
    $("#texto_mensaje_numero").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_numero").css("display", "none");
      $("#texto_mensaje_numero").text("");

      $("#registrar").css("display", "none");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (valor.length < 8) {
      $("#texto_mensaje_numero")
        .css("display", "block")
        .text("El valor ingresado debe de ser mayor a 8 digitos!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#registrar").css("display", "none");
      return;
    }

    if (esValido) {
      $("#texto_mensaje_numero").css("display", "none").text("");
    } else {
      $("#texto_mensaje_numero")
        .css("display", "block")
        .text("Este campo solo acepta numeros!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#registrar").css("display", "none");
      return;
    }

    if (!campos_llenos()) {
      $("#registrar").css("display", "none");
    } else {
      $("#registrar").css("display", "block");
    }
  });

  // -------------------------------------------------------------

  $("#cedula").keyup(function () {
    var valor = $("#cedula").val();
    var esValido = numerico($("#cedula"));

    $("#texto_mensaje_cedula").css("display", "none");
    $("#texto_mensaje_cedula").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_cedula").css("display", "none");
      $("#texto_mensaje_cedula").text("");

      $("#registrar").css("display", "none");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (esValido) {
      $("#texto_mensaje_cedula").css("display", "none").text("");
    } else {
      $("#texto_mensaje_cedula")
        .css("display", "block")
        .text("Este campo solo acepta numeros!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#registrar").css("display", "none");
    }

    if (valor.length < 6) {
      $("#texto_mensaje_cedula")
        .css("display", "block")
        .text("El valor ingresado debe de tener entre 6 y 9 numeros!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#registrar").css("display", "none");
      return 
    }

    if ($("#nombre").val().length > 0) {
      $.ajax({
        type: "POST",
        url: "",
        data: {
          cedula: valor,
          banco: $("#nombre").val(),
          accion: "validar",
        },
        success: function (response) {
          var res = JSON.parse(response);

          if (res > 0) {
            $("#texto_mensaje_cedula")
              .css("display", "block")
              .html("Ya existe una cuenta en el banco '" + $("#nombre").val() +"'!");
            $("#texto_mensaje_cedula");

            $("#cedula")
              .css("border", "1px solid rgb(158, 3, 3)")
              .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

            $("#registrar").css("display", "none");
            return;
          }
        },
        error: function (xhr, status, error) {
          mensaje("error", "Registrado");
        },
      });

    } else {
      $("#texto_mensaje_cedula")
        .css("display", "block")
        .text("Debes de escribir el nombre del banco!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#registrar").css("display", "none");
    }

    if (!campos_llenos()) {
      $("#registrar").css("display", "none");
    } else {
      $("#registrar").css("display", "block");
    }
  });

  // -------------------------------------------------------------

  $("#telefono").keyup(function () {
    var valor = $("#telefono").val();
    var esValido = numerico($(this));

    $("#texto_mensaje_telefono").css("display", "none");
    $("#texto_mensaje_telefono").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_telefono").css("display", "none");
      $("#texto_mensaje_telefono").text("");

      $("#registrar").css("display", "none");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (esValido) {
      $("#texto_mensaje_telefono").css("display", "none").text("");
    } else {
      $("#texto_mensaje_telefono")
        .css("display", "block")
        .text("Este campo solo acepta numeros!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#registrar").css("display", "none");
    }

    if (!campos_llenos()) {
      $("#registrar").css("display", "none");
    } else {
      $("#registrar").css("display", "block");
    }
  });

  // -------------------------------------------------------------

  $("#nombre_modificar").keyup(function () {
    var expresion = /^[a-zA-ZñÑáÁeÉiÍoÓuUúÚüÜ-\s]*$/;
    var valor = $(this).val();
    var valorCapitalizado = capitalizarPalabras(valor);

    $("#texto_mensaje_nombre_modificar").css("display", "none");
    $("#texto_mensaje_nombre_modificar").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor !== valorCapitalizado) {
      var cursorPosicion = $(this).prop("selectionStart");

      $(this).val(valorCapitalizado);
      $(this).prop("selectionStart", cursorPosicion);
      $(this).prop("selectionEnd", cursorPosicion);
    }

    if (valor.length == 0) {
      $("#texto_mensaje_nombre_modificar").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (valorCapitalizado.length <= 3) {
      $("#texto_mensaje_nombre_modificar")
        .css("display", "block")
        .text("El nombre tiene que tener mas de 3 caracteres!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (!expresion.test(valorCapitalizado)) {
      $("#texto_mensaje_nombre_modificar").css("display", "block");
      $("#texto_mensaje_nombre_modificar").text("Este campo solo puede aceptar letras!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#modificar").css("display", "none");
      return;
    }

    if (!campos_llenos_modificar()) {
      $("#modificar").css("display", "none");
    } else {
      $("#modificar").css("display", "block");
    }
  });

  // -------------------------------------------------------------

  $("#numero_modificar").keyup(function () {
    var valor = $("#numero_modificar").val();
    var esValido = numerico($(this));

    $("#texto_mensaje_numero_modificar").css("display", "none");
    $("#texto_mensaje_numero_modificar").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_numero_modificar").css("display", "none");
      $("#texto_mensaje_numero_modificar").text("");

      $("#modificar").css("display", "none");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (valor.length < 8) {
      $("#texto_mensaje_numero_modificar")
        .css("display", "block")
        .text("El valor ingresado debe de ser mayor a 8 digitos!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#modificar").css("display", "none");
      return;
    }

    if (esValido) {
      $("#texto_mensaje_numero_modificar").css("display", "none").text("");
    } else {
      $("#texto_mensaje_numero_modificar")
        .css("display", "block")
        .text("Este campo solo acepta numeros!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#modificar").css("display", "none");
      return;
    }

    if (!campos_llenos_modificar()) {
      $("#modificar").css("display", "none");
    } else {
      $("#modificar").css("display", "block");
    }
  });

  // -------------------------------------------------------------

  var copia_cedula = 0
  $("#cedula_modificar").keyup(function () {
    var valor = $("#cedula_modificar").val();
    copia_cedula = valor
    var esValido = numerico($("#cedula_modificar"));

    $("#texto_mensaje_cedula_modificar").css("display", "none");
    $("#texto_mensaje_cedula_modificar").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_cedula_modificar").css("display", "none");
      $("#texto_mensaje_cedula_modificar").text("");

      $("#modificar").css("display", "none");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (esValido) {
      $("#texto_mensaje_cedula_modificar").css("display", "none").text("");
    } else {
      $("#texto_mensaje_cedula_modificar")
        .css("display", "block")
        .text("Este campo solo acepta numeros!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#modificar").css("display", "none");
    }

    if (valor.length < 6) {
      $("#texto_mensaje_cedula_modificar")
        .css("display", "block")
        .text("El valor ingresado debe de tener entre 6 y 9 numeros!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#modificar").css("display", "none");
      return 
    }

    if(copia_cedula !== valor){
      if ($("#nombre_modificar").val().length > 0) {
        $.ajax({
          type: "POST",
          url: "",
          data: {
            cedula: valor,
            banco: $("#nombre_modificar").val(),
            accion: "validar",
          },
          success: function (response) {
            var res = JSON.parse(response);
  
            if (res > 0) {
              $("#texto_mensaje_cedula_modificar")
                .css("display", "block")
                .html("Ya existe una cuenta en el banco '" + $("#nombre_modificar").val() +"'!");
              $("#texto_mensaje_cedula_modificar");
  
              $("#cedula_modificar")
                .css("border", "1px solid rgb(158, 3, 3)")
                .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
  
              $("#modificar").css("display", "none");
              return;
            }
          },
          error: function (xhr, status, error) {
            mensaje("error", "Registrado");
          },
        });
  
      } else {
        $("#texto_mensaje_cedula_modificar")
          .css("display", "block")
          .text("Debes de escribir el nombre del banco!");
  
        $(this)
          .css("border", "1px solid rgb(158, 3, 3)")
          .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
  
        $("#modificar").css("display", "none");
      }
    }

    if (!campos_llenos_modificar()) {
      $("#modificar").css("display", "none");
    } else {
      $("#modificar").css("display", "block");
    }
  });

  // -------------------------------------------------------------

  $("#telefono_modificar").keyup(function () {
    var valor = $("#telefono_modificar").val();
    var esValido = numerico($(this));

    $("#texto_mensaje_telefono_modificar").css("display", "none");
    $("#texto_mensaje_telefono_modificar").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_telefono_modificar").css("display", "none");
      $("#texto_mensaje_telefono_modificar").text("");

      $("#modificar").css("display", "none");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (esValido) {
      $("#texto_mensaje_telefono_modificar").css("display", "none").text("");
    } else {
      $("#texto_mensaje_telefono_modificar")
        .css("display", "block")
        .text("Este campo solo acepta numeros!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#modificar").css("display", "none");
    }

    if (!campos_llenos_modificar()) {
      $("#modificar").css("display", "none");
    } else {
      $("#modificar").css("display", "block");
    }
  });

  // -------------------------------------------------------------

  function campos_llenos() {
    if (
      $("#nombre").val() &&
      $("#numero").val() &&
      $("#cedula").val() &&
      $("#telefono").val()
    ) {
      return true;
    } else {
      return false;
    }
  }

  function campos_llenos_modificar() {
    if (
      $("#nombre_modificar").val() &&
      $("#numero_modificar").val() &&
      $("#cedula_modificar").val() &&
      $("#telefono_modificar").val()
    ) {
      return true;
    } else {
      return false;
    }
  }
});

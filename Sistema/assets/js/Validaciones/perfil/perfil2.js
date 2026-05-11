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

  // -------------------------------------------------------------

  $("#cedula").keypress(function (event) {
    if (event.which < 48 || event.which > 57 || this.value.length === 9) {
      return false;
    }
  });

  $("#cedula").keyup(function () {
    var valor = $(this).val();
    var prefijo = $("#prefijo").val();
    var cedula = prefijo + valor;
    var longitudCedula = valor.length;

    $("#texto_mensaje_cedula").css("display", "none").html("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");
    $("#prefijo").css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0 && $("#prefijo").val().length == 0) {
      $("#texto_mensaje_cedula").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if ($("#prefijo").val().length <= 0) {
      $("#texto_mensaje_cedula")
        .css("display", "block")
        .html("Debes de seleccionar el prefijo!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      $("#prefijo")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      return;
    }

    if (valor.length <= 6) {
      $("#texto_mensaje_cedula").css("display", "block");
      $("#texto_mensaje_cedula").text(
        "La cedula debe de tener entre 7 y 9 digitos!"
      );

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      $("#prefijo")
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
      return;
    }

    if ($("#prefijo").val().length == 0) {
      $("#texto_mensaje_cedula").css("display", "none").text("");

      return;
    } else {
      $("#prefijo")
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    $.ajax({
      type: "POST",
      url: "",
      data: {
        cedula: cedula,
        accion: "validarC",
      },
      success: function (response) {
        var res = JSON.parse(response);

        if (res.data > 0) {
          $("#texto_mensaje_cedula")
            .css("display", "block")
            .html("Esta cedula ya esta registrada!");
          $("#texto_mensaje_cedula");

          $("#cedula")
            .css("border", "1px solid rgb(158, 3, 3)")
            .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
          $("#prefijo")
            .css("border", "1px solid rgb(158, 3, 3)")
            .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

          return;
        } else if (res.error) {
          mensaje("error");
        }
      },
      error: function (xhr, status, error) {
        mensaje("error", "Registrado");
      },
    });

    if (validarDatos()) {
      $("#modificar").css("display", "block");
    } else {
      $("#modificar").css("display", "none");
    }
  });

  $("#prefijo").change(function () {
    var valor = $(this).val();
    var cedula = $("#cedula").val();
    var longValor = valor.length;
    var longCedula = cedula.length;

    $("#texto_mensaje_cedula").css("display", "none").html("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");
    $("#cedula").css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (longValor <= 0 && longCedula >= 1) {
      $("#texto_mensaje_cedula")
        .css("display", "block")
        .html("Debes de seleccionar el prefijo!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      $("#cedula")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (valor.length == 0) {
      $("#texto_mensaje_cedula").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (longValor > 0 && longCedula < 1) {
      $("#cedula")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (cedula.length <= 6) {
      $("#texto_mensaje_cedula").css("display", "block");
      $("#texto_mensaje_cedula").text(
        "La cedula debe de tener entre 7 y 9 digitos!"
      );

      $("#cedula")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (validarDatos()) {
      $("#modificar").css("display", "block");
    } else {
      $("#modificar").css("display", "none");
    }
  });

  // ---------------------------------------------------------------------

  $("#nombre").on("input", function () {
    var valor = $(this).val();
    var soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
    const capitalizado = capitalizarPalabras(valor);

    $("#texto_mensaje_nombre").css("display", "none");
    $("#texto_mensaje_nombre").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor !== capitalizado) {
      var cursorPosicion = $(this).prop("selectionStart");

      $(this).val(capitalizado);
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

    if (!soloLetras.test(capitalizado)) {
      $("#texto_mensaje_nombre").css("display", "block");
      $("#texto_mensaje_nombre").text("Este campo solo puede aceptar letras!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#modificar").css("display", "none");
      return;
    }

    if (capitalizado.length < 3) {
      $("#texto_mensaje_nombre").css("display", "block");
      $("#texto_mensaje_nombre").text(
        "El nombre tiene que tener mas de 3 caracteres!"
      );

      $("#modificar").css("display", "none");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    } else {
      $("#modificar").css("display", "block");
    }

    if (validarDatos()) {
      $("#modificar").css("display", "block");
    } else {
      $("#modificar").css("display", "none");
    }
  });

  // ---------------------------------------------------------------------

  $("#apellido").on("input", function () {
    var valor = $(this).val();
    var soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
    const capitalizado = capitalizarPalabras(valor);

    $("#texto_mensaje_apellido").css("display", "none");
    $("#texto_mensaje_apellido").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor !== capitalizado) {
      var cursorPosicion = $(this).prop("selectionStart");

      $(this).val(capitalizado);
      $(this).prop("selectionStart", cursorPosicion);
      $(this).prop("selectionEnd", cursorPosicion);
    }

    if (valor.length == 0) {
      $("#texto_mensaje_apellido").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (!soloLetras.test(capitalizado)) {
      $("#texto_mensaje_apellido").css("display", "block");
      $("#texto_mensaje_apellido").text(
        "Este campo solo puede aceptar letras!"
      );

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#modificar").css("display", "none");
      return;
    }

    if (capitalizado.length < 3) {
      $("#texto_mensaje_apellido").css("display", "block");
      $("#texto_mensaje_apellido").text(
        "El nombre tiene que tener mas de 3 caracteres!"
      );

      $("#modificar").css("display", "none");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    } else {
      $("#modificar").css("display", "block");
    }

    if (validarDatos()) {
      $("#modificar").css("display", "block");
    } else {
      $("#modificar").css("display", "none");
    }
  });

  // --------------------------------------------------------------------

  $("#cargo").change(function () {
    var valor = $(this).val();
    var expresion = /^[0-9]*$/;

    $("#texto_mensaje_cargo").css("display", "none");
    $("#texto_mensaje_cargo").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (!expresion.test(valor)) {
      $("#texto_mensaje_cargo").css("display", "block");
      $("#texto_mensaje_cargo").text("El valor no es valido!");

      $("#modificar").css("display", "none");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (validarDatos()) {
      $("#modificar").css("display", "block");
    } else {
      $("#modificar").css("display", "none");
    }
  });

  // ---------------------------------------------------------------------

  $("#correo").keyup(function () {
    var valor = $(this).val();
    var expresion = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    $("#texto_mensaje_correo").css("display", "none");
    $("#texto_mensaje_correo").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_correo").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (valor.length > 7) {
      if (!expresion.test(valor)) {
        $("#texto_mensaje_correo").css("display", "block");
        $("#texto_mensaje_correo").text(
          "El correo no es valido Ej. correo@gmail.com!"
        );

        $(this)
          .css("border", "1px solid rgb(158, 3, 3)")
          .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
        return;
      }
    }

    if (validarDatos()) {
      $("#modificar").css("display", "block");
    } else {
      $("#modificar").css("display", "none");
    }
  });

  // ---------------------------------------------------------------------

  $("#operadora").change(function () {
    var valor = $(this).val();
    var numero = $("#telefono").val();
    var expresion = /^[0-9]*$/;

    $("#texto_mensaje_telefono").css("display", "none");
    $("#texto_mensaje_telefono").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0 && numero.length == 0) {
      $("#texto_mensaje_telefono").css("display", "none").text("");
      $("#telefono")
        .css("border", "1px solid #ced4da")
        .css("box-shadow", "none");
    }

    if (valor.length == 0 && numero.length > 0) {
      $("#texto_mensaje_telefono").css("display", "block");
      $("#texto_mensaje_telefono").text("Debes de seleccionar la operadora!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#telefono")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (valor.length > 0 && numero.length >= 5 && numero.length <= 7) {
      $("#telefono")
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (valor.length > 0 && numero.length < 5) {
      $("#texto_mensaje_telefono").css("display", "block");
      $("#texto_mensaje_telefono").text(
        "El valor debe de estar entre 6 y 7 numeros!"
      );
      $("#modificar").css("display", "none");

      $("#telefono")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
    }

    if (!expresion.test(valor)) {
      $("#texto_mensaje_telefono").css("display", "block");
      $("#texto_mensaje_telefono").text(
        "El valor seleccionado no es numerico!"
      );

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (valor.length == 0) {
      $("#texto_mensaje_telefono").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (valor.length > 0 && numero.length == 0) {
      $("#telefono")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
    }
  });

  $("#telefono").keyup(function () {
    var valor = $(this).val();
    var operadora = $("#operadora").val();

    $("#texto_mensaje_telefono").css("display", "none");
    $("#texto_mensaje_telefono").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0 && operadora.length == 0) {
      $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");
      $("#operadora")
        .css("border", "1px solid #ced4da")
        .css("box-shadow", "none");

      return;
    }

    if (valor.length == 0 && operadora.length > 0) {
      $("#texto_mensaje_telefono").css("display", "none").text("");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
    }

    if (operadora.length == 0) {
      $("#texto_mensaje_telefono").css("display", "block");
      $("#texto_mensaje_telefono").text("Debes de seleccionar la operadora!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#operadora")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (valor.length == 0) {
      $("#texto_mensaje_telefono").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (!(valor.length >= 5 && valor.length <= 7)) {
      $("#texto_mensaje_telefono").css("display", "block");
      $("#texto_mensaje_telefono").text(
        "El valor debe de estar entre 6 y 7 numeros!"
      );
      $("#modificar").css("display", "none");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (validarDatos()) {
      $("#modificar").css("display", "block");
    } else {
      $("#modificar").css("display", "none");
    }
  });

  $("#telefono").keypress(function (event) {
    if (event.which < 48 || event.which > 57 || this.value.length === 7) {
      return false;
    }
  });

  // --------------------------------------------------------------------

  $("#direccion").keyup(function () {
    var valor = $(this).val();

    $("#texto_mensaje_direccion").css("display", "none");
    $("#texto_mensaje_direccion").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_direccion").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (valor.length < 10) {
      $("#texto_mensaje_direccion").css("display", "block");
      $("#texto_mensaje_direccion").text(
        "La direccion debe de tener por lo menos 10 caracteres!"
      );

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (validarDatos()) {
      $("#modificar").css("display", "block");
    } else {
      $("#modificar").css("display", "none");
    }
  });

  function validarDatos() {
    var cedula = $("#cedula").val();
    var nombre = $("#nombre").val();
    var apellido = $("#apellido").val();
    var correo = $("#correo").val();
    var telefono = $("#telefono").val();
    var operadora = $("#operadora").val();
    var direccion = $("#direccion").val();
    var cargo = $("#cargos").val();

    if (
      cedula &&
      nombre &&
      apellido &&
      correo &&
      telefono &&
      operadora &&
      direccion &&
      cargo
    ) {
      return true;
    } else {
      return false;
    }
  }
});

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

  $("#cargo").keyup(function () {
    var cargo = $(this).val();
    var soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
    const capitalizado = capitalizarPalabras(cargo);

    $("#texto_mensaje_nombre").css("display", "none");
    $("#texto_mensaje_nombre").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    $.ajax({
      type: "POST",
      url: "",
      data: {
        nombre: cargo,
        accion: "validarNombre",
      },
      success: function (response) {
        var res = response;

        if (Array.isArray(res)) {
          mensaje("error", "Ah Ocurrido un error en el Servidor!");
          $("#registro").css("display", "none");
        }

        if (res >= 1) {
          $("#texto_mensaje_nombre").css("display", "block");
          $("#texto_mensaje_nombre").text(
            "Este cargo ya se encuentra registrado!"
          );

          $("#cargo")
            .css("border", "1px solid rgb(158, 3, 3)")
            .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

          $("#registro").css("display", "none");
          return;
        }
      },
      error: function (xhr, status, error) {
        mensaje("error", "Ah Ocurrido un error en el Servidor!");
      },
    });

    if (cargo !== capitalizado) {
      var cursorPosicion = $(this).prop("selectionStart");

      $(this).val(capitalizado);
      $(this).prop("selectionStart", cursorPosicion);
      $(this).prop("selectionEnd", cursorPosicion);
    }

    if (cargo.length == 0) {
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

      $("#registro").css("display", "none");
      return;
    }

    if (capitalizado.length <= 3) {
      $("#texto_mensaje_nombre").css("display", "block");
      $("#texto_mensaje_nombre").text(
        "El nombre tiene que tener mas de 3 caracteres!"
      );

      $("#registro").css("display", "none");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    } else {
      $("#registro").css("display", "block");
    }
  });

  // ------------------------------------------------------------------

  window.cargo_Modificar = ""

  $("#cargo_modificar").keyup(function () {
    var cargo = $(this).val();
    var soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
    const capitalizado = capitalizarPalabras(cargo);

    $("#texto_mensaje_nombre_modificar").css("display", "none");
    $("#texto_mensaje_nombre_modificar").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if(window.cargo_Modificar != capitalizado){
      $.ajax({
        type: "POST",
        url: "",
        data: {
          nombre: cargo,
          accion: "validarNombre",
        },
        success: function (response) {
          var res = response;
  
          if (Array.isArray(res)) {
            mensaje("error", "Ah Ocurrido un error en el Servidor!");
            $("#modificar").css("display", "none");
          }
  
          if (res >= 1) {
            $("#texto_mensaje_nombre_modificar").css("display", "block");
            $("#texto_mensaje_nombre_modificar").text(
              "Este cargo ya se encuentra registrado!"
            );
  
            $("#cargo_modificar")
              .css("border", "1px solid rgb(158, 3, 3)")
              .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
  
            $("#modificar").css("display", "none");
            return;
          }
        },
        error: function (xhr, status, error) {
          mensaje("error", "Ah Ocurrido un error en el Servidor!");
        },
      });
    }

    if (cargo !== capitalizado) {
      var cursorPosicion = $(this).prop("selectionStart");

      $(this).val(capitalizado);
      $(this).prop("selectionStart", cursorPosicion);
      $(this).prop("selectionEnd", cursorPosicion);
    }

    if (cargo.length == 0) {
      $("#texto_mensaje_nombre_modificar").css("display", "none").text("");
      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (!soloLetras.test(capitalizado)) {
      $("#texto_mensaje_nombre_modificar").css("display", "block");
      $("#texto_mensaje_nombre_modificar").text("Este campo solo puede aceptar letras!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#modificar").css("display", "none");
      return;
    }

    if (capitalizado.length <= 3) {
      $("#texto_mensaje_nombre_modificar").css("display", "block");
      $("#texto_mensaje_nombre_modificar").text(
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
  });
});
$(document).ready(function () {
  $("#rol").keyup(function () {
    var valor = $(this).val();
    var soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;

    const capitalizado = valor.replace(/\b\w+/g, function (palabra) {
      return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
    });
    const datosParaEnviar = {
      nombre: capitalizado,
      accion: "validarRol",
    };

    $("#texto_mensaje_rol").css("display", "none");
    $("#texto_mensaje_rol").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_rol").css("display", "none").text("");

      if (validarDatos()) {
        $("#registro").css("display", "block");
      } else {
        $("#registro").css("display", "none");
      }

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (valor !== capitalizado) {
      var cursorPosicion = $(this).prop("selectionStart");

      $(this).val(capitalizado);
      $(this).prop("selectionStart", cursorPosicion);
      $(this).prop("selectionEnd", cursorPosicion);
    }

    if (capitalizado.length <= 4) {
      $("#texto_mensaje_rol").css("display", "block");
      $("#texto_mensaje_rol").text(
        "El nombre tiene que tener mas de 4 caracteres!"
      );

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#registro").css("display", "none");
      return;
    }

    if (!soloLetras.test(capitalizado)) {
      $("#texto_mensaje_rol").css("display", "block");
      $("#texto_mensaje_rol").text("Este campo solo puede aceptar letras!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#registro").css("display", "none");
      return;
    }

    $.ajax({
      type: "POST",
      url: "",
      contentType: "application/json",
      data: JSON.stringify(datosParaEnviar),
      dataType: "json",
      success: function (response) {
        var res = response;

        if (res.error) {
          mensaje("error", "Ah Ocurrido un error en el Servidor!");
          return;
        }

        if (res.conteo > 0) {
          $("#texto_mensaje_rol").css("display", "block");
          $("#texto_mensaje_rol").text(
            "Este rol ya esta registrado en el sistema!"
          );

          $("#rol")
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

    if (validarDatos()) {
      $("#registro").css("display", "block");
    } else {
      $("#registro").css("display", "none");
    }
  });

  $("#rol_modificar").keyup(function () {
    var rol = $(this).val();
    var soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;

    const capitalizado = rol.replace(/\b\w+/g, function (palabra) {
      return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
    });

    $(this).val(capitalizado);

    if (rol.length > 4 && soloLetras.test(rol)) {
      $("#modificar").css("display", "block");
    } else {
      $("#modificar").css("display", "none");
    }
  });

  $(document).on("click", 'input[type="checkbox"]', function () {
    if (validarDatos()) {
      $("#registro").css("display", "block");
    } else {
      $("#registro").css("display", "none");
    }
  });

  function validarDatosModificar() {
    var id_rol = $("#id_rol").val();
  
    const checkboxes = document.querySelectorAll(
      'input[name^="operaciones_modulo_modificar_"]:checked'
    );

    if (id_rol && checkboxes.length > 0) {
      return true;
    } else {
      return false;
    }
}

  function validarDatos() {
    var rol = $("#rol").val();
    const checkboxes = document.querySelectorAll(
      'input[name^="operaciones_modulo_"]:checked'
    );

    const operacionesSeleccionadas = Array.from(checkboxes).map((cb) => ({
      operacion: cb.dataset.operacion,
      valor_operacion: cb.value,
      id_modulo: getModuloIdFromName(cb.name),
    }));

    function getModuloIdFromName(name) {
      const match = name.match(/operaciones_modulo_(\d+)\[\]/);
      return match ? parseInt(match[1]) : null;
    }

    if (rol && operacionesSeleccionadas.length > 0) {
      return true;
    } else {
      return false;
    }
  }
});

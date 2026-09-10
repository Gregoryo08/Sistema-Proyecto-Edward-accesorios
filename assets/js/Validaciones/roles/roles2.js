$(document).ready(function () {
  $("#rol, #rol_modificar").on("keypress", function (e) {
    if (/[0-9]/.test(String.fromCharCode(e.which))) {
      e.preventDefault();
      const mensaje = this.id === "rol"
        ? $("#texto_mensaje_rol")
        : $("#texto_mensaje_rol_modificar");
      $(this).removeClass("is-valid").addClass("is-invalid");
      mensaje.text("Este campo solo puede aceptar letras!").show();
      Swal.fire({
        title: "Campo inválido",
        text: "Este campo solo puede aceptar letras!",
        icon: "warning",
        color: "white",
        background: "#000910"
      });
    }
  });

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

    if (valor.length === 0) {
      $("#texto_mensaje_rol").css("display", "none").text("");
      $(this).css({ border: '1px solid #ced4da', 'box-shadow': 'none' }).removeClass('is-invalid is-valid');
      $("#registro").css("display", validarDatos() ? "block" : "none");
      return;
    }

    if (valor !== capitalizado) {
      var cursorPosicion = $(this).prop("selectionStart");
      $(this).val(capitalizado);
      $(this).prop("selectionStart", cursorPosicion);
      $(this).prop("selectionEnd", cursorPosicion);
    }

    if (!soloLetras.test(capitalizado)) {
      $("#texto_mensaje_rol").css("display", "block").text("Este campo solo puede aceptar letras!");
      $(this).removeClass("is-valid").addClass("is-invalid");
      $("#registro").css("display", "none");
      return;
    }

    if (capitalizado.length <= 4) {
      $("#texto_mensaje_rol").css("display", "block").text("El nombre tiene que tener mas de 4 caracteres!");
      $(this).removeClass("is-valid").addClass("is-invalid");
      $("#registro").css("display", "none");
      return;
    }

    $(this).removeClass("is-invalid").addClass("is-valid");
    $(this).css({ border: '1px solid rgb(14, 184, 37)', 'box-shadow': '0 0 15px rgb(14, 184, 37)' });
    $("#texto_mensaje_rol").css("display", "none").text("");

    $.ajax({
      type: "POST",
      url: "",
      contentType: "application/json",
      data: JSON.stringify(datosParaEnviar),
      dataType: "json",
      success: function (response) {
        var res = response;

        if (res.error) {
          if (typeof mensaje === 'function') mensaje("error", "Ah Ocurrido un error en el Servidor!");
          return;
        }

        if (res.conteo > 0) {
          $("#texto_mensaje_rol").css("display", "block").text("Este rol ya esta registrado en el sistema!");
          $("#rol").removeClass("is-valid").addClass("is-invalid");
          $("#rol").css({ border: '1px solid rgb(158, 3, 3)', 'box-shadow': '0 0 15px rgb(158, 3, 3)' });
          $("#registro").css("display", "none");
          return;
        }
      },
      error: function () {
        if (typeof mensaje === 'function') mensaje("error", "Ah Ocurrido un error en el Servidor!");
      },
    });

    $("#registro").css("display", validarDatos() ? "block" : "none");
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
    $("#registro").css("display", validarDatos() ? "block" : "none");
  });

  function validarDatosModificar() {
    var id_rol = $("#id_rol").val();
    const checkboxes = document.querySelectorAll('input[name^="operaciones_modulo_modificar_"]:checked');
    return Boolean(id_rol && checkboxes.length > 0);
  }

  function validarDatos() {
    var rol = $("#rol").val();
    const checkboxes = document.querySelectorAll('input[name^="operaciones_modulo_"]:checked');
    return Boolean(rol && rol.trim().length > 4 && checkboxes.length > 0);
  }
});
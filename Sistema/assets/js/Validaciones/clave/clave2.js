$(document).ready(function () {
  $("#cedulaP").keypress(function (event) {
    if (event.which < 48 || event.which > 57 || this.value.length === 9) {
      return false;
    }
  });

  $("#cedulaP").keyup(function () {
    var cedula = $(this).val();

    if (!(cedula.length >= 8)) {
      $("#confirmarPregunta").css("display", "none");
    }

    if ($("#prefijoP").val().length <= 0) {
      $("#mensajeClaveP").css("display", "block");
      $("#mensajeClaveP").html("Formato de Cedula Invalido!");
      $("#mensajeClaveP").css("color", "#f2f2f2");
      $("#confirmarPregunta").css("display", "none");
    } else {
      $("#mensajeClaveP").css("display", "none");
      $("#mensajeClaveP").html("");
    }
  });

  $("#prefijoP").change(function () {
    if ($(this).val().length <= 0 && $("#cedula").val().length >= 1) {
      $("#mensajeClaveP").css("display", "block");
      $("#mensajeClaveP").html("Formato de Cedula Invalido!");
      $("#mensajeClaveP").css("color", "#f2f2f2");
      $("#confirmarPregunta").css("display", "none");
    } else {
      $("#mensajeClaveP").css("display", "none");
      validarCedula();
    }
  });

  $("#cedulaP").keyup(function () {
    validarCedula();
  });

  function validarCedula() {
    var prefijo = $("#prefijoP").val();
    var cedula = prefijo + $("#cedulaP").val();
    var longitudCedula = $("#cedulaP").val().length;

    if (longitudCedula >= 5) {
      var data = "cedula=" + cedula + "&accion=validarC";
      $.ajax({
        type: "POST",
        url: "",
        data: data,
        success: function (response) {
          var res = JSON.parse(response);
          if (res.data >= 1) {
            $("#mensajeClaveP").css("display", "none");
            $("#mensajeClaveP").html("");
            $("#cedulaP").css("border", "2px solid green");
            $("#cedulaP").css("box-shadow", "0 0 10px rgba(1, 209, 11, 0.5)");
            $("#prefijoP").css("border", "2px solid green");
            $("#prefijoP").css("box-shadow", "0 0 10px rgba(1, 209, 11, 0.5)");
          } else if (res.data <= 0) {
            $("#cedulaP").css("border", "2px solid red");
            $("#cedulaP").css("box-shadow", "0 0 10px rgba(209, 1, 1, 0.5)");
            $("#prefijoP").css("border", "2px solid red");
            $("#prefijoP").css("box-shadow", "0 0 10px rgba(209, 1, 1, 0.5)");
            $("#confirmarPregunta").css("display", "none");
            $("#mensajeClaveP").css("display", "block");
            $("#mensajeClaveP").html("Cedula Inesistente!");
          } else if(res.error){
            mensaje("error")
          }
        },
        error: function (xhr, status, error) {
          mensaje("error");
        },
      });
    }
  }

  $("#pregunta").keyup(function () {
    var cedula = $("#prefijoP").val() + $("#cedulaP").val();
    var pregunta = $("#pregunta").val();
    var numeroPregunta = $("#numPregunta").val()

    if (cedula.length >= 8 && pregunta.length != 0) {
      var data =
        "cedula=" + cedula + "&seguridad=" + pregunta + "&numero=" + numeroPregunta + "&accion=validarSeguridad";

      $.ajax({
        type: "POST",
        url: "",
        data: data,
        success: function (response) {
          var res = JSON.parse(response);
          if (res.data >= 1) {
            $("#mensajePregunta").css("display", "none");
            $("#mensajePregunta").html("");
            $("#pregunta").css("border", "2px solid green");
            $("#pregunta").css(
              "box-shadow",
              "0 0 10px rgba(1, 209, 11, 0.5)"
            );
            $("#confirmarPregunta").css("display", "block");
          } else if (res.data <= 0) {
            $("#pregunta").css("border", "2px solid red");
            $("#pregunta").css(
              "box-shadow",
              "0 0 10px rgba(209, 1, 1, 0.5)"
            );
            $("#confirmarPregunta").css("display", "none");
            $("#mensajePregunta").css("display", "block");
            $("#mensajePregunta").html("Respuesta Incorrecta!");
          } else if(res.error){
            mensaje("error")
          }
        },
        error: function (xhr, status, error) {
          mensaje("error");
        },
      });
    }
  });

  $("#confirmarPregunta").click(function () {
    var cedula = $("#prefijoP").val() + $("#cedulaP").val();
    var seguridad = $("#pregunta").val();
    var num = $("#numPregunta").val()

    if (cedula && seguridad && num) {
      mensaje("success");
      $("#modalClavePregunta").modal("hide");
      $("#modalCambioContraseña").modal("show");

      $("#id_cedula").val(cedula);
      $("#codigo_seguridad").val(seguridad);
      $("#numeroSeguridadT").val(num);
    } else {
      mensaje("errorC");
    }
  });

  function mensaje(accion,mensaje) {
    if (accion == "errorC") {
      Swal.fire({
        title: "Ups!",
        text: "Debes de completar todos los campos!",
        icon: "error",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
      });
    } else if (accion == "error") {
      Swal.fire({
        title: "Ups!",
        text: "Ah Ocurrido un error en el Servidor!",
        icon: "error",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
      });
    } else if (accion == "invalido") {
      Swal.fire({
        title: "Ups!",
        text: mensaje,
        icon: "error",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
      });
    } else if (accion == "success") {
      Swal.fire({
        title: "Datos Validados con Exito!",
        icon: "success",
        color: "white",
        showConfirmButton: false,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
        timer: 1500,
      });
    } else if (accion == "pregunta") {
      Swal.fire({
        title: "Estas Seguro!",
        text: "Seguro de los datos Introducidos?",
        icon: "question",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        confirmButtonBorder: "rgb(238, 191, 0)",
        background: "#000910",
        confirmButtonText: "Confirmar Eliminación",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          modificar();
        }
      });
    } else {
      Swal.fire({
        title: "Listo!",
        text: "Proceso Ejecutado con Exito!",
        icon: "success",
        color: "white",
        showConfirmButton: false,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
      });
    }
  }
});

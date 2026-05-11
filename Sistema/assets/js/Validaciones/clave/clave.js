$(document).ready(function () {

  $("#codigo").click(function () {
    $("#modalClaveCodigo").modal("show");
  });

  $("#cedula").keypress(function (event) {
    if (event.which < 48 || event.which > 57 || this.value.length === 9) {
      return false;
    }
  });

  $("#codigoSeguridad").keypress(function (event) {
    if (this.value.length === 6) {
      return false;
    }
  });

  $("#codigoSeguridad").keyup(function () {
    var expresion = /^.{6}$/;

    if (!expresion.test($(this).val())) {
      $("#confirmarCodigo").css("display", "none");
    } else {
      $("#confirmarCodigo").css("display", "block");
    }
  });

  $("#cedula").keyup(function () {
    var cedula = $(this).val();

    if (!(cedula.length >= 8)) {
      $("#confirmarCodigo").css("display", "none");
    }

    if ($("#prefijo").val().length <= 0) {
      $("#mensajeClave").css("display", "block");
      $("#mensajeClave").html("Formato de Cedula Invalido!");
      $("#mensajeClave").css("color", "#f2f2f2");
      $("#confirmarCodigo").css("display", "none");
    } else {
      $("#mensajeClave").css("display", "none");
      $("#mensajeClave").html("");
    }
  });

  $("#prefijo").change(function () {
    if ($(this).val().length <= 0 && $("#cedula").val().length >= 1) {
      $("#mensajeClave").css("display", "block");
      $("#mensajeClave").html("Formato de Cedula Invalido!");
      $("#mensajeClave").css("color", "#f2f2f2");
      $("#confirmarCodigo").css("display", "none");
    } else {
      $("#mensajeClave").css("display", "none");
      validarCedula();
    }
  });

  $("#cedula").keyup(function () {
    validarCedula();
  });

  function validarCedula() {
    var prefijo = $("#prefijo").val();
    var cedula = prefijo + $("#cedula").val();
    var longitudCedula = $("#cedula").val().length;

    if (longitudCedula >= 5) {
      var data = "cedula=" + cedula + "&accion=validarC";
      $.ajax({
        type: "POST",
        url: "",
        data: data,
        success: function (response) {
          var res = JSON.parse(response);
          if (res.data >= 1) {
            $("#mensajeClave").css("display", "none");
            $("#mensajeClave").html("");
            $("#cedula").css("border", "2px solid green");
            $("#cedula").css("box-shadow", "0 0 10px rgba(1, 209, 11, 0.5)");
            $("#prefijo").css("border", "2px solid green");
            $("#prefijo").css("box-shadow", "0 0 10px rgba(1, 209, 11, 0.5)");
          } else if (res.data <= 0) {
            $("#cedula").css("border", "2px solid red");
            $("#cedula").css("box-shadow", "0 0 10px rgba(209, 1, 1, 0.5)");
            $("#prefijo").css("border", "2px solid red");
            $("#prefijo").css("box-shadow", "0 0 10px rgba(209, 1, 1, 0.5)");
            $("#confirmarCodigo").css("display", "none");
            $("#mensajeClave").css("display", "block");
            $("#mensajeClave").html("Cedula Inesistente!");
          } else if (res.error) {
            mensaje("error");
          }
        },
        error: function (xhr, status, error) {
          mensaje("error");
        },
      });
    }
  }

  $("#codigoSeguridad").keyup(function () {
    var cedula = $("#prefijo").val() + $("#cedula").val();
    var seguridad = $("#codigoSeguridad").val();

    if (cedula.length >= 8 && seguridad.length >= 5) {
      var data =
        "cedula=" +
        cedula +
        "&seguridad=" +
        seguridad +
        "&numero=" +
        0 +
        "&accion=validarSeguridad";

      $.ajax({
        type: "POST",
        url: "",
        data: data,
        success: function (response) {
          console.log(response);
          var res = JSON.parse(response);

          if (res.data >= 1) {
            $("#mensajeCodigo").css("display", "none");
            $("#mensajeCodigo").html("");
            $("#codigoSeguridad").css("border", "2px solid green");
            $("#codigoSeguridad").css(
              "box-shadow",
              "0 0 10px rgba(1, 209, 11, 0.5)"
            );
            $("#confirmarCodigo").css("display", "block");
          } else if (res.data <= 0) {
            $("#codigoSeguridad").css("border", "2px solid red");
            $("#codigoSeguridad").css(
              "box-shadow",
              "0 0 10px rgba(209, 1, 1, 0.5)"
            );
            $("#confirmarCodigo").css("display", "none");
            $("#mensajeCodigo").css("display", "block");
            $("#mensajeCodigo").html("Codigo Incorrecto!");
          } else if (res.error) {
            mensaje("error");
          }
        },
        error: function (xhr, status, error) {
          mensaje("error");
        },
      });
    }
  });

  $("#confirmarCodigo").click(function () {
    var cedula = $("#prefijo").val() + $("#cedula").val();
    var codigo = $("#codigoSeguridad").val();
    var num = 0;

    if (cedula && codigo) {
      mensaje("success");
      $("#modalCambioContraseña").modal("show");
      $("#modalClaveCodigo").modal("hide");

      $("#id_cedula").val(cedula);
      $("#codigo_seguridad").val(codigo);
      $("#numeroSeguridadT").val(num);
    } else {
      mensaje("errorC");
    }
  });


  $("#clave").keyup(function () {
    var valor = $(this).val();
    var valor_confirmar = $("#claveConfirm").val()
    var regex = /^(?=.*[A-Z])(?=.*[^a-zA-Z0-9])[a-zA-Z0-9\W]+$/;
    var expresion_letras = /^(?=.*[a-zA-Z]).+$/
    var expresion_mayusculas = /^(?=.*[A-Z]).+$/
    var expresion_numeros = /^(?=.*[0-9]).+$/
    var expresion_especial = /^(?=.*[^a-zA-Z0-9\s]).+$/

    $("#texto_mensaje_clave_inicio").css("display", "none");
    $("#texto_mensaje_clave_inicio").text("");
    $(this).css("border","1px solid #ced4da").css("box-shadow","none")

    if (valor.length == 0) {
      $("#texto_mensaje_clave_inicio").css("display", "none").text("");

      return;
    }
    else{
      $(this).css("border","1px solid rgb(14, 184, 37)").css("box-shadow","0 0 15px rgb(14, 184, 37)")
    }

    if(valor.length < 5){
      $("#texto_mensaje_clave_inicio").css("display", "block").text("La contraseña debe de ser mayor a 5 caracteres!");

      $(this).css("border","1px solid rgb(158, 3, 3)").css("box-shadow","0 0 15px rgb(158, 3, 3)")
      return;
    }

    if(!(regex.test(valor))){
      var mensaje = ""

      if(!(expresion_letras.test(valor))){
        mensaje += '<i class="fa-regular fa-circle-xmark"></i> Debe de contener al menos una letra!<br>'
      }

      if(!(expresion_mayusculas.test(valor))){
        mensaje += '<i class="fa-regular fa-circle-xmark"></i> Debe de contener al menos una letra en mayusculas!<br>'
      }

      if(!(expresion_numeros.test(valor))){
        mensaje += '<i class="fa-regular fa-circle-xmark"></i> Debe de contener al menos un numero!<br>'
      }

      if(!(expresion_especial.test(valor))){
        mensaje += '<i class="fa-regular fa-circle-xmark"></i> Debe de contener al menos un caracter especial!<br>'
      }

      $("#texto_mensaje_clave_inicio").css("display", "block").css("clip-path", "none").css("text-align","left").css("color","white").css("font-weight","bold").html(mensaje);

      $(this).css("border","1px solid rgb(158, 3, 3)").css("box-shadow","0 0 15px rgb(158, 3, 3)")
      return;
    }

    if(valor.length > 0 && valor_confirmar.length > 0){
      $("#texto_mensaje_clave_confirmacion").css("display", "none").text("");
      $("#claveConfirm").css("border","1px solid #ced4da").css("box-shadow","none")

      if (valor_confirmar.length == 0) {
        $("#texto_mensaje_clave_confirmacion").css("display", "none").text("");
  
        return;
      }
      else{
        $("#claveConfirm").css("border","1px solid rgb(14, 184, 37)").css("box-shadow","0 0 15px rgb(14, 184, 37)")
      }

      if(valor_confirmar.length < 5){
        $("#texto_mensaje_clave_confirmacion").css("display", "block").text("La contraseña debe de ser mayor a 5 caracteres!");
  
        $("#claveConfirm").css("border","1px solid rgb(158, 3, 3)").css("box-shadow","0 0 15px rgb(158, 3, 3)")
        return;
      }

      if(!(regex.test(valor_confirmar))){
        var mensaje = ""
  
        if(!(expresion_letras.test(valor_confirmar))){
          mensaje += '<i class="fa-regular fa-circle-xmark"></i> Debe de contener al menos una letra!<br>'
        }
  
        if(!(expresion_mayusculas.test(valor_confirmar))){
          mensaje += '<i class="fa-regular fa-circle-xmark"></i> Debe de contener al menos una letra en mayusculas!<br>'
        }
  
        if(!(expresion_numeros.test(valor_confirmar))){
          mensaje += '<i class="fa-regular fa-circle-xmark"></i> Debe de contener al menos un numero!<br>'
        }
  
        if(!(expresion_especial.test(valor_confirmar))){
          mensaje += '<i class="fa-regular fa-circle-xmark"></i> Debe de contener al menos un caracter especial!<br>'
        }
  
        $("#texto_mensaje_clave_confirmacion").css("display", "block").css("clip-path", "none").css("text-align","left").css("color","white").css("font-weight","bold").html(mensaje);
  
        $("#claveConfirm").css("border","1px solid rgb(158, 3, 3)").css("box-shadow","0 0 15px rgb(158, 3, 3)")
        return;
      }
  
      if(valor != valor_confirmar){
        $("#texto_mensaje_clave_confirmacion").css("display", "block").text("las contraseñas ingresadas no coinciden!");
  
        $("#claveConfirm").css("border","1px solid rgb(158, 3, 3)").css("box-shadow","0 0 15px rgb(158, 3, 3)")
        return;
      }
    }

    if(valor && valor_confirmar){
      $("#confirmarContraseña").css("display", "block");
    } else {
      $("#confirmarContraseña").css("display", "none");
    }
  });

  $("#claveConfirm").keyup(function () {
    var valor = $(this).val();
    var valor_confirmar = $("#clave").val()
    var regex = /^(?=.*[A-Z])(?=.*[^a-zA-Z0-9])[a-zA-Z0-9\W]+$/;
    var expresion_letras = /^(?=.*[a-zA-Z]).+$/
    var expresion_mayusculas = /^(?=.*[A-Z]).+$/
    var expresion_numeros = /^(?=.*[0-9]).+$/
    var expresion_especial = /^(?=.*[^a-zA-Z0-9\s]).+$/

    $("#texto_mensaje_clave_confirmacion").css("display", "none");
    $("#texto_mensaje_clave_confirmacion").text("");
    $(this).css("border","1px solid #ced4da").css("box-shadow","none")

    if (valor.length == 0) {
      $("#texto_mensaje_clave_confirmacion").css("display", "none").text("");

      return;
    }
    else{
      $(this).css("border","1px solid rgb(14, 184, 37)").css("box-shadow","0 0 15px rgb(14, 184, 37)")
    }

    if(valor.length < 5){
      $("#texto_mensaje_clave_confirmacion").css("display", "block").text("La contraseña debe de ser mayor a 5 caracteres!");

      $(this).css("border","1px solid rgb(158, 3, 3)").css("box-shadow","0 0 15px rgb(158, 3, 3)")
      return;
    }

    if(!(regex.test(valor))){
      var mensaje = ""

      if(!(expresion_letras.test(valor))){
        mensaje += '<i class="fa-regular fa-circle-xmark"></i> Debe de contener al menos una letra!<br>'
      }

      if(!(expresion_mayusculas.test(valor))){
        mensaje += '<i class="fa-regular fa-circle-xmark"></i> Debe de contener al menos una letra en mayusculas!<br>'
      }

      if(!(expresion_numeros.test(valor))){
        mensaje += '<i class="fa-regular fa-circle-xmark"></i> Debe de contener al menos un numero!<br>'
      }

      if(!(expresion_especial.test(valor))){
        mensaje += '<i class="fa-regular fa-circle-xmark"></i> Debe de contener al menos un caracter especial!<br>'
      }

      $("#texto_mensaje_clave_confirmacion").css("display", "block").css("clip-path", "none").css("text-align","left").css("color","white").css("font-weight","bold").html(mensaje);

      $(this).css("border","1px solid rgb(158, 3, 3)").css("box-shadow","0 0 15px rgb(158, 3, 3)")
      return;
    }

    if(valor != valor_confirmar){
      $("#texto_mensaje_clave_confirmacion").css("display", "block").text("las contraseñas ingresadas no coinciden!");

      $(this).css("border","1px solid rgb(158, 3, 3)").css("box-shadow","0 0 15px rgb(158, 3, 3)")
      return;
    }

    if(valor && valor_confirmar){
      $("#confirmarContraseña").css("display", "block");
    } else {
      $("#confirmarContraseña").css("display", "none");
    }
  });

  $("#confirmarContraseña").click(function () {
    var clave = $("#clave").val();
    var clave2 = $("#claveConfirm").val();

    if (clave == clave2) {
      $("#confirmarContraseña").css("display", "block");
      $("#mensajeConfirmC").html("");
      $("#mensajeConfirmC").css("display", "none");

      mensaje("pregunta");
    } else {
      $("#confirmarContraseña").css("display", "none");
      $("#mensajeConfirmC").css("display", "block");
      $("#mensajeConfirmC").html("Las contraseñas no coinciden!");
    }
  });

  function modificar() {
    var datos =
      "cedula=" +
      $("#id_cedula").val() +
      "&seguridad=" +
      $("#codigo_seguridad").val() +
      "&clave=" +
      $("#claveConfirm").val() +
      "&numero=" +
      $("#numeroSeguridadT").val() +
      "&accion=modificar";


    let timerInterval;
    Swal.fire({
      title: "Procesando!",
      html: "",
      timer: 1500,
      color: "white",
      background: "#000910",
      timerProgressBar: true,
      didOpen: () => {
        Swal.showLoading();
      },
      willClose: () => {
        clearInterval(timerInterval);
      },
    }).then((result) => {
      if (result.dismiss === Swal.DismissReason.timer) {
        $.ajax({
          type: "POST",
          url: "",
          data: datos,
          success: function (response) {
            console.log(response);
            
            var res = JSON.parse(response);
            if (res.success) {
              $("#modalCambioContraseña").modal("hide");

              mensaje("listo");

              setTimeout(() => {
                if (res.cargo) {
                  window.location.href = "?pagina=principal";
                } else {
                  window.location.href = "?pagina=iniciarSesion";
                }
              }, 1500);
            } else if (res.invalido) {
              mensaje("invalido",res.invalido)
            } else if (res.error) {
              mensaje("error");
            }
          },
          error: function (xhr, status, error) {
            mensaje("error");
          },
        });
      }
    });
  }

  $("#preguntas").click(function () {
    $("#modalClavePregunta").modal("show");
    var numero = pregunta();

    if (numero == 1) {
      $("#labelPregunta").text("¿Cual es tu comida Favorita?");
    } else if (numero == 2) {
      $("#labelPregunta").text("¿En qué ciudad naciste?");
    } else if (numero == 3) {
      $("#labelPregunta").text("¿Cuál es tu película favorita?");
    }

    $("#numPregunta").val(numero);
  });

  function pregunta() {
    return Math.floor(Math.random() * 3) + 1;
  }

  function mensaje(accion, mensaje) {
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
        confirmButtonText: "Confirmar",
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
        timer: 1500,
      });
    }
  }
});

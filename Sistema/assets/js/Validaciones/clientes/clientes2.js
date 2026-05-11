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

  const calcularEdad = (fechaN) => {
    var edad = 0;
    const fechaActual = new Date();
    const añoActual = parseInt(fechaActual.getFullYear());
    const mesActual = parseInt(fechaActual.getMonth()) + 1;
    const diaActual = parseInt(fechaActual.getDate());

    const añoNacimiento = parseInt(String(fechaN).substring(0,4));
    const mesNacimiento = parseInt(String(fechaN).substring(5,7));
    const diaNacimiento = parseInt(String(fechaN).substring(8,10));

    edad = añoActual - añoNacimiento;
    if(mesActual < mesNacimiento){
        edad--;
    }
    else if(mesActual == mesNacimiento){
        if(diaActual < diaNacimiento){
            edad--;
        }
    }

    return edad;
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

    var url = window.location.search;
    var pad = url.split("=")[1]

    if(pad === "alquiler"){
      var datos = {
        cedula: cedula,
        accion: "validarC",
      }
      $.ajax({
        type: "POST",
        url: "",
        contentType: "application/json",
        data: JSON.stringify(datos),
        dataType: "json",
        success: function (response) {
          var res = response;
  
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
    }
    else{
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
    }

    if (validarDatos()) {
      $("#guardarCliente").css("display", "block");
    } else {
      $("#guardarCliente").css("display", "none");
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
      $("#guardarCliente").css("display", "block");
    } else {
      $("#guardarCliente").css("display", "none");
    }
  });

  $("#cedula_seleccion").change(function(){
    var valor = $(this).val();
    var expresion = /^[VE]-\d{6,9}$/

    $("#texto_mensaje_cedula_seleccion").css("display", "none");
    $("#texto_mensaje_cedula_seleccion").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_cedula_seleccion").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if(!(expresion.test(valor))){
      $("#texto_mensaje_cedula_seleccion").css("display", "block");
      $("#texto_mensaje_cedula_seleccion").text(
        "La cedula debe de tener entre 7 y 9 digitos!"
      );

      $("#Seleccion_cliente").css("display", "none")

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    var datosParaEnviar = {
      cedula: valor,
      accion: "validarClienteAlquiler",
    };

    $.ajax({
      type: "POST",
      url: "",
      contentType: "application/json",
      data: JSON.stringify(datosParaEnviar),
      dataType: "json",
      success: function (response) {
        var res = response;

        if(res.error){
          alertas("warning", res.error, "Ups!")
        }
        else if(res.incompleto){
          alertas("warning", res.incompleto, "Lo Siento!")
        }
        else if(res.invalido){
          alertas("warning", res.invalido, "Lo Siento!")
        }

        if (res.conteo > 0) {
          $("#texto_mensaje_cedula_seleccion").css("display", "block");
          $("#texto_mensaje_cedula_seleccion").text(
            "Este cliente ya se encuentra en un alquiler!"
          );

          $("#Seleccion_cliente").css("display", "none")

          $("#cedula_seleccion")
            .css("border", "1px solid rgb(158, 3, 3)")
            .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
          return;
        }
      },
      error: function (xhr, status, error) {
        mensaje("error");
      },
    })

    if(valor.length > 0){
      $("#Seleccion_cliente").css("display", "block")
    }
    else{
      $("#Seleccion_cliente").css("display", "none")
    }

  })

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

      $("#guardarCliente").css("display", "none");
      return;
    }

    if (capitalizado.length < 3) {
      $("#texto_mensaje_nombre").css("display", "block");
      $("#texto_mensaje_nombre").text(
        "El nombre tiene que tener mas de 3 caracteres!"
      );

      $("#guardarCliente").css("display", "none");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    } else {
      $("#guardarCliente").css("display", "block");
    }

    if (validarDatos()) {
      $("#guardarCliente").css("display", "block");
    } else {
      $("#guardarCliente").css("display", "none");
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

      $("#guardarCliente").css("display", "none");
      return;
    }

    if (capitalizado.length < 3) {
      $("#texto_mensaje_apellido").css("display", "block");
      $("#texto_mensaje_apellido").text(
        "El nombre tiene que tener mas de 3 caracteres!"
      );

      $("#guardarCliente").css("display", "none");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    } else {
      $("#guardarCliente").css("display", "block");
    }

    if (validarDatos()) {
      $("#guardarCliente").css("display", "block");
    } else {
      $("#guardarCliente").css("display", "none");
    }
  });

  // --------------------------------------------------------------------

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
      $("#guardarCliente").css("display", "block");
    } else {
      $("#guardarCliente").css("display", "none");
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
      $("#guardarCliente").css("display", "none");

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
      $("#guardarCliente").css("display", "none");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (validarDatos()) {
      $("#guardarCliente").css("display", "block");
    } else {
      $("#guardarCliente").css("display", "none");
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
      $("#guardarCliente").css("display", "block");
    } else {
      $("#guardarCliente").css("display", "none");
    }
  });

  // --------------------------------------------------------------

  $("#sexo").change(function(){
    var valor = $(this).val()
    var soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;

    $("#texto_mensaje_sexo").css("display", "none");
    $("#texto_mensaje_sexo").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_sexo").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (!(soloLetras.test(valor))) {
      $("#texto_mensaje_sexo").css("display", "block").text(
        "El valor tiene un formato no valido!"
      );

      $("#guardarCliente").css("display", "none");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      return;
    }

    if (validarDatos()) {
      $("#guardarCliente").css("display", "block");
    } else {
      $("#guardarCliente").css("display", "none");
    }

  })

// ------------------------------------------------------------------

  $("#fecha").on("change keyup", function(){
    var valor = $(this).val()

    $("#texto_mensaje_fecha_nacimiento").css("display", "none");
    $("#texto_mensaje_fecha_nacimiento").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_fecha_nacimiento").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if(!(calcularEdad(valor) >= 18)){
      $("#texto_mensaje_fecha_nacimiento").css("display", "block").text(
        "El cliente tiene que ser mayor de edad!"
      );

      $("#guardarCliente").css("display", "none");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      return;
    }

    if (validarDatos()) {
      $("#guardarCliente").css("display", "block");
    } else {
      $("#guardarCliente").css("display", "none");
    }
  })

  // ------------------------------------------------------------------------
// -----------------------------------------------------------------------------
  
  $("#cedulaModificar").keypress(function (event) {
    if (event.which < 48 || event.which > 57 || this.value.length === 9) {
      return false;
    }
  });

  $("#cedulaModificar").keyup(function () {
    var valor = $(this).val();
    var prefijo = $("#prefijoModificar").val();
    var cedula = prefijo + valor;
    var longitudCedula = valor.length;

    $("#texto_mensaje_cedula_modificar").css("display", "none").html("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");
    $("#prefijo").css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0 && $("#prefijo").val().length == 0) {
      $("#texto_mensaje_cedula_modificar").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if ($("#prefijoModificar").val().length <= 0) {
      $("#texto_mensaje_cedula_modificar")
        .css("display", "block")
        .html("Debes de seleccionar el prefijo!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      $("#prefijoModificar")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      return;
    }

    if (valor.length <= 6) {
      $("#texto_mensaje_cedula_modificar").css("display", "block");
      $("#texto_mensaje_cedula_modificar").text(
        "La cedula debe de tener entre 7 y 9 digitos!"
      );

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      $("#prefijoModificar")
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
      return;
    }

    if ($("#prefijoModificar").val().length == 0) {
      $("#texto_mensaje_cedula_modificar").css("display", "none").text("");

      return;
    } else {
      $("#prefijoModificar")
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
          $("#texto_mensaje_cedula_modificar")
            .css("display", "block")
            .html("Esta cedula ya esta registrada!");
          $("#texto_mensaje_cedula_modificar");

          $("#cedulaModificar")
            .css("border", "1px solid rgb(158, 3, 3)")
            .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
          $("#prefijoModificar")
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

    if (validarDatosModificar()) {
      $("#modificarDatos").css("display", "block");
    } else {
      $("#modificarDatos").css("display", "none");
    }
  });

  $("#prefijoModificar").change(function () {
    var valor = $(this).val();
    var cedula = $("#cedulaModificar").val();
    var longValor = valor.length;
    var longCedula = cedula.length;

    $("#texto_mensaje_cedula_modificar").css("display", "none").html("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");
    $("#cedula").css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (longValor <= 0 && longCedula >= 1) {
      $("#texto_mensaje_cedula_modificar")
        .css("display", "block")
        .html("Debes de seleccionar el prefijo!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      $("#cedulaModificar")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (valor.length == 0) {
      $("#texto_mensaje_cedula_modificar").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (longValor > 0 && longCedula < 1) {
      $("#cedulaModificar")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (cedula.length <= 6) {
      $("#texto_mensaje_cedula_modificar").css("display", "block");
      $("#texto_mensaje_cedula_modificar").text(
        "La cedula debe de tener entre 7 y 9 digitos!"
      );

      $("#cedulaModificar")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (validarDatosModificar()) {
      $("#modificarDatos").css("display", "block");
    } else {
      $("#modificarDatos").css("display", "none");
    }
  });

  // ---------------------------------------------------------------------

  $("#nombreModificar").on("input", function () {
    var valor = $(this).val();
    var soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
    const capitalizado = capitalizarPalabras(valor);

    $("#texto_mensaje_nombre_modificar").css("display", "none");
    $("#texto_mensaje_nombre_modificar").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor !== capitalizado) {
      var cursorPosicion = $(this).prop("selectionStart");

      $(this).val(capitalizado);
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

    if (!soloLetras.test(capitalizado)) {
      $("#texto_mensaje_nombre_modificar").css("display", "block");
      $("#texto_mensaje_nombre_modificar").text("Este campo solo puede aceptar letras!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#modificarDatos").css("display", "none");
      return;
    }

    if (capitalizado.length < 3) {
      $("#texto_mensaje_nombre_modificar").css("display", "block");
      $("#texto_mensaje_nombre_modificar").text(
        "El nombre tiene que tener mas de 3 caracteres!"
      );

      $("#modificarDatos").css("display", "none");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    } else {
      $("#modificarDatos").css("display", "block");
    }

    if (validarDatosModificar()) {
      $("#modificarDatos").css("display", "block");
    } else {
      $("#modificarDatos").css("display", "none");
    }
  });

  // ---------------------------------------------------------------------

  $("#apellidoModificar").on("input", function () {
    var valor = $(this).val();
    var soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
    const capitalizado = capitalizarPalabras(valor);

    $("#texto_mensaje_apellido_modificar").css("display", "none");
    $("#texto_mensaje_apellido").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor !== capitalizado) {
      var cursorPosicion = $(this).prop("selectionStart");

      $(this).val(capitalizado);
      $(this).prop("selectionStart", cursorPosicion);
      $(this).prop("selectionEnd", cursorPosicion);
    }

    if (valor.length == 0) {
      $("#texto_mensaje_apellido_modificar").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (!soloLetras.test(capitalizado)) {
      $("#texto_mensaje_apellido_modificar").css("display", "block");
      $("#texto_mensaje_apellido_modificar").text(
        "Este campo solo puede aceptar letras!"
      );

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#modificarDatos").css("display", "none");
      return;
    }

    if (capitalizado.length < 3) {
      $("#texto_mensaje_apellido_modificar").css("display", "block");
      $("#texto_mensaje_apellido_modificar").text(
        "El nombre tiene que tener mas de 3 caracteres!"
      );

      $("#modificarDatos").css("display", "none");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    } else {
      $("#modificarDatos").css("display", "block");
    }

    if (validarDatosModificar()) {
      $("#modificarDatos").css("display", "block");
    } else {
      $("#modificarDatos").css("display", "none");
    }
  });

  // --------------------------------------------------------------------

  $("#correoModificar").keyup(function () {
    var valor = $(this).val();
    var expresion = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    $("#texto_mensaje_correo_modificar").css("display", "none");
    $("#texto_mensaje_correo_modificar").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_correo_modificar").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (valor.length > 7) {
      if (!expresion.test(valor)) {
        $("#texto_mensaje_correo_modificar").css("display", "block");
        $("#texto_mensaje_correo_modificar").text(
          "El correo no es valido Ej. correo@gmail.com!"
        );

        $(this)
          .css("border", "1px solid rgb(158, 3, 3)")
          .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
        return;
      }
    }

    if (validarDatosModificar()) {
      $("#modificarDatos").css("display", "block");
    } else {
      $("#modificarDatos").css("display", "none");
    }
  });

  // ---------------------------------------------------------------------

  $("#operadoraModificar").change(function () {
    var valor = $(this).val();
    var numero = $("#telefonoModificar").val();
    var expresion = /^[0-9]*$/;

    $("#texto_mensaje_telefono_modificar").css("display", "none");
    $("#texto_mensaje_telefono_modificar").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0 && numero.length == 0) {
      $("#texto_mensaje_telefono_modificar").css("display", "none").text("");
      $("#telefonoModificar")
        .css("border", "1px solid #ced4da")
        .css("box-shadow", "none");
    }

    if (valor.length == 0 && numero.length > 0) {
      $("#texto_mensaje_telefono_modificar").css("display", "block");
      $("#texto_mensaje_telefono_modificar").text("Debes de seleccionar la operadora!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#telefonoModificar")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (valor.length > 0 && numero.length >= 5 && numero.length <= 7) {
      $("#telefonoModificar")
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (valor.length > 0 && numero.length < 5) {
      $("#texto_mensaje_telefono_modificar").css("display", "block");
      $("#texto_mensaje_telefono_modificar").text(
        "El valor debe de estar entre 6 y 7 numeros!"
      );
      $("#modificarDatos").css("display", "none");

      $("#telefonoModificar")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
    }

    if (!expresion.test(valor)) {
      $("#texto_mensaje_telefono_modificar").css("display", "block");
      $("#texto_mensaje_telefono_modificar").text(
        "El valor seleccionado no es numerico!"
      );

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (valor.length == 0) {
      $("#texto_mensaje_telefono_modificar").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (valor.length > 0 && numero.length == 0) {
      $("#telefonoModificar")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
    }
  });

  $("#telefonoModificar").keyup(function () {
    var valor = $(this).val();
    var operadora = $("#operadoraModificar").val();

    $("#texto_mensaje_telefono_modificar").css("display", "none");
    $("#texto_mensaje_telefono_modificar").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0 && operadora.length == 0) {
      $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");
      $("#operadoraModificar")
        .css("border", "1px solid #ced4da")
        .css("box-shadow", "none");

      return;
    }

    if (valor.length == 0 && operadora.length > 0) {
      $("#texto_mensaje_telefono_modificar").css("display", "none").text("");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
    }

    if (operadora.length == 0) {
      $("#texto_mensaje_telefono_modificar").css("display", "block");
      $("#texto_mensaje_telefono_modificar").text("Debes de seleccionar la operadora!");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      $("#operadoraModificar")
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (valor.length == 0) {
      $("#texto_mensaje_telefono_modificar").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (!(valor.length >= 5 && valor.length <= 7)) {
      $("#texto_mensaje_telefono_modificar").css("display", "block");
      $("#texto_mensaje_telefono_modificar").text(
        "El valor debe de estar entre 6 y 7 numeros!"
      );
      $("#modificarDatos").css("display", "none");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (validarDatosModificar()) {
      $("#modificarDatos").css("display", "block");
    } else {
      $("#modificarDatos").css("display", "none");
    }
  });

  $("#telefonoModificar").keypress(function (event) {
    if (event.which < 48 || event.which > 57 || this.value.length === 7) {
      return false;
    }
  });

  // --------------------------------------------------------------------

  $("#direccionModificar").keyup(function () {
    var valor = $(this).val();

    $("#texto_mensaje_direccion_modificar").css("display", "none");
    $("#texto_mensaje_direccion_modificar").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_direccion_modificar").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (valor.length < 10) {
      $("#texto_mensaje_direccion_modificar").css("display", "block");
      $("#texto_mensaje_direccion_modificar").text(
        "La direccion debe de tener por lo menos 10 caracteres!"
      );

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
      return;
    }

    if (validarDatosModificar()) {
      $("#modificarDatos").css("display", "block");
    } else {
      $("#modificarDatos").css("display", "none");
    }
  });

  // --------------------------------------------------------------

  $("#sexoModificar").change(function(){
    var valor = $(this).val()
    var soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;

    $("#texto_mensaje_sexo_modificar").css("display", "none");
    $("#texto_mensaje_sexo_modificar").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_sexo_modificar").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if (!(soloLetras.test(valor))) {
      $("#texto_mensaje_sexo_modificar").css("display", "block").text(
        "El valor tiene un formato no valido!"
      );

      $("#modificarDatos").css("display", "none");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      return;
    }

    if (validarDatosModificar()) {
      $("#modificarDatos").css("display", "block");
    } else {
      $("#modificarDatos").css("display", "none");
    }

  })

// ------------------------------------------------------------------

  $("#fechaModificar").on("change keyup", function(){
    var valor = $(this).val()

    $("#texto_mensaje_fecha_nacimiento_modificar").css("display", "none");
    $("#texto_mensaje_fecha_nacimiento_modificar").text("");
    $(this).css("border", "1px solid #ced4da").css("box-shadow", "none");

    if (valor.length == 0) {
      $("#texto_mensaje_fecha_nacimiento_modificar").css("display", "none").text("");

      return;
    } else {
      $(this)
        .css("border", "1px solid rgb(14, 184, 37)")
        .css("box-shadow", "0 0 15px rgb(14, 184, 37)");
    }

    if(!(calcularEdad(valor) >= 18)){
      $("#texto_mensaje_fecha_nacimiento_modificar").css("display", "block").text(
        "El cliente tiene que ser mayor de edad!"
      );

      $("#modificarDatos").css("display", "none");

      $(this)
        .css("border", "1px solid rgb(158, 3, 3)")
        .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

      return;
    }

    if (validarDatosModificar()) {
      $("#modificarDatos").css("display", "block");
    } else {
      $("#modificarDatos").css("display", "none");
    }
  })

  function validarDatosModificar() {
    var cedula = $("#cedulaModificar").val();
    var nombre = $("#nombreModificar").val();
    var apellido = $("#apellidoModificar").val();
    var correo = $("#correoModificar").val();
    var telefono = $("#telefonoModificar").val();
    var operadora = $("#operadoraModificar").val();
    var direccion = $("#direccionModificar").val();
    var sexo = $("#sexoModificar").val();

    if (
      cedula &&
      nombre &&
      apellido &&
      correo &&
      telefono &&
      operadora &&
      direccion &&
      sexo
    ) {
      return true;
    } else {
      return false;
    }
  }

  function validarDatos() {
    var cedula = $("#cedula").val();
    var prefijo = $("#prefijo").val();
    var nombre = $("#nombre").val();
    var apellido = $("#apellido").val();
    var correo = $("#correo").val();
    var telefono = $("#telefono").val();
    var operadora = $("#operadora").val();
    var direccion = $("#direccion").val();
    var sexo = $("#sexo").val();
    var fecha = $("#fecha").val();

    if (
      cedula &&
      prefijo &&
      nombre &&
      apellido &&
      correo &&
      telefono &&
      operadora &&
      direccion &&
      sexo &&
      fecha
    ) {
      return true;
    } else {
      return false;
    }
  }

  function alertas(accion, texto, titulo, funcion, dato) {
    if (accion == "errorC") {
      Swal.fire({
        title: titulo,
        text: texto,
        icon: "error",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
      });
    } else if (accion == "error") {
      Swal.fire({
        title: titulo,
        text: texto,
        icon: "error",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
      });
    } else if (accion == "warning") {
      Swal.fire({
        title: titulo,
        text: texto,
        icon: "error",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
      });
    } else if (accion == "pregunta") {
      Swal.fire({
        title: titulo,
        text: texto,
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
          funcion();
        }
      });
    } else if (accion == "eliminar") {
      Swal.fire({
        title: titulo,
        text: texto,
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
          funcion(dato);
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

  function mensaje(accion, tipo, regla) {
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
    } else if (accion == "pregunta") {
      if (tipo == "inactivar") {
        Swal.fire({
          title: "Estas Seguro!",
          text: "Seguro de que quieres " + tipo + " los datos?",
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
            estado("inactivar");
          }
        });
      } else if (tipo == "activo") {
        Swal.fire({
          title: "Estas Seguro!",
          text: "Seguro de que quieres Activar los datos?",
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
            estado("activo");
          }
        });
      } else {
        Swal.fire({
          title: "Estas Seguro!",
          text: "Seguro de que quieres " + tipo + " el Perfil?",
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
            eliminar("estatus", regla);
          }
        });
      }
    } else if (accion == "perfil") {
      Swal.fire({
        title: "Listo!",
        text: "Perfil creado con exito!",
        icon: "success",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
      });
    } else {
      Swal.fire({
        title: "Listo!",
        text: "Proceso Ejecutado con Exito!",
        icon: "success",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
      });
    }
  }

});
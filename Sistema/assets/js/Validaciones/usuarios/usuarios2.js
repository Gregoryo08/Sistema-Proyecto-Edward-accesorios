$(document).ready(function () {

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

    if(validacionSeguridad()){
      $("#registrarPerfil").css("display", "block");
    } else {
      $("#registrarPerfil").css("display", "none");
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

    if(validacionSeguridad()){
      $("#registrarPerfil").css("display", "block");
    } else {
      $("#registrarPerfil").css("display", "none");
    }
  });
  
  

  
  $("#idRoles").change(function(){
    var valor = $(this).val()
    var expresion = /^[0-9]*$/

    $("#texto_mensaje_rol").css("display", "none");
    $("#texto_mensaje_rol").text("");
    $(this).css("border","1px solid #ced4da").css("box-shadow","none")

    if(!(expresion.test(valor))){
      $("#texto_mensaje_rol").css("display", "block").text("Error el valor del rol no es valido!");

      $(this).css("border","1px solid rgb(158, 3, 3)").css("box-shadow","0 0 15px rgb(158, 3, 3)")
      return;
    }

    validarDatos();
  })

  function validarDatos() {
    var cedula = $("#empleadoPerfil").val();
    var clave = $("#clave").val();
    var clave_confirm = $("#claveConfirm").val();
    var codigo = $("#codigoC").val();
    var rol = $("#idRoles").val()

    if (
      cedula &&
      clave &&
      clave_confirm &&
      codigo &&
      rol
    ) {
      $("#crearPerfil").css("display", "block");
    } else {
      $("#crearPerfil").css("display", "none");
    }
  }

  function validacionSeguridad(){
    var clave1 = $("#clave").val()
    var clave2 = $("#claveConfirm").val()

    if(clave1 && clave2){
      return true
    }
    else{
      return false
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
    } else if (accion == "invalido") {
      Swal.fire({
        title: "Ups!",
        text: tipo,
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
        showConfirmButton: false,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
        timer: 1500,
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

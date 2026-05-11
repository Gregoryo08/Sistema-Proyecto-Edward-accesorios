$(document).ready(function () {
  $("#nombre_metodopago").on("input", function () {
    var dato = $(this).val();
    var expresion = /[^a-zA-Z\s]/g; 
    $(this).val(dato.replace(expresion, ""));
  });

  $("#nombreModificar").on("input", function () {
    var dato = $(this).val();
    var expresion = /[^a-zA-Z\s]/g; 
    $(this).val(dato.replace(expresion, ""));
  });

  $("#nombre_metodopago").keyup(function () {
    var nombre = $(this).val();
    const check = document.getElementsByName("tipoCuenta")
    var checked = false

    for(var i = 0; i < check.length; i++){
      if(check[i].checked){
        checked = true

        break
      }
    }

    if (nombre.length >= 3 && checked === true) {
      $("#guardarMetodopago").css("display", "block");
    } else {
      $("#guardarMetodopago").css("display", "none");
    }
  });

  $(".radios").change(function () { 
    var nombre = $("#nombre_metodopago").val();
    const check = document.getElementsByName("tipoCuenta")
    var checked = false

    for(var i = 0; i < check.length; i++){
      if(check[i].checked){
        checked = true

        break
      }
    }

    if (nombre.length >= 3 && checked === true) {
      $("#guardarMetodopago").css("display", "block");
    } else {
      $("#guardarMetodopago").css("display", "none");
    }
  });

  $("#nombreModificar").keyup(function () {
    var nombre = $(this).val();
    if (nombre.length >= 3) {
      $("#modificarDatos").css("display", "block");
    } else {
      $("#modificarDatos").css("display", "none");
    }
  });
});
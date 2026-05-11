$(document).ready(function () {
   
    datosAjax(1); 
});

function datosAjax(tiene) {
    $.ajax({
        type: "POST",
        url: window.location.href,
        data: { accion: "consulta" },
        success: function (response) {
            json = JSON.parse(response);
           
            ajax(tiene);
        },
        error: function () {
            console.error("Error al consultar datos");
        },
    });
}

function ajax(tiene) {
  $.ajax({
    type: "POST",
    url: "assets/comunes/tablaDatos.php",
    success: function (response) {
      
      $("#cont_content").html(response);

     
      $("#d_nombre").html(json.nombre);
      $("#d_apellido").html(json.apellido);
      $("#d_cedula").html(json.cedula_empleado); 
      $("#d_telefono").html(json.telefono);
      $("#d_cargo").html(json.nombre_cargo);    
      $("#d_correo").html(json.correo);
      $("#d_direccion").html(json.direccion);

      if(tiene === 1){
        buttonAjax();
      }
    },
  });
}

function buttonAjax() {
  $.ajax({
    type: "POST",
    url: "assets/comunes/botonModificarPerfil.php",
    success: function (response) {
      const contenedor_boton = document.getElementById("cont_button")
      contenedor_boton.innerHTML = response

      const boton = contenedor_boton.querySelector("#buttonModificar")
      boton.dataset.nombre = json.nombre
      boton.dataset.apellido = json.apellido
      boton.dataset.cedula = json.cedula_empleado
      boton.dataset.telefono = json.telefono
      boton.dataset.correo = json.correo
      boton.dataset.direccion = json.direccion
      boton.dataset.cargo = json.id_cargo
    },
  });
}



$(document).ready(function () {
  $(document).on("click", "#buttonModificar", function () {
    var cedula = $("#buttonModificar").data("cedula");
    var nombre = $(this).data("nombre");
    var apellido = $("#buttonModificar").data("apellido");
    var operadora = $("#buttonModificar").data("telefono").substring(0, 4);
    var telefono = $(this).data("telefono").substring(4);
    var correo = $(this).data("correo");
    var direccion = $(this).data("direccion");
    var cargo = $(this).data("cargo");

    $("#modalModificarDatos").modal("show");

    $("#cedula").val(cedula);
    $("#nombre").val(nombre);
    $("#apellido").val(apellido);
    $("#operadora").val(operadora);
    $("#telefono").val(telefono);
    $("#correo").val(correo);
    $("#direccion").val(direccion);
    $("#cargos").val(cargo);
  });

  $("#modificar").click(function () {
    var cedula = $("#cedula").val();
    var nombre = $("#nombre").val();
    var apellido = $("#apellido").val();
    var telefono = $("#operadora").val() + $("#telefono").val();
    var correo = $("#correo").val();
    var direccion = $("#direccion").val();
    var cargos = $("#cargos").val();

    if (cedula && nombre && apellido && telefono && correo && direccion && cargos) {
      mensajes("pregunta", "Estas seguro de modificar los Datos!", modificar);
    } else {
      mensajes("vacio");
    }
  });

  function modificar() {
    var nombre = $("#nombre").val(),
      apellido = $("#apellido").val(),
      cedula = $("#cedula").val(),
      correo = $("#correo").val(),
      telefono = $("#operadora").val() + $("#telefono").val(),
      direccion = $("#direccion").val(),
      cargo = $("#cargos").val();

    let timerInterval;
    Swal.fire({
      title: "Procesando!",
      html: "",
      timer: 2000,
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
          data: {
            nombre: nombre,
            apellido: apellido,
            cedula: cedula,
            correo: correo,
            telefono: telefono,
            direccion: direccion,
            cargo: cargo,
            accion: "modificar",
          },
          success: function (response) {
            var res = JSON.parse(response);

            if (res.success) {
              $("#modalModificarDatos").modal("hide");
              mensajes("modificar");
              datosAjax(1); 
            } else if (res.error) {
              mensajes("error", res.error);
            } else if (res.incompleto) {
              mensajes("vacio");
            } else if (res.invalido) {
              mensajes("invalido", res.invalido);
            }
          }
        });
      }
    });
  }

  function mensajes(accion, mensaje, funcion) {
    if (accion == "vacio") {
      Swal.fire({
        title: "Ups!",
        text: "Debes de completar todos los campos!",
        icon: "error",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
      });
    } else if (accion == "pregunta") {
      Swal.fire({
        title: "Un momento!",
        text: mensaje,
        icon: "question",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
        confirmButtonText: "Confirmar",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          funcion();
        }
      });
    } else if (accion == "modificar") {
      Swal.fire({
        title: "Listo!",
        text: "Proceso Ejecutado con Exito!",
        icon: "success",
        color: "white",
        showConfirmButton: false,
        background: "#000910",
        timer: 1500,
      });
    } else {
      Swal.fire({
        title: "Ups!",
        text: mensaje,
        icon: "error",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
      });
    }
  }
});
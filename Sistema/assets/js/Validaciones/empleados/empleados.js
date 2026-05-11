window.obtenerPermisos = function () {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();

    xhr.open("POST", window.location.href, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    xhr.onload = function () {
      if (xhr.status >= 200 && xhr.status < 300) {
        try {
          const respuesta = JSON.parse(xhr.responseText);
          var permisos = respuesta.array.find(
            (registro) => registro.nombre_modulo === respuesta.modulo
          );

          resolve(permisos);
        } catch (e) {
          console.error("Error al parsear JSON:", e);
        }
      } else {
        console.error("La solicitud falló con el estado:", xhr.status);
      }
    };
    xhr.onerror = function () {
      console.error("Error de red al intentar la solicitud.");
    };
    xhr.send("accion=" + encodeURIComponent("permisos"));
  });
};

obtenerPermisos().then((permisosObtenidos) => {
  if (
    permisosObtenidos &&
    (permisosObtenidos.control_total === 1 || permisosObtenidos.registrar === 1)
  ) {
    $(".text-right button").css("display", "block");
  }

  if (
    permisosObtenidos &&
    (permisosObtenidos.control_total === 1 || permisosObtenidos.listar === 1)
  ) {
    $("#tablaEmpleados").DataTable({
      paging: true,
      searching: true,
      ordering: true,
      info: true,
      ajax: {
        url: "?pagina=empleado&ajax=true",
        dataSrc: "",
      },
      columns: [
        { data: "cedula_empleado" },
        {
          data: null,
          render: function (data, type, row) {
            return `${row.nombre} ${row.apellido}`;
          },
        },
        { data: "nombre_cargo" },
        {
          data: null,
          render: function (data, type, row) {
            var botonesHTML = "";

            if (permisosObtenidos && permisosObtenidos.control_total === 1) {
              botonesHTML = `<button type="button" class="btn btn-info btn-verDatos"
              data-cedula="${row.cedula_empleado}"><i class="bi bi-eye-fill"></i></button>
                
                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalModificar"
                            data-nombre="${row.nombre}" 
                            data-apellido="${row.apellido}" 
                            data-correo="${row.correo}" 
                            data-telefono="${row.telefono}"
                            data-direccion="${row.direccion}"
                            data-cedula="${row.cedula_empleado}"
                            data-cargo="${row.nombre_cargo}"
                            data-id_cargo="${row.id_cargo}"><i class="fa-solid fa-pen-to-square"></i></button>
                            
              <button type="button" class="btn btn-danger btn-deshabilitar" data-id="${row.cedula_empleado}" data-nombre="${row.nombre + " " + row.apellido}"><i class="fa-solid fa-trash-can"></i></button>`;
            }

            if (
              permisosObtenidos &&
              permisosObtenidos.control_total === 0 &&
              permisosObtenidos.consultar === 1
            ) {
              botonesHTML += ` <button type="button" class="btn btn-info btn-verDatos"
              data-cedula="${row.cedula_empleado}"><i class="bi bi-eye-fill"></i></button>`;
            }

            if (
              permisosObtenidos &&
              permisosObtenidos.control_total === 0 &&
              permisosObtenidos.modificar === 1
            ) {
              botonesHTML += ` <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalModificar"
              data-nombre="${row.nombre}" 
              data-apellido="${row.apellido}" 
              data-correo="${row.correo}" 
              data-telefono="${row.telefono}"
              data-direccion="${row.direccion}"
              data-cedula="${row.cedula_empleado}"
              data-cargo="${row.nombre_cargo}"
              data-id_cargo="${row.id_cargo}"><i class="fa-solid fa-pen-to-square"></i></button>`;
            }

            if (
              permisosObtenidos &&
              permisosObtenidos.control_total === 0 &&
              permisosObtenidos.eliminar === 1
            ) {
              botonesHTML += ` <button type="button" class="btn btn-danger btn-deshabilitar" data-id="${row.cedula_empleado}" data-nombre="${row.nombre + " " + row.apellido}"><i class="fa-solid fa-trash-can"></i></button>`;
            }

            return botonesHTML;
          },
        },
      ],
      pageLength: 4,
      lengthMenu: [
        [4, 8, 12, 16],
        ["4", "8", "12", "16"],
      ],
      columnDefs: [
        {
          className: "dt-head-center",
          targets: "_all",
          headers: true,
        },
      ],
      language: {
        lengthMenu: "Mostrar _MENU_ registros por página",
        zeroRecords: "No se encontraron resultados",
        info: "Mostrando página _PAGE_ de _PAGES_",
        infoEmpty: "No hay registros disponibles",
        infoFiltered: "(filtrado de _MAX_ registros totales)",
        search: "Buscar:",
        paginate: {
          first: "Primero",
          last: "Último",
          next: "Siguiente",
          previous: "Anterior",
        },
      },
    });

    $("#tablaInactivos").DataTable({
      paging: true,
      searching: true,
      ordering: true,
      info: true,
      ajax: {
        url: "?pagina=empleado&ajax=true&x=inactivos",
        dataSrc: "",
      },
      columns: [
        { data: "cedula_empleado" },
        {
          data: null,
          render: function (data, type, row) {
            return `${row.nombre} ${row.apellido}`;
          },
        },
        {
          data: null,
          render: function (data, type, row) {
            
            return row.nombre_cargo;
          },
        },
        {
          data: null,
          render: function (data, type, row) {
            var botonesHTML = "";

            if(permisosObtenidos && permisosObtenidos.control_total == 1){
              botonesHTML = ` 
              <button type="button" class="btn btn-danger btn-activar" data-id="${row.cedula_empleado}" data-nombre="${row.nombre + " " + row.apellido}"><i class="bi bi-x"></i></button>`
            }

            if (
              permisosObtenidos &&
              permisosObtenidos.control_total == 0 &&
              permisosObtenidos.eliminar == 1
            ) {
              botonesHTML += ` 
              <button type="button" class="btn btn-danger btn-activar" data-id="${row.cedula_empleado}" data-nombre="${row.nombre + " " + row.apellido}"><i class="bi bi-x"></i></button>
            `;
            }

            return botonesHTML;
          },
        },
      ],
      pageLength: 4,
      lengthMenu: [
        [4, 8, 12, 16],
        ["4", "8", "12", "16"],
      ],
      columnDefs: [
        {
          className: "dt-head-center",
          targets: "_all",
          headers: true,
        },
      ],
      language: {
        lengthMenu: "Mostrar _MENU_ registros por página",
        zeroRecords: "No se encontraron resultados",
        info: "Mostrando página _PAGE_ de _PAGES_",
        infoEmpty: "No hay registros disponibles",
        infoFiltered: "(filtrado de _MAX_ registros totales)",
        search: "Buscar:",
        paginate: {
          first: "Primero",
          last: "Último",
          next: "Siguiente",
          previous: "Anterior",
        },
      },
    });
  } else {
    $("#btn_verInactivos").css("display", "none");
    $(".table-responsive").css("display", "none");
  }
});

$(document).ready(function () {
  var arreglo;
  var datacion = "";

  $("#cargo").select2({
    dropdownParent: $("#modalRegistroEmpleados"),
  });

  $("#btn_verInactivos").on("click", function () {
    $("#container_empleados").fadeIn(300);
    if ($.fn.DataTable.isDataTable('#tablaInactivos')) {
        $("#tablaInactivos").DataTable().ajax.reload();
    }
  });

  $("#btn_salir").on("click", function () {
    $("#container_empleados").fadeOut(300);
  });

  $(document).on("click", ".btn-verDatos", function () {
    var cedula = $(this).data("cedula");

    $.ajax({
      type: "POST",
      url: "",
      data: {
        cedula: cedula,
        accion: "consultar",
      },
      success: function (response) {
        var res = JSON.parse(response);

        if (typeof res == "object") {
          $("#VerNombre").text(res.nombre);
          $("#VerApellido").text(res.apellido);
          $("#VerCedula").text(res.cedula_empleado);
          $("#VerTelefono").text(res.telefono);
          $("#VerCorreo").text(res.correo);
          $("#VerCargo").text(res.nombre_cargo);
          $("#VerDireccion").text(res.direccion);

          $("#modalVerDatos").modal("show");
        } else {
          alertas(
            "error",
            "No se encontraron datos en el sistema!",
            "Ups!"
          );
        }
      },
      error: function (xhr, status, error) {
        alertas("error", "Ah Ocurrido un error en el Servidor!", "Ups!");
      },
    });

  });

  $("#registrarEmpleados").click(function () {
    $.ajax({
      type: "POST",
      url: "",
      data: {
        accion: "buscarCargos",
      },
      success: function (response) {
        var res = JSON.parse(response);

        if (res.length > 0) {
          var select = $("#cargo");

          select.empty();

          select.append(
            $("<option>", {
              value: "",
              text: "",
            })
          );

          $.each(res, function (index, value) {
            var option = $("<option>");
            option.val(value.id_cargo);
            option.text(value.nombre_cargo);

            select.append(option);
          });

          $("#modalRegistroEmpleados").modal("show");
        } else {
          alertas(
            "warning",
            "No existen cargos registrados en el sistema!",
            "Lo Siento!"
          );
        }
      },
      error: function (xhr, status, error) {
        alertas("error", "Ah Ocurrido un error en el Servidor!", "Ups!");
      },
    });
  });

  $("#guardarCliente").on("click", function () {
    if (validarDatos()) {
      alertas(
        "pregunta",
        "Estas seguro de los datos ingresados?",
        "Espera un momento!",
        registrar
      );
    } else {
      mensaje("errorC");
    }
  });

  function registrar() {
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
            nombre: $("#nombre").val(),
            apellido: $("#apellido").val(),
            cedula: $("#prefijo").val() + $("#cedula").val(),
            correo: $("#correo").val(),
            telefono: $("#operadora").val() + $("#telefono").val(),
            direccion: $("#direccion").val(),
            cargo: $("#cargo").val(),
            accion: "registrar",
          },
          success: function (response) {
            console.log(response);
            var res = JSON.parse(response);

            if (res.success) {
              $("#modalRegistroEmpleados").modal("hide");
              $("#formRegistroEmpleado")[0].reset();
              $(".modal-backdrop").remove();

              $("input")
                .css("border", "1px solid #ced4da")
                .css("box-shadow", "none");
              $("textarea")
                .css("border", "1px solid #ced4da")
                .css("box-shadow", "none");
              $("select")
                .css("border", "1px solid #ced4da")
                .css("box-shadow", "none");

              alertas("success");

              $("#tablaEmpleados").DataTable().ajax.reload();
            } else if (res.error) {
              alertas("error", res.error, "Ups!");
            } else if (res.incompleto) {
              alertas("errorC", "Hay datos incompletos!", "Lo Siento!");
              var array = res.input.slice(0, -1).split("-");

              $("#formRegistroEmpleado input")
                .css("border", "1px solid rgb(14, 184, 37)")
                .css("box-shadow", "0 0 15px rgb(14, 184, 37)");

              $.each(array, function (index, value) {
                $("#" + value)
                  .css("border", "1px solid rgb(158, 3, 3)")
                  .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

                if (value == "telefono") {
                  $("#operadora")
                    .css("border", "1px solid rgb(158, 3, 3)")
                    .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
                }

                if (value == "cedula") {
                  $("#prefijo")
                    .css("border", "1px solid rgb(158, 3, 3)")
                    .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
                }
              });
            } else if (res.invalido) {
              alertas("warning", res.invalido, "Lo Siento!");

              $(
                "#formRegistroEmpleado input, #formRegistroEmpleado select, #formRegistroEmpleado textarea"
              )
                .css("border", "1px solid rgb(14, 184, 37)")
                .css("box-shadow", "0 0 15px rgb(14, 184, 37)");

              $("#" + res.input)
                .css("border", "1px solid rgb(158, 3, 3)")
                .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

              if (res.input == "telefono") {
                $("#operadora")
                  .css("border", "1px solid rgb(158, 3, 3)")
                  .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
              }

              if (res.input == "cedula") {
                $("#prefijo")
                  .css("border", "1px solid rgb(158, 3, 3)")
                  .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
              }
            }
          },
          error: function (xhr, status, error) {
            alertas("error", "Ah ocurrido un error con el Servidor!", "Ups!");
          },
        });
      }
    });
  }

  $("#btn_cancel_register").click(function () {
    $("#formRegistroEmpleado")[0].reset();

    $("input").css("border", "1px solid #ced4da").css("box-shadow", "none");
    $("textarea").css("border", "1px solid #ced4da").css("box-shadow", "none");
    $("select").css("border", "1px solid #ced4da").css("box-shadow", "none");
    $(".mensaje p").css("display", "none");
  });

  $(document).on("click", ".btn-warning", function (event) {
    var button = $(this);
    var nombre = button.data("nombre");
    var apellido = button.data("apellido");
    var correo = button.data("correo");
    var operadora = button.data("telefono").substring(0, 4);
    var telefono = button.data("telefono").substring(4);
    var direccion = button.data("direccion");
    var cedula = button.data("cedula").substring(2);
    var prefijo = button.data("cedula").substring(0, 2)
    var cargo = button.data("id_cargo");

    var modal = $("#modalModificar");
    modal.find(".modal-body #nombreModificar").val(nombre);
    modal.find(".modal-body #apellidoModificar").val(apellido);
    modal.find(".modal-body #correoModificar").val(correo);
    modal.find(".modal-body #telefonoModificar").val(telefono);
    modal.find(".modal-body #operadoraModificar").val(operadora);
    modal.find(".modal-body #direccionModificar").val(direccion);
    modal.find(".modal-body #cedulaModificar").val(cedula);
    modal.find(".modal-body #prefijoModificar").val(prefijo);
    $("#id_empleado").val(button.data("cedula"))

    $.ajax({
      type: "POST",
      url: "",
      data: {
        accion: "buscarCargos",
      },
      success: function (response) {
        var res = JSON.parse(response);

        if (res.length > 0) {
          var select = $("#cargoModificar");

          select.empty();

          select.append(
            $("<option>", {
              value: "",
              text: "",
            })
          );

          $.each(res, function (index, value) {
            var option = $("<option>");
            option.val(value.id_cargo);
            option.text(value.nombre_cargo);

            select.append(option);
          });

          modal.find(".modal-body #cargoModificar").val(cargo);
          $("#modalModificar").modal("show");
        } else {
          alertas(
            "warning",
            "No existen cargos registrados en el sistema!",
            "Lo Siento!"
          );
        }
      },
      error: function (xhr, status, error) {
        alertas("error", "Ah Ocurrido un error en el Servidor!", "Ups!");
      },
    });

  });

  $("#modificarDatos").on("click", function () {
    if (validarDatosModificar()) {
      alertas(
        "pregunta",
        "Estas seguro de los datos ingresados?",
        "Espera un momento!",
        modificar
      );
    } else {
      mensaje("errorC");
    }
  });

  function modificar(){

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
          data: {
            cedula_vieja: $("#id_empleado").val(),
            cedula_nueva: $("#prefijoModificar").val() + $("#cedulaModificar").val(),
            nombre: $("#nombreModificar").val(),
            apellido: $("#apellidoModificar").val(),
            correo: $("#correoModificar").val(),
            direccion: $("#direccionModificar").val(),
            telefono: $("#operadoraModificar").val() + $("#telefonoModificar").val(),
            cargo: $("#cargoModificar").val(),
            accion: "modificar"
          },
          success: function (response) {
            console.log(response)
            var res = JSON.parse(response);

            if (res.success) {
              $("#modalModificar").modal("hide");

              $("input")
                .css("border", "1px solid #ced4da")
                .css("box-shadow", "none");
              $("textarea")
                .css("border", "1px solid #ced4da")
                .css("box-shadow", "none");
              $("select")
                .css("border", "1px solid #ced4da")
                .css("box-shadow", "none");


              $("#tablaEmpleados").DataTable().ajax.reload();

              alertas("success")
            } else if (res.error) {
              alertas("error", res.error, "Ups!");
            } else if (res.incompleto) {
              alertas("errorC", "Hay datos incompletos!", "Lo Siento!");
              var array = res.input.slice(0, -1).split("-");

              $(
                "#formModificar input, #formModificar select, #formModificar textarea"
              )
                .css("border", "1px solid rgb(14, 184, 37)")
                .css("box-shadow", "0 0 15px rgb(14, 184, 37)");

              $.each(array, function (index, value) {
                $("#" + value)
                  .css("border", "1px solid rgb(158, 3, 3)")
                  .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

                if (value == "telefonoModificar") {
                  $("#operadoraModificar")
                    .css("border", "1px solid rgb(158, 3, 3)")
                    .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
                }

                if (value == "cedulaModificar") {
                  $("#prefijoModificar")
                    .css("border", "1px solid rgb(158, 3, 3)")
                    .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
                }
              });
            } else if (res.invalido) {
              alertas("warning", res.invalido, "Lo Siento!");

              $(
                "#formModificar input, #formModificar select, #formModificar textarea"
              )
                .css("border", "1px solid rgb(14, 184, 37)")
                .css("box-shadow", "0 0 15px rgb(14, 184, 37)");

              $("#" + res.input)
                .css("border", "1px solid rgb(158, 3, 3)")
                .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

              if (res.input == "telefonoModificar") {
                $("#operadoraModificar")
                  .css("border", "1px solid rgb(158, 3, 3)")
                  .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
              }

              if (res.input == "cedulaModificar") {
                $("#prefijoModificar")
                  .css("border", "1px solid rgb(158, 3, 3)")
                  .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
              }
            }
          },
          error: function (xhr, status, error) {
            mensaje("error", "Modificado");
          },
        });
      }
    });
  }

  $(document).on("click", ".btn-deshabilitar", function () {
    var id = $(this).data("id");
    var nombre = $(this).data("nombre")
    $("#id_empleado_delete").val(id);

    alertas("eliminar", "Estas seguro de inactivar al empleado '" + nombre +"'","Espera un momento!", estado, "inactivo");
  });

  $(document).on("click", ".btn-activar", function () {
    var id = $(this).data("id");
    var nombre = $(this).data("nombre")
    $("#id_empleado_delete").val(id);

    alertas("eliminar", "Estas seguro de activar al empleado '" + nombre +"'","Espera un momento!", estado, "activo");
  });

  function estado(accion) {
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
          data: {
            id: $("#id_empleado_delete").val(),
            estado: accion,
            accion: "eliminar"
          },
          success: function (response) {
            console.log(response)
            var res = JSON.parse(response);

            if (res.success) {
              $("#tablaEmpleados").DataTable().ajax.reload();
              $("#tablaInactivos").DataTable().ajax.reload();
              $("#modalConfirmarEliminacion").modal("hide");

              alertas("success");
            } else if(res.error) {
              alertas("error", res.error, "Ups!");
            }
          },
          error: function (xhr, status, error) {
            mensaje("error");
          },
        });
      }
    });
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
    var cargo = $("#cargo").val();

    if (
      cedula &&
      prefijo &&
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

  function validarDatosModificar() {
    var cedula = $("#cedulaModificar").val();
    var prefijo = $("#prefijoModificar").val();
    var nombre = $("#nombreModificar").val();
    var apellido = $("#apellidoModificar").val();
    var correo = $("#correoModificar").val();
    var telefono = $("#telefonoModificar").val();
    var operadora = $("#operadoraModificar").val();
    var direccion = $("#direccionModificar").val();
    var cargo = $("#cargoModificar").val();

    if (
      cedula &&
      prefijo &&
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

});

    $(".text-right button").css("display", "block");

    
    $("#tablaPerfilados").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        ajax: {
            url: "?pagina=usuarios&ajax=true",
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
                    var color = (row.perfil == "si") ? "rgb(14, 184, 37)" : "rgb(158, 3, 3)";
                    var perfil = (row.perfil == "si") ? "Si tiene" : "No tiene";
                    return `<span class="interruptor" style="background: ${color};">${perfil}</span>`;
                },
            },
            {
                data: null,
                render: function (data, type, row) {
                   
                    if (row.perfil == "no") {
                        return `<button type="button" class="btn btn-primary btn-perfil" data-id="${row.cedula_empleado}" id="btn_perfil"><i class="fa-solid fa-user-plus"></i></button>`;
                    } else {
                        return `<button type="button" class="btn btn-danger btn-suspender" data-id="${row.cedula_empleado}"><i class="fa-solid fa-trash-can"></i></button>`;
                    }
                },
            },
        ],
        pageLength: 4,
        lengthMenu: [[4, 8, 12, 16], ["4", "8", "12", "16"]],
        columnDefs: [{ className: "dt-head-center", targets: "_all" }],
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

   
    $("#tablaInactivosPerfil").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        ajax: {
            url: "?pagina=usuarios&ajax=true&x=inactivos",
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
                    
                    return `<button type="button" class="btn btn-success btn-habilitar" data-id="${row.cedula_empleado}"><i class="bi bi-person-check"></i></button>`;
                },
            },
        ],
        pageLength: 4,
        lengthMenu: [[4, 8, 12, 16], ["4", "8", "12", "16"]],
        columnDefs: [{ className: "dt-head-center", targets: "_all" }],
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


$(document).ready(function () {
  $("#idRoles").select2({
    dropdownParent: $("#modalSeguridad"),
  });

  $("#btn_verInactivos").click(function () {
    $("#container_empleados").fadeIn(300, function() {
      if ($.fn.DataTable.isDataTable('#tablaInactivosPerfil')) {
          var table = $('#tablaInactivosPerfil').DataTable();
          table.ajax.reload();
          table.columns.adjust().draw(); 
      }
    });
    $("#container_empleados").css({
      "opacity": "1",
      "left": "0"
    });
  });

  $("#btn_salir").on("click", function () {
    $("#container_empleados").fadeOut(300);
    $("#container_empleados").css({
      "opacity": "0",
      "left": "-100%"
    });
  });

  $(document).on("click", ".btn-perfil", function () {
    var codigoC = codigo(6);
    $("#usuario_Codigo").text(codigoC);
    $("#codigoC").val(codigoC);

    $("#usuario_Cedula").text($(this).data("id").substring(2));
    $("#empleadoPerfil").val($(this).data("id"));
    $("#cargoPerfil").val($(this).data("cargo"));
    $("#modalCrearPerfil").modal("show");
  });

  function codigo(longitud) {
    const caracteres =
      "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    var codigoA = "";
    for (var i = 0; i < longitud; i++) {
      codigoA += caracteres.charAt(
        Math.floor(Math.random() * caracteres.length)
      );
    }
    return codigoA;
  }

  $("#button_preguntas, #button_cancelar").on("click", function () {
    $("#modalSeguridad").modal("hide");
    $("#modalCrearPerfil").modal("show");

    $("#formPerfil")[0].reset();
    $("#formPerfilPreguntas")[0].reset();
    $("#idRoles").html("");
  });

  $("#registrarPerfil").click(function () {
    if (validacionSeguridad()) {
      $.ajax({
        type: "POST",
        url: "",
        data: "accion=consultaRoles",
        success: function (response) {
          var res = JSON.parse(response);
          const select = $("#idRoles");

          if (typeof res == "object") {
            select.append(
              $("<option>", {
                value: "",
                text: "",
              })
            );

            $.each(res, function (index, value) {
              var option = $("<option>");
              option.val(value.idRol);
              option.text(value.descripcion_rol);
              select.append(option);
            });

            $("#modalCrearPerfil").modal("hide");
            $("#modalSeguridad").modal("show");
          } else {
            mensaje("warning", "No se encontraron roles registrados!");
          }
        },
        error: function (xhr, status, error) {
          mensaje("error", "Ah ocurrido un error con el servidor!");
        },
      });
    } else {
      $(this).css("display", "none");
      mensaje("warning", "Alguno de los campos esta vacio!");
    }
  });

  $("#crearPerfil").click(function () {
    if (validarDatos()) {
      mensaje("pregunta", "Estas seguro de los datos ingresados?", crear);
    } else {
      mensaje(
        "warning",
        "Existen datos faltantes, rectifica para poder seguir con el registro!"
      );
    }
  });

  function crear() {
    var cedula = $("#empleadoPerfil").val();
    var rol = $("#idRoles").val();
    var clave_confirm = $("#claveConfirm").val();
    var codigo = $("#codigoC").val();

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
            cedula: cedula,
            clave: clave_confirm,
            codigo: codigo,
            id_rol: rol,
            accion: "perfil",
          },
          success: function (response) {
            var res = JSON.parse(response);

            if (res.success) {
              $("#modalSeguridad").modal("hide");
              $("#btn_cancel").click();

              $(
                " #formPerfilPreguntas select"
              )
                .css("border", "1px solid #ced4da")
                .css("box-shadow", "none");

              $("#formPerfil input, #formPerfil select, #formPerfil textarea")
                .css("border", "1px solid #ced4da")
                .css("box-shadow", "none");

              mensaje("success");

              $("#formPerfilPreguntas")[0].reset();
              $("#formPerfil")[0].reset();
              const lista = document.getElementById("idRoles");
              lista.innerHTML = "";

              $("#tablaPerfilados").DataTable().ajax.reload();
              $("#tablaInactivosPerfil").DataTable().ajax.reload();
            } else if (res.incompleto) {
              mensaje("warning", res.incompleto);
            } else if (res.invalido) {
              mensaje("invalido", res.invalido);
            } else {
              mensaje("error", res.error);
            }
          },
          error: function (xhr, status, error) {
            mensaje("error", "Error en el servidor");
          },
        });
      }
    });
  }

  $(document).on("click", ".btn-suspender", function () {
    var id = $(this).data("id");
    $("#id_empleado_delete").val(id);

    mensaje("eliminar", "Estas seguro de inhabilitar a este usuario?", eliminar, "Inactivo");
  });

  $(document).on("click", ".btn-habilitar", function () {
    var id = $(this).data("id");
    $("#id_empleado_delete").val(id);

    mensaje("eliminar", "Estas seguro de habilitar a este usuario?", eliminar, "Activo");
  });

  function eliminar(dato) {
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
            id: $("#id_empleado_delete").val(),
            estatus: dato,
            accion: "estatus"
          },
          success: function (response) {
            var res = JSON.parse(response);

            if (res.success) {
              mensaje("success")
              $("#tablaPerfilados").DataTable().ajax.reload();
              $("#tablaInactivosPerfil").DataTable().ajax.reload();
            } else if (res.error) {
              mensaje("error", res.error);
            } else if (res.invalido) {
              mensaje("invalido", res.invalido);
            } else if (res.incompleto) {
              mensaje("warning", res.incompleto);
            }
          },
          error: function (xhr, status, error) {
            mensaje("error", "Error en el servidor");
          },
        });
      }
    });
  }

  function validarDatos() {
    var cedula = $("#empleadoPerfil").val();
    var clave = $("#clave").val();
    var clave_confirm = $("#claveConfirm").val();
    var codigo = $("#codigoC").val();
    var rol = $("#idRoles").val();

    return (cedula && rol && clave && clave_confirm && codigo);
  }

  function validacionSeguridad() {
    var clave1 = $("#clave").val();
    var clave2 = $("#claveConfirm").val();
    return (clave1 && clave2);
  }

  function mensaje(accion, mensaje, funcion, dato) {
    if (accion == "error") {
      Swal.fire({
        title: "Ups!", text: mensaje, icon: "error", color: "white",
        showConfirmButton: true, confirmButtonColor: "rgb(238, 191, 0)", background: "#000910",
      });
    } else if (accion == "warning") {
      Swal.fire({
        title: "Lo Siento!", text: mensaje, icon: "warning", color: "white",
        showConfirmButton: false, background: "#000910", timer: 2000,
      });
    } else if (accion == "invalido") {
      Swal.fire({
        title: "Ups!", text: mensaje, icon: "error", color: "white",
        showConfirmButton: true, confirmButtonColor: "rgb(238, 191, 0)", background: "#000910",
      });
    } else if (accion == "pregunta") {
      Swal.fire({
        title: "Estas Seguro!", text: mensaje, icon: "question", color: "white",
        showConfirmButton: true, confirmButtonColor: "rgb(238, 191, 0)", background: "#000910",
        confirmButtonText: "Confirmar", showCancelButton: true, cancelButtonText: "Cancelar",
      }).then((result) => { if (result.isConfirmed) { funcion(); } });
    } else if (accion == "eliminar") {
      Swal.fire({
        title: "Estas Seguro!", text: mensaje, icon: "question", color: "white",
        showConfirmButton: true, confirmButtonColor: "rgb(238, 191, 0)", background: "#000910",
        confirmButtonText: "Confirmar", showCancelButton: true, cancelButtonText: "Cancelar",
      }).then((result) => { if (result.isConfirmed) { funcion(dato); } });
    } else {
      Swal.fire({
        title: "Listo!", text: "Proceso Ejecutado con Exito!", icon: "success", color: "white",
        showConfirmButton: false, background: "#000910", timer: 1500,
      });
    }
  }
});
$(document).ready(function () {

  $(".text-right button").css("display", "block");


  $("#tablaCargos").DataTable({
    destroy: true, 
    paging: true,
    searching: true,
    ordering: true,
    info: true,
    ajax: {
      url: "?pagina=cargos&ajax=true",
      dataSrc: "",
    },
    columns: [
      {
        data: "id_cargo",
        visible: false
      },
      {
        data: "nombre_cargo"
      },
      {
        data: null,
        render: function (data, type, row) {
         
          return `
            <button type="button" class="btn btn-warning btn_modificar" data-toggle="modal" data-target="#modalModificar" 
              data-nombre="${row.nombre_cargo}"
              data-id="${row.id_cargo}"><i class="fa-solid fa-pen-to-square"></i></button>

            <button type="button" class="btn btn-danger btn-eliminar" data-id="${row.id_cargo}" data-nombre="${row.nombre_cargo}">
              <i class="fa-solid fa-trash-can"></i></button>
          `;
        },
      },
    ],
    pageLength: 4,
    lengthMenu: [
      [4, 8],
      ["4", "8"],
    ],
    columnDefs: [
      {
        className: "dt-head-center",
        targets: "_all",
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
});





$(document).ready(function () {
  $("#registro").click(function () {
    mensaje("pregunta", "Estas Seguro de Registrar el Dato?", registrar);
  });

  function registrar() {
    var nombre = $("#cargo").val();

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
            accion: "registrar",
          },
          success: function (response) {
            var res = JSON.parse(response);

            if (res.success) {
              mensaje("success");

              $("#modalRegistroCargo").modal("hide");
              $("#btn_cancel").click();

              $("input, select, textarea")
                .css("border", "1px solid #ced4da")
                .css("box-shadow", "none");

              $("#formRegistroCargo")[0].reset();
              $("#registrar").css("display", "none");
              $("#tablaCargos").DataTable().ajax.reload();
            } else if (res.incompleto) {
              mensaje("warning", res.incompleto);
              $("#" + res.input)
                .css("border", "1px solid rgb(158, 3, 3)")
                .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
            } else if (res.invalido) {
              mensaje("warning", res.invalido);
              $("#" + res.input)
                .css("border", "1px solid rgb(158, 3, 3)")
                .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
            } else {
              mensaje("error", "Ah Ocurrido un error en el Servidor!");
            }
          },
          error: function (xhr, status, error) {
            mensaje("error", "Ah Ocurrido un error en el Servidor!");
          },
        });
      }
    });
  }

  $(document).on("click", ".btn_modificar", function () {
    var id = $(this).data("id");
    var nombre = $(this).data("nombre");
    $("#nombre_cargo").val(nombre);

    window.cargo_Modificar = nombre;

    if (id && nombre) {
      $("#id_cargo").val(id);
      $("#cargo_modificar").val(nombre);
      $("#modalModificarCargo").modal("show");
    } else {
      mensaje("error", "Ah Ocurrido un error en el Servidor!");
    }
  });

  $("#modificar").click(function () {
    mensaje("pregunta", "Estas Seguro de Modificar el Dato?", modificar);
  });

  function modificar() {
    var nombre = $("#cargo_modificar").val();
    var id = $("#id_cargo").val();

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
            id: id,
            nombre: nombre,
            accion: "modificar",
          },
          success: function (response) {
            var res = JSON.parse(response);

            if (res.success) {
              mensaje("success");

              $("#modalModificarCargo").modal("hide");
              $("#btn_cancel").click();
              $("#formModificarCargo")[0].reset();

              $("input, select, textarea")
                .css("border", "1px solid #ced4da")
                .css("box-shadow", "none");

              $("#tablaCargos").DataTable().ajax.reload();
            } else if (res.incompleto) {
              mensaje("warning", res.incompleto);
              $("#" + res.input)
                .css("border", "1px solid rgb(158, 3, 3)")
                .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
            } else if (res.invalido) {
              mensaje("warning", res.invalido);
              $("#" + res.input)
                .css("border", "1px solid rgb(158, 3, 3)")
                .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
            } else {
              mensaje("error", res.error);
            }
          },
          error: function (xhr, status, error) {
            mensaje("error", "Ah Ocurrido un error en el Servidor!");
          },
        });
      }
    });
  }

  $(document).on("click", ".btn-eliminar", function () {
    $("#id_cargo").val($(this).data("id"));

    mensaje(
      "eliminar",
      "Estas Seguro de eliminar el Cargo?",
      eliminar,
      "Al eliminar el cargo se eliminaran, los empleados ligados al cargo '" +
      $(this).data("nombre") +
      "'"
    );
  });

  function eliminar() {
    var id = $("#id_cargo").val();

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
            id: id,
            accion: "eliminar",
          },
          success: function (response) {
            var res = JSON.parse(response);

            if (res.success) {
              mensaje("success");

              $("#modalModificarRol").modal("hide");
              $("#btn_cancel").click();

              $("#tablaCargos").DataTable().ajax.reload();
            } else {
              mensaje("error", res.error);
            }
          },
          error: function (xhr, status, error) {
            mensaje("error", "Ah Ocurrido un error en el Servidor!");
          },
        });
      }
    });
  }

  function mensaje(accion, mensaje, funcion, title) {
    if (accion == "error") {
      Swal.fire({
        title: "Ups!",
        text: mensaje,
        icon: "error",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
      });
    } else if (accion == "warning") {
      Swal.fire({
        title: "Lo Siento!",
        text: mensaje,
        icon: "warning",
        color: "white",
        showConfirmButton: false,
        confirmButtonColor: "rgb(238, 191, 0)",
        background: "#000910",
        timer: 2000,
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
    } else if (accion == "pregunta") {
      Swal.fire({
        title: "Estas Seguro!",
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
    } else if (accion == "eliminar") {
      Swal.fire({
        title: mensaje,
        text: title,
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
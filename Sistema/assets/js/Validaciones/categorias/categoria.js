$(document).ready(function () {
 
  $(".text-right button").css("display", "block");

 
  $("#tablaCategorias").DataTable({
    destroy: true,
    paging: true,
    searching: true,
    ordering: true,
    info: true,
    ajax: {
      url: "?pagina=categoria&ajax=true",
      dataSrc: "",
    },
    columns: [
      { data: "id_categoria" },
      { data: "nombre_categoria" },
      {
        data: null,
        render: function (data, type, row) {
          
          return `
            <button type="button" class="btn btn-warning btn_modificar" data-toggle="modal" data-target="#modalModificarCategoria" 
              data-nombre="${row.nombre_categoria}"
              data-id="${row.id_categoria}"><i class="fa-solid fa-pen-to-square"></i></button>

            <button type="button" class="btn btn-danger btn-eliminar" data-id="${row.id_categoria}" data-nombre="${row.nombre_categoria}">
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
        targets: [0],
        visible: false,
        searchable: false,
      },
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
    mensaje("pregunta", "Estas Seguro de Registrar la Categoría?", registrar);
  });

  function registrar() {
    var nombre = $("#categoria").val();

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

              $("#modalRegistroCategoria").modal("hide");
              $("#btn_cancel").click(); 

              $("#formRegistroCategoria")[0].reset();
              $("#tablaCategorias").DataTable().ajax.reload();
            } else if (res.incompleto) {
              mensaje("warning", res.incompleto);
            } else if (res.invalido) {
              mensaje("warning", res.invalido);
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

    if (id && nombre) {
      $("#id_categoria").val(id);
      $("#categoria_modificar").val(nombre);
      $("#modalModificarCategoria").modal("show");
    } else {
      mensaje("error", "Ah Ocurrido un error en el Servidor!");
    }
  });

 
  $("#modificar").click(function () {
    mensaje("pregunta", "¿Estas Seguro de Modificar la Categoría?", modificar);
  });

  function modificar() {
    var nombre = $("#categoria_modificar").val();
    var id = $("#id_categoria").val();

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
              $("#modalModificarCategoria").modal("hide");
              $("#tablaCategorias").DataTable().ajax.reload();
            } else if (res.incompleto) {
              mensaje("warning", res.incompleto);
            } else if (res.invalido) {
              mensaje("warning", res.invalido);
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

  
  $(document).on("click", ".btn-eliminar", function () {
    $("#id_categoria").val($(this).data("id"));
    var nombre = $(this).data("nombre");

    mensaje("eliminar", "¿Estas Seguro de Eliminar la Categoría?", eliminar, "Esta acción no se puede deshacer.");
  });

  function eliminar() {
    var id = $("#id_categoria").val();

    let timerInterval;
    Swal.fire({
      title: "Procesando!",
      timer: 2000,
      color: "white",
      background: "#000910",
      timerProgressBar: true,
      didOpen: () => {
        Swal.showLoading();
      }
    }).then((result) => {
      if (result.dismiss === Swal.DismissReason.timer) {
        $.ajax({
          type: "POST",
          url: "",
          data: { id: id, accion: "eliminar" },
          success: function (response) {
            var res = JSON.parse(response);
            if (res.success) {
              mensaje("success");
              $("#tablaCategorias").DataTable().ajax.reload();
            } else {
              mensaje("error", "Error al eliminar el registro");
            }
          }
        });
      }
    });
  }

  
  function mensaje(accion, mensaje, funcion, title) {
    const colorPrimario = "rgb(238, 191, 0)"; 
    const fondoOscuro = "#000910";

    const configBase = {
      color: "white",
      background: fondoOscuro,
      confirmButtonColor: colorPrimario,
      cancelButtonColor: "#d33"
    };

    if (accion == "error") {
      Swal.fire({
        ...configBase,
        title: "¡Ups!",
        text: mensaje,
        icon: "error"
      });
    } else if (accion == "warning") {
      Swal.fire({
        ...configBase,
        title: "Atención",
        text: mensaje,
        icon: "warning",
        timer: 2000,
        showConfirmButton: false
      });
    } else if (accion == "pregunta" || accion == "eliminar") {
      Swal.fire({
        ...configBase,
        title: mensaje,
        text: title || "",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Confirmar",
        cancelButtonText: "Cancelar"
      }).then((result) => {
        if (result.isConfirmed && funcion) funcion();
      });
    } else {
      
      Swal.fire({
        ...configBase,
        title: "¡Listo!",
        text: "Proceso Ejecutado con Exito!",
        icon: "success",
        timer: 1500,
        showConfirmButton: false
      });
    }
  }
});
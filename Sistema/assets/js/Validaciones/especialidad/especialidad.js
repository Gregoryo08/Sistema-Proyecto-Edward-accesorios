$(document).ready(function () {
    
    let permisos = { registrar: false, modificar: false, eliminar: false };


    $.get("?pagina=especialidad&permisos=true", function (data) {
        permisos = JSON.parse(data);

        
        if (permisos.registrar) {
            $(".text-right button").show();
        } else {
            $(".text-right button").hide();
        }

       
        cargarTabla();
    });

    function cargarTabla() {
        $("#tablaEspecialidades").DataTable({
            destroy: true,
            ajax: {
                url: "?pagina=especialidad&ajax=true",
                dataSrc: "",
            },
            columns: [
                { data: "id_especialidad", visible: false },
                { data: "nombre_especialidad" },
                {
                    data: null,
                    render: function (data, type, row) {
                        let botones = '';

                        if (permisos.modificar) {
                            botones += `
                                <button type="button" class="btn btn-warning btn_modificar" 
                                    data-toggle="modal" data-target="#modalModificarEspecialidad" 
                                    data-nombre="${row.nombre_especialidad}"
                                    data-id="${row.id_especialidad}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button> `;
                        }

                        if (permisos.eliminar) {
                            botones += `
                                <button type="button" class="btn btn-danger btn-eliminar" 
                                    data-id="${row.id_especialidad}" 
                                    data-nombre="${row.nombre_especialidad}">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>`;
                        }

                        return botones !== '' ? botones : '<span class="badge badge-secondary">Solo lectura</span>';
                    },
                },
            ],
            pageLength: 4,
            lengthMenu: [[4, 8], ["4", "8"]],
            columnDefs: [{ className: "dt-head-center", targets: "_all" }],
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _PAGE_ de _PAGES_",
                paginate: { next: "Siguiente", previous: "Anterior" }
            }
        });
    }
});



//Permisos











///////////////////////////////////////////////////////////////////7


$(document).ready(function () {
  
  $("#registro").click(function () {
    mensaje("pregunta", "¿Estas Seguro de Registrar la Especialidad?", registrar);
  });

  function registrar() {
    var nombre = $("#especialidad").val();

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

              $("#modalRegistroEspecialidad").modal("hide");
              $("#btn_cancel").click(); 

              $("#formRegistroEspecialidad")[0].reset();
              $("#tablaEspecialidades").DataTable().ajax.reload();
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
      $("#id_especialidad").val(id);
      $("#especialidad_modificar").val(nombre);
      $("#modalModificarEspecialidad").modal("show");
    } else {
      mensaje("error", "Ah Ocurrido un error en el Servidor!");
    }
  });

 
  $("#modificar").click(function () {
    mensaje("pregunta", "¿Estas Seguro de Modificar la Especialidad?", modificar);
  });

  function modificar() {
    var nombre = $("#especialidad_modificar").val();
    var id = $("#id_especialidad").val();

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
              $("#modalModificarEspecialidad").modal("hide");
              $("#tablaEspecialidades").DataTable().ajax.reload();
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
    $("#id_especialidad").val($(this).data("id"));
    var nombre = $(this).data("nombre");

    mensaje("eliminar", "¿Estas Seguro de Eliminar la Especialidad?", eliminar, "Esta acción no se puede deshacer.");
  });

  function eliminar() {
    var id = $("#id_especialidad").val();

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
              $("#tablaEspecialidades").DataTable().ajax.reload();
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
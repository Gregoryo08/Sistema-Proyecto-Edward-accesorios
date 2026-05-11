$(document).ready(function () {
    
   
    let permisos = { registrar: false, modificar: false, eliminar: false, control_total: false };

    
    $.get("?pagina=marcas&permisos=true", function (data) {
        permisos = JSON.parse(data);

        
        if (permisos.control_total || permisos.registrar) {
            $(".text-left button").show();
        } else {
            $(".text-left button").hide();
        }

       
        cargarTablaMarcas();
    });

    function cargarTablaMarcas() {
        $("#tablaMarcas").DataTable({
            destroy: true,
            ajax: {
                url: "?pagina=marcas&ajax=true&x=marcas",
                dataSrc: "",
            },
            columns: [
                { data: "id_marca", visible: false },
                { data: "nombre_marca" },
                {
                    data: null,
                    render: function (data, type, row) {
                        let botones = '<div class="btn-group" role="group">';

                      
                        if (permisos.control_total || permisos.modificar) {
                            botones += `
                                <button type="button" class="btn btn-warning btn_modificarMarca" 
                                    data-bs-toggle="modal" data-bs-target="#modalModificarMarca" 
                                    data-nombre="${row.nombre_marca}" 
                                    data-id="${row.id_marca}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button> `;
                        }

                       
                        if (permisos.control_total || permisos.eliminar) {
                            botones += `
                                <button type="button" class="btn btn-danger btn-eliminarMarca" 
                                    data-id="${row.id_marca}">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>`;
                        }

                        botones += '</div>';

                        
                        return (botones === '<div class="btn-group" role="group"></div>') 
                            ? '<span class="badge bg-secondary">Solo lectura</span>' 
                            : botones;
                    },
                },
            ],
            pageLength: 4,
            lengthMenu: [[4, 8], ["4", "8"]],
            columnDefs: [{ className: "dt-head-center", targets: "_all" }],
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros por página",
                info: "Mostrando página _PAGE_ de _PAGES_",
                zeroRecords: "No se encontraron resultados",
                paginate: { 
                    first: "Primero", 
                    last: "Último", 
                    next: "Siguiente", 
                    previous: "Anterior" 
                },
            }
        });
    }
});







$(document).ready(function () {

  
  function limpiarEstadoModal() {
    $('.modal').each(function() {
      const instance = bootstrap.Modal.getInstance(this);
      if(instance) instance.hide();
    });

    $(".modal-backdrop").remove();
    $("body").removeClass("modal-open");
    $("body").css({ "overflow": "", "padding-right": "" });
    
    $("input").css("border", "1px solid #ced4da").css("box-shadow", "none");
  }

  $('.modal').on('hidden.bs.modal', function () {
    limpiarEstadoModal();
  });

  $("#btnRegistrarMarca").click(function () {
    mensaje("pregunta", "¿Estas seguro de los datos ingresados?", registrar);
  });

  function registrar() {
    var datos = "nombre=" + $("#nombre").val() + "&accion=registrarMarca";

    Swal.fire({
      title: "Procesando!",
      timer: 800,
      color: "white",
      background: "#000910",
      didOpen: () => { Swal.showLoading(); },
    }).then(() => {
        $.ajax({
          type: "POST",
          url: window.location.href,
          data: datos,
          success: function (response) {
            var res = JSON.parse(response);
            if (res.success) {
              limpiarEstadoModal(); 
              $("#formRegistroMarca")[0].reset();
              mensaje("success");
              $("#tablaMarcas").DataTable().ajax.reload();
            } else if (res.invalido) {
              mensaje("yaExiste", res.invalido); // Usamos el mensaje del PHP
              $("#" + res.input).css("border", "1px solid rgb(158, 3, 3)").css("box-shadow", "0 0 15px rgb(158, 3, 3)");
            } else if (res.incompleto) {
              mensaje("errorC");
              var array = res.input.slice(0, -1).split("-");
              $.each(array, function (index, value) {
                $("#" + value).css("border", "1px solid rgb(158, 3, 3)").css("box-shadow", "0 0 15px rgb(158, 3, 3)");
              });
            } else {
              mensaje("error");
            }
          }
        });
    });
  }

  $(document).on("click", ".btn_modificarMarca", function () {
    $("#nombreModificar").val($(this).data("nombre"));
    $("#marca_id").val($(this).data("id"));
    const myModal = new bootstrap.Modal(document.getElementById('modalModificarMarca'));
    myModal.show();
  });

  $("#btnModificarMarca").click(function () {
    mensaje("pregunta", "¿Estas seguro de modificar los datos?", modificar);
  });

  function modificar() {
    var datos = "nombre=" + $("#nombreModificar").val() + "&id=" + $("#marca_id").val() + "&accion=modificarMarca";

    Swal.fire({
      title: "Procesando!",
      timer: 800,
      color: "white",
      background: "#000910",
      didOpen: () => { Swal.showLoading(); },
    }).then(() => {
        $.ajax({
          type: "POST",
          url: window.location.href,
          data: datos,
          success: function (response) {
            var res = JSON.parse(response);
            if (res.success) {
              limpiarEstadoModal();
              mensaje("success");
              $("#tablaMarcas").DataTable().ajax.reload();
            } else if (res.invalido) {
              mensaje("yaExiste", res.invalido);
              $("#" + res.input).css("border", "1px solid rgb(158, 3, 3)").css("box-shadow", "0 0 15px rgb(158, 3, 3)");
            } else if (res.error) {
              mensaje("error");
            }
          }
        });
    });
  }

  $(document).on("click", ".btn-eliminarMarca", function () {
    $("#btn_deleteMarca").val($(this).data("id"));
    mensaje("deshabilitar");
  });

  function eliminar() {
    var datos = "id=" + $("#btn_deleteMarca").val() + "&accion=eliminarMarca";
    $.ajax({
      type: "POST",
      url: window.location.href,
      data: datos,
      success: function (response) {
        var res = JSON.parse(response);
        if (res.success) {
          mensaje("success");
          $("#tablaMarcas").DataTable().ajax.reload();
        } else {
          mensaje("error");
        }
      }
    });
  }

  function mensaje(accion, texto, funcion) {
    let config = { color: "white", background: "#000910", confirmButtonColor: "rgb(238, 191, 0)" };

    if (accion == "yaExiste") {
      Swal.fire({ ...config, title: "Atención", text: texto, icon: "warning" });
    } else if (accion == "errorC") {
      Swal.fire({ ...config, title: "Ups!", text: "Completa los campos!", icon: "error" });
    } else if (accion == "error") {
      Swal.fire({ ...config, title: "Ups!", text: "Error en el Servidor!", icon: "error" });
    } else if (accion == "pregunta") {
      Swal.fire({ ...config, title: "Estas Seguro!", text: texto, icon: "question", showCancelButton: true, confirmButtonText: "Confirmar" }).then((r) => { if (r.isConfirmed) funcion(); });
    } else if (accion == "deshabilitar") {
      Swal.fire({ ...config, title: "Estas Seguro!", text: "Eliminar esta Marca?", icon: "question", showCancelButton: true, confirmButtonText: "Eliminar" }).then((r) => { if (r.isConfirmed) eliminar(); });
    } else {
      Swal.fire({ ...config, title: "Listo!", text: "Éxito!", icon: "success", showConfirmButton: false, timer: 1500 });
    }
  }
});
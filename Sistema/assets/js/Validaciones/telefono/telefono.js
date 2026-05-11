$(document).ready(function () {
    let permisos = { registrar: false, modificar: false, eliminar: false };

   
    $.get("?pagina=telefono&permisos=true", function (data) {
        permisos = JSON.parse(data);

       
        if (permisos.registrar) {
            $("#btn_nuevo_telefono").show();
        } else {
            $("#btn_nuevo_telefono").hide();
        }

        
        inicializarTablaTelefono();
    });

    function inicializarTablaTelefono() {
        $("#Telefonotabla").DataTable({
            destroy: true,
            ajax: { 
                url: "?pagina=telefono&ajax=true&x=telefono", 
                dataSrc: "" 
            },
            columns: [
                { data: "id_telefono", visible: false },
                { data: "nombre_marca" }, 
                { data: "modelo" },
                { data: "almacenamiento" },
                { data: "ram" },
                { data: "imei" },
                {
                    data: null,
                    render: function (data, type, row) {
                        let btns = `<div class="btn-group" role="group">`;
                        
                       
                        if (permisos.modificar) {
                            btns += `<button class="btn btn-warning btn-modificar" 
                                        data-id="${row.id_telefono}" 
                                        data-id_marca="${row.id_marca}" 
                                        data-modelo="${row.modelo}" 
                                        data-almacenamiento="${row.almacenamiento}" 
                                        data-ram="${row.ram}" 
                                        data-imei="${row.imei}">
                                        <i class="bi bi-pencil-square"></i>
                                     </button>`;
                        }
                        
                       
                        if (permisos.eliminar) {
                            btns += `<button class="btn btn-danger btn-eliminar" 
                                        data-id="${row.id_telefono}">
                                        <i class="bi bi-trash3-fill"></i>
                                     </button>`;
                        }
                        
                       
                        if (!permisos.modificar && !permisos.eliminar) {
                            btns += `<span class="badge bg-secondary">Solo lectura</span>`;
                        }

                        return btns + `</div>`;
                    }
                }
            ],
            language: {
                processing: "Procesando...",
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                infoFiltered: "(filtrado de un total de _MAX_ registros)",
                loadingRecords: "Cargando...",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                paginate: { 
                    first: "Primero", 
                    previous: "Anterior", 
                    next: "Siguiente", 
                    last: "Último" 
                }
            }
        });
    }
});


$("#btn_nuevo_telefono").show();















function cargarMarcas() {
  $.get("?pagina=telefono&ajax=true&x=marcas", function (response) {
    try {
      const marcas = JSON.parse(response);
      let options = '<option value="" selected disabled>Seleccione una marca...</option>';
      marcas.forEach(m => {
        options += `<option value="${m.id_marca}">${m.nombre_marca}</option>`;
      });
      $("#marca, #marca_modificar").html(options);
    } catch (e) { console.error("Error cargando marcas:", e); }
  });
}

$(document).ready(function () {
  const SWAL_CFG = { background: "#000910", color: "white", confirmButtonColor: "#0d6efd" };
  
  
  cargarMarcas();

  function limpiarEstadoModal() {
    $('.modal').each(function() {
      const ins = bootstrap.Modal.getInstance(this);
      if(ins) ins.hide();
    });
    $(".modal-backdrop").remove();
    $("body").removeClass("modal-open").css("padding-right", "");
    $("input, select").css({ "border": "", "box-shadow": "" });
    $("#formularioRegistroTelefono")[0].reset();
    $("#formularioModificarTelefono")[0].reset();
  }

 
  $(document).on("click", "#btn_nuevo_telefono", function () {
    $("#formularioRegistroTelefono")[0].reset();
    new bootstrap.Modal('#modalRegistroTelefono').show();
  });

  $("#btn_registrar").click(function () {
    if (!$("#marca").val() || !$("#modelo").val() || !$("#imei").val()) {
      alertas("error", "Complete los campos obligatorios");
      return;
    }
    const datos = $("#formularioRegistroTelefono").serialize() + "&accion=registrarTelefono";
    procesarPeticion(datos, true);
  });


  $(document).on("click", ".btn-ver", function () {
    const d = $(this).data();
    $("#id_display").text("#" + d.id);
    $("#marca_consulta_text").text(d.marca);
    $("#modelo_consulta_text").text(d.modelo);
    $("#almacenamiento_consulta_text").text(d.almacenamiento);
    $("#ram_consulta_text").text(d.ram);
    $("#imei_consulta_text").text(d.imei);
    $("#ram_badge").text(d.ram + " RAM");
    new bootstrap.Modal('#modalConsultarTelefono').show();
  });

  
  $(document).on("click", ".btn-modificar", function () {
    const d = $(this).data();
    $("#id_telefono_modificar").val(d.id);
    $("#marca_modificar").val(d.id_marca); 
    $("#modelo_modificar").val(d.modelo);
    $("#almacenamiento_modificar").val(d.almacenamiento);
    $("#ram_modificar").val(d.ram);
    $("#imei_modificar").val(d.imei);
    new bootstrap.Modal('#modalModificarTelefono').show();
  });

  $("#modificarDatos").click(() => {
    alertas("pregunta", "¿Desea guardar los cambios?", "Confirmar Modificación", ejecutarModificacion);
  });

  function ejecutarModificacion() {
    const datos = $("#formularioModificarTelefono").serialize() + "&accion=modificarTelefono";
    procesarPeticion(datos);
  }

  
  $(document).on("click", ".btn-eliminar", function () {
    const id = $(this).data("id");
    alertas("pregunta", "¿Está seguro de eliminar este registro?", "Atención", () => {
      procesarPeticion("id=" + id + "&accion=eliminarTelefono");
    });
  });

  function procesarPeticion(datos, esRegistro = false) {
    Swal.fire({ title: "Procesando", ...SWAL_CFG, didOpen: () => Swal.showLoading() });

    $.ajax({
      type: "POST",
      url: window.location.href,
      data: datos,
      success: function (response) {
        Swal.close();
        try {
          const res = JSON.parse(response);
          if (res.success) {
            alertas("success");
            limpiarEstadoModal();
            $("#Telefonotabla").DataTable().ajax.reload(null, false);
          } else if (res.invalido) {
            Swal.fire({ ...SWAL_CFG, icon: "warning", title: "Atención", text: res.invalido });
            if (res.input) $(`#${res.input}`).css({ "border": "2px solid red", "box-shadow": "0 0 5px red" });
          } else {
            alertas("error", res.error || "Error en la operación");
          }
        } catch (e) { alertas("error", "Error en la respuesta del servidor"); }
      },
      error: () => { Swal.close(); alertas("error", "Error de comunicación"); }
    });
  }

  function alertas(accion, texto, titulo, funcion) {
    if (accion === "success") {
      Swal.fire({ ...SWAL_CFG, title: "¡Éxito!", icon: "success", timer: 1500, showConfirmButton: false });
    } else if (accion === "error") {
      Swal.fire({ ...SWAL_CFG, title: titulo || "Error", text: texto, icon: "error" });
    } else if (accion === "pregunta") {
      Swal.fire({
        ...SWAL_CFG, title: titulo, text: texto, icon: "question",
        showCancelButton: true, confirmButtonText: "Confirmar"
      }).then((result) => { if (result.isConfirmed) funcion(); });
    }
  }
});
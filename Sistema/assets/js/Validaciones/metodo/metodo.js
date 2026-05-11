window.obtenerPermisos = function() {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", window.location.href, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    xhr.onload = function() {
      if (xhr.status >= 200 && xhr.status < 300) {
        try {
          const respuesta = JSON.parse(xhr.responseText);
          const permisos = respuesta.array.find(
            (registro) => registro.nombre_modulo === respuesta.modulo
          );

          $.ajax({
            type: "POST",
            url: "models/registroBitacora.php",
            data: {
              accion: "bitacora",
              modulo: permisos.nombre_modulo,
              usuario: respuesta.usuario
            },
            error: function() {}
          });
          resolve(permisos);
        } catch (e) {
          reject(e);
        }
      } else {
        reject(new Error("La solicitud falló con el estado: " + xhr.status));
      }
    };
    xhr.onerror = function() {
      reject(new Error("Error de red al intentar la solicitud."));
    };
    xhr.send("accion=" + encodeURIComponent("permisos"));
  });
};

let metodoPagosTable;

obtenerPermisos().then((permisosObtenidos) => {
  if (permisosObtenidos && (permisosObtenidos.control_total === 1 || permisosObtenidos.registrar === 1)) {
    $("#btn_registro_metodo").show();
  } else {
    $("#btn_registro_metodo").hide();
  }

  if (permisosObtenidos && (permisosObtenidos.control_total === 1 || permisosObtenidos.listar === 1)) {
    metodoPagosTable = $("#Metodopagotabla").DataTable({
      paging: true,
      searching: true,
      ordering: true,
      info: true,
      ajax: {
        url: "?pagina=metodo&ajax=true&x=metodo", 
        dataSrc: "",
      },
      columns: [
        { data: "id_metodopago",visible:false }, 
        { data: "nombre_metodopago" }, 
        { 
          data: null,
          render: function(data, type, row) {
            let botonesHTML = "";
            const canModificar = permisosObtenidos.control_total === 1 || permisosObtenidos.modificar === 1;
            const canEliminar = permisosObtenidos.control_total === 1 || permisosObtenidos.eliminar === 1;

            if (canModificar) {
              botonesHTML += `
                <button type="button" class="btn btn-warning btn-modificar-metodo"
                  data-id="${row.id_metodopago}"
                  data-nombre="${row.nombre_metodopago}">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>`;
            }
            if (canEliminar) {
              botonesHTML += `
                <button type="button" class="btn btn-danger btn-eliminar-metodo" data-id="${row.id_metodopago}">
                  <i class="fa-solid fa-trash" style="color: #000000;"></i>
                </button>`;
            }
            return botonesHTML;
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
  } else {
    $(".table-responsive").hide();
  }
}).catch(() => {
  $(".table-responsive").hide();
});

$(document).ready(function() {
  const MIN_SIZE = 3;
  const MAX_SIZE = 20;

  // --- VALIDACIONES ---
  function validateInput(inputElement, buttonElement, feedbackElement, initialLoad = false) {
    let value = inputElement.val();
    if (!initialLoad) {
      feedbackElement.text('');
      inputElement.removeClass("is-valid is-invalid");
    }

    const regex = /[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g;
    const cleanValue = value.replace(regex, "");
    if (value !== cleanValue) inputElement.val(cleanValue);
    
    value = cleanValue.trim();

    if (value.length >= MIN_SIZE && value.length <= MAX_SIZE) {
      inputElement.addClass("is-valid");
      buttonElement.prop("disabled", false);
      return true;
    } else {
      inputElement.addClass("is-invalid");
      buttonElement.prop("disabled", true);
      if (value.length === 0) feedbackElement.text("Este campo es obligatorio.");
      else feedbackElement.text(`Debe tener entre ${MIN_SIZE} y ${MAX_SIZE} caracteres.`);
      return false;
    }
  }

  const $registroInput = $('#nombre_metodopago');
  const $modificarInput = $('#nombreModificar');

  // --- EVENTOS MODALES ---
  $(document).on("click", ".btn-modificar-metodo", function() {
    const id = $(this).data("id");
    const nombre = $(this).data("nombre");
    $("#idModificar").val(id);
    $("#nombreModificar").val(nombre);
    $("#modalModificar").modal("show");
    validateInput($modificarInput, $("#modificarDatos"), $("#metodoModificarFeedback"), true);
  });

  $registroInput.on('input', () => validateInput($registroInput, $("#guardarMetodopago"), $("#metodoFeedback")));
  $modificarInput.on('input', () => validateInput($modificarInput, $("#modificarDatos"), $("#metodoModificarFeedback")));

  // --- ACCIONES (REGISTRAR, MODIFICAR, ELIMINAR) ---

  $("#guardarMetodopago").on("click", function() {
    const checked = $("input[name='tipoCuenta']:checked").length > 0;
    if ($registroInput.hasClass('is-valid') && checked) {
      showSweetAlert("pregunta1");
    } else {
      showSweetAlert("errorC");
    }
  });

  window.registerData = function() {
    const data = {
      nombre: $registroInput.val().trim(),
      cuenta: $("input[name='tipoCuenta']:checked").val(),
      accion: "registrar"
    };

    ejecutarAjax(data, "#modalRegistroMetodopago");
  };

  $("#modificarDatos").on("click", function() {
    if ($modificarInput.hasClass('is-valid')) {
      showSweetAlert("pregunta2");
    } else {
      showSweetAlert("errorC");
    }
  });

  window.modifyData = function() {
    const data = {
      id: $("#idModificar").val(),
      nombre: $modificarInput.val().trim(),
      accion: "modificar"
    };
    ejecutarAjax(data, "#modalModificar");
  };

  $(document).on("click", ".btn-danger", function() {
    const id = $(this).data("id");
    $("#id_MetodoPago_delete").val(id);
    showSweetAlert("pregunta3");
  });

  window.deleteData = function() {
    const data = {
      id: $("#id_MetodoPago_delete").val(),
      accion: "eliminar"
    };
    ejecutarAjax(data, null);
  };

  // --- FUNCIÓN AJAX GENÉRICA ---
  function ejecutarAjax(datos, modalId) {
    showProcessingAlert();
    $.ajax({
      type: "POST",
      url: "?pagina=metodo",
      data: datos,
      dataType: "json",
      success: function(res) {
        if (res.success) {
          if (modalId) $(modalId).modal("hide");
          $('.modal-backdrop').remove();
          $('body').removeClass('modal-open');
          
          showSweetAlert("success").then(() => {
            if (metodoPagosTable) metodoPagosTable.ajax.reload();
          });
        } else {
          showSweetAlert("invalido", res.invalido || res.error || "Error desconocido");
        }
      },
      error: function(xhr) {
        console.error(xhr.responseText);
        showSweetAlert("error");
      }
    });
  }

  // --- SWEET ALERTS ---
  const commonSwalMixin = Swal.mixin({
    color: "white",
    background: "#000910",
    confirmButtonColor: "rgb(238, 191, 0)",
  });

  function showProcessingAlert() {
    Swal.fire({
      title: "Procesando!",
      timerProgressBar: true,
      didOpen: () => { Swal.showLoading(); },
      background: "#000910",
      color: "white",
      allowOutsideClick: false
    });
  }

  function showSweetAlert(action, message = "") {
    const config = {
      pregunta1: { title: "¿Registrar?", text: "¿Desea guardar este método?", icon: "question", cb: registerData },
      pregunta2: { title: "¿Modificar?", text: "¿Desea guardar los cambios?", icon: "question", cb: modifyData },
      pregunta3: { title: "¿Eliminar?", text: "¿Desea eliminar este registro?", icon: "warning", cb: deleteData }
    };

    if (config[action]) {
      return commonSwalMixin.fire({
        title: config[action].title,
        text: config[action].text,
        icon: config[action].icon,
        showCancelButton: true,
        confirmButtonText: "Sí, confirmar"
      }).then((result) => { if (result.isConfirmed) config[action].cb(); });
    }

    const simpleAlerts = {
      success: { title: "¡Listo!", icon: "success", timer: 1500, showConfirmButton: false },
      errorC: { title: "Campos incompletos", icon: "error" },
      error: { title: "Error de servidor", icon: "error" },
      invalido: { title: "Atención", text: message, icon: "warning" }
    };

    return commonSwalMixin.fire(simpleAlerts[action]);
  }
});
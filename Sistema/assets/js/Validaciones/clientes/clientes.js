$(document).ready(function () {
    
    let permisos = { registrar: false, modificar: false, eliminar: false, consultar: false, control_total: false };

    
    $.get("?pagina=clientes&permisos=true", function (data) {
        permisos = JSON.parse(data);

       
        if (permisos.control_total || permisos.registrar) {
            $(".text-right button").show();
        } else {
            $(".text-right button").hide();
        }

       
        if (permisos.control_total || permisos.consultar) {
            $("#btn_verInactivos").show();
        } else {
            $("#btn_verInactivos").hide();
        }

        
        cargarTablaClientes();
        cargarTablaInactivos();
    });

    function cargarTablaClientes() {
        $("#clientestabla").DataTable({
            destroy: true,
            ajax: {
                url: "?pagina=clientes&ajax=true",
                dataSrc: "",
            },
            columns: [
                { data: "cedula_cliente" },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `${row.nombre} ${row.apellido}`;
                    },
                },
                { data: "sexo" },
                {
                    data: null,
                    render: function (data, type, row) {
                        let btns = `<div class="btn-group" role="group">`;

                        
                        if (permisos.control_total || permisos.consultar) {
                            btns += `<button type="button" class="btn btn-info btn-verDatos" data-id="${row.cedula_cliente}"><i class="bi bi-eye-fill"></i></button>`;
                        }

                        
                        if (permisos.control_total || permisos.modificar) {
                            btns += `<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalModificar" data-id="${row.cedula_cliente}"><i class="fa-solid fa-pen-to-square"></i></button>`;
                        }

                        
                        if (permisos.control_total || permisos.eliminar) {
                            btns += `<button type="button" class="btn btn-danger btn-eliminar" data-id="${row.cedula_cliente}" data-nombre="${row.nombre} ${row.apellido}"><i class="fa-solid fa-trash-can"></i></button>`;
                        }

                        btns += `</div>`;
                        return btns === `<div class="btn-group" role="group"></div>` ? '<span class="badge bg-secondary">Solo lectura</span>' : btns;
                    },
                },
            ],
            pageLength: 4,
            lengthMenu: [[4, 8, 12, 16], ["4", "8", "12", "16"]],
            columnDefs: [{ className: "dt-head-center", targets: "_all" }],
            language: {
                search: "Buscar:",
                zeroRecords: "No se encontraron resultados",
                info: "Mostrando página _PAGE_ de _PAGES_",
                paginate: { next: "Siguiente", previous: "Anterior" }
            }
        });
    }

    function cargarTablaInactivos() {
        $("#tablaInactivos").DataTable({
            destroy: true,
            ajax: {
                url: "?pagina=clientes&ajax=true&x=inactivos",
                dataSrc: "",
            },
            columns: [
                { data: "cedula_cliente" },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `${row.nombre} ${row.apellido}`;
                    },
                },
                { data: "sexo" },
                {
                    data: null,
                    render: function (data, type, row) {
                      
                        if (permisos.control_total || permisos.eliminar || permisos.modificar) {
                            return `<button type="button" class="btn btn-danger btn-activar" data-id="${row.cedula_cliente}" data-nombre="${row.nombre} ${row.apellido}"><i class="bi bi-x"></i></button>`;
                        }
                        return '<i class="bi bi-lock-fill"></i>';
                    },
                },
            ],
            pageLength: 4,
            columnDefs: [{ className: "dt-head-center", targets: "_all" }],
            language: {
                search: "Buscar Inactivos:",
                zeroRecords: "No hay clientes inactivos",
                paginate: { next: "Siguiente", previous: "Anterior" }
            }
        });
    }
});







// -----------------------------------------------------------------------------------------------------------
$(document).ready(function () {
  $("#modalRegistroCliente").on("show.bs.modal", function () {
    $.ajax({
      type: "GET",
      url: "", 
      data: { obtener_tasa: true },
      success: function (response) {
        var res = JSON.parse(response);
        if (res.tasa) {
          $("#tasa_bcv").val(res.tasa.toString().replace(',', '.'));
        }
      }
    });
  });

  $(document).on("input", "#ingreso_bs, #tasa_bcv", function () {
    var bsVal = $("#ingreso_bs").val().toString().replace(',', '.');
    var tasaVal = $("#tasa_bcv").val().toString().replace(',', '.');
    var bs = parseFloat(bsVal) || 0;
    var tasa = parseFloat(tasaVal) || 1;
    var usd = (bs / tasa).toFixed(2);
    $("#calc_usd").text(usd);
    $("#ingresos_mensuales").val(usd); 
  });

  $("#guardarCliente").on("click", function () {
    if (validarDatos()) {
      alertas(
        "pregunta",
        "¿Estás seguro de los datos ingresados?",
        "¡Espera un momento!",
        registrar
      );
    } else {
      alertas("warning", "Debes completar todos los campos correctamente", "¡Lo Siento!");
    }
  });

  function registrar() {
    var nombre = $("#nombre").val();
    var apellido = $("#apellido").val();
    var cedula = $("#prefijo").val() + $("#cedula").val();
    var telefono = $("#operadora").val() + $("#telefono").val();
    var direccion = $("#direccion").val();
    var correo = $("#correo").val();
    var sexo = $("#sexo").val();
    var fecha = $("#fecha").val();
    var ingresos_mensuales = $("#ingresos_mensuales").val();
    var tipo_residencia = $("#tipo_residencia").val();
    var profesion = $("#profesion").val();
    var estado_civil = $("#estado_civil").val();
    var carga_familiar = $("#carga_familiar").val();
    var ocupacion = $("#ocupacion").val();

    let timerInterval;
    Swal.fire({
      title: "Procesando!",
      html: "Guardando información en el sistema...",
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
            nombre: nombre,
            apellido: apellido,
            cedula: cedula,
            fecha: fecha,
            correo: correo,
            telefono: telefono,
            direccion: direccion,
            sexo: sexo,
            ingresos_mensuales: ingresos_mensuales,
            tipo_residencia: tipo_residencia,
            profesion: profesion,
            estado_civil: estado_civil, // AGREGADO: Ahora PHP lo reconocerá
            carga_familiar: carga_familiar,
            ocupacion: ocupacion,       // AGREGADO: Ahora PHP lo reconocerá
            accion: "registrar",
          },
          success: function (response) {
            var res = JSON.parse(response);

            if (res.success) {
              $("#modalRegistroCliente").modal("hide");
              $("#formRegistroCliente")[0].reset();
              $("#calc_usd").text("0.00");
              limpiarEstilos();
              alertas("success", res.success);
              $("#clientestabla").DataTable().ajax.reload();
            } else if (res.error) {
              alertas("error", res.error, "¡Ups!");
            } else if (res.incompleto) {
              alertas("errorC", "¡Hay datos incompletos!", "Lo Siento!");
              resaltarErrores(res.input, "incompleto");
            } else if (res.invalido) {
              alertas("warning", res.invalido, "Dato Inválido");
              resaltarErrores(res.input, "invalido");
            }
          },
          error: function () {
            alertas("error", "Error en el servidor al registrar.", "¡Error!");
          },
        });
      }
    });
  }

  function resaltarErrores(inputError, tipo) {
    var inputs = "#formRegistroCliente input, #formRegistroCliente select, #formRegistroCliente textarea";
    $(inputs).css("border", "1px solid rgb(14, 184, 37)").css("box-shadow", "0 0 5px rgb(14, 184, 37)");

    if (tipo === "incompleto") {
      var array = inputError.slice(0, -1).split("-");
      $.each(array, function (index, value) {
        aplicarRojo("#" + value);
      });
    } else {
      aplicarRojo("#" + inputError);
    }
  }

  function aplicarRojo(selector) {
    $(selector).css("border", "1px solid rgb(158, 3, 3)").css("box-shadow", "0 0 10px rgb(158, 3, 3)");
    if (selector === "#telefono") $("#operadora").css("border", "1px solid rgb(158, 3, 3)").css("box-shadow", "0 0 10px rgb(158, 3, 3)");
    if (selector === "#cedula") $("#prefijo").css("border", "1px solid rgb(158, 3, 3)").css("box-shadow", "0 0 10px rgb(158, 3, 3)");
  }

  function limpiarEstilos() {
    $("input, textarea, select").css("border", "1px solid #ced4da").css("box-shadow", "none");
  }

  $("#btn_cancel_register, .btn-close").click(function () {
    $("#formRegistroCliente")[0].reset();
    $("#calc_usd").text("0.00");
    limpiarEstilos();
  });

  function validarDatos() {
    var valid = true;
    $("#formRegistroCliente [required]").each(function() {
        if ($(this).val() === "" || $(this).val() === null) {
            valid = false;
        }
    });
    return valid;
  }



  //--------------------------------------------------------------------------------------------

  const calcularEdad = (fechaN) => {
    var edad = 0;
    const fechaActual = new Date();
    const añoActual = parseInt(fechaActual.getFullYear());
    const mesActual = parseInt(fechaActual.getMonth()) + 1;
    const diaActual = parseInt(fechaActual.getDate());

    const añoNacimiento = parseInt(String(fechaN).substring(0, 4));
    const mesNacimiento = parseInt(String(fechaN).substring(5, 7));
    const diaNacimiento = parseInt(String(fechaN).substring(8, 10));

    edad = añoActual - añoNacimiento;
    if (mesActual < mesNacimiento) {
      edad--;
    } else if (mesActual == mesNacimiento) {
      if (diaActual < diaNacimiento) {
        edad--;
      }
    }

    return edad;
  };

$(document).on("click", ".btn-verDatos", function () {
    var id = $(this).data("id");

    // Agregamos los nuevos IDs a la limpieza inicial
    $("#VerNombre, #VerApellido, #VerCedula, #VerTelefono, #VerCorreo, #VerSexo, #VerFecha, #VerDireccion, #VerIngresos, #VerTipoResidencia, #VerProfesion, #VerEstadoCivil, #VerCargas, #VerOcupacion").text("Cargando...");

    $.ajax({
      type: "POST",
      url: "", 
      data: {
        id: id,
        accion: "consultar",
      },
      success: function (response) {
        var res = JSON.parse(response);

        if (res.error) {
          alertas("warning", res.error, "Lo Siento!");
          return;
        } 
        
        if (!res.cedula_cliente) {
          alertas("warning", "No se encontraron registros en el sistema", "Lo Siento!");
          return;
        }

        var operadora = "";
        var telefono = "";
        if (res.telefono) {
            if (res.telefono[0] !== "0") {
              operadora = "0" + res.telefono.substring(0, 3);
              telefono = res.telefono.substring(3);
            } else {
              operadora = res.telefono.substring(0, 4);
              telefono = res.telefono.substring(4);
            }
        }

        // Datos Básicos
        $("#VerNombre").text(res.nombre);
        $("#VerApellido").text(res.apellido);
        $("#VerCedula").text(res.cedula_cliente);
        $("#VerTelefono").text(operadora + "-" + telefono);
        $("#VerCorreo").text(res.correo);
        $("#VerSexo").text(res.sexo);
        $("#VerFecha").text(calcularEdad(res.fecha_nacimiento) + " años");
        $("#VerDireccion").text(res.residencia);
        
        // Datos Socioeconómicos (LAS QUE FALTABAN)
        $("#VerTipoResidencia").text(res.tipo_residencia);
        $("#VerProfesion").text(res.profesion); // Categoría Laboral
        $("#VerEstadoCivil").text(res.estado_civil);
        $("#VerCargas").text(res.carga_familiar + " pers.");
        $("#VerOcupacion").text(res.ocupacion || "No especificada");
        
        if($("#VerIngresos").length > 0) {
            $("#VerIngresos").text("$ " + parseFloat(res.ingresos_mensuales).toFixed(2));
        }

        $("#modalVerDatos").modal("show");
      },
      error: function() {
        alertas("error", "Error de conexión con el servidor", "¡Error!");
      }
    });
});

$("#btn_salir_ver, .btn-close").click(function () {
    $("#modalVerDatos").modal("hide");
});


  $(document).on("click", ".btn-warning", function () {
    var id = $(this).data("id");

    $.ajax({
      type: "POST",
      url: "",
      data: {
        id: id,
        accion: "consultar",
      },
      success: function (response) {
        var res = JSON.parse(response);

        if (res.error) {
          alertas("warning", res.error, "Lo Siento!");
          return;
        } else if (res.incompleto) {
          alertas("warning", res.incompleto, "Lo Siento!");
          return;
        } else if (res.invalido) {
          alertas("warning", res.invalido, "Lo Siento!");
          return;
        } else if (res.length === 0) {
          alertas(
            "warning",
            "No se encontraron registros en el sistema",
            "Lo Siento!"
          );
          return;
        }

        var operadora = "";
        var telefono = "";
        if (res.telefono[0] !== "0") {
          operadora = "0" + res.telefono.substring(0, 3);
          telefono = res.telefono.substring(3);
        } else {
          operadora = res.telefono.substring(0, 4);
          telefono = res.telefono.substring(4);
        }

        var modal = $("#modalModificar");

        modal.find(".modal-body #clienteId").val(id);
        modal.find(".modal-body #nombreModificar").val(res.nombre);
        modal.find(".modal-body #apellidoModificar").val(res.apellido);
        modal.find(".modal-body #correoModificar").val(res.correo);
        modal.find(".modal-body #telefonoModificar").val(telefono);
        modal.find(".modal-body #operadoraModificar").val(operadora);
        modal.find(".modal-body #direccionModificar").val(res.residencia);
        modal.find(".modal-body #cedulaModificar").val(res.cedula_cliente);
        modal.find(".modal-body #sexoModificar").val(res.sexo);

        modal.modal("show");
      },
    });
  });

  $("#modificarDatos").on("click", function () {
    if (validarDatosModificar()) {
      alertas(
        "pregunta",
        "Estas seguro de los atos ingresados?",
        "Espera un momento!",
        modificar
      );
    } else {
      alertas("errorC", "Debes completar todos los campos.", "Lo Siento!");
    }
  });

  function modificar() {
    var nombre = $("#nombreModificar").val();
    var apellido = $("#apellidoModificar").val();
    var cedula = $("#cedulaModificar").val();
    var correo = $("#correoModificar").val();
    var telefono =
      $("#operadoraModificar").val() + $("#telefonoModificar").val();
    var direccion = $("#direccionModificar").val();
    var sexo = $("#sexoModificar").val();

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
            nombre: nombre,
            apellido: apellido,
            cedula: cedula,
            telefono: telefono,
            correo: correo,
            direccion: direccion,
            sexo: sexo,
            accion: "modificar",
          },
          success: function (response) {
            var res = JSON.parse(response);

            if (res.success) {
              $("#modalModificar").modal("hide");
              $("#formModificar")[0].reset();
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

              $("#clientestabla").DataTable().ajax.reload();
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
            }
          },
          error: function (xhr, status, error) {
            alertas("error", "Error en el servidor al modificar.", "¡Error!");
          },
        });
      }
    });
  }

  $(document).ready(function () {
    
    // --- CONTROL DE VISTA (MOSTRAR/OCULTAR) ---
    $("#btn_verInactivos").click(function () {
        $("#container_clientes").addClass("active");
    });

    $("#btn_salir").on("click", function () {
        $("#container_clientes").removeClass("active");
    });

    // --- LÓGICA DE CAMBIO DE ESTADO (ACTIVAR/INACTIVAR) ---

    // Botón de Inactivar (En tabla principal)
    $(document).on("click", ".btn-eliminar", function () {
        var id = $(this).data("id");
        var nombre = $(this).data("nombre");
        
        alertas(
            "eliminar",
            "¿Estás seguro de inactivar al cliente '" + nombre + "'?",
            "¡Atención!",
            function() { cambiarEstado(id, "inactivo"); }
        );
    });

    // Botón de Activar (En tabla de inactivos)
    $(document).on("click", ".btn-activar", function () {
        var id = $(this).data("id");
        var nombre = $(this).data("nombre");
        
        alertas(
            "pregunta",
            "¿Desea activar nuevamente al cliente '" + nombre + "'?",
            "Restaurar Cliente",
            function() { cambiarEstado(id, "activo"); }
        );
    });

    function cambiarEstado(id, nuevoEstado) {
        $.ajax({
            type: "POST",
            url: "", // Tu controlador actual
            data: {
                id: id,
                estado: nuevoEstado,
                accion: "eliminar" // Mantengo "eliminar" porque así lo tienes en tu backend
            },
            success: function (response) {
                try {
                    var res = JSON.parse(response);
                    if (res.success) {
                        alertas("success");
                        // Recargamos ambas tablas para que el cliente "salte" de una a otra
                        $("#clientestabla").DataTable().ajax.reload();
                        $("#tablaInactivos").DataTable().ajax.reload();
                    } else {
                        alertas("error", res.error, "Error");
                    }
                } catch (e) {
                    console.error("Error parseando respuesta:", e);
                }
            }
        });
    }
});
  //--------------------------------------------------------------------------------------------
  function validarDatos() {
    var nombre = $("#nombre").val();
    var apellido = $("#apellido").val();
    var cedula = $("#prefijo").val() + $("#cedula").val();
    var correo = $("#correo").val();
    var telefono = $("#operadora").val() + $("#telefono").val();
    var direccion = $("#direccion").val();
    var sexo = $("#sexo").val();
    var fecha = $("#fecha").val();

    return (
      nombre &&
      apellido &&
      cedula &&
      correo &&
      telefono &&
      direccion &&
      sexo &&
      fecha
    );
  }
  //--------------------------------------------------------------------------------------------
  function validarDatosModificar() {
    var nombre = $("#nombreModificar").val();
    var apellido = $("#apellidoModificar").val();
    var cedula = $("#cedulaModificar").val();
    var correo = $("#correoModificar").val();
    var telefono =
      $("#operadoraModificar").val() + $("#telefonoModificar").val();
    var direccion = $("#direccionModificar").val();
    var sexo = $("#sexoModificar").val();

    return (
      nombre && apellido && cedula && correo && telefono && direccion && sexo
    );
  }
  //--------------------------------------------------------------------------------------------
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

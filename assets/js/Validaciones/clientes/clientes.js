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
    });
    



  
$.ajax({
    type: "GET",
    url: "?pagina=clientes&obtener_tasa=true",
    success: function (response) {
     
        var res = typeof response === "object" ? response : JSON.parse(response);
        
        
        if (res.success && res.tasa > 0) {
            $("#tasa_bcv").val(res.tasa.toString().replace(',', '.'));
        } else {
            $("#tasa_bcv").val("0.00");
        }
    }
});
    function cargarTablaClientes() {
        $("#clientestabla").DataTable({
            destroy: true,
            ajax: {
                url: "?pagina=clientes&ajax=true",
                dataSrc: "",
            },
            columns: [
                { data: "cedula_persona" },
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
                        let esActivo = (row.estado === 'activo');
                        return esActivo 
                            ? '<span class="badge bg-success">Activo</span>' 
                            : '<span class="badge bg-danger">Inactivo</span>';
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        let btns = `<div class="btn-group" role="group">`;

                        if (permisos.control_total || permisos.consultar) {
                            btns += `<button type="button" class="btn btn-info btn-verDatos m-2" data-id="${row.cedula_persona}"><i class="bi bi-eye-fill"></i></button>`;
                        }

                        if (permisos.control_total || permisos.modificar) {
                            btns += `<button type="button" class="btn btn-warning m-2" data-toggle="modal" data-target="#modalModificar" data-id="${row.cedula_persona}"><i class="fa-solid fa-pen-to-square"></i></button>`;
                        }

                        if (permisos.control_total || permisos.registrar) {
                            if (row.ingresos_mensuales === null || row.ingresos_mensuales === undefined) {
                                btns += `<button type="button" class="btn btn-primary btn-agregarPerfil m-2" data-toggle="modal" data-target="#modalRegistroPerfilFinanciero" data-id="${row.cedula_persona}"><i class="bi bi-wallet2"></i></button>`;
                            }
                        }

                        if (permisos.control_total || permisos.eliminar) {
                            let esActivo = (row.estado == 1 || row.estado === 'activo');
                            
                            if (esActivo) {
                                btns += `<button type="button" class="btn btn-danger btn-cambiar-estado m-2" data-id="${row.cedula_persona}" data-nombre="${row.nombre} ${row.apellido}" data-accion="inactivo">
                                                <i class="bi bi-person-fill-slash" style="font-size: 1.25rem;" title="Inhabilitar Cliente"></i>
                                            </button>`;
                            } else {
                                btns += `<button type="button" class="btn btn-success btn-cambiar-estado m-2" data-id="${row.cedula_persona}" data-nombre="${row.nombre} ${row.apellido}" data-accion="activo">
                                                <i class="bi bi-person-check p-0" style="font-size: 1.25rem;" title="Habilitar Cliente"></i>
                                            </button>`;
                            }
                        }

                        btns += `</div>`;
                        return btns === `<div class="btn-group" role="group"></div>` ? '<span class="badge bg-secondary">Solo lectura</span>' : btns;
                    },
                },
            ],
            pageLength: 4,
            lengthMenu: [[4, 8, 12, 16], ["4", "8", "12", "16"]],
            columnDefs: [
                {
                    targets: "_all",
                    className: "text-center align-middle"
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
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                },
                aria: {
                    sortAscending: ": Activar para ordenar la columna de manera ascendente",
                    sortDescending: ": Activar para ordenar la columna de manera descendente"
                }
            }
        });
    }

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



$(document).on("input", "#ingreso_bs_perfil", function () {
    var bs = parseFloat($(this).val().toString().replace(',', '.')) || 0;
    var tasa = parseFloat($("#tasa_bcv_perfil").val()) || 1;
    
  
    var usd = tasa > 0 ? (bs / tasa).toFixed(2) : "0.00";

    $("#calc_usd_perfil").text(usd);
    $("#ingresos_mensuales").val(usd); 
});

  $("#guardarCliente").off("click.validacionCliente").on("click.validacionCliente", function () {
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
    var fecha = $("#fecha_nacimiento").val();
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
                    estado_civil: estado_civil,
                    carga_familiar: carga_familiar,
                    ocupacion: ocupacion,       
                    accion: "registrar",
                },
                success: function (response) {
                    var res = JSON.parse(response);

                    if (res.success) {
                       
                        $("#modalRegistroCliente").modal("hide");
                        
                        
                        setTimeout(() => {
                            $(".modal-backdrop").remove();
                            $("body").removeClass("modal-open");
                            $("body").css("padding-right", "");
                        }, 500);

                        
                        $("#formRegistroCliente")[0].reset();
                        $("#calc_usd").text("0.00");
                        limpiarEstilos();
                        
                        alertas("success", res.success);
                        $("#clientestabla").DataTable().ajax.reload();

                    } else if (res.error) {
                      mostrarAlertaDuplicado(res.error);
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

  function validarFechaNacimientoCliente(fechaStr) {
    const hoy = new Date();
    const fechaNac = new Date(fechaStr + 'T00:00:00');

    if (!fechaStr || isNaN(fechaNac.getTime())) {
      return false;
    }

    let edad = hoy.getFullYear() - fechaNac.getFullYear();
    const mes = hoy.getMonth() - fechaNac.getMonth();

    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
      edad--;
    }

    return fechaNac <= hoy && edad >= 18;
  }

  function validarDatos() {
    const nombre = ($("#nombre").val() || "").trim();
    const apellido = ($("#apellido").val() || "").trim();
    const cedula = ($("#cedula").val() || "").trim();
    const prefijo = ($("#prefijo").val() || "").trim();
    const correo = ($("#correo").val() || "").trim();
    const telefono = ($("#telefono").val() || "").trim();
    const direccion = ($("#direccion").val() || "").trim();
    const fecha = ($("#fecha_nacimiento").val() || "").trim();
    const sexo = ($("#sexo").val() || "").trim();
    const tipoResidencia = ($("#tipo_residencia").val() || "").trim();
    const estadoCivil = ($("#estado_civil").val() || "").trim();
    const profesion = ($("#profesion").val() || "").trim();
    const cargaFamiliar = ($("#carga_familiar").val() || "").trim();
    const ocupacion = ($("#ocupacion").val() || "").trim();
    const ingresoBs = ($("#ingreso_bs").val() || "").trim();
    const operadora = ($("#operadora").val() || "").trim();

    if (!/^[VE]-$/.test(prefijo) || !/^[0-9]{7,8}$/.test(cedula)) return false;
    if (!nombre || nombre.length < 2 || nombre.length > 35 || !/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(nombre)) return false;
    if (!apellido || apellido.length < 2 || apellido.length > 35 || !/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(apellido)) return false;
    if (!fecha || !validarFechaNacimientoCliente(fecha)) return false;
    if (!sexo) return false;
    if (!correo || correo.length > 45 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) return false;
    if (!operadora || !/^[0-9]{3,4}$/.test(operadora)) return false;
    if (!telefono || !/^[0-9]{6,7}$/.test(telefono)) return false;
    if (!direccion || direccion.length < 5 || direccion.length > 50) return false;
    if (!tipoResidencia || !estadoCivil || !profesion) return false;
    if (cargaFamiliar === "" || isNaN(cargaFamiliar) || Number(cargaFamiliar) < 0 || Number(cargaFamiliar) > 20) return false;
    if (!ocupacion || ocupacion.length < 5 || ocupacion.length > 60 || !/^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9\s.,/-]+$/.test(ocupacion)) return false;
    if (!ingresoBs || !/^[0-9]+([.,][0-9]+)?$/.test(ingresoBs) || Number(ingresoBs.replace(",",".")) <= 0) return false;

    return true;
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
        
        if (!res.cedula_persona) {
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

        
        $("#VerNombre").text(res.nombre || "No especificado");
        $("#VerApellido").text(res.apellido || "No especificado");
        $("#VerCedula").text(res.cedula_persona || "No especificado");
        $("#VerTelefono").text(res.telefono ? (operadora + "-" + telefono) : "No especificado");
        $("#VerCorreo").text(res.correo || "No especificado");
        $("#VerSexo").text(res.sexo || "No especificado");
        $("#VerFecha").text(res.fecha_nacimiento ? (calcularEdad(res.fecha_nacimiento) + " años") : "No especificado");
        $("#VerDireccion").text(res.direccion || "No especificada");
        
        $("#VerTipoResidencia").text(res.tipo_residencia || "No especificado");
        $("#VerProfesion").text(res.profesion || "No especificada"); 
        $("#VerEstadoCivil").text(res.estado_civil || "No especificado");
        $("#VerCargas").text((res.carga_familiar !== null && res.carga_familiar !== undefined && res.carga_familiar !== "") ? res.carga_familiar + " pers." : "Sin dato");
        $("#VerOcupacion").text(res.ocupacion || "No especificada");
        
        if($("#VerIngresos").length > 0) {
            let ingresos = parseFloat(res.ingresos_mensuales);
            if (!isNaN(ingresos)) {
                $("#VerIngresos").text("$ " + ingresos.toFixed(2));
            } else {
                $("#VerIngresos").text("Sin registro");
            }
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

$(document).on("click", ".btn-cambiar-estado", function () {
        var id = $(this).data("id");
        var nombre = $(this).data("nombre");
        var nuevaAccion = $(this).data("accion");

        let textoMensaje = (nuevaAccion === 'inactivo') 
            ? "¿Estás seguro de inhabilitar al cliente '" + nombre + "'?" 
            : "¿Estás seguro de habilitar al cliente '" + nombre + "'?";

        alertas("eliminar", textoMensaje, "Espera un momento!", estado, {id: id, nuevaAccion: nuevaAccion});
    });

    function estado(datos) {
        let timerInterval;
        Swal.fire({
            title: "Procesando!",
            timer: 1500,
            color: "white",
            background: "#000910",
            timerProgressBar: true,
            didOpen: () => { Swal.showLoading(); },
            willClose: () => { clearInterval(timerInterval); },
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                $.ajax({
                    type: "POST",
                    url: "",
                    data: {
                        id: datos.id,
                        estado: datos.nuevaAccion,
                        accion: "eliminar"
                    },
                    success: function (response) {
                        var res = JSON.parse(response);

                        if (res.success) {
                            $("#clientestabla").DataTable().ajax.reload();
                            alertas("success");
                        } else if (res.error) {
                          mostrarAlertaDuplicado(res.error);
                        }
                    },
                    error: function () {
                        mensaje("error");
                    },
                });
            }
        });
    }


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
        modal.find(".modal-body #direccionModificar").val(res.direccion || res.residencia);
        modal.find(".modal-body #cedulaModificar").val(res.cedula_persona);
        modal.find(".modal-body #sexoModificar").val(res.sexo);
        modal.find(".modal-body #tipo_residenciaModificar").val(res.tipo_residencia);
        modal.find(".modal-body #estado_civilModificar").val(res.estado_civil);
        modal.find(".modal-body #profesionModificar").val(res.profesion);
        modal.find(".modal-body #carga_familiarModificar").val(res.carga_familiar);
        modal.find(".modal-body #ocupacionModificar").val(res.ocupacion);
        modal.find(".modal-body #ingresosModificar").val(res.ingresos_mensuales);

        modal.modal("show");
      },
    });
  });

  $("#modificarDatos").off("click.validacionModCliente").on("click.validacionModCliente", function () {
    if (validarDatosModificar()) {
      alertas(
        "pregunta",
        "Estas seguro de los atos ingresados?",
        "Espera un momento!",
        modificar
      );
    } else {
      alertas("errorC", "Debes completar todos los campos y cumplir con el formato solicitado.", "Lo Siento!");
    }
  });





$(document).on("click", ".btn-agregarPerfil", function () {
    var id = $(this).attr("data-id");
    $("#cedulaPerfil").val(id);
    $("#cedulaPerfilVisual").val(id);

    $("#ingreso_bs_perfil").val("");
    $("#calc_usd_perfil").text("0.00");
    $("#ingresos_mensuales").val("");

    $.ajax({
        type: "GET",
        url: "",
        data: { obtener_tasa: true },
        dataType: "json",
        success: function (res) {
            if (res && res.tasa) {
                $("#tasa_bcv_perfil").val(res.tasa.toString().replace(',', '.'));
                if (typeof calcularDolaresPerfil === "function") {
                    calcularDolaresPerfil();
                }
            }
        }
    });

    $("#modalRegistroPerfilFinanciero").modal("show");
});

$(document).on("input", "#ingreso_bs_perfil", function () {
    var bs = parseFloat($(this).val().toString().replace(',', '.')) || 0;
    var tasa = parseFloat($("#tasa_bcv_perfil").val()) || 1;
    var usd = (bs / tasa).toFixed(2);

    $("#calc_usd_perfil").text(usd);
    $("#ingresos_mensuales").val(usd);
});

$("#guardarPerfilFinanciero").off("click.validacionPerfil").on("click.validacionPerfil", function () {
    var cedula = $("#cedulaPerfil").val();
    var ingresosUsd = $("#calc_usd_perfil").text();

    if (!cedula || !/^[VE]-\d{7,8}$/.test(cedula)) {
        alertas("error", "Debe asociar una cédula válida antes de guardar el perfil financiero.", "¡Error!");
        return;
    }

    if (!$("#tipo_residenciaPerfil").val() || !$("#estado_civilPerfil").val() || !$("#profesionPerfil").val() ||
        !$("#carga_familiarPerfil").val() || !$("#ocupacionPerfil").val() || !$("#ingreso_bs_perfil").val() ||
        Number($("#ingreso_bs_perfil").val()) <= 0 || !/^[0-9]+([.,][0-9]+)?$/.test($("#ingreso_bs_perfil").val().trim()) ||
        !/^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9\s.,/-]+$/.test($("#ocupacionPerfil").val().trim())) {
        alertas("warning", "Debes completar todos los campos y respetar el formato indicado.", "¡Lo Siento!");
        return;
    }

    var formData = $("#formRegistroPerfil").serializeArray();
    formData.push({ name: "ingresos_mensuales", value: ingresosUsd });
    formData.push({ name: "accion", value: "registrarPerfil" });

    $.ajax({
        type: "POST",
        url: "?pagina=clientes",
        data: formData,
        success: function (response) {
            try {
                var res = JSON.parse(response);
                if (res.success) {
                    $("#modalRegistroPerfilFinanciero").modal("hide");
                    $("#formRegistroPerfil")[0].reset();
                    $("#calc_usd_perfil").text("0.00");
                    alertas("success", "Perfil financiero guardado con éxito");
                    $("#clientestabla").DataTable().ajax.reload();
                } else {
                    alertas("error", res.error || "Error al guardar el perfil", "¡Error!");
                }
            } catch (e) {
                alertas("error", "Error en la respuesta del servidor", "¡Error!");
            }
        },
        error: function () {
            alertas("error", "Error de comunicación con el servidor", "¡Error!");
        }
    });
});









$('#modalRegistroPerfilFinanciero').on('hidden.bs.modal', function () {
    $("#formRegistroPerfil")[0].reset();
    $("#calc_usd_perfil").text("0.00");
});





  

function modificar() {
    var nombre = $("#nombreModificar").val();
    var apellido = $("#apellidoModificar").val();
    var cedula = $("#cedulaModificar").val();
    var correo = $("#correoModificar").val();
    var telefono = $("#operadoraModificar").val() + $("#telefonoModificar").val();
    var direccion = $("#direccionModificar").val();
    var sexo = $("#sexoModificar").val();
    var tipo_residencia = $("#tipo_residenciaModificar").val();
    var estado_civil = $("#estado_civilModificar").val();
    var profesion = $("#profesionModificar").val();
    var carga_familiar = $("#carga_familiarModificar").val();
    var ocupacion = $("#ocupacionModificar").val();
    var ingresos_mensuales = $("#ingresosModificar").val();
    var id = $("#clienteId").val();

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
                    id: id,
                    nombre: nombre,
                    apellido: apellido,
                    cedula: cedula,
                    telefono: telefono,
                    correo: correo,
                    direccion: direccion,
                    sexo: sexo,
                    tipo_residencia: tipo_residencia,
                    estado_civil: estado_civil,
                    profesion: profesion,
                    carga_familiar: carga_familiar,
                    ocupacion: ocupacion,
                    ingresos_mensuales: ingresos_mensuales,
                    accion: "modificar",
                },
                success: function (response) {
                    var res = JSON.parse(response);

                    if (res.success) {
                        
                        $("#modalModificar").modal("hide");
                        
                        
                        setTimeout(() => {
                            $(".modal-backdrop").remove();
                            $("body").removeClass("modal-open");
                            $("body").css("padding-right", "");
                        }, 500);

                        $("#formModificar")[0].reset();

                        $("#formModificar input:visible, #formModificar textarea, #formModificar select")
                            .css("border", "1px solid #ced4da")
                            .css("box-shadow", "none");

                        alertas("success");
                        $("#clientestabla").DataTable().ajax.reload();

                    } else if (res.error) {
                        alertas("error", res.error, "Ups!");

                    } else if (res.incompleto) {
                        alertas("errorC", "Hay datos incompletos!", "Lo Siento!");
                        var array = res.input.slice(0, -1).split("-");

                        $("#formModificar input:visible, #formModificar select, #formModificar textarea")
                            .css("border", "1px solid rgb(14, 184, 37)")
                            .css("box-shadow", "0 0 15px rgb(14, 184, 37)");

                        $.each(array, function (index, value) {
                            var campoId = value.endsWith("Modificar") ? value : value + "Modificar";
                            
                            $("#" + campoId)
                                .css("border", "1px solid rgb(158, 3, 3)")
                                .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

                            if (value == "telefono" || value == "telefonoModificar") {
                                $("#operadoraModificar")
                                    .css("border", "1px solid rgb(158, 3, 3)")
                                    .css("box-shadow", "0 0 15px rgb(158, 3, 3)");
                            }
                        });

                    } else if (res.invalido) {
                        alertas("warning", res.invalido, "Lo Siento!");

                        $("#formModificar input:visible, #formModificar select, #formModificar textarea")
                            .css("border", "1px solid rgb(14, 184, 37)")
                            .css("box-shadow", "0 0 15px rgb(14, 184, 37)");

                        var campoInvalidoId = res.input.endsWith("Modificar") ? res.input : res.input + "Modificar";

                        $("#" + campoInvalidoId)
                            .css("border", "1px solid rgb(158, 3, 3)")
                            .css("box-shadow", "0 0 15px rgb(158, 3, 3)");

                        if (res.input == "telefono" || res.input == "telefonoModificar") {
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
  //--------------------------------------------------------------------------------------------
  function validarDatosModificar() {
    const nombre = ($("#nombreModificar").val() || "").trim();
    const apellido = ($("#apellidoModificar").val() || "").trim();
    const cedula = ($("#cedulaModificar").val() || "").trim();
    const correo = ($("#correoModificar").val() || "").trim();
    const telefono = ($("#telefonoModificar").val() || "").trim();
    const direccion = ($("#direccionModificar").val() || "").trim();
    const sexo = ($("#sexoModificar").val() || "").trim();
    const tipoResidencia = ($("#tipo_residenciaModificar").val() || "").trim();
    const estadoCivil = ($("#estado_civilModificar").val() || "").trim();
    const profesion = ($("#profesionModificar").val() || "").trim();
    const cargaFamiliar = ($("#carga_familiarModificar").val() || "").trim();
    const ocupacion = ($("#ocupacionModificar").val() || "").trim();
    const ingresosMensuales = ($("#ingresosModificar").val() || "").trim();
    const operadora = ($("#operadoraModificar").val() || "").trim();

    if (!cedula || !/^[VE]-?[0-9]{7,8}$/.test(cedula.replace(/\s+/g, ''))) return false;
    if (!nombre || nombre.length < 2 || nombre.length > 35 || !/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(nombre)) return false;
    if (!apellido || apellido.length < 2 || apellido.length > 35 || !/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(apellido)) return false;
    if (!correo || correo.length > 45 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) return false;
    if (!operadora || !/^[0-9]{3,4}$/.test(operadora)) return false;
    if (!telefono || !/^[0-9]{6,7}$/.test(telefono)) return false;
    if (!direccion || direccion.length < 5 || direccion.length > 50) return false;
    if (!sexo) return false;
    if (!tipoResidencia || !estadoCivil || !profesion) return false;
    if (cargaFamiliar === "" || isNaN(cargaFamiliar) || Number(cargaFamiliar) < 0 || Number(cargaFamiliar) > 20) return false;
    if (!ocupacion || ocupacion.length < 5 || ocupacion.length > 60 || !/^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9\s.,/-]+$/.test(ocupacion)) return false;
    if (!ingresosMensuales || !/^[0-9]+([.,][0-9]+)?$/.test(ingresosMensuales) || Number(ingresosMensuales.replace(",",".")) <= 0) return false;

    return true;
}

   
  

  //--------------------------------------------------------------------------------------------
  function mostrarAlertaDuplicado(texto) {
    if (/cédula ya está registrada/i.test(texto)) {
      Swal.fire({
        title: "Ups!",
        text: texto,
        icon: "error",
        color: "#545454",
        background: "#ffffff",
        confirmButtonColor: "#7066e0"
      });
      return;
    }
    alertas("error", texto, "¡Ups!");
  }

  function alertas(accion, texto, titulo, funcion, dato) {
    if (accion == "errorC") {
      Swal.fire({
        title: titulo,
        text: texto,
        icon: "error",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "#2C74B3",
        background: "#000910",
      });
    } else if (accion == "error") {
      Swal.fire({
        title: titulo,
        text: texto,
        icon: "error",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "#2C74B3",
        background: "#000910",
      });
    } else if (accion == "warning") {
      Swal.fire({
        title: titulo,
        text: texto,
        icon: "error",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "#2C74B3",
        background: "#000910",
      });
    } else if (accion == "pregunta") {
      Swal.fire({
        title: titulo,
        text: texto,
        icon: "question",
        color: "white",
        showConfirmButton: true,
        confirmButtonColor: "#2C74B3",
        confirmButtonBorder: "#2C74B3",
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
        confirmButtonColor: "#2C74B3",
        confirmButtonBorder: "#2C74B3",
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
        confirmButtonColor: "#2C74B3",
        background: "#000910",
        timer: 1500,
      });
    }
  }
});

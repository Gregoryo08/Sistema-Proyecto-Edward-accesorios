function formatoFecha(fecha) {
    if (!fecha || fecha === "0000-00-00") return "N/A";

    let partes = fecha.split('T')[0].split('-');
    
    if (partes.length !== 3) return fecha;

    const año = partes[0];
    const mes = partes[1];
    const dia = partes[2];

    return dia + "/" + mes + "/" + año;
}







function formateHours(hora) {
    const [horas, minutos] = hora.split(":").map(Number);
    const ahora = new Date();
    ahora.setHours(horas, minutos, 0, 0);
    return ahora.toLocaleTimeString("es-VE", {
        hour: "2-digit",
        minute: "2-digit",
        hour12: true,
    });
}

function mensaje(accion, mensajeTexto, funcion = null) {
    const baseOptions = {
        color: "white",
        background: "#000910",
        confirmButtonColor: "rgb(238, 191, 0)",
    };

    if (accion === "error" || accion === "invalido") {
        Swal.fire({
            title: "Ups!",
            text: mensajeTexto,
            icon: "error",
            showConfirmButton: true,
            ...baseOptions,
        });
    } else if (accion === "warning") {
        Swal.fire({
            title: "Lo Siento!",
            text: mensajeTexto,
            icon: "warning",
            showConfirmButton: false,
            timer: 2000,
            ...baseOptions,
        });
    } else if (accion === "pregunta") {
        Swal.fire({
            title: "Estás Seguro!",
            text: mensajeTexto,
            icon: "question",
            showConfirmButton: true,
            confirmButtonText: "Confirmar",
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            ...baseOptions,
        }).then((result) => {
            
            if (result.isConfirmed && typeof funcion === 'function') {
                funcion();
            }
        });
    } else if (accion === "success") {
        Swal.fire({
            title: "Listo!",
            text: mensajeTexto || "Proceso Ejecutado con Éxito!",
            icon: "success",
            showConfirmButton: false,
            timer: 1500,
            ...baseOptions,
        }).then(() => {
            
            if (typeof window.cargar === 'function') {
                window.cargar();
            }
        });
    }
}



$(document).ready(function() {
    let empleadosAsignados = new Map();
    let observacionesRegistradas = [];
    let idTurnoModificar = null;

    let empleadosAsignadosMod = new Map();
    let observacionesRegistradasMod = [];

    const tablaEmpleadosBody = $("#TablaEmpleados tbody");
    const tablaObsBody = $("#TablaObs tbody");
    const inputEmpleadosHidden = $("#empleados");
    const inputObsHidden = $("#obs");
    const btnRegistrar = $("#registrar");
    const fechaInput = $("#fecha");
    const horaEntradaInput = $("#hora_entrada");
    const horaSalidaInput = $("#hora_salida");
    const observacionesTextArea = $("#observaciones");
    const textoMensajeObs = $("#texto_mensaje_obs");
    const selectEmpleados = $("#Listaempleado");
    const modalRegistroTurno = $("#modalRegistroTurno");

    const tablaEmpleadosModBody = $("#TablaEmpleados_mod tbody");
    const tablaObsModBody = $("#TablaObs_mod tbody");
    const selectEmpleadosMod = $("#Listaempleado_mod");
    const observacionesTextAreaMod = $("#observaciones_mod");
    const inputEmpleadosModHidden = $("#empleados_mod");
    const inputObsModHidden = $("#obs_mod");
    const btnModificar = $("#btn_modificar");
    const fechaInputMod = $("#fecha_mod");
    const horaEntradaInputMod = $("#hora_entrada_mod");
    const horaSalidaInputMod = $("#hora_salida_mod");

    function cargarEmpleadosEnSelect2(selectElement, callback) {
        $.ajax({
            url: '?pagina=turno',
            type: 'POST',
            dataType: 'json',
            data: {
                accion: 'listarEmpleados',
                q: ''
            },
            success: function(response) {
                selectElement.empty().append('<option value="">Seleccione un empleado...</option>');
                if (Array.isArray(response)) {
                    $.each(response, function(index, empleado) {
                        selectElement.append(
                            `<option value="${empleado.id}" ` +
                            `data-nombre="${empleado.nombre}" ` +
                            `data-apellido="${empleado.apellido}" ` +
                            `data-cargo="${empleado.cargo}">` +
                            `${empleado.id} - ${empleado.nombre} ${empleado.apellido} (${empleado.cargo})` +
                            `</option>`
                        );
                    });
                } else {
                    console.warn("La respuesta del servidor no contiene un array válido de empleados.", response);
                }
                if (selectElement.data('select2')) {
                    selectElement.select2('destroy');
                }
                selectElement.select2({
                    dropdownParent: selectElement.closest('.modal'),
                    placeholder: "Seleccione un empleado...",
                    allowClear: true,
                    language: {
                        noResults: function() {
                            return "No se encontraron empleados.";
                        },
                        searching: function() {
                            return "Buscando...";
                        }
                    }
                });
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar empleados:", textStatus, errorThrown, jqXHR.responseText);
                mensaje('error', 'No se pudieron cargar los empleados. Intente nuevamente.');
            }
        });
    }

    function renderizarTablaEmpleados(mapaEmpleados, tablaBody, inputHidden, isMod) {
        tablaBody.empty();
        if (mapaEmpleados.size === 0) {
            tablaBody.append(`<tr class="text-muted"><td colspan="4">No hay empleados asignados aún.</td></tr>`);
        } else {
            mapaEmpleados.forEach((empleado, id) => {
                const btnClass = isMod ? "eliminar-empleado-mod" : "btn-quitarSeleccion";
                tablaBody.append(`
                    <tr data-id="${id}" data-cedula="${empleado.cedula}">
                        <td>${empleado.cedula}</td>
                        <td>${empleado.nombre} ${empleado.apellido}</td>
                        <td>${empleado.cargo}</td>
                        <td><button type="button" class="btn btn-danger btn-sm ${btnClass}"><i class="fa-solid fa-trash-can"></i></button></td>
                    </tr>
                `);
            });
        }
        inputHidden.val(Array.from(mapaEmpleados.keys()).join('-'));
    }

    function renderizarTablaObservaciones(arrayObservaciones, tablaBody, inputHidden, isMod) {
        tablaBody.empty();
        if (arrayObservaciones.length === 0) {
            tablaBody.append(`<tr class="text-muted"><td colspan="3">No hay observaciones registradas.</td></tr>`);
        } else {
            arrayObservaciones.forEach((obs, index) => {
                const btnClass = isMod ? "eliminar-obs-mod" : "btn-quitarObs";
                tablaBody.append(`
                    <tr data-index="${index}">
                        <td>${index + 1}</td>
                        <td>${obs}</td>
                        <td><button type="button" class="btn btn-danger btn-sm ${btnClass}"><i class="fa-solid fa-trash-can"></i></button></td>
                    </tr>
                `);
            });
        }
        inputHidden.val(arrayObservaciones.join('-'));
    }

function validarCamposFormulario() {
    let isValid = true;
    fechaInput.removeClass("is-invalid is-valid");
    horaEntradaInput.removeClass("is-invalid is-valid");
    horaSalidaInput.removeClass("is-invalid is-valid");
    selectEmpleados.removeClass("is-invalid is-valid");

    
    if (!fechaInput.val()) {
        fechaInput.addClass("is-invalid");
        isValid = false;
    } else {
        fechaInput.addClass("is-valid");
    }

    
    if (!horaEntradaInput.val()) {
        horaEntradaInput.addClass("is-invalid");
        isValid = false;
    } 

   
    if (!horaSalidaInput.val()) {
        horaSalidaInput.addClass("is-invalid");
        isValid = false;
    }


    if (empleadosAsignados.size === 0) {
        selectEmpleados.addClass("is-invalid");
        isValid = false;
    } else {
        selectEmpleados.addClass("is-valid");
    }

    
    if (horaEntradaInput.val() && horaSalidaInput.val()) {
        const entrada = new Date(`2000-01-01T${horaEntradaInput.val()}`);
        let salida = new Date(`2000-01-01T${horaSalidaInput.val()}`);

        
        if (salida.getTime() <= entrada.getTime()) {
            salida.setDate(salida.getDate() + 1); 
        }
        
        const duracionMs = salida.getTime() - entrada.getTime();

        if (duracionMs <= 60000) { 
            
            horaEntradaInput.removeClass("is-invalid").addClass("is-valid"); 
            horaSalidaInput.removeClass("is-valid").addClass("is-invalid"); 
            $("#horaSalidaFeedback").text("La hora de salida debe ser posterior a la de entrada y el turno debe tener duración.");
            isValid = false;
        } else { 
            
            horaEntradaInput.removeClass("is-invalid").addClass("is-valid"); 
            horaSalidaInput.removeClass("is-invalid").addClass("is-valid");
            $("#horaSalidaFeedback").text(""); 
        }
    }

    return isValid;
}
    btnModificar.click(function() {
    if (validarCamposModificacion()) {
        mensaje("pregunta", "¿Estás seguro de modificar este turno?", modificarTurno);
    } else {
        mensaje("warning", "Hay campos inválidos. Por favor, verifica el formulario.");
    }
});

  function validarCamposModificacion() {
    let isValid = true;
    fechaInputMod.removeClass("is-invalid is-valid");
    horaEntradaInputMod.removeClass("is-invalid is-valid");
    horaSalidaInputMod.removeClass("is-invalid is-valid");

    
    if (!fechaInputMod.val()) {
        fechaInputMod.addClass("is-invalid");
        isValid = false;
    } else {
        fechaInputMod.addClass("is-valid");
    }

    
    if (!horaEntradaInputMod.val()) {
        horaEntradaInputMod.addClass("is-invalid");
        isValid = false;
    } else {
        horaEntradaInputMod.addClass("is-valid");
    }
    if (!horaSalidaInputMod.val()) {
        horaSalidaInputMod.addClass("is-invalid");
        isValid = false;
    } else {
        horaSalidaInputMod.addClass("is-valid");
    }

if (horaEntradaInputMod.val() && horaSalidaInputMod.val()) {
        
        const entrada = new Date(`2000-01-01T${horaEntradaInputMod.val()}`);
        let salida = new Date(`2000-01-01T${horaSalidaInputMod.val()}`);
        
       
        if (salida.getTime() <= entrada.getTime()) {
            salida.setDate(salida.getDate() + 1); 
        }
        
        const duracionMs = salida.getTime() - entrada.getTime();

       
        if (duracionMs <= 60000) { 
            horaSalidaInputMod.addClass("is-invalid");
            $("#horaSalidaFeedbackMod").text("La hora de salida debe ser posterior a la de entrada y el turno debe tener una duración mínima.");
            isValid = false;
        } else {
            horaSalidaInputMod.addClass("is-valid");
        }
    }

  
    if (empleadosAsignadosMod.size === 0) {
        selectEmpleadosMod.addClass("is-invalid");
        isValid = false;
    } else {
        selectEmpleadosMod.removeClass("is-invalid");
    }
    

    return isValid;
}

    function actualizarEstadoBotonRegistrar() {
        if (validarCamposFormulario()) {
            btnRegistrar.css("display", "block");
        } else {
            btnRegistrar.css("display", "none");
        }
    }
    
    function actualizarEstadoBotonModificar() {
        if (validarCamposModificacion()) {
            btnModificar.css("display", "block");
        } else {
            btnModificar.css("display", "none");
        }
    }


    function validarFechaTurno(fecha, callback) {
        const fechaSeleccionada = new Date(fecha);
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        fechaSeleccionada.setHours(0, 0, 0, 0);

        const feedback = $("#fechaFeedback");
        feedback.text("");
        fechaInput.removeClass("is-invalid is-valid");

        if (fechaSeleccionada < hoy) {
            fechaInput.addClass("is-invalid");
            feedback.text("No puedes seleccionar una fecha anterior a la actual.");
            actualizarEstadoBotonRegistrar();
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "?pagina=turno",
            dataType: "json",
            data: {
                accion: "validarTurno",
                fecha: fecha,
            },
            success: function(response) {
                if (response.existe) {
                    fechaInput.addClass("is-invalid");
                    feedback.text("Ya existe un turno registrado para esta fecha.");
                } else {
                    fechaInput.addClass("is-valid");
                }
                actualizarEstadoBotonRegistrar();
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error al validar la fecha del turno:", status, error, xhr.responseText);
                mensaje("error", "Error al validar la fecha del turno.");
                fechaInput.addClass("is-invalid");
                actualizarEstadoBotonRegistrar();
                if (typeof callback === 'function') {
                    callback({
                        error: true
                    });
                }
            },
        });
    }

    function validarFechaModificacion(fecha, id, callback) {
        const feedback = $("#fechaFeedbackMod");
        feedback.text("");
        fechaInputMod.removeClass("is-invalid is-valid");

        $.ajax({
            type: "POST",
            url: "?pagina=turno",
            dataType: "json",
            data: {
                accion: "validarTurno",
                fecha: fecha,
                id_excluir: id
            },
            success: function(response) {
                if (response.existe) {
                    fechaInputMod.addClass("is-invalid");
                    feedback.text("Ya existe un turno registrado para esta fecha.");
                } else {
                    fechaInputMod.addClass("is-valid");
                }
                actualizarEstadoBotonModificar();
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error al validar la fecha del turno para modificar:", status, error, xhr.responseText);
                mensaje("error", "Error al validar la fecha del turno.");
                fechaInputMod.addClass("is-invalid");
                actualizarEstadoBotonModificar();
                if (typeof callback === 'function') {
                    callback({
                        error: true
                    });
                }
            },
        });
    }

    function resetearFormulario() {
        $("#formRegistroTurno")[0].reset();
        selectEmpleados.val(null).trigger('change');
        empleadosAsignados.clear();
        observacionesRegistradas = [];
        idTurnoModificar = null;
        renderizarTablaEmpleados(empleadosAsignados, tablaEmpleadosBody, inputEmpleadosHidden, false);
        renderizarTablaObservaciones(observacionesRegistradas, tablaObsBody, inputObsHidden, false);
        inputEmpleadosHidden.val('');
        inputObsHidden.val('');
        $("#fecha, #hora_entrada, #hora_salida, #observaciones, #Listaempleado").removeClass("is-valid is-invalid").css({
            "border": "",
            "box-shadow": ""
        });
        textoMensajeObs.text("").css("display", "none");
        cargarEmpleadosEnSelect2(selectEmpleados);
        actualizarEstadoBotonRegistrar();
    }

function registrarTurno() {
    const fecha = fechaInput.val();
    const cedulas_empleados = inputEmpleadosHidden.val();
    const hora_e = horaEntradaInput.val();
    const hora_s = horaSalidaInput.val();
    const obs = observacionesRegistradas;
    const accion = idTurnoModificar ? "modificar" : "registrar";
    
    const dataToSend = {
        fecha: fecha,
        cedulas_empleados: cedulas_empleados,
        hora_entrada: hora_e,
        hora_salida: hora_s,
        obs: obs,
        accion: accion,
    };

    if (idTurnoModificar) {
        dataToSend.id = idTurnoModificar;
    }

    Swal.fire({
        title: "Procesando!",
        html: "Espere un momento...",
        timer: 1000, 
        color: "white",
        background: "#000910",
        timerProgressBar: true,
        didOpen: () => Swal.showLoading(),
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer || result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "?pagina=turno",
                dataType: "json",
                data: dataToSend,
                success: function(response) {
                    if (response.success) {
                        
                        mensaje("success", response.success);

                        
                        $("#formRegistroTurno input, #formRegistroTurno select, #formRegistroTurno textarea")
                            .removeClass("is-invalid is-valid")
                            .css({
                                "border": "",
                                "box-shadow": ""
                            });

                        
                        $("#modalRegistroTurno").modal("hide");

                        
                        if ($.fn.DataTable.isDataTable('#tablaTurnos')) {
                            $('#tablaTurnos').DataTable().ajax.reload(null, false);
                        }
                        
                        
                        if (!idTurnoModificar) resetearFormulario();

                    } else if (response.incompleto || response.invalido) {
                        mensaje("warning", response.incompleto || response.invalido);
                        
                        const camposInvalidos = (response.input || '').split("-").filter(n => n);
                        $.each(camposInvalidos, function(index, value) {
                            $("#" + value).addClass("is-invalid").css({
                                "border": "1px solid rgb(158, 3, 3)",
                                "box-shadow": "0 0 15px rgb(158, 3, 3)"
                            });
                        });
                    } else if (response.error) {
                        mensaje("error", response.error);
                    }
                },
                error: function(xhr, status, error) {
                    mensaje("error", "Ha ocurrido un error en el servidor!");
                    console.error(xhr.responseText);
                },
            });
        }
    });
}

function modificarTurno() {
    const id = $("#id_turno_modificar").val();
    const fecha = fechaInputMod.val();
    const cedulas_empleados = inputEmpleadosModHidden.val();
    const hora_e = horaEntradaInputMod.val().substring(0, 5);
    const hora_s = horaSalidaInputMod.val().substring(0, 5);
    const obs = observacionesRegistradasMod;

    const dataToSend = {
        id_turno: id,
        fecha: fecha,
        cedulas_empleados: cedulas_empleados,
        hora_entrada: hora_e,
        hora_salida: hora_s,
        obs: obs,
        accion: "modificar",
    };

    Swal.fire({
        title: "Procesando!",
        html: "",
        timer: 2000,
        color: "white",
        background: "#000910",
        timerProgressBar: true,
        didOpen: () => Swal.showLoading(),
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
            $.ajax({
                type: "POST",
                url: "?pagina=turno",
                dataType: "json",
                data: dataToSend,
                success: function(response) {
                    if (response.success) {
                        mensaje("success", "Turno modificado correctamente.");
                        $("#modalmodificarTurno").modal("hide");
                        if ($.fn.DataTable.isDataTable('#tablaTurnos')) {
                            $('#tablaTurnos').DataTable().ajax.reload();
                        }
                    } else if (response.error) {
                        mensaje("error", response.error);
                    }
                },
                error: function(xhr, status, error) {
                    mensaje("error", "Ha ocurrido un error en el servidor al modificar el turno!");
                },
            });
        }
    });
}

$("#modalmodificarTurno").on('hidden.bs.modal', function () {
    $("#btn_modificar").focus();
});
    $("#btn_turno").click(function() {
        const ahora = new Date();
        const mes = (ahora.getMonth() + 1).toString().padStart(2, "0");
        const fechaLocal = `${ahora.getFullYear()}-${mes}-${ahora.getDate().toString().padStart(2, "0")}`;

        cargarEmpleadosEnSelect2(selectEmpleados, function(empleadosResponse) {
            if (Array.isArray(empleadosResponse) && empleadosResponse.length > 0) {
                modalRegistroTurno.modal("show");
                fechaInput.val(fechaLocal);
                resetearFormulario();
                actualizarEstadoBotonRegistrar();
            } else {
                mensaje("warning", "No se han encontrado empleados registrados!");
            }
        });
    });

    selectEmpleados.on("select2:select", function(e) {
        const data = e.params.data;
        if (!data.id) return;
        if (empleadosAsignados.has(data.id)) {
            mensaje("warning", `El empleado ${data.element.dataset.nombre} ${data.element.dataset.apellido} ya ha sido asignado.`);
        } else {
            empleadosAsignados.set(data.id, {
                cedula: data.id,
                nombre: data.element.dataset.nombre,
                apellido: data.element.dataset.apellido,
                cargo: data.element.dataset.cargo,
            });
            renderizarTablaEmpleados(empleadosAsignados, tablaEmpleadosBody, inputEmpleadosHidden, false);
        }
        $(this).val(null).trigger('change');
        actualizarEstadoBotonRegistrar();
    });

    $(document).on("click", ".btn-quitarSeleccion", function() {
        const filaEliminar = $(this).closest("tr");
        const id = filaEliminar.data("id");
        empleadosAsignados.delete(id);
        renderizarTablaEmpleados(empleadosAsignados, tablaEmpleadosBody, inputEmpleadosHidden, false);
        actualizarEstadoBotonRegistrar();
    });

    observacionesTextArea.keyup(function() {
        const valor = $(this).val().trim();
        textoMensajeObs.css("display", "none").text("");
        $(this).removeClass("is-invalid is-valid");
        if (valor.length === 0) return;
        if (/[^a-zA-ZñÑ\s]/.test(valor)) {
            textoMensajeObs.css("display", "block").text("Solo se pueden ingresar letras.");
            $(this).addClass("is-invalid");
        } else if (valor.length >= 5) {
            $(this).addClass("is-valid");
        }
    });

    $("#agregarObservacion").click(function(e) {
        e.preventDefault();
        const observacion = observacionesTextArea.val().trim();
        if (observacion.length < 5) {
            mensaje("warning", "La observación debe tener al menos 5 caracteres.");
            observacionesTextArea.addClass("is-invalid");
            return;
        }
        observacionesRegistradas.push(observacion);
        renderizarTablaObservaciones(observacionesRegistradas, tablaObsBody, inputObsHidden, false);
        observacionesTextArea.val("");
        observacionesTextArea.removeClass("is-invalid is-valid");
        actualizarEstadoBotonRegistrar();
    });

    $(document).on("click", ".btn-quitarObs", function() {
        const filaEliminar = $(this).closest("tr");
        const indexToDelete = filaEliminar.data("index");
        observacionesRegistradas.splice(indexToDelete, 1);
        renderizarTablaObservaciones(observacionesRegistradas, tablaObsBody, inputObsHidden, false);
        actualizarEstadoBotonRegistrar();
    });

    fechaInput.on("change", function() {
        const fecha = $(this).val();
        if (fecha) {
            validarFechaTurno(fecha);
        } else {
            $(this).removeClass("is-valid is-invalid");
            btnRegistrar.css("display", "none");
        }
    });
    horaEntradaInput.on("change", actualizarEstadoBotonRegistrar);
    horaSalidaInput.on("change", actualizarEstadoBotonRegistrar);

    btnRegistrar.click(function() {
        if (validarCamposFormulario()) {
            const mensajePregunta = idTurnoModificar ? "¿Estás seguro de modificar este turno?" : "¿Estás seguro de los datos ingresados?";
            mensaje("pregunta", mensajePregunta, registrarTurno);
        } else {
            mensaje("warning", "El registro es incorrecto, por favor verifica el formulario!");
        }
    });

    $("#btn_cancel").click(function() {
        resetearFormulario();
    });

    
    


$(document).on("click", ".btn-modificarTurno", function() {
    const id_turno = $(this).data("id");
    idTurnoModificar = id_turno;

    $.ajax({
        type: "POST",
        url: "?pagina=turno",
        dataType: "json",
        data: {
            id: id_turno,
            accion: "consultarTurno"
        },
        success: function(response) {
            if (response.error) {
                mensaje("error", response.error);
            } else if (typeof response === "object" && response.turno) {
                const datos_turno = response.turno;
                const datos_empleados = response.datos_empleados;
                const datos_observaciones = response.observaciones;

                $("#id_turno_modificar").val(datos_turno.id_turno);

                fechaInputMod.val(datos_turno.fecha_turno);
                horaEntradaInputMod.val(datos_turno.hora_entrada);
                horaSalidaInputMod.val(datos_turno.hora_salida);

                empleadosAsignadosMod = new Map(datos_empleados.map(emp => [emp.cedula_empleado, {
                    cedula: emp.cedula_empleado,
                    nombre: emp.nombre,
                    apellido: emp.apellido,
                    cargo: emp.nombre_cargo,
                }]));
                
                observacionesRegistradasMod = datos_observaciones.map(obs => obs.descripcion);
                
                if (observacionesRegistradasMod.length > 0 && observacionesRegistradasMod[0].includes('-')) {
                    observacionesRegistradasMod = observacionesRegistradasMod[0].split('-');
                } else if (!observacionesRegistradasMod) {
                    observacionesRegistradasMod = [];
                }

                renderizarTablaEmpleados(empleadosAsignadosMod, tablaEmpleadosModBody, inputEmpleadosModHidden, true);
                renderizarTablaObservaciones(observacionesRegistradasMod, tablaObsModBody, inputObsModHidden, true);
                
                cargarEmpleadosEnSelect2(selectEmpleadosMod);

                $("#modalmodificarTurno").modal("show");
                $("#btnAbrirModalRegistroTurno").focus(); 
                actualizarEstadoBotonModificar();
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al consultar turno para modificar:", status, error, xhr.responseText);
            mensaje("error", "Ha ocurrido un error en el servidor al consultar el turno!");
        },
    });
});

    selectEmpleadosMod.on("select2:select", function(e) {
        const data = e.params.data;
        if (!data.id) {
            actualizarEstadoBotonModificar();
            return;
        }
        if (empleadosAsignadosMod.has(data.id)) {
            mensaje("warning", `El empleado ${data.element.dataset.nombre} ${data.element.dataset.apellido} ya ha sido asignado.`);
        } else {
            empleadosAsignadosMod.set(data.id, {
                cedula: data.id,
                nombre: data.element.dataset.nombre,
                apellido: data.element.dataset.apellido,
                cargo: data.element.dataset.cargo,
            });
            renderizarTablaEmpleados(empleadosAsignadosMod, tablaEmpleadosModBody, inputEmpleadosModHidden, true);
        }
        $(this).val(null).trigger('change');
        actualizarEstadoBotonModificar();
    });

    $(document).on("click", ".eliminar-empleado-mod", function() {
        const filaEliminar = $(this).closest("tr");
        const id = filaEliminar.data("cedula");
        empleadosAsignadosMod.delete(id);
        renderizarTablaEmpleados(empleadosAsignadosMod, tablaEmpleadosModBody, inputEmpleadosModHidden, true);
        actualizarEstadoBotonModificar();
    });

    $("#agregarObservacionMod").click(function(e) {
        e.preventDefault();
        const observacion = observacionesTextAreaMod.val().trim();
        if (observacion.length < 5) {
            mensaje("warning", "La observación debe tener al menos 5 caracteres.");
            observacionesTextAreaMod.addClass("is-invalid");
            return;
        }
        observacionesRegistradasMod.push(observacion);
        renderizarTablaObservaciones(observacionesRegistradasMod, tablaObsModBody, inputObsModHidden, true);
        observacionesTextAreaMod.val("");
        observacionesTextAreaMod.removeClass("is-invalid is-valid");
        actualizarEstadoBotonModificar();
    });

    $(document).on("click", ".eliminar-obs-mod", function() {
        const filaEliminar = $(this).closest("tr");
        const indexToDelete = filaEliminar.data("index");
        observacionesRegistradasMod.splice(indexToDelete, 1);
        renderizarTablaObservaciones(observacionesRegistradasMod, tablaObsModBody, inputObsModHidden, true);
        actualizarEstadoBotonModificar();
    });

    fechaInputMod.on("change", function() {
        const fecha = $(this).val();
        if (fecha && idTurnoModificar) {
            validarFechaModificacion(fecha, idTurnoModificar);
        } else {
            $(this).removeClass("is-valid is-invalid");
            btnModificar.css("display", "none");
        }
    });
    horaEntradaInputMod.on("change", actualizarEstadoBotonModificar);
    horaSalidaInputMod.on("change", actualizarEstadoBotonModificar);

    btnModificar.click(function() {
        if (validarCamposModificacion()) {
            mensaje("pregunta", "¿Estás seguro de modificar este turno?", modificarTurno);
        } else {
            mensaje("warning", "El registro es incorrecto, por favor verifica el formulario!");
        }
    });

    $(document).on("click", ".btn-eliminarTurno", function() {
        const id_turno = $(this).data("id");
        mensaje("pregunta", "¿Estás seguro de que quieres eliminar este turno? Esta acción no se puede deshacer.", function() {
            $.ajax({
                type: "POST",
                url: "?pagina=turno",
                dataType: "json",
                data: {
                    id: id_turno,
                    accion: "eliminar"
                },
                success: function(response) {
                    if (response.success) {
                        mensaje("success", "Turno eliminado correctamente.");
                        if ($.fn.DataTable.isDataTable('#tablaTurnos')) {
                            $('#tablaTurnos').DataTable().ajax.reload();
                        }
                    } else {
                        mensaje("error", response.error || "No se pudo eliminar el turno.");
                    }
                },
                error: function(xhr, status, error) {
                    mensaje("error", "Ha ocurrido un error en el servidor al intentar eliminar el turno.");
                }
            });
        });
    });

    $(document).on("click", ".btn-verTurno", function() {
        var id_turno = $(this).data("id");
        $.ajax({
            type: "POST",
            url: "?pagina=turno",
            dataType: "json",
            data: {
                id: id_turno,
                accion: "consultarTurno",
            },
            success: function(response) {
                if (response.error || response.invalido || response.incompleto) {
                    mensaje("error", response.error || response.invalido || response.incompleto);
                } else if (typeof response === "object" && response.turno) {
                    const datos_turno = response.turno;
                    const datos_empleados = response.datos_empleados;
                    const datos_observaciones = response.observaciones;

                    $("#dataFecha").text(formatoFecha(datos_turno.fecha_turno));
                    $("#dataHoraE").text(formateHours(datos_turno.hora_entrada));
                    $("#dataHoraS").text(formateHours(datos_turno.hora_salida));

                    var tablaEmpleadosVerBody = $("#tablaInfoTurno tbody");
                    tablaEmpleadosVerBody.empty();
                    $.each(datos_empleados, function(i, empleado) {
                        var newRow = tablaEmpleadosVerBody[0].insertRow(-1);
                        newRow.insertCell(0).textContent = empleado.cedula_empleado;
                        newRow.insertCell(1).textContent = empleado.nombre + " " + empleado.apellido;
                        newRow.insertCell(2).textContent = empleado.nombre_cargo;
                    });

                    var tablaObsVerBody = $("#tablaInfoTurnoObs tbody");
                    tablaObsVerBody.empty();
                    $.each(datos_observaciones, function(i, obs) {
                        var newRow = tablaObsVerBody[0].insertRow(-1);
                        newRow.classList.add("aumento");
                        newRow.insertCell(0).textContent = i + 1;
                        var obsCell = newRow.insertCell(1);
                        obsCell.textContent = obs.descripcion;
                        obsCell.classList.add("aumento-td");
                    });

                    $("#modalConsultaTurno").modal("show");
                    $("#btnAbrirModalRegistroTurno").focus(); 
                }
            },
            error: function(xhr, status, error) {
                console.error("Error al consultar turno:", status, error, xhr.responseText);
                mensaje("error", "Ha ocurrido un error en el servidor al consultar el turno!");
            },
        });
    });

    $("#btn_salir_ver").click(function() {
        setTimeout(() => {
            $("#tablaInfoTurno tbody").empty();
            $("#tablaInfoTurnoObs tbody").empty();
        }, 200);
    });

    fechaInput.val(new Date().toISOString().slice(0, 10));
    resetearFormulario();

   $("#tablaTurnos").DataTable({
    paging: true,
    searching: true,
    ordering: true,
    info: true,
    ajax: {
        url: "?pagina=turno&ajax=true",
        dataSrc: "",
    },
    columns: [{
        data: "id_turno",
        visible: false
    }, {
        data: null,
        render: function(data, type, row) {
            var fecha = formatoFecha(row.fecha_turno);
            return fecha;
        },
    }, {
        data: null,
        render: function(data, type, row) {
            var entrada = formateHours(row.hora_entrada);
            var salida = formateHours(row.hora_salida);
            return `${entrada}<br>${salida}`;
        },
    }, {
        data: null,
        render: function(data, type, row) {
            return row.conteo;
        },
    }, {
        data: null,
        render: function(data, type, row) {
            var botonesHTML = "";
            botonesHTML += ` <button type="button" class="btn btn-info btn-verTurno" data-id="${row.id_turno}"><i class="bi bi-eye-fill"></i></button>`;
            botonesHTML += ` <button type="button" class="btn btn-warning btn-modificarTurno" data-id="${row.id_turno}"><i class="bi bi-pencil-square"></i></button>`;
            botonesHTML += ` <button type="button" class="btn btn-danger btn-eliminarTurno" data-id="${row.id_turno}"><i class="bi bi-trash-fill"></i></button>`;
            return botonesHTML;
        },
    }, ],
    pageLength: 4,
    lengthMenu: [
        [4, 8],
        ["4", "8"],
    ],
    columnDefs: [{
        className: "dt-head-center",
        targets: "_all",
        headers: true,
    }, ],
    language: {
        lengthMenu: "Mostrar _MENU_ registros por página",
        zeroRecords: "No se encontraron turnos - Lo siento",
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
$(document).ready(function () {
    let permisos = { registrar: false, modificar: false, eliminar: false, control_total: false, consultar: false };

    $.get("?pagina=empleado&permisos=true", function (data) {
        permisos = typeof data === 'string' ? JSON.parse(data) : data;

        if (permisos.control_total || permisos.registrar) {
            $("#registrarEmpleados").show();
        } else {
            $("#registrarEmpleados").hide();
        }

        cargarTablaEmpleados();
    });

    function cargarTablaEmpleados() {
        $("#tablaEmpleados").DataTable({
            destroy: true,
            ajax: {
                url: "?pagina=empleado&ajax=true",
                dataSrc: "",
            },
            columns: [
                { data: "cedula_persona" },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `${row.nombre} ${row.apellido}`;
                    }
                },
                { data: "nombre_cargo" },
                {
                    data: null,
                    render: function (data, type, row) {
                        let esActivo = (row.estado == 1 || row.estado === 'activo');
                        return esActivo 
                            ? '<span class="badge bg-success">Activo</span>' 
                            : '<span class="badge bg-danger">Inactivo</span>';
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        let botones = '<div class="btn-group" role="group">';

                        if (permisos.control_total || permisos.consultar) {
                            botones += `<button type="button" class="btn btn-info btn-verDatos m-2" data-cedula="${row.cedula_persona}">
                                            <i class="bi bi-eye-fill"></i>
                                        </button> `;
                        }

                        if (permisos.control_total || permisos.modificar) {
                            botones += `<button type="button" class="btn btn-warning m-2" data-bs-toggle="modal" data-bs-target="#modalModificar"
                                            data-nombre="${row.nombre}" data-apellido="${row.apellido}" data-correo="${row.correo}" 
                                            data-telefono="${row.telefono}" data-direccion="${row.direccion}" data-cedula="${row.cedula_persona}" 
                                            data-cargo="${row.nombre_cargo}" data-id_cargo="${row.id_cargo}"
                                            data-sexo="${row.sexo}" data-fecha_nacimiento="${row.fecha_nacimiento || 'N/A'}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button> `;
                        }

                        if (permisos.control_total || permisos.eliminar) {
                            let esActivo = (row.estado == 1 || row.estado === 'activo');
                            
                            if (esActivo) {
                                botones += `<button type="button" class="btn btn-danger btn-cambiar-estado m-2" data-id="${row.cedula_persona}" data-nombre="${row.nombre} ${row.apellido}" data-accion="inactivo">
                                                <i class="fa-solid fa-trash-can" title="Inhabilitar"></i>
                                            </button>`;
                            } else {
                                botones += `<button type="button" class="btn btn-success btn-cambiar-estado m-2" data-id="${row.cedula_persona}" data-nombre="${row.nombre} ${row.apellido}" data-accion="activo">
                                                <i class="bi bi-person-check" title="Habilitar"></i>
                                            </button>`;
                            }
                        }

                        botones += '</div>';
                        return (botones === '<div class="btn-group" role="group"></div>')
                            ? '<span class="badge bg-secondary">Solo lectura</span>'
                            : botones;
                    },
                },
            ],
            pageLength: 4,
            lengthMenu: [[4, 8, 12, 16], ["4", "8", "12", "16"]],
            columnDefs: [{ className: "dt-head-center", targets: "_all" }],
            language: {
                "emptyTable": "No hay datos disponibles en la tabla",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "loadingRecords": "Cargando...",
                "processing": "Procesando..."
            }
        });
    }

    $("#cargo").select2({
        dropdownParent: $("#modalRegistroEmpleados"),
    });

    $(document).on("click", ".btn-verDatos", function () {
        var cedula = $(this).data("cedula");

        $.ajax({
            type: "POST",
            url: window.location.href,
            data: {
                cedula: cedula,
                accion: "consultar",
            },
            success: function (response) {
                try {
                    var res = JSON.parse(response);

                    if (res && typeof res === "object" && !res.error) {
                        $("#VerNombre").text(res.nombre || "N/A");
                        $("#VerApellido").text(res.apellido || "N/A");
                        $("#VerCedula").text(res.cedula_persona || "N/A");
                        $("#VerTelefono").text(res.telefono || "N/A");
                        $("#VerCorreo").text(res.correo || "N/A");
                        $("#VerCargo").text(res.nombre_cargo || "N/A");
                        $("#VerDireccion").text(res.direccion || "N/A");

                        var sexoTexto = (res.sexo === "M") ? "Masculino" : (res.sexo === "F") ? "Femenino" : "N/A";
                        $("#VerSexo").text(sexoTexto);

                        if (res.fecha_nacimiento) {
                            var fecha = res.fecha_nacimiento.split("-");
                            $("#VerFechaNac").text(fecha[2] + "-" + fecha[1] + "-" + fecha[0]);
                        } else {
                            $("#VerFechaNac").text("N/A");
                        }

                        $("#modalVerDatos").modal("show");
                    } else {
                        Swal.fire({ title: "Ups!", text: "No se encontraron datos!", icon: "error", color: "white", background: "#000910" });
                    }
                } catch (e) {
                    Swal.fire({ title: "Error", text: "Error al procesar la respuesta del servidor", icon: "error", color: "white", background: "#000910" });
                }
            },
            error: function () {
                Swal.fire({ title: "Ups!", text: "Error en el servidor!", icon: "error", color: "white", background: "#000910" });
            },
        });
    });

    $("#registrarEmpleados").click(function () {
        $.ajax({
            type: "POST",
            url: window.location.href,
            data: {
                accion: "buscarCargos",
            },
            success: function (response) {
                var res = JSON.parse(response);

                if (res.length > 0) {
                    var select = $("#cargo");
                    select.empty();
                    select.append($("<option>", { value: "", text: "Seleccione un cargo" }));

                    $.each(res, function (index, value) {
                        select.append($("<option>", {
                            value: value.id_cargo,
                            text: value.nombre_cargo
                        }));
                    });

                    $("#modalRegistroEmpleados").modal("show");
                } else {
                    Swal.fire({
                        title: "Lo Siento!",
                        text: "No existen cargos registrados en el sistema!",
                        icon: "warning",
                        color: "white",
                        background: "#000910"
                    });
                }
            },
            error: function () {
                Swal.fire({
                    title: "Ups!",
                    text: "Ha ocurrido un error en el servidor!",
                    icon: "error",
                    color: "white",
                    background: "#000910"
                });
            },
        });
    });

    $("#btn_registrar").on("click", function () {
        if (validarDatos()) {
            alertas(
                "pregunta",
                "¿Estás seguro de los datos ingresados?",
                "Espera un momento!",
                registrar
            );
        } else {
            mensaje("errorC");
        }
    });

    function registrar() {
        Swal.fire({
            title: "Procesando!",
            timer: 2000,
            timerProgressBar: true,
            color: "white",
            background: "#000910",
            didOpen: () => { Swal.showLoading(); },
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer || result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: window.location.href,
                    data: {
                        nombre: $("#nombre").val(),
                        apellido: $("#apellido").val(),
                        cedula: $("#prefijo").val() + $("#cedula").val(),
                        correo: $("#correo").val(),
                        telefono: $("#operadora").val() + $("#telefono").val(),
                        direccion: $("#direccion").val(),
                        cargo: $("#cargo").val(),
                        fecha_nacimiento: $("#fecha_nacimiento").val(),
                        sexo: $("#sexo").val(),
                        accion: "registrar",
                    },
                    success: function (response) {
                        var res = JSON.parse(response);

                        if (res.success) {
                            $("#modalRegistroEmpleados").modal("hide");
                            $("#formRegistroEmpleado")[0].reset();
                            limpiarEstilos();
                            alertas("success");
                            $("#tablaEmpleados").DataTable().ajax.reload();
                        } else if (res.error) {
                            alertas("error", res.error, "Ups!");
                        } else if (res.incompleto) {
                            alertas("errorC", "Hay datos incompletos!", "Lo Siento!");
                            procesarErrores(res.input, "incompleto");
                        } else if (res.invalido) {
                            alertas("warning", res.invalido, "Lo Siento!");
                            procesarErrores(res.input, "invalido");
                        }
                    },
                    error: function () {
                        alertas("error", "Ha ocurrido un error en el Servidor!", "Ups!");
                    },
                });
            }
        });
    }

    function limpiarEstilos() {
        $("#formRegistroEmpleado input, #formRegistroEmpleado select, #formRegistroEmpleado textarea")
            .css({ "border": "1px solid #ced4da", "box-shadow": "none" });
    }

    function procesarErrores(input, tipo) {
        limpiarEstilos();
        var campos = (tipo === "incompleto") ? input.slice(0, -1).split("-") : [input];

        $.each(campos, function (index, value) {
            $("#" + value).css({ "border": "1px solid rgb(158, 3, 3)", "box-shadow": "0 0 15px rgb(158, 3, 3)" });

            if (value === "telefono") $("#operadora").css({ "border": "1px solid rgb(158, 3, 3)", "box-shadow": "0 0 15px rgb(158, 3, 3)" });
            if (value === "cedula") $("#prefijo").css({ "border": "1px solid rgb(158, 3, 3)", "box-shadow": "0 0 15px rgb(158, 3, 3)" });
        });
    }

    $("#btn_cancel_register").click(function () {
        $("#formRegistroEmpleado")[0].reset();

        $("input").css("border", "1px solid #ced4da").css("box-shadow", "none");
        $("textarea").css("border", "1px solid #ced4da").css("box-shadow", "none");
        $("select").css("border", "1px solid #ced4da").css("box-shadow", "none");
        $(".mensaje p").css("display", "none");
    });

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
        } else {
            Swal.fire({ ...config, title: "Listo!", text: "Éxito!", icon: "success", showConfirmButton: false, timer: 1500 });
        }
    }

    $(document).on("click", ".btn-warning", function (event) {
        var button = $(this);
        var cedulaCompleta = String(button.data("cedula"));
        var telefonoCompleto = String(button.data("telefono"));
        var fechaNacimiento = button.data("fecha_nacimiento"); 

        var modal = $("#modalModificar");
        
        modal.find("#nombreModificar").val(button.data("nombre"));
        modal.find("#apellidoModificar").val(button.data("apellido"));
        modal.find("#correoModificar").val(button.data("correo"));
        modal.find("#direccionModificar").val(button.data("direccion"));
        modal.find("#sexoModificar").val(button.data("sexo"));
        
        modal.find("#fechaNacimientoModificar").val(fechaNacimiento);
        modal.find("#fecha_nacimiento_real").val((fechaNacimiento === "N/A" || !fechaNacimiento) ? "" : fechaNacimiento);
        
        modal.find("#operadoraModificar").val(telefonoCompleto.substring(0, 4));
        modal.find("#telefonoModificar").val(telefonoCompleto.substring(4));
        
        modal.find("#prefijoModificar").val(cedulaCompleta.substring(0, 2));
        modal.find("#cedulaModificar").val(cedulaCompleta.substring(2));
        
        $("#id_empleado").val(cedulaCompleta);

        $.ajax({
            type: "POST",
            url: window.location.href,
            data: { accion: "buscarCargos" },
            success: function (response) {
                var res = JSON.parse(response);
                if (res.length > 0) {
                    var select = $("#cargoModificar");
                    select.empty().append($("<option>", { value: "", text: "Seleccione un cargo" }));

                    $.each(res, function (index, value) {
                        select.append($("<option>", {
                            value: value.id_cargo,
                            text: value.nombre_cargo
                        }));
                    });

                    modal.find("#cargoModificar").val(button.data("id_cargo"));
                    $("#modalModificar").modal("show");
                } else {
                    alertas("warning", "No existen cargos registrados en el sistema!", "Lo Siento!");
                }
            },
            error: function () {
                alertas("error", "Ha ocurrido un error en el Servidor!", "Ups!");
            }
        });
    });

    $("#modificarDatos").on("click", function () {
        if (validarDatosModificar()) {
            alertas(
                "pregunta",
                "Estas seguro de los datos ingresados?",
                "Espera un momento!",
                modificar
            );
        } else {
            mensaje("errorC");
        }
    });

    function modificar() {
        Swal.fire({
            title: "Procesando!",
            timer: 1500,
            color: "white",
            background: "#000910",
            timerProgressBar: true,
            didOpen: () => { Swal.showLoading(); }
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                $.ajax({
                    type: "POST",
                    url: window.location.href,
                    data: {
                        cedula_vieja: $("#id_empleado").val(),
                        cedula_nueva: $("#prefijoModificar").val().replace("-", "") + $("#cedulaModificar").val(),
                        nombre: $("#nombreModificar").val(),
                        apellido: $("#apellidoModificar").val(),
                        correo: $("#correoModificar").val(),
                        direccion: $("#direccionModificar").val(),
                        telefono: $("#operadoraModificar").val() + $("#telefonoModificar").val(),
                        cargo: $("#cargoModificar").val(),
                        sexo: $("#sexoModificar").val(),
                        fecha_nacimiento_real: $("#fecha_nacimiento_real").val(),
                        accion: "modificar"
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

                            limpiarEstilos();
                            $("#tablaEmpleados").DataTable().ajax.reload();
                            alertas("success");
                            
                        } else if (res.error) {
                            alertas("error", res.error, "Ups!");
                        } else if (res.incompleto || res.invalido) {
                            alertas("errorC", res.incompleto ? "Hay datos incompletos!" : res.invalido, "Lo Siento!");
                            procesarErrores(res);
                        }
                    },
                    error: function () {
                        mensaje("error", "Error al procesar la solicitud");
                    }
                });
            }
        });
    }

    $(document).on("click", ".btn-cambiar-estado", function () {
        var id = $(this).data("id");
        var nombre = $(this).data("nombre");
        var nuevaAccion = $(this).data("accion");
        
        $("#id_empleado_delete").val(id);

        let textoMensaje = (nuevaAccion === 'inactivo') 
            ? "¿Estás seguro de inhabilitar al empleado '" + nombre + "'?" 
            : "¿Estás seguro de habilitar al empleado '" + nombre + "'?";

        alertas("eliminar", textoMensaje, "Espera un momento!", estado, nuevaAccion);
    });

    function estado(accion) {
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
                        id: $("#id_empleado_delete").val(),
                        estado: accion,
                        accion: "eliminar"
                    },
                    success: function (response) {
                        var res = JSON.parse(response);

                        if (res.success) {
                            $("#tablaEmpleados").DataTable().ajax.reload();
                            alertas("success");
                        } else if (res.error) {
                            alertas("error", res.error, "Ups!");
                        }
                    },
                    error: function () {
                        mensaje("error");
                    },
                });
            }
        });
    }

    function validarDatos() {
        return $("#cedula").val() && $("#prefijo").val() && $("#nombre").val() && 
               $("#apellido").val() && $("#correo").val() && $("#telefono").val() && 
               $("#operadora").val() && $("#direccion").val() && $("#cargo").val();
    }

    function validarDatosModificar() {
        return $("#cedulaModificar").val() && $("#prefijoModificar").val() && $("#nombreModificar").val() && 
               $("#apellidoModificar").val() && $("#correoModificar").val() && $("#telefonoModificar").val() && 
               $("#operadoraModificar").val() && $("#direccionModificar").val() && $("#cargoModificar").val();
    }

    function alertas(accion, texto, titulo, funcion, dato) {
        let config = { color: "black", background: "#ffff", confirmButtonColor: "bg-secondary" };
        
        if (accion == "errorC" || accion == "error" || accion == "warning") {
            Swal.fire({ ...config, title: titulo, text: texto, icon: "error" });
        } else if (accion == "pregunta") {
            Swal.fire({ ...config, title: titulo, text: texto, icon: "question", showCancelButton: true, confirmButtonText: "Confirmar", cancelButtonText: "Cancelar" }).then((r) => { if (r.isConfirmed) funcion(); });
        } else if (accion == "eliminar") {
            Swal.fire({ ...config, title: titulo, text: texto, icon: "question", showCancelButton: true, confirmButtonText: "Confirmar", cancelButtonText: "Cancelar" }).then((r) => { if (r.isConfirmed) funcion(dato); });
        } else {
            Swal.fire({ ...config, title: "Listo!", text: "Proceso Ejecutado con Exito!", icon: "success", showConfirmButton: false, timer: 1500 });
        }
    }
});
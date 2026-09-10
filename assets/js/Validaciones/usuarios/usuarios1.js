$(document).ready(function () {
    let permisos = { registrar: false, modificar: false, eliminar: false };

    $.ajax({
        url: "?pagina=usuarios&permisos=true",
        method: "GET",
        dataType: "json",
        success: function (data) {
            permisos = data;
            if (permisos.registrar) $("#btn_nuevo_usuario").show();
            if (permisos.registrar || permisos.modificar || permisos.eliminar) cargarTablaUsuarios();
        }
    });



    function cargarTablaUsuarios() {
        $("#tablaPerfilados").DataTable({
            destroy: true,
            processing: true,
            ajax: {
                url: "?pagina=usuarios&ajax=listar",
                dataSrc: function (json) {
                    return Array.isArray(json) ? json : [];
                }
            },
            columns: [
                { data: "cedula_usuario" },
                { data: null, render: (d, t, row) => `${row.nombre || ''} ${row.apellido || ''}` },
                { data: "descripcion_rol" },
                { data: "estatus", render: (data) => `<span class="badge" style="background:${(data === "Activo" ? "green" : "red")}">${data}</span>` },
                { data: null, render: function(d, t, row) {
                    let btn = `<div class="btn-group">`;
                    if (permisos.modificar) btn += `<button type="button" class="btn btn-sm btn-warning btn-editar m-2" title="Editar Usuario" data-id="${row.cedula_usuario}"><i class="fa-solid fa-pen"></i></button>`;
                    if (permisos.eliminar && row.estatus === "Activo") btn += `<button type="button" class="btn btn-sm btn-danger btn-suspender m-2" title="Suspender Usuario" data-id="${row.cedula_usuario}"><i class="fa-solid fa-trash-can"></i></button>`;
                    else if (permisos.modificar && row.estatus === "Inactivo") btn += `<button type="button" class="btn btn-sm btn-success btn-habilitar m-2" title="Habilitar Usuario" data-id="${row.cedula_usuario}"><i class="bi bi-person-check"></i></button>`;
                    return btn + `</div>`;
                }}
            ]
        });
    }

    function cargarDatosAuxiliares() {
        $.ajax({
            url: "?pagina=usuarios",
            method: "POST",
            data: { accion: "consultaRoles" },
            dataType: "json",
            success: function(roles) {
                let select = $("#id_rol");
                select.empty().append('<option value="">Seleccione un rol</option>');
                roles.forEach(rol => {
                    select.append(`<option value="${rol.idRol}">${rol.descripcion_rol}</option>`);
                });
            }
        });
        $.ajax({
            url: "?pagina=usuarios",
            method: "POST",
            data: { accion: "consultaCargos" },
            dataType: "json",
            success: function(cargos) {
                let select = $("#id_cargo");
                select.empty().append('<option value="">Seleccione un cargo</option>');
                cargos.forEach(cargo => {
                    select.append(`<option value="${cargo.id_cargo}">${cargo.nombre_cargo}</option>`);
                });
            }
        });
    }

    $("#btn_nuevo_usuario").click(function () {
        $("#modalTitle").text("Registrar Usuario");
        $("#accion").val("registrar");
        $("#cedula").val("").prop('disabled', false);
        $("#formSeguridad")[0].reset();
        $("#formDatosPersonales")[0].reset();
        $("#formClasificacion")[0].reset();
        cargarDatosAuxiliares();
        $("#modalSeguridad").modal("show");
    });

    function cargarDatosAuxiliaresMod() {
        $.ajax({
            url: "?pagina=usuarios",
            method: "POST",
            data: { accion: "consultaRoles" },
            dataType: "json",
            success: function(roles) {
                let select = $("#id_rol_mod");
                select.empty().append('<option value="">Seleccione un rol</option>');
                roles.forEach(rol => {
                    select.append(`<option value="${rol.idRol}">${rol.descripcion_rol}</option>`);
                });
            }
        });
    }

    $(document).on("click", ".btn-editar", function () {
        let id = $(this).data("id");
        cargarDatosAuxiliaresMod();
        
        $.ajax({
            url: "?pagina=usuarios",
            method: "POST",
            data: { accion: "consultarUno", cedula: id },
            dataType: "json",
            success: function (response) {
                if (response) {
                    $("#cedula_mod").val(response.cedula_usuario);
                    $("input[name='clave']").val("");
                    $("#id_rol_mod").val(response.id_rol);
                    $("input[name='nombre']").val(response.nombre);
                    $("input[name='apellido']").val(response.apellido);
                    $("input[name='correo']").val(response.correo);
                    $("input[name='telefono']").val(response.telefono);
                    $("input[name='direccion']").val(response.direccion);
                    $("input[name='fecha_nacimiento']").val(response.fecha_nacimiento);
                    $("select[name='sexo']").val(response.sexo);
                }
                
                let modalEl = document.getElementById('modalModificar');
                let myModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                myModal.show();
            }
        });
    });

    $("#btnGuardarModificacion").click(function () {
        let datos = $("#formModificarUsuario").serialize();
        
        $.ajax({
            url: "?pagina=usuarios",
            method: "POST",
            data: datos,
            dataType: "json",
            success: function (r) {
                if (r.success) {
                    $("#modalModificar").modal("hide");
                    Swal.fire("Éxito", "Modificación realizada correctamente", "success");
                    cargarTablaUsuarios();
                } else {
                    Swal.fire("Error", r.error || "Operación fallida", "error");
                }
            }
        });
    });

    $("#btnFinalizarRegistro").click(function () {
        let datos = $("#formSeguridad").serialize() + "&" + 
                    $("#formDatosPersonales").serialize() + "&" + 
                    $("#formClasificacion").serialize() + "&" +
                    "accion=" + $("#accion").val() + 
                    "&cedula=" + $("#cedula").val();
        
        $.ajax({
            url: "?pagina=usuarios",
            method: "POST",
            data: datos,
            dataType: "json",
            success: function (r) {
                if (r.success) {
                    $(".modal").modal("hide");
                    Swal.fire("Éxito", "Proceso realizado correctamente", "success");
                    cargarTablaUsuarios();
                } else {
                    Swal.fire("Error", r.error || "Operación fallida", "error");
                }
            }
        });
    });

    $(document).on("click", ".btn-suspender, .btn-habilitar", function () {
        let id = $(this).data("id");
        let estatus = $(this).hasClass("btn-suspender") ? "Inactivo" : "Activo";
        $.post("?pagina=usuarios", { accion: "estatus", id: id, estatus: estatus }, function () {
            cargarTablaUsuarios();
        });
    });
});

function toggleOpciones(valor) {
    const contRol = document.getElementById('container_rol');
    const contCargo = document.getElementById('container_cargo');
    if (valor === 'empleado') {
        contRol.classList.remove('d-none');
        contCargo.classList.remove('d-none');
    } else if (valor === 'cliente') {
        contRol.classList.remove('d-none');
        contCargo.classList.add('d-none');
    } else {
        contRol.classList.add('d-none');
        contCargo.classList.add('d-none');
    }
}
$(document).ready(function () {
    datosAjax(1);
});

function datosAjax(tiene) {
    $.ajax({
        type: "POST",
        url: window.location.href,
        data: { accion: "consulta" },
        success: function (response) {
            try {
                let json = JSON.parse(response);
                if (json) {
                    $("#d_nombre").html(json.nombre || "Administrador");
                    $("#d_apellido").html(json.apellido || "--");
                    $("#d_cedula").html(json.cedula_persona || "--");
                    $("#d_telefono").html(json.telefono || "Sin registro");
                    $("#d_correo").html(json.correo || "Sin registro");
                    $("#d_direccion").html(json.direccion || "Sin registro");
                    $("#d_fecha_nacimiento").html(json.fecha_nacimiento || "Sin registro");
                    if (tiene === 1 && typeof buttonAjax === "function") {
                        buttonAjax(json);
                    }
                }
            } catch (e) {
                console.error("Error al parsear JSON:", e);
            }
        }
    });
}

$(document).on("click", "#buttonModificar", function () {
    const data = $(this).data();
    $("#cedula").val(data.cedula);
    $("#nombre").val(data.nombre);
    $("#apellido").val(data.apellido);
    $("#operadora").val(String(data.telefono).substring(0, 4));
    $("#telefono").val(String(data.telefono).substring(4));
    $("#correo").val(data.correo);
    $("#direccion").val(data.direccion);
    $("#cargo").val(data.cargo);
    $("#fecha_nacimiento").val(data.fecha_nacimiento);
    $("#modalModificarDatos").modal("show");
});

$("#modificar").click(function () {
    if ($("#nombre").val() && $("#apellido").val() && $("#telefono").val() && $("#correo").val() && $("#direccion").val()) {
        mensajes("pregunta", "Estas seguro de modificar los Datos!");
    } else {
        mensajes("vacio");
    }
});

function modificar() {
    Swal.fire({ title: "Procesando...", allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
    $.ajax({
        type: "POST",
        url: "",
        data: {
            nombre: $("#nombre").val(),
            apellido: $("#apellido").val(),
            cedula: $("#cedula").val(),
            correo: $("#correo").val(),
            operadora: $("#operadora").val(),
            telefono: $("#telefono").val(),
            direccion: $("#direccion").val(),
            cargo: $("#cargo").val(),
            fecha_nacimiento: $("#fecha_nacimiento").val(),
            accion: "modificar"
        },
        success: function (response) {
            try {
                var res = JSON.parse(response);
                if (res.success) {
                    $("#modalModificarDatos").modal("hide");
                    mensajes("modificar");
                    datosAjax(1);
                } else {
                    mensajes("error", res.error || "Error desconocido");
                }
            } catch (e) {
                mensajes("error", "Error de respuesta del servidor.");
            }
        },
        error: function() { mensajes("error", "Error de conexión."); }
    });
}

function mensajes(accion, mensaje) {
    const config = {
        color: "white",
        background: "#000910",
        confirmButtonColor: "rgb(238, 191, 0)"
    };

    if (accion == "vacio") {
        Swal.fire({ ...config, title: "Ups!", text: "Debes completar todos los campos!", icon: "error" });
    } else if (accion == "pregunta") {
        Swal.fire({ ...config, title: "Un momento!", text: mensaje, icon: "question", showCancelButton: true, confirmButtonText: "Confirmar" }).then((result) => {
            if (result.isConfirmed) modificar();
        });
    } else if (accion == "modificar") {
        Swal.fire({ ...config, title: "Listo!", text: "Proceso Ejecutado con Exito!", icon: "success", showConfirmButton: false, timer: 1500 });
    } else {
        Swal.fire({ ...config, title: "Ups!", text: mensaje, icon: "error" });
    }
}
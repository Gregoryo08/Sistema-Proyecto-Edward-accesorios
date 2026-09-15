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



function mensajes(accion, mensaje) {
    const config = {
        color: "white",
        background: "#000910",
        confirmButtonColor: "#2C74B3"
    };

    if (accion == "vacio") {
        Swal.fire({ ...config, title: "Ups!", text: "Debes completar todos los campos!", icon: "error" });
    } else {
        Swal.fire({ ...config, title: "Ups!", text: mensaje, icon: "error" });
    }
}
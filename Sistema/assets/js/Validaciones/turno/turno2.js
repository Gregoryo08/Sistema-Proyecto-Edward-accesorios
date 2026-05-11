$(document).ready(function () {
    const horaEntradaInput = $("#hora_entrada");
    const horaSalidaInput = $("#hora_salida");
    const observacionesTextArea = $("#observaciones");
    const textoMensajeObs = $("#texto_mensaje_obs");
    const horaEntradaInputMod = $("#hora_entrada_mod");
    const horaSalidaInputMod = $("#hora_salida_mod");
    const observacionesTextAreaMod = $("#observaciones_mod");
    const textoMensajeObsMod = $("#texto_mensaje_obs_mod");

 

    observacionesTextArea.keyup(function () {
        const valor = $(this).val().trim();
        if (valor.length === 0) {
            $(this).removeClass("is-invalid is-valid");
            textoMensajeObs.text("").hide();
        } else if (valor.length < 5) {
            $(this).addClass("is-invalid").removeClass("is-valid");
            textoMensajeObs.text("La observación debe tener al menos 5 caracteres.").show();
        } else {
            $(this).addClass("is-valid").removeClass("is-invalid");
            textoMensajeObs.text("").hide();
        }
    });

    horaSalidaInputMod.on('change', function () {
        const horaEntradaMod = horaEntradaInputMod.val();
        const horaSalidaMod = horaSalidaInputMod.val();
        if (horaEntradaMod && horaSalidaMod && horaSalidaMod <= horaEntradaMod) {
            $(this).addClass("is-invalid").removeClass("is-valid");
            $(this).next('.invalid-feedback').show();
        } else {
            $(this).removeClass("is-invalid").addClass("is-valid");
            $(this).next('.invalid-feedback').hide();
        }
    });

    observacionesTextAreaMod.keyup(function () {
        const valor = $(this).val().trim();
        if (valor.length === 0) {
            $(this).removeClass("is-invalid is-valid");
            textoMensajeObsMod.text("").hide();
        } else if (valor.length < 5) {
            $(this).addClass("is-invalid").removeClass("is-valid");
            textoMensajeObsMod.text("La observación debe tener al menos 5 caracteres.").show();
        } else {
            $(this).addClass("is-valid").removeClass("is-invalid");
            textoMensajeObsMod.text("").hide();
        }
    });
});
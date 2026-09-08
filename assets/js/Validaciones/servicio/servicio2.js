$(document).ready(function() {

    // Helper general para gestionar los mensajes de error/éxito
    function aplicarValidacion(input, esValido, titulo, mensaje) {
        const $el = $(input);
        
        let $target = $el;
        if ($el.hasClass('select2-hidden-accessible')) {
            $target = $el.next('.select2-container');
        }

        let $errorDiv = $el.siblings('.msg-error');
        if ($errorDiv.length === 0) {
            $errorDiv = $el.parent().find('.msg-error');
        }

        if (esValido) {
            $target.removeClass('is-invalid').addClass('is-valid');
            if ($errorDiv.length) $errorDiv.hide().text('');
        } else {
            $target.removeClass('is-valid').addClass('is-invalid');
            if ($errorDiv.length) {
                $errorDiv.removeClass('text-success')
                          .addClass('text-danger')
                          .text(mensaje || titulo)
                          .show();
            }
        }
    }

    // 1. Teléfono: Bloquea letras en tiempo real y evalúa rango/obligatoriedad
    $('#reg_telefono, #orden_telefono_tecnico, input[name="telefono"]').on('keydown input blur', function(evento) {
        if (evento.type === 'keydown' && evento.key.length === 1 && !/[0-9]/.test(evento.key)) {
            evento.preventDefault();
            const mensajeNumeros = "Este campo solo permite aceptar números.";
            aplicarValidacion(this, false, "Teléfono inválido", mensajeNumeros);
            Swal.fire({
                title: "Teléfono inválido",
                text: mensajeNumeros,
                icon: "warning",
                color: "white",
                background: "#000910"
            });
            clearTimeout(this.mensajeNumerosTimer);
            this.mensajeNumerosTimer = setTimeout(() => {
                aplicarValidacion(this, true);
            }, 4000);
            return;
        }

        const valorOriginal = $(this).val().trim();
        const contieneCaracteresInvalidos = /[^0-9]/.test(valorOriginal);
        let telefono = valorOriginal.replace(/[^0-9]/g, '');

        if (telefono === "") {
            aplicarValidacion(this, false, "Este campo es obligatorio", "");
        } else if (contieneCaracteresInvalidos) {
            aplicarValidacion(this, false, "Campo inválido", "Este campo solo acepta números");
        } else if (!/^[0-9]{7,11}$/.test(telefono)) {
            aplicarValidacion(this, false, "Teléfono Inválido", "Este campo solo acepta números (7 a 11 dígitos)");
        } else {
            aplicarValidacion(this, true);
        }

        this.value = telefono;
    });

    // 2. Modelo / Equipo
    $('#reg_equipo, #orden_equipo_tecnico, input[name="equipo"]').on('input change blur', function() {
        let valor = $(this).val().trim();

        if (valor === "") {
            aplicarValidacion(this, false, "Campo Requerido", "Este campo es obligatorio");
        } else {
            aplicarValidacion(this, true);
        }
    });

    // 3. Diagnóstico Inicial / Falla
    $('#reg_falla, #orden_falla_tecnico, textarea[name="falla"]').on('input change blur', function() {
        let valor = $(this).val().trim();

        if (valor === "") {
            aplicarValidacion(this, false, "Campo Requerido", "Este campo es obligatorio");
        } else {
            aplicarValidacion(this, true);
        }
    });

    // 4. Cliente
    $('#reg_cedula, #orden_cliente_tecnico').on('change input blur', function() {
        let val = $(this).val();
        if (!val) {
            aplicarValidacion(this, false, "Cliente Requerido", "Seleccione un cliente válido antes de registrar el servicio");
        } else {
            aplicarValidacion(this, true);
        }
    });

    // 5. Marca
    $('#reg_marca, #orden_marca_tecnico').on('change blur', function() {
        let val = $(this).val();
        if (!val) {
            aplicarValidacion(this, false, "Marca Requerida", "Selecciona la marca del equipo");
        } else {
            aplicarValidacion(this, true);
        }
    });

    // 6. Especialidad
    $('#reg_especialidad, #orden_especialidad_tecnico').on('change blur', function() {
        let val = $(this).val();
        if (!val) {
            aplicarValidacion(this, false, "Especialidad Requerida", "Selecciona la especialidad del servicio");
        } else {
            aplicarValidacion(this, true);
        }
    });

    $('#mod_equipo, #mod_falla, #mod_diagnostico').on('input change blur', function() {
        if (!$(this).val().trim()) {
            aplicarValidacion(this, false, "", "Este campo es obligatorio");
        } else {
            aplicarValidacion(this, true);
        }
    });

    $('#mod_estado').on('change blur', function() {
        if (!$(this).val()) {
            aplicarValidacion(this, false, "", "Seleccione un estado válido antes de modificar el servicio");
        } else {
            aplicarValidacion(this, true);
        }
    });

    $('#selectAgregarProducto').on('change blur', function() {
        if (!$(this).val()) {
            aplicarValidacion(this, false, "", "Seleccione un producto válido");
        } else {
            aplicarValidacion(this, true);
        }
    });

    $('#mod_monto').on('input change blur', function() {
        if ($(this).val() === '' || Number($(this).val()) < 0) {
            aplicarValidacion(this, false, "", "Este campo es obligatorio");
        } else {
            aplicarValidacion(this, true);
        }
    });

    $('#diagnostico_cobro, #nota_tecnico_cobro').on('input change blur', function() {
        if (!$(this).val().trim()) {
            aplicarValidacion(this, false, "", "Este campo es obligatorio");
        } else {
            aplicarValidacion(this, true);
        }
    });

    $('#montoTotalCobro').on('keydown input blur', function(evento) {
        if (evento.type === 'keydown' && evento.key.length === 1 && !/[0-9.,]/.test(evento.key)) {
            evento.preventDefault();
            aplicarValidacion(this, false, "", "Este campo solo acepta números");
            clearTimeout(this.mensajeMontoTimer);
            this.mensajeMontoTimer = setTimeout(() => {
                if ($(this).val().trim()) aplicarValidacion(this, true);
            }, 4000);
            return;
        }

        const valorOriginal = $(this).val().trim();
        const contieneCaracteresInvalidos = /[^0-9.,]/.test(valorOriginal);
        const monto = valorOriginal.replace(/[^0-9.,]/g, '').replace(',', '.');

        if (!monto) {
            aplicarValidacion(this, false, "", "Este campo es obligatorio");
        } else if (contieneCaracteresInvalidos) {
            aplicarValidacion(this, false, "", "Este campo solo acepta números");
        } else if (Number.isNaN(Number(monto)) || Number(monto) < 0) {
            aplicarValidacion(this, false, "", "Este campo solo acepta números");
        } else {
            aplicarValidacion(this, true);
        }

        this.value = monto;
    });
});
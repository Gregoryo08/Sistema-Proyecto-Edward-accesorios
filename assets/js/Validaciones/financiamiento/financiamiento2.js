$(document).ready(function() {
    $('#btn_registrar').prop('disabled', true);

    $('#modalRegistroFinanciamiento [data-bs-dismiss="modal"]').on('click', function() {
        $('#formularioRegistroFinanciamiento')[0].reset();
        $('#cedula_persona, #id_telefono').val(null).trigger('change');
        $('.form-control, .select2-selection').removeClass('is-valid is-invalid');
        $('.select2-container').removeClass('select2-container--valid');
        $('.msg-error').hide().text("");
        $('#cuota_estimada').text("$ 0.00");
        $('#contenedor_ia').hide();
        verificarFormulario();
    });

    const validaciones = {
        'monto_total': { regex: /^[0-9]+(\.[0-9]{1,2})?$/, error: 'Monto inválido' },
        'pago_inicial': { regex: /^[0-9]+(\.[0-9]{1,2})?$/, error: 'Monto inválido' },
        'cantidad_cuotas': { regex: /^[0-9]{1,2}$/, error: 'Solo números' },
        'dia_pago': { regex: /^(0?[1-9]|[12][0-9]|3[01])$/, error: 'Día inválido' },
        'fecha_inicio': { regex: /.+/, error: 'Seleccione una fecha' }
    };

    function gestionarEstado(id, esValido, mensaje = "") {
        const $el = $("#" + id);
        const $errorDiv = $("#error_" + id);
        const $container = $el.hasClass('select2-hidden-accessible') ? $el.next('.select2-container') : $el;

        if (esValido) {
            $el.removeClass('is-invalid').addClass('is-valid');
            $container.addClass('select2-container--valid');
            $errorDiv.hide().text(""); 
        } else {
            $el.removeClass('is-valid').addClass('is-invalid');
            $container.removeClass('select2-container--valid');
            $errorDiv.removeClass('text-success').addClass('text-danger').text(mensaje).show();
        }
        verificarFormulario();
    }

    function verificarFormulario() {
        const camposInputs = ['monto_total', 'pago_inicial', 'cantidad_cuotas', 'dia_pago', 'fecha_inicio'];
        const camposSelects = ['cedula_persona', 'id_telefono'];
        let esValido = true;

        camposInputs.forEach(id => {
            if (!$('#' + id).hasClass('is-valid')) esValido = false;
        });

        camposSelects.forEach(id => {
            const val = $('#' + id).val();
            if (val === null || val === "") esValido = false;
        });

        $('#btn_registrar').prop('disabled', !esValido);
    }

    $('#cedula_persona, #id_telefono').select2({
        placeholder: "Seleccionar...",
        allowClear: true,
        width: '100%'
    }).on('change', function() {
        gestionarEstado($(this).attr('id'), $(this).val() !== null && $(this).val() !== "", "Selección requerida");
    });

    $(".form-control").on("input change", function() {
        const id = $(this).attr('id');
        if (!validaciones[id]) return;

        let val = $(this).val();
        if (id === 'pago_inicial' && val.length > 6) {
            val = val.slice(0, 6);
            $(this).val(val);
        }

        const config = validaciones[id];

        if (val === "" || val === null) {
            gestionarEstado(id, false, "Campo requerido");
        } else if (config.regex.test(val)) {
            gestionarEstado(id, true);
        } else {
            gestionarEstado(id, false, config.error);
        }
    });

    $('#btn_registrar').on('click', function(e) {
        if ($(this).prop('disabled')) {
            e.preventDefault();
            return false;
        }
    });
});
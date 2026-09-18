$(document).ready(function() {
    $('#btn_registrar').prop('disabled', true);

    let dispositivosValidos = [];
    let clientesValidos = [];
    let manipulacionDetectada = false;

    function cargarDatosValidacion() {
        $.ajax({
            url: '?pagina=financiamiento&ajax=true&x=telefonos_disponibles',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (Array.isArray(response)) {
                    dispositivosValidos = response;
                    iniciarObservadorOpciones('#id_telefono', dispositivosValidos);
                }
            }
        });

        $.ajax({
            url: '?pagina=financiamiento&ajax=true&x=clientes',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (Array.isArray(response)) {
                    clientesValidos = response;
                    iniciarObservadorOpciones('#cedula_persona', clientesValidos);
                }
            }
        });
    }

    cargarDatosValidacion();

    function iniciarObservadorOpciones(selectorSelect, listaValida) {
        const selectElement = $(selectorSelect)[0];
        if (!selectElement) return;

        const observer = new MutationObserver(function(mutations) {
            if (!listaValida || listaValida.length === 0) return;

            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    const optionModificado = mutation.target;
                    if (optionModificado.tagName !== 'OPTION') return;

                    const valorActual = optionModificado.getAttribute('value');
                    const selectPadre = $(selectorSelect);

                    if (selectPadre.val() === valorActual) {
                        const esValido = listaValida.some(item => 
                            Object.values(item).some(val => String(val) === String(valorActual))
                        );

                        if (!esValido) {
                            marcarManipulacion(selectorSelect);
                        }
                    }
                }
            });
        });

        observer.observe(selectElement, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['value']
        });
    }

    function marcarManipulacion(selectorSelect) {
        manipulacionDetectada = true;
        const $el = $(selectorSelect);
        
        $el.val(null).trigger('change.select2');
        gestionarEstado($el.attr('id'), false, "Manipulación detectada en el DOM");
    }

    $('#modalRegistroFinanciamiento').on('hidden.bs.modal', function() {
        $('#formularioRegistroFinanciamiento')[0].reset();
        $('#cedula_persona, #id_telefono').val(null).trigger('change');
        $('#id_producto, #id_unidad').val('');
        $('.form-control, .select2-selection').removeClass('is-valid is-invalid');
        $('.select2-container').removeClass('select2-container--valid');
        $('.msg-error').hide().text("");
        $('#cuota_estimada').text("$ 0.00");
        $('#contenedor_ia').hide();
        manipulacionDetectada = false;
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
        let $errorDiv = $("#error_" + id);
        
        if ($errorDiv.length === 0) {
            $errorDiv = $el.siblings('.msg-error');
        }

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

        if (manipulacionDetectada) {
            $('#btn_registrar').prop('disabled', true);
            return;
        }

        camposInputs.forEach(id => {
            if (!$('#' + id).hasClass('is-valid')) esValido = false;
        });

        camposSelects.forEach(id => {
            const val = $('#' + id).val();
            if (val === null || val === "") esValido = false;
        });

        $('#btn_registrar').prop('disabled', !esValido);
    }

    $('#cedula_persona').select2({
        placeholder: "Seleccionar...",
        allowClear: true,
        width: '100%'
    }).on('select2:select', function(e) {
        var valorSeleccionado = $(this).val();
        var clienteEncontrado = clientesValidos.find(function(item) {
            return Object.values(item).some(val => String(val) === String(valorSeleccionado));
        });

        if (!clienteEncontrado) {
            marcarManipulacion('#cedula_persona');
            return;
        }

        manipulacionDetectada = false;
        gestionarEstado($(this).attr('id'), true, "");
    }).on('select2:clear', function() {
        if (!manipulacionDetectada) {
            gestionarEstado($(this).attr('id'), false, "Selección requerida");
        }
    });

    $('#id_telefono').select2({
        placeholder: "Seleccionar...",
        allowClear: true,
        width: '100%'
    }).on('select2:select', function(e) {
        var valorSeleccionado = $(this).val();
        var dispositivoEncontrado = dispositivosValidos.find(function(item) {
            return Object.values(item).some(val => String(val) === String(valorSeleccionado));
        });

        if (!dispositivoEncontrado) {
            $('#id_producto, #id_unidad').val('');
            $('#monto_total').val('').trigger('input');
            marcarManipulacion('#id_telefono');
            return;
        }

        manipulacionDetectada = false;
        $('#id_producto').val(dispositivoEncontrado.id_producto);
        $('#id_unidad').val(dispositivoEncontrado.id_unidad);
        $('#monto_total').val(dispositivoEncontrado.precio_detal).trigger('input');

        gestionarEstado($(this).attr('id'), true, "");
    }).on('select2:clear', function() {
        if (!manipulacionDetectada) {
            $('#id_producto, #id_unidad').val('');
            $('#monto_total').val('').trigger('input');
            gestionarEstado($(this).attr('id'), false, "Selección requerida");
        }
    });

    $(".form-control").on("input change", function() {
        const id = $(this).attr('id');
        if (!validaciones[id]) return;

        let val = $(this).val();

        // Limpieza estricta de caracteres según el campo
        if (id === 'monto_total' || id === 'pago_inicial') {
            // Solo permite números y un único punto decimal
            val = val.replace(/[^0-9.]/g, '');
            const partes = val.split('.');
            if (partes.length > 2) {
                val = partes[0] + '.' + partes.slice(1).join('');
            }
            if (id === 'pago_inicial' && val.length > 6) {
                val = val.slice(0, 6);
            }
            $(this).val(val);
        } else if (id === 'cantidad_cuotas' || id === 'dia_pago') {
            // Solo permite números enteros
            val = val.replace(/[^0-9]/g, '');
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
        var clienteActual = $('#cedula_persona').val();
        var unidadActual = $('#id_telefono').val();

        var clienteValidoBD = clientesValidos.some(item => 
            Object.values(item).some(val => String(val) === String(clienteActual))
        );

        var dispositivoValidoBD = dispositivosValidos.some(item => 
            Object.values(item).some(val => String(val) === String(unidadActual))
        );

        if ($(this).prop('disabled') || !clienteValidoBD || !dispositivoValidoBD || manipulacionDetectada) {
            e.preventDefault();
            alert("Error: Se detectó una alteración o datos no válidos en los campos de selección.");
            return false;
        }
    });
});
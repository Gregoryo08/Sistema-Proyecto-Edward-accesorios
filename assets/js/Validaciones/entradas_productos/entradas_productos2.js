$(document).ready(function() {

    $("#guardarEntrada").prop('disabled', true);

    $('#modalReponerProducto [data-bs-dismiss="modal"]').on('click', function() {
        $('#formReponerProducto')[0].reset();
        $('#proveedorSelect, #productoSelect').val(null).trigger('change');
        $('.form-control, .select2-selection').removeClass('is-valid is-invalid');
        $('.select2-container').removeClass('select2-container--valid');
        $('.msg-error').hide().text("");
        $("#tablaEntradaProductos tbody").empty();
        verificarFormulario();
    });

    const validacionesEstaticas = {
        'cantidad_modal': { regex: /^[1-9][0-9]*(\.[0-9]{1,2})?$/, error: 'Cantidad debe ser mayor a cero (Máx. 2 decimales)' },
        'garantia_modal': { regex: /^(0|[1-9][0-9]{0,3})$/, error: 'Solo números enteros (0 o mayores)' }
    };

    function gestionarEstado(id, esValido, mensaje = "", selectorPersonalizado = null) {
        const $el = selectorPersonalizado ? $(selectorPersonalizado) : $("#" + id);
        if (!$el.length) return;
        
        const $container = $el.hasClass('select2-hidden-accessible') ? $el.next('.select2-container') : $el;
        let $errorDiv = id ? $("#error_" + id) : $el.siblings('.msg-error');
        
        if (esValido) {
            $el.removeClass('is-invalid').addClass('is-valid');
            if ($el.hasClass('select2-hidden-accessible')) {
                $container.addClass('select2-container--valid').removeClass('border border-danger');
            }
            if ($errorDiv.length) $errorDiv.hide().text(""); 
        } else {
            $el.removeClass('is-valid').addClass('is-invalid');
            if ($el.hasClass('select2-hidden-accessible')) {
                $container.removeClass('select2-container--valid');
            }
            if ($errorDiv.length) {
                $errorDiv.removeClass('text-success').addClass('text-danger').text(mensaje).show();
            }
        }
        verificarFormulario();
    }

    function verificarFormulario() {
        let esValido = true;

        const proveedor = $('#proveedorSelect').val();
        if (!proveedor || proveedor === "") {
            esValido = false;
        }

        const $filas = $("#tablaEntradaProductos tbody tr");
        if ($filas.length === 0) {
            esValido = false;
        }

        $filas.each(function() {
            const $txtCantidad = $(this).find(".input-cantidad");
            if (!$txtCantidad.hasClass('is-valid')) {
                esValido = false;
            }

            const $chkGarantia = $(this).find(".toggle-garantia");
            if ($chkGarantia.length && $chkGarantia.is(":checked")) {
                const $numGarantia = $(this).find(".input-garantia");
                if (!$numGarantia.hasClass('is-valid')) {
                    esValido = false;
                }
            }
        });

        $("#guardarEntrada").prop('disabled', !esValido);
    }

    $('#proveedorSelect').select2({
        placeholder: "Selecciona un Proveedor",
        allowClear: true,
        width: '100%',
        dropdownParent: $("#modalReponerProducto")
    }).on('change', function() {
        const val = $(this).val();
        gestionarEstado('proveedorSelect', val !== null && val !== "", "Debe seleccionar un proveedor válido");
    });


    $("#tablaEntradaProductos").on("input change", ".input-cantidad", function () {
        let valor = $(this).val();
        
        valor = valor.replace(/^0+/g, "");
        valor = valor.replace(/[^0-9.]/g, "");
        
        const partes = valor.split(".");
        if (partes.length > 2) {
            valor = partes[0] + "." + partes.slice(1).join("").slice(0, 2);
        }
        $(this).val(valor);

        const regexCantidadPositiva = /^[1-9][0-9]*(\.[0-9]{1,2})?$/;
        const esValido = valor !== "" && !isNaN(valor) && parseFloat(valor) > 0 && regexCantidadPositiva.test(valor);
        
        gestionarEstado(null, esValido, "Cantidad inválida (Mayor a 0)", this);
    });

    $("#tablaEntradaProductos").on("input change", ".input-garantia", function () {
        let valor = $(this).val();
        
        valor = valor.replace(/^0+/g, "");
        valor = valor.replace(/[^0-9]/g, "");
        $(this).val(valor);

        const regexEnteroPositivo = /^[1-9][0-9]*$/;
        const esValido = valor !== "" && regexEnteroPositivo.test(valor);
        
        gestionarEstado(null, esValido, "Días inválidos (Mínimo 1)", this);
    });

    $("#tablaEntradaProductos").on("change", ".toggle-garantia", function () {
        const $fila = $(this).closest("tr");
        const $inputGarantia = $fila.find(".input-garantia");
        
        if ($(this).is(":checked")) {

            $inputGarantia.val("").removeClass('is-valid').addClass('is-invalid');
            gestionarEstado(null, false, "Escriba los días de garantía", $inputGarantia);
        } else {

            $inputGarantia.val("0").removeClass('is-invalid').addClass('is-valid');
            $fila.find('.msg-error').hide().text("");
            gestionarEstado(null, true, "", $inputGarantia);
        }
    });

    $("#cantidadEntradaM, #garantiaEntradaM").on("input change", function() {
        const id = $(this).attr('id');
        let val = $(this).val();
        
        if (id === 'cantidadEntradaM') {
            val = val.replace(/^0+/g, "");
            val = val.replace(/[^0-9.]/g, "");
            const partes = val.split(".");
            if (partes.length > 2) {
                val = partes[0] + "." + partes.slice(1).join("").slice(0, 2);
            }
        } else {
        
            if(val !== "0") val = val.replace(/^0+/g, "");
            val = val.replace(/[^0-9]/g, "");
        }
        $(this).val(val);

        const mapaEstatico = id === 'cantidadEntradaM' ? 'cantidad_modal' : 'garantia_modal';
        const config = validacionesEstaticas[mapaEstatico];

        if (id === 'garantiaEntradaM' && (val === "" || val === "0")) {
            $(this).val("0");
            gestionarEstado(id, true);
            return;
        }

        if (val === "" || val === null || parseFloat(val) < 0) {
            gestionarEstado(id, false, "Campo requerido");
        } else if (config.regex.test(val)) {
            gestionarEstado(id, true);
        } else {
            gestionarEstado(id, false, config.error);
        }
    });

    $("#tablaEntradaProductos").on("click", ".eliminar-fila", function() {
        $(this).closest('tr').remove();
        verificarFormulario();
    });

    $('#guardarEntrada, #modificarDatosE2').on('click', function(e) {
        if ($(this).prop('disabled')) {
            e.preventDefault();
            return false;
        }
    });
});
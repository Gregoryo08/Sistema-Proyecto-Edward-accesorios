$(document).ready(function () {

    function gestionarEstado(id, esValido, mensaje = "") {
        const $el = $("#" + id);
        if (!$el.length) return;

        let $errorDiv = $el.siblings('.msg-error, .text-danger');
        if (!$errorDiv.length) {
            $el.after(`<small class="msg-error text-danger" style="display:none;"></small>`);
            $errorDiv = $el.siblings('.msg-error');
        }

        if (esValido) {
            $el.removeClass('is-invalid').addClass('is-valid');
            $errorDiv.hide().text("");
        } else {
            $el.removeClass('is-valid').addClass('is-invalid');
            $errorDiv.text(mensaje).show();
        }
        
        verificarFormularios();
    }

    function verificarFormularios() {

        let registroValido = true;
        const camposRegistro = [
            '#nombre', '#id_marca', '#id_categoria', 
            '#stock_minimo', '#stock_maximo', '#stock_actual', '#precio'
        ];
        camposRegistro.forEach(selector => {
            if (!$(selector).hasClass('is-valid')) registroValido = false;
        });
        $("#btnRegistrarProducto").prop('disabled', !registroValido);

        let modificarValido = true;
        const camposModificar = [
            '#nombreModificar', '#marcaModificar', '#categoriaModificar', 
            '#stock_minimoModificar', '#stock_maximoModificar', '#stock_actualModificar', '#precioModificar'
        ];
        camposModificar.forEach(selector => {
            if (!$(selector).hasClass('is-valid')) modificarValido = false;
        });
        $("#btnModificarProducto").prop('disabled', !modificarValido);
    }

    function capitalizarPalabras(cadena) {
        if (!cadena) return "";
        return cadena.replace(/\b\w+/g, w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase());
    }

    function validarCampoProducto(id) {
        let $input = $("#" + id);
        let entrada = $input.val();

        if (id === "nombre" || id === "nombreModificar") {
            if (/[0-9]/.test(entrada)) {
                $input.val(entrada.replace(/[0-9]/g, ""));
                gestionarEstado(id, false, "Este campo no acepta números");
                return;
            }

            let soloLetras = entrada.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s-]/g, "");
            let capitalizado = capitalizarPalabras(soloLetras);
            
            if (entrada !== capitalizado) {
                let pos = $input.prop("selectionStart");
                $input.val(capitalizado);
                $input.prop("selectionStart", pos).prop("selectionEnd", pos);
            }

            let valorTrim = capitalizado.trim();
            if (valorTrim.length === 0) {
                gestionarEstado(id, false, "El nombre del producto es obligatorio");
            } else if (valorTrim.length < 3 || valorTrim.length > 30) {
                gestionarEstado(id, false, "Debe tener entre 3 y 30 caracteres");
            } else {
                gestionarEstado(id, true);
            }
        }

        if (id === "descripcion" || id === "descripcionModificar") {
            let valorTrim = entrada.trim();
            if (valorTrim.length > 0) {
                if (valorTrim.length < 4 || valorTrim.length > 40) {
                    gestionarEstado(id, false, "La descripción debe tener entre 4 y 40 caracteres");
                } else {
                    gestionarEstado(id, true);
                }
            } else {
              
                $input.removeClass('is-invalid is-valid');
                $input.siblings('.msg-error, .text-danger').hide().text("");
            }
        }

        if (id === "id_marca" || id === "marcaModificar" || id === "id_categoria" || id === "categoriaModificar") {
            if (entrada === "" || entrada === null) {
                gestionarEstado(id, false, "Debe seleccionar una opción obligatoriamente");
            } else {
                gestionarEstado(id, true);
            }
        }

        if (id.includes('stock')) {
            let limpio = entrada.replace(/[^0-9]/g, "");
            if (entrada !== limpio) $input.val(limpio);

            if (limpio === "") {
                gestionarEstado(id, false, "Este campo es requerido");
            } else if (parseInt(limpio) < 0) {
                gestionarEstado(id, false, "No se permiten valores negativos");
            } else {
                gestionarEstado(id, true);
            }
        }

        if (id === "precio" || id === "precioModificar") {
            let limpio = entrada.replace(/[^0-9.]/g, "");
            const partes = limpio.split(".");
            if (partes.length > 2) {
                limpio = partes[0] + "." + partes.slice(1).join("").slice(0, 2);
            }
            if (entrada !== limpio) $input.val(limpio);

            if (limpio === "" || isNaN(limpio) || parseFloat(limpio) <= 0) {
                gestionarEstado(id, false, "Ingrese un precio válido mayor a 0");
            } else {
                gestionarEstado(id, true);
            }
        }
    }

    const camposMonitoreados = [
        "nombre", "nombreModificar", 
        "descripcion", "descripcionModificar",
        "id_marca", "marcaModificar", 
        "id_categoria", "categoriaModificar",
        "stock_minimo", "stock_minimoModificar", 
        "stock_maximo", "stock_maximoModificar", 
        "stock_actual", "stock_actualModificar",
        "precio", "precioModificar"
    ];

    camposMonitoreados.forEach(id => {
        $("#" + id).on("input change blur", () => validarCampoProducto(id));
    });

    $('.modal').on('hidden.bs.modal', function () {
        if ($(this).find('form').length && $(this).find('form')[0]) {
            $(this).find('form')[0].reset();
        }
        $(this).find('.form-control, select').removeClass('is-valid is-invalid');
        $(this).find('.msg-error, .text-danger').hide().text("");
        
        $("#btnRegistrarProducto, #btnModificarProducto").prop('disabled', true);
        
        if (!$('.modal.show').length) {
            $('body').removeClass('modal-open').css('overflow', '');
            $('.modal-backdrop').remove();
        }
    });

    $("#btnRegistrarProducto, #btnModificarProducto").prop('disabled', true);
});
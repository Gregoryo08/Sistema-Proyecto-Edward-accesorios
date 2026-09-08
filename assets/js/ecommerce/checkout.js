$(function() {

    let tipoEntrega = 'retiro';
    let costoEnvio = 0;

    // =============================================
    // VALIDACIONES
    // =============================================
    function validarFormularioCheckout() {
        let nombre = $('#nombre').val().trim();
        let email = $('#email').val().trim();
        let telefono = $('#telefono').val().trim();

        if (!validarTextoNoVacio(nombre)) {
            mostrarError('El nombre es obligatorio');
            return false;
        }
        if (!validarCorreo(email)) {
            mostrarError('Correo electrónico inválido');
            return false;
        }
        if (!validarTelefono(telefono)) {
            mostrarError('Teléfono inválido (mínimo 7 dígitos)');
            return false;
        }
        return true;
    }

    // =============================================
    // MOSTRAR RESUMEN DEL CARRITO
    // =============================================
    function mostrarResumen() {
        let carrito = JSON.parse(localStorage.getItem('carrito') || '[]');

        if (carrito.length === 0) {
            $('#resumen-carrito').html('<div class="alert alert-warning">No tienes productos en el carrito</div>');
            $('#total-final').text('$0.00');
            return;
        }

        let html = '<strong>Resumen del pedido:</strong><ul>';
        let subtotalProductos = 0;
        carrito.forEach(function(item) {
            let subtotal = parseFloat(item.precio) * parseInt(item.cantidad);
            subtotalProductos += subtotal;
            html += '<li>' + item.nombre + ' x ' + item.cantidad + ' - $' + subtotal.toFixed(2) + '</li>';
        });
        html += '</ul>';
        html += '<p>Subtotal productos: $' + subtotalProductos.toFixed(2) + '</p>';
        html += '<p>Envío: ' + (costoEnvio > 0 ? '$' + costoEnvio.toFixed(2) : 'Gratis') + '</p>';
        html += '<p class="fw-bold">Total: $' + (subtotalProductos + costoEnvio).toFixed(2) + '</p>';

        $('#resumen-carrito').html(html);
        $('#total-final').text('$' + (subtotalProductos + costoEnvio).toFixed(2));
    }

    // =============================================
    // ACTUALIZAR TOTAL (al cambiar delivery/retiro)
    // =============================================
    function actualizarTotal() {
        let carrito = JSON.parse(localStorage.getItem('carrito') || '[]');
        let subtotal = carrito.reduce(function(sum, item) {
            return sum + (parseFloat(item.precio) || 0) * (parseInt(item.cantidad) || 1);
        }, 0);
        let total = subtotal + costoEnvio;
        $('#total-final').text('$' + total.toFixed(2));
    }

    // =============================================
    // ENVIAR PEDIDO (AJAX)
    // =============================================
    function enviarPedido() {
        let carrito = JSON.parse(localStorage.getItem('carrito') || '[]');

        if (!validarFormularioCheckout()) return;
        if (carrito.length === 0) {
            mostrarError('El carrito está vacío');
            return;
        }

        let nombre = $('#nombre').val().trim();
        let email = $('#email').val().trim();
        let telefono = $('#telefono').val().trim();
        let cedula = $('#cedula_persona').val().trim();
        let metodoPago = $('input[name="metodo_pago"]:checked').val();

        if (!metodoPago) {
            mostrarError('Selecciona el método de pago');
            return;
        }

        let direccion = '';
        if (tipoEntrega === 'delivery') {
            direccion = $('#direccion').val().trim();
            if (!validarTextoNoVacio(direccion)) {
                mostrarError('Ingresa tu dirección de entrega');
                return;
            }
        }

        const pedido = {
            cliente: { cedula: cedula, nombre: nombre, email: email, telefono: telefono },
            entrega: tipoEntrega,
            direccion: direccion,
            costo_envio: costoEnvio,
            total: carrito.reduce(function(sum, item) {
                return sum + (parseFloat(item.precio) || 0) * (parseInt(item.cantidad) || 1);
            }, 0) + costoEnvio,
            carrito: carrito,
            metodo_pago: metodoPago
        };

        Swal.fire({
            title: 'Procesando...',
            text: 'Guardando tu pedido',
            allowOutsideClick: false,
            didOpen: function() { Swal.showLoading(); }
        });

        $.ajax({
            url: '?pagina=guardarPedido',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(pedido),
            success: function(res) {
                if (res.success) {
                    localStorage.removeItem('carrito');
                    Swal.fire({
                        icon: 'success',
                        title: '¡Pedido creado!',
                        text: 'Ahora completa el pago',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.href = '?pagina=datosPago&pedido=' + res.id_pedido + '&metodo=' + metodoPago;
                    });
                } else {
                    mostrarError(res.error || 'No se pudo guardar el pedido');
                }
            },
            error: function() {
                mostrarError('Problemas de conexión con el servidor');
            }
        });
    }

    // =============================================
    // EVENTOS
    // =============================================
    $('#btn-retiro').on('click', function() {
        tipoEntrega = 'retiro';
        costoEnvio = 0;
        $('#delivery-form').hide();
        actualizarTotal();
    });

    $('#btn-delivery').on('click', function() {
        tipoEntrega = 'delivery';
        costoEnvio = 10;
        $('#delivery-form').show();
        actualizarTotal();
    });

    $('#btn-confirmar').on('click', function(e) {
        e.preventDefault();
        enviarPedido();
    });

    // =============================================
    // INICIALIZACIÓN
    // =============================================
    mostrarResumen();
});

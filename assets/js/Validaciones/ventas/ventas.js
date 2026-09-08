$(document).ready(function () {
    let todosLosProductos = [];
    let carrito = [];
    let clienteSeleccionado = null;
    let listaPagosRegistrados = [];

    const IVA_PORCENTAJE = 0.16;
    let TASA_DOLAR = 0;
    let categoriaSeleccionada = "todas";
    let totalDolarCalculado = 0;
    let subtotalDolarCalculado = 0;

    function cargarTasaDolar() {
        $.ajax({
            url: '?pagina=ventas&accion=obtenerTasaCambio',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.tasa && data.tasa > 0) {
                    TASA_DOLAR = parseFloat(data.tasa);
                    if (todosLosProductos.length > 0) {
                        aplicarFiltros();
                    }
                }
            },
            error: function () {
                console.warn('⚠️ No se pudo cargar la tasa de cambio.');
            }
        });
    }
    cargarTasaDolar();

    function obtenerCatalogo() {
        renderizarTabla([], true);
        $.ajax({
            url: '?pagina=ventas&accion=listarProductos',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    todosLosProductos = res.data;
                    renderizarTabla(todosLosProductos);
                    cargarCategoriasFiltro();
                } else {
                    console.error("Error al obtener catálogo:", res.mensaje);
                }
            },
            error: function (err) {
                console.error("Error de conexión en AJAX catálogo.", err);
            }
        });
    }

    function listarClientes() {
        $.ajax({
            url: '?pagina=ventas&accion=listarClientes',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                let html = '';
                if (res.success && res.data.length > 0) {
                    res.data.forEach(cliente => {
                        html += `<tr>
                            <td class="ps-3 fw-medium text-center">${cliente.cedula_persona}</td>
                            <td><div class="fw-bold text-dark text-center">${cliente.nombre} ${cliente.apellido}</div></td>
                            <td class="text-muted text-center"><i class="fa-solid fa-phone fa-xs me-1"></i> ${cliente.telefono}</td>
                            <td class="text-center pe-3">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-medium" 
                                    onclick="selectClient('${cliente.nombre} ${cliente.apellido}', '${cliente.cedula_persona}')">Seleccionar</button>
                            </td>
                        </tr>`;
                    });
                } else {
                    html = `<tr><td colspan="4" class="text-center text-muted py-4"><i class="fa-solid fa-users-slash me-2"></i>No hay clientes registrados.</td></tr>`;
                }
                $('#tablaClientesModal tbody').html(html);
            },
            error: function (err) {
                console.error("Error de conexión en AJAX clientes.", err);
            }
        });
    }

    if (typeof aplicarRestriccionesCliente === 'function') {
        aplicarRestriccionesCliente();
    }

    $('#formRegistrarCliente').on('submit', function (e) {
        e.preventDefault();

        if (typeof validarFormularioCliente === 'function' && !validarFormularioCliente()) {
            return;
        }

        let cedulaCompleta = $('#prefijo').val() + $('#cedula').val().trim();
        let telefonoNum = $('#telefono').val().trim();
        let telefonoCompleto = telefonoNum !== '' ? ($('#operadora').val() + telefonoNum) : '';

        $.ajax({
            url: '?pagina=ventas&accion=registrarCliente',
            type: 'POST',
            data: {
                data: {
                    cedula: cedulaCompleta,
                    nombre: $('#nombre').val().trim(),
                    apellido: $('#apellido').val().trim(),
                    telefono: telefonoCompleto
                }
            },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Cliente registrado!',
                        text: res.mensaje,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    $('#formRegistrarCliente')[0].reset();

                    let modalRegistro = bootstrap.Modal.getInstance(document.getElementById('modalRegistrarCliente'));
                    if (modalRegistro) modalRegistro.hide();

                    listarClientes();

                    let modalBuscar = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalBuscarCliente'));
                    modalBuscar.show();

                } else {
                    let esCedulaDuplicada = res.mensaje.toLowerCase().includes('duplicate') ||
                        res.mensaje.toLowerCase().includes('ya existe') ||
                        res.mensaje.toLowerCase().includes('1062');

                    Swal.fire({
                        icon: esCedulaDuplicada ? 'warning' : 'error',
                        title: esCedulaDuplicada ? 'Cliente ya registrado' : 'Error al registrar',
                        text: esCedulaDuplicada ? 'La cédula ingresada ya se encuentra registrada en el sistema.' : res.mensaje,
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#174276'
                    });

                    if (esCedulaDuplicada) {
                        $('#cedula').addClass('is-invalid').focus();
                    }
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de red',
                    text: 'No se pudo conectar con el servidor para verificar el cliente.',
                    confirmButtonColor: '#174276'
                });
            }
        });
    });

    function filtrarClientes() {
        let query = $('#searchClientInput').val().toLowerCase().trim();
        $('#tablaClientesModal tbody tr').each(function () {
            let nombre = $(this).find('td').eq(1).text().toLowerCase();
            let cedula = $(this).find('td').eq(0).text().toLowerCase();
            let telefono = $(this).find('td').eq(2).text().toLowerCase();
            if (nombre.includes(query) || cedula.includes(query) || telefono.includes(query)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    function obtenerMetodosPago() {
        $.ajax({
            url: '?pagina=ventas&accion=listarMetodosPago',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    let html = '<option value="" selected disabled>Seleccione un método de pago</option>';
                    res.data.forEach(metodo => {
                        html += `<option value="${metodo.id_metodopago}" data-currency="${metodo.moneda}">${metodo.nombre_metodopago} (${metodo.moneda})</option>`;
                    });
                    $('select[name="paymentMethod[]"]').html(html);
                } else {
                    console.error("Error al obtener métodos de pago:", res.mensaje);
                }
            },
            error: function (err) {
                console.error("Error de conexión en AJAX métodos de pago.", err);
            }
        });
    }

    function renderizarTabla(productos, cargando = false) {
        let html = '';
        if (cargando) {
            for (let i = 0; i < 6; i++) {
                html += `
                    <tr class="placeholder-glow">
                        <td class="align-middle text-start"><span class="placeholder col-8 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle"><span class="placeholder col-6 bg-secondary opacity-25 rounded-2"></span></td>
                        <td class="align-middle"><span class="placeholder col-4 bg-success opacity-25 rounded-2"></span></td>
                        <td class="align-middle"><span class="placeholder col-4 bg-primary opacity-25 rounded-2"></span></td>
                        <td class="align-middle"><span class="placeholder col-3 bg-success opacity-25 rounded-2"></span></td>
                    </tr>`;
            }
            $('#tablaProductos').html(html);
            return;
        }

        if (productos.length === 0) {
            html = `<tr><td colspan="5" class="text-muted py-3 text-center">No se encontraron productos coincidentes</td></tr>`;
        } else {
            productos.forEach(p => {
                const precioDetalle = parseFloat(p.precio_detal) || 0;
                const precioEnBs = precioDetalle * TASA_DOLAR;

                html += `
                    <tr class="item-producto" data-id="${p.id_producto}" style="cursor: pointer;">
                        <td class="text-start align-middle fw-medium">${p.nombre_producto}</td>
                        <td class="align-middle"><span class="badge bg-secondary text-capitalize">${p.categoria}</span></td>
                        <td class="align-middle fw-semibold text-success">$${precioDetalle.toFixed(2)}</td>
                        <td class="align-middle fw-semibold text-primary">Bs. ${precioEnBs.toFixed(2)}</td>
                        <td class="align-middle fw-bold"><span class="badge bg-success text-capitalize">${p.stock_actual}</span></td>
                    </tr>`;
            });
        }
        $('#tablaProductos').html(html);
    }

    function cargarCategoriasFiltro() {
        $.get("?pagina=productos&ajax=true&x=categorias", (r) => {
            let cats = typeof r === "string" ? JSON.parse(r) : r;
            let html = '<option value="todas">Todas las categorías</option>';
            cats.forEach(c => {
                html += `<option value="${c.nombre_categoria.toLowerCase()}">${c.nombre_categoria}</option>`;
            });
            $("#categoriesContainer").html(html);
        });
    }

    function aplicarFiltros() {
        let busqueda = $("#searchInput").val().toLowerCase().trim();
        let productosFiltrados = todosLosProductos.filter(p => {
            let coincideBusqueda = p.nombre_producto.toLowerCase().includes(busqueda);
            let coincideCategoria = (categoriaSeleccionada === "todas") || (p.categoria.toLowerCase() === categoriaSeleccionada);
            return coincideBusqueda && coincideCategoria;
        });
        renderizarTabla(productosFiltrados);
    }

    $(document).on('click', '#tablaProductos tr.item-producto', function () {
        let idProducto = $(this).data('id');
        let productoOriginal = todosLosProductos.find(p => p.id_producto == idProducto);

        if (!productoOriginal) return;

        let itemEnCarrito = carrito.find(item => item.id == idProducto);

        if (itemEnCarrito) {
            if (itemEnCarrito.cantidad >= parseInt(productoOriginal.stock_actual)) {
                Swal.fire({
                    icon: "error",
                    title: "¡Stock Insuficiente!",
                    text: `No puedes añadir más unidades. Stock disponible: ${productoOriginal.stock_actual}`
                });
                return;
            }
            itemEnCarrito.cantidad++;
        } else {
            if (parseInt(productoOriginal.stock_actual) <= 0) {
                Swal.fire({ icon: "error", title: "Sin Stock", text: "Producto agotado." });
                return;
            }
            carrito.push({
                id: productoOriginal.id_producto,
                nombre: productoOriginal.nombre_producto,
                precio: parseFloat(productoOriginal.precio_detal),
                cantidad: 1,
                stockMax: parseInt(productoOriginal.stock_actual)
            });
        }
        actualizarCarritoUI();
    });

    window.cambiarCantidad = function (id, cambio) {
        let item = carrito.find(i => i.id == id);
        if (!item) return;

        let prodCatalog = todosLosProductos.find(p => p.id_producto == id);
        let maxAvailable = prodCatalog ? parseInt(prodCatalog.stock_actual) : item.stockMax;

        if (item.cantidad + cambio > maxAvailable) {
            Swal.fire({
                icon: "error",
                title: "¡Stock Insuficiente!",
                text: `No puedes añadir más unidades. Stock disponible: ${maxAvailable}`,
            });
            item.cantidad = maxAvailable;
        } else {
            item.cantidad += cambio;
        }

        if (item.cantidad <= 0) {
            carrito = carrito.filter(i => i.id != id);
        }

        actualizarCarritoUI();
    };

    window.eliminarItem = function (id) {
        carrito = carrito.filter(i => i.id != id);
        actualizarCarritoUI();
    };

    window.clearCart = function () {
        carrito = [];
        actualizarCarritoUI();
    };

    function actualizarCarritoUI() {
        if (carrito.length === 0) {
            $('#emptyCartMessage').removeClass('d-none');
            $('.cart-item-row').remove();
            $('#cartCountBadge').text(0);
            $('#btnPagar').prop('disabled', true);
            calcularTotales(0);
            return;
        }

        $('#emptyCartMessage').addClass('d-none');
        $('.cart-item-row').remove();

        let totalItemsCount = 0;
        let subtotalUSD = 0;

        carrito.forEach(item => {
            totalItemsCount += item.cantidad;
            subtotalUSD += (item.precio * item.cantidad);

            let filaHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3 cart-item-row bg-light p-2 rounded-3 border">
                    <div style="max-width: 60%;">
                        <h6 class="mb-0 fw-bold text-truncate small">${item.nombre}</h6>
                        <small class="text-muted">${item.precio.toFixed(2)} $ x unidad</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-danger" style="min-width:30px" onclick="cambiarCantidad(${item.id}, -1)">-</button>
                            <span class="btn btn-light fw-bold disabled" style="min-width:30px;">${item.cantidad}</span>
                            <button type="button" class="btn btn-outline-success" style="min-width:30px" onclick="cambiarCantidad(${item.id}, 1)">+</button>
                        </div>
                        <button type="button" class="btn btn-danger d-flex align-items-center justify-content-center rounded-3" 
                            style="width: 26px; height: 26px;" onclick="eliminarItem(${item.id})">
                            <i class="fa-solid fa-xmark fa-md"></i>
                        </button>
                    </div>
                </div>`;
            $('#cartItemsList').append(filaHTML);
        });

        $('#cartCountBadge').text(totalItemsCount);
        $('#btnPagar').prop('disabled', false);
        calcularTotales(subtotalUSD);
    }

    function calcularTotales(subtotalUSD) {
        subtotalDolarCalculado = subtotalUSD;
        let ivaUSD = subtotalUSD * IVA_PORCENTAJE;
        totalDolarCalculado = subtotalUSD + ivaUSD;

        let subtotalBs = subtotalUSD * TASA_DOLAR;
        let ivaBs = ivaUSD * TASA_DOLAR;
        let totalBs = totalDolarCalculado * TASA_DOLAR;

        $('#subtotalVal').text(`${subtotalBs.toFixed(2)} Bs`);
        $('#ivaVal').text(`${ivaBs.toFixed(2)} Bs`);
        $('#totalMainVal').text(`${totalBs.toFixed(2)} Bs`);
        $('#totalAltVal').text(`Equivale a: $${totalDolarCalculado.toFixed(2)}`);

        $('#totalAmountDollar').text(`$${totalDolarCalculado.toFixed(2)}`);
        $('#totalAmount').text(`${totalBs.toFixed(2)} bs`);

        recalcularMontosYVuelto();
    }

    window.openClientModal = function () {
        $('#searchClientInput').val('');
        let modalElement = document.getElementById('modalBuscarCliente');
        let modalCliente = bootstrap.Modal.getOrCreateInstance(modalElement);
        modalCliente.show();
        listarClientes();
    };

    window.selectClient = function (nombreCompleto, cedula) {
        clienteSeleccionado = { nombre: nombreCompleto, cedula: cedula };
        $('#selectedClientName').text(nombreCompleto);
        $('#selectedClientCed').text(cedula);
        $('#selectedClientContainer').removeClass('d-none');

        let modalCliente = bootstrap.Modal.getInstance(document.getElementById('modalBuscarCliente'));
        if (modalCliente) modalCliente.hide();

        Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500
        }).fire({ icon: 'success', title: 'Cliente asignado al ticket' });
    };

    window.removeSelectedClient = function () {
        clienteSeleccionado = null;
        $('#selectedClientContainer').addClass('d-none');
    };

    window.openPaymentModal = function () {
        listaPagosRegistrados = [];
        $('.sublista-pagos-container').remove();
        $('#montoPagoInput').val('');
        $('#refPago').val('');

        let modalElement = document.getElementById('paymentModal');
        let modalPago = bootstrap.Modal.getOrCreateInstance(modalElement);
        modalPago.show();

        obtenerMetodosPago();
        recalcularMontosYVuelto();
    };

    $(document).on('change', 'select[name="paymentMethod[]"]', function () {
        let moneda = $(this).find(':selected').data('currency') || 'USD';
        if (moneda === 'VES') {
            $('#simboloMonedaInput').text('Bs.');
            $('#siglaMoneda').text('VES');
            $('#conversionAyuda').removeClass('d-none');
        } else {
            $('#simboloMonedaInput').text('$');
            $('#siglaMoneda').text('USD');
            $('#conversionAyuda').addClass('d-none');
        }
        actualizarSugerenciaConversion();
    });

    // --- FORMATEO AUTOMÁTICO TIPO CAJERO AUTOMÁTICO EN EL INPUT ---
    $(document).on('input', '#montoPagoInput', function () {
        let input = $(this);
        let valor = input.val();

        let digitos = valor.replace(/\D/g, '');

        if (digitos === '') {
            input.val('');
            actualizarSugerenciaConversion();
            return;
        }

        let entero = parseInt(digitos, 10);
        let monto = (entero / 100).toFixed(2);

        input.val(monto.replace('.', ','));
        actualizarSugerenciaConversion();
    });

    // Función auxiliar para obtener el valor flotante real (convirtiendo coma a punto)
    function obtenerMontoPagoNumerico() {
        let val = $('#montoPagoInput').val();
        if (!val) return 0;
        return parseFloat(val.replace(',', '.')) || 0;
    }

    function actualizarSugerenciaConversion() {
        let moneda = $('select[name="paymentMethod[]"]').find(':selected').data('currency') || 'USD';
        let monto = obtenerMontoPagoNumerico();
        if (moneda === 'VES' && monto > 0 && TASA_DOLAR > 0) {
            let equivUSD = monto / TASA_DOLAR;
            $('#conversionAyuda').text(`Equivale aprox a: $${equivUSD.toFixed(2)} USD`);
        } else {
            $('#conversionAyuda').text('');
        }
    }

    window.pagoExacto = function () {
        let totalDolar = totalDolarCalculado;
        let abonadoDolar = 0;
        listaPagosRegistrados.forEach(p => abonadoDolar += p.monto_dolar);
        let pendienteDolar = totalDolar - abonadoDolar;
        if (pendienteDolar <= 0) return;

        let moneda = $('select[name="paymentMethod[]"]').find(':selected').data('currency') || 'USD';
        if (moneda === 'VES') {
            let pendienteBs = pendienteDolar * TASA_DOLAR;
            $('#montoPagoInput').val(pendienteBs.toFixed(2).replace('.', ','));
        } else {
            $('#montoPagoInput').val(pendienteDolar.toFixed(2).replace('.', ','));
        }
        actualizarSugerenciaConversion();
    };

    window.agregarPago = function () {
        let selectElement = $('select[name="paymentMethod[]"]');
        let idMetodo = selectElement.val();
        let nombreMetodo = selectElement.find(':selected').text();
        let moneda = selectElement.find(':selected').data('currency');
        let monto = obtenerMontoPagoNumerico();
        let referencia = $('#refPago').val().trim();

        if (!idMetodo || isNaN(monto) || monto <= 0) {
            Swal.fire({ icon: "error", title: "Monto inválido", text: "Verifique el método y el monto ingresado." });
            return;
        }

        let montoDolar = (moneda === 'VES') ? (monto / TASA_DOLAR) : monto;
        let montoBs = (moneda === 'VES') ? monto : (monto * TASA_DOLAR);

        listaPagosRegistrados.push({
            id_metodopago: idMetodo,
            metodo: nombreMetodo,
            moneda: moneda,
            monto_original: monto,
            monto_dolar: montoDolar,
            monto_bs: montoBs,
            referencia: referencia
        });

        $('#montoPagoInput').val('');
        $('#refPago').val('');
        recalcularMontosYVuelto();
        renderizarListaPagosParciales();
    };

    function renderizarListaPagosParciales() {
        $('.sublista-pagos-container').remove();

        if (listaPagosRegistrados.length === 0) return;

        let listHTML = `<div class="sublista-pagos-container mt-3 border-top pt-2">
                            <label class="text-xs text-muted fw-bold d-block mb-2">Abonos Agregados:</label>
                            <ul class="list-group">`;
        listaPagosRegistrados.forEach((p, idx) => {
            listHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center bg-white p-2 small border rounded-3 mb-1">
                    <div>
                        <strong>${p.metodo}</strong> ${p.referencia ? `<span class="badge bg-secondary ms-1">Ref: ${p.referencia}</span>` : ''}
                        <br><span class="text-muted">${p.monto_original.toFixed(2)} ${p.moneda}</span>
                    </div>
                    <button type="button" class="btn btn-sm text-danger p-1" onclick="eliminarPagoRegistrado(${idx})">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </li>`;
        });
        listHTML += `</ul></div>`;

        $('#contenedorListaPagos').append(listHTML);
    }

    window.eliminarPagoRegistrado = function (idx) {
        listaPagosRegistrados.splice(idx, 1);
        recalcularMontosYVuelto();
        renderizarListaPagosParciales();
    };

    function recalcularMontosYVuelto() {
        let totalDolar = totalDolarCalculado;

        let totalAbonadoDolar = 0;
        listaPagosRegistrados.forEach(p => totalAbonadoDolar += p.monto_dolar);
        let totalAbonadoBs = totalAbonadoDolar * TASA_DOLAR;

        let restanteDolar = totalDolar - totalAbonadoDolar;

        $('#mTotalAbonado').text(`$${totalAbonadoDolar.toFixed(2)}`);
        $('#mTotalAbonadoBs').text(`${totalAbonadoBs.toFixed(2)} bs`);

        if (restanteDolar > 0.001) {
            let restanteBs = restanteDolar * TASA_DOLAR;
            $('#lblRestante').text("Por Pagar").removeClass('text-success').addClass('text-warning');
            $('#mTotalRestante').text(`$${restanteDolar.toFixed(2)}`).removeClass('text-success').addClass('text-warning');
            $('#mTotalRestanteBs').text(`${restanteBs.toFixed(2)} bs`);
            $('#cardRestante').removeClass('border-success').addClass('border-warning');
        } else {
            let vueltoDolar = Math.abs(restanteDolar);
            let vueltoBs = vueltoDolar * TASA_DOLAR;
            
            $('#lblRestante').text("Vuelto").removeClass('text-warning').addClass('text-success');
            $('#mTotalRestante').text(`$${vueltoDolar.toFixed(2)}`).removeClass('text-warning').addClass('text-success');
            $('#mTotalRestanteBs').text(`${vueltoBs.toFixed(2)} bs`);
            $('#cardRestante').removeClass('border-warning').addClass('border-success');
        }
    }

    $('#paymentForm').on('submit', function (e) {
        e.preventDefault();

        let totalDolar = totalDolarCalculado;
        let totalAbonadoDolar = 0;
        listaPagosRegistrados.forEach(p => totalAbonadoDolar += p.monto_dolar);

        if (listaPagosRegistrados.length === 0) {
            Swal.fire({
                icon: "error",
                title: "Pagos no registrados",
                text: "Debe registrar al menos un pago para procesar la venta."
            });
            return;
        }

        if (totalAbonadoDolar < (totalDolar - 0.01)) {
            Swal.fire({
                icon: "error",
                title: "Monto Insuficiente",
                text: "Los pagos cargados no cubren el monto total de la venta."
            });
            return;
        }

        let datosVenta = {
            cliente: clienteSeleccionado,
            items: carrito,
            pagos: listaPagosRegistrados,
            total_usd: totalDolar,
            tasa_cambio: TASA_DOLAR
        };

        $.ajax({
            url: '?pagina=ventas',
            type: 'POST',
            data: {
                accion: 'procesarVenta',
                data: JSON.stringify(datosVenta)
            },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    let modalPago = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                    if (modalPago) modalPago.hide();

                    Swal.fire({
                        position: "center",
                        icon: 'success',
                        title: '¡Venta Realizada!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        clearCart();
                        removeSelectedClient();
                        obtenerCatalogo();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en la Transacción',
                        text: res.mensaje
                    });
                }
            },
            error: function (err) {
                console.error("Error crítico en el procesado de venta por AJAX.", err);
                Swal.fire({ icon: 'error', title: 'Error de Red', text: 'No se pudo comunicar con el servidor.' });
            }
        });
    });

    $("#searchClientInput").on("keyup", function () { filtrarClientes(); });
    $("#searchInput").on("keyup", function () { aplicarFiltros(); });
    $("#categoriesContainer").on("change", function () {
        categoriaSeleccionada = $(this).val();
        aplicarFiltros();
    });

    obtenerCatalogo();
});
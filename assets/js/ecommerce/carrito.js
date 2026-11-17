
function obtenerCarrito() {
    try {
        return JSON.parse(localStorage.getItem('carrito') || '[]');
    } catch (e) {
        return [];
    }
}


$.ajax({
    url: '?pagina=verificarSesion',
    type: 'GET',
    dataType: 'json',
    success: function(response) {
        if (response.logueado || response.logged || response.success) {
           
            $('#btn-login').hide();
            
           
            $('#dropdownUser').show();
            $('#nombre-usuario').text(response.nombre || response.usuario || 'Usuario');
        } else {
            $('#btn-login').show();
            $('#dropdownUser').hide();
        }
    }
});

function guardarCarrito(carrito) {
    localStorage.setItem('carrito', JSON.stringify(carrito));
}

function actualizarContadorCarrito() {
    var carrito = obtenerCarrito();
    var total = carrito.reduce(function(sum, item) {
        return sum + (item.cantidad || 1);
    }, 0);
    
    document.querySelectorAll('#cartCount').forEach(function(badge) {
        badge.innerText = total;
    });
}

function mostrarModalDecision() {
    var modalElement = document.getElementById('modalDecision');
    if (modalElement) {
        var modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    } else {
        window.location.href = '?pagina=login_principal&destino=checkout';
    }
}

function renderizarCarrito() {
    var carrito = obtenerCarrito();
    var contenedor = document.getElementById('carrito-contenido');
    
    if (!contenedor) {
        console.error('❌ No se encontró el contenedor #carrito-contenido');
        return;
    }

    fetch('?pagina=verificarSesion')
        .then(response => response.json())
        .then(data => {
            var logueado = data.logueado || false;
            
            if (carrito.length === 0) {
                contenedor.innerHTML = `
                    <div class="alert alert-info text-center py-5">
                        <h4>🛒 Carrito vacío</h4>
                        <p class="mb-3">No tienes productos en tu carrito</p>
                        <a href="?pagina=web_Catalogo" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Seguir comprando
                        </a>
                    </div>
                `;
                
                agregarBotonesCarrito(contenedor, logueado, true);
                return;
            }

            var html = `
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            var total = 0;
            carrito.forEach(function(item, index) {
                var subtotal = parseFloat(item.precio) * (item.cantidad || 1);
                total += subtotal;
                html += `
                    <tr>
                        <td><strong>${item.nombre}</strong></td>
                        <td>$${parseFloat(item.precio).toFixed(2)}</td>
                        <td>
                            <input type="number" 
                                   value="${item.cantidad || 1}" 
                                   min="1" 
                                   max="99"
                                   class="form-control cantidad-input" 
                                   data-index="${index}"
                                   style="width:80px;">
                        </td>
                        <td>$${subtotal.toFixed(2)}</td>
                        <td>
                            <button class="btn btn-danger btn-sm eliminar-item" data-index="${index}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td><strong>$${total.toFixed(2)}</strong></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" id="vaciar-carrito">
                                        <i class="fas fa-trash-alt"></i> Vaciar
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;

            contenedor.innerHTML = html;
            
            agregarBotonesCarrito(contenedor, logueado, false);
            
            asignarEventosCarrito();
        })
        .catch(function() {
            if (carrito.length === 0) {
                contenedor.innerHTML = `
                    <div class="alert alert-info text-center py-5">
                        <h4>🛒 Carrito vacío</h4>
                        <p class="mb-3">No tienes productos en tu carrito</p>
                        <a href="?pagina=web_Catalogo" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Seguir comprando
                        </a>
                    </div>
                `;
            }
            agregarBotonesCarrito(contenedor, false, carrito.length === 0);
        });
}

function agregarBotonesCarrito(contenedor, logueado, vacio) {
    var misPedidosBtn = '';
    if (logueado) {
        misPedidosBtn = `
            <a href="?pagina=misPedidos" class="btn btn-info btn-lg me-2">
                <i class="fas fa-box"></i> Mis Pedidos
            </a>
        `;
    }
    
    var btnPagar = '';
    if (vacio) {
        btnPagar = `
            <button class="btn btn-secondary btn-lg" disabled style="opacity: 0.6; cursor: not-allowed;">
                <i class="fas fa-credit-card"></i> Proceder al pago
            </button>
        `;
    } else {
        btnPagar = `
            <button id="btn-pagar" class="btn btn-success btn-lg">
                <i class="fas fa-credit-card"></i> Proceder al pago
            </button>
        `;
    }
    
    var botonesHTML = `
        <div class="d-flex justify-content-between mt-3">
            <a href="?pagina=web_Catalogo" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Seguir comprando
            </a>
            <div>
                ${misPedidosBtn}
                ${btnPagar}
            </div>
        </div>
    `;
    
    contenedor.insertAdjacentHTML('beforeend', botonesHTML);
    
    if (!vacio) {
        document.getElementById('btn-pagar')?.addEventListener('click', function() {
            var carrito = obtenerCarrito();
            if (carrito.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Carrito vacío',
                    text: 'No tienes productos para pagar',
                    confirmButtonText: 'Ir al catálogo'
                });
                return;
            }
            
            fetch('?pagina=verificarSesion')
                .then(response => response.json())
                .then(data => {
                    if (data.logueado) {
                        window.location.href = '?pagina=checkout';
                    } else {
                        mostrarModalDecision();
                    }
                })
                .catch(function(error) {
                    console.error('Error al verificar sesión:', error);
                    mostrarModalDecision();
                });
        });
    }
}

function asignarEventosCarrito() {
    document.querySelectorAll('.eliminar-item').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var index = parseInt(this.dataset.index);
            var carrito = obtenerCarrito();
            var nombre = carrito[index]?.nombre || 'Producto';
            
            Swal.fire({
                title: '¿Eliminar?',
                text: '¿Quieres eliminar "' + nombre + '" del carrito?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    carrito.splice(index, 1);
                    guardarCarrito(carrito);
                    renderizarCarrito();
                    actualizarContadorCarrito();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });
    });

    document.querySelectorAll('.cantidad-input').forEach(function(input) {
        input.addEventListener('change', function() {
            var index = parseInt(this.dataset.index);
            var cantidad = parseInt(this.value) || 1;
            
            if (cantidad < 1) {
                this.value = 1;
                cantidad = 1;
            }
            if (cantidad > 99) {
                this.value = 99;
                cantidad = 99;
            }
            
            var carrito = obtenerCarrito();
            if (carrito[index]) {
                carrito[index].cantidad = cantidad;
                guardarCarrito(carrito);
                renderizarCarrito();
                actualizarContadorCarrito();
            }
        });
    });

    document.getElementById('vaciar-carrito')?.addEventListener('click', function() {
        Swal.fire({
            title: '¿Vaciar carrito?',
            text: 'Esta acción eliminará todos los productos',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, vaciar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                guardarCarrito([]);
                renderizarCarrito();
                actualizarContadorCarrito();
                Swal.fire({
                    icon: 'success',
                    title: 'Carrito vaciado',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });
}


document.addEventListener('DOMContentLoaded', function() {
    console.log('🛒 Carrito.js cargado correctamente');
    renderizarCarrito();
    actualizarContadorCarrito();
});

if (document.readyState === 'complete' || document.readyState === 'interactive') {
    console.log('🛒 DOM ya cargado, ejecutando carrito...');
    renderizarCarrito();
    actualizarContadorCarrito();
}
// /src/assets/js/ecommerce/carrito.js
// =============================================
// CARRITO DE COMPRAS - FUNCIONES COMPLETAS
// =============================================

/**
 * Obtener carrito desde localStorage
 */
function obtenerCarrito() {
    try {
        return JSON.parse(localStorage.getItem('carrito') || '[]');
    } catch (e) {
        return [];
    }
}

/**
 * Guardar carrito en localStorage
 */
function guardarCarrito(carrito) {
    localStorage.setItem('carrito', JSON.stringify(carrito));
}

/**
 * Actualizar contador del carrito en el header
 */
function actualizarContadorCarrito() {
    var carrito = obtenerCarrito();
    var total = carrito.reduce(function(sum, item) {
        return sum + (item.cantidad || 1);
    }, 0);
    
    document.querySelectorAll('#cartCount').forEach(function(badge) {
        badge.innerText = total;
    });
}

/**
 * Función para mostrar modal de decisión
 */
function mostrarModalDecision() {
    var modalElement = document.getElementById('modalDecision');
    if (modalElement) {
        var modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    } else {
        window.location.href = '?pagina=loginEcommerce&destino=checkout';
    }
}

/**
 * Renderizar el carrito en la página
 */
function renderizarCarrito() {
    var carrito = obtenerCarrito();
    var contenedor = document.getElementById('carrito-contenido');
    
    if (!contenedor) {
        console.error('❌ No se encontró el contenedor #carrito-contenido');
        return;
    }

    // ✅ PRIMERO: Verificar sesión para saber si mostrar "Mis Pedidos"
    fetch('?pagina=verificarSesion')
        .then(response => response.json())
        .then(data => {
            var logueado = data.logueado || false;
            
            // ✅ CARRITO VACÍO
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
                
                // ✅ AGREGAR BOTONES (INCLUYENDO "MIS PEDIDOS" SI ESTÁ LOGUEADO)
                agregarBotonesCarrito(contenedor, logueado, true);
                return;
            }

            // ✅ CARRITO CON PRODUCTOS
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
            
            // ✅ AGREGAR BOTONES (CON "MIS PEDIDOS" SI ESTÁ LOGUEADO)
            agregarBotonesCarrito(contenedor, logueado, false);
            
            // ✅ Asignar eventos (solo si hay productos)
            asignarEventosCarrito();
        })
        .catch(function() {
            // ❌ Si falla la verificación, NO mostrar "Mis Pedidos"
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

/**
 * Agregar botones del carrito con verificación de sesión
 * @param {HTMLElement} contenedor - Contenedor donde agregar los botones
 * @param {boolean} logueado - Si el usuario está logueado
 * @param {boolean} vacio - Si el carrito está vacío
 */
function agregarBotonesCarrito(contenedor, logueado, vacio) {
    // ✅ BOTÓN "MIS PEDIDOS" (SOLO SI ESTÁ LOGUEADO)
    var misPedidosBtn = '';
    if (logueado) {
        misPedidosBtn = `
            <a href="?pagina=misPedidos" class="btn btn-info btn-lg me-2">
                <i class="fas fa-box"></i> Mis Pedidos
            </a>
        `;
    }
    
    // ✅ BOTÓN "PROCEDER AL PAGO" (DESHABILITADO SI CARRITO VACÍO)
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
    
    // ✅ Si hay productos, asignar evento al botón "Proceder al pago"
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

/**
 * Asignar eventos del carrito (solo si hay productos)
 */
function asignarEventosCarrito() {
    // ✅ Eliminar producto
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

    // ✅ Cambiar cantidad
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

    // ✅ Vaciar carrito
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

// =============================================
// INICIALIZACIÓN
// =============================================

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
// ============================================
// MODAL CARRITO DE COMPRAS
// Lee el carrito desde localStorage ('carrito')
// y lo muestra en un modal en lugar de navegar.
// ============================================

function obtenerCarritoStorage() {
    return JSON.parse(localStorage.getItem('carrito') || '[]');
}

function guardarCarritoStorage(carrito) {
    localStorage.setItem('carrito', JSON.stringify(carrito));
}

function actualizarContadorCarritoModal() {
    const carrito = obtenerCarritoStorage();
    const total = carrito.reduce((sum, item) => sum + (item.cantidad || 0), 0);
    const badge = document.getElementById('cartCount');
    if (badge) {
        badge.innerText = total;
    }
}

function formatearPrecio(valor) {
    return '$' + parseFloat(valor || 0).toFixed(2);
}

function escaparHtml(texto) {
    return String(texto === undefined || texto === null ? '' : texto)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function calcularTotalCarrito(carrito) {
    return carrito.reduce((sum, item) => sum + (parseFloat(item.precio) * (item.cantidad || 0)), 0);
}

// ============================================
// RENDERIZADO DEL MODAL
// ============================================
function renderizarCarritoModal() {
    const contenedor = document.getElementById('cartItemsContainer');
    const totalEl = document.getElementById('cartTotalPrice');
    if (!contenedor) return;

    const carrito = obtenerCarritoStorage();

    if (carrito.length === 0) {
        contenedor.innerHTML =
            '<div class="cart-empty-state">' +
            '<i class="fas fa-shopping-cart"></i>' +
            '<p>Tu carrito está vacío.<br>¡Agrega productos desde el catálogo!</p>' +
            '</div>';
        if (totalEl) totalEl.textContent = formatearPrecio(0);
        return;
    }

    let html = '';
    carrito.forEach(function (item, index) {
        html +=
            '<div class="cart-item">' +
            '   <div class="cart-item-info">' +
            '       <div class="cart-item-title">' + escaparHtml(item.nombre) + '</div>' +
            '       <div class="cart-item-price">' + formatearPrecio(item.precio) + '</div>' +
            '   </div>' +
            '   <div class="cart-item-controls">' +
            '       <button type="button" class="btn-qty" data-accion="restar" data-index="' + index + '">-</button>' +
            '       <span class="cart-qty-num">' + (item.cantidad || 0) + '</span>' +
            '       <button type="button" class="btn-qty" data-accion="sumar" data-index="' + index + '">+</button>' +
            '   </div>' +
            '   <button type="button" class="btn-remove-item" data-accion="eliminar" data-index="' + index + '" aria-label="Eliminar">' +
            '       <i class="fas fa-trash-alt"></i>' +
            '   </button>' +
            '</div>';
    });

    contenedor.innerHTML = html;
    if (totalEl) totalEl.textContent = formatearPrecio(calcularTotalCarrito(carrito));
}

// ============================================
// ACCIONES SOBRE LOS PRODUCTOS
// ============================================
function procesarAccionCarrito(accion, index) {
    let carrito = obtenerCarritoStorage();
    if (!carrito[index]) return;

    if (accion === 'sumar') {
        carrito[index].cantidad = (carrito[index].cantidad || 0) + 1;
    } else if (accion === 'restar') {
        carrito[index].cantidad = (carrito[index].cantidad || 0) - 1;
        if (carrito[index].cantidad <= 0) {
            carrito.splice(index, 1);
        }
    } else if (accion === 'eliminar') {
        carrito.splice(index, 1);
    }

    guardarCarritoStorage(carrito);
    renderizarCarritoModal();
    actualizarContadorCarritoModal();
}

function vaciarCarrito() {
    const carrito = obtenerCarritoStorage();
    if (carrito.length === 0) return;

    const confirmar = (typeof Swal !== 'undefined')
        ? Swal.fire({
            title: '¿Vaciar carrito?',
            text: 'Se eliminarán todos los productos',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, vaciar',
            cancelButtonText: 'Cancelar'
          }).then(r => r.isConfirmed)
        : Promise.resolve(confirm('¿Vaciar el carrito?'));

    confirmar.then(ok => {
        if (!ok) return;
        localStorage.removeItem('carrito');
        renderizarCarritoModal();
        actualizarContadorCarritoModal();
    });
}

// ============================================
// ABRIR / CERRAR MODAL
// ============================================
function abrirCarritoCompras(e) {
    if (e) e.preventDefault();
    renderizarCarritoModal();
    const overlay = document.getElementById('cartModalOverlay');
    if (overlay) overlay.classList.add('active');
}

function cerrarCarritoCompras() {
    const overlay = document.getElementById('cartModalOverlay');
    if (overlay) overlay.classList.remove('active');
}

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    actualizarContadorCarritoModal();

    // Icono del carrito del menú → abre el modal (NO navega)
    const verCarrito = document.getElementById('verCarrito');
    if (verCarrito) {
        verCarrito.addEventListener('click', abrirCarritoCompras);
    }

    // Cerrar con la X
    const closeCartBtn = document.getElementById('closeCartBtn');
    if (closeCartBtn) {
        closeCartBtn.addEventListener('click', cerrarCarritoCompras);
    }

    // Cerrar al hacer clic fuera del modal
    const overlay = document.getElementById('cartModalOverlay');
    if (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) cerrarCarritoCompras();
        });
    }

    // Cerrar con tecla ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') cerrarCarritoCompras();
    });

    // Botones de cantidad / eliminar (delegación de eventos)
    const contenedor = document.getElementById('cartItemsContainer');
    if (contenedor) {
        contenedor.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-accion]');
            if (!btn) return;
            procesarAccionCarrito(btn.dataset.accion, parseInt(btn.dataset.index, 10));
        });
    }

    // Vaciar carrito
    const btnClearCart = document.getElementById('btnClearCart');
    if (btnClearCart) {
        btnClearCart.addEventListener('click', vaciarCarrito);
    }

    // Procesar compra: no permitir si está vacío
    const btnCheckout = document.getElementById('btnCheckout');
    if (btnCheckout) {
        btnCheckout.addEventListener('click', function (e) {
            if (obtenerCarritoStorage().length === 0) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Carrito vacío',
                        text: 'Agrega productos antes de procesar la compra',
                        confirmButtonText: 'Aceptar'
                    });
                } else {
                    alert('Tu carrito está vacío');
                }
            }
        });
    }
});

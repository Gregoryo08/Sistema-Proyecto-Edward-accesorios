// /src/assets/js/ecommerce/carritoContador.js
// =============================================
// Actualiza el contador del carrito (#cartCount) desde localStorage
// Usado en misPedidos.php y verPedido.php
// =============================================

function actualizarContadorCarrito() {
    try {
        var carrito = JSON.parse(localStorage.getItem('carrito') || '[]');
        var total = carrito.reduce(function(sum, item) {
            return sum + (item.cantidad || 1);
        }, 0);
        var badge = document.getElementById('cartCount');
        if (badge) {
            badge.innerText = total;
        }
    } catch (e) {
        console.log('Error al leer carrito:', e);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    actualizarContadorCarrito();
});

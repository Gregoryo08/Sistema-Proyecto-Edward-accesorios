function actualizarContadorCarrito() {
    let carrito = JSON.parse(localStorage.getItem('carrito') || '[]');
    let total = carrito.reduce((sum, item) => sum + (item.cantidad || 0), 0);
    const badge = document.getElementById('cartCount');
    if (badge) {
        badge.innerText = total;
        //  ANIMACIÓN: El badge rebota cuando cambia
        badge.style.transform = 'scale(1.5)';
        setTimeout(() => {
            badge.style.transform = 'scale(1)';
        }, 300);
    }
}


/**
 * Agregar producto al carrito con AJAX + Animación
 * @param {HTMLElement} btn - Botón que disparó la acción
 * @param {number} id - ID del producto
 * @param {string} nombre - Nombre del producto
 * @param {number} precio - Precio del producto
 */
function agregarAlCarritoDesdeCatalogo(btn, id, nombre, precio) {
    //  1. ANIMACIÓN: Botón feedback
    animarBoton(btn);

    //  2. OBTENER carrito actual
    let carrito = JSON.parse(localStorage.getItem('carrito') || '[]');
    let existente = carrito.find(item => item.id == id);

    //  3. ACTUALIZAR cantidad
    if (existente) {
        existente.cantidad = (existente.cantidad || 1) + 1;
    } else {
        carrito.push({
            id: id,
            nombre: nombre,
            precio: parseFloat(precio),
            cantidad: 1
        });
    }

    //  4. GUARDAR en localStorage
    localStorage.setItem('carrito', JSON.stringify(carrito));

    //  5. ACTUALIZAR contador (SIN recargar)
    actualizarContadorCarrito();

    //  6. FEEDBACK VISUAL (SweetAlert TOAST)
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: '¡Agregado!',
            text: nombre + ' agregado al carrito',
            timer: 1200,
            showConfirmButton: false,
            position: 'top-end',
            toast: true,
            timerProgressBar: true
        });
    } else {
        alert('Producto agregado al carrito');
    }
}

/**
 * ANIMACIÓN DEL BOTÓN
 * @param {HTMLElement} btn - Botón a animar
 */
function animarBoton(btn) {
    //  1. Guardar texto original
    const textoOriginal = btn.innerHTML;

    //  2. Cambiar texto y estilo (feedback visual)
    btn.innerHTML = '<i class="fas fa-check"></i> ¡Agregado!';
    btn.style.backgroundColor = '#28a745';  // Verde éxito
    btn.style.transition = 'all 0.3s ease';
    btn.style.transform = 'scale(0.95)';

    //  3. Efecto "pulse" (latido)
    setTimeout(() => {
        btn.style.transform = 'scale(1.05)';
    }, 150);

    //  4. Restaurar después de 1.5 segundos
    setTimeout(() => {
        btn.innerHTML = textoOriginal;
        btn.style.backgroundColor = '#3498db';  // Azul original
        btn.style.transform = 'scale(1)';
    }, 1200);
}

// gmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmg
// FILTRO DE PRODUCTOS (buscador + categoría)
// gmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmg

function filtrarProductos(valor) {
    const inputBuscador = document.getElementById('buscadorProductos');
    const termino = (valor !== undefined ? valor : (inputBuscador ? inputBuscador.value : '')).toLowerCase().trim();
    const selectCategoria = document.getElementById('selectCategoria');
    const categoria = selectCategoria ? selectCategoria.value : '';

    let visibles = 0;

    document.querySelectorAll('.product-card').forEach(function (card) {
        const titulo = (card.querySelector('.product-title')?.textContent || '').toLowerCase();
        const desc = (card.querySelector('.product-desc')?.textContent || '').toLowerCase();
        const coincideTexto = !termino || titulo.includes(termino) || desc.includes(termino);
        const coincideCategoria = !categoria || card.dataset.categoria === categoria;

        const mostrar = coincideTexto && coincideCategoria;
        card.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });

    // Mensaje cuando no hay resultados
    const sinResultados = document.getElementById('sinResultados');
    if (sinResultados) {
        sinResultados.style.display = visibles === 0 ? 'block' : 'none';
    }
}

// gmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmg
// INICIALIZACIÓN
// gmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmgmg

document.addEventListener('DOMContentLoaded', function () {
    //  ACTUALIZAR contador al cargar
    actualizarContadorCarrito();

    //  EVENTOS: Botones "Agregar al Carrito" (AJAX + Animación)
    document.querySelectorAll('.agregar-carrito').forEach(btn => {
        btn.addEventListener('click', function () {
            let id = this.dataset.id;
            let nombre = this.dataset.nombre;
            let precio = parseFloat(this.dataset.precio);

            //  PASAR EL BOTÓN para animarlo
            agregarAlCarritoDesdeCatalogo(this, id, nombre, precio);
        });
    });

    //  EVENTOS: Filtro de productos (buscador y categoría)
    const inputBusqueda = document.getElementById('buscadorProductos');
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', function () {
            filtrarProductos(this.value);
        });
    }

    const selectCategoria = document.getElementById('selectCategoria');
    if (selectCategoria) {
        selectCategoria.addEventListener('change', function () {
            filtrarProductos();
        });
    }

    //  EVENTO: Ver carrito → lo maneja carritoModal.js (modal en lugar de navegar)
});


function actualizarContadorCarrito() {
    let carrito = JSON.parse(localStorage.getItem('carrito') || '[]');
    let total = carrito.reduce((sum, item) => sum + (item.cantidad || 0), 0);
    const badge = document.getElementById('cartCount');
    if (badge) {
        badge.innerText = total;
        badge.style.transform = 'scale(1.5)';
        setTimeout(() => {
            badge.style.transform = 'scale(1)';
        }, 300);
    }
}


function agregarAlCarritoDesdeCatalogo(btn, id, nombre, precio) {
    animarBoton(btn);

    let carrito = JSON.parse(localStorage.getItem('carrito') || '[]');
    let existente = carrito.find(item => item.id == id);

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

    localStorage.setItem('carrito', JSON.stringify(carrito));

    actualizarContadorCarrito();

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

function animarBoton(btn) {
    const textoOriginal = btn.innerHTML;

    btn.innerHTML = '<i class="fas fa-check"></i> ¡Agregado!';
    btn.style.backgroundColor = '#28a745';  // Verde éxito
    btn.style.transition = 'all 0.3s ease';
    btn.style.transform = 'scale(0.95)';

    setTimeout(() => {
        btn.style.transform = 'scale(1.05)';
    }, 150);

    setTimeout(() => {
        btn.innerHTML = textoOriginal;
        btn.style.backgroundColor = '#3498db';  // Azul original
        btn.style.transform = 'scale(1)';
    }, 1200);
}


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

    const sinResultados = document.getElementById('sinResultados');
    if (sinResultados) {
        sinResultados.style.display = visibles === 0 ? 'block' : 'none';
    }
}


document.addEventListener('DOMContentLoaded', function () {
    actualizarContadorCarrito();

    document.querySelectorAll('.agregar-carrito').forEach(btn => {
        btn.addEventListener('click', function () {
            let id = this.dataset.id;
            let nombre = this.dataset.nombre;
            let precio = parseFloat(this.dataset.precio);

            agregarAlCarritoDesdeCatalogo(this, id, nombre, precio);
        });
    });

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

});


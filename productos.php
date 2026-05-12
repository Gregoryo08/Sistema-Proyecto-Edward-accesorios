<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo | Edward Accesorios</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
    <link rel="icon" href="./Sistema/assets/img/icono.ico">

    <style>
        :root {
            --primary: #3498db;
            --dark-bg: #0b0b0b;
            --card-bg: #1a1a1a;
            --text: #ffffff;
            --accent: #2980b9;
        }

        body { background-color: var(--dark-bg); color: var(--text); font-family: 'Poppins', sans-serif; }

        /* NAVBAR (Sincronizada con Index) */
        .navbar {
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: 80px;
            background: rgba(15, 15, 15, 0.9);
            backdrop-filter: blur(10px);
            position: fixed;
            width: 100%;
            z-index: 1000;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .logo { font-size: 24px; font-weight: 800; color: var(--primary); }
        .logo span { color: #fff; }
        .nav-links { display: flex; gap: 25px; list-style: none; align-items: center; }
        .nav-links a { color: #fff; text-decoration: none; font-weight: 500; opacity: 0.8; transition: 0.3s; }
        .nav-links a:hover { opacity: 1; color: var(--primary); }
        .btn-sistema { background: var(--primary); padding: 8px 20px; border-radius: 50px; font-weight: 600 !important; opacity: 1 !important; }

        /* HEADER DEL CATÁLOGO */
        .catalog-header {
            padding-top: 140px;
            padding-bottom: 40px;
            text-align: center;
        }
        .highlight { color: var(--primary); }

        /* FILTROS */
        .search-container {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.05);
            margin-bottom: 40px;
        }
        .filter-input {
            background: #0b0b0b !important;
            border: 1px solid #333 !important;
            color: white !important;
            border-radius: 10px !important;
        }

        /* REJILLA DE PRODUCTOS (Estilo Index) */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 80px;
        }
        .product-card {
            background: var(--card-bg);
            padding: 35px 25px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.05);
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-align: center;
        }
        .product-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary);
        }
        .product-icon { font-size: 45px; color: var(--primary); margin-bottom: 15px; }
        .price-tag { font-size: 1.8rem; font-weight: 800; color: var(--primary); margin: 15px 0; }

        /* BOTÓN AGREGAR */
        .cta-add {
            padding: 12px 30px;
            background: var(--primary);
            color: #fff;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            border: none;
            width: 100%;
            cursor: pointer;
            transition: 0.3s;
        }
        .cta-add:hover { background: var(--accent); transform: scale(1.05); }

        /* CARRITO FLOTANTE */
        .floating-cart {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--primary);
            width: 65px;
            height: 65px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
            z-index: 2000;
        }
        .cart-count {
            position: absolute;
            top: 0;
            right: 0;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px solid var(--dark-bg);
        }

        /* MODAL CARRITO PROFESIONAL */
        .modal-content {
            background: #1a1a1a;
            border: 1px solid var(--primary);
            border-radius: 20px;
        }
        .modal-header { border-bottom: 1px solid #333; }
        .modal-footer { border-top: 1px solid #333; }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #222;
        }

        .reveal { opacity: 0; transform: translateY(30px); transition: 1s all ease; }
        .reveal.active { opacity: 1; transform: translateY(0); }
    </style>
</head>

<body>




    <nav class="navbar">
        <div class="logo">Edward<span>Accesorios</span></div>
        <ul class="nav-links">
            <li><a href="index.php">Inicio</a></li>
            <li><a href="productos.php" style="color: var(--primary);">Catálogo</a></li>

        </ul>
    </nav>

    <header class="catalog-header">
        <div class="container">
            <h1 class="reveal active">Nuestro <span class="highlight">Catálogo</span></h1>
            <p class="reveal active">Equipos premium y accesorios con garantía oficial.</p>
        </div>
    </header>

    <main class="container">
        <div class="search-container reveal active">
            <div class="row g-3">
                <div class="col-md-8">
                    <input type="text" id="buscador" class="form-control filter-input" placeholder="Buscar por modelo o marca...">
                </div>
                <div class="col-md-4">
                    <select id="filtro-marca" class="form-select filter-input">
                        <option value="">Todas las Marcas</option>
                        <option value="Xiaomi">Xiaomi</option>
                        <option value="Samsung">Samsung</option>
                        <option value="Motorola">Motorola</option>
                        <option value="Apple">Apple</option>
                    </select>
                </div>
            </div>
        </div>

        <div id="contenedor-productos" class="product-grid">
            </div>
    </main>

    <div class="floating-cart" id="btn-abrir-carrito">
        <i class="fas fa-shopping-basket fa-lg text-white"></i>
        <span id="contador-carrito" class="cart-count">0</span>
    </div>

    <div class="modal fade" id="modalCarrito" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-cart-shopping text-primary pe-2"></i> Mi Pedido</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="cuerpo-carrito">
                    </div>
                <div class="modal-footer d-flex justify-content-between">
                    <h4 class="mb-0">Total: <span id="total-carrito" class="text-primary">$0.00</span></h4>
                    <button id="pago-final" class="cta-add" style="width: auto; padding: 10px 25px;">IR A PAGAR</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalCarrito" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-cart-shopping text-primary pe-2"></i> Mi Pedido</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cuerpo-carrito"></div>
            <div class="modal-footer d-flex justify-content-between">
                <h4 class="mb-0">Total: <span id="total-carrito" class="text-primary">$0.00</span></h4>
                <button id="pago-final" class="cta-add" style="width: auto; padding: 10px 25px;">IR A PAGAR</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDatosEnvio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Datos para tu solicitud</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formSolicitud">
                    <div class="mb-3">
                        <label class="form-label text-white-50">Nombre Completo</label>
                        <input type="text" id="solicitud_nombre" class="form-control filter-input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50">Teléfono</label>
                        <input type="text" id="solicitud_telefono" class="form-control filter-input" placeholder="04xx-xxxxxxx" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50">Dirección / Punto de referencia</label>
                        <textarea id="solicitud_direccion" class="form-control filter-input" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50">Método de Pago</label>
                        <select id="solicitud_metodo" class="form-select filter-input">
                            <option value="Pago Movil">Pago Móvil</option>
                            <option value="Zelle">Zelle</option>
                            <option value="Efectivo">Efectivo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="btnConfirmarSolicitud" class="cta-add">ENVIAR SOLICITUD</button>
            </div>
        </div>
    </div>
</div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    let productosBase = [];
let carrito = JSON.parse(localStorage.getItem('carrito_edward')) || [];

$(document).ready(function () {
    cargarProductos();
    actualizarContador();

    $("#buscador, #filtro-marca").on("input change", function() {
        filtrar();
    });

    $("#btn-abrir-carrito").click(function() {
        pintarCarrito();
        new bootstrap.Modal('#modalCarrito').show();
    });

    $("#pago-final").click(function() {
        if(carrito.length > 0) {
            bootstrap.Modal.getInstance('#modalCarrito').hide();
            new bootstrap.Modal('#modalDatosEnvio').show();
        } else {
            Swal.fire({ title: "Carrito vacío", icon: "warning", background: "#1a1a1a", color: "white" });
        }
    });

    $("#btnConfirmarSolicitud").click(function() {
        let datos = {
            accion: 'enviarSolicitud',
            nombre: $("#solicitud_nombre").val(),
            telefono: $("#solicitud_telefono").val(),
            direccion: $("#solicitud_direccion").val(),
            metodo: $("#solicitud_metodo").val(),
            productos: carrito,
            total: carrito.reduce((acc, x) => acc + (x.pre * x.cant), 0)
        };

        if(!datos.nombre || !datos.telefono || !datos.direccion) {
            Swal.fire({ title: "Campos incompletos", icon: "error", background: "#1a1a1a", color: "white" });
            return;
        }

        Swal.fire({
            title: 'Procesando solicitud...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            type: "POST",
            url: "Sistema/index.php?pagina=chequeo",
            data: datos,
            success: function(response) {
                let res = JSON.parse(response);
                if(res.success) {
                    Swal.fire({
                        title: "¡Solicitud Enviada!",
                        text: "Tu pedido #" + res.id_solicitud + " ha sido recibido.",
                        icon: "success",
                        background: "#1a1a1a",
                        color: "white"
                    }).then(() => {
                        localStorage.removeItem('carrito_edward');
                        location.reload();
                    });
                } else {
                    Swal.fire({ title: "Error", text: res.error, icon: "error" });
                }
            }
        });
    });
});

function cargarProductos() {
    $.get("Sistema/index.php?pagina=api_productos", function(data) {
        productosBase = data;
        renderizar(productosBase);
    });
}

function filtrar() {
    let txt = $("#buscador").val().toLowerCase();
    let mrc = $("#filtro-marca").val();
    let res = productosBase.filter(p => {
        let matchTexto = p.nombre_producto.toLowerCase().includes(txt);
        let matchMarca = mrc === "" || p.nombre_marca === mrc;
        return matchTexto && matchMarca;
    });
    renderizar(res);
}

function renderizar(lista) {
    let html = "";
    lista.forEach(p => {
        html += `
        <div class="product-card reveal active">
            <div class="product-icon"><i class="fas fa-mobile-alt"></i></div>
            <span style="opacity: 0.6; font-size: 0.8rem; text-transform: uppercase;">${p.nombre_marca}</span>
            <h3 style="margin: 10px 0; font-size: 1.3rem;">${p.nombre_producto}</h3>
            <div class="price-tag">$${parseFloat(p.precio_detal).toFixed(2)}</div>
            <button class="cta-add btn-add" 
                data-id="${p.id_producto}" 
                data-nombre="${p.nombre_producto}" 
                data-precio="${p.precio_detal}">
                <i class="fas fa-cart-plus pe-2"></i>AGREGAR
            </button>
        </div>`;
    });
    $("#contenedor-productos").html(html);
}

$(document).on("click", ".btn-add", function() {
    let id = String($(this).data("id"));
    let nombre = $(this).data("nombre");
    let precio = parseFloat($(this).data("precio"));
    let item = carrito.find(x => String(x.id) === id);
    if(item) { item.cant = parseInt(item.cant) + 1; } 
    else { carrito.push({ id: id, nom: nombre, pre: precio, cant: 1 }); }
    actualizarContador();
    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '¡Añadido!', showConfirmButton: false, timer: 1000, background: '#1a1a1a', color: 'white' });
});

function pintarCarrito() {
    let html = "";
    let total = 0;
    if(!carrito || carrito.length === 0) {
        html = "<p class='text-center opacity-50 py-3'>Carrito vacío</p>";
    } else {
        carrito.forEach((x, i) => {
            let precioUnitario = parseFloat(x.pre) || 0;
            let cantidad = parseInt(x.cant) || 0;
            let sub = precioUnitario * cantidad;
            total += sub;
            html += `
            <div class="cart-item d-flex justify-content-between align-items-center mb-2 border-bottom border-secondary pb-2">
                <div>
                    <h6 class="mb-0 text-white">${x.nom}</h6>
                    <small class="text-primary">$${precioUnitario.toFixed(2)} x ${cantidad}</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="fw-bold me-3 text-white">$${sub.toFixed(2)}</span>
                    <button class="btn btn-sm text-danger border-0" onclick="quitar(${i})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>`;
        });
    }
    $("#cuerpo-carrito").html(html);
    $("#total-carrito").text(`$${total.toFixed(2)}`);
}

window.quitar = function(i) {
    carrito.splice(i, 1);
    actualizarContador();
    pintarCarrito();
}

function actualizarContador() {
    localStorage.setItem('carrito_edward', JSON.stringify(carrito));
    let num = carrito.reduce((acc, x) => acc + (parseInt(x.cant) || 0), 0);
    $("#contador-carrito").text(num);
}
</script>
</body>
</html>
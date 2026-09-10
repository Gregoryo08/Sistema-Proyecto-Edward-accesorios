<?php
  $mostrarOpcionesCliente = $mostrarOpcionesCliente ?? false;
  $nombreCliente = $nombreCliente ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edward Accesorios | Expertos en Telefonía</title>

    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/ecommerce/carritoModal.css">
    <link rel="icon" href="assets/img/icono.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body data-edai-contexto="ecommerce">
    <header class="main-header">
        <nav class="navbar">
            <div class="logo">Edward<span>Accesorios</span></div>
            
            <div class="nav-container" id="nav-menu">
                <ul class="nav-links">
                    <li><a href="?pagina=home">Inicio</a></li>
                    <li><a href="#financiados">Financiados</a></li>
                    <li><a href="?pagina=web_Catalogo">Catálogo</a></li>
                    <li><a href="#servicio">Soporte Técnico</a></li>
                    <li><a href="#ubicacion">Ubicación</a></li>
                    <li><a href="?pagina=iniciarsesion" class="btn-sistema">Mi Sistema</a></li>
                </ul>

                <div class="user-actions">
                    <button type="button" id="theme-toggle" class="theme-toggle-btn" aria-label="Cambiar modo claro/oscuro">
                        <i class="bx bx-moon" id="theme-icon"></i>
                    </button>

                    <a href="?pagina=carrito" id="verCarrito" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cartCount" class="cart-badge">0</span>
                    </a>

                   <?php if ($mostrarOpcionesCliente): ?>
                        <div class="custom-user-dropdown">
                            <button type="button" class="custom-user-btn">
                                <i class="fas fa-user-circle"></i> 
                                <span><?php echo htmlspecialchars($nombreCliente); ?></span>
                                <i class="fas fa-chevron-down custom-arrow"></i>
                            </button>
                            <div class="custom-dropdown-menu">
                                <a href="?pagina=principal_cliente" class="custom-dropdown-item custom-cuotas">
                                    <i class="fas fa-wallet"></i> Mis Cuotas
                                </a>
                                <div class="custom-dropdown-divider"></div>
                                <a href="?pagina=cerrarSesionCliente" class="custom-dropdown-item custom-logout">
                                    <i class="fas fa-sign-out-alt"></i> Salir
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="auth-buttons">
                            <a href="?pagina=loginEcommerce&destino=web_Catalogo" class="btn-auth">
                                <i class="fas fa-user"></i> Login
                            </a>
                            <a href="?pagina=loginEcommerce&destino=web_Catalogo&register=1" class="btn-auth btn-register">
                                <i class="fas fa-user-plus"></i> Registro
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <button class="burger" id="burger" type="button" aria-label="Abrir menú" aria-expanded="false">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </button>
        </nav>
    </header>

    <?php include 'assets/comunes/modal_carritoCompras.php'; ?>
    <script src="assets/js/ecommerce/carritoModal.js"></script>

    <div class="edai-chat" id="edaiChat" data-contexto="ecommerce">
        <div id="edaiWindow" class="edai-window">
            <div class="edai-header">
                <img src="assets/avatar.jpg" alt="Ed-AI" class="edai-avatar">
                <div class="edai-header-info">
                    <strong>Ed-AI Tienda</strong>
                    <small id="edai-status">disponible</small>
                </div>
                <button id="edaiClose" type="button">&times;</button>
            </div>
            <div class="edai-body" id="edaiBody">
                <p class="edai-msg edai-bot">¡Hola! Soy <strong>Ed-AI</strong>, tu asistente de la tienda. Pregúntame sobre pedidos, pagos, envíos, el catálogo o la tasa de cambio.</p>
            </div>
            <div class="edai-footer">
                <input type="text" id="edaiInput" placeholder="Escribe tu duda..." maxlength="300">
                <button id="edaiSend" type="button"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
        <button id="edaiFab" class="edai-fab" title="Ed-AI Tienda">
            <img src="assets/avatar.jpg" alt="Ed-AI" class="edai-fab-img">
            <span class="edai-dot"></span>
        </button>
    </div>

    <style>
:root {
    --bg-main: #000000;
    --bg-card: #0a0a0a;
    --bg-secondary: #121212;
    --text-main: #ffffff;
    --text-muted: #a1a1aa;
    --border-color: #27272a;
    --header-bg: #121212;
    --header-text: #ffffff;
    --header-hover: #3b82f6;
}

body.light-mode {
    --bg-main: #ffffff;
    --bg-card: #f8fafc;
    --bg-secondary: #f1f5f9;
    --text-main: #0f172a;
    --text-muted: #475569;
    --border-color: #e2e8f0;
    --header-bg: #ffffff;
    --header-text: #0f172a;
    --header-hover: #2563eb;
}

body {
    background-color: var(--bg-main) !important;
    background: var(--bg-main) !important;
    color: var(--text-main) !important;
    transition: background-color 0.3s ease, color 0.3s ease;
    margin: 0;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

p, li, span, h1, h2, h3, h4, h5, h6 {
    color: var(--text-main);
}

header,
nav,
.main-header,
.navbar {
    background-color: var(--header-bg) !important;
}

.main-header {
    border-bottom: 1px solid var(--border-color) !important;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.logo {
    color: var(--header-text) !important;
    font-size: 20px;
    font-weight: 700;
}

.logo span {
    color: #3b82f6 !important;
}

body.light-mode .logo span {
    color: #2563eb !important;
}

.nav-links li a,
.nav-links a {
    color: var(--header-text) !important;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
}

.nav-links li a:hover,
.nav-links a:hover {
    color: var(--header-hover) !important;
}

.btn-sistema {
    background-color: #2563eb !important;
    color: #ffffff !important;
    padding: 8px 16px !important;
    border-radius: 20px !important;
}

.btn-sistema:hover {
    background-color: #1d4ed8 !important;
    color: #ffffff !important;
}

.theme-toggle-btn,
.cart-icon,
.btn-auth {
    background-color: var(--bg-secondary) !important;
    border: 1px solid var(--border-color) !important;
    color: var(--text-main) !important;
}

.theme-toggle-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.25s ease;
    outline: none;
}

.theme-toggle-btn i {
    font-size: 20px;
    color: #ffffff !important;
}

body.light-mode .theme-toggle-btn i {
    color: #f59e0b !important;
}

.cart-icon {
    position: relative;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.cart-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ef4444;
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.auth-buttons {
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-auth {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
}

.btn-register {
    background-color: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
}

/* Corrección del menú desplegable del usuario */
.custom-user-dropdown {
    position: relative;
    display: inline-block;
}

.custom-user-btn {
    background: transparent;
    border: none;
    color: var(--header-text);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    padding: 6px 10px;
}

.custom-dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    border-radius: 8px;
    min-width: 160px;
    z-index: 1050;
    padding: 6px 0;
}

.custom-user-dropdown:hover .custom-dropdown-menu,
.custom-dropdown-menu.show {
    display: block;
}

.custom-dropdown-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    color: var(--text-main);
    text-decoration: none;
    font-size: 13px;
    transition: background 0.2s;
}

.custom-dropdown-item:hover {
    background-color: var(--bg-secondary);
}

.custom-dropdown-divider {
    height: 1px;
    background-color: var(--border-color);
    margin: 4px 0;
}

@media (max-width: 992px) {
    .burger {
        display: flex;
    }
    .nav-container {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background-color: var(--bg-card) !important;
        flex-direction: column;
        align-items: flex-start;
        padding: 20px;
        gap: 20px;
        border-bottom: 1px solid var(--border-color);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        display: none;
        box-sizing: border-box;
    }
    .nav-container.active {
        display: flex;
    }
    .nav-links {
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
    }
    .user-actions {
        width: 100%;
        justify-content: space-between;
    }
}

.edai-chat { position: fixed; right: 22px; bottom: 22px; z-index: 1060; }
.edai-fab { width: 58px; height: 58px; border-radius: 50%; background: #144272; color: #fff; border: none; box-shadow: 0 8px 20px rgba(20,66,114,.4); display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; transition: transform .2s ease; }
.edai-fab:hover { transform: scale(1.08); }
.edai-fab-img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
.edai-dot { position: absolute; top: 2px; right: 2px; width: 12px; height: 12px; background: #22c55e; border: 2px solid #fff; border-radius: 50%; }
.edai-window { display: none; position: absolute; right: 0; bottom: 70px; width: 330px; max-width: 88vw; height: 440px; background: var(--bg-card); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; box-shadow: 0 20px 45px rgba(0,0,0,.25); flex-direction: column; }
.edai-window.open { display: flex; }
.edai-header { background: #144272; color: #fff; padding: 12px 14px; display: flex; align-items: center; gap: 10px; }
.edai-avatar { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; }
.edai-header-info { flex: 1; line-height: 1.15; }
.edai-header-info small { display: block; font-size: 11px; opacity: .85; }
.edai-header button { background: transparent; border: none; color: #fff; font-size: 22px; cursor: pointer; }
.edai-body { flex: 1; overflow-y: auto; padding: 14px; display: flex; flex-direction: column; gap: 8px; background: var(--bg-main); }
.edai-msg { max-width: 82%; padding: 9px 12px; border-radius: 14px; font-size: 13px; line-height: 1.4; word-wrap: break-word; }
.edai-bot { background: var(--bg-secondary); color: var(--text-main); align-self: flex-start; border: 1px solid var(--border-color); }
.edai-user { background: #144272; color: #fff; align-self: flex-end; }
.edai-footer { display: flex; gap: 6px; padding: 10px; background: var(--bg-card); border-top: 1px solid var(--border-color); }
.edai-footer input { flex: 1; border: 1px solid var(--border-color); background: var(--bg-secondary); color: var(--text-main); border-radius: 20px; padding: 8px 14px; font-size: 13px; outline: none; }
.edai-footer button { background: #144272; color: #fff; border: none; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; }

.section.gray-bg { background-color: var(--bg-secondary) !important; }
.card { background-color: var(--bg-card) !important; color: var(--text-main) !important; border: 1px solid var(--border-color) !important; }
.subtitle { color: var(--text-muted) !important; }
.detail-box { background-color: var(--bg-card) !important; color: var(--text-main) !important; border: 1px solid var(--border-color) !important; }
.detail-box p { color: var(--text-muted) !important; }
.footer { background-color: var(--bg-main) !important; color: var(--text-muted) !important; border-top: 1px solid var(--border-color) !important; }
</style>

    <script>
$(document).ready(function() {
    const $body = $('body');
    const $themeIcon = $('#theme-icon');

    if (localStorage.getItem('theme') === 'light') {
        $body.addClass('light-mode');
        $themeIcon.removeClass('bx-moon').addClass('bx-sun');
    }

    $('#theme-toggle').on('click', function() {
        $body.toggleClass('light-mode');
        if ($body.hasClass('light-mode')) {
            localStorage.setItem('theme', 'light');
            $themeIcon.removeClass('bx-moon').addClass('bx-sun');
        } else {
            localStorage.setItem('theme', 'dark');
            $themeIcon.removeClass('bx-sun').addClass('bx-moon');
        }
    });

    const $burger = $('#burger');
    const $navContainer = $('#nav-menu');
    if ($burger.length && $navContainer.length) {
        $burger.on('click', function() {
            $navContainer.toggleClass('active');
            $burger.attr('aria-expanded', $navContainer.hasClass('active'));
        });
    }
});
    </script>
    <script src="assets/js/edai.js"></script>
</body>
</html>
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

    <!-- Modal Carrito de Compras (HTML puro) -->
    <?php include 'assets/comunes/modal_carritoCompras.php'; ?>
    <script src="assets/js/ecommerce/carritoModal.js"></script>

    <!-- ============ ED-AI ASISTENTE (E-COMMERCE) ============ -->
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
.custom-user-dropdown {
    position: relative;
    display: inline-block;
}

.custom-user-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #ffffff;
    padding: 7px 16px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.25s ease;
    outline: none;
    font-family: inherit;
}

.custom-user-btn:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.25);
}

.custom-arrow {
    font-size: 10px;
    opacity: 0.7;
    transition: transform 0.25s ease;
}

.custom-dropdown-menu {
    position: absolute;
    right: 0;
    top: calc(100% + 10px);
    background: #141414;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    min-width: 170px;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-8px);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    overflow: hidden;
    padding: 6px;
}

.custom-user-dropdown:hover .custom-dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.custom-user-dropdown:hover .custom-arrow {
    transform: rotate(180deg);
}

.custom-dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    color: #d1d1d1;
    text-decoration: none;
    font-size: 14px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.custom-cuotas:hover {
    background: rgba(0, 123, 255, 0.15);
    color: #3b82f6;
}

.custom-logout:hover {
    background: rgba(220, 53, 69, 0.15);
    color: #ef4444;
}

.custom-dropdown-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.08);
    margin: 4px 6px;
}


.btn-sistema {
  color: white; 
  text-decoration: none; 
}


.btn-sistema:hover {
  color: #000000 !important; 
  
}





        .edai-chat { position: fixed; right: 22px; bottom: 22px; z-index: 1060; }
        .edai-fab { width: 58px; height: 58px; border-radius: 50%; background: #144272; color: #fff; border: none; box-shadow: 0 8px 20px rgba(20,66,114,.4); display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; transition: transform .2s ease; }
        .edai-fab:hover { transform: scale(1.08); }
        .edai-fab-img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        .edai-dot { position: absolute; top: 2px; right: 2px; width: 12px; height: 12px; background: #22c55e; border: 2px solid #fff; border-radius: 50%; }
        .edai-window { display: none; position: absolute; right: 0; bottom: 70px; width: 330px; max-width: 88vw; height: 440px; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 45px rgba(0,0,0,.25); flex-direction: column; }
        .edai-window.open { display: flex; }
        .edai-header { background: #144272; color: #fff; padding: 12px 14px; display: flex; align-items: center; gap: 10px; }
        .edai-avatar { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; }
        .edai-header-info { flex: 1; line-height: 1.15; }
        .edai-header-info small { display: block; font-size: 11px; opacity: .85; }
        .edai-header button { background: transparent; border: none; color: #fff; font-size: 22px; cursor: pointer; }
        .edai-body { flex: 1; overflow-y: auto; padding: 14px; display: flex; flex-direction: column; gap: 8px; background: #f8fafc; }
        .edai-msg { max-width: 82%; padding: 9px 12px; border-radius: 14px; font-size: 13px; line-height: 1.4; word-wrap: break-word; }
        .edai-bot { background: #eef2ff; color: #1e293b; align-self: flex-start; }
        .edai-user { background: #144272; color: #fff; align-self: flex-end; }
        .edai-footer { display: flex; gap: 6px; padding: 10px; background: #fff; }
        .edai-footer input { flex: 1; border: 1px solid #e2e8f0; border-radius: 20px; padding: 8px 14px; font-size: 13px; }
        .edai-footer button { background: #144272; color: #fff; border: none; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; }
    </style>
    <script src="assets/js/edai.js"></script>

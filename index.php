<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edward Accesorios | Expertos en Telefonía</title>
    <link rel="stylesheet" href="assets/styles.css">
    <link rel="icon" href="./Sistema/assets/img/icono.ico">

</head>

<body>



<div class="chat-widget">
    <div id="chat-window" class="chat-window">
        <div class="chat-header">
            <div class="chat-header-avatar">
                <img src="assets/avatar.jpg" alt="Avatar IA" class="avatar-img-zoom">
            </div>
            <span>Ed-AI Asistente</span>
            <button id="close-chat">&times;</button>
        </div>
        <div class="chat-body" id="chat-content">
            <p class="bot-msg">¡Hola! Soy Ed-AI de <strong>Edward Accesorios</strong>. ¿En qué puedo ayudarte hoy?</p>
        </div>
        <div class="chat-footer">
            <input type="text" id="chat-input" placeholder="Escribe tu duda...">
            <button id="send-msg"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <div id="chat-icon" class="chat-icon">
        <img src="assets/avatar.jpg" alt="Avatar IA" class="avatar-img-zoom">
        <span class="notification-dot">1</span>
    </div>
</div>

<div id="welcome-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="ai-avatar">
            <img src="assets/avatar.jpg" alt="Ed-AI" class="avatar-img">
        </div>
        <h2>¡Hola! Soy Ed-AI</h2>
        <p>Tu asistente virtual de <strong>Edward Accesorios</strong>. ¿En qué puedo ayudarte hoy?</p>
        <button class="cta" id="modal_ia">¡Genial, gracias!</button>
    </div>
</div>






    <nav class="navbar">
        <div class="logo">Edward<span>Accesorios</span></div>
        <ul class="nav-links">
            <li><a href="#home">Inicio</a></li>
            <li><a href="#financiados">Financiados</a></li>
            <li><a href="productos.php">Catálogo</a></li>
            <li><a href="#servicio">Soporte Técnico</a></li>
            <li><a href="#ubicacion">Ubicación</a></li>
            <li><a href="Sistema/index.php" class="btn-sistema">Mi Sistema</a></li>
        </ul>
        <div class="burger">
            <div class="line1"></div>
            <div class="line2"></div>
            <div class="line3"></div>
        </div>
    </nav>

    <header id="home" class="hero">
        <div class="hero-content reveal">
            <span class="badge">C.C. COSMOS - Barquisimeto</span>
            <h1>Expertos en <br><span class="highlight">Telefonía Móvil</span></h1>
            <p>Encuentra accesorios premium, cornetas y los mejores equipos financiados para que estrenes hoy mismo.</p>
            <div class="hero-btns">
                <a href="#financiados" class="cta">Ver Planes</a>
                <a href="#servicio" class="cta-outline">Soporte Técnico</a>
            </div>
        </div>
    </header>

    <section id="financiados" class="section">
        <div class="container">
            <h2 class="title">Equipos Financiados</h2>
            <p class="subtitle">Llévatelo ahora y paga después con nuestros planes</p>
            <div class="grid-3">
                <div class="card reveal">
                    <div class="card-icon"><i class="fas fa-hand-holding-dollar"></i></div>
                    <h3>Plan Cuotas</h3>
                    <p>Financiamiento flexible con una inicial mínima adaptada a tu presupuesto.</p>
                </div>
                <div class="card reveal">
                    <div class="card-icon"><i class="fas fa-mobile-screen"></i></div>
                    <h3>Equipos Nuevos</h3>
                    <p>Garantía oficial en las mejores marcas: iPhone, Samsung y Xiaomi.</p>
                </div>
                <div class="card reveal">
                    <div class="card-icon"><i class="fas fa-bolt"></i></div>
                    <h3>Aprobación Rápida</h3>
                    <p>Procesamos tu solicitud al instante en nuestra tienda física.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="catalogo" class="section gray-bg">
        <div class="container">
            <h2 class="title">Lo Más Buscado</h2>
            <div class="gallery">
                <div class="gallery-item reveal">
                    <img src="https://tuapplemundo.com/wp-content/uploads/2024/07/camaras-iphone-17.webp" alt="iPhone">
                    <div class="overlay"><span>Smartphones</span></div>
                </div>
                <div class="gallery-item reveal">
                    <img src="https://i.ytimg.com/vi/0uUwWaVSUaY/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLDCe6huhrrhJZThyRyd3fnNlRIYCw" alt="Accesorios">
                    <div class="overlay"><span>Audio & Cornetas</span></div>
                </div>
                <div class="gallery-item reveal">
                    <img src="https://images.unsplash.com/photo-1603313011101-31c726a54881?auto=format&fit=crop&w=500" alt="Forros">
                    <div class="overlay"><span>Forros & Protectores</span></div>
                </div>
            </div>
        </div>
    </section>

    <section id="servicio" class="section">
        <div class="container service-flex">
            <div class="service-img reveal">
                <img src="https://i.blogs.es/7ccd29/mobile-phone-2510529_960_720/1366_2000.jpg" alt="Soporte">
            </div>
            <div class="service-text reveal">
                <h2 class="title left">Soporte Técnico</h2>
                <p>¿Tu teléfono falló? No te preocupes, somos especialistas en:</p>
                <ul class="service-list">
                    <li><i class="fas fa-check-circle"></i> Cambio de pantallas (Display)</li>
                    <li><i class="fas fa-check-circle"></i> Reemplazo de baterías</li>
                    <li><i class="fas fa-check-circle"></i> Limpieza de puertos y cornetas</li>
                    <li><i class="fas fa-check-circle"></i> Mantenimiento preventivo</li>
                </ul>
                <a href="https://wa.me/tu-numero" class="btn-whatsapp"><i class="fab fa-whatsapp"></i> Consultar Precio</a>
            </div>
        </div>
    </section>

    <section id="ubicacion" class="section gray-bg">
        <div class="container">
            <h2 class="title">Nuestra Ubicación</h2>
            <div class="location-wrapper reveal">
                <div class="map-container">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d278.2913223208099!2d-69.31666140063034!3d10.068811551334043!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e87670c33ca4e37%3A0x30c12a6bf1cc59f!2sCosmos%20Mall!5e1!3m2!1sen!2sve!4v1774383093426!5m2!1sen!2sve"
                        width="100%" height="350" style="border:0; border-radius: 20px;" allowfullscreen="" loading="lazy"></iframe>
                </div>
                <div class="location-details">
                    <div class="detail-box">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>Visítanos</h3>
                        <p>C.C. COSMOS, Mesanina Local N°8</p>
                        <p>Carrera 21 entre Calle 25 y 22</p>
                        <p>Barquisimeto, Lara</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <p>&copy; 2026 <strong>Edward Accesorios</strong>. Todos los derechos reservados.</p>
    </footer>

    <script src="assets/script.js"></script>
</body>

</html>
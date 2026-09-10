<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Clientes | Edward Accesorios</title>
    
    <link rel="icon" href="assets/img/icono.ico">
    <link rel="stylesheet" href="assets/CSS/Estilos_login.css">
    <link rel="stylesheet" href="assets/CSS/loginEcommerce.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="<?= !empty($mostrarRegistro) ? 'mostrar-registro' : '' ?>">

<div class="wrapper">
    <span class="bg-animate"></span>
    <span class="bg-animate2"></span>

   <!-- ============================================= -->
    <!-- LOGIN -->
    <!-- ============================================= -->
    <div class="form-box login">
        <h2 class="animation" style="--i:0;">Acceso Clientes</h2>
        
        <form id="clienteLoginForm" autocomplete="on">
            <div class="input-box animation" style="--i:1;">
                <input type="text" id="cedula_cliente" name="cedula" required autocomplete="username" readonly>
                <label for="cedula_cliente">Cédula</label>
                <i class='bx bx-id-card'></i>
            </div>
            <div class="input-box animation" style="--i:2;">
                <input type="password" id="pass_cliente" name="clave" required autocomplete="current-password" readonly>
                <label for="pass_cliente">Contraseña</label>
                <i class='bx bx-lock-alt' id="togglePassCliente" style="cursor:pointer;"></i>
            </div>
            
            <div class="forgot-pass animation" style="--i:2.5;">
                <a href="#" class="recover-link">¿Olvidaste tu contraseña?</a>
            </div>

            <div class="contenedor-botones">
                <button type="submit" id="acceder" class="fadeIn fourth">Ingresar</button>
            </div>

            <div class="logreg-link animation" style="--i:4;">
                <p>¿No tienes una cuenta? <a href="#" class="register-link">Regístrate aquí</a></p>
            </div>
        </form>
    </div>

    <div class="info-text login">
        <h2 class="animation" style="--i:0;">¡Bienvenido de nuevo!</h2>
        <p class="animation" style="--i:1;">Ingresa tu cédula y contraseña para acceder a tu cuenta.</p>
    </div>

    <!-- ============================================= -->
    <!-- REGISTRO (scroll completo, centrado) -->
    <!-- ============================================= -->
    <div class="form-box register">
        <h2 class="animation" style="--i:17;">Registro Cliente</h2>
        
        <form id="clienteRegistroForm" autocomplete="off" method="POST">
            <div class="form-scroll-container animation" style="--i:18;">
                
                <div class="input-box">
                    <div style="display:flex; gap:8px; align-items:center;">
                        <select name="tipo_cedula" id="tipo_cedula" style="width:55px; background:transparent; color:#fff; border:none; border-bottom:2px solid #0ef; padding:5px; outline:none; height:40px;">
                            <option value="V">V</option>
                            <option value="E">E</option>
                            <option value="J">J</option>
                            <option value="P">P</option>
                        </select>
                        <input type="text" id="cedula" name="cedula" required style="flex:1; background:transparent; border:none; outline:none; color:#fff; border-bottom:2px solid #0ef; padding:5px 10px; height:40px;" autocomplete="off">
                    </div>
                    <label style="position:absolute; top:-8px; left:65px; font-size:0.75em; color:#0ef;">Número de Cédula</label>
                    <i class='bx bx-id-card' style="position:absolute; right:8px; color:#0ef;"></i>
                </div>
                <small id="msgCedula" style="display:none; color:#dc3545; font-size:0.7rem; margin-top:2px;"></small>

                <div class="input-box">
                    <input type="text" id="nombre_apellido" name="nombre_apellido" required autocomplete="off">
                    <label for="nombre_apellido">Nombre y Apellido</label>
                    <i class='bx bx-user'></i>
                </div>
                <small id="msgNombre" style="display:none; color:#dc3545; font-size:0.7rem; margin-top:2px;"></small>

                <div class="input-box">
                    <input type="email" id="correo" name="correo" required autocomplete="off">
                    <label for="correo">Correo Electrónico</label>
                    <i class='bx bx-envelope'></i>
                </div>
                <small id="msgCorreo" style="display:none; color:#dc3545; font-size:0.7rem; margin-top:2px;"></small>

                <div class="input-box">
                    <input type="tel" id="telefono" name="telefono" required autocomplete="off">
                    <label for="telefono">Teléfono</label>
                    <i class='bx bx-phone'></i>
                </div>
                <small id="msgTelefono" style="display:none; color:#dc3545; font-size:0.7rem; margin-top:2px;"></small>

                <div class="input-box">
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" autocomplete="off">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <i class='bx bx-calendar'></i>
                </div>

                <div class="input-box">
                    <select id="sexo" name="sexo" style="width:100%; background:transparent; border:none; border-bottom:2px solid #0ef; color:white; padding:8px 0; outline:none;">
                        <option value="" disabled selected>Seleccione su sexo</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>
                    <label style="position:absolute; top:-8px; left:5px; font-size:0.7em; color:#0ef;">Sexo</label>
                    <i class='bx bx-user'></i>
                </div>

                <div class="input-box">
                    <input type="text" id="direccion" name="direccion" autocomplete="off">
                    <label for="direccion">Dirección (opcional)</label>
                    <i class='bx bx-map'></i>
                </div>

                <div class="input-box">
                    <input type="password" id="clave" name="clave" required autocomplete="new-password">
                    <label for="clave">Contraseña</label>
                    <i class='bx bx-lock-alt' id="toggleClave" style="cursor:pointer;"></i>
                </div>
                <small id="msgPassword" style="display:none; color:#dc3545; font-size:0.7rem; margin-top:2px;"></small>

                <div class="input-box">
                    <input type="password" id="confirmar_clave" name="confirmar_clave" required autocomplete="off">
                    <label for="confirmar_clave">Confirmar Contraseña</label>
                    <i class='bx bx-lock-alt' id="toggleConfirmar" style="cursor:pointer;"></i>
                </div>
                <small id="msgConfirmar" style="display:none; font-size:0.7rem; margin-top:2px;"></small>

            </div>

            <button type="submit" class="btn animation" style="--i:24;" id="btnRegistro">Registrarse</button>
            <div class="logreg-link animation" style="--i:25;">
                <p>¿Ya tienes una cuenta? <a href="#" class="login-link">Inicia Sesión</a></p>
            </div>
        </form>
    </div>

    <div class="info-text register">
        <h2 class="animation" style="--i:17;">¡Hola, Amigo!</h2>
        <p class="animation" style="--i:18;">Regístrate con tus datos personales y comienza tu aventura con nosotros.</p>
    </div>

    <!-- ============================================= -->
    <!-- RECUPERACIÓN -->
    <!-- ============================================= -->
    <div class="form-box recover">
        <div id="alertMessage" style="display:none;"></div>

        <div id="sectionSolicitar">
            <div class="recover-header">
                <i class="bx bx-lock-alt recover-icon"></i>
                <h2 class="animation" style="--i:0;">Recuperar</h2>
                <p class="animation" style="--i:1;">Te enviaremos un enlace a tu correo.</p>
            </div>

            <form id="formSolicitarRecuperacion" autocomplete="off">
                <input type="hidden" name="accion" value="solicitarRecuperacion">
                
                <div class="input-box animation" style="--i:2;">
                    <input type="email" id="email_recuperacion" name="email" required autocomplete="off">
                    <label for="email_recuperacion">Correo Electrónico</label>
                    <i class='bx bx-envelope'></i>
                </div>

                <button type="submit" class="btn animation" style="--i:3;">Enviar Enlace</button>

                <div class="logreg-link animation" style="--i:4;">
                    <p><a href="#" class="back-to-login">← Volver al Login</a></p>
                </div>
            </form>
        </div>

        <div id="sectionRestablecer" style="display: none;">
            <div class="recover-header">
                <i class="bx bx-reset recover-icon"></i>
                <h2 class="animation" style="--i:0;">Nueva Clave</h2>
                <p class="animation" style="--i:1;">Crea tu nueva contraseña segura.</p>
            </div>

            <form id="formRestablecerClave" autocomplete="off">
                <input type="hidden" name="accion" value="restablecerClave">
                <input type="hidden" name="token" id="tokenHiddenInput">

                <div class="input-box animation" style="--i:2;">
                    <input type="password" id="nueva_clave" name="nueva_clave" required autocomplete="new-password">
                    <label for="nueva_clave">Nueva Contraseña</label>
                    <i class="bx bx-lock-open-alt" id="toggleNuevaClave" style="cursor: pointer;"></i>
                </div>

                <div class="input-box animation" style="--i:3;">
                    <input type="password" id="repetir_clave" name="repetir_clave" required autocomplete="off">
                    <label for="repetir_clave">Confirmar Contraseña</label>
                    <i class="bx bx-lock-alt" id="toggleRepetirClave" style="cursor: pointer;"></i>
                </div>

                <button type="submit" class="btn animation" style="--i:4;">Cambiar Contraseña</button>
            </form>
        </div>
    </div>
</div>

<button id="themeToggle" class="theme-toggle-btn" title="Cambiar tema" data-icon-oscuro="bx bx-sun" data-icon-claro="bx bx-moon">
    <i class='bx bx-sun' id="themeIcon"></i>
</button>

<!-- ============================================= -->
<!-- SCRIPTS (orden: validaciones → login → registro) -->
<!-- ============================================= -->

<script src="assets/js/ecommerce/temaBoton.js"></script>
<script src="assets/js/ecommerce/loginEcommerce.js?v=3"></script>
<script src="assets/js/ecommerce/registroEcommerce.js?v=3"></script>

</body>
</html>
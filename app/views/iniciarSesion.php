<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accesorios Edward</title>

    <link rel="icon" href="./assets/img/icono.ico">
    <link rel="stylesheet" href="assets/CSS/Estilos_login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="assets/Library/JQuery/jquery-3.7.0.min.js"></script>
    <script src="assets/Library/SweetAlerts/sweetalert2.all.js"></script>
    <script src="assets/Library/Bootstrap/bootstrap.min.js"></script>
</head>

<body>
    <div class="wrapper">
        <span class="bg-animate"></span>
        <span class="bg-animate2"></span>

        <div class="form-box login">
            <a href="?pagina=pagina_principal" class="btn-volver">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <h2 class="animation" style="--i:0;">Login</h2>

            <form id="loginForm" method="POST" onsubmit="return false;">
                <div class="input-box animation" style="--i:1;">
                    <input type="text" id="login" name="usuario" placeholder="Cedula" maxlength="20" required>
                    <i class="bx bx-user-circle"></i>
                    <small id="error_login" class="error-msg">Campo obligatorio</small>
                </div>
                <div class="input-box animation" style="--i:2;">
                    <input type="password" id="Contraseña" name="clave" placeholder="Contraseña" maxlength="20" required>
                    <i class='bx bx-show' id="toggleLogin" style="cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 10; color: #00d4ff;"></i>
                    <small id="error_password" class="error-msg">Campo obligatorio</small>
                </div>
                <div class="input-box animation" style="--i:2.2; display: flex; justify-content: center;">
                    <div class="g-recaptcha" data-sitekey="6LfgnqstAAAAAIUCJUsYquqH2iNKSZoOhjMYKlaa"></div>
                </div>
                <div class="forgot-pass animation" style="--i:2.5;">
                    <a href="#" class="recover-link">¿Olvidaste tu contraseña?</a>
                </div>

                <div class="contenedor-botones">
                    <button type="submit" id="acceder" class="fadeIn fourth" disabled>Ingresar</button>
                </div>

                <div class="logreg-link animation" style="--i:4;">
                    <p>¿No tienes una cuenta? <a href="#" class="register-link">Regístrate</a></p>
                </div>
            </form>
        </div>

        <div class="info-text login">
            <h2 class="animation" style="--i:0;">¡Bienvenido de nuevo!</h2>
            <p class="animation" style="--i:1;">Ingresa tus datos personales para acceder a tu cuenta.</p>
        </div>

        <div class="form-box register">
            <h2 class="animation" style="--i:17;">Registro</h2>

            <form id="formRegistro">
                <div class="form-scroll-container animation" style="--i:18;">
                    <div class="input-box">
                        <input type="text" name="cedula" id="cedula" required>
                        <label>Cédula</label>
                        <i class="bx bx-id-card"></i>
                        <small class="error-msg">Obligatorio</small>
                    </div>
                    <div class="input-box">
                        <input type="text" name="nombre" id="nombre" required>
                        <label>Nombre</label>
                        <i class="bx bx-user"></i>
                        <small class="error-msg">Obligatorio</small>
                    </div>
                    <div class="input-box">
                        <input type="text" name="apellido" id="apellido" required>
                        <label>Apellido</label>
                        <i class="bx bx-user"></i>
                        <small class="error-msg">Obligatorio</small>
                    </div>
                    <div class="input-box">
                        <input type="email" name="correo" id="correo" maxlength="30" required>
                        <label>Correo</label>
                        <i class="bx bx-envelope"></i>
                        <small class="error-msg">Formato inválido</small>
                    </div>
                    <div class="input-box">
                        <input type="tel" name="telefono" id="telefono" required>
                        <label>Teléfono</label>
                        <i class="bx bx-phone"></i>
                        <small class="error-msg">Obligatorio</small>
                    </div>
                    <div class="input-box">
                        <input type="text" name="residencia" id="residencia" required>
                        <label>Dirección (Residencia)</label>
                        <i class="bx bx-map"></i>
                        <small class="error-msg">Obligatorio</small>
                    </div>
                    <div class="input-box">
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>
                        <label class="active">Fecha de Nacimiento</label>
                        <small class="error-msg">Obligatorio</small>
                    </div>
                    <div class="input-box">
                        <select name="sexo" id="sexo" required>
                            <option value="" disabled selected>Seleccione su sexo</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                        </select>
                        <small class="error-msg">Obligatorio</small>
                    </div>
                    <div class="input-box">
                        <input type="password" name="clave" id="reg_pass" maxlength="30" required>
                        <label>Contraseña</label>
                        <i class="bx bx-show" id="toggleReg" style="cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);"></i>
                        <small class="error-msg">Obligatorio</small>
                    </div>
                    <div class="input-box">
                        <input type="password" id="conf_pass" name="confirmar_clave" maxlength="30" required>
                        <label>Confirmar Contraseña</label>
                        <i class="bx bx-show" id="toggleConf" style="cursor: pointer; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);"></i>
                        <small class="error-msg">Debe coincidir</small>
                    </div>
                    <div class="input-box animation" style="--i:2.2; display: flex; justify-content: center;">
                        <div class="g-recaptcha" data-sitekey="6LfgnqstAAAAAIUCJUsYquqH2iNKSZoOhjMYKlaa"></div>
                    </div>
                </div>
                <button type="submit" class="btn animation" style="--i:24;" disabled>Registrarse</button>
                <div class="logreg-link animation" style="--i:25;">
                    <p>¿Ya tienes cuenta? <a href="#" class="login-link">Login</a></p>
                </div>
            </form>
        </div>

        <div class="info-text register">
            <h2 class="animation" style="--i:17;">¡Hola, Amigo!</h2>
            <p class="animation" style="--i:18;">Regístrate con tus datos personales y comienza tu aventura con nosotros.</p>
        </div>

        <div class="form-box recover">
            <div id="alertMessage" style="display:none;"></div>
            <div id="sectionSolicitar">
                <div class="recover-header">
                    <i class="bx bx-lock-alt recover-icon"></i>
                    <h2 class="animation" style="--i:0;">Recuperar</h2>
                    <p class="animation" style="--i:1;">Te enviaremos un enlace a tu correo.</p>
                </div>
                <form id="formSolicitarRecuperacion" action="?pagina=recuperacion" method="POST">
                    <input type="hidden" name="accion" value="solicitarRecuperacion">
                    <div class="input-box animation" style="--i:2;">
                        <input type="email" name="email" required>
                        <label>Correo Electrónico</label>
                        <i class="bx bx-envelope"></i>
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
                <form id="formRestablecerClave" action="?pagina=recuperacion" method="POST">
                    <input type="hidden" name="accion" value="restablecerClave">
                    <input type="hidden" name="token" id="tokenHiddenInput">
                    <div class="input-box animation" style="--i:2;">
                        <input type="password" name="nueva_clave" id="nueva_clave" required>
                        <label>Nueva Contraseña</label>
                        <i class="bx bx-lock-open-alt" id="toggleNuevaClave" style="cursor: pointer;"></i>
                    </div>
                    <div class="input-box animation" style="--i:3;">
                        <input type="password" name="repetir_clave" id="repetir_clave" required>
                        <label>Confirmar Contraseña</label>
                        <i class="bx bx-lock-alt" id="toggleRepetirClave" style="cursor: pointer;"></i>
                    </div>
                    <button type="submit" class="btn animation" style="--i:4;">Cambiar Contraseña</button>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/validaciones/iniciarSesion/script.js"></script>
    <script src="assets/js/validaciones/iniciarSesion/iniciarSesion.js"></script>
    <script src="assets/js/validaciones/iniciarSesion/iniciarSesion2.js"></script>
    <script src="assets/js/validaciones/clave/recuperacion.js"></script>
</body>

</html>
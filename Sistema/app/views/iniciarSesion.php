<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accesorios Edward</title>
    
   <link rel="icon" href="./assets/img/icono.ico">
    <link rel="stylesheet" href="assets/CSS/Estilos_login.css">

    <script src="assets/Library/JQuery/jquery-3.7.0.min.js"></script>
    <script src="assets/Library/SweetAlerts/sweetalert2.all.js"></script>
    <script src="assets/Library/Bootstrap/bootstrap.min.js"></script>
    
   

    
   
</head>

<body>

    <div class="wrapper">
        <span class="bg-animate"></span>
        <span class="bg-animate2"></span>

        <div class="form-box login">
            <h2 class="animation" style="--i:0;">Login</h2>
            
            <a href="../index.php" class="btn-regresar fadeIn fourth">
                <i class="fas fa-home"></i> Inicio
            </a>

            <form id="loginForm" method="POST" onsubmit="return false;">
                <div class="input-box animation" style="--i:1;">
                    <input type="text" id="login" name="usuario" placeholder="Usuario">
                    <label>Usuario</label>
                    <i class="bx bx-user-circle"></i>
                    <small id="textoMensaje" class="error-msg" style="color: #ff4d4d; display: none;"></small>
                </div>
                <div class="input-box animation" style="--i:2;">
                    <input type="password" id="Contraseña" name="clave" placeholder="Contraseña">
                    <label>Contraseña</label>
                    <i class="bx bx-lock"></i>
                </div>
                
                <div class="forgot-pass animation" style="--i:2.5;">
                    <a href="#" class="recover-link">¿Olvidaste tu contraseña?</a>
                </div>

                <div class="contenedor-botones">
                    <button type="submit" id="acceder" class="fadeIn fourth">Ingresar</button>
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
            <form action="#">
                <div class="input-box animation" style="--i:18;">
                    <input type="text" required>
                    <label>Usuario</label>
                    <i class="bx bx-user-circle"></i>
                </div>
                <div class="input-box animation" style="--i:19;">
                    <input type="email" required>
                    <label>Correo</label>
                    <i class="bx bx-envelope"></i>
                </div>
                <div class="input-box animation" style="--i:20;">
                    <input type="password" required>
                    <label>Contraseña</label>
                    <i class="bx bx-lock"></i>
                </div>
                <button type="submit" class="btn animation" style="--i:21;">Registrarse</button>
                <div class="logreg-link animation" style="--i:22;">
                    <p>¿Ya tienes una cuenta? <a href="#" class="login-link">Inicia Sesión</a></p>
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
    <script src="assets/js/iniciarSesion.js"></script>
    <script src="assets/js/validaciones/iniciarSesion/iniciarSesion.js"></script>
    <script src="assets/js/validaciones/clave/recuperacion.js"></script>
</body>

</html>
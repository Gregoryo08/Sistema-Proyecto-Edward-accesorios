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
        <a href="?pagina=pagina_principal" class="btn-volver-fijo">
            <i class="fas fa-arrow-left"></i> Volver
        </a>

        <span class="bg-animate"></span>
        <span class="bg-animate2"></span>

        <div class="form-box login">
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
                    <div class="cedula-group">
                        <div class="input-box">
                            <input type="text" name="cedula" id="cedula" required>
                            <label>Cédula</label>
                            <i class="bx bx-id-card"></i>
                            <small class="error-msg">Obligatorio</small>
                        </div>
                        <button type="button" id="btnValidarCedula" class="btn-validar">Validar</button>
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
                        <input type="text" name="direccion" id="direccion" required>
                        <label>Dirección</label>
                        <i class="bx bx-map"></i>
                        <small class="error-msg">Obligatorio</small>
                    </div>
                    <div class="input-box date-box">
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>
                        <label class="date-label-fixed">Fecha de Nacimiento</label>
                        <small class="error-msg">Obligatorio</small>
                    </div>
                    <div class="input-box select-box">
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
    <script src="assets/js/validaciones/iniciarSesion/registro.js"></script>
    <script src="assets/js/validaciones/clave/recuperacion.js"></script>

    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                const $btn = $('#acceder');
                $btn.text('Iniciando sesión...').prop('disabled', true);
            });

            $('#fecha_nacimiento').on('change', function() {
                const fechaVal = $(this).val();
                if (fechaVal) {
                    const fechaNacimiento = new Date(fechaVal);
                    const hoy = new Date();
                    let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
                    const m = hoy.getMonth() - fechaNacimiento.getMonth();

                    if (m < 0 || (m === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
                        edad--;
                    }

                    if (edad < 18) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Edad no permitida',
                            text: 'Debes ser mayor de 18 años para registrarte.',
                            confirmButtonColor: '#00d4ff'
                        });
                        $(this).val('');
                    }
                }
            });

            $('#formRegistro').on('submit', function(e) {
                const fechaNacimientoVal = $('#fecha_nacimiento').val();
                if (fechaNacimientoVal) {
                    const fechaNacimiento = new Date(fechaNacimientoVal);
                    const hoy = new Date();
                    let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
                    const m = hoy.getMonth() - fechaNacimiento.getMonth();

                    if (m < 0 || (m === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
                        edad--;
                    }

                    if (edad < 18) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Edad no permitida',
                            text: 'Debes ser mayor de 18 años para registrarte.',
                            confirmButtonColor: '#00d4ff'
                        });
                        e.preventDefault();
                        return false;
                    }
                }

                const $btn = $(this).find('button[type="submit"]');
                $btn.text('Registrando...').prop('disabled', true);
            });
        });
    </script>

    <style>
        .btn-volver-fijo {
            padding-top: 15px;
            position: absolute;
            top: 15px;
            left: 20px;
            z-index: 100;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #0ef;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: 0.3s ease;
        }

        .btn-volver-fijo:hover {
            color: #fff;
            transform: translateX(-3px);
        }

        .form-scroll-container {
            padding-bottom: 50px;
            max-height: 250px;
            overflow-y: auto;
            padding-right: 12px;
        }

        .form-scroll-container::-webkit-scrollbar {
            width: 6px;
        }

        .form-scroll-container::-webkit-scrollbar-thumb {
            background: #0ef;
            border-radius: 10px;
        }

        .cedula-group {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            margin: 25px 0 15px 0;
        }

        .cedula-group .input-box {
            flex: 1;
            margin: 0 !important;
        }

        .btn-validar {
            height: 48px;
            padding: 0 20px;
            background: #0ef;
            border: 2px solid #0ef;
            outline: none;
            border-radius: 40px;
            cursor: pointer;
            font-size: 0.95em;
            color: #fff;
            font-weight: 600;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
            transition: 0.5s;
            white-space: nowrap;
        }

        .btn-validar:hover {
            background: transparent;
            color: #0ef;
        }

        .input-box.date-box {
            position: relative;
            margin: 25px 0 15px 0;
            border-bottom: 2px solid #0ef;
        }

        .input-box.date-box input[type="date"] {
            width: 100%;
            height: 45px;
            background: transparent;
            border: none;
            outline: none;
            font-size: 1rem;
            color: #fff;
            padding: 0 35px 0 5px;
            box-sizing: border-box;
            color-scheme: dark;
        }

        .input-box.date-box input[type="date"]::-webkit-calendar-picker-indicator {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .input-box.date-box::after {
            content: '\ea17';
            font-family: 'boxicons';
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: #fff;
            pointer-events: none;
        }

        .input-box.date-box .date-label-fixed {
            position: absolute;
            top: -15px;
            left: 0;
            font-size: 0.8rem;
            color: #0ef;
            font-weight: 600;
            pointer-events: none;
        }

        .input-box.select-box {
            position: relative;
            margin: 25px 0 15px 0;
            border-bottom: 2px solid #0ef;
        }

        .input-box.select-box select {
            width: 100%;
            height: 45px;
            background: transparent;
            border: none;
            outline: none;
            font-size: 1rem;
            color: #fff;
            padding: 0 35px 0 5px;
            box-sizing: border-box;
        }

        .input-box.select-box select option {
            background: #081b29;
            color: #fff;
        }

        .theme-toggle-btn {
            position: fixed;
            top: 25px;
            right: 25px;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(0, 239, 255, 0.3);
            color: #0ef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .theme-toggle-btn:hover {
            transform: scale(1.1);
            background: rgba(0, 239, 255, 0.2);
            border-color: #0ef;
            box-shadow: 0 0 15px rgba(0, 239, 255, 0.4);
        }

        body.light-mode .input-box.date-box::after {
            color: #007bff;
        }

        body.light-mode {
            color: #333;
        }

        body.light-mode .wrapper {
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        body.light-mode .form-box h2,
        body.light-mode .info-text h2 {
            color: #333;
        }

        body.light-mode .input-box input,
        body.light-mode .input-box.date-box input[type="date"],
        body.light-mode .input-box.select-box select {
            color: #333;
        }

        body.light-mode .input-box.date-box input[type="date"]::-webkit-calendar-picker-indicator {
            filter: none;
        }

        body.light-mode .input-box.date-box select option,
        body.light-mode .input-box.select-box select option {
            background: #ffffff;
            color: #333;
        }

        body.light-mode .input-box label,
        body.light-mode .input-box i,
        body.light-mode .input-box.date-box .date-label-fixed {
            color: #007bff;
        }

        body.light-mode .input-box.date-box,
        body.light-mode .input-box.select-box {
            border-bottom-color: #007bff;
        }

        body.light-mode .theme-toggle-btn {
            background: rgba(0, 0, 0, 0.05);
            border-color: rgba(0, 123, 255, 0.3);
            color: #007bff;
        }

        body.light-mode .theme-toggle-btn:hover {
            background: rgba(0, 123, 255, 0.1);
            border-color: #007bff;
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.2);
        }
    </style>

</html>
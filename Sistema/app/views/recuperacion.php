<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Mesón De La Campana</title>
    <link rel="icon" href="assets/image/logo.png">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">

    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/Library/DataTables/datatables.min.css">
    <link rel="stylesheet" href="assets/Library/SweetAlerts/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/Library/Select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="assets/Library/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/Library/Toastr/toastr.min.css">
    <script src="assets/Library/JQuery/jquery-3.7.0.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/Library/SweetAlerts/sweetalert2.all.js"></script>
    <script src="assets/Library/Select2/dist/js/select2.min.js"></script>
    <script src="assets/Library/DataTables/datatables.min.js"></script>
    <script src="assets/Library/Toastr/toastr.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <link rel="stylesheet" href="assets/CSS/main.css">
    <link rel="stylesheet" href="assets/CSS/perfil.css">
    <link rel="stylesheet" href="assets/CSS/empleados.css">
    <link rel="stylesheet" href="assets/CSS/clientes.css">
    <link rel="stylesheet" href="assets/CSS/contraseña.css">
    <link rel="stylesheet" href="assets/CSS/habitaciones.css">
    <link rel="stylesheet" href="assets/CSS/reportes.css">
    <link rel="stylesheet" href="assets/CSS/almacen.css">
    <link rel="stylesheet" href="assets/CSS/preloader.css">
    <link rel="stylesheet" href="assets/CSS/menu.css">

</head>

<main class="main" id="main">
    <section id="recuperacion-seccion" class="hero section d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="container d-flex flex-column align-items-center">
            <div class="col-lg-6 col-md-8 col-sm-10 col-12">
                <div class="card shadow-lg p-4 p-md-5">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4 display-5 fw-bold">Recuperación de Contraseña </h2>

                        <?php
                       
                        if (isset($_SESSION['mensaje_recuperacion'])) {
                            $mensaje = $_SESSION['mensaje_recuperacion']['mensaje'];
                            $tipo = $_SESSION['mensaje_recuperacion']['tipo'];
                            echo "<div id='alertMessage' class='alert alert-{$tipo} mt-3 mb-4' role='alert' style='display: block;'>{$mensaje}</div>";
                            unset($_SESSION['mensaje_recuperacion']); 
                        } else {
                            
                            echo "<div id='alertMessage' class='mt-3 mb-4' style='display: none;'></div>";
                        }
                        ?>

                        <form id="formSolicitarRecuperacion" class="needs-validation" novalidate  method="POST">
                            <input type="hidden" name="accion" value="solicitarRecuperacion">
                            <p class="text-center text-muted mb-4">Ingresa tu correo electrónico asociado a la cuenta para recibir un enlace de recuperación.</p>
                            <div class="mb-4">
                                <label for="email" class="form-label visually-hidden">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control form-control-lg" name="email" id="email" required placeholder="tu.correo@ejemplo.com">
                                    <div class="invalid-feedback">
                                        Por favor, ingresa un correo electrónico válido.
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-send-fill me-2"></i> Enviar Enlace de Recuperación
                                </button>
                            </div>
                        </form>

                        <form id="formRestablecerClave" class="needs-validation" novalidate style="display: none;"  method="POST">
                            <input type="hidden" id="tokenHiddenInput" name="token">
                            <input type="hidden" name="accion" value="restablecerClave">
                            <p class="text-center text-muted mb-4">Ingresa tu nueva contraseña. Asegúrate de que sea segura.</p>

                            <div class="mb-3">
                                <label for="nueva_clave" class="form-label visually-hidden">Nueva Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" class="form-control form-control-lg" name="nueva_clave" id="nueva_clave" required placeholder="Nueva contraseña">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleNuevaClave">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="invalid-feedback">
                                        La contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas, minúsculas y números.
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="repetir_clave" class="form-label visually-hidden">Repetir Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                    <input type="password" class="form-control form-control-lg" name="repetir_clave" id="repetir_clave" required placeholder="Repetir contraseña">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleRepetirClave">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="invalid-feedback">
                                        Las contraseñas no coinciden.
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-arrow-clockwise me-2"></i> Restablecer Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php 

require_once('assets/comunes/footer.php'); 
?>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/js/iniciarSesion.js"></script>
<script src="assets/js/validaciones/clave/recuperacion.js"></script>



</body>
</html>
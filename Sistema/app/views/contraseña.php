<?php require_once('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modalClave.php'); ?>

<main class="main" id="main">

    <section class="content_data clave section " id="hero" style="height: auto;">
        <div class="div_data clave3">
            <div class="cont_title">
                <img src="assets/img/user.png" class="img_title">
                <h2 class="title">Cambiar Contraseña</h2>
                <img src="assets/img/user.png" class="img_title">
            </div>

            <div class="cont_content clave2" id="cont_content">
                <div class="div_content_clave">
                    <label for="">Cambiar Contraseña por..??</label>
                    <p>Para cambiar la contraseña, primero debes de introducir los datos requeridos en la creacion de tu perfil <br><b>(Codigo de seguridad, Preguntas de seguridad)</b></p>
                </div>
                <div class="div_content_clave">
                    <label for="">Codigo de Seguridad</label>
                    <button class="btn btn-primary" style="background-color: var(--color-titulo);" id="codigo"><i class="bi bi-qr-code"></i></button>
                </div>
            </div>
            <div class="cont_button" id="cont_button">
                <a href="?pagina=principal" class="btn btn-primary">Salir</a>
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
<script src="assets/js/main.js"></script>
<!--<script src="assets/js/validaciones/clave/clave2.js"></script>-->
  <script src="assets/js/validaciones/clave/clave.js"></script>

</body>

</html>
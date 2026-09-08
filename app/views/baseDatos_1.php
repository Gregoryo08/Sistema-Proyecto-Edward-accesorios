<?php
require_once('assets/comunes/menu.php');
?>

<main class="main mt-4" id="main">
    <section id="hero" class="hero section mt-2" style="height: auto;">
        <div>
            <h2 class="text-center">Administrar Base de Datos</h2>

            <div style="padding: 20px; display: flex; width: 100%; align-items: center; flex-direction: column; justify-content: center;">
                
                <div class="contenido_separado" style="background: #333; width: 80%;">
                    <div class="header_content_bd">
                        <h3 class="mb-3">Backup de la Base de Datos</h3>
                    </div>
                    <p style="color: var(--color-dorado);">
                        Realice un backup manual de la base de datos para asegurar su información.
                    </p>
                    <button onclick="realizarBackup()" class="btn btn-primary" style="background-color: #d68307;">Realizar Backup Manual</button>
                </div>

                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <div id="mensaje-backup" style="width: 100%;" class="mt-3"></div>
            </div>
        </div>
    </section>
</main>

<?php require_once('assets/comunes/footer.php'); ?>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<script src="assets/js/validaciones/baseDatos/baseDatos.js"></script>
</body>
</html>
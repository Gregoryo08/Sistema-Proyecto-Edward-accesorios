<?php
require_once('assets/comunes/menu.php');
?>

<main class="main mt-4" id="main">
    <section id="hero" class="hero section mt-2" style="height: auto;">
        <div>
            <h2 class="text-center">Administrar Base de Datos</h2>

            <div style="padding: 20px; display: flex; width: 100%; align-items: center; flex-direction: column; justify-content: center;">
                
                <div class="contenido_separado mt-5" style="background: #333; width: 80%; margin: 0 auto;">
                    <div class="header_content_bd text-center">
                        <h3 class="mb-3">Restaurar Base de Datos</h3>
                    </div>
                    <p style="color: var(--color-dorado); text-align: center;">
                        Seleccione un archivo <strong>.sql</strong> para restaurar la base de datos.
                    </p>

                    <form id="formRestaurar" enctype="multipart/form-data" class="text-center">
                        <input type="hidden" name="accion" value="restaurar_bd">
                        <div class="mb-4">
                            <label for="backup" class="custom-file-label">Seleccionar archivo</label>
                            <input type="file" name="backup" id="backup" accept=".sql" class="custom-file-input" required>
                            <span id="file-name">Ningún archivo seleccionado</span>
                        </div>
                        <button type="button" style="background-color: #d68307;" id="botonRestaurar" class="btn btn-danger">Restaurar Base de Datos</button>
                    </form>
                </div>

                <div id="mensaje-backup" style="width: 100%;" class="mt-3"></div>
            </div>
        </div>
    </section>
</main>

<?php require_once('assets/comunes/footer.php'); ?>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/validaciones/baseDatos/baseDatos.js"></script>
<script>
    document.getElementById('backup').addEventListener('change', function() {
        document.getElementById('file-name').textContent = this.files.length ? this.files[0].name : 'Ningún archivo seleccionado';
    });

    document.getElementById('botonRestaurar').addEventListener('click', function(e) {
        const fileInput = document.getElementById('backup');
        
        if (!fileInput.files.length) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor, seleccione un archivo .sql antes de continuar.',
                confirmButtonColor: '#d68307'
            });
            return;
        }

        Swal.fire({
            title: '¿Está seguro?',
            text: 'Esto sobrescribirá la información actual.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d68307',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, restaurar',
            cancelButtonText: 'Cancelar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Restaurando...',
                    text: 'Por favor espere mientras se procesa la base de datos.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const formData = new FormData(document.getElementById('formRestaurar'));

                try {
                    const response = await fetch("", {
                        method: "POST",
                        body: formData
                    });

                    const textResponse = await response.text();
                    let data;
                    
                    try {
                        data = JSON.parse(textResponse);
                    } catch (err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de servidor',
                            text: 'El servidor devolvió una respuesta no válida.',
                            confirmButtonColor: '#d68307'
                        });
                        return;
                    }

                    if (data.resultado === 'exito') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.mensaje,
                            confirmButtonColor: '#d68307'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.mensaje,
                            confirmButtonColor: '#d68307'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error inesperado al conectar con el servidor.',
                        confirmButtonColor: '#d68307'
                    });
                }
            }
        });
    });
</script>
</body>
</html>
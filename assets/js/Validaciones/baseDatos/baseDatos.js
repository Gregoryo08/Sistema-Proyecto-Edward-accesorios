$(document).ready(function () {
    const backupInput = document.getElementById('backup');
    if (backupInput) {
        backupInput.addEventListener('change', function() {
            const fileName = this.files.length ? this.files[0].name : 'Ningún archivo seleccionado';
            const fileNameContainer = document.getElementById('file-name');
            if (fileNameContainer) {
                fileNameContainer.textContent = fileName;
            }
        });
    }

    async function realizarBackup() {
        const mensajeBackupDiv = document.getElementById("mensaje-backup");
        mensajeBackupDiv.innerHTML = '<div class="alert alert-info">Procesando backup...</div>';

        try {
            const response = await fetch("?pagina=baseDatos", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "accion=realizar_backup",
            });

            const textResponse = await response.text();
            let data;
            
            try {
                data = JSON.parse(textResponse);
            } catch (e) {
                mensajeBackupDiv.innerHTML = `<div class="alert alert-danger">Error de JSON. Revisa la consola para detalles.</div>`;
                console.error("Texto recibido que no es JSON:", textResponse);
                return;
            }
            
            if (data.resultado === "exito") {
                const urlDescarga = `?pagina=baseDatos&accion=descargar_backup&archivo=${encodeURIComponent(data.archivo)}`;
                
                mensajeBackupDiv.innerHTML = `
                    <div class="alert alert-success">
                        Backup generado correctamente. 
                        <a id="btnDescargar" href="${urlDescarga}" class="btn btn-sm btn-success" style="cursor: pointer; margin-left: 10px; color: white;" download="${data.archivo}">Descargar archivo</a>
                    </div>`;
                
                window.location.href = urlDescarga;

            } else {
                mensajeBackupDiv.innerHTML = `<div class="alert alert-danger">${data.mensaje}</div>`;
            }
        } catch (error) {
            console.error(error);
            mensajeBackupDiv.innerHTML = '<div class="alert alert-danger">Ocurrió un error inesperado al realizar el backup.</div>';
        }
    }

    window.realizarBackup = realizarBackup;
});
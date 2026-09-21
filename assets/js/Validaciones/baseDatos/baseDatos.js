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
            const response = await fetch("?pagina=baseDatos_1", {
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
            
            if (data.resultado === "exito" && Array.isArray(data.archivos)) {
                let enlacesHTML = "";

                data.archivos.forEach(function (archivo, index) {
                    const urlDescarga = `?pagina=baseDatos_1&accion=descargar_backup&archivo=${encodeURIComponent(archivo)}`;
                    enlacesHTML += `<a href="${urlDescarga}" class="btn btn-sm btn-success" style="cursor: pointer; margin-left: 5px; margin-top: 5px; color: white;" download="${archivo}">Descargar ${archivo}</a> `;

                    setTimeout(function () {
                        const a = document.createElement("a");
                        a.href = urlDescarga;
                        a.download = archivo;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    }, index * 500);
                });

                mensajeBackupDiv.innerHTML = `
                    <div class="alert alert-success">
                        Backups generados correctamente. <br>
                        ${enlacesHTML}
                    </div>`;

            } else {
                mensajeBackupDiv.innerHTML = `<div class="alert alert-danger">${data.mensaje || "Error al realizar el respaldo."}</div>`;
            }
        } catch (error) {
            console.error(error);
            mensajeBackupDiv.innerHTML = '<div class="alert alert-danger">Ocurrió un error inesperado al realizar el backup.</div>';
        }
    }

    window.realizarBackup = realizarBackup;
});
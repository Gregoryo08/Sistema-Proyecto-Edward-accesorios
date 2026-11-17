document.addEventListener('DOMContentLoaded', () => {

    const formSolicitarRecuperacion = document.getElementById('formSolicitarRecuperacion');
    const formRestablecerClave = document.getElementById('formRestablecerClave');
    const tokenHiddenInput = document.getElementById('tokenHiddenInput');
    const alertMessageDiv = document.getElementById('alertMessage');

    const showMessage = (message, type) => {
        if (alertMessageDiv) {
            alertMessageDiv.className = `alert alert-${type} mt-3 mb-4`;
            alertMessageDiv.textContent = message;
            alertMessageDiv.style.display = 'block';
        }
    };

    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');

    const controllerUrl = "?pagina=recuperacion"; 

    if (token) {
        fetch(`${controllerUrl}&accion=validarToken&token=${encodeURIComponent(token)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.valido) {
                    if (formSolicitarRecuperacion) formSolicitarRecuperacion.style.display = 'none';
                    if (formRestablecerClave) formRestablecerClave.style.display = 'block';
                    if (tokenHiddenInput) tokenHiddenInput.value = token;
                } else {
                    if (formSolicitarRecuperacion) formSolicitarRecuperacion.style.display = 'block';
                    if (formRestablecerClave) formRestablecerClave.style.display = 'none';
                    showMessage(data.mensaje || "El enlace de recuperación es inválido o ha expirado.", "danger");
                }
            })
            .catch(error => {
                console.error('Error al validar el token:', error);
                if (formSolicitarRecuperacion) formSolicitarRecuperacion.style.display = 'block';
                if (formRestablecerClave) formRestablecerClave.style.display = 'none';
                showMessage("Ocurrió un error al verificar el enlace de recuperación. Inténtalo de nuevo.", "danger");
            });
    } else {
        if (formSolicitarRecuperacion) formSolicitarRecuperacion.style.display = 'block';
        if (formRestablecerClave) formRestablecerClave.style.display = 'none';
    }
});
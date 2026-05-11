document.addEventListener('DOMContentLoaded', () => {

    const wrapper = document.querySelector('.wrapper');
    const formSolicitarRecuperacion = document.getElementById('formSolicitarRecuperacion');
    const formRestablecerClave = document.getElementById('formRestablecerClave');
    const tokenHiddenInput = document.getElementById('tokenHiddenInput');
    const alertMessageDiv = document.getElementById('alertMessage');
    const sectionSolicitar = document.getElementById('sectionSolicitar');
    const sectionRestablecer = document.getElementById('sectionRestablecer');

    const showMessage = (message, type) => {
        if (alertMessageDiv) {
            alertMessageDiv.className = `alert alert-${type} mt-3 mb-4`;
            alertMessageDiv.textContent = message;
            alertMessageDiv.style.display = 'block';
        }
    };

    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');
    const controllerUrl = "index.php?pagina=iniciarSesion";

    if (token) {
        // CORRECCIÓN: Usamos la clase que ya tienes en tu CSS para recuperación
        if (wrapper) {
            wrapper.classList.remove('active'); 
            wrapper.classList.add('show-recover'); 
        }

        fetch(`${controllerUrl}&accion=validarToken&token=${encodeURIComponent(token)}`)
            .then(response => response.json())
            .then(data => {
                if (data.valido) {
                    // Ocultamos la parte de pedir correo y mostramos la de nueva clave
                    if (sectionSolicitar) sectionSolicitar.style.display = 'none';
                    if (sectionRestablecer) sectionRestablecer.style.display = 'block';
                    if (tokenHiddenInput) tokenHiddenInput.value = token;
                } else {
                    showMessage(data.mensaje || "El enlace es inválido o ha expirado.", "danger");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage("Error al verificar el enlace.", "danger");
            });
    }

    // Listener para el link de "¿Olvidaste tu contraseña?"
    const recoverLink = document.querySelector('.recover-link');
    if (recoverLink) {
        recoverLink.addEventListener('click', (e) => {
            e.preventDefault();
            wrapper.classList.add('show-recover');
        });
    }

    // Listener para volver al login
    const backToLogin = document.querySelector('.back-to-login');
    if (backToLogin) {
        backToLogin.addEventListener('click', (e) => {
            e.preventDefault();
            wrapper.classList.remove('show-recover');
        });
    }

    const enviarFormulario = async (form, accion) => {
        const formData = new FormData(form);
        formData.append('accion', accion);

        try {
            const response = await fetch(controllerUrl, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                showMessage(data.success, "success");
                if (data.redirect) {
                    setTimeout(() => window.location.href = data.redirect, 2000);
                }
                form.reset();
            } else {
                const errorMsg = data.error || data.invalido || data.incompleto || "Error inesperado.";
                showMessage(errorMsg, "warning");
            }
        } catch (error) {
            showMessage("Error de conexión.", "danger");
        }
    };

    if (formSolicitarRecuperacion) {
        formSolicitarRecuperacion.addEventListener('submit', (e) => {
            e.preventDefault();
            enviarFormulario(formSolicitarRecuperacion, 'solicitarRecuperacion');
        });
    }

    if (formRestablecerClave) {
        formRestablecerClave.addEventListener('submit', (e) => {
            e.preventDefault();
            const nClave = document.getElementById('nueva_clave');
            const rClave = document.getElementById('repetir_clave');
            
            const passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;

            if (!passwordPattern.test(nClave.value)) {
                showMessage("La clave debe tener 8 caracteres, una mayúscula y un número.", "warning");
                return;
            }

            if (nClave.value !== rClave.value) {
                showMessage("Las contraseñas no coinciden.", "warning");
                return;
            }
            
            enviarFormulario(formRestablecerClave, 'restablecerClave');
        });
    }

    const setupToggle = (inputId, btnId) => {
        const input = document.getElementById(inputId);
        const btn = document.getElementById(btnId);
        if (input && btn) {
            btn.addEventListener('click', () => {
                const isPass = input.type === 'password';
                input.type = isPass ? 'text' : 'password';
                btn.classList.toggle('bx-lock-open-alt');
                btn.classList.toggle('bx-lock-alt');
            });
        }
    };

    setupToggle('nueva_clave', 'toggleNuevaClave');
    setupToggle('repetir_clave', 'toggleRepetirClave');
});
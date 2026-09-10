document.addEventListener('DOMContentLoaded', () => {

    const wrapper = document.querySelector('.wrapper');
    const formSolicitarRecuperacion = document.getElementById('formSolicitarRecuperacion');
    const formRestablecerClave = document.getElementById('formRestablecerClave');
    const tokenHiddenInput = document.getElementById('tokenHiddenInput');
    const sectionSolicitar = document.getElementById('sectionSolicitar');
    const sectionRestablecer = document.getElementById('sectionRestablecer');

    const showSwal = (title, text, icon) => {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            confirmButtonColor: '#00d2ff',
            background: '#0d1117',
            color: '#fff',
            customClass: {
                popup: 'rounded-4 shadow-lg'
            }
        });
    };

    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');
    const controllerUrl = "index.php?pagina=iniciarSesion";

    if (token) {
        if (wrapper) {
            wrapper.classList.remove('active'); 
            wrapper.classList.add('show-recover'); 
        }

        fetch(`${controllerUrl}&accion=validarToken&token=${encodeURIComponent(token)}`)
            .then(response => response.json())
            .then(data => {
                if (data.valido) {
                    if (sectionSolicitar) sectionSolicitar.style.display = 'none';
                    if (sectionRestablecer) sectionRestablecer.style.display = 'block';
                    if (tokenHiddenInput) tokenHiddenInput.value = token;
                } else {
                    showSwal(
                        "Enlace Inválido",
                        data.mensaje || "El enlace de recuperación es inválido o ha expirado.",
                        "error"
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showSwal("Error", "Ocurrió un problema al verificar el enlace.", "error");
            });
    }

    const recoverLink = document.querySelector('.recover-link');
    if (recoverLink) {
        recoverLink.addEventListener('click', (e) => {
            e.preventDefault();
            wrapper.classList.add('show-recover');
        });
    }

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

        Swal.fire({
            title: 'Procesando...',
            text: 'Por favor espera un momento.',
            background: '#0d1117',
            color: '#fff',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await fetch(controllerUrl, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                showSwal(
                    "¡Correo Enviado!",
                    data.success,
                    "success"
                );

                if (data.redirect) {
                    setTimeout(() => window.location.href = data.redirect, 2000);
                }
                form.reset();
            } else {
                // Maneja los distintos tipos de error retornados por PHP
                const errorMsg = data.not_found || data.error || data.invalido || data.incompleto || "Ocurrió un error inesperado.";
                const titleModal = data.not_found ? "Correo No Registrado" : "Atención";
                
                showSwal(titleModal, errorMsg, data.not_found ? "warning" : "error");
            }
        } catch (error) {
            showSwal("Error de Conexión", "No se pudo establecer comunicación con el servidor.", "error");
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
                showSwal(
                    "Contraseña Débil",
                    "La contraseña debe tener al menos 8 caracteres, una mayúscula y un número.",
                    "warning"
                );
                return;
            }

            if (nClave.value !== rClave.value) {
                showSwal(
                    "Sin Coincidencia",
                    "Las contraseñas ingresadas no coinciden.",
                    "warning"
                );
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
const wrapper = document.querySelector('.wrapper');
const registerLink = document.querySelector('.register-link');
const loginLink = document.querySelector('.login-link');
const recoverLink = document.querySelector('.recover-link');
const backLogin = document.querySelector('.back-to-login');

// Cambios de pantalla
registerLink.onclick = () => { wrapper.classList.add('active'); };
loginLink.onclick = () => { wrapper.classList.remove('active'); };
recoverLink.onclick = () => { wrapper.classList.add('show-recover'); };
backLogin.onclick = () => { wrapper.classList.remove('show-recover'); };

// Funciones de validación
const setError = (element, message) => {
    const inputBox = element.parentElement;
    const errorDisplay = inputBox.querySelector('.error-msg');
    errorDisplay.innerText = message;
    inputBox.classList.add('error');
};

const setSuccess = element => {
    const inputBox = element.parentElement;
    inputBox.classList.remove('error');
};

const isValidEmail = email => {
    return /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(String(email).toLowerCase());
};

const validateForm = (form) => {
    const inputs = form.querySelectorAll('input');
    let isFormValid = true;

    inputs.forEach(input => {
        const value = input.value.trim();

        if (value === '') {
            setError(input, 'Requerido');
            isFormValid = false;
        } else if (input.type === 'email' && !isValidEmail(value)) {
            setError(input, 'Email inválido');
            isFormValid = false;
        } else if (input.type === 'password' && value.length < 8) {
            setError(input, 'Mínimo 8 caracteres');
            isFormValid = false;
        } else if (input.type === 'text' && value.length < 4) {
            setError(input, 'Mínimo 4 caracteres');
            isFormValid = false;
        } else {
            setSuccess(input);
        }
    });

    return isFormValid;
};

// Eventos de envío y escritura
document.querySelectorAll('form').forEach(form => {
    form.onsubmit = (e) => {
        if (!validateForm(form)) {
            e.preventDefault();
        }
    };

    // Limpiar error mientras el usuario escribe para mejorar UX
    form.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', () => {
            if (input.parentElement.classList.contains('error')) {
                setSuccess(input);
            }
        });
    });
});
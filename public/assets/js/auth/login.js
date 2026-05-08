const loginForm = document.querySelector('#loginForm');
const loginAlert = document.querySelector('#loginAlert');
const emailInput = document.querySelector('#email');
const passwordInput = document.querySelector('#password');
const emailError = document.querySelector('#emailError');
const passwordError = document.querySelector('#passwordError');
const togglePassword = document.querySelector('#togglePassword');

function setFieldError(input, errorElement, message) {
    const field = input.closest('.field');

    field.classList.add('has-error');
    errorElement.textContent = message;
}

function clearFieldError(input, errorElement) {
    const field = input.closest('.field');

    field.classList.remove('has-error');
    errorElement.textContent = '';
}

function isValidEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}

togglePassword.addEventListener('click', () => {
    const isPasswordVisible = passwordInput.type === 'text';

    passwordInput.type = isPasswordVisible ? 'password' : 'text';
    togglePassword.textContent = isPasswordVisible ? 'Voir' : 'Masquer';
    togglePassword.setAttribute(
        'aria-label',
        isPasswordVisible ? 'Afficher le mot de passe' : 'Masquer le mot de passe'
    );
});

loginForm.addEventListener('submit', (event) => {
    event.preventDefault();

    let hasError = false;
    const email = emailInput.value.trim();
    const password = passwordInput.value.trim();

    clearFieldError(emailInput, emailError);
    clearFieldError(passwordInput, passwordError);
    loginAlert.classList.remove('is-visible');

    if (!email) {
        setFieldError(emailInput, emailError, 'Veuillez saisir votre email.');
        hasError = true;
    } else if (!isValidEmail(email)) {
        setFieldError(emailInput, emailError, 'Veuillez saisir un email valide.');
        hasError = true;
    }

    if (!password) {
        setFieldError(passwordInput, passwordError, 'Veuillez saisir votre mot de passe.');
        hasError = true;
    } else if (password.length < 6) {
        setFieldError(passwordInput, passwordError, 'Le mot de passe doit contenir au moins 6 caracteres.');
        hasError = true;
    }

    if (hasError) {
        loginAlert.classList.add('is-visible');
        return;
    }

    loginAlert.textContent = 'Interface prete. La connexion backend sera ajoutee plus tard.';
    loginAlert.classList.add('is-visible');
});

[emailInput, passwordInput].forEach((input) => {
    input.addEventListener('input', () => {
        const errorElement = input === emailInput ? emailError : passwordError;

        clearFieldError(input, errorElement);

        if (!emailError.textContent && !passwordError.textContent) {
            loginAlert.classList.remove('is-visible');
        }
    });
});

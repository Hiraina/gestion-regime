const registerForm = document.querySelector('#registerForm');
const registerAlert = document.querySelector('#registerAlert');
let isSubmitting = false;

const fields = {
    fullName: {
        input: document.querySelector('#fullName'),
        error: document.querySelector('#fullNameError'),
    },
    email: {
        input: document.querySelector('#email'),
        error: document.querySelector('#emailError'),
    },
    gender: {
        input: document.querySelector('#gender'),
        error: document.querySelector('#genderError'),
    },
    password: {
        input: document.querySelector('#password'),
        error: document.querySelector('#passwordError'),
    },
    passwordConfirm: {
        input: document.querySelector('#passwordConfirm'),
        error: document.querySelector('#passwordConfirmError'),
    },
};

function setFieldError(field, message) {
    field.input.closest('.field').classList.add('has-error');
    field.error.textContent = message;
}

function clearFieldError(field) {
    field.input.closest('.field').classList.remove('has-error');
    field.error.textContent = '';
}

function isValidEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}

function hasVisibleErrors() {
    return Object.values(fields).some((field) => field.error.textContent !== '');
}

document.querySelectorAll('.toggle-password').forEach((button) => {
    button.addEventListener('click', () => {
        const targetInput = document.querySelector(`#${button.dataset.target}`);
        const isPasswordVisible = targetInput.type === 'text';

        targetInput.type = isPasswordVisible ? 'password' : 'text';
        button.textContent = isPasswordVisible ? 'Voir' : 'Masquer';
        button.setAttribute(
            'aria-label',
            isPasswordVisible ? 'Afficher le mot de passe' : 'Masquer le mot de passe'
        );
    });
});

registerForm.addEventListener('submit', (event) => {
    if (isSubmitting) {
        return;
    }

    event.preventDefault();

    let hasError = false;
    const fullName = fields.fullName.input.value.trim();
    const email = fields.email.input.value.trim();
    const gender = fields.gender.input.value;
    const password = fields.password.input.value.trim();
    const passwordConfirm = fields.passwordConfirm.input.value.trim();

    Object.values(fields).forEach(clearFieldError);
    registerAlert.classList.remove('is-visible');

    if (fullName.length < 2) {
        setFieldError(fields.fullName, 'Veuillez saisir votre nom complet.');
        hasError = true;
    }

    if (!email) {
        setFieldError(fields.email, 'Veuillez saisir votre email.');
        hasError = true;
    } else if (!isValidEmail(email)) {
        setFieldError(fields.email, 'Veuillez saisir un email valide.');
        hasError = true;
    }

    if (!gender) {
        setFieldError(fields.gender, 'Veuillez choisir votre genre.');
        hasError = true;
    }

    if (!password) {
        setFieldError(fields.password, 'Veuillez saisir un mot de passe.');
        hasError = true;
    } else if (password.length < 6) {
        setFieldError(fields.password, 'Le mot de passe doit contenir au moins 6 caracteres.');
        hasError = true;
    }

    if (!passwordConfirm) {
        setFieldError(fields.passwordConfirm, 'Veuillez confirmer votre mot de passe.');
        hasError = true;
    } else if (password && password !== passwordConfirm) {
        setFieldError(fields.passwordConfirm, 'Les mots de passe ne correspondent pas.');
        hasError = true;
    }

    if (hasError) {
        registerAlert.classList.add('is-visible');
        return;
    }

    isSubmitting = true;
    registerForm.submit();
});

Object.values(fields).forEach((field) => {
    field.input.addEventListener('input', () => {
        clearFieldError(field);

        if (!hasVisibleErrors()) {
            registerAlert.classList.remove('is-visible');
        }
    });
});

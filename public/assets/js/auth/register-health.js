const healthForm = document.querySelector('#healthForm');
const healthAlert = document.querySelector('#healthAlert');
const heightInput = document.querySelector('#height');
const weightInput = document.querySelector('#weight');
const heightError = document.querySelector('#heightError');
const weightError = document.querySelector('#weightError');
const imcValue = document.querySelector('#imcValue');
const imcCategory = document.querySelector('#imcCategory');

let isSubmitting = false;

function setFieldError(input, errorElement, message) {
    input.closest('.field').classList.add('has-error');
    errorElement.textContent = message;
}

function clearFieldError(input, errorElement) {
    input.closest('.field').classList.remove('has-error');
    errorElement.textContent = '';
}

function getImcCategory(imc) {
    if (imc < 18.5) {
        return 'Insuffisance ponderale';
    }

    if (imc < 25) {
        return 'Normal';
    }

    if (imc < 30) {
        return 'Surpoids';
    }

    return 'Obesite';
}

function calculateImc() {
    const height = Number.parseFloat(heightInput.value);
    const weight = Number.parseFloat(weightInput.value);

    if (!height || !weight || height <= 0 || weight <= 0) {
        imcValue.textContent = '--';
        imcCategory.textContent = 'En attente';
        return;
    }

    const heightInMeters = height / 100;
    const imc = weight / (heightInMeters * heightInMeters);

    imcValue.textContent = imc.toFixed(1);
    imcCategory.textContent = getImcCategory(imc);
}

function validateHealthForm() {
    let hasError = false;
    const height = Number.parseFloat(heightInput.value);
    const weight = Number.parseFloat(weightInput.value);

    clearFieldError(heightInput, heightError);
    clearFieldError(weightInput, weightError);
    healthAlert.classList.remove('is-visible');

    if (!heightInput.value.trim()) {
        setFieldError(heightInput, heightError, 'Veuillez saisir votre taille.');
        hasError = true;
    } else if (Number.isNaN(height) || height < 80 || height > 250) {
        setFieldError(heightInput, heightError, 'La taille doit etre comprise entre 80 et 250 cm.');
        hasError = true;
    }

    if (!weightInput.value.trim()) {
        setFieldError(weightInput, weightError, 'Veuillez saisir votre poids.');
        hasError = true;
    } else if (Number.isNaN(weight) || weight < 20 || weight > 350) {
        setFieldError(weightInput, weightError, 'Le poids doit etre compris entre 20 et 350 kg.');
        hasError = true;
    }

    if (hasError) {
        healthAlert.classList.add('is-visible');
    }

    return !hasError;
}

[heightInput, weightInput].forEach((input) => {
    input.addEventListener('input', () => {
        if (Number.parseFloat(input.value) < 0) {
            input.value = '';
        }

        clearFieldError(input, input === heightInput ? heightError : weightError);
        healthAlert.classList.remove('is-visible');
        calculateImc();
    });
});

healthForm.addEventListener('submit', (event) => {
    if (isSubmitting) {
        return;
    }

    event.preventDefault();

    if (!validateHealthForm()) {
        return;
    }

    isSubmitting = true;
    healthForm.submit();
});

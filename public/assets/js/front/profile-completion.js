const profileForm = document.querySelector('#profileForm');
const profileAlert = document.querySelector('#profileAlert');
const foodHabit = document.querySelector('#foodHabit');
const targetWeight = document.querySelector('#targetWeight');

const errors = {
    mainGoal: document.querySelector('#mainGoalError'),
    activityLevel: document.querySelector('#activityLevelError'),
    foodHabit: document.querySelector('#foodHabitError'),
    targetWeight: document.querySelector('#targetWeightError'),
};

let isSubmitting = false;

function getCheckedValue(name) {
    const selected = document.querySelector(`input[name="${name}"]:checked`);

    return selected ? selected.value : '';
}

function setGroupError(group, errorElement, message) {
    group.classList.add('has-error');
    errorElement.textContent = message;
}

function clearGroupError(group, errorElement) {
    group.classList.remove('has-error');
    errorElement.textContent = '';
}

function setFieldError(input, errorElement, message) {
    input.closest('.field').classList.add('has-error');
    errorElement.textContent = message;
}

function clearFieldError(input, errorElement) {
    input.closest('.field').classList.remove('has-error');
    errorElement.textContent = '';
}

function refreshSelectedCards() {
    document.querySelectorAll('.choice-card').forEach((card) => {
        const input = card.querySelector('input');

        card.classList.toggle('is-selected', input.checked);
    });
}

document.querySelectorAll('.choice-card input').forEach((input) => {
    input.addEventListener('change', () => {
        const group = input.closest('.choice-group');
        const errorElement = input.name === 'main_goal' ? errors.mainGoal : errors.activityLevel;

        clearGroupError(group, errorElement);
        profileAlert.classList.remove('is-visible');
        refreshSelectedCards();
    });
});

foodHabit.addEventListener('change', () => {
    clearFieldError(foodHabit, errors.foodHabit);
    profileAlert.classList.remove('is-visible');
});

targetWeight.addEventListener('input', () => {
    if (Number.parseFloat(targetWeight.value) < 0) {
        targetWeight.value = '';
    }

    clearFieldError(targetWeight, errors.targetWeight);
    profileAlert.classList.remove('is-visible');
});

profileForm.addEventListener('submit', (event) => {
    if (isSubmitting) {
        return;
    }

    event.preventDefault();

    let hasError = false;
    const mainGoalGroup = document.querySelector('input[name="main_goal"]').closest('.choice-group');
    const activityLevelGroup = document.querySelector('input[name="activity_level"]').closest('.choice-group');
    const targetWeightValue = targetWeight.value.trim();
    const targetWeightNumber = Number.parseFloat(targetWeightValue);

    clearGroupError(mainGoalGroup, errors.mainGoal);
    clearGroupError(activityLevelGroup, errors.activityLevel);
    clearFieldError(foodHabit, errors.foodHabit);
    clearFieldError(targetWeight, errors.targetWeight);
    profileAlert.classList.remove('is-visible');

    if (!getCheckedValue('main_goal')) {
        setGroupError(mainGoalGroup, errors.mainGoal, 'Veuillez choisir votre objectif principal.');
        hasError = true;
    }

    if (!getCheckedValue('activity_level')) {
        setGroupError(activityLevelGroup, errors.activityLevel, 'Veuillez choisir votre niveau d activite.');
        hasError = true;
    }

    if (!foodHabit.value) {
        setFieldError(foodHabit, errors.foodHabit, 'Veuillez choisir une habitude alimentaire.');
        hasError = true;
    }

    if (targetWeightValue && (Number.isNaN(targetWeightNumber) || targetWeightNumber < 20 || targetWeightNumber > 350)) {
        setFieldError(targetWeight, errors.targetWeight, 'Le poids cible doit etre compris entre 20 et 350 kg.');
        hasError = true;
    }

    if (hasError) {
        profileAlert.classList.add('is-visible');
        return;
    }

    isSubmitting = true;
    profileForm.submit();
});

refreshSelectedCards();

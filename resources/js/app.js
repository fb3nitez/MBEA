document.addEventListener('DOMContentLoaded', () => {
    const birthday = document.getElementById('birthday');
    const age = document.getElementById('age');
    const genderSelector = document.getElementById('genderSelector');

    birthday.addEventListener('change', () => {
        age.textContent = calculateAge(birthday.value);
    });

    genderSelector.addEventListener('change', () => {
        toggleOtherGenderInput(genderSelector.value);
        loadGenderInputValue(genderSelector.value);
    });

    age.textContent = calculateAge(birthday.value);
    toggleOtherGenderInput(genderSelector.value);
});

function calculateAge(birthday) {
    const today = new Date();
    const dob = new Date(birthday);

    const yearDiff = today.getFullYear() - dob.getFullYear();
    const monthDiff = today.getMonth() - dob.getMonth();

    const age = (
        monthDiff < 0 || monthDiff === 0 &&
        today.getDate() < dob.getDate()
    )? yearDiff - 1 : yearDiff;

    return age > 0? `${age} years old` : '';
}

function toggleOtherGenderInput(gender) {
    const genderInput = document.getElementById('genderInput');
    if (gender.toLowerCase() === 'other') {
        genderInput.classList.remove('hidden');
    } else {
        genderInput.classList.add('hidden');
    }
}

function loadGenderInputValue(gender) {
    const genderInput = document.getElementById('genderInput');
    if (gender.toLowerCase() === 'other') {
        genderInput.value = "";
    } else {
        genderInput.value = gender;
    }
}

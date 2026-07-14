document.addEventListener('livewire:initialized', function() {
    // Update URL when step changes
    Livewire.on('update-url', (data) => {
        const url = new URL(window.location.href);
        url.searchParams.set('step', data.step);
        window.history.replaceState({}, '', url);
    });
});

// Client-side utilities
document.addEventListener('DOMContentLoaded', function() {
    // Age calculation
    const birthday = document.getElementById('birthday');
    const ageDisplay = document.getElementById('age');

    if (birthday && ageDisplay) {
        birthday.addEventListener('change', function() {
            ageDisplay.textContent = calculateAge(this.value);
        });
        // Initial calculation
        ageDisplay.textContent = calculateAge(birthday.value);
    }

    // Gender toggle
    const genderSelector = document.getElementById('genderSelector');
    const genderInput = document.getElementById('genderInput');

    if (genderSelector && genderInput) {
        genderSelector.addEventListener('change', function() {
            const isOther = this.value.toLowerCase() === 'other';
            genderInput.classList.toggle('hidden', !isOther);
            genderInput.value = isOther ? '' : this.value;
        });
    }

    // Slider displays
    const healthSlider = document.getElementById('health-score');
    const healthDisplay = document.getElementById('health-score-display');

    if (healthSlider && healthDisplay) {
        healthSlider.addEventListener('input', function() {
            healthDisplay.textContent = this.value;
        });
    }

    // Concern sliders
    document.querySelectorAll('input[type="range"].concern-slider').forEach(function(slider) {
        slider.addEventListener('input', function() {
            const display = document.querySelector(`.concern-display[data-for="${this.id}"]`);
            if (display) {
                display.textContent = this.value;
            }
        });
    });

    // Expandable sections
    document.querySelectorAll('[data-expands]').forEach(function(input) {
        input.addEventListener('change', function() {
            const target = document.getElementById(this.dataset.expands);
            if (target) {
                target.classList.toggle('hidden', !this.checked);
            }
        });
    });

    // Radio toggles for diagnosed
    document.querySelectorAll('input[name="diagnosed-mh"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const target = document.getElementById('diagnosed-expand');
            if (target) {
                target.classList.toggle('hidden', this.value !== 'yes');
            }
        });
    });

    // Radio toggles for hospitalized
    document.querySelectorAll('input[name="hospitalized"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const target = document.getElementById('hospitalized-expand');
            if (target) {
                target.classList.toggle('hidden', this.value !== 'yes');
            }
        });
    });
});

function calculateAge(birthday) {
    if (!birthday) return '';

    const today = new Date();
    const dob = new Date(birthday);
    let age = today.getFullYear() - dob.getFullYear();
    const monthDiff = today.getMonth() - dob.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
    }

    return age > 0 ? `${age} years old` : '';
}

class PatientIntakeForm {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 5;
        this.formData = {};
        this.stepValidators = {
            1: this.validateStep1.bind(this),
            2: this.validateStep2.bind(this),
            3: this.validateStep3.bind(this),
            4: this.validateStep4.bind(this),
        };

        this.init();
        this.loadFromStorage();
        this.bindEvents();
        this.updateUI();
    }

    init() {
        this.elements = {
            stepPanels: document.querySelectorAll('.step-panel'),
            btnBack: document.getElementById('btn-back'),
            btnNext: document.getElementById('btn-next'),
            btnSubmit: document.getElementById('btn-submit'),
            submitText: document.getElementById('submit-text'),
            submitLoading: document.getElementById('submit-loading'),
            stepLabel: document.getElementById('step-label'),
            percentLabel: document.getElementById('percent-label'),
            progressFill: document.getElementById('progress-fill'),
            errorMessages: document.getElementById('error-messages'),
            errorList: document.getElementById('error-list'),
            successMessage: document.getElementById('success-message'),
            successText: document.getElementById('success-text'),
            form: document.getElementById('intake-form'),
        };

        this.currentStep = this.getStepFromUrl();
    }

    loadFromStorage() {
        try {
            const saved = localStorage.getItem('intake_form_data');
            if (saved) {
                const data = JSON.parse(saved);
                this.formData = data;
                this.populateForm(data);
            }
        } catch (e) {
            console.error('Failed to load from storage:', e);
        }
    }

    getStepFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        const stepParam = urlParams.get('step');

        if (stepParam) {
            const step = parseInt(stepParam);
            if (step >= 1 && step <= this.totalSteps) {
                return step;
            }
        }

        return 1;
    }

    saveToStorage() {
        try {
            const data = this.collectFormData();
            data._currentStep = this.currentStep;
            localStorage.setItem('intake_form_data', JSON.stringify(data));
        } catch (e) {
            console.error('Failed to save to storage:', e);
        }
    }

    collectFormData() {
        const form = document.getElementById('intake-form');
        const formData = new FormData(form);
        const data = {};

        for (let [key, value] of formData.entries()) {
            // Handle checkbox arrays
            if (key.endsWith('[]')) {
                const cleanKey = key.slice(0, -2);
                if (!data[cleanKey]) data[cleanKey] = [];
                data[cleanKey].push(value);
            } else {
                data[key] = value;
            }
        }

        // Handle checkboxes that weren't checked (they don't appear in FormData)
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            const name = checkbox.name;
            if (!data[name]) {
                data[name] = false;
            } else if (data[name] === 'on') {
                data[name] = true;
            }
        });

        // Handle radio buttons
        document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
            data[radio.name] = radio.value;
        });

        return data;
    }

    populateForm(data) {
        // Populate text inputs, textareas, selects
        document.querySelectorAll('input:not([type="radio"]):not([type="checkbox"]), textarea, select').forEach(input => {
            if (data[input.name] !== undefined) {
                input.value = data[input.name];
            }
        });

        // Populate checkboxes
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            if (data[checkbox.name] !== undefined) {
                checkbox.checked = !!data[checkbox.name];
            }
        });

        // Populate radio buttons
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            if (data[radio.name] !== undefined && data[radio.name] === radio.value) {
                radio.checked = true;
            }
        });

        // Trigger change events for dependent fields
        document.querySelectorAll('input[type="checkbox"][data-expands], input[type="radio"][data-expands]').forEach(input => {
            this.handleExpandable(input);
        });

        // Update age display
        if (data.birthday) {
            this.updateAge(data.birthday);
        }

        // Update gender display
        if (data.gender.toLowerCase() === 'other') {
            const genderInput = document.getElementById('genderInput');
            if (genderInput) {
                genderInput.value = data.gender_other;
                genderInput.classList.remove('hidden');
            }
        }

        // Update slider displays
        document.querySelectorAll('input[type="range"]').forEach(slider => {
            this.updateSliderDisplay(slider);
        });
    }

    bindEvents() {
        // Navigation
        this.elements.btnNext.addEventListener('click', () => this.nextStep());
        this.elements.btnBack.addEventListener('click', () => this.prevStep());

        // Form submission
        this.elements.btnSubmit.addEventListener('click', () => this.submitForm());

        // Auto-save on input
        document.addEventListener('input', (e) => {
            if (e.target.closest('#intake-form')) {
                if (e.target.type === 'range') {
                    this.updateSliderDisplay(e.target);
                }
                if (e.target.id === 'birthday') {
                    this.updateAge(e.target.value);
                }
                this.debounceSave();
            }
        });

        // Auto-save on change (for checkboxes, radios, selects)
        document.addEventListener('change', (e) => {
            if (e.target.closest('#intake-form')) {
                // Handle gender toggle
                if (e.target.id === 'genderSelector') {
                    this.handleGenderToggle(e.target);
                }
                // Handle expandable sections
                if (e.target.hasAttribute('data-expands') || e.target.name === 'diagnosed-mh' || e.target.name === 'hospitalized') {
                    this.handleExpandable(e.target);
                }
                this.debounceSave();
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.target.closest('textarea')) {
                e.preventDefault();
                if (this.currentStep === this.totalSteps) {
                    this.submitForm();
                } else {
                    this.nextStep();
                }
            }
        });
    }

    debounceSave() {
        clearTimeout(this.saveTimeout);
        this.saveTimeout = setTimeout(() => {
            this.saveToStorage();
        }, 500);
    }

    // ============================================================
    // STEP VALIDATION
    // ============================================================
    validateStep1() {
        const errors = {};
        const data = this.collectFormData();

        if (!data.name?.trim()) errors.name = 'Full name is required';
        if (!data.birthday) errors.birthday = 'Birthday is required';
        if (!data.sex) errors.sex = 'Sex is required';
        if (!data.chiefComplaint?.trim()) errors.chiefComplaint = 'Chief complaint is required';

        return errors;
    }

    validateStep2() {
        const errors = {};
        const data = this.collectFormData();

        if (data.pmhAutoimmune && !data.pmhAutoimmuneSpecify?.trim()) {
            errors.pmhAutoimmuneSpecify = 'Please specify the autoimmune disease';
        }
        if (data.pmhCancer && !data.pmhCancerSpecify?.trim()) {
            errors.pmhCancerSpecify = 'Please specify the type of cancer';
        }
        if (data.pmhOther && !data.pmhOtherSpecify?.trim()) {
            errors.pmhOtherSpecify = 'Please specify the other medical condition';
        }
        if (data.fhCancer) {
            if (!data.fhCancerType?.trim()) errors.fhCancerType = 'Please specify the type of cancer';
            if (!data.fhCancerRelation?.trim()) errors.fhCancerRelation = 'Please specify the relation';
        }
        if (data.fhPsychiatric) {
            if (!data.fhPsychiatricType?.trim()) errors.fhPsychiatricType = 'Please specify the psychiatric disorder';
            if (!data.fhPsychiatricRelation?.trim()) errors.fhPsychiatricRelation = 'Please specify the relation';
        }
        if (data.fhOther) {
            if (!data.fhOtherSpecify?.trim()) errors.fhOtherSpecify = 'Please specify the other condition';
            if (!data.fhOtherRelation?.trim()) errors.fhOtherRelation = 'Please specify the relation';
        }

        return errors;
    }

    validateStep3() {
        const errors = {};
        const data = this.collectFormData();

        if (data.diagnosedMH === 'yes' && !data.diagnosisSpecify?.trim()) {
            errors.diagnosisSpecify = 'Please specify your mental health diagnosis';
        }

        if (data.hospitalized === 'yes') {
            if (!data.hospTimes) errors.hospTimes = 'Please specify the number of hospitalizations';
            if (!data.hospWhen?.trim()) errors.hospWhen = 'Please specify when you were hospitalized';
        }

        if (data.traumaPhysical) {
            const hasSub = data.tpChild || data.tpAdult || data.tpOngoing || data.tpPast;
            if (!hasSub) errors.traumaPhysical = 'Please specify when physical abuse occurred';
        }
        if (data.traumaEmotional) {
            const hasSub = data.teChild || data.teAdult || data.teOngoing || data.tePast;
            if (!hasSub) errors.traumaEmotional = 'Please specify when emotional abuse occurred';
        }
        if (data.traumaSexual) {
            const hasSub = data.tsChild || data.tsAdult || data.tsOngoing || data.tsPast;
            if (!hasSub) errors.traumaSexual = 'Please specify when sexual abuse occurred';
        }
        if (data.traumaNeglect) {
            const hasSub = data.tnChild || data.tnAdult || data.tnOngoing || data.tnPast;
            if (!hasSub) errors.traumaNeglect = 'Please specify when neglect occurred';
        }

        return errors;
    }

    validateStep4() {
        const errors = {};
        const data = this.collectFormData();

        if (!data.healthScore) errors.healthScore = 'Health score is required';

        const substanceFields = [
            { check: 'subNicotine', amount: 'subNicotineAmount', label: 'nicotine' },
            { check: 'subAlcohol', amount: 'subAlcoholAmount', label: 'alcohol' },
            { check: 'subRecreational', amount: 'subRecreationalAmount', label: 'recreational drugs' },
            { check: 'subMarijuana', amount: 'subMarijuanaAmount', label: 'marijuana' },
            { check: 'subScreentime', amount: 'subScreentimeAmount', label: 'screen time' },
            { check: 'subGambling', amount: 'subGamblingAmount', label: 'gambling' },
            { check: 'subOthers', amount: 'subOthersSpecify', label: 'other substances' },
        ];

        for (const field of substanceFields) {
            if (data[field.check] && !data[field.amount]?.trim()) {
                errors[field.amount] = `Please specify the amount for ${field.label}`;
            }
        }

        return errors;
    }

    validateAllSteps() {
        const allErrors = {};
        for (let step = 1; step <= this.totalSteps; step++) {
            if (this.stepValidators[step]) {
                const errors = this.stepValidators[step]();
                Object.keys(errors).forEach(key => {
                    allErrors[key] = errors[key];
                });
            }
        }
        return allErrors;
    }

    // ============================================================
    // NAVIGATION
    // ============================================================
    nextStep() {
        const errors = this.stepValidators[this.currentStep]();
        if (Object.keys(errors).length > 0) {
            this.showFieldErrors(errors);
            return;
        }

        this.clearFieldErrors();
        this.saveToStorage();

        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            this.updateUI();
            const url = new URL(window.location.href);
            url.searchParams.set('step', this.currentStep);
            window.history.replaceState({}, '', url);
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.updateUI();
            const url = new URL(window.location.href);
            url.searchParams.set('step', this.currentStep);
            window.history.replaceState({}, '', url);
        }
    }

    goToStep(step) {
        if (step >= 1 && step <= this.totalSteps) {
            this.currentStep = step;
            this.updateUI();
            const url = new URL(window.location.href);
            url.searchParams.set('step', this.currentStep);
            window.history.replaceState({}, '', url);
        }
    }

    updateUI() {
        // Show/hide panels
        this.elements.stepPanels.forEach((panel, index) => {
            panel.classList.toggle('hidden', index + 1 !== this.currentStep);
        });

        // Update progress
        const progress = Math.round((this.currentStep / this.totalSteps) * 100);
        this.elements.stepLabel.textContent = this.currentStep;
        this.elements.percentLabel.textContent = `${progress}%`;
        this.elements.progressFill.value = progress;

        // Show/hide navigation buttons
        this.elements.btnBack.classList.toggle('hidden', this.currentStep === 1);
        this.elements.btnNext.classList.toggle('hidden', this.currentStep === this.totalSteps);
        this.elements.btnSubmit.classList.toggle('hidden', this.currentStep !== this.totalSteps);

        // Scroll to top of form
        document.querySelector('main').scrollIntoView({ behavior: 'smooth', block: 'start' });

        // Initialize expandable sections
        this.initExpandable();

        // If on review step, populate summary data
        if (this.currentStep === this.totalSteps) {
            this.populateSummary();
        }
    }

    // ============================================================
    // FORM SUBMISSION
    // ============================================================
    async submitForm() {
        const form = document.getElementById('intake-form');
        const formData = new FormData(form);

        // Show loading state
        const submitLoading = document.getElementById('pageLoading');
        submitLoading.classList.remove('hidden');

        try {
            const response = await fetch('/submit-intake', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Show success modal
                document.getElementById('success_message').textContent = data.message;
                document.getElementById('success_modal').showModal();
            } else {
                // Show error modal with specific message
                document.getElementById('error_message').textContent = data.message || 'An error occurred. Please try again.';
                document.getElementById('error_modal').showModal();
            }
        } catch (error) {
            // Show error modal
            document.getElementById('error_message').textContent = 'Network error. Please check your connection and try again.';
            document.getElementById('error_modal').showModal();
            console.error(error);
        } finally {
            // Reset button state
            submitLoading.classList.add('hidden');
        }
    }

    setLoading(loading) {
        this.elements.submitText.classList.toggle('hidden', loading);
        this.elements.submitLoading.classList.toggle('hidden', !loading);
        this.elements.btnSubmit.disabled = loading;
    }

    // ============================================================
    // UI HELPERS
    // ============================================================
    showErrors(errors) {
        const container = this.elements.errorMessages;
        const list = this.elements.errorList;

        container.classList.remove('hidden');
        list.innerHTML = errors.map(err => `<li>${err}</li>`).join('');

        // Scroll to errors
        container.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    hideErrors() {
        this.elements.errorMessages.classList.add('hidden');
        this.elements.errorList.innerHTML = '';
    }

    showSuccess(message) {
        this.elements.successMessage.classList.remove('hidden');
        this.elements.successText.textContent = message;
        this.elements.successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    updateAge(birthday) {
        const ageDisplay = document.getElementById('age');
        if (!birthday || !ageDisplay) return;

        const today = new Date();
        const dob = new Date(birthday);
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        ageDisplay.textContent = age > 0 ? `${age} years old` : '';
    }

    updateSliderDisplay(slider) {
        const display = document.querySelector(`[data-for="${slider.id}"]`);
        if (display) {
            display.textContent = slider.value;
        }
    }

    handleGenderToggle(select) {
        const input = document.getElementById('genderInput');
        if (!input) return;

        const isOther = select.value.toLowerCase() === 'other';
        input.classList.toggle('hidden', !isOther);
        if (isOther) {
            input.value = '';
            input.focus();
        } else {
            input.value = select.value;
        }
    }

    handleExpandable(input) {
        const targetId = input.dataset?.expands;
        if (!targetId) {
            // Handle radio button groups
            if (input.name === 'diagnosed-mh') {
                const target = document.getElementById('diagnosed-expand');
                if (target) {
                    target.classList.toggle('hidden', input.value !== 'yes');
                }
            }
            if (input.name === 'hospitalized') {
                const target = document.getElementById('hospitalized-expand');
                if (target) {
                    target.classList.toggle('hidden', input.value !== 'yes');
                }
            }
            return;
        }

        const target = document.getElementById(targetId);
        if (target) {
            const isChecked = input.type === 'checkbox' ? input.checked : input.value === 'yes';
            target.classList.toggle('hidden', !isChecked);
        }
    }

    initExpandable() {
        // Checkbox expandables
        document.querySelectorAll('[data-expands]').forEach(input => {
            this.handleExpandable(input);
        });

        // Radio button expandables
        document.querySelectorAll('input[name="diagnosed-mh"], input[name="hospitalized"]').forEach(radio => {
            if (radio.checked) {
                this.handleExpandable(radio);
            }
        });
    }

    startOver() {
        localStorage.removeItem('intake_form_data');
        const url = new URL(window.location.href);
        url.searchParams.delete('step');
        window.location.href = url;
    }

    showFieldErrors(errors) {
        // Clear existing errors first
        this.clearFieldErrors();

        // Show errors below each field
        Object.keys(errors).forEach(fieldName => {
            const message = errors[fieldName];

            // Find the input element
            let input = document.querySelector(`[name="${fieldName}"]`);

            // For special cases like trauma fields
            if (!input) {
                // Try to find by ID or other selectors
                input = document.getElementById(fieldName);
            }

            if (input) {
                // Add error class to input
                input.classList.add('input-error', 'select-error', 'textarea-error');

                // Create error message element
                const errorEl = document.createElement('span');
                errorEl.className = 'text-error text-sm mt-1 block field-error';
                errorEl.textContent = message;
                errorEl.dataset.for = fieldName;

                // Insert after the input or its container
                const parent = input.closest('.form-control') || input.parentElement;
                if (parent) {
                    parent.appendChild(errorEl);
                }
            } else {
                // For form-level errors
                const form = document.getElementById('intake-form');
                if (form) {
                    const errorEl = document.createElement('div');
                    errorEl.className = 'alert alert-error mt-4';
                    errorEl.innerHTML = `<span>${message}</span>`;
                    form.prepend(errorEl);
                }
            }
        });
    }

    clearFieldErrors() {
        // Remove error classes from inputs
        document.querySelectorAll('.input-error, .select-error, .textarea-error').forEach(el => {
            el.classList.remove('input-error', 'select-error', 'textarea-error');
        });

        // Remove error message elements
        document.querySelectorAll('.field-error').forEach(el => el.remove());

        // Remove form-level error alerts
        document.querySelectorAll('#intake-form .alert-error').forEach(el => el.remove());
    }

    toggleReviewCard(cardId) {
        const content = document.getElementById(cardId);
        const icon = document.getElementById(`${cardId}-icon`);

        if (!content || !icon) return;

        content.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }

    populateSummary() {
        const data = this.collectFormData();
        const gender = data.gender.toLowerCase() === 'other'? data.gender_other : data.gender;

        // Patient Information
        document.getElementById('summary-name').textContent = data.name || 'Not provided';
        document.getElementById('summary-birthday').textContent = data.birthday || 'Not provided';
        document.getElementById('summary-sex').textContent = data.sex || 'Not provided';
        document.getElementById('summary-religion').textContent = data.religion || 'Not provided';
        document.getElementById('summary-gender').textContent = gender || 'Not provided';
        document.getElementById('summary-marital').textContent = data.maritalStatus || 'Not provided';
        document.getElementById('summary-year').textContent = data.yearLevel || 'Not provided';
        document.getElementById('summary-course').textContent = data.course || 'Not provided';
        document.getElementById('summary-occupation').textContent = data.occupation || 'Not provided';
        document.getElementById('summary-chief').textContent = data.chiefComplaint || 'Not provided';
        document.getElementById('summary-diagnosis').textContent = data.primaryDiagnosis || 'Not provided';

        // Medical History
        const pmhList = [];
        const pmhFields = ['pmhHypertension', 'pmhStroke', 'pmhTuberculosis', 'pmhThyroid',
                           'pmhDiabetes', 'pmhChronicPain', 'pmhAsthma', 'pmhEpilepsy'];
        const pmhLabels = ['Hypertension', 'Stroke/TIA', 'Tuberculosis', 'Thyroid Disorders',
                           'Diabetes Mellitus', 'Chronic Pain', 'Bronchial Asthma', 'Epilepsy'];

        pmhFields.forEach((field, index) => {
            if (data[field]) pmhList.push(pmhLabels[index]);
        });
        if (data.pmhAutoimmune) pmhList.push('Autoimmune Disease');
        if (data.pmhCancer) pmhList.push('Cancer');
        if (data.pmhOther) pmhList.push('Other');

        document.getElementById('summary-pmh').textContent = pmhList.length > 0 ? pmhList.join(', ') : 'None selected';
        document.getElementById('summary-autoimmune').textContent = data.pmhAutoimmuneSpecify || 'N/A';
        document.getElementById('summary-cancer').textContent = data.pmhCancerSpecify || 'N/A';
        document.getElementById('summary-other-medical').textContent = data.pmhOtherSpecify || 'N/A';
        document.getElementById('summary-medications').textContent = data.currentMedications || 'Not provided';

        // Family History
        const fhList = [];
        if (data.fhHypertension) fhList.push('Hypertension');
        if (data.fhStroke) fhList.push('Stroke');
        if (data.fhDiabetes) fhList.push('Diabetes');
        if (data.fhSubstance) fhList.push('Substance Use');
        if (data.fhCancer) fhList.push('Cancer');
        if (data.fhPsychiatric) fhList.push('Psychiatric');
        if (data.fhOther) fhList.push('Other');

        document.getElementById('summary-fh').textContent = fhList.length > 0 ? fhList.join(', ') : 'None selected';
        document.getElementById('summary-fh-cancer').textContent = data.fhCancerType && data.fhCancerRelation ?
            `${data.fhCancerType} (${data.fhCancerRelation})` : 'N/A';
        document.getElementById('summary-fh-psych').textContent = data.fhPsychiatricType && data.fhPsychiatricRelation ?
            `${data.fhPsychiatricType} (${data.fhPsychiatricRelation})` : 'N/A';
        document.getElementById('summary-fh-other').textContent = data.fhOtherSpecify && data.fhOtherRelation ?
            `${data.fhOtherSpecify} (${data.fhOtherRelation})` : 'N/A';

        // Psychiatric History
        document.getElementById('summary-diagnosed').textContent = data.diagnosedMH ?
            (data.diagnosedMH === 'yes' ? 'Yes' : 'No') : 'Not answered';
        document.getElementById('summary-diagnosis-specify').textContent = data.diagnosisSpecify || 'N/A';
        document.getElementById('summary-hospitalized').textContent = data.hospitalized ?
            (data.hospitalized === 'yes' ? 'Yes' : 'No') : 'Not answered';
        document.getElementById('summary-hosp-times').textContent = data.hospTimes || 'N/A';
        document.getElementById('summary-hosp-when').textContent = data.hospWhen || 'N/A';
        document.getElementById('summary-trauma-physical').textContent = data.traumaPhysical ? 'Yes' : 'No';
        document.getElementById('summary-trauma-emotional').textContent = data.traumaEmotional ? 'Yes' : 'No';
        document.getElementById('summary-trauma-sexual').textContent = data.traumaSexual ? 'Yes' : 'No';
        document.getElementById('summary-trauma-neglect').textContent = data.traumaNeglect ? 'Yes' : 'No';

        // Lifestyle Assessment
        document.getElementById('summary-health-score').textContent = data.healthScore ? `${data.healthScore}/10` : 'Not provided';
        document.getElementById('summary-sleep').textContent = data.sleepHours ? `${data.sleepHours} hours` : 'Not provided';
        document.getElementById('summary-tired').textContent = data.tiredFrequency || 'Not provided';
        document.getElementById('summary-weight').textContent = data.weightPerception || 'Not provided';
        document.getElementById('summary-fastfood').textContent = data.fastFood || 'Not provided';
        document.getElementById('summary-fruitsveg').textContent = data.fruitsVeg || 'Not provided';
        document.getElementById('summary-exercise').textContent = data.exerciseFreq || 'Not provided';

        // PHQ-9 Summary
        const phqFields = ['phqLittleInterest', 'phqFeelingDown', 'phqTroubleSleeping',
                           'phqFeelingTired', 'phqPoorAppetite', 'phqFeelingBad',
                           'phqTroubleConcentrating', 'phqMovingSlow', 'phqThoughtsHurting'];
        const phqAnswered = phqFields.filter(f => data[f]);
        document.getElementById('summary-phq').textContent = phqAnswered.length > 0 ?
            `${phqAnswered.length}/9 questions answered` : 'Not answered';

        // Substances
        const substanceList = [];
        if (data.subNicotine) substanceList.push('Nicotine');
        if (data.subAlcohol) substanceList.push('Alcohol');
        if (data.subRecreational) substanceList.push('Recreational Drugs');
        if (data.subMarijuana) substanceList.push('Marijuana');
        if (data.subScreentime) substanceList.push('Screen Time');
        if (data.subGambling) substanceList.push('Gambling');
        if (data.subOthers) substanceList.push('Other');
        document.getElementById('summary-substances').textContent = substanceList.length > 0 ?
            substanceList.join(', ') : 'None reported';

        document.getElementById('summary-motivation-text').textContent = data.lifestyleMotivation || 'Not provided';
        document.getElementById('summary-motivation').textContent = data.motivationLevel || 'Not provided';
    }
}

// Make toggleReviewCard globally accessible
window.toggleReviewCard = function(cardId) {
    if (window.intakeForm) {
        window.intakeForm.toggleReviewCard(cardId);
    }
}

// ============================================================
// GLOBAL FUNCTIONS
// ============================================================
// Initialize the form
document.addEventListener('DOMContentLoaded', () => {
    window.intakeForm = new PatientIntakeForm();
});

// Expose goToStep globally
window.goToStep = (step) => {
    if (window.intakeForm) {
        window.intakeForm.goToStep(step);
    }
};


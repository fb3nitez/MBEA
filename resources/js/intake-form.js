const IntakeForm = (function() {
    var STATE = {
        currentStep: 1,
        totalSteps: 5,
    };

    // ============================================================
    // DOM HELPERS
    // ============================================================
    function $(selector, context) {
        context = context || document;
        return context.querySelector(selector);
    }

    function $$(selector, context) {
        context = context || document;
        return Array.from(context.querySelectorAll(selector));
    }

    function getValue(id) {
        var el = $('#' + id);
        return el ? el.value : '';
    }

    function isChecked(id) {
        var el = $('#' + id);
        return el ? el.checked : false;
    }

    function getRadioValue(name) {
        var checked = $('input[name="' + name + '"]:checked');
        return checked ? checked.value : '';
    }

    // ============================================================
    // NAVIGATION
    // ============================================================
    function setStep(step) {
        // Validate step range
        if (step < 1 || step > STATE.totalSteps) return;

        // Hide current panel
        var currentPanel = $('#step-' + STATE.currentStep);
        if (currentPanel) {
            currentPanel.classList.add('hidden');
            currentPanel.classList.remove('active');
        }

        STATE.currentStep = step;

        // Show new panel
        var nextPanel = $('#step-' + STATE.currentStep);
        if (nextPanel) {
            nextPanel.classList.remove('hidden');
            nextPanel.classList.add('active');
        }

        // Update progress indicators
        updateProgress();

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Render summary if on last step
        if (STATE.currentStep === STATE.totalSteps) {
            renderSummary();
        }
    }

    function nextStep() {
        if (STATE.currentStep < STATE.totalSteps) {
            setStep(STATE.currentStep + 1);
        }
    }

    function prevStep() {
        if (STATE.currentStep > 1) {
            setStep(STATE.currentStep - 1);
        }
    }

    function goToStep(step) {
        setStep(step);
    }

    // ============================================================
    // PROGRESS UI
    // ============================================================
    function updateProgress() {
        var percent = Math.round((STATE.currentStep / STATE.totalSteps) * 100);

        var progressBar = $('#progress-fill');
        if (progressBar) {
            progressBar.value = percent;
        }

        var stepLabel = $('#step-label');
        if (stepLabel) {
            stepLabel.textContent = STATE.currentStep;
        }

        var percentLabel = $('#percent-label');
        if (percentLabel) {
            percentLabel.textContent = percent + '%';
        }

        // Toggle navigation buttons
        var backButton = $('#btn-back');
        var nextButton = $('#btn-next');
        var submitButton = $('#btn-submit');

        if (backButton) {
            backButton.classList.toggle('hidden', STATE.currentStep === 1);
        }
        if (nextButton) {
            nextButton.classList.toggle('hidden', STATE.currentStep === STATE.totalSteps);
        }
        if (submitButton) {
            submitButton.classList.toggle('hidden', STATE.currentStep !== STATE.totalSteps);
        }
    }

    // ============================================================
    // EXPANDABLE SECTIONS (Checkbox/Radio Toggle)
    // ============================================================
    function initExpandableSections() {
        // Checkbox toggles
        var expandTriggers = $$('[data-expands]');
        expandTriggers.forEach(function(input) {
            input.addEventListener('change', function() {
                var target = $('#' + input.dataset.expands);
                if (target) {
                    target.classList.toggle('hidden', !input.checked);
                }
            });
        });

        // Radio: "Diagnosed" toggle
        var diagnosedRadios = $$('input[name="diagnosed-mh"]');
        diagnosedRadios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                var target = $('#diagnosed-expand');
                if (target) {
                    target.classList.toggle('hidden', radio.value !== 'yes');
                }
            });
        });

        // Radio: "Hospitalized" toggle
        var hospitalizedRadios = $$('input[name="hospitalized"]');
        hospitalizedRadios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                var target = $('#hospitalized-expand');
                if (target) {
                    target.classList.toggle('hidden', radio.value !== 'yes');
                }
            });
        });
    }

    // ============================================================
    // SLIDER DISPLAYS
    // ============================================================
    function initSliders() {
        // Health score slider
        var healthSlider = $('#health-score');
        var healthDisplay = $('#health-score-display');
        if (healthSlider && healthDisplay) {
            healthSlider.addEventListener('input', function() {
                healthDisplay.textContent = healthSlider.value;
            });
        }

        // Concern sliders (substance use)
        var concernSliders = $$('input[type="range"].concern-slider');
        concernSliders.forEach(function(slider) {
            slider.addEventListener('input', function() {
                var display = $('.concern-display[data-for="' + slider.id + '"]');
                if (display) {
                    display.textContent = slider.value;
                }
            });
        });
    }

    // ============================================================
    // AGE CALCULATION
    // ============================================================
    function calculateAge(birthday) {
        if (!birthday) return '';

        var today = new Date();
        var dob = new Date(birthday);

        var age = today.getFullYear() - dob.getFullYear();
        var monthDiff = today.getMonth() - dob.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        return age > 0 ? age + ' years old' : '';
    }

    function initAgeCalculation() {
        var birthdayInput = $('#birthday');
        var ageDisplay = $('#age');

        if (birthdayInput && ageDisplay) {
            function updateAge() {
                ageDisplay.textContent = calculateAge(birthdayInput.value);
            }

            birthdayInput.addEventListener('change', updateAge);
            updateAge(); // Initial calculation
        }
    }

    // ============================================================
    // GENDER TOGGLE
    // ============================================================
    function initGenderToggle() {
        var genderSelector = $('#genderSelector');
        var genderInput = $('#genderInput');

        if (!genderSelector || !genderInput) return;

        function toggleGenderField() {
            var isOther = genderSelector.value.toLowerCase() === 'other';
            genderInput.classList.toggle('hidden', !isOther);
            genderInput.value = isOther ? '' : genderSelector.value;
        }

        genderSelector.addEventListener('change', toggleGenderField);
        toggleGenderField(); // Initial state
    }

    // ============================================================
    // SUMMARY RENDERERS
    // ============================================================
    function createSummaryRow(label, value) {
        var displayValue = value && value.trim() ? value : 'Not provided';
        return `<div class="flex border-b border-base-content/10 pb-2 mt-2">
                    <span class="label">${label}</span><span class="value ms-auto">${displayValue}</span>
                </div>`;
    }

    function createBadgeRow(label, items) {
        if (!items || items.length === 0) {
            return createSummaryRow(label, 'None selected');
        }

        var badges = '';
        for (var i = 0; i < items.length; i++) {
            badges += '<span class="badge">' + items[i] + '</span>';
        }

        return '<div class="summary-row"><span class="label">' + label + '</span><div class="badge-row">' + badges + '</div></div>';
    }

    function renderPatientSummary() {
        var container = $('#summary-patient');
        if (!container) return;

        var html =
            createSummaryRow('Name', getValue('name')) +
            createSummaryRow('Age', getValue('age')) +
            createSummaryRow('Sex', getValue('sex')) +
            createSummaryRow('Chief Complaint', getValue('chief-complaint'));

        container.innerHTML = html;
    }

    function renderMedicalSummary() {
        var container = $('#summary-medical');
        if (!container) return;

        var conditionFields = [
            { id: 'pmh-hypertension', label: 'Hypertension' },
            { id: 'pmh-stroke', label: 'Stroke or TIA' },
            { id: 'pmh-tuberculosis', label: 'Tuberculosis' },
            { id: 'pmh-thyroid', label: 'Thyroid Disorders' },
            { id: 'pmh-diabetes', label: 'Diabetes Mellitus' },
            { id: 'pmh-chronic-pain', label: 'Chronic Pain / Fibromyalgia' },
            { id: 'pmh-asthma', label: 'Bronchial Asthma' },
            { id: 'pmh-epilepsy', label: 'Epilepsy / Seizure Disorder' },
            { id: 'pmh-autoimmune', label: 'Autoimmune Disease' },
            { id: 'pmh-cancer', label: 'Cancer' },
            { id: 'pmh-other', label: 'Other' },
        ];

        var selectedConditions = [];
        for (var i = 0; i < conditionFields.length; i++) {
            if (isChecked(conditionFields[i].id)) {
                selectedConditions.push(conditionFields[i].label);
            }
        }

        var html =
            createBadgeRow('Personal Medical History', selectedConditions) +
            createSummaryRow('Current Medications', getValue('current-medications'));

        container.innerHTML = html;
    }

    function renderPsychiatricSummary() {
        var container = $('#summary-psychiatric');
        if (!container) return;

        var diagnosed = getRadioValue('diagnosed-mh');
        var html = createSummaryRow('Diagnosed with mental health condition', diagnosed);

        if (diagnosed === 'yes') {
            html += createSummaryRow('Diagnosis', getValue('diagnosis-specify'));
        }

        html += createSummaryRow('Previous hospitalization', getRadioValue('hospitalized'));

        container.innerHTML = html;
    }

    function renderLifestyleSummary() {
        var container = $('#summary-lifestyle');
        if (!container) return;

        var healthScore = getValue('health-score');
        var html =
            createSummaryRow('Health Score', healthScore ? healthScore + '/10' : 'Not rated') +
            createSummaryRow('Sleep Hours', getValue('sleep-hours')) +
            createSummaryRow('Exercise Frequency', getValue('exercise-freq')) +
            createSummaryRow('Motivation Level', getValue('motivation-level'));

        container.innerHTML = html;
    }

    function renderSummary() {
        renderPatientSummary();
        renderMedicalSummary();
        renderPsychiatricSummary();
        renderLifestyleSummary();
    }

    // ============================================================
    // FORM SUBMISSION
    // ============================================================
    function submitForm() {
        alert(
            'Thank you! Your information has been submitted successfully.\n' +
            'Please wait to be called for your consultation.'
        );
        window.location.href = '/';
    }

    // ============================================================
    // INITIALIZATION
    // ============================================================
    function init() {
        // Navigation buttons
        var goToStepButtons = $$('[data-go-step]');
        for (var i = 0; i < goToStepButtons.length; i++) {
            (function(button) {
                button.addEventListener('click', function() {
                    goToStep(Number(button.dataset.goStep));
                });
            })(goToStepButtons[i]);
        }

        var nextButtons = $$('[data-action="next"]');
        for (var j = 0; j < nextButtons.length; j++) {
            nextButtons[j].addEventListener('click', nextStep);
        }

        var prevButtons = $$('[data-action="prev"]');
        for (var k = 0; k < prevButtons.length; k++) {
            prevButtons[k].addEventListener('click', prevStep);
        }

        var submitButtons = $$('[data-action="submit"]');
        for (var l = 0; l < submitButtons.length; l++) {
            submitButtons[l].addEventListener('click', submitForm);
        }

        // Hide all panels initially
        var panels = $$('.step-panel');
        for (var m = 0; m < panels.length; m++) {
            panels[m].classList.add('hidden');
            panels[m].classList.remove('active');
        }

        // Initialize features
        initExpandableSections();
        initSliders();
        initAgeCalculation();
        initGenderToggle();

        setStep(5);
    }

    // ============================================================
    // PUBLIC API
    // ============================================================
    return {
        init: init,
        goToStep: goToStep,
        nextStep: nextStep,
        prevStep: prevStep,
        submitForm: submitForm,
        setStep: setStep,
    };
})();

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', IntakeForm.init);

// Expose for inline use
window.IntakeForm = IntakeForm;

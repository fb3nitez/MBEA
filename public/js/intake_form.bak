const formState = {
  currentStep: 1,
  totalSteps: 5,
};

function $(selector, root = document) {
  return root.querySelector(selector);
}

function $all(selector, root = document) {
  return Array.from(root.querySelectorAll(selector));
}

function setStep(step) {
  const previousPanel = $('#step-' + formState.currentStep);
  if (previousPanel) {
    previousPanel.classList.add('hidden');
    previousPanel.classList.remove('active');
  }

  formState.currentStep = step;

  const nextPanel = $('#step-' + formState.currentStep);
  if (nextPanel) {
    nextPanel.classList.remove('hidden');
    nextPanel.classList.add('active');
  }

  const percent = Math.round((formState.currentStep / formState.totalSteps) * 100);
  const progress = $('#progress-fill');
  if (progress) {
    progress.value = percent;
  }

  const stepLabel = $('#step-label');
  if (stepLabel) {
    stepLabel.textContent = String(formState.currentStep);
  }

  const percentLabel = $('#percent-label');
  if (percentLabel) {
    percentLabel.textContent = percent + '%';
  }

  const backButton = $('#btn-back');
  const nextButton = $('#btn-next');
  const submitButton = $('#btn-submit');
  if (backButton) backButton.classList.toggle('hidden', formState.currentStep === 1);
  if (nextButton) nextButton.classList.toggle('hidden', formState.currentStep === formState.totalSteps);
  if (submitButton) submitButton.classList.toggle('hidden', formState.currentStep !== formState.totalSteps);

  window.scrollTo({ top: 0, behavior: 'smooth' });

  if (formState.currentStep === formState.totalSteps) {
    renderSummary();
  }
}

function nextStep() {
  if (formState.currentStep < formState.totalSteps) {
    setStep(formState.currentStep + 1);
  }
}

function prevStep() {
  if (formState.currentStep > 1) {
    setStep(formState.currentStep - 1);
  }
}

/* --------------------------------------------------------------------------
   Checkbox / Radio Expand Logic
   -------------------------------------------------------------------------- */

function attachExpandListeners() {
  $all('[data-expands]').forEach(function (input) {
    input.addEventListener('change', function () {
      const target = document.getElementById(this.dataset.expands);
      if (!target) return;
      if (this.type === 'checkbox') {
        target.classList.toggle('hidden', !this.checked);
      }
    });
  });

  $all('input[name="diagnosed-mh"]').forEach(function (r) {
    r.addEventListener('change', function () {
      document.getElementById('diagnosed-expand').classList.toggle('hidden', this.value !== 'yes');
    });
  });

  $all('input[name="hospitalized"]').forEach(function (r) {
    r.addEventListener('change', function () {
      document.getElementById('hospitalized-expand').classList.toggle('hidden', this.value !== 'yes');
    });
  });
}

/* --------------------------------------------------------------------------
   Slider Display Updates
   -------------------------------------------------------------------------- */

function attachSliderListeners() {
  const healthScore = $('#health-score');
  if (healthScore) {
    healthScore.addEventListener('input', function () {
      const display = $('#health-score-display');
      if (display) display.textContent = this.value;
    });
  }

  $all('input[type="range"].concern-slider').forEach(function (slider) {
    slider.addEventListener('input', function () {
      const display = $('.concern-display[data-for="' + this.id + '"]');
      if (display) display.textContent = this.value;
    });
  });
}

/* --------------------------------------------------------------------------
   Summary Rendering (Step 5)
   -------------------------------------------------------------------------- */

function makeRow(label, value) {
  return '<div class="summary-row"><span class="label">' + label + '</span><span class="value">' + (value || 'Not provided') + '</span></div>';
}

function getVal(id) {
  const el = document.getElementById(id);
  return el ? el.value : '';
}

function isChecked(id) {
  const el = document.getElementById(id);
  return el ? el.checked : false;
}

function getRadioValue(name) {
  const checked = document.querySelector('input[name="' + name + '"]:checked');
  return checked ? checked.value : '';
}

function renderPatientSummary() {
  const container = $('#summary-patient');
  if (!container) return;
  let html = '';
  html += makeRow('Name', getVal('name'));
  html += makeRow('Age', getVal('age'));
  html += makeRow('Sex', getVal('sex'));
  html += makeRow('Chief Complaint', getVal('chief-complaint'));
  container.innerHTML = html;
}

function renderMedicalSummary() {
  const container = $('#summary-medical');
  if (!container) return;
  let html = '';

  const pmhFields = [
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
    { id: 'pmh-other', label: 'Other' }
  ];

  const checkedConditions = pmhFields.filter(function (f) { return isChecked(f.id); });

  html += '<div class="summary-row"><span class="label">Personal Medical History</span></div>';
  if (checkedConditions.length === 0) {
    html += makeRow('Conditions', 'None selected');
  } else {
    html += '<div class="badge-row">';
    checkedConditions.forEach(function (f) {
      html += '<span class="badge">' + f.label + '</span>';
    });
    html += '</div>';
  }

  const meds = getVal('current-medications');
  if (meds) {
    html += makeRow('Current Medications', meds);
  }

  container.innerHTML = html;
}

function renderPsychiatricSummary() {
  const container = $('#summary-psychiatric');
  if (!container) return;
  let html = '';

  const diagnosed = getRadioValue('diagnosed-mh');
  html += makeRow('Diagnosed with mental health condition', diagnosed || 'Not answered');
  if (diagnosed === 'yes') {
    html += makeRow('Diagnosis', getVal('diagnosis-specify'));
  }

  const hospitalized = getRadioValue('hospitalized');
  html += makeRow('Previous hospitalization', hospitalized || 'Not answered');

  container.innerHTML = html;
}

function renderLifestyleSummary() {
  const container = $('#summary-lifestyle');
  if (!container) return;
  let html = '';

  const healthScore = getVal('health-score');
  html += makeRow('Health Score', healthScore ? healthScore + '/10' : '');
  html += makeRow('Sleep Hours', getVal('sleep-hours'));
  html += makeRow('Exercise Frequency', getVal('exercise-freq'));
  html += makeRow('Motivation Level', getVal('motivation-level'));

  container.innerHTML = html;
}

function renderSummary() {
  renderPatientSummary();
  renderMedicalSummary();
  renderPsychiatricSummary();
  renderLifestyleSummary();
}

/* --------------------------------------------------------------------------
   Form Submit
   -------------------------------------------------------------------------- */

function submitForm() {
  alert('Thank you! Your information has been submitted successfully. Please wait to be called for your consultation.');
  window.location.href = '/';
}

/* --------------------------------------------------------------------------
   Initialization
   -------------------------------------------------------------------------- */

document.addEventListener('DOMContentLoaded', function () {
  $all('[data-go-step]').forEach(function (button) {
    button.addEventListener('click', function () {
      setStep(Number(this.dataset.goStep));
    });
  });

  $all('[data-action="next"]').forEach(function (button) {
    button.addEventListener('click', nextStep);
  });

  $all('[data-action="prev"]').forEach(function (button) {
    button.addEventListener('click', prevStep);
  });

  $all('[data-action="submit"]').forEach(function (button) {
    button.addEventListener('click', submitForm);
  });

  $all('.step-panel').forEach(function (panel) {
    panel.classList.add('hidden');
    panel.classList.remove('active');
  });
  attachExpandListeners();
  attachSliderListeners();
  setStep(4);
});

window.goToStep = setStep;
window.nextStep = nextStep;
window.prevStep = prevStep;
window.submitForm = submitForm;

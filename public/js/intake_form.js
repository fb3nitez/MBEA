/* ==========================================================================
   Patient Intake Form — Logic
   MedCare Integrated Psychiatric & Lifestyle Medicine Clinic
   ========================================================================== */

let currentStep = 1;
const TOTAL_STEPS = 5;

/* --------------------------------------------------------------------------
   Step Navigation
   -------------------------------------------------------------------------- */

function goToStep(step) {
  // Hide current step panel
  document.getElementById('step-' + currentStep).classList.remove('active');
  currentStep = step;
  // Show new step panel
  document.getElementById('step-' + currentStep).classList.add('active');
  // Update progress bar
  const pct = Math.round((currentStep / TOTAL_STEPS) * 100);
  document.getElementById('progress-fill').style.width = pct + '%';
  document.getElementById('step-label').textContent = 'Step ' + currentStep + ' of ' + TOTAL_STEPS;
  document.getElementById('percent-label').textContent = pct + '% Complete';
  // Update nav buttons
  document.getElementById('btn-back').classList.toggle('hidden', currentStep === 1);
  document.getElementById('btn-next').classList.toggle('hidden', currentStep === TOTAL_STEPS);
  document.getElementById('btn-submit').classList.toggle('hidden', currentStep !== TOTAL_STEPS);
  // Scroll to top
  window.scrollTo({ top: 0, behavior: 'smooth' });
  // If step 5, render summary
  if (currentStep === 5) renderSummary();
}

function nextStep() {
  if (currentStep < TOTAL_STEPS) goToStep(currentStep + 1);
}

function prevStep() {
  if (currentStep > 1) goToStep(currentStep - 1);
}

/* --------------------------------------------------------------------------
   Checkbox / Radio Expand Logic
   -------------------------------------------------------------------------- */

function attachExpandListeners() {
  document.querySelectorAll('[data-expands]').forEach(function (input) {
    input.addEventListener('change', function () {
      const target = document.getElementById(this.dataset.expands);
      if (!target) return;
      if (this.type === 'checkbox') {
        target.classList.toggle('hidden', !this.checked);
      }
    });
  });

  document.querySelectorAll('input[name="diagnosed-mh"]').forEach(function (r) {
    r.addEventListener('change', function () {
      document.getElementById('diagnosed-expand').classList.toggle('hidden', this.value !== 'yes');
    });
  });

  document.querySelectorAll('input[name="hospitalized"]').forEach(function (r) {
    r.addEventListener('change', function () {
      document.getElementById('hospitalized-expand').classList.toggle('hidden', this.value !== 'yes');
    });
  });
}

/* --------------------------------------------------------------------------
   Slider Display Updates
   -------------------------------------------------------------------------- */

function attachSliderListeners() {
  const healthScore = document.getElementById('health-score');
  if (healthScore) {
    healthScore.addEventListener('input', function () {
      document.getElementById('health-score-display').textContent = this.value;
    });
  }

  document.querySelectorAll('input[type="range"].concern-slider').forEach(function (slider) {
    slider.addEventListener('input', function () {
      const display = document.querySelector('.concern-display[data-for="' + this.id + '"]');
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
  const container = document.getElementById('summary-patient');
  let html = '';
  html += makeRow('Name', getVal('name'));
  html += makeRow('Age', getVal('age'));
  html += makeRow('Sex', getVal('sex'));
  html += makeRow('Chief Complaint', getVal('chief-complaint'));
  container.innerHTML = html;
}

function renderMedicalSummary() {
  const container = document.getElementById('summary-medical');
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
  const container = document.getElementById('summary-psychiatric');
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
  const container = document.getElementById('summary-lifestyle');
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
  attachExpandListeners();
  attachSliderListeners();
  goToStep(1); // initialize display state
});
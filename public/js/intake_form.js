/* ==========================================================================
   Patient Intake Form — Logic
   MedCare Integrated Psychiatric & Lifestyle Medicine Clinic
   ========================================================================== */

'use strict';

let currentStep = 1;
const TOTAL_STEPS = 5;

/* --------------------------------------------------------------------------
   VALIDATION RULES per step
   -------------------------------------------------------------------------- */
const STEP_VALIDATIONS = {
  1: [
    { id: 'name',            errId: 'err-name',           msg: 'Please enter your full name.',         check: v => v.trim().length >= 2 },
    { id: 'age',             errId: 'err-age',            msg: 'Please enter a valid age (1–120).',    check: v => v && parseInt(v) >= 1 && parseInt(v) <= 120 },
    { id: 'birthday',        errId: 'err-birthday',       msg: 'Please enter your date of birth.',     check: v => v.trim() !== '' },
    { id: 'sex',             errId: 'err-sex',            msg: 'Please select your sex.',              check: v => v !== '' },
    { id: 'chief-complaint', errId: 'err-chief-complaint',msg: 'Please describe your reason for visiting.', check: v => v.trim().length >= 5 },
  ],
  2: [], // Medical history — all optional checkboxes
  3: [], // Psychiatric history — sensitive, all optional
  4: [], // Lifestyle — all optional
};

/* --------------------------------------------------------------------------
   VALIDATION
   -------------------------------------------------------------------------- */
function validateStep(step) {
  const rules = STEP_VALIDATIONS[step] || [];
  let allValid = true;
  let firstError = null;

  rules.forEach(function(rule) {
    const el = document.getElementById(rule.id);
    const errEl = document.getElementById(rule.errId);
    if (!el) return;

    const val = el.tagName === 'SELECT' ? el.value : el.value;
    const valid = rule.check(val);

    if (!valid) {
      el.classList.add('invalid');
      if (errEl) {
        errEl.textContent = rule.msg;
        errEl.classList.remove('hidden');
      }
      if (!firstError) firstError = el;
      allValid = false;
    } else {
      el.classList.remove('invalid');
      if (errEl) errEl.classList.add('hidden');
    }
  });

  // Show/hide top banner
  const banner = document.getElementById('validation-banner');
  const bannerMsg = document.getElementById('validation-message');
  if (!allValid) {
    banner.classList.remove('hidden');
    bannerMsg.textContent = 'Please fill in all required fields before continuing.';
    // Scroll to first error
    if (firstError) {
      firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      firstError.focus();
    }
  } else {
    banner.classList.add('hidden');
  }

  return allValid;
}

// Clear validation state on input
function attachClearValidation() {
  ['name','age','birthday','sex','chief-complaint'].forEach(function(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('input', function() {
      this.classList.remove('invalid');
      const errId = 'err-' + id;
      const errEl = document.getElementById(errId);
      if (errEl) errEl.classList.add('hidden');
    });
    el.addEventListener('change', function() {
      this.classList.remove('invalid');
      const errId = 'err-' + id;
      const errEl = document.getElementById(errId);
      if (errEl) errEl.classList.add('hidden');
    });
  });
}

/* --------------------------------------------------------------------------
   STEP INDICATORS
   -------------------------------------------------------------------------- */
function updateStepIndicators(step) {
  document.querySelectorAll('.step-dot').forEach(function(dot) {
    const dotStep = parseInt(dot.getAttribute('data-step'));
    dot.classList.remove('active', 'completed');
    if (dotStep === step) dot.classList.add('active');
    else if (dotStep < step) dot.classList.add('completed');
  });

  document.querySelectorAll('.step-connector').forEach(function(conn, i) {
    if (i + 1 < step) conn.classList.add('done');
    else conn.classList.remove('done');
  });
}

/* --------------------------------------------------------------------------
   STEP NAVIGATION
   -------------------------------------------------------------------------- */
function goToStep(step) {
  // Hide current
  document.getElementById('step-' + currentStep).classList.remove('active');

  currentStep = step;

  // Show new
  document.getElementById('step-' + currentStep).classList.add('active');

  // Progress
  const pct = Math.round((currentStep / TOTAL_STEPS) * 100);
  document.getElementById('progress-fill').style.width = pct + '%';
  document.getElementById('step-label').textContent = 'Step ' + currentStep + ' of ' + TOTAL_STEPS;
  document.getElementById('percent-label').textContent = pct + '% Complete';

  // Nav buttons
  const btnBack   = document.getElementById('btn-back');
  const btnNext   = document.getElementById('btn-next');
  const btnSubmit = document.getElementById('btn-submit');
  btnBack.classList.toggle('hidden', currentStep === 1);
  btnNext.classList.toggle('hidden', currentStep === TOTAL_STEPS);
  btnSubmit.classList.toggle('hidden', currentStep !== TOTAL_STEPS);

  // Step indicators
  updateStepIndicators(currentStep);

  // Clear banner
  document.getElementById('validation-banner').classList.add('hidden');

  // If review step, render summary
  if (currentStep === TOTAL_STEPS) renderSummary();

  // Scroll to top smoothly
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function nextStep() {
  if (!validateStep(currentStep)) return;
  if (currentStep < TOTAL_STEPS) goToStep(currentStep + 1);
}

function prevStep() {
  if (currentStep > 1) goToStep(currentStep - 1);
}

/* --------------------------------------------------------------------------
   EXPAND / COLLAPSE LOGIC
   -------------------------------------------------------------------------- */
function attachExpandListeners() {
  // Generic data-expands checkboxes
  document.querySelectorAll('[data-expands]').forEach(function(input) {
    input.addEventListener('change', function() {
      const target = document.getElementById(this.dataset.expands);
      if (!target) return;
      if (this.checked) {
        target.classList.remove('hidden');
      } else {
        target.classList.add('hidden');
      }
    });
  });

  // Diagnosed mental health — radio
  document.querySelectorAll('input[name="diagnosed-mh"]').forEach(function(r) {
    r.addEventListener('change', function() {
      const ex = document.getElementById('diagnosed-expand');
      if (ex) ex.classList.toggle('hidden', this.value !== 'yes');
    });
  });

  // Hospitalized — radio
  document.querySelectorAll('input[name="hospitalized"]').forEach(function(r) {
    r.addEventListener('change', function() {
      const ex = document.getElementById('hospitalized-expand');
      if (ex) ex.classList.toggle('hidden', this.value !== 'yes');
    });
  });
}

/* --------------------------------------------------------------------------
   SLIDERS
   -------------------------------------------------------------------------- */
function attachSliderListeners() {
  // Health score
  const healthScore = document.getElementById('health-score');
  const healthDisplay = document.getElementById('health-score-display');
  if (healthScore && healthDisplay) {
    healthScore.addEventListener('input', function() {
      healthDisplay.textContent = this.value;
      updateSliderColor(this);
    });
    updateSliderColor(healthScore);
  }

  // Concern sliders
  document.querySelectorAll('input[type="range"].concern-slider').forEach(function(slider) {
    slider.addEventListener('input', function() {
      // Update inline concern-val span (id="val-{slider-id}")
      const valEl = document.getElementById('val-' + this.id);
      if (valEl) valEl.textContent = this.value;
    });
  });
}

function updateSliderColor(slider) {
  const val = ((slider.value - slider.min) / (slider.max - slider.min)) * 100;
  slider.style.background = `linear-gradient(90deg, #2563eb ${val}%, #e2e8f0 ${val}%)`;
}

/* --------------------------------------------------------------------------
   SUMMARY RENDERING
   -------------------------------------------------------------------------- */
function makeRow(label, value) {
  return '<div class="summary-row"><span class="label">' + label + '</span><span class="value">' + (value || '<em style="color:#94a3b8;">Not provided</em>') + '</span></div>';
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

function formatRadio(val) {
  const map = {
    'not-at-all':      'Not at all',
    'several-days':    'Several days',
    'more-than-half':  'More than half the days',
    'nearly-every-day':'Nearly every day',
    'yes': 'Yes', 'no': 'No',
  };
  return map[val] || val;
}

function renderPatientSummary() {
  const c = document.getElementById('summary-patient');
  if (!c) return;
  let h = '';
  h += makeRow('Full Name',        getVal('name'));
  h += makeRow('Age',              getVal('age'));
  h += makeRow('Date of Birth',    getVal('birthday'));
  h += makeRow('Sex',              getVal('sex'));
  h += makeRow('Gender Identity',  getVal('gender'));
  h += makeRow('Marital Status',   getVal('marital-status'));
  h += makeRow('Religion',         getVal('religion'));
  h += makeRow('Occupation',       getVal('occupation'));
  h += makeRow('Chief Complaint',  getVal('chief-complaint'));
  c.innerHTML = h;
}

function renderMedicalSummary() {
  const c = document.getElementById('summary-medical');
  if (!c) return;
  const pmhFields = [
    { id:'pmh-hypertension', label:'Hypertension' },
    { id:'pmh-stroke',       label:'Stroke or TIA' },
    { id:'pmh-tuberculosis', label:'Tuberculosis' },
    { id:'pmh-thyroid',      label:'Thyroid Disorders' },
    { id:'pmh-diabetes',     label:'Diabetes Mellitus' },
    { id:'pmh-chronic-pain', label:'Chronic Pain' },
    { id:'pmh-asthma',       label:'Bronchial Asthma' },
    { id:'pmh-epilepsy',     label:'Epilepsy/Seizures' },
    { id:'pmh-autoimmune',   label:'Autoimmune Disease' },
    { id:'pmh-cancer',       label:'Cancer' },
    { id:'pmh-other',        label:'Other' },
  ];
  const checked = pmhFields.filter(f => isChecked(f.id));
  let h = '';
  if (checked.length) {
    h += '<div class="badge-row">';
    checked.forEach(f => { h += '<span class="s-badge">' + f.label + '</span>'; });
    h += '</div>';
  } else {
    h += makeRow('Personal Medical History', 'None selected');
  }
  const meds = getVal('current-medications');
  if (meds.trim()) h += makeRow('Current Medications', meds);
  c.innerHTML = h;
}

function renderPsychiatricSummary() {
  const c = document.getElementById('summary-psychiatric');
  if (!c) return;
  let h = '';
  const diagnosed = getRadioValue('diagnosed-mh');
  h += makeRow('Diagnosed with mental health condition', formatRadio(diagnosed));
  if (diagnosed === 'yes') {
    h += makeRow('Diagnosis', getVal('diagnosis-specify'));
  }
  const hosp = getRadioValue('hospitalized');
  h += makeRow('Previous psychiatric hospitalization', formatRadio(hosp));
  if (hosp === 'yes') {
    h += makeRow('Number of times', getVal('hosp-times'));
    h += makeRow('When', getVal('hosp-when'));
  }

  const traumaTypes = [
    { id:'trauma-physical-check',  label:'Physical Abuse' },
    { id:'trauma-emotional-check', label:'Emotional Abuse' },
    { id:'trauma-sexual-check',    label:'Sexual Abuse' },
    { id:'trauma-neglect-check',   label:'Neglect' },
  ];
  const checkedTrauma = traumaTypes.filter(t => isChecked(t.id));
  if (checkedTrauma.length) {
    h += '<div class="badge-row">';
    checkedTrauma.forEach(t => { h += '<span class="s-badge" style="background:#fee2e2;color:#991b1b;">' + t.label + '</span>'; });
    h += '</div>';
  }
  c.innerHTML = h;
}

function renderLifestyleSummary() {
  const c = document.getElementById('summary-lifestyle');
  if (!c) return;
  let h = '';
  h += makeRow('Overall Health Score', getVal('health-score') + '/10');
  h += makeRow('Sleep (hrs/night)',    getVal('sleep-hours'));
  h += makeRow('Fatigue frequency',   getVal('tired-frequency'));
  h += makeRow('Exercise per week',   getVal('exercise-freq'));
  h += makeRow('Fast food frequency', getVal('fast-food'));
  h += makeRow('Motivation level',    getVal('motivation-level'));

  // PHQ snippet
  const phqThoughtsHurting = getRadioValue('phq-thoughts-hurting');
  if (phqThoughtsHurting && phqThoughtsHurting !== 'not-at-all') {
    h += makeRow('⚠ Thoughts of self-harm', formatRadio(phqThoughtsHurting));
  }

  // Substances
  const subs = ['nicotine','alcohol','recreational','marijuana','screentime','gambling','others'];
  const usedSubs = subs.filter(s => isChecked('sub-' + s));
  if (usedSubs.length) {
    h += '<div class="badge-row">';
    usedSubs.forEach(s => {
      const labels = { nicotine:'Nicotine', alcohol:'Alcohol', recreational:'Rec. Drugs', marijuana:'Marijuana', screentime:'Screen Time', gambling:'Gambling', others:'Other' };
      h += '<span class="s-badge">' + labels[s] + '</span>';
    });
    h += '</div>';
  }
  c.innerHTML = h;
}

function renderSummary() {
  renderPatientSummary();
  renderMedicalSummary();
  renderPsychiatricSummary();
  renderLifestyleSummary();
}

/* --------------------------------------------------------------------------
   SUBMIT
   -------------------------------------------------------------------------- */
function submitForm() {
  // Show success overlay instead of alert
  const overlay = document.getElementById('success-overlay');
  if (overlay) overlay.classList.remove('hidden');
}

/* --------------------------------------------------------------------------
   INIT
   -------------------------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', function() {
  attachExpandListeners();
  attachSliderListeners();
  attachClearValidation();
  goToStep(1);
});
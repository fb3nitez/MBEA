/* psychiatrist_patients.js — Patients table, patient detail modal, saves */

/* ============================================================
   BUILD PATIENTS TABLE
============================================================ */
function buildPatients(list) {
  var tbody = document.getElementById('patients-tbody');
  if (!tbody) return;
  tbody.innerHTML = '';
  if (!list.length) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#64748b;">No patients found.</td></tr>';
    return;
  }
  list.forEach(function (p) {
    var tr = document.createElement('tr');
    tr.innerHTML = '<td class="td-id">' + (p.patient_id || p.id) + '</td>' +
      '<td class="td-name">' + (p.name || '—') + '</td>' +
      '<td>' + (p.age != null ? p.age : '—') + '</td>' +
      '<td>' + (p.coach || 'Unassigned') + '</td>' +
      '<td class="td-complaint">' + (p.complaint || p.chief_complaint || '—') + '</td>' +
      '<td><button class="btn-outline-sm" onclick="viewPatient(' + p.id + ')"><i data-feather="eye" style="width:12px;height:12px;vertical-align:middle;margin-right:4px;"></i>View</button></td>';
    tbody.appendChild(tr);
  });
  ri();
}

function switchPmTab(tab) {
  qsa('#pm-tabs .tab-btn').forEach(function (btn) {
    btn.classList.toggle('active', btn.getAttribute('data-pm-tab') === tab);
  });
  qsa('.pm-tab-panel').forEach(function (panel) {
    panel.classList.toggle('active', panel.getAttribute('data-pm-panel') === tab);
  });
}

function populatePatientModal(p) {
  CURRENT_PATIENT = p;
  var initials = (p.name || '?').split(' ').map(function (w) { return w[0]; }).join('').slice(0, 2).toUpperCase();
  var avatarEl = document.getElementById('pm-avatar');
  if (avatarEl) avatarEl.textContent = initials;

  var setText = function (id, val) { var el = document.getElementById(id); if (el) el.textContent = val == null || val === '' ? '—' : val; };
  setText('pm-name', p.name);
  setText('pm-sub', p.patient_id || p.id);
  setText('pm-id', p.patient_id || p.id);
  setText('pm-age', p.age);
  setText('pm-sex', p.sex);
  setText('pm-coach', p.coach || 'Unassigned');
  setText('pm-complaint', p.complaint || p.chief_complaint);

  // Patient record form
  setVal('pr-fullname', p.name);
  setVal('pr-birthday', p.birthday);
  setVal('pr-sex', (p.sex || 'female').toLowerCase());
  setVal('pr-gender', p.gender);
  setVal('pr-marital', p.marital_status || 'single');
  setVal('pr-religion', p.religion);
  setVal('pr-year', p.student_year_level);
  setVal('pr-course', p.course);
  setVal('pr-occupation', p.occupation);
  setVal('pr-complaint', p.chief_complaint || p.complaint);
  setVal('pr-diagnosis', p.primary_diagnosis);
  setVal('pm-coach-select', p.life_coach_id || '');

  // Medical history
  var mh = p.medical_history || {};
  qsa('.mh-check').forEach(function (cb) {
    var field = cb.getAttribute('data-field');
    cb.checked = !!mh[field];
  });
  setVal('mh-autoimmune_specify', mh.autoimmune_specify);
  setVal('mh-cancer_specify', mh.cancer_specify);
  setVal('mh-other_medical_specify', mh.other_medical_specify);
  setVal('mh-current_medications', mh.current_medications);
  setVal('mh-family_cancer_type', mh.family_cancer_type);
  setVal('mh-family_cancer_relation', mh.family_cancer_relation);
  setVal('mh-family_psychiatric_relation', mh.family_psychiatric_relation);
  setVal('mh-family_other_specify', mh.family_other_specify);

  // Psychiatric history
  var ph = p.psychiatric_history || {};
  setChecked('ph-diagnosed_mental_condition', ph.diagnosed_mental_condition);
  setChecked('ph-psychiatric_hospitalized', ph.psychiatric_hospitalized);
  setVal('ph-mental_condition', ph.mental_condition);
  setVal('ph-hospitalization_count', ph.hospitalization_count);
  setVal('ph-hospitalization_when', ph.hospitalization_when);
  ['physical', 'emotional', 'sexual', 'neglect'].forEach(function (key) {
    var mainId = key === 'neglect' ? 'ph-neglect' : ('ph-' + key + '_abuse');
    // IDs in blade: ph-physical_abuse, ph-emotional_abuse, ph-sexual_abuse, ph-neglect_abuse
    setChecked('ph-' + key + '_abuse', ph[key === 'neglect' ? 'neglect' : (key + '_abuse')]);
    setChecked('ph-' + key + '_child', ph[key + '_child']);
    setChecked('ph-' + key + '_adult', ph[key + '_adult']);
    setChecked('ph-' + key + '_ongoing', ph[key + '_ongoing']);
    setChecked('ph-' + key + '_past', ph[key + '_past']);
    setVal('ph-' + key + '_notes', ph[key + '_notes']);
  });
  // fix neglect main flag (field is "neglect" not "neglect_abuse")
  setChecked('ph-neglect_abuse', ph.neglect);

  // Lifestyle
  var ls = p.lifestyle_assessment || {};
  toggleExpandableInputs();
  ['health_score', 'sleep_hours', 'tired_frequency', 'weight_perception', 'fast_food_frequency',
    'fruits_veg_servings', 'exercise_frequency', 'motivation_level', 'lifestyle_motivation'].forEach(function (f) {
      setVal('ls-' + f, ls[f]);
    });
  qsa('.ls-phq-radio').forEach(function (rb) {
    rb.checked = (ls[rb.getAttribute('data-field')] || '') === rb.value;
  });
  qsa('.ls-sub-check').forEach(function (cb) {
    cb.checked = !!ls[cb.getAttribute('data-field')];
    var block = document.querySelector('.pm-substance-block[data-substance="' + cb.getAttribute('data-field') + '"]');
    if (block) block.style.display = cb.checked ? 'block' : 'none';
  });
  ['sub_nicotine_amount', 'sub_nicotine_concern', 'sub_alcohol_amount', 'sub_alcohol_concern', 'sub_recreational_amount', 'sub_recreational_concern', 'sub_marijuana_amount', 'sub_marijuana_concern', 'sub_screentime_amount', 'sub_screentime_concern', 'sub_gambling_amount', 'sub_gambling_concern', 'sub_others_specify', 'sub_others_concern'].forEach(function (field) {
    var el = document.getElementById('ls-' + field);
    if (el) {
      el.value = ls[field] == null ? '' : ls[field];
    }
  });
  setVal('ls-motivation_level', ls.motivation_level);

  var modal = document.getElementById('patient-detail-modal');
  if (modal) modal.setAttribute('data-current-patient', p.id);

  if (window.displayAsHTML) window.displayAsHTML(p.clinical_notes);

  switchPmTab('overview');
  openModal('patient-detail-modal');
  ri();
}

window.viewPatient = function (id) {
  var local = findPatientLocal(id);
  if (local && local.medical_history !== undefined) {
    populatePatientModal(local);
  }

  var base = (window.PSYCH_ROUTES || {}).patientsShow || '/psychiatrist/patients';
  apiFetch(base + '/' + id)
    .then(function (data) {
      upsertPatientLocal(data.patient);
      populatePatientModal(data.patient);
    })
    .catch(function (err) {
      if (!local) showToast(err.message || 'Unable to load patient.');
    });
};

// Patient modal tabs
document.addEventListener('click', function (e) {
  var btn = e.target.closest('[data-pm-tab]');
  if (!btn) return;
  switchPmTab(btn.getAttribute('data-pm-tab'));
});

document.addEventListener('change', function (e) {
  if (e.target && e.target.hasAttribute('data-expands')) {
    toggleExpandableInputs();
  }
  if (e.target && e.target.classList.contains('ls-sub-check')) {
    var block = document.querySelector('.pm-substance-block[data-substance="' + e.target.getAttribute('data-field') + '"]');
    if (block) block.style.display = e.target.checked ? 'block' : 'none';
  }
});

// Patient modal quick-action buttons
var pmBtnConsult = document.getElementById('pm-btn-consult');
if (pmBtnConsult) pmBtnConsult.addEventListener('click', function () {
  var modal = document.getElementById('patient-detail-modal');
  var id = modal ? modal.getAttribute('data-current-patient') : null;
  closeModal('patient-detail-modal');
  populateConsultPatientSelect(id);
  openModal('schedule-consult-modal');
});

var pmBtnRecord = document.getElementById('pm-btn-record');
if (pmBtnRecord) pmBtnRecord.addEventListener('click', function () {
  var modal = document.getElementById('patient-detail-modal');
  var id = modal ? modal.getAttribute('data-current-patient') : null;
  closeModal('patient-detail-modal');
  if (id) openRecord(id);
});

var pmBtnRx = document.getElementById('pm-btn-rx');
if (pmBtnRx) pmBtnRx.addEventListener('click', function () {
  var modal = document.getElementById('patient-detail-modal');
  var id = modal ? modal.getAttribute('data-current-patient') : null;
  var p = id ? findPatientLocal(id) : null;
  closeModal('patient-detail-modal');
  goToPage('prescriptions', id ? ('patient=' + encodeURIComponent(p ? (p.patient_id || p.id) : id)) : null);
});

var pmBtnAssess = document.getElementById('pm-btn-assess');
if (pmBtnAssess) pmBtnAssess.addEventListener('click', function () {
  var modal = document.getElementById('patient-detail-modal');
  var id = modal ? modal.getAttribute('data-current-patient') : null;
  var p = id ? findPatientLocal(id) : null;
  closeModal('patient-detail-modal');
  goToPage('assessments', id ? ('patient=' + encodeURIComponent(id)) : null);
});

function toggleExpandableInputs() {
  qsa('[data-expands]').forEach(function (input) {
    var targetId = input.getAttribute('data-expands');
    if (!targetId) return;
    var target = document.getElementById(targetId);
    if (!target) return;
    var isChecked = input.type === 'checkbox' ? input.checked : (input.value === 'yes');
    target.classList.toggle('hidden', !isChecked);
  });
}

function collectMedicalHistory() {
  var data = {};
  qsa('.mh-check').forEach(function (cb) {
    data[cb.getAttribute('data-field')] = cb.checked;
  });
  data.autoimmune_specify = getVal('mh-autoimmune_specify');
  data.cancer_specify = getVal('mh-cancer_specify');
  data.other_medical_specify = getVal('mh-other_medical_specify');
  data.current_medications = getVal('mh-current_medications');
  data.family_cancer_type = getVal('mh-family_cancer_type');
  data.family_cancer_relation = getVal('mh-family_cancer_relation');
  data.family_psychiatric_relation = getVal('mh-family_psychiatric_relation');
  data.family_other_specify = getVal('mh-family_other_specify');
  return data;
}

function collectPsychiatricHistory() {
  var data = {
    diagnosed_mental_condition: !!qs('#ph-diagnosed_mental_condition') && qs('#ph-diagnosed_mental_condition').checked,
    psychiatric_hospitalized: !!qs('#ph-psychiatric_hospitalized') && qs('#ph-psychiatric_hospitalized').checked,
    mental_condition: getVal('ph-mental_condition'),
    hospitalization_count: getVal('ph-hospitalization_count'),
    hospitalization_when: getVal('ph-hospitalization_when'),
  };
  ['physical', 'emotional', 'sexual', 'neglect'].forEach(function (key) {
    var mainField = key === 'neglect' ? 'neglect' : (key + '_abuse');
    data[mainField] = !!qs('#ph-' + key + '_abuse') && qs('#ph-' + key + '_abuse').checked;
    data[key + '_child'] = !!qs('#ph-' + key + '_child') && qs('#ph-' + key + '_child').checked;
    data[key + '_adult'] = !!qs('#ph-' + key + '_adult') && qs('#ph-' + key + '_adult').checked;
    data[key + '_ongoing'] = !!qs('#ph-' + key + '_ongoing') && qs('#ph-' + key + '_ongoing').checked;
    data[key + '_past'] = !!qs('#ph-' + key + '_past') && qs('#ph-' + key + '_past').checked;
    data[key + '_notes'] = getVal('ph-' + key + '_notes');
  });
  return data;
}

function collectLifestyle() {
  var data = {};
  ['health_score', 'sleep_hours', 'tired_frequency', 'weight_perception', 'fast_food_frequency',
    'fruits_veg_servings', 'exercise_frequency', 'motivation_level', 'lifestyle_motivation'].forEach(function (f) {
      data[f] = getVal('ls-' + f);
    });
  qsa('.ls-phq-radio').forEach(function (rb) {
    if (rb.checked) {
      data[rb.getAttribute('data-field')] = rb.value;
    }
  });
  qsa('.ls-sub-check').forEach(function (cb) {
    data[cb.getAttribute('data-field')] = cb.checked;
  });
  ['sub_nicotine_amount', 'sub_nicotine_concern', 'sub_alcohol_amount', 'sub_alcohol_concern', 'sub_recreational_amount', 'sub_recreational_concern', 'sub_marijuana_amount', 'sub_marijuana_concern', 'sub_screentime_amount', 'sub_screentime_concern', 'sub_gambling_amount', 'sub_gambling_concern', 'sub_others_specify', 'sub_others_concern'].forEach(function (field) {
    var el = document.getElementById('ls-' + field);
    data[field] = el ? el.value : '';
  });
  data.motivation_level = getVal('ls-motivation_level');
  return data;
}

function currentPatientId() {
  var modal = document.getElementById('patient-detail-modal');
  return modal ? modal.getAttribute('data-current-patient') : null;
}

function bindPatientSaves() {
  var base = (window.PSYCH_ROUTES || {}).patientsUpdate || '/psychiatrist/patients';

  var saveRecord = document.getElementById('pm-save-record');
  if (saveRecord) saveRecord.addEventListener('click', function () {
    var id = currentPatientId();
    if (!id) return;
    apiFetch(base + '/' + id, {
      method: 'PUT',
      body: JSON.stringify({
        name: getVal('pr-fullname'),
        birthday: getVal('pr-birthday') || null,
        sex: getVal('pr-sex'),
        gender: getVal('pr-gender') || null,
        marital_status: getVal('pr-marital'),
        religion: getVal('pr-religion') || null,
        student_year_level: getVal('pr-year') || null,
        course: getVal('pr-course') || null,
        occupation: getVal('pr-occupation') || null,
        chief_complaint: getVal('pr-complaint'),
        primary_diagnosis: getVal('pr-diagnosis') || null,
      }),
    }).then(function (data) {
      upsertPatientLocal(data.patient);
      populatePatientModal(data.patient);
      buildPatients(PATIENTS);
      showToast(data.message || 'Patient record updated.');
    }).catch(function (err) { showToast(err.message); });
  });

  var saveMedical = document.getElementById('pm-save-medical');
  if (saveMedical) saveMedical.addEventListener('click', function () {
    var id = currentPatientId();
    if (!id) return;
    apiFetch(base + '/' + id + '/medical-history', {
      method: 'PUT',
      body: JSON.stringify(collectMedicalHistory()),
    }).then(function (data) {
      if (CURRENT_PATIENT) CURRENT_PATIENT.medical_history = data.medical_history;
      showToast(data.message || 'Medical history updated.');
    }).catch(function (err) { showToast(err.message); });
  });

  var savePsych = document.getElementById('pm-save-psychiatric');
  if (savePsych) savePsych.addEventListener('click', function () {
    var id = currentPatientId();
    if (!id) return;
    apiFetch(base + '/' + id + '/psychiatric-history', {
      method: 'PUT',
      body: JSON.stringify(collectPsychiatricHistory()),
    }).then(function (data) {
      if (CURRENT_PATIENT) CURRENT_PATIENT.psychiatric_history = data.psychiatric_history;
      showToast(data.message || 'Personal history updated.');
    }).catch(function (err) { showToast(err.message); });
  });

  var saveLifestyle = document.getElementById('pm-save-lifestyle');
  if (saveLifestyle) saveLifestyle.addEventListener('click', function () {
    var id = currentPatientId();
    if (!id) return;
    apiFetch(base + '/' + id + '/lifestyle', {
      method: 'PUT',
      body: JSON.stringify(collectLifestyle()),
    }).then(function (data) {
      if (CURRENT_PATIENT) CURRENT_PATIENT.lifestyle_assessment = data.lifestyle_assessment;
      var lsIdx = LIFESTYLE_PATIENTS.findIndex(function (p) { return String(p.id) === String(id); });
      var entry = {
        id: Number(id),
        patient_id: CURRENT_PATIENT ? CURRENT_PATIENT.patient_id : null,
        name: CURRENT_PATIENT ? CURRENT_PATIENT.name : 'Patient',
        age: CURRENT_PATIENT ? CURRENT_PATIENT.age : null,
        sex: CURRENT_PATIENT ? CURRENT_PATIENT.sex : null,
        lifestyle_assessment: data.lifestyle_assessment,
      };
      if (lsIdx >= 0) LIFESTYLE_PATIENTS[lsIdx] = Object.assign({}, LIFESTYLE_PATIENTS[lsIdx], entry);
      else LIFESTYLE_PATIENTS.push(entry);
      filterLifestyle();
      showToast(data.message || 'Lifestyle assessment updated.');
    }).catch(function (err) { showToast(err.message); });
  });

  var saveCoach = document.getElementById('pm-save-coach');
  if (saveCoach) saveCoach.addEventListener('click', function () {
    var id = currentPatientId();
    if (!id) return;
    var coachId = getVal('pm-coach-select') || null;
    apiFetch(base + '/' + id, {
      method: 'PUT',
      body: JSON.stringify({ life_coach_id: coachId }),
    }).then(function (data) {
      upsertPatientLocal(data.patient);
      populatePatientModal(data.patient);
      buildPatients(PATIENTS);
      showToast('Life coach assignment saved.');
    }).catch(function (err) { showToast(err.message); });
  });
}

// Patient search
function filterPatients() {
  var searchEl = document.getElementById('patient-search');
  if (!searchEl) return;
  var q = (searchEl.value || '').toLowerCase();
  var list = PATIENTS.filter(function (p) {
    var pid = String(p.patient_id || p.id || '').toLowerCase();
    return !q || (p.name || '').toLowerCase().includes(q) || pid.includes(q);
  });
  buildPatients(list);
}

var psearch = document.getElementById('patient-search');
if (psearch) psearch.addEventListener('input', filterPatients);

consultPatientPicker = createPatientPicker({
  searchId: 'consult-patient-search',
  hiddenId: 'consult-patient',
  dropdownId: 'consult-patient-dropdown',
});
rxPatientPicker = createPatientPicker({
  searchId: 'rx-patient-search',
  hiddenId: 'rx-patient',
  dropdownId: 'rx-patient-dropdown',
  ageId: 'rx-age',
});
dxPatientPicker = createPatientPicker({
  searchId: 'dx-patient-search',
  hiddenId: 'dx-patient',
  dropdownId: 'dx-patient-dropdown',
  ageId: 'dx-age',
});


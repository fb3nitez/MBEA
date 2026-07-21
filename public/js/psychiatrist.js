/* ============================================================
   MEDCARE PSYCHIATRIST DASHBOARD — psychiatrist_dashboard.js
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  /* ============================================================
     DATA (from server via window.PSYCH_DATA)
  ============================================================ */

  var DATA = window.PSYCH_DATA || {};
  var PATIENT_SUGGESTIONS = Array.isArray(DATA.patientSuggestions) ? DATA.patientSuggestions.slice() : [];
  var PATIENTS = Array.isArray(DATA.allPatients) ? DATA.allPatients.slice()
    : (Array.isArray(DATA.patients) ? DATA.patients.slice() : PATIENT_SUGGESTIONS.slice());
  var ASSESSMENT_PATIENTS = Array.isArray(DATA.assessmentPatients) ? DATA.assessmentPatients.slice() : PATIENTS;
  var PRESCRIPTION_PATIENTS = Array.isArray(DATA.prescriptionPatients) ? DATA.prescriptionPatients.slice() : PATIENTS;
  var CONSULTATIONS = Array.isArray(DATA.consultations) ? DATA.consultations.slice() : [];
  var RECORDS = Array.isArray(DATA.records) ? DATA.records.slice() : [];
  var LIFE_COACHES = Array.isArray(DATA.lifeCoaches) ? DATA.lifeCoaches.slice() : [];
  var LIFESTYLE_PATIENTS = Array.isArray(DATA.lifestyle) ? DATA.lifestyle.slice() : [];
  var RX_TEMPLATES = Array.isArray(DATA.rxTemplates) ? DATA.rxTemplates.slice() : [];
  var DX_TEMPLATES = Array.isArray(DATA.dxTemplates) ? DATA.dxTemplates.slice() : [];
  var CURRENT_PATIENT = null;

  var ASSESSMENTS = [
    { id: 'P001', name: 'Sarah Johnson', age: '34y', sex: 'Female', diag: 'Major Depressive Disorder (F32.1)', status: 'Stable', prog: 85, lastDate: '2026-05-28', tag: 'Cognitive Behavioral Coaching', provider: 'Dr. Maria Santos · Psychiatrist' },
    { id: 'P002', name: 'David Martinez', age: '42y', sex: 'Male', diag: 'Generalized Anxiety Disorder (F41.1)', status: 'Maintenance', prog: 70, lastDate: '2026-05-20', tag: 'Stress Management', provider: 'Emily Roberts · Life Coach' },
    { id: 'P003', name: 'Emily Thompson', age: '28y', sex: 'Female', diag: 'Panic Disorder with Agoraphobia (F40.01)', status: 'Critical', prog: 60, lastDate: '2026-06-08', tag: null, provider: 'Dr. Maria Santos · Psychiatrist' },
    { id: 'P004', name: 'James Wilson', age: '55y', sex: 'Male', diag: 'Insomnia Disorder (G47.00)', status: 'Life Coaching', prog: 50, lastDate: '2026-05-15', tag: 'Sleep Wellness & Lifestyle Medicine', provider: 'Michael Chen · Life Coach' },
  ];

  var DX_LAB_GROUPS = [
    { cat: 'Hematology', tests: ['CBC with differential', 'ESR', 'CRP'] },
    { cat: 'Urinalysis', tests: ['Complete urinalysis', 'Urine culture'] },
    { cat: 'Thyroid', tests: ['TSH', 'Free T3', 'Free T4', 'Anti-TPO'] },
    { cat: 'Cardiac', tests: ['ECG', '2D Echo'] },
    { cat: 'Liver', tests: ['AST', 'ALT', 'Total Bilirubin', 'Alkaline Phosphatase', 'GGT'] },
    { cat: 'Renal', tests: ['BUN', 'Creatinine', 'eGFR', 'Uric Acid'] },
    { cat: 'Metabolic', tests: ['Fasting Blood Sugar', 'HbA1c', 'Lipid Profile'] },
    { cat: 'Electrolytes', tests: ['Sodium', 'Potassium', 'Chloride', 'Calcium', 'Magnesium'] },
  ];

  var DIAGNOSES = [
    'F41.1 Generalized Anxiety Disorder',
    'F32.1 Major Depressive Disorder',
    'F31.0 Bipolar I Disorder',
    'F43.1 Post-Traumatic Stress Disorder',
    'G47.0 Insomnia',
    'F20.9 Schizophrenia',
    'F40.01 Panic Disorder with Agoraphobia',
    'F33.0 Recurrent Depressive Disorder',
    'F60.3 Borderline Personality Disorder',
    'F90.0 ADHD, predominantly inattentive',
  ];

  var MED_SUGGESTIONS = [
    'Sertraline', 'Fluoxetine', 'Escitalopram', 'Paroxetine', 'Venlafaxine',
    'Duloxetine', 'Bupropion', 'Mirtazapine', 'Quetiapine', 'Olanzapine',
    'Risperidone', 'Aripiprazole', 'Haloperidol', 'Clonazepam', 'Alprazolam',
    'Lorazepam', 'Diazepam', 'Zolpidem', 'Melatonin', 'Lithium',
    'Valproic Acid', 'Carbamazepine', 'Lamotrigine', 'Methylphenidate',
    'Atomoxetine', 'Clozapine', 'Paliperidone', 'Lurasidone', 'Trazodone', 'Biperiden',
  ];

  var DOSAGES = ['10mg', '25mg', '50mg', '100mg', '150mg', '200mg', '250mg', '300mg', '500mg', 'Custom'];

  /* ============================================================
     HELPERS
  ============================================================ */
  function ri() { if (window.feather) window.feather.replace(); }
  function qs(sel, ctx) { return (ctx || document).querySelector(sel); }
  function qsa(sel, ctx) { return (ctx || document).querySelectorAll(sel); }
  function show(el) { if (el) el.classList.remove('hidden'); }
  function hide(el) { if (el) el.classList.add('hidden'); }

  function csrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }

  function apiFetch(url, options) {
    options = options || {};
    options.headers = Object.assign({
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
    }, options.headers || {});
    return fetch(url, options).then(function (res) {
      return res.json().then(function (body) {
        if (!res.ok) {
          var msg = (body && body.message) || 'Request failed.';
          if (body && body.errors) {
            msg = Object.values(body.errors).flat().join(' ');
          }
          throw new Error(msg);
        }
        return body;
      });
    });
  }

  function now() {
    var d = new Date();
    var h = d.getHours(); var m = d.getMinutes();
    var ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    return h + ':' + (m < 10 ? '0' + m : m) + ' ' + ampm;
  }

  function today() {
    var d = new Date();
    return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
  }

  window.showToast = function (msg, duration) {
    var t = document.getElementById('toast');
    if (!t) return;
    t.textContent = msg;
    show(t);
    clearTimeout(t._timer);
    t._timer = setTimeout(function () { hide(t); }, duration || 2800);
  };

  function statusBadge(s) {
    var map = { Active: 'badge-active', Critical: 'badge-critical', Inactive: 'badge-inactive', Pending: 'badge-pending', Submitted: 'badge-pending', Completed: 'badge-completed', Scheduled: 'badge-scheduled', Emergency: 'badge-emergency', Stable: 'badge-stable', Monitoring: 'badge-monitoring', Maintenance: 'badge-maintenance', Cancelled: 'badge-inactive' };
    return '<span class="badge ' + (map[s] || 'badge-outline') + '">' + s + '</span>';
  }

  function setVal(id, val) {
    var el = document.getElementById(id);
    if (el) el.value = val == null ? '' : val;
  }

  function getVal(id) {
    var el = document.getElementById(id);
    return el ? el.value : '';
  }

  function setChecked(id, val) {
    var el = document.getElementById(id);
    if (el) el.checked = !!val;
  }

  function findPatientLocal(id) {
    id = String(id);
    return PATIENTS.find(function (p) {
      return String(p.id) === id || String(p.patient_id) === id;
    }) || PATIENT_SUGGESTIONS.find(function (p) {
      return String(p.id) === id || String(p.patient_id) === id;
    }) || LIFESTYLE_PATIENTS.find(function (p) {
      return String(p.id) === id || String(p.patient_id) === id;
    }) || PRESCRIPTION_PATIENTS.find(function (p) {
      return String(p.id) === id || String(p.patient_id) === id;
    });
  }

  function upsertPatientLocal(patient) {
    var idx = PATIENTS.findIndex(function (p) { return String(p.id) === String(patient.id); });
    if (idx >= 0) PATIENTS[idx] = Object.assign({}, PATIENTS[idx], patient);
    else PATIENTS.push(patient);

    var sIdx = PATIENT_SUGGESTIONS.findIndex(function (p) { return String(p.id) === String(patient.id); });
    var suggestion = {
      id: patient.id,
      patient_id: patient.patient_id,
      name: patient.name,
      age: patient.age,
      sex: patient.sex,
    };
    if (sIdx >= 0) PATIENT_SUGGESTIONS[sIdx] = Object.assign({}, PATIENT_SUGGESTIONS[sIdx], suggestion);
    else PATIENT_SUGGESTIONS.unshift(suggestion);
  }

  function patientLabel(p) {
    return (p.name || 'Patient') + (p.patient_id ? ' (' + p.patient_id + ')' : '');
  }

  function searchPatientsApi(query) {
    var routes = window.PSYCH_ROUTES || {};
    var url = (routes.patientsSearch || '/psychiatrist/patients/search') + '?limit=12';
    if (query) url += '&q=' + encodeURIComponent(query);
    return apiFetch(url).then(function (data) {
      return Array.isArray(data.patients) ? data.patients : [];
    });
  }

  function createPatientPicker(opts) {
    var searchEl = document.getElementById(opts.searchId);
    var hiddenEl = document.getElementById(opts.hiddenId);
    var dropdownEl = document.getElementById(opts.dropdownId);
    if (!searchEl || !hiddenEl || !dropdownEl) return null;

    var timer = null;
    var requestSeq = 0;

    function setSelected(patient) {
      if (!patient) {
        searchEl.value = '';
        hiddenEl.value = '';
        if (opts.ageId) setVal(opts.ageId, '');
        return;
      }
      upsertPatientLocal(patient);
      searchEl.value = patientLabel(patient);
      hiddenEl.value = patient.id;
      if (opts.ageId && patient.age != null) setVal(opts.ageId, patient.age);
      if (typeof opts.onSelect === 'function') opts.onSelect(patient);
    }

    function renderMatches(matches, query) {
      dropdownEl.innerHTML = '';
      if (!matches.length) {
        if (query) {
          var empty = document.createElement('div');
          empty.className = 'typeahead-item';
          empty.style.color = '#64748b';
          empty.textContent = 'No patients found';
          dropdownEl.appendChild(empty);
          show(dropdownEl);
        } else {
          hide(dropdownEl);
        }
        return;
      }

      if (!query) {
        var hint = document.createElement('div');
        hint.className = 'typeahead-item';
        hint.style.color = '#64748b';
        hint.style.fontSize = '12px';
        hint.style.cursor = 'default';
        hint.textContent = 'Recent suggestions — type to search all patients';
        dropdownEl.appendChild(hint);
      }

      matches.forEach(function (p) {
        var item = document.createElement('div');
        item.className = 'typeahead-item';
        item.textContent = patientLabel(p);
        item.addEventListener('click', function () {
          setSelected(p);
          hide(dropdownEl);
        });
        dropdownEl.appendChild(item);
      });
      show(dropdownEl);
    }

    function runSearch(query) {
      var q = (query || '').trim();
      var seq = ++requestSeq;

      if (!q && PATIENT_SUGGESTIONS.length) {
        renderMatches(PATIENT_SUGGESTIONS.slice(0, 12), '');
        return;
      }

      searchPatientsApi(q).then(function (matches) {
        if (seq !== requestSeq) return;
        matches.forEach(upsertPatientLocal);
        if (!q && matches.length) {
          PATIENT_SUGGESTIONS = matches.slice();
        }
        renderMatches(matches, q);
      }).catch(function () {
        if (seq !== requestSeq) return;
        var local = (q ? PATIENTS : PATIENT_SUGGESTIONS).filter(function (p) {
          if (!q) return true;
          var name = (p.name || '').toLowerCase();
          var pid = String(p.patient_id || '').toLowerCase();
          var needle = q.toLowerCase();
          return name.includes(needle) || pid.includes(needle);
        }).slice(0, 12);
        renderMatches(local, q);
      });
    }

    searchEl.addEventListener('input', function () {
      hiddenEl.value = '';
      clearTimeout(timer);
      timer = setTimeout(function () { runSearch(searchEl.value); }, 220);
    });
    searchEl.addEventListener('focus', function () {
      runSearch(searchEl.value);
    });
    document.addEventListener('click', function (e) {
      if (!dropdownEl.contains(e.target) && e.target !== searchEl) hide(dropdownEl);
    });

    return {
      setSelected: setSelected,
      clear: function () { setSelected(null); hide(dropdownEl); },
      getId: function () { return hiddenEl.value; },
    };
  }

  var consultPatientPicker = null;
  var rxPatientPicker = null;
  var dxPatientPicker = null;

  function populateConsultPatientSelect(selectedId) {
    if (!consultPatientPicker) return;
    if (selectedId) {
      var selected = findPatientLocal(selectedId);
      if (selected) {
        consultPatientPicker.setSelected(selected);
        return;
      }
      searchPatientsApi(String(selectedId)).then(function (matches) {
        var found = matches.find(function (p) {
          return String(p.id) === String(selectedId) || String(p.patient_id) === String(selectedId);
        }) || matches[0];
        if (found) consultPatientPicker.setSelected(found);
      });
      return;
    }
    consultPatientPicker.clear();
  }

  /* ============================================================
     MULTI-PAGE NAVIGATION
  ============================================================ */
  var ROUTES = window.PSYCH_ROUTES || {};

  function goToPage(key, query) {
    var url = ROUTES[key];
    if (!url) return;
    if (query) {
      url += (url.indexOf('?') >= 0 ? '&' : '?') + query;
    }
    window.location.href = url;
  }

  // Legacy data-goto attributes → real page navigation
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-goto]');
    if (!el) return;
    e.preventDefault();
    goToPage(el.getAttribute('data-goto'));
  });

  // Topbar date
  var topbarDate = document.getElementById('topbar-date');
  if (topbarDate) {
    topbarDate.textContent = new Date().toLocaleDateString('en-US', {
      weekday: 'long', month: 'long', day: 'numeric', year: 'numeric'
    });
  }

  /* ============================================================
     MOBILE HAMBURGER
  ============================================================ */
  var hamBtn = document.getElementById('hamburger-btn');
  var sidebar = document.getElementById('sidebar');
  if (hamBtn) {
    hamBtn.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });
  }

  var profileBtn = document.getElementById('profile-btn');
  if (profileBtn) profileBtn.addEventListener('click', function () { openModal('profile-modal'); });

  /* ============================================================
     INTAKE TIME
  ============================================================ */
  var intakeTime = now();

  window.openPatientDetail = function (id) {
    viewPatient(id);
  };

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
    setVal('pr-clinical-notes', p.clinical_notes);
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
          clinical_notes: getVal('pr-clinical-notes') || null,
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
        showToast(data.message || 'Psychiatric history updated.');
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

  /* ============================================================
     BUILD CONSULTATIONS TABLE
  ============================================================ */
  function buildConsultations(list) {
    var tbody = document.getElementById('consults-tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    if (!list.length) {
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#64748b;">No consultations scheduled.</td></tr>';
      return;
    }
    list.forEach(function (c) {
      var typeBadge = c.type === 'Emergency' ? '<span class="badge badge-emergency">Emergency</span>' :
        c.type === 'Initial' ? '<span class="badge badge-outline">Initial</span>' :
          '<span class="badge badge-outline">Follow-up</span>';
      var statBadge = c.status === 'Completed' ? '<span class="badge badge-completed">Completed</span>' :
        c.status === 'Cancelled' ? '<span class="badge badge-inactive">Cancelled</span>' :
          '<span class="badge badge-scheduled">Scheduled</span>';
      var tr = document.createElement('tr');
      tr.innerHTML = '<td class="td-name">' + (c.patient || '—') + '</td>' +
        '<td>' + (c.date || '—') + '</td>' +
        '<td>' + (c.time || '—') + '</td>' +
        '<td>' + typeBadge + '</td>' +
        '<td>' + statBadge + '</td>' +
        '<td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="' + (c.notes || '') + '">' + (c.notes || '—') + '</td>' +
        '<td><button class="btn-outline-sm" onclick="editConsult(' + c.id + ')">Edit</button></td>';
      tbody.appendChild(tr);
    });
  }

  window.editConsult = function (id) {
    var c = CONSULTATIONS.find(function (x) { return String(x.id) === String(id); });
    if (!c) return;

    setVal('ec-id', c.id);

    var titleEl = document.getElementById('ec-modal-title');
    var subEl = document.getElementById('ec-modal-sub');
    if (titleEl) titleEl.textContent = 'Edit Consultation';
    if (subEl) subEl.textContent = c.patient + ' · ' + c.type;

    var nameEl = document.getElementById('ec-patient-name');
    if (nameEl) nameEl.textContent = c.patient;

    var typeBadgeEl = document.getElementById('ec-type-badge');
    if (typeBadgeEl) {
      var tc = c.type === 'Emergency' ? 'badge-emergency' : 'badge-outline';
      typeBadgeEl.innerHTML = '<span class="badge ' + tc + '">' + c.type + '</span>';
    }

    var statBadgeEl = document.getElementById('ec-status-badge');
    if (statBadgeEl) {
      var sc = c.status === 'Completed' ? 'badge-completed' : (c.status === 'Cancelled' ? 'badge-inactive' : 'badge-scheduled');
      statBadgeEl.innerHTML = '<span class="badge ' + sc + '">' + c.status + '</span>';
    }

    setVal('ec-date', c.date || '');
    setVal('ec-time', c.time_24 || '');
    setVal('ec-type', c.type || 'Follow-up');
    setVal('ec-status', c.status || 'Scheduled');
    setVal('ec-notes', c.notes || '');
    setVal('ec-diagnosis', c.diagnosis || '');
    setVal('ec-treatment', c.treatment || '');

    var outcomeSection = document.getElementById('ec-outcome-section');
    if (outcomeSection) {
      if (c.status === 'Completed') outcomeSection.classList.remove('hidden');
      else outcomeSection.classList.add('hidden');
    }

    openModal('edit-consult-modal');
    ri();
  };

  function initEditConsultModal() {
    var ecStatus = document.getElementById('ec-status');
    var ecOutcome = document.getElementById('ec-outcome-section');
    if (ecStatus && ecOutcome) {
      ecStatus.addEventListener('change', function () {
        if (this.value === 'Completed') ecOutcome.classList.remove('hidden');
        else ecOutcome.classList.add('hidden');
        ri();
      });
    }

    var ecSaveBtn = document.getElementById('ec-save-btn');
    if (ecSaveBtn) {
      ecSaveBtn.addEventListener('click', function () {
        var id = getVal('ec-id');
        if (!id) return;
        var base = (window.PSYCH_ROUTES || {}).consultationsUpdate || '/psychiatrist/consultations';
        apiFetch(base + '/' + id, {
          method: 'PUT',
          body: JSON.stringify({
            date: getVal('ec-date'),
            time: getVal('ec-time'),
            type: getVal('ec-type'),
            status: getVal('ec-status'),
            notes: getVal('ec-notes'),
            diagnosis: getVal('ec-diagnosis'),
            treatment: getVal('ec-treatment'),
          }),
        }).then(function (data) {
          var idx = CONSULTATIONS.findIndex(function (x) { return String(x.id) === String(id); });
          if (idx >= 0) CONSULTATIONS[idx] = data.consultation;
          buildConsultations(CONSULTATIONS);
          closeModal('edit-consult-modal');
          showToast(data.message || 'Consultation updated.');
        }).catch(function (err) { showToast(err.message); });
      });
    }

    var ecDeleteBtn = document.getElementById('ec-delete-btn');
    if (ecDeleteBtn) {
      ecDeleteBtn.addEventListener('click', function () {
        var id = getVal('ec-id');
        if (!id) return;
        var c = CONSULTATIONS.find(function (x) { return String(x.id) === String(id); });
        if (!confirm('Delete consultation for ' + (c ? c.patient : 'this patient') + '? This cannot be undone.')) return;
        var base = (window.PSYCH_ROUTES || {}).consultationsUpdate || '/psychiatrist/consultations';
        apiFetch(base + '/' + id, { method: 'DELETE' })
          .then(function (data) {
            CONSULTATIONS = CONSULTATIONS.filter(function (x) { return String(x.id) !== String(id); });
            buildConsultations(CONSULTATIONS);
            closeModal('edit-consult-modal');
            showToast(data.message || 'Consultation deleted.');
          }).catch(function (err) { showToast(err.message); });
      });
    }
  }

  /* ============================================================
     BUILD LIFESTYLE
  ============================================================ */
  function metricColor(pct) {
    if (pct >= 70) return 'bar-green';
    if (pct >= 40) return 'bar-amber';
    return 'bar-red';
  }

  function clampPct(n) {
    n = Number(n) || 0;
    if (n < 0) return 0;
    if (n > 100) return 100;
    return Math.round(n);
  }

  function scoreFrequency(value, goodWords, badWords) {
    var v = String(value || '').toLowerCase();
    if (!v) return { pct: 40, label: '—' };
    for (var i = 0; i < goodWords.length; i++) {
      if (v.indexOf(goodWords[i]) >= 0) return { pct: 80, label: value };
    }
    for (var j = 0; j < badWords.length; j++) {
      if (v.indexOf(badWords[j]) >= 0) return { pct: 25, label: value };
    }
    return { pct: 55, label: value };
  }

  function phqStressPct(ls) {
    var map = {
      'Not at all': 15,
      'Several days': 45,
      'More than half the days': 70,
      'Nearly every day': 90,
    };
    var keys = ['phq_feeling_down', 'phq_feeling_tired', 'phq_trouble_sleeping', 'phq_little_interest'];
    var total = 0;
    var count = 0;
    keys.forEach(function (k) {
      if (ls[k] && map[ls[k]] != null) {
        total += map[ls[k]];
        count += 1;
      }
    });
    if (!count) return { pct: 40, label: 'Not assessed' };
    var pct = Math.round(total / count);
    var label = pct >= 70 ? 'High' : (pct >= 40 ? 'Moderate' : 'Low');
    return { pct: pct, label: label };
  }

  function lifestyleMetrics(ls) {
    ls = ls || {};
    var sleepHours = ls.sleep_hours != null && ls.sleep_hours !== '' ? Number(ls.sleep_hours) : null;
    var sleepPct = sleepHours == null ? 40 : clampPct((sleepHours / 8) * 100);
    var exercise = scoreFrequency(ls.exercise_frequency, ['daily', 'every day', '5', '6', '7', 'regular'], ['never', 'rare', 'none', 'sedentary', '0']);
    var nutrition = scoreFrequency(
      [ls.fruits_veg_servings, ls.fast_food_frequency, ls.weight_perception].filter(Boolean).join(' · ') || '',
      ['good', 'healthy', 'daily', '5', 'plenty'],
      ['poor', 'fair', 'often', 'daily fast', 'obese']
    );
    if (!ls.fruits_veg_servings && !ls.fast_food_frequency) {
      nutrition = { pct: 40, label: '—' };
    } else if (ls.fruits_veg_servings && !nutrition.label) {
      nutrition.label = ls.fruits_veg_servings;
    } else if (ls.fruits_veg_servings) {
      nutrition.label = ls.fruits_veg_servings;
    }
    var stress = phqStressPct(ls);
    var health = ls.health_score != null && ls.health_score !== ''
      ? { pct: clampPct(Number(ls.health_score) * 10), label: String(ls.health_score) + '/10' }
      : null;

    var metrics = [
      { label: 'Sleep', value: sleepHours == null ? '—' : (sleepHours + ' hrs'), pct: sleepPct, color: metricColor(sleepPct) },
      { label: 'Exercise', value: exercise.label || '—', pct: exercise.pct, color: metricColor(exercise.pct) },
      { label: 'Nutrition', value: nutrition.label || '—', pct: nutrition.pct, color: metricColor(nutrition.pct) },
      { label: 'Stress', value: stress.label, pct: stress.pct, color: metricColor(100 - stress.pct) },
    ];
    if (health) {
      metrics.unshift({ label: 'Health', value: health.label, pct: health.pct, color: metricColor(health.pct) });
    }
    return metrics;
  }

  function buildLifestyle(list) {
    var grid = document.getElementById('lifestyle-grid');
    var empty = document.getElementById('lifestyle-empty');
    if (!grid) return;
    list = Array.isArray(list) ? list : LIFESTYLE_PATIENTS;
    grid.innerHTML = '';

    if (!list.length) {
      if (empty) show(empty);
      return;
    }
    if (empty) hide(empty);

    list.forEach(function (p) {
      var metrics = lifestyleMetrics(p.lifestyle_assessment);
      var card = document.createElement('div');
      card.className = 'lifestyle-card';
      card.style.cursor = 'pointer';
      card.setAttribute('data-patient-id', p.id);

      var html = '<div class="lifestyle-card-top" style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:12px;">' +
        '<div>' +
        '<div class="lifestyle-card-name" style="margin-bottom:2px;">' + (p.name || 'Patient') + '</div>' +
        '<div style="font-size:12px;color:#64748b;">' + (p.patient_id || ('#' + p.id)) +
        (p.age != null ? ' · ' + p.age + 'y' : '') +
        (p.sex ? ' · ' + p.sex : '') + '</div>' +
        '</div>' +
        '<button type="button" class="btn-outline-sm lifestyle-open-btn" data-lifestyle-open="' + p.id + '">Open</button>' +
        '</div>';

      metrics.forEach(function (m) {
        html += '<div class="metric-row">' +
          '<span class="metric-label">' + m.label + '</span>' +
          '<div class="metric-track"><div class="metric-bar ' + m.color + '" style="width:' + m.pct + '%"></div></div>' +
          '<span class="metric-value">' + m.value + '</span>' +
          '</div>';
      });

      if (p.lifestyle_assessment && p.lifestyle_assessment.motivation_level) {
        html += '<div style="margin-top:8px;font-size:12px;color:#64748b;">Motivation: <strong style="color:#0f172a;">' +
          p.lifestyle_assessment.motivation_level + '</strong></div>';
      }

      card.innerHTML = html;
      card.addEventListener('click', function (e) {
        if (e.target.closest('.lifestyle-open-btn') || e.target === card || e.target.closest('.lifestyle-card')) {
          openLifestylePatient(p.id);
        }
      });
      grid.appendChild(card);
    });
    ri();
  }

  function openLifestylePatient(id) {
    var local = findPatientLocal(id);
    if (local && local.medical_history !== undefined) {
      populatePatientModal(local);
      switchPmTab('lifestyle');
    } else {
      var base = (window.PSYCH_ROUTES || {}).patientsShow || '/psychiatrist/patients';
      apiFetch(base + '/' + id)
        .then(function (data) {
          upsertPatientLocal(data.patient);
          populatePatientModal(data.patient);
          switchPmTab('lifestyle');
        })
        .catch(function (err) {
          showToast(err.message || 'Unable to load patient.');
        });
    }
  }

  function filterLifestyle() {
    var input = document.getElementById('lifestyle-search');
    var q = input ? input.value.toLowerCase().trim() : '';
    var list = LIFESTYLE_PATIENTS.filter(function (p) {
      if (!q) return true;
      return (p.name || '').toLowerCase().includes(q) || String(p.patient_id || '').toLowerCase().includes(q);
    });
    buildLifestyle(list);
  }

  var lifestyleSearch = document.getElementById('lifestyle-search');
  if (lifestyleSearch) lifestyleSearch.addEventListener('input', filterLifestyle);

  /* ============================================================
     BUILD ASSESSMENTS
  ============================================================ */
  function buildAssessments(list) {
    var grid = document.getElementById('assessment-cards-grid');
    if (!grid) return;
    grid.innerHTML = '';

    var accentMap = {
      Complete: { accent: 'accent-stable', pctClass: 'pct-green', barClass: 'bar-lg-green', progClass: 'prog-green', badge: 'badge-stable', label: 'Complete' },
      'In Progress': { accent: 'accent-monitoring', pctClass: 'pct-amber', barClass: 'bar-lg-amber', progClass: 'prog-amber', badge: 'badge-monitoring', label: 'In Progress' },
      'Not Started': { accent: 'accent-maintenance', pctClass: 'pct-blue', barClass: 'bar-lg-blue', progClass: 'prog-blue', badge: 'badge-outline', label: 'Not Started' },
    };

    list.forEach(function (a) {
      var theme = accentMap[a.status] || accentMap['Not Started'];
      var badgeClass = theme.badge;
      var statusLabel = theme.label;

      var div = document.createElement('div');
      div.className = 'assess-card';
      div.setAttribute('data-assess-id', a.id);

      div.innerHTML =
        /* Top accent bar */
        '<div class="assess-card-accent ' + theme.accent + '"></div>' +

        /* Card body */
        '<div class="assess-card-body">' +

        /* Top row: ID + status badge */
        '<div class="assess-card-top">' +
        '<div class="assess-card-id-wrap">' +
        '<span class="assess-card-id">' + a.id + '</span>' +
        '<div class="assess-card-name">' + a.name + '</div>' +
        '<div class="assess-card-age">' + a.age + ' · ' + a.sex + '</div>' +
        '</div>' +
        '<span class="badge ' + badgeClass + '" style="white-space:nowrap;flex-shrink:0;">' + statusLabel + '</span>' +
        '</div>' +

        /* Diagnosis pill */
        '<div class="assess-diag-pill">' +
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>' +
        a.diag +
        '</div>' +

        /* Program tag (if any) */
        (a.tag ?
          '<div><span class="assess-prog-tag">' +
          '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:10px;height:10px;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>' +
          a.tag +
          '</span></div>'
          : '') +

        /* Provider */
        '<div class="assess-provider-row">' +
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>' +
        a.provider +
        '</div>' +

        /* Completion */
        '<div class="assess-completion-block">' +
        '<div class="assess-completion-header">' +
        '<span class="assess-completion-label">Assessment Completion</span>' +
        '<span class="assess-completion-pct ' + theme.pctClass + '">' + a.prog + '%</span>' +
        '</div>' +
        '<div class="assess-prog-track-lg">' +
        '<div class="assess-prog-bar-lg ' + theme.barClass + '" style="width:' + a.prog + '%"></div>' +
        '</div>' +
        '</div>' +

        '</div>' + /* /assess-card-body */

        /* Card footer */
        '<div class="assess-card-footer">' +
        '<span class="assess-last-date">' +
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' +
        'Last assessed ' + a.lastDate +
        '</span>' +
        '<button class="assess-open-btn" data-assess-open="' + a.id + '">' +
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>' +
        'Open' +
        '</button>' +
        '</div>';

      grid.appendChild(div);
    });
  }

  function populateAssessmentPatientSelects() {
    // Searchable patient pickers are initialized separately.
  }

  function populateAssessmentCards() {
    var patients = ASSESSMENT_PATIENTS || [];
    var cards = patients.map(function (p) {
      var prog = typeof p.completion === 'number' ? p.completion : (p.has_assessment ? 50 : 0);
      var status = prog >= 100 ? 'Complete' : (prog > 0 || p.has_assessment ? 'In Progress' : 'Not Started');
      return {
        id: String(p.id),
        patient_id: p.patient_id || '',
        name: p.name || 'Patient',
        age: p.age != null ? (p.age + 'y') : '—',
        sex: p.sex || '—',
        diag: p.summary || p.primary_diagnosis || 'No assessment yet',
        status: status,
        prog: prog,
        lastDate: p.assessment_updated_at || (p.has_assessment ? 'Saved' : 'Not started'),
        tag: null,
        provider: 'Dr. Maria Santos · Psychiatrist',
        patient: p,
      };
    });
    ASSESSMENTS = cards;
    buildAssessments(cards);
  }

  // Server-side search form handles filtering; keep local filter as no-op fallback.
  var assSearch = document.getElementById('assess-search');
  if (assSearch) {
    assSearch.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        if (this.form) this.form.submit();
      }
    });
  }

  document.addEventListener('click', function (e) {
    var openBtn = e.target.closest('[data-assess-open]');
    if (openBtn) {
      openAssessDetail(openBtn.getAttribute('data-assess-open'));
      return;
    }
    var card = e.target.closest('#assessment-cards-grid .assess-card');
    if (card && !e.target.closest('button')) {
      openAssessDetail(card.getAttribute('data-assess-id'));
    }
  });

  var CURRENT_ASSESSMENT = {};

  function escapeHtml(str) {
    return String(str || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function collectBiological() {
    return {
      bp: getVal('v-bp'),
      hr: getVal('v-hr'),
      temp: getVal('v-temp'),
      weight: getVal('v-weight'),
      height: getVal('v-height'),
      bmi: getVal('v-bmi'),
      cc: getVal('bio-cc'),
      hpi: getVal('bio-hpi'),
      pph: getVal('bio-pph'),
      pmh: getVal('bio-pmh'),
      fh: getVal('bio-fh'),
      sh: getVal('bio-sh'),
      ros: getVal('bio-ros'),
      pe: getVal('bio-pe'),
      lab: getVal('bio-lab'),
    };
  }

  function collectPsychological() {
    var si = 'none';
    qsa('input[name="psy-si"]').forEach(function (rb) { if (rb.checked) si = rb.value; });
    return {
      appearance: getVal('psy-appearance'),
      behavior: getVal('psy-behavior'),
      speech: getVal('psy-speech'),
      mood: getVal('psy-mood'),
      affect: getVal('psy-affect'),
      thought_process: getVal('psy-thought_process'),
      thought_content: getVal('psy-thought_content'),
      perception: getVal('psy-perception'),
      cognition: getVal('psy-cognition'),
      insight: getVal('psy-insight'),
      judgment: getVal('psy-judgment'),
      si: si,
      risk_notes: getVal('psy-risk_notes'),
      safety_plan: getVal('psy-safety_plan'),
    };
  }

  function collectSocial() {
    return {
      living: getVal('soc-living'),
      occupation: getVal('soc-occupation'),
      financial: getVal('soc-financial'),
      relationships: getVal('soc-relationships'),
      cultural: getVal('soc-cultural'),
      legal: getVal('soc-legal'),
      substance: getVal('soc-substance'),
      stressors: getVal('soc-stressors'),
    };
  }

  function collectSpiritual() {
    return {
      beliefs: getVal('spi-beliefs'),
      practices: getVal('spi-practices'),
      coping: getVal('spi-coping'),
      needs: getVal('spi-needs'),
      strengths: getVal('spi-strengths'),
      meaning: getVal('spi-meaning'),
    };
  }

  function collectPrayerPoints() {
    var points = [];
    qsa('#prayer-list .prayer-entry').forEach(function (el) {
      points.push({
        date: el.getAttribute('data-date') || '',
        author: el.getAttribute('data-author') || 'Dr. Maria Santos',
        text: el.getAttribute('data-text') || (el.querySelector('.prayer-text') ? el.querySelector('.prayer-text').textContent : ''),
      });
    });
    return points;
  }

  function collectIntervention() {
    var timeline = [];
    qsa('#intervention-timeline .timeline-entry').forEach(function (el) {
      timeline.push({
        date: el.getAttribute('data-date') || '',
        text: el.getAttribute('data-text') || (el.querySelector('.timeline-text') ? el.querySelector('.timeline-text').textContent : ''),
      });
    });
    return {
      plan: getVal('intervention-plan'),
      timeline: timeline,
      medical_history: {
        allergies: getVal('medhist-allergies'),
        medications: getVal('medhist-medications'),
        surgical: getVal('medhist-surgical'),
        hospitalizations: getVal('medhist-hospitalizations'),
        immunizations: getVal('medhist-immunizations'),
        family: getVal('medhist-family'),
      },
    };
  }

  function renderPrayerPoints(points) {
    var list = document.getElementById('prayer-list');
    if (!list) return;
    list.innerHTML = '';
    (points || []).forEach(function (point) {
      var entry = document.createElement('div');
      entry.className = 'prayer-entry';
      entry.setAttribute('data-date', point.date || '');
      entry.setAttribute('data-author', point.author || 'Dr. Maria Santos');
      entry.setAttribute('data-text', point.text || '');
      entry.innerHTML = '<div class="prayer-meta">' + escapeHtml(point.date || '') +
        ' · ' + escapeHtml(point.author || 'Dr. Maria Santos') +
        ' <button type="button" class="btn-outline-sm prayer-remove" style="margin-left:8px;">Remove</button></div>' +
        '<div class="prayer-text">' + escapeHtml(point.text || '') + '</div>';
      list.appendChild(entry);
    });
  }

  function renderInterventionTimeline(entries) {
    var list = document.getElementById('intervention-timeline');
    if (!list) return;
    list.innerHTML = '';
    (entries || []).forEach(function (item) {
      var entry = document.createElement('div');
      entry.className = 'timeline-entry';
      entry.setAttribute('data-date', item.date || '');
      entry.setAttribute('data-text', item.text || '');
      entry.innerHTML = '<div class="timeline-dot"></div>' +
        '<div class="timeline-content">' +
        '<div class="timeline-date">' + escapeHtml(item.date || '') +
        ' <button type="button" class="btn-outline-sm timeline-remove" style="margin-left:8px;">Remove</button></div>' +
        '<div class="timeline-text">' + escapeHtml(item.text || '') + '</div>' +
        '</div>';
      list.appendChild(entry);
    });
  }

  function hydrateAssessmentForm(assessment) {
    assessment = assessment || {};
    var bio = assessment.biological || {};
    setVal('v-bp', bio.bp); setVal('v-hr', bio.hr); setVal('v-temp', bio.temp);
    setVal('v-weight', bio.weight); setVal('v-height', bio.height); setVal('v-bmi', bio.bmi);
    setVal('bio-cc', bio.cc); setVal('bio-hpi', bio.hpi); setVal('bio-pph', bio.pph);
    setVal('bio-pmh', bio.pmh); setVal('bio-fh', bio.fh); setVal('bio-sh', bio.sh);
    setVal('bio-ros', bio.ros); setVal('bio-pe', bio.pe); setVal('bio-lab', bio.lab);

    var psy = assessment.psychological || {};
    ['appearance', 'behavior', 'speech', 'mood', 'affect', 'thought_process', 'thought_content', 'perception', 'cognition', 'insight', 'judgment', 'risk_notes', 'safety_plan'].forEach(function (k) {
      setVal('psy-' + k, psy[k]);
    });
    var si = psy.si || 'none';
    qsa('input[name="psy-si"]').forEach(function (rb) { rb.checked = rb.value === si; });

    var soc = assessment.social || {};
    ['living', 'occupation', 'financial', 'relationships', 'cultural', 'legal', 'substance', 'stressors'].forEach(function (k) {
      setVal('soc-' + k, soc[k]);
    });

    var spi = assessment.spiritual || {};
    ['beliefs', 'practices', 'coping', 'needs', 'strengths', 'meaning'].forEach(function (k) {
      setVal('spi-' + k, spi[k]);
    });

    renderPrayerPoints(assessment.prayer_points || []);

    var inter = assessment.intervention || {};
    setVal('intervention-plan', inter.plan || '');
    renderInterventionTimeline(inter.timeline || []);
    var mh = inter.medical_history || {};
    ['allergies', 'medications', 'surgical', 'hospitalizations', 'immunizations', 'family'].forEach(function (k) {
      setVal('medhist-' + k, mh[k]);
    });
  }

  function openAssessDetail(id) {
    var a = ASSESSMENTS.find(function (x) {
      return String(x.id) === String(id) || String(x.patient_id) === String(id);
    });
    if (!a) {
      // Deep-link may use patient_id before cards map exists; try API by numeric id if possible.
      var numericId = parseInt(id, 10);
      if (!numericId) return;
      a = { id: String(numericId), name: 'Patient', patient: { id: numericId } };
    }

    CURRENT_PATIENT = Object.assign({}, a.patient || {}, { id: a.patient && a.patient.id ? a.patient.id : a.id });
    hide(document.getElementById('assessment-list-view'));
    show(document.getElementById('assessment-detail-view'));
    document.getElementById('assess-detail-name').textContent = a.name || CURRENT_PATIENT.name || 'Patient';
    document.getElementById('assess-detail-id').textContent = a.patient_id || CURRENT_PATIENT.patient_id || a.id;
    hydrateAssessmentForm({});
    switchAssessTab('biological');

    var url = (ROUTES.assessmentsShow || '/psychiatrist/patients/__ID__/assessment').replace('__ID__', CURRENT_PATIENT.id);
    apiFetch(url).then(function (data) {
      CURRENT_ASSESSMENT = data.assessment || {};
      CURRENT_PATIENT = Object.assign({}, CURRENT_PATIENT, data.patient || {});
      document.getElementById('assess-detail-name').textContent = CURRENT_PATIENT.name || a.name;
      document.getElementById('assess-detail-id').textContent = CURRENT_PATIENT.patient_id || a.id;
      hydrateAssessmentForm(CURRENT_ASSESSMENT);
      ri();
    }).catch(function (err) {
      showToast(err.message || 'Unable to load assessment.');
    });
    ri();
  }

  var assessBackBtn = document.getElementById('assess-back-btn');
  if (assessBackBtn) assessBackBtn.addEventListener('click', function () {
    hide(document.getElementById('assessment-detail-view'));
    show(document.getElementById('assessment-list-view'));
    ri();
  });

  function switchAssessTab(tab) {
    qsa('#assess-tab-bar .assess-tab-btn').forEach(function (b) {
      b.classList.toggle('active', b.getAttribute('data-assess-tab') === tab);
    });
    qsa('#assessment-detail-view .tab-panel').forEach(function (p) {
      p.classList.toggle('active', p.getAttribute('data-assess-panel') === tab || p.id === ('tab-' + tab));
    });
  }

  qsa('#assess-tab-bar .assess-tab-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      switchAssessTab(this.getAttribute('data-assess-tab'));
    });
  });

  function saveAssessmentSection(section) {
    if (!CURRENT_PATIENT || !CURRENT_PATIENT.id) {
      showToast('Open a patient assessment first.');
      return;
    }

    var payload = {};
    if (section === 'biological') payload.biological = collectBiological();
    else if (section === 'psychological') payload.psychological = collectPsychological();
    else if (section === 'social') payload.social = collectSocial();
    else if (section === 'spiritual') payload.spiritual = collectSpiritual();
    else if (section === 'prayer_points') payload.prayer_points = collectPrayerPoints();
    else if (section === 'intervention') payload.intervention = collectIntervention();
    else {
      payload = {
        biological: collectBiological(),
        psychological: collectPsychological(),
        social: collectSocial(),
        spiritual: collectSpiritual(),
        prayer_points: collectPrayerPoints(),
        intervention: collectIntervention(),
      };
    }

    apiFetch((ROUTES.assessmentsStore || '').replace('__ID__', CURRENT_PATIENT.id), {
      method: 'POST',
      body: JSON.stringify(payload),
    }).then(function (data) {
      CURRENT_ASSESSMENT = data.assessment || Object.assign({}, CURRENT_ASSESSMENT, payload);
      CURRENT_PATIENT.assessment = CURRENT_ASSESSMENT;
      var card = ASSESSMENTS.find(function (x) { return String(x.id) === String(CURRENT_PATIENT.id); });
      if (card) {
        card.diag = (CURRENT_ASSESSMENT.biological && CURRENT_ASSESSMENT.biological.cc) || card.diag;
        card.lastDate = (data.updated_at || '').slice(0, 10) || card.lastDate;
        card.prog = Math.max(card.prog || 0, 20);
      }
      showToast(data.message || 'Assessment saved.');
    }).catch(function (err) {
      showToast(err.message || 'Unable to save assessment.');
    });
  }

  document.addEventListener('click', function (e) {
    var saveBtn = e.target.closest('[data-assess-save]');
    if (saveBtn && saveBtn.closest('#assessment-detail-view')) {
      e.preventDefault();
      saveAssessmentSection(saveBtn.getAttribute('data-assess-save'));
    }
    if (e.target.closest('.prayer-remove')) {
      var prayerEntry = e.target.closest('.prayer-entry');
      if (prayerEntry) prayerEntry.remove();
    }
    if (e.target.closest('.timeline-remove')) {
      var tlEntry = e.target.closest('.timeline-entry');
      if (tlEntry) tlEntry.remove();
    }
  });

  if (typeof populateAssessmentCards === 'function') populateAssessmentCards();

  /* ============================================================
     ACCORDION (Medical History)
  ============================================================ */
  function initAccordion() {
    qsa('#medhist-accordion .accordion-trigger').forEach(function (trigger) {
      trigger.addEventListener('click', function () {
        var panel = this.nextElementSibling;
        var isOpen = panel.classList.contains('open');
        qsa('#medhist-accordion .accordion-trigger').forEach(function (t) {
          t.classList.remove('open');
          if (t.nextElementSibling) t.nextElementSibling.classList.remove('open');
        });
        if (!isOpen) {
          this.classList.add('open');
          panel.classList.add('open');
        }
        ri();
      });
    });

    var toggleAllBtn = document.getElementById('medhist-toggle-all');
    if (toggleAllBtn) {
      var allOpen = false;
      toggleAllBtn.addEventListener('click', function () {
        allOpen = !allOpen;
        qsa('#medhist-accordion .accordion-trigger').forEach(function (t) {
          if (allOpen) { t.classList.add('open'); if (t.nextElementSibling) t.nextElementSibling.classList.add('open'); }
          else { t.classList.remove('open'); if (t.nextElementSibling) t.nextElementSibling.classList.remove('open'); }
        });
        toggleAllBtn.textContent = allOpen ? 'Collapse All' : 'Expand All';
        ri();
      });
    }
  }

  /* ============================================================
     PRAYER POINTS
  ============================================================ */
  var addPrayerBtn = document.getElementById('add-prayer-btn');
  var prayerInput = document.getElementById('prayer-input');

  if (addPrayerBtn) {
    addPrayerBtn.addEventListener('click', function () {
      var text = prayerInput ? prayerInput.value.trim() : '';
      if (!text) { showToast('Please enter a prayer point.'); return; }
      var d = new Date();
      var dateStr = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
      var points = collectPrayerPoints();
      points.unshift({ date: dateStr, author: 'Dr. Maria Santos', text: text });
      renderPrayerPoints(points);
      prayerInput.value = '';
      saveAssessmentSection('prayer_points');
    });
  }

  /* ============================================================
     INTERVENTION TIMELINE
  ============================================================ */
  var addInterventionBtn = document.getElementById('add-intervention-btn');
  var interventionInput = document.getElementById('intervention-input');

  if (addInterventionBtn) {
    addInterventionBtn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var text = interventionInput ? interventionInput.value.trim() : '';
      if (!text) { showToast('Please enter an intervention.'); return; }
      var d = new Date();
      var dateStr = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
      var inter = collectIntervention();
      inter.timeline = inter.timeline || [];
      inter.timeline.unshift({ date: dateStr, text: text });
      setVal('intervention-plan', inter.plan || getVal('intervention-plan'));
      renderInterventionTimeline(inter.timeline);
      interventionInput.value = '';
      saveAssessmentSection('intervention');
    });
  }

  /* ============================================================
     PRESCRIPTIONS — SUB-TABS
  ============================================================ */
  qsa('.subtab-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var key = this.getAttribute('data-subtab');
      qsa('.subtab-btn').forEach(function (b) { b.classList.remove('active'); });
      qsa('.subtab-panel').forEach(function (p) { p.classList.remove('active'); });
      this.classList.add('active');
      var panel = document.getElementById('subtab-' + key);
      if (panel) panel.classList.add('active');
      ri();
    });
  });

  /* ============================================================
     RX: DATE
  ============================================================ */
  var rxDate = document.getElementById('rx-date');
  var dxDate = document.getElementById('dx-date');
  var todayStr = today();
  if (rxDate) rxDate.value = todayStr;
  if (dxDate) dxDate.value = todayStr;

  /* ============================================================
     RX: DIAGNOSIS TYPEAHEAD
  ============================================================ */
  var rxDiagInput = document.getElementById('rx-diagnosis');
  var rxDiagDropdown = document.getElementById('rx-diag-dropdown');

  if (rxDiagInput && rxDiagDropdown) {
    rxDiagInput.addEventListener('input', function () {
      var q = this.value.toLowerCase();
      if (!q) { hide(rxDiagDropdown); return; }
      var matches = DIAGNOSES.filter(function (d) { return d.toLowerCase().includes(q); });
      if (!matches.length) { hide(rxDiagDropdown); return; }
      rxDiagDropdown.innerHTML = '';
      matches.forEach(function (d) {
        var item = document.createElement('div');
        item.className = 'typeahead-item';
        item.textContent = d;
        item.addEventListener('click', function () {
          rxDiagInput.value = d;
          hide(rxDiagDropdown);
        });
        rxDiagDropdown.appendChild(item);
      });
      show(rxDiagDropdown);
    });

    document.addEventListener('click', function (e) {
      if (!rxDiagInput.contains(e.target) && !rxDiagDropdown.contains(e.target)) hide(rxDiagDropdown);
    });
  }

  /* ============================================================
     RX: MEDICATION ROWS
  ============================================================ */
  var rxMedsList = document.getElementById('rx-meds-list');
  var addMedBtn = document.getElementById('add-med-btn');

  function createMedRow(prefill) {
    prefill = prefill || {};
    var row = document.createElement('div');
    row.className = 'rx-med-row';

    // Med name
    var nameWrap = document.createElement('div');
    nameWrap.className = 'med-name-wrap';
    var nameInput = document.createElement('input');
    nameInput.type = 'text';
    nameInput.className = 'field-input';
    nameInput.placeholder = 'Medication name...';
    nameInput.value = prefill.name || '';
    var dropdown = document.createElement('div');
    dropdown.className = 'med-typeahead hidden';

    nameInput.addEventListener('input', function () {
      var q = this.value.toLowerCase();
      if (!q) { hide(dropdown); return; }
      var hits = MED_SUGGESTIONS.filter(function (m) { return m.toLowerCase().startsWith(q); });
      if (!hits.length) { hide(dropdown); return; }
      dropdown.innerHTML = '';
      hits.slice(0, 8).forEach(function (m) {
        var item = document.createElement('div');
        item.className = 'med-typeahead-item';
        item.textContent = m;
        item.addEventListener('click', function () {
          nameInput.value = m;
          hide(dropdown);
        });
        dropdown.appendChild(item);
      });
      show(dropdown);
    });

    document.addEventListener('click', function (e) {
      if (!nameWrap.contains(e.target)) hide(dropdown);
    });

    nameWrap.appendChild(nameInput);
    nameWrap.appendChild(dropdown);

    // Dosage
    var doseSelect = document.createElement('select');
    doseSelect.className = 'field-input';
    DOSAGES.forEach(function (d) {
      var opt = document.createElement('option');
      opt.value = d; opt.textContent = d;
      if (prefill.dose === d) opt.selected = true;
      doseSelect.appendChild(opt);
    });
    // Custom input
    var customDoseInput = document.createElement('input');
    customDoseInput.type = 'text';
    customDoseInput.className = 'field-input hidden';
    customDoseInput.placeholder = 'Custom dosage...';
    customDoseInput.style.marginTop = '4px';
    var doseWrap = document.createElement('div');
    doseWrap.appendChild(doseSelect);
    doseWrap.appendChild(customDoseInput);
    doseSelect.addEventListener('change', function () {
      if (this.value === 'Custom') show(customDoseInput);
      else hide(customDoseInput);
    });

    // Frequency
    var freqCol = document.createElement('div');
    freqCol.className = 'med-freq-col';
    var freqCheckboxes = document.createElement('div');
    freqCheckboxes.className = 'freq-checkboxes';
    var FREQS = ['Morning', 'Breakfast', 'Lunch', 'Dinner', 'Bedtime'];
    FREQS.forEach(function (f) {
      var lbl = document.createElement('label');
      lbl.className = 'freq-cb-label';
      var cb = document.createElement('input');
      cb.type = 'checkbox';
      if (prefill.freq && prefill.freq.includes(f)) cb.checked = true;
      lbl.appendChild(cb);
      lbl.appendChild(document.createTextNode(f));
      freqCheckboxes.appendChild(lbl);
    });
    var freqCustom = document.createElement('input');
    freqCustom.type = 'text';
    freqCustom.className = 'freq-custom-input';
    freqCustom.placeholder = 'Custom (e.g. PRN)...';
    freqCol.appendChild(freqCheckboxes);
    freqCol.appendChild(freqCustom);

    // Qty
    var qtyInput = document.createElement('input');
    qtyInput.type = 'number';
    qtyInput.className = 'med-qty-input';
    qtyInput.placeholder = 'Qty';
    qtyInput.value = prefill.qty || '';

    // Delete
    var delBtn = document.createElement('button');
    delBtn.type = 'button';
    delBtn.className = 'med-del-btn';
    delBtn.innerHTML = '<i data-feather="x"></i>';
    delBtn.addEventListener('click', function () {
      row.remove();
      ri();
    });

    row.appendChild(nameWrap);
    row.appendChild(doseWrap);
    row.appendChild(freqCol);
    row.appendChild(qtyInput);
    row.appendChild(delBtn);

    return row;
  }

  if (addMedBtn && rxMedsList) {
    addMedBtn.addEventListener('click', function () {
      rxMedsList.appendChild(createMedRow());
      ri();
    });
    // Add one row by default
    rxMedsList.appendChild(createMedRow());
    ri();
  }

  /* ============================================================
     RX: GENERATE Rx PREVIEW
  ============================================================ */
  var generateRxBtn = document.getElementById('generate-rx-btn');
  if (generateRxBtn) {
    generateRxBtn.addEventListener('click', function () {
      var patientId = document.getElementById('rx-patient').value;
      var patient = findPatientLocal(patientId);
      var patientLabel = patient ? (patient.name || '—') : '—';
      var age = (document.getElementById('rx-age').value) || '—';
      var date = (document.getElementById('rx-date').value) || todayStr;
      var diag = (document.getElementById('rx-diagnosis').value) || '—';
      var notes = (document.getElementById('rx-notes').value) || '—';

      document.getElementById('preview-patient').textContent = patientLabel;
      document.getElementById('preview-age').textContent = age;
      document.getElementById('preview-date').textContent = date;
      document.getElementById('preview-diag').textContent = diag;
      document.getElementById('preview-notes').textContent = notes;

      var medsList = document.getElementById('preview-meds-list');
      medsList.innerHTML = '';
      var medications = [];
      var rows = qsa('.rx-med-row', rxMedsList);
      rows.forEach(function (row) {
        var name = row.querySelector('input[type="text"]').value;
        var sel = row.querySelector('select');
        var dose = sel ? sel.value : '';
        if (dose === 'Custom') {
          var ci = row.querySelectorAll('input[type="text"]')[1];
          dose = ci ? ci.value : '';
        }
        var freq = [];
        row.querySelectorAll('input[type="checkbox"]:checked').forEach(function (cb) {
          freq.push(cb.parentElement.textContent.trim());
        });
        var customFreq = row.querySelector('.freq-custom-input');
        if (customFreq && customFreq.value.trim()) freq.push(customFreq.value.trim());
        var qty = row.querySelector('.med-qty-input').value;
        if (!name) return;
        var med = { name: name, dose: dose, frequency: freq.join(', '), qty: qty };
        medications.push(med);
        var li = document.createElement('li');
        li.textContent = name + ' ' + dose + (freq.length ? ' — ' + freq.join(', ') : '') + (qty ? ' · Qty: ' + qty : '');
        medsList.appendChild(li);
      });

      if (!patientId) {
        showToast('Please select a patient first.');
        return;
      }

      apiFetch((ROUTES.prescriptionsStore || '').replace('__ID__', patientId), {
        method: 'POST',
        body: JSON.stringify({ diagnosis: diag, medications: medications, notes: notes, status: 'Draft' }),
      }).then(function (data) {
        showToast(data.message || 'Prescription saved.');
      }).catch(function (err) {
        showToast(err.message || 'Unable to save prescription.');
      });

      ri();
    });
  }

  /* ============================================================
     RX / DX: TEMPLATE LIBRARY (DB-backed)
  ============================================================ */
  function normalizeTemplate(t) {
    var payload = t.payload || {};
    return {
      id: t.id,
      type: t.type,
      name: t.name,
      tag: t.tag || '',
      tagClass: t.tagClass || t.tag_class || 'tag-psychiatric',
      desc: t.desc || t.description || '',
      diag: t.diag || payload.diag || '',
      meds: t.meds || payload.meds || [],
      tests: t.tests || payload.tests || [],
      payload: payload,
    };
  }

  function upsertTemplateLocal(template) {
    var t = normalizeTemplate(template);
    var list = t.type === 'dx' ? DX_TEMPLATES : RX_TEMPLATES;
    var idx = list.findIndex(function (item) { return String(item.id) === String(t.id); });
    if (idx >= 0) list[idx] = t;
    else list.push(t);
    if (t.type === 'dx') DX_TEMPLATES = list;
    else RX_TEMPLATES = list;
  }

  function removeTemplateLocal(type, id) {
    if (type === 'dx') {
      DX_TEMPLATES = DX_TEMPLATES.filter(function (t) { return String(t.id) !== String(id); });
    } else {
      RX_TEMPLATES = RX_TEMPLATES.filter(function (t) { return String(t.id) !== String(id); });
    }
  }

  function buildTemplateItem(t, onApply) {
    var div = document.createElement('div');
    div.className = 'template-item';
    div.innerHTML = '<div class="template-item-top">' +
      '<span class="template-name">' + (t.name || 'Template') + '</span>' +
      (t.tag ? '<span class="template-tag ' + (t.tagClass || '') + '">' + t.tag + '</span>' : '') +
      '</div>' +
      '<div class="template-desc">' + (t.desc || '') + '</div>' +
      '<div class="template-actions" style="display:flex;gap:6px;flex-wrap:wrap;margin-top:4px;">' +
      '<button type="button" class="btn-outline-sm tpl-apply">+ Apply</button>' +
      '<button type="button" class="btn-outline-sm tpl-edit">Edit</button>' +
      '<button type="button" class="btn-outline-sm tpl-delete" style="color:#b91c1c;">Delete</button>' +
      '</div>';
    div.querySelector('.tpl-apply').addEventListener('click', function () { onApply(t); });
    div.querySelector('.tpl-edit').addEventListener('click', function () { openTemplateModal(t.type, t); });
    div.querySelector('.tpl-delete').addEventListener('click', function () { deleteTemplate(t); });
    return div;
  }

  function buildRxTemplates() {
    var list = document.getElementById('rx-template-list');
    if (!list) return;
    list.innerHTML = '';
    if (!RX_TEMPLATES.length) {
      list.innerHTML = '<div class="template-item" style="color:#64748b;">No Rx templates yet. Click Manage / Add.</div>';
      return;
    }
    RX_TEMPLATES.map(normalizeTemplate).forEach(function (t) {
      list.appendChild(buildTemplateItem(t, applyRxTemplate));
    });
  }

  function buildDxTemplates() {
    var list = document.getElementById('dx-template-list');
    if (!list) return;
    list.innerHTML = '';
    if (!DX_TEMPLATES.length) {
      list.innerHTML = '<div class="template-item" style="color:#64748b;">No diagnostic templates yet. Click Manage / Add.</div>';
      return;
    }
    DX_TEMPLATES.map(normalizeTemplate).forEach(function (t) {
      list.appendChild(buildTemplateItem(t, applyDxTemplate));
    });
  }

  function applyRxTemplate(t) {
    if (!rxMedsList) return;
    t = normalizeTemplate(t);
    rxMedsList.innerHTML = '';
    (t.meds || []).forEach(function (m) {
      rxMedsList.appendChild(createMedRow(m));
    });
    if (rxDiagInput) rxDiagInput.value = t.diag || '';
    ri();
    showToast('Template "' + t.name + '" applied.');
  }

  function applyDxTemplate(t) {
    t = normalizeTemplate(t);
    qsa('.dx-cb').forEach(function (cb) { cb.checked = false; });
    (t.tests || []).forEach(function (test) {
      var cb = qs('.dx-cb[data-test="' + test + '"]');
      if (cb) cb.checked = true;
    });
    showToast('Template "' + t.name + '" applied.');
  }

  function parseMedsText(text) {
    return String(text || '').split('\n').map(function (line) {
      line = line.trim();
      if (!line) return null;
      var parts = line.split('|').map(function (p) { return p.trim(); });
      return {
        name: parts[0] || '',
        dose: parts[1] || '',
        freq: parts[2] ? parts[2].split(',').map(function (f) { return f.trim(); }).filter(Boolean) : [],
        qty: parts[3] ? Number(parts[3]) || parts[3] : '',
      };
    }).filter(Boolean);
  }

  function medsToText(meds) {
    return (meds || []).map(function (m) {
      return [m.name || '', m.dose || '', Array.isArray(m.freq) ? m.freq.join(', ') : (m.freq || ''), m.qty || ''].join(' | ');
    }).join('\n');
  }

  function parseTestsText(text) {
    return String(text || '')
      .split(/[\n,]+/)
      .map(function (t) { return t.trim(); })
      .filter(Boolean);
  }

  function openTemplateModal(type, template) {
    setVal('tpl-id', template ? template.id : '');
    setVal('tpl-type', type);
    setVal('tpl-name', template ? template.name : '');
    setVal('tpl-tag', template ? template.tag : '');
    setVal('tpl-desc', template ? (template.desc || '') : '');
    setVal('tpl-diag', template ? (template.diag || '') : '');
    setVal('tpl-meds', template ? medsToText(template.meds) : '');
    setVal('tpl-tests', template ? (template.tests || []).join(', ') : '');

    var title = document.getElementById('template-modal-title');
    if (title) title.textContent = (template ? 'Edit' : 'Add') + (type === 'dx' ? ' Diagnostic Template' : ' Rx Template');

    var diagWrap = document.getElementById('tpl-diag-wrap');
    var medsWrap = document.getElementById('tpl-meds-wrap');
    var testsWrap = document.getElementById('tpl-tests-wrap');
    if (type === 'dx') {
      if (diagWrap) hide(diagWrap);
      if (medsWrap) hide(medsWrap);
      if (testsWrap) show(testsWrap);
    } else {
      if (diagWrap) show(diagWrap);
      if (medsWrap) show(medsWrap);
      if (testsWrap) hide(testsWrap);
    }
    openModal('template-modal');
  }

  function deleteTemplate(t) {
    if (!t.id) return;
    if (!window.confirm('Delete template "' + t.name + '"?')) return;
    var base = (window.PSYCH_ROUTES || {}).templatesUpdate || '/psychiatrist/clinical-templates';
    apiFetch(base + '/' + t.id, { method: 'DELETE' })
      .then(function (data) {
        removeTemplateLocal(t.type, t.id);
        buildRxTemplates();
        buildDxTemplates();
        showToast(data.message || 'Template deleted.');
      })
      .catch(function (err) { showToast(err.message); });
  }

  var rxTemplateAddBtn = document.getElementById('rx-template-add-btn');
  if (rxTemplateAddBtn) rxTemplateAddBtn.addEventListener('click', function () { openTemplateModal('rx'); });
  var dxTemplateAddBtn = document.getElementById('dx-template-add-btn');
  if (dxTemplateAddBtn) dxTemplateAddBtn.addEventListener('click', function () { openTemplateModal('dx'); });

  var tplSaveBtn = document.getElementById('tpl-save-btn');
  if (tplSaveBtn) {
    tplSaveBtn.addEventListener('click', function () {
      var id = getVal('tpl-id');
      var type = getVal('tpl-type') || 'rx';
      var name = getVal('tpl-name').trim();
      if (!name) { showToast('Template name is required.'); return; }

      var payload = {
        type: type,
        name: name,
        tag: getVal('tpl-tag').trim() || null,
        description: getVal('tpl-desc').trim() || null,
      };
      if (type === 'dx') {
        payload.tests = parseTestsText(getVal('tpl-tests'));
      } else {
        payload.diag = getVal('tpl-diag').trim() || null;
        payload.meds = parseMedsText(getVal('tpl-meds'));
      }

      var routes = window.PSYCH_ROUTES || {};
      var url = id
        ? ((routes.templatesUpdate || '/psychiatrist/clinical-templates') + '/' + id)
        : (routes.templatesStore || '/psychiatrist/clinical-templates');
      var method = id ? 'PUT' : 'POST';

      apiFetch(url, { method: method, body: JSON.stringify(payload) })
        .then(function (data) {
          upsertTemplateLocal(data.template);
          buildRxTemplates();
          buildDxTemplates();
          closeModal('template-modal');
          showToast(data.message || 'Template saved.');
        })
        .catch(function (err) { showToast(err.message); });
    });
  }

  /* ============================================================
     RX: PRINT
  ============================================================ */
  window.printRx = function () {
    var el = document.getElementById('print-area-rx');
    if (!el) return;
    var win = window.open('', '_blank', 'width=600,height=700');
    win.document.write('<html><head><title>MedCare Rx</title><style>body{font-family:sans-serif;padding:40px;font-size:13px;color:#0f172a;}.rx-preview-stamp{font-size:32px;font-weight:900;color:#2563eb;font-style:italic;}.rx-sig-line-bar{height:1px;background:#0f172a;width:140px;margin-bottom:4px;}</style></head><body>');
    win.document.write(el.innerHTML);
    win.document.write('</body></html>');
    win.document.close();
    win.print();
  };

  /* ============================================================
     DIAGNOSTIC REQUEST
  ============================================================ */
  function buildDxChecklist() {
    var list = document.getElementById('dx-checklist');
    if (!list) return;
    list.innerHTML = '';
    DX_LAB_GROUPS.forEach(function (g) {
      var cat = document.createElement('div');
      cat.className = 'dx-cat-label';
      cat.textContent = g.cat;
      list.appendChild(cat);
      g.tests.forEach(function (test) {
        var lbl = document.createElement('label');
        lbl.className = 'dx-check-item';
        var cb = document.createElement('input');
        cb.type = 'checkbox';
        cb.className = 'dx-cb';
        cb.setAttribute('data-test', test);
        lbl.appendChild(cb);
        lbl.appendChild(document.createTextNode(test));
        list.appendChild(lbl);
      });
    });
  }

  var generateDxBtn = document.getElementById('generate-dx-btn');
  if (generateDxBtn) {
    generateDxBtn.addEventListener('click', function () {
      var patientId = document.getElementById('dx-patient').value;
      var patient = findPatientLocal(patientId);
      var patientLabelText = patient ? (patient.name || '—') : (document.getElementById('dx-patient-search').value || '—');
      var date = document.getElementById('dx-date').value || todayStr;
      var notes = document.getElementById('dx-notes').value || '—';

      document.getElementById('dx-prev-patient').textContent = patientLabelText;
      document.getElementById('dx-prev-date').textContent = date;
      document.getElementById('dx-prev-notes').textContent = notes;

      var testList = document.getElementById('dx-prev-tests');
      testList.innerHTML = '';
      qsa('.dx-cb:checked').forEach(function (cb) {
        var li = document.createElement('li');
        li.textContent = cb.getAttribute('data-test');
        testList.appendChild(li);
      });
      var otherImg = document.getElementById('dx-other-imaging');
      if (otherImg && otherImg.value.trim()) {
        var li2 = document.createElement('li');
        li2.textContent = otherImg.value.trim();
        testList.appendChild(li2);
      }

      if (!patientId) {
        showToast('Please select a patient first.');
        return;
      }
      showToast('Diagnostic request generated.');
    });
  }

  window.printDx = function () {
    var el = document.getElementById('print-area-dx');
    if (!el) return;
    var win = window.open('', '_blank', 'width=600,height=700');
    win.document.write('<html><head><title>MedCare Diagnostic Request</title><style>body{font-family:sans-serif;padding:40px;font-size:13px;color:#0f172a;}.rx-sig-line-bar{height:1px;background:#0f172a;width:140px;margin-bottom:4px;}</style></head><body>');
    win.document.write(el.innerHTML);
    win.document.write('</body></html>');
    win.document.close();
    win.print();
  };

  /* ============================================================
     MODALS
  ============================================================ */
  function openModal(id) {
    var el = document.getElementById(id);
    if (el) { show(el); ri(); }
  }

  function closeModal(id) {
    var el = document.getElementById(id);
    if (el) hide(el);
  }

  // Open buttons
  var addPatientBtn = document.getElementById('add-patient-btn');
  if (addPatientBtn) addPatientBtn.addEventListener('click', function () { openModal('add-patient-modal'); });

  var scheduleConsultBtn = document.getElementById('schedule-consult-btn');
  if (scheduleConsultBtn) scheduleConsultBtn.addEventListener('click', function () {
    populateConsultPatientSelect();
    openModal('schedule-consult-modal');
  });

  var logoutBtn = document.getElementById('logout-btn');
  if (logoutBtn) logoutBtn.addEventListener('click', function () { openModal('logout-modal'); });

  // Close buttons (data-close attribute)
  document.addEventListener('click', function (e) {
    var cl = e.target.closest('[data-close]');
    if (cl) closeModal(cl.getAttribute('data-close'));
    // Click outside modal box
    if (e.target.classList.contains('modal-overlay')) {
      var modals = ['add-patient-modal', 'schedule-consult-modal', 'record-modal', 'profile-modal', 'edit-consult-modal', 'patient-detail-modal', 'template-modal', 'logout-modal'];
      modals.forEach(function (m) { closeModal(m); });
    }
  });

  /* ============================================================
     SAVE PATIENT
  ============================================================ */
  var savePatientBtn = document.getElementById('save-patient-btn');
  if (savePatientBtn) {
    savePatientBtn.addEventListener('click', function () {
      var name = getVal('new-name').trim();
      if (!name) { showToast('Please enter patient name.'); return; }
      var payload = {
        name: name,
        age: getVal('new-age') ? parseInt(getVal('new-age'), 10) : null,
        sex: getVal('new-sex') || 'female',
        chief_complaint: getVal('new-complaint'),
        life_coach_id: getVal('new-coach') || null,
      };
      apiFetch((window.PSYCH_ROUTES || {}).patientsStore || '/psychiatrist/patients', {
        method: 'POST',
        body: JSON.stringify(payload),
      }).then(function (data) {
        upsertPatientLocal(data.patient);
        buildPatients(PATIENTS);
        populateConsultPatientSelect();
        closeModal('add-patient-modal');
        showToast(data.message || ('Patient "' + name + '" added successfully.'));
        ['new-name', 'new-age', 'new-complaint'].forEach(function (id) { setVal(id, ''); });
      }).catch(function (err) { showToast(err.message); });
    });
  }

  /* ============================================================
     SAVE CONSULTATION
  ============================================================ */
  var saveConsultBtn = document.getElementById('save-consult-btn');
  if (saveConsultBtn) {
    saveConsultBtn.addEventListener('click', function () {
      var patientId = getVal('consult-patient');
      var date = getVal('consult-date');
      var time = getVal('consult-time');
      var type = getVal('consult-type');
      var notes = getVal('consult-notes');
      if (!patientId) { showToast('Please select a patient.'); return; }
      if (!date || !time) { showToast('Please select date and time.'); return; }
      apiFetch((window.PSYCH_ROUTES || {}).consultationsStore || '/psychiatrist/consultations', {
        method: 'POST',
        body: JSON.stringify({
          patient_record_id: parseInt(patientId, 10),
          date: date,
          time: time,
          type: type,
          notes: notes,
        }),
      }).then(function (data) {
        CONSULTATIONS.unshift(data.consultation);
        buildConsultations(CONSULTATIONS);
        closeModal('schedule-consult-modal');
        showToast(data.message || 'Consultation scheduled.');
      }).catch(function (err) { showToast(err.message); });
    });
  }

  /* ============================================================
     INITIAL RENDER
  ============================================================ */
  populateConsultPatientSelect();
  bindPatientSaves();
  buildPatients(PATIENTS);
  buildConsultations(CONSULTATIONS);
  buildLifestyle();
  buildAssessments(ASSESSMENTS);
  buildRxTemplates();
  buildDxChecklist();
  buildDxTemplates();
  initAccordion();
  initEditConsultModal();

  // Deep-link support from other pages (?patient=P001)
  (function applyPatientQuery() {
    var params = new URLSearchParams(window.location.search);
    var patientId = params.get('patient');
    if (!patientId) return;

    var page = document.body.getAttribute('data-page');
    var p = findPatientLocal(patientId);

    function selectRx(patient) {
      if (rxPatientPicker) rxPatientPicker.setSelected(patient);
      else {
        setVal('rx-patient', patient.id);
        setVal('rx-patient-search', patientLabel(patient));
        if (patient.age != null) setVal('rx-age', patient.age);
      }
    }

    if (page === 'prescriptions') {
      if (p) {
        selectRx(p);
      } else {
        searchPatientsApi(String(patientId)).then(function (matches) {
          var found = matches.find(function (item) {
            return String(item.id) === String(patientId) || String(item.patient_id) === String(patientId);
          }) || matches[0];
          if (found) selectRx(found);
        });
      }
    }

    if (page === 'assessments') {
      setTimeout(function () { openAssessDetail(patientId); }, 50);
    }

    if (page === 'records') {
      setTimeout(function () { openRecord(patientId); }, 50);
    }

    if (page === 'lifestyle') {
      setTimeout(function () { openLifestylePatient(patientId); }, 50);
    }
  })();

  ri();

});

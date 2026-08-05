/* psychiatrist_core.js — Shared: data, helpers, apiFetch, modals, navigation, patient picker */

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


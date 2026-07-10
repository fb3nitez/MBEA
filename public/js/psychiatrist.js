/* ============================================================
   MEDCARE PSYCHIATRIST DASHBOARD — psychiatrist_dashboard.js
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  /* ============================================================
     DATA
  ============================================================ */

  var PATIENTS = [
    { id: 'P001', name: 'Sarah Johnson', age: 34, sex: 'Female', status: 'Active', coach: 'Michael Chen', complaint: 'Sleep disturbances, anxiety, lifestyle imbalance' },
    { id: 'P002', name: 'Robert Martinez', age: 45, sex: 'Male', status: 'Critical', coach: 'Emily Roberts', complaint: 'Depression symptoms and suicidal ideation' },
    { id: 'P003', name: 'Emily Thompson', age: 28, sex: 'Female', status: 'Active', coach: 'Michael Chen', complaint: 'Chronic anxiety and stress management' },
    { id: 'P004', name: 'David Lee', age: 52, sex: 'Male', status: 'Active', coach: 'Emily Roberts', complaint: 'Bipolar disorder management' },
    { id: 'P005', name: 'Maria Garcia', age: 31, sex: 'Female', status: 'Active', coach: 'Michael Chen', complaint: 'PTSD following workplace trauma' },
  ];

  var CONSULTATIONS = [
    { patient: 'Sarah Johnson', date: '2026-06-07', time: '9:00 AM', type: 'Follow-up', status: 'Scheduled', notes: 'Follow-up for medication review' },
    { patient: 'Robert Martinez', date: '2026-06-07', time: '10:30 AM', type: 'Emergency', status: 'Scheduled', notes: 'Crisis intervention — suicidal ideation reported' },
    { patient: 'Emily Thompson', date: '2026-06-06', time: '2:00 PM', type: 'Initial', status: 'Completed', notes: 'Initial psychiatric assessment completed' },
    { patient: 'David Lee', date: '2026-06-05', time: '11:00 AM', type: 'Follow-up', status: 'Completed', notes: 'Medication adjustment for mood stabilization' },
  ];

  var RECORDS = [
    { id: 'P001', name: 'Sarah Johnson', diag: 'Anxiety Disorder, Insomnia', updated: 'Jun 5, 2026', complaint: 'Persistent insomnia and anxiety related to work stress.' },
    { id: 'P002', name: 'Robert Martinez', diag: 'Major Depressive Disorder', updated: 'Jun 7, 2026', complaint: 'Depression with passive suicidal ideation.' },
    { id: 'P003', name: 'Emily Thompson', diag: 'Generalized Anxiety Disorder', updated: 'Jun 4, 2026', complaint: 'Chronic anxiety and panic attacks.' },
    { id: 'P004', name: 'David Lee', diag: 'Bipolar I Disorder', updated: 'Jun 3, 2026', complaint: 'Mood swings, manic and depressive episodes.' },
    { id: 'P005', name: 'Maria Garcia', diag: 'Post-Traumatic Stress Disorder', updated: 'Jun 1, 2026', complaint: 'Flashbacks and hyperarousal following workplace trauma.' },
  ];

  var LIFESTYLE = [
    {
      name: 'Sarah Johnson', metrics: [
        { label: 'Sleep', value: '7.5 hrs', pct: 75, color: 'bar-green' },
        { label: 'Exercise', value: '4×/week', pct: 50, color: 'bar-amber' },
        { label: 'Nutrition', value: 'Good', pct: 80, color: 'bar-green' },
        { label: 'Stress', value: 'Moderate', pct: 60, color: 'bar-amber' },
      ]
    },
    {
      name: 'Emily Thompson', metrics: [
        { label: 'Sleep', value: '5.5 hrs', pct: 55, color: 'bar-amber' },
        { label: 'Exercise', value: '2×/week', pct: 25, color: 'bar-red' },
        { label: 'Nutrition', value: 'Fair', pct: 60, color: 'bar-amber' },
        { label: 'Stress', value: 'High', pct: 85, color: 'bar-red' },
      ]
    },
  ];

  var ASSESSMENTS = [
    { id: 'P001', name: 'Sarah Johnson', age: '34y', sex: 'Female', diag: 'Major Depressive Disorder (F32.1)', status: 'Stable', prog: 85, lastDate: '2026-05-28', tag: 'Cognitive Behavioral Coaching', provider: 'Dr. Maria Santos · Psychiatrist' },
    { id: 'P002', name: 'David Martinez', age: '42y', sex: 'Male', diag: 'Generalized Anxiety Disorder (F41.1)', status: 'Maintenance', prog: 70, lastDate: '2026-05-20', tag: 'Stress Management', provider: 'Emily Roberts · Life Coach' },
    { id: 'P003', name: 'Emily Thompson', age: '28y', sex: 'Female', diag: 'Panic Disorder with Agoraphobia (F40.01)', status: 'Critical', prog: 60, lastDate: '2026-06-08', tag: null, provider: 'Dr. Maria Santos · Psychiatrist' },
    { id: 'P004', name: 'James Wilson', age: '55y', sex: 'Male', diag: 'Insomnia Disorder (G47.00)', status: 'Life Coaching', prog: 50, lastDate: '2026-05-15', tag: 'Sleep Wellness & Lifestyle Medicine', provider: 'Michael Chen · Life Coach' },
  ];

  var INTAKES = [
    { id: 'P001', name: 'Sarah Johnson', age: 34, sex: 'Female', complaint: 'Sleep disturbances, anxiety', status: 'Active' },
    { id: 'P002', name: 'Robert Martinez', age: 45, sex: 'Male', complaint: 'Depression symptoms and suicidal ideation', status: 'Pending' },
    { id: 'P003', name: 'Emily Thompson', age: 28, sex: 'Female', complaint: 'Chronic anxiety, stress management', status: 'Active' },
    { id: 'P004', name: 'David Lee', age: 52, sex: 'Male', complaint: 'Bipolar disorder management', status: 'Active' },
    { id: 'P005', name: 'Maria Garcia', age: 31, sex: 'Female', complaint: 'PTSD following trauma', status: 'Active' },
  ];

  var RX_TEMPLATES = [
    { name: 'MDD — First Line SSRI', tag: 'Depression', tagClass: 'tag-depression', desc: 'Sertraline 50mg', meds: [{ name: 'Sertraline', dose: '50mg', freq: ['Morning'], qty: 30 }], diag: 'F32.1 Major Depressive Disorder' },
    { name: 'GAD — Sertraline + PRN Clonazepam', tag: 'Anxiety', tagClass: 'tag-anxiety', desc: 'Sertraline 50mg · Clonazepam 0.5mg', meds: [{ name: 'Sertraline', dose: '50mg', freq: ['Morning'], qty: 30 }, { name: 'Clonazepam', dose: '0.5mg', freq: ['Bedtime'], qty: 10 }], diag: 'F41.1 Generalized Anxiety Disorder' },
    { name: 'Schizophrenia — Risperidone Starter', tag: 'Psychosis', tagClass: 'tag-psychosis', desc: 'Risperidone 2mg · Biperiden 2mg', meds: [{ name: 'Risperidone', dose: '2mg', freq: ['Morning', 'Dinner'], qty: 60 }, { name: 'Biperiden', dose: '2mg', freq: ['Morning'], qty: 30 }], diag: 'F20.9 Schizophrenia' },
    { name: 'Bipolar — Mood Stabilizer', tag: 'Bipolar', tagClass: 'tag-bipolar', desc: 'Valproic Acid 500mg · Quetiapine 100mg', meds: [{ name: 'Valproic Acid', dose: '500mg', freq: ['Morning', 'Dinner'], qty: 60 }, { name: 'Quetiapine', dose: '100mg', freq: ['Bedtime'], qty: 30 }], diag: 'F31.0 Bipolar I Disorder' },
  ];

  var DX_TEMPLATES = [
    { name: 'Psychiatric Baseline Panel', tag: 'Psychiatric', tagClass: 'tag-psychiatric', desc: 'CBC, TSH, FBS, Lipid Profile, Urinalysis', tests: ['CBC with differential', 'TSH', 'Fasting Blood Sugar', 'Lipid Profile', 'Complete urinalysis'] },
    { name: 'Mood Disorder Workup', tag: 'Anxiety', tagClass: 'tag-anxiety', desc: 'CBC, TSH, Free T3/T4, Sodium, Lithium', tests: ['CBC with differential', 'TSH', 'Free T3', 'Free T4', 'Sodium'] },
    { name: 'Metabolic Monitoring', tag: 'Metabolic', tagClass: 'tag-metabolic', desc: 'FBS, HbA1c, Lipid Profile, Creatinine', tests: ['Fasting Blood Sugar', 'HbA1c', 'Lipid Profile', 'Creatinine', 'eGFR'] },
    { name: 'Comprehensive Workup', tag: 'Comprehensive', tagClass: 'tag-comprehensive', desc: 'All CBC, Thyroid, Liver, Renal + X-ray', tests: ['CBC with differential', 'TSH', 'Free T3', 'Free T4', 'AST', 'ALT', 'BUN', 'Creatinine', 'Fasting Blood Sugar', 'HbA1c', 'Lipid Profile', 'Chest X-ray'] },
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
    var map = { Active: 'badge-active', Critical: 'badge-critical', Inactive: 'badge-inactive', Pending: 'badge-pending', Completed: 'badge-completed', Scheduled: 'badge-scheduled', Emergency: 'badge-emergency', Stable: 'badge-stable', Monitoring: 'badge-monitoring', Maintenance: 'badge-maintenance' };
    return '<span class="badge ' + (map[s] || 'badge-outline') + '">' + s + '</span>';
  }

  /* ============================================================
     SECTION SWITCHING
  ============================================================ */
  var SECTION_TITLES = {
    dashboard: 'Dashboard', patients: 'Patient Management', consultations: 'Consultations',
    records: 'Medical Records', lifestyle: 'Lifestyle Monitoring', assessments: 'Biopsychosociospiritual Assessments', prescriptions: 'Prescriptions'
  };

  function switchSection(key) {
    qsa('.psych-section').forEach(function (s) { s.classList.remove('active'); });
    qsa('.nav-item').forEach(function (n) { n.classList.remove('active'); });
    var sec = document.getElementById('section-' + key);
    if (sec) sec.classList.add('active');
    var btn = qs('[data-section="' + key + '"]');
    if (btn) btn.classList.add('active');
    var title = document.getElementById('page-title');
    if (title) title.textContent = SECTION_TITLES[key] || key;

    // Close mobile sidebar
    document.getElementById('sidebar').classList.remove('open');
  }

  qsa('.nav-item[data-section]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      switchSection(this.getAttribute('data-section'));
    });
  });

  // "View All" / "goto" buttons
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-goto]');
    if (el) switchSection(el.getAttribute('data-goto'));
  });

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

  /* ============================================================
     LOGOUT / PROFILE
  ============================================================ */
  var logoutBtn = document.getElementById('logout-btn');
  if (logoutBtn) logoutBtn.addEventListener('click', function () { window.location.href = '/'; });

  var profileBtn = document.getElementById('profile-btn');
  if (profileBtn) profileBtn.addEventListener('click', function () { openModal('profile-modal'); });

  /* ============================================================
     INTAKE TIME
  ============================================================ */
  var intakeTime = now();

  /* ============================================================
     BUILD INTAKES TABLE
  ============================================================ */
  function buildIntakes() {
    var tbody = document.getElementById('intakes-tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    INTAKES.forEach(function (p) {
      var tr = document.createElement('tr');
      tr.innerHTML = '<td class="td-name">' + p.name + '</td>' +
        '<td>' + p.age + ' / ' + p.sex + '</td>' +
        '<td>' + p.complaint + '</td>' +
        '<td>' + intakeTime + '</td>' +
        '<td>' + statusBadge(p.status) + '</td>' +
        '<td><button class="btn-outline-sm" onclick="openPatientDetail(\'' + p.id + '\')">View Details</button></td>';
      tbody.appendChild(tr);
    });
  }

  window.openPatientDetail = function (id) {
    var p = PATIENTS.find(function (x) { return x.id === id; });
    if (!p) return;
    showToast('Viewing ' + p.name + ' — ' + p.id);
  };

  /* ============================================================
     BUILD PATIENTS TABLE
  ============================================================ */
  function buildPatients(list) {
    var tbody = document.getElementById('patients-tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    list.forEach(function (p) {
      var tr = document.createElement('tr');
      tr.innerHTML = '<td class="td-id">' + p.id + '</td>' +
        '<td class="td-name">' + p.name + '</td>' +
        '<td>' + p.age + '</td>' +
        '<td>' + statusBadge(p.status) + '</td>' +
        '<td>' + p.coach + '</td>' +
        '<td class="td-complaint">' + p.complaint + '</td>' +
        '<td><button class="btn-outline-sm" onclick="viewPatient(\'' + p.id + '\')"><i data-feather="eye" style="width:12px;height:12px;vertical-align:middle;margin-right:4px;"></i>View</button></td>';
      tbody.appendChild(tr);
    });
    ri();
  }

  window.viewPatient = function (id) {
    var p = PATIENTS.find(function (x) { return x.id === id; });
    if (!p) return;

    // Avatar initials
    var initials = p.name.split(' ').map(function (w) { return w[0]; }).join('').slice(0, 2).toUpperCase();
    var avatarEl = document.getElementById('pm-avatar');
    if (avatarEl) avatarEl.textContent = initials;

    // Populate fields
    var set = function (id, val) { var el = document.getElementById(id); if (el) el.textContent = val; };
    set('pm-name', p.name);
    set('pm-sub', p.id + ' · ' + p.status);
    set('pm-id', p.id);
    set('pm-age', p.age);
    set('pm-sex', p.sex);
    set('pm-coach', p.coach);
    set('pm-complaint', p.complaint);

    // Status badge
    var statusEl = document.getElementById('pm-status');
    if (statusEl) statusEl.innerHTML = statusBadge(p.status);

    // Store current patient ID on modal for action buttons
    var modal = document.getElementById('patient-detail-modal');
    if (modal) modal.setAttribute('data-current-patient', id);

    openModal('patient-detail-modal');
  };

  // Patient modal quick-action buttons
  var pmBtnConsult = document.getElementById('pm-btn-consult');
  if (pmBtnConsult) pmBtnConsult.addEventListener('click', function () {
    var modal = document.getElementById('patient-detail-modal');
    var id = modal ? modal.getAttribute('data-current-patient') : null;
    var p = id ? PATIENTS.find(function (x) { return x.id === id; }) : null;
    closeModal('patient-detail-modal');
    if (p) {
      var sel = document.getElementById('consult-patient');
      if (sel) {
        for (var i = 0; i < sel.options.length; i++) {
          if (sel.options[i].value === p.name) { sel.selectedIndex = i; break; }
        }
      }
    }
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
    var p = id ? PATIENTS.find(function (x) { return x.id === id; }) : null;
    closeModal('patient-detail-modal');
    switchSection('prescriptions');
    if (p) {
      var sel = document.getElementById('rx-patient');
      if (sel) {
        var targetVal = p.name + ' (' + p.id + ')';
        for (var i = 0; i < sel.options.length; i++) {
          if (sel.options[i].value === targetVal) { sel.selectedIndex = i; break; }
        }
      }
    }
  });

  var pmBtnAssess = document.getElementById('pm-btn-assess');
  if (pmBtnAssess) pmBtnAssess.addEventListener('click', function () {
    var modal = document.getElementById('patient-detail-modal');
    var id = modal ? modal.getAttribute('data-current-patient') : null;
    closeModal('patient-detail-modal');
    switchSection('assessments');
    if (id) {
      setTimeout(function () { openAssessDetail(id); }, 50);
    }
  });

  // Patient search + filter
  function filterPatients() {
    var q = (document.getElementById('patient-search').value || '').toLowerCase();
    var st = document.getElementById('patient-status-filter').value;
    var list = PATIENTS.filter(function (p) {
      var matchQ = !q || p.name.toLowerCase().includes(q) || p.id.toLowerCase().includes(q);
      var matchS = !st || p.status === st;
      return matchQ && matchS;
    });
    buildPatients(list);
  }

  var psearch = document.getElementById('patient-search');
  var psfilter = document.getElementById('patient-status-filter');
  if (psearch) psearch.addEventListener('input', filterPatients);
  if (psfilter) psfilter.addEventListener('change', filterPatients);

  /* ============================================================
     BUILD CONSULTATIONS TABLE
  ============================================================ */
  function buildConsultations(list) {
    var tbody = document.getElementById('consults-tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    list.forEach(function (c, i) {
      var typeBadge = c.type === 'Emergency' ? '<span class="badge badge-emergency">Emergency</span>' :
        c.type === 'Initial' ? '<span class="badge badge-outline">Initial</span>' :
          '<span class="badge badge-outline">Follow-up</span>';
      var statBadge = c.status === 'Completed' ? '<span class="badge badge-completed">Completed</span>' :
        '<span class="badge badge-scheduled">Scheduled</span>';
      var tr = document.createElement('tr');
      tr.innerHTML = '<td class="td-name">' + c.patient + '</td>' +
        '<td>' + c.date + '</td>' +
        '<td>' + c.time + '</td>' +
        '<td>' + typeBadge + '</td>' +
        '<td>' + statBadge + '</td>' +
        '<td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="' + c.notes + '">' + (c.notes || '—') + '</td>' +
        '<td><button class="btn-outline-sm" onclick="editConsult(' + i + ')">Edit</button></td>';
      tbody.appendChild(tr);
    });
  }

  window.editConsult = function (i) {
    var c = CONSULTATIONS[i];
    if (!c) return;

    // Store index on modal for save
    var modal = document.getElementById('edit-consult-modal');
    if (modal) modal.setAttribute('data-consult-index', i);

    // Header
    var titleEl = document.getElementById('ec-modal-title');
    var subEl = document.getElementById('ec-modal-sub');
    if (titleEl) titleEl.textContent = 'Edit Consultation';
    if (subEl) subEl.textContent = c.patient + ' · ' + c.type;

    // Patient strip
    var nameEl = document.getElementById('ec-patient-name');
    if (nameEl) nameEl.textContent = c.patient;

    var typeBadgeEl = document.getElementById('ec-type-badge');
    if (typeBadgeEl) {
      var tc = c.type === 'Emergency' ? 'badge-emergency' : 'badge-outline';
      typeBadgeEl.innerHTML = '<span class="badge ' + tc + '">' + c.type + '</span>';
    }

    var statBadgeEl = document.getElementById('ec-status-badge');
    if (statBadgeEl) {
      var sc = c.status === 'Completed' ? 'badge-completed' : 'badge-scheduled';
      statBadgeEl.innerHTML = '<span class="badge ' + sc + '">' + c.status + '</span>';
    }

    // Fill editable fields
    var dateEl = document.getElementById('ec-date');
    var timeEl = document.getElementById('ec-time');
    var typeEl = document.getElementById('ec-type');
    var statusEl = document.getElementById('ec-status');
    var notesEl = document.getElementById('ec-notes');

    if (dateEl) dateEl.value = c.date || '';
    if (notesEl) notesEl.value = c.notes || '';

    // Convert "9:00 AM" → "09:00" for time input
    if (timeEl && c.time) {
      var tp = c.time.match(/(\d+):(\d+)\s*(AM|PM)/i);
      if (tp) {
        var hh = parseInt(tp[1]);
        var mm = tp[2];
        var ap = tp[3].toUpperCase();
        if (ap === 'PM' && hh !== 12) hh += 12;
        if (ap === 'AM' && hh === 12) hh = 0;
        timeEl.value = (hh < 10 ? '0' : '') + hh + ':' + mm;
      }
    }

    if (typeEl) {
      for (var i2 = 0; i2 < typeEl.options.length; i2++) {
        if (typeEl.options[i2].value === c.type) { typeEl.selectedIndex = i2; break; }
      }
    }
    if (statusEl) {
      for (var i3 = 0; i3 < statusEl.options.length; i3++) {
        if (statusEl.options[i3].value === c.status) { statusEl.selectedIndex = i3; break; }
      }
    }

    // Show/hide outcome section
    var outcomeSection = document.getElementById('ec-outcome-section');
    if (outcomeSection) {
      if (c.status === 'Completed') outcomeSection.classList.remove('hidden');
      else outcomeSection.classList.add('hidden');
    }

    openModal('edit-consult-modal');
    ri();
  };

  function initEditConsultModal() {
    // Toggle outcome section when status changes
    var ecStatus = document.getElementById('ec-status');
    var ecOutcome = document.getElementById('ec-outcome-section');
    if (ecStatus && ecOutcome) {
      ecStatus.addEventListener('change', function () {
        if (this.value === 'Completed') ecOutcome.classList.remove('hidden');
        else ecOutcome.classList.add('hidden');
        ri();
      });
    }

    // Save changes
    var ecSaveBtn = document.getElementById('ec-save-btn');
    if (ecSaveBtn) {
      ecSaveBtn.addEventListener('click', function () {
        var modal = document.getElementById('edit-consult-modal');
        var idx = modal ? parseInt(modal.getAttribute('data-consult-index')) : -1;
        if (idx < 0 || !CONSULTATIONS[idx]) return;

        var dateVal = document.getElementById('ec-date').value;
        var timeInput = document.getElementById('ec-time').value;
        var typeVal = document.getElementById('ec-type').value;
        var statusVal = document.getElementById('ec-status').value;
        var notesVal = document.getElementById('ec-notes').value;

        // Convert 24h "09:00" → "9:00 AM"
        var displayTime = timeInput;
        if (timeInput) {
          var parts = timeInput.split(':');
          var h = parseInt(parts[0]);
          var m = parts[1];
          var ap = h >= 12 ? 'PM' : 'AM';
          h = h % 12 || 12;
          displayTime = h + ':' + m + ' ' + ap;
        }

        CONSULTATIONS[idx].date = dateVal;
        CONSULTATIONS[idx].time = displayTime;
        CONSULTATIONS[idx].type = typeVal;
        CONSULTATIONS[idx].status = statusVal;
        CONSULTATIONS[idx].notes = notesVal;

        buildConsultations(CONSULTATIONS);
        closeModal('edit-consult-modal');
        showToast('Consultation updated for ' + CONSULTATIONS[idx].patient + '.');
      });
    }

    // Delete consultation
    var ecDeleteBtn = document.getElementById('ec-delete-btn');
    if (ecDeleteBtn) {
      ecDeleteBtn.addEventListener('click', function () {
        var modal = document.getElementById('edit-consult-modal');
        var idx = modal ? parseInt(modal.getAttribute('data-consult-index')) : -1;
        if (idx < 0) return;
        var name = CONSULTATIONS[idx].patient;
        if (confirm('Delete consultation for ' + name + '? This cannot be undone.')) {
          CONSULTATIONS.splice(idx, 1);
          buildConsultations(CONSULTATIONS);
          closeModal('edit-consult-modal');
          showToast('Consultation deleted.');
        }
      });
    }
  }

  /* ============================================================
     BUILD RECORDS GRID
  ============================================================ */
  function buildRecords() {
    var grid = document.getElementById('records-grid');
    if (!grid) return;
    grid.innerHTML = '';
    RECORDS.forEach(function (r) {
      var div = document.createElement('div');
      div.className = 'record-card';
      div.innerHTML = '<div style="display:flex;align-items:center;justify-content:space-between;">' +
        '<span class="record-card-name">' + r.name + '</span>' +
        '<span class="record-card-id">' + r.id + '</span>' +
        '</div>' +
        '<div class="record-card-diag">Dx: ' + r.diag + '</div>' +
        '<div class="record-card-date">Last updated: ' + r.updated + '</div>' +
        '<button class="btn-outline-sm" style="margin-top:4px;width:100%;" onclick="openRecord(\'' + r.id + '\')">View Records</button>';
      grid.appendChild(div);
    });
  }

  window.openRecord = function (id) {
    var r = RECORDS.find(function (x) { return x.id === id; });
    if (!r) return;
    document.getElementById('record-modal-title').textContent = r.name + ' — Medical Record';
    document.getElementById('record-complaint').value = r.complaint;
    document.getElementById('record-diagnosis').value = r.diag;
    openModal('record-modal');
  };

  var saveRecordBtn = document.getElementById('save-record-btn');
  if (saveRecordBtn) saveRecordBtn.addEventListener('click', function () {
    closeModal('record-modal');
    showToast('Medical record saved.');
  });

  /* ============================================================
     BUILD LIFESTYLE
  ============================================================ */
  function buildLifestyle() {
    var grid = document.getElementById('lifestyle-grid');
    if (!grid) return;
    grid.innerHTML = '';
    LIFESTYLE.forEach(function (p) {
      var card = document.createElement('div');
      card.className = 'lifestyle-card';
      var html = '<div class="lifestyle-card-name">' + p.name + '</div>';
      p.metrics.forEach(function (m) {
        html += '<div class="metric-row">' +
          '<span class="metric-label">' + m.label + '</span>' +
          '<div class="metric-track"><div class="metric-bar ' + m.color + '" style="width:' + m.pct + '%"></div></div>' +
          '<span class="metric-value">' + m.value + '</span>' +
          '</div>';
      });
      card.innerHTML = html;
      grid.appendChild(card);
    });
  }

  /* ============================================================
     BUILD ASSESSMENTS
  ============================================================ */
  function buildAssessments(list) {
    var grid = document.getElementById('assessment-cards-grid');
    if (!grid) return;
    grid.innerHTML = '';

    var accentMap = {
      Stable: { accent: 'accent-stable', pctClass: 'pct-green', barClass: 'bar-lg-green', progClass: 'prog-green' },
      Monitoring: { accent: 'accent-monitoring', pctClass: 'pct-amber', barClass: 'bar-lg-amber', progClass: 'prog-amber' },
      Critical: { accent: 'accent-critical', pctClass: 'pct-red', barClass: 'bar-lg-red', progClass: 'prog-red' },
      Maintenance: { accent: 'accent-maintenance', pctClass: 'pct-blue', barClass: 'bar-lg-blue', progClass: 'prog-blue' },
      'Life Coaching': { accent: 'accent-coaching', pctClass: 'pct-blue', barClass: 'bar-lg-blue', progClass: 'prog-blue' },
    };

    var statusBadgeMap = {
      Stable: 'badge-stable',
      Monitoring: 'badge-monitoring',
      Critical: 'badge-critical',
      Maintenance: 'badge-maintenance',
      'Life Coaching': 'badge-maintenance',
    };

    list.forEach(function (a) {
      var theme = accentMap[a.status] || accentMap['Maintenance'];
      var badgeClass = statusBadgeMap[a.status] || 'badge-outline';
      var statusLabel = a.status === 'Life Coaching' ? '♥ Life Coaching' : a.status;

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

  // Assessment search/filter
  var assSearch = document.getElementById('assess-search');
  var assSFilter = document.getElementById('assess-status-filter');
  function filterAssessments() {
    var q = (assSearch ? assSearch.value : '').toLowerCase();
    var st = assSFilter ? assSFilter.value : '';
    var list = ASSESSMENTS.filter(function (a) {
      var mq = !q || a.name.toLowerCase().includes(q) || a.id.toLowerCase().includes(q);
      var ms = !st || a.status === st;
      return mq && ms;
    });
    buildAssessments(list);
  }
  if (assSearch) assSearch.addEventListener('input', filterAssessments);
  if (assSFilter) assSFilter.addEventListener('change', filterAssessments);

  // Open assessment detail
  document.addEventListener('click', function (e) {
    var openBtn = e.target.closest('[data-assess-open]');
    if (openBtn) {
      openAssessDetail(openBtn.getAttribute('data-assess-open'));
      return;
    }
    var card = e.target.closest('.assess-card');
    if (card && !e.target.closest('button')) {
      openAssessDetail(card.getAttribute('data-assess-id'));
    }
  });

  function openAssessDetail(id) {
    var a = ASSESSMENTS.find(function (x) { return x.id === id; });
    if (!a) return;
    hide(document.getElementById('assessment-list-view'));
    show(document.getElementById('assessment-detail-view'));
    document.getElementById('assess-detail-name').textContent = a.name;
    document.getElementById('assess-detail-id').textContent = a.id;
    switchAssessTab('biological');
    ri();
  }

  var assessBackBtn = document.getElementById('assess-back-btn');
  if (assessBackBtn) assessBackBtn.addEventListener('click', function () {
    hide(document.getElementById('assessment-detail-view'));
    show(document.getElementById('assessment-list-view'));
    ri();
  });

  // Assessment 6-tab switching
  function switchAssessTab(tab) {
    qsa('.tab-btn').forEach(function (b) { b.classList.remove('active'); });
    qsa('.tab-panel').forEach(function (p) { p.classList.remove('active'); });
    var btn = qs('[data-tab="' + tab + '"]');
    var panel = document.getElementById('tab-' + tab);
    if (btn) btn.classList.add('active');
    if (panel) panel.classList.add('active');
  }

  qsa('.tab-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      switchAssessTab(this.getAttribute('data-tab'));
    });
  });

  /* ============================================================
     ACCORDION (Medical History)
  ============================================================ */
  function initAccordion() {
    qsa('.accordion-trigger').forEach(function (trigger) {
      trigger.addEventListener('click', function () {
        var panel = this.nextElementSibling;
        var isOpen = panel.classList.contains('open');
        // Close all
        qsa('.accordion-trigger').forEach(function (t) {
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
        qsa('.accordion-trigger').forEach(function (t) {
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
  var prayerList = document.getElementById('prayer-list');

  if (addPrayerBtn) {
    addPrayerBtn.addEventListener('click', function () {
      var text = prayerInput ? prayerInput.value.trim() : '';
      if (!text) { showToast('Please enter a prayer point.'); return; }
      var entry = document.createElement('div');
      entry.className = 'prayer-entry';
      var d = new Date();
      var dateStr = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
      entry.innerHTML = '<div class="prayer-meta">' + dateStr + ' · Dr. Maria Santos</div>' +
        '<div class="prayer-text">' + text + '</div>';
      if (prayerList) prayerList.insertBefore(entry, prayerList.firstChild);
      prayerInput.value = '';
      showToast('Prayer point added.');
    });
  }

  /* ============================================================
     INTERVENTION TIMELINE
  ============================================================ */
  var addInterventionBtn = document.getElementById('add-intervention-btn');
  var interventionInput = document.getElementById('intervention-input');
  var interventionTL = document.getElementById('intervention-timeline');

  if (addInterventionBtn) {
    addInterventionBtn.addEventListener('click', function () {
      var text = interventionInput ? interventionInput.value.trim() : '';
      if (!text) { showToast('Please enter an intervention.'); return; }
      var entry = document.createElement('div');
      entry.className = 'timeline-entry';
      var d = new Date();
      var dateStr = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
      entry.innerHTML = '<div class="timeline-dot"></div>' +
        '<div class="timeline-content">' +
        '<div class="timeline-date">' + dateStr + '</div>' +
        '<div class="timeline-text">' + text + '</div>' +
        '</div>';
      if (interventionTL) interventionTL.insertBefore(entry, interventionTL.firstChild);
      interventionInput.value = '';
      showToast('Intervention entry added.');
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
      var patient = (document.getElementById('rx-patient').value) || '—';
      var age = (document.getElementById('rx-age').value) || '—';
      var date = (document.getElementById('rx-date').value) || todayStr;
      var diag = (document.getElementById('rx-diagnosis').value) || '—';
      var notes = (document.getElementById('rx-notes').value) || '—';

      document.getElementById('preview-patient').textContent = patient;
      document.getElementById('preview-age').textContent = age;
      document.getElementById('preview-date').textContent = date;
      document.getElementById('preview-diag').textContent = diag;
      document.getElementById('preview-notes').textContent = notes;

      var medsList = document.getElementById('preview-meds-list');
      medsList.innerHTML = '';
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
        var li = document.createElement('li');
        li.textContent = name + ' ' + dose + (freq.length ? ' — ' + freq.join(', ') : '') + (qty ? ' · Qty: ' + qty : '');
        medsList.appendChild(li);
      });

      showToast('Prescription generated.');
      ri();
    });
  }

  /* ============================================================
     RX: TEMPLATE LIBRARY
  ============================================================ */
  function buildRxTemplates() {
    var list = document.getElementById('rx-template-list');
    if (!list) return;
    list.innerHTML = '';
    RX_TEMPLATES.forEach(function (t) {
      var div = document.createElement('div');
      div.className = 'template-item';
      div.innerHTML = '<div class="template-item-top">' +
        '<span class="template-name">' + t.name + '</span>' +
        '<span class="template-tag ' + t.tagClass + '">' + t.tag + '</span>' +
        '</div>' +
        '<div class="template-desc">' + t.desc + '</div>' +
        '<button class="btn-outline-sm" style="margin-top:4px;">+ Apply Template</button>';
      div.querySelector('button').addEventListener('click', function () {
        applyRxTemplate(t);
      });
      list.appendChild(div);
    });
  }

  function applyRxTemplate(t) {
    if (!rxMedsList) return;
    rxMedsList.innerHTML = '';
    t.meds.forEach(function (m) {
      rxMedsList.appendChild(createMedRow(m));
    });
    if (rxDiagInput) rxDiagInput.value = t.diag;
    ri();
    showToast('Template "' + t.name + '" applied.');
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
      var patient = document.getElementById('dx-patient').value || '—';
      var date = document.getElementById('dx-date').value || todayStr;
      var notes = document.getElementById('dx-notes').value || '—';

      document.getElementById('dx-prev-patient').textContent = patient;
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

  function buildDxTemplates() {
    var list = document.getElementById('dx-template-list');
    if (!list) return;
    list.innerHTML = '';
    DX_TEMPLATES.forEach(function (t) {
      var div = document.createElement('div');
      div.className = 'template-item';
      div.innerHTML = '<div class="template-item-top">' +
        '<span class="template-name">' + t.name + '</span>' +
        '<span class="template-tag ' + t.tagClass + '">' + t.tag + '</span>' +
        '</div>' +
        '<div class="template-desc">' + t.desc + '</div>' +
        '<button class="btn-outline-sm" style="margin-top:4px;">+ Apply Template</button>';
      div.querySelector('button').addEventListener('click', function () {
        applyDxTemplate(t);
      });
      list.appendChild(div);
    });
  }

  function applyDxTemplate(t) {
    qsa('.dx-cb').forEach(function (cb) { cb.checked = false; });
    t.tests.forEach(function (test) {
      var cb = qs('.dx-cb[data-test="' + test + '"]');
      if (cb) cb.checked = true;
    });
    showToast('Template "' + t.name + '" applied.');
  }

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
  if (scheduleConsultBtn) scheduleConsultBtn.addEventListener('click', function () { openModal('schedule-consult-modal'); });

  // Close buttons (data-close attribute)
  document.addEventListener('click', function (e) {
    var cl = e.target.closest('[data-close]');
    if (cl) closeModal(cl.getAttribute('data-close'));
    // Click outside modal box
    if (e.target.classList.contains('modal-overlay')) {
      var modals = ['add-patient-modal','schedule-consult-modal','record-modal','profile-modal','edit-consult-modal','patient-detail-modal'];
      modals.forEach(function (m) { closeModal(m); });
    }
  });

  /* ============================================================
     SAVE PATIENT
  ============================================================ */
  var savePatientBtn = document.getElementById('save-patient-btn');
  if (savePatientBtn) {
    savePatientBtn.addEventListener('click', function () {
      var name = document.getElementById('new-name').value.trim();
      if (!name) { showToast('Please enter patient name.'); return; }
      var age = document.getElementById('new-age').value;
      var complaint = document.getElementById('new-complaint').value;
      var coach = document.getElementById('new-coach').value;
      var newId = 'P00' + (PATIENTS.length + 1);
      PATIENTS.push({ id: newId, name: name, age: parseInt(age) || 0, sex: '—', status: 'Active', coach: coach, complaint: complaint });
      INTAKES.push({ id: newId, name: name, age: parseInt(age) || 0, sex: '—', complaint: complaint, status: 'Active' });
      buildPatients(PATIENTS);
      buildIntakes();
      closeModal('add-patient-modal');
      showToast('Patient "' + name + '" added successfully.');
      // Clear form
      ['new-name', 'new-age', 'new-email', 'new-phone', 'new-complaint'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.value = '';
      });
    });
  }

  /* ============================================================
     SAVE CONSULTATION
  ============================================================ */
  var saveConsultBtn = document.getElementById('save-consult-btn');
  if (saveConsultBtn) {
    saveConsultBtn.addEventListener('click', function () {
      var patient = document.getElementById('consult-patient').value;
      var date = document.getElementById('consult-date').value;
      var time = document.getElementById('consult-time').value;
      var type = document.getElementById('consult-type').value;
      var notes = document.getElementById('consult-notes').value;
      if (!date || !time) { showToast('Please select date and time.'); return; }
      CONSULTATIONS.unshift({ patient: patient, date: date, time: time, type: type, status: 'Scheduled', notes: notes });
      buildConsultations(CONSULTATIONS);
      closeModal('schedule-consult-modal');
      showToast('Consultation scheduled for ' + patient + '.');
    });
  }

  /* ============================================================
     INITIAL RENDER
  ============================================================ */
  buildIntakes();
  buildPatients(PATIENTS);
  buildConsultations(CONSULTATIONS);
  buildRecords();
  buildLifestyle();
  buildAssessments(ASSESSMENTS);
  buildRxTemplates();
  buildDxChecklist();
  buildDxTemplates();
  initAccordion();
  initEditConsultModal();
  ri();

});
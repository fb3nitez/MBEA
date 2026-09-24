/* psychiatrist_init.js — Modal handlers, save patient, save consultation, initial render */

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

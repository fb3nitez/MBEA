/* psychiatrist_consultations.js — Consultations table, edit/delete */

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


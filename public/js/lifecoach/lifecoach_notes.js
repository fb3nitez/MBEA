/* lifecoach_notes.js */
document.addEventListener('DOMContentLoaded', function () {
  lcInitSidebar('notes');

  var NOTES = (LC_DATA.NOTES || []).slice();

  function buildPatientFilter() {
    var sel = document.getElementById('notes-patient-filter');
    if (!sel) return;
    var current = sel.value;
    sel.innerHTML = '<option value="">All Patients</option>';
    (LC_DATA.PATIENT_OPTIONS || []).forEach(function (p) {
      var opt = document.createElement('option');
      opt.value = String(p.id);
      opt.textContent = p.name;
      if (String(current) === String(p.id)) opt.selected = true;
      sel.appendChild(opt);
    });
  }

  function buildNotes() {
    var list = document.getElementById('global-notes-list');
    if (!list) return;
    var q = (document.getElementById('notes-search').value || '').toLowerCase();
    var pf = document.getElementById('notes-patient-filter').value;

    var shown = NOTES.filter(function (n) {
      var mq = !q || (n.text || '').toLowerCase().includes(q) || (n.patient || '').toLowerCase().includes(q);
      var mp = !pf || String(n.patientId) === String(pf) || String(n.patient_record_id) === String(pf);
      return mq && mp;
    });

    list.innerHTML = '';
    if (!shown.length) {
      list.innerHTML = '<div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">No notes found.</div>';
      return;
    }

    shown.forEach(function (n) {
      var div = document.createElement('div');
      div.className = 'global-note-item';
      div.innerHTML =
        '<div class="gn-top">' +
          '<span class="gn-patient">' + lcEscape(n.patient) + '</span>' +
          '<div class="gn-meta">' +
            '<span class="gn-type">' + lcEscape(n.type) + '</span>' +
            '<span class="gn-date">' + lcEscape(n.date) + '</span>' +
          '</div>' +
        '</div>' +
        '<div class="gn-text">' + lcEscape(n.text) + '</div>' +
        '<div class="gn-actions"><button type="button" class="btn-outline-sm" data-delete-note="' + n.id + '">Delete</button></div>';
      list.appendChild(div);
    });
  }

  buildPatientFilter();
  buildNotes();
  lcFillPatientSelect(document.getElementById('note-patient'));

  document.getElementById('notes-search').addEventListener('input', buildNotes);
  document.getElementById('notes-patient-filter').addEventListener('change', buildNotes);

  document.getElementById('global-notes-list').addEventListener('click', function (e) {
    var btn = e.target.closest('[data-delete-note]');
    if (!btn) return;
    var id = btn.getAttribute('data-delete-note');
    if (!confirm('Delete this note?')) return;
    lcApi(lcRoute('notesDestroy', id), { method: 'DELETE' })
      .then(function (data) {
        NOTES = NOTES.filter(function (n) { return String(n.id) !== String(id); });
        LC_DATA.NOTES = NOTES;
        buildNotes();
        lcToast(data.message || 'Note deleted.');
      })
      .catch(function (err) { lcToast(err.message || 'Could not delete note.'); });
  });

  document.getElementById('add-note-btn').addEventListener('click', function () {
    if (!(LC_DATA.PATIENT_OPTIONS || []).length) {
      lcToast('Assign a patient first.');
      return;
    }
    lcOpenModal('note-modal');
  });

  document.getElementById('save-note-btn').addEventListener('click', function () {
    var patientId = document.getElementById('note-patient').value;
    var type = document.getElementById('note-type').value;
    var text = document.getElementById('note-text').value.trim();
    if (!patientId) { lcToast('Select a patient.'); return; }
    if (!text) { lcToast('Please enter a note.'); return; }

    var btn = document.getElementById('save-note-btn');
    btn.disabled = true;
    lcApi(lcRoute('notesStore'), {
      method: 'POST',
      body: JSON.stringify({
        patient_record_id: Number(patientId),
        session_type: type,
        body: text,
      }),
    }).then(function (data) {
      NOTES.unshift(data.note);
      LC_DATA.NOTES = NOTES;
      buildNotes();
      lcCloseModal('note-modal');
      document.getElementById('note-text').value = '';
      lcToast(data.message || 'Note saved.');
    }).catch(function (err) {
      lcToast(err.message || 'Could not save note.');
    }).finally(function () {
      btn.disabled = false;
    });
  });

  lcRi();
});

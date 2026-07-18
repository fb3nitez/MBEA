/* lifecoach_notes.js */
document.addEventListener('DOMContentLoaded', function () {
  lcInitSidebar('notes');

  var NOTES = LC_DATA.globalNotes();

  function buildNotes() {
    var list = document.getElementById('global-notes-list');
    if (!list) return;
    var q  = (document.getElementById('notes-search').value || '').toLowerCase();
    var pf = document.getElementById('notes-patient-filter').value;

    var shown = NOTES.filter(function(n) {
      var mq = !q || n.text.toLowerCase().includes(q) || n.patient.toLowerCase().includes(q);
      var mp = !pf || n.patient === pf;
      return mq && mp;
    });

    list.innerHTML = '';
    if (!shown.length) {
      list.innerHTML = '<div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">No notes found.</div>';
      return;
    }

    shown.forEach(function(n) {
      var div = document.createElement('div');
      div.className = 'global-note-item';
      div.innerHTML =
        '<div class="gn-top">' +
          '<span class="gn-patient">' + n.patient + '</span>' +
          '<div class="gn-meta">' +
            '<span class="gn-type">' + n.type + '</span>' +
            '<span class="gn-date">' + n.date + '</span>' +
          '</div>' +
        '</div>' +
        '<div class="gn-text">' + n.text + '</div>';
      list.appendChild(div);
    });
  }

  buildNotes();

  document.getElementById('notes-search').addEventListener('input', buildNotes);
  document.getElementById('notes-patient-filter').addEventListener('change', buildNotes);

  // Add Note
  document.getElementById('add-note-btn').addEventListener('click', function() {
    lcOpenModal('note-modal');
  });

  document.getElementById('save-note-btn').addEventListener('click', function() {
    var patient = document.getElementById('note-patient').value;
    var type    = document.getElementById('note-type').value;
    var text    = document.getElementById('note-text').value.trim();
    if (!text) { lcToast('Please enter a note.'); return; }

    var d = new Date();
    var dateStr = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    var newNote = { patient: patient, type: type, date: dateStr, text: text };

    NOTES.unshift(newNote);

    // Also add to the patient object
    var p = LC_DATA.PATIENTS.find(function(x) { return x.name === patient; });
    if (p) p.notes.unshift({ type: type, date: dateStr, text: text });

    buildNotes();
    lcCloseModal('note-modal');
    document.getElementById('note-text').value = '';
    lcToast('Note saved for ' + patient + '.');
  });

  lcRi();
});

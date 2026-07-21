/* lifecoach_patients.js */
document.addEventListener('DOMContentLoaded', function () {
  lcInitSidebar('patients');

  var PATIENTS = (LC_DATA.PATIENTS || []).slice();
  var currentPatientId = null;
  var complianceChart = null;
  var DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

  var urlParams = new URLSearchParams(window.location.search);
  var urlId = urlParams.get('id');

  buildPatientCards();
  lcFillPatientSelect(document.getElementById('note-patient'));

  if (urlId) {
    var found = PATIENTS.find(function (p) { return String(p.id) === String(urlId); });
    if (found) openPatientDetail(urlId);
  }

  function buildPatientCards() {
    var grid = document.getElementById('lc-patients-grid');
    if (!grid) return;
    grid.innerHTML = '';
    var badge = document.getElementById('patient-count-badge');
    if (badge) badge.textContent = PATIENTS.length + (PATIENTS.length === 1 ? ' patient' : ' patients');

    if (!PATIENTS.length) {
      grid.innerHTML = '<div style="padding:24px;color:#94a3b8;font-size:13px;">No patients assigned to you yet. A psychiatrist can assign patients from their workspace.</div>';
      return;
    }

    PATIENTS.forEach(function (p) {
      var accentClass = p.status === 'Active' ? 'accent-active' : p.status === 'Critical' ? 'accent-critical' : 'accent-inactive';
      var div = document.createElement('div');
      div.className = 'lc-patient-card';
      div.innerHTML =
        '<div class="lc-patient-card-accent ' + accentClass + '"></div>' +
        '<div class="lc-patient-card-body">' +
          '<div class="lc-pc-top">' +
            '<div><div class="lc-pc-name">' + lcEscape(p.name) + '</div><div class="lc-pc-id">' + lcEscape(p.patient_id || p.id) + '</div></div>' +
            lcStatusBadge(p.status || 'Active') +
          '</div>' +
          '<div class="lc-pc-details">' +
            '<div class="lc-pc-detail-row"><span class="lc-pc-detail-label">Age</span><span class="lc-pc-detail-val">' + lcEscape(p.age || '—') + '</span></div>' +
            '<div class="lc-pc-detail-row"><span class="lc-pc-detail-label">Chief Complaint</span><span class="lc-pc-detail-val">' + lcEscape(p.complaint || '—') + '</span></div>' +
            '<div class="lc-pc-detail-row"><span class="lc-pc-detail-label">Next Appointment</span><span class="lc-pc-detail-val">' + lcEscape(p.nextAppt || '—') + '</span></div>' +
          '</div>' +
          '<button type="button" class="btn-green-full" data-open-patient="' + p.id + '">View Details</button>' +
        '</div>';
      grid.appendChild(div);
    });
  }

  document.getElementById('lc-patients-grid').addEventListener('click', function (e) {
    var btn = e.target.closest('[data-open-patient]');
    if (btn) openPatientDetail(btn.getAttribute('data-open-patient'));
  });

  window.openPatientDetail = function (id) {
    var p = PATIENTS.find(function (x) { return String(x.id) === String(id); });
    if (!p) return;
    currentPatientId = id;

    lcHide(document.getElementById('patient-list-view'));
    lcShow(document.getElementById('patient-detail-view'));

    document.getElementById('pd-avatar').textContent = lcInitials(p.name);
    document.getElementById('pd-name').textContent = p.name;
    document.getElementById('pd-meta').textContent = (p.patient_id || p.id) + ' · ' + (p.status || 'Active') + ' · Age ' + (p.age || '—');
    document.getElementById('page-title').textContent = 'Patient: ' + p.name;

    history.replaceState(null, '', lcRoute('patients') + '?id=' + id);

    switchPatientTab('overview');
    populateDetail(p);
    lcRi();
  };

  var backBtn = document.getElementById('patient-back-btn');
  if (backBtn) {
    backBtn.addEventListener('click', function () {
      lcHide(document.getElementById('patient-detail-view'));
      lcShow(document.getElementById('patient-list-view'));
      document.getElementById('page-title').textContent = 'Assigned Patients';
      history.replaceState(null, '', lcRoute('patients'));
    });
  }

  window.switchPatientTab = function (tab) {
    document.querySelectorAll('.tab-btn[data-ptab]').forEach(function (b) { b.classList.remove('active'); });
    document.querySelectorAll('.ptab-panel').forEach(function (panel) { panel.classList.remove('active'); });
    var btn = document.querySelector('[data-ptab="' + tab + '"]');
    var panel = document.getElementById('ptab-' + tab);
    if (btn) btn.classList.add('active');
    if (panel) panel.classList.add('active');
    if (tab === 'metrics') buildComplianceChart();
    lcRi();
  };

  document.querySelectorAll('.tab-btn[data-ptab]').forEach(function (btn) {
    btn.addEventListener('click', function () { switchPatientTab(this.getAttribute('data-ptab')); });
  });

  function populateDetail(p) {
    var infoList = document.getElementById('pd-info-list');
    if (infoList) {
      var fields = [
        ['Patient ID', p.patient_id || p.id],
        ['Status', lcStatusBadge(p.status || 'Active')],
        ['Age / Sex', (p.age || '—') + ' · ' + (p.sex || '—')],
        ['Chief Complaint', p.complaint || '—'],
        ['Program', p.program || '—'],
        ['Coach', p.coach || '—'],
        ['Next Appt', p.nextAppt || '—'],
      ];
      infoList.innerHTML = fields.map(function (f) {
        return '<div class="pi-row"><span class="pi-label">' + f[0] + '</span><span class="pi-val">' + f[1] + '</span></div>';
      }).join('');
    }

    var rxList = document.getElementById('pd-rx-list');
    if (rxList) {
      if (!(p.prescriptions || []).length) {
        rxList.innerHTML = '<div style="padding:12px;color:#94a3b8;font-size:13px;">No active prescriptions.</div>';
      } else {
        rxList.innerHTML = p.prescriptions.map(function (rx) {
          return '<div class="rx-item"><div class="rx-item-left"><span class="rx-item-tag">' + lcEscape(rx.tag) + '</span><span class="rx-item-name">' + lcEscape(rx.name) + '</span></div><span class="rx-active-badge">Active</span></div>';
        }).join('');
      }
    }

    var recentNotes = document.getElementById('pd-recent-notes');
    if (recentNotes) {
      if (!(p.notes || []).length) {
        recentNotes.innerHTML = '<div style="padding:12px;color:#94a3b8;font-size:13px;">No coaching notes yet.</div>';
      } else {
        recentNotes.innerHTML = p.notes.slice(0, 2).map(function (n) {
          return '<div class="pd-note-row"><div class="pd-note-top"><span class="pd-note-type">' + lcEscape(n.type) + '</span><span class="pd-note-date">' + lcEscape(n.date) + '</span></div><div class="pd-note-text">' + lcEscape(n.text) + '</div></div>';
        }).join('');
      }
    }

    var metList = document.getElementById('pd-metrics-list');
    if (metList) {
      if (!(p.metrics || []).length) {
        metList.innerHTML = '<div style="padding:12px;color:#94a3b8;font-size:13px;">No lifestyle assessment on file.</div>';
      } else {
        metList.innerHTML = p.metrics.map(function (m) {
          return '<div class="metric-item"><div class="metric-icon" style="background:#f0fdf4;color:#16a34a;"><i data-feather="' + m.icon + '"></i></div><div class="metric-body"><div class="metric-label-row"><span class="metric-name">' + lcEscape(m.name) + '</span><span class="metric-value ' + m.val + '">' + lcEscape(m.value) + '</span></div><div class="metric-track"><div class="metric-bar ' + m.bar + '" style="width:' + m.pct + '%"></div></div></div></div>';
        }).join('');
      }
    }

    buildGoals(p);
    buildHabits(p);
    buildPatientNotes(p);
  }

  function buildComplianceChart() {
    var p = PATIENTS.find(function (x) { return String(x.id) === String(currentPatientId); });
    if (!p) return;
    var canvas = document.getElementById('compliance-chart');
    if (!canvas || typeof Chart === 'undefined') return;
    if (complianceChart) { complianceChart.destroy(); complianceChart = null; }
    complianceChart = new Chart(canvas, {
      type: 'line',
      data: {
        labels: DAYS,
        datasets: [{
          label: 'Compliance %',
          data: p.compliance || [0, 0, 0, 0, 0, 0, 0],
          borderColor: '#16a34a',
          backgroundColor: 'rgba(22,163,74,.08)',
          borderWidth: 2.5,
          pointBackgroundColor: '#16a34a',
          pointRadius: 4,
          tension: 0.4,
          fill: true,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { min: 0, max: 100, grid: { color: '#f1f5f9' }, ticks: { callback: function (v) { return v + '%'; }, font: { size: 11 } } },
          x: { grid: { display: false }, ticks: { font: { size: 11 } } },
        },
      },
    });
  }

  function buildGoals(p) {
    var list = document.getElementById('pd-goals-list');
    if (!list) return;
    list.innerHTML = '';
    var catColors = {
      Sleep: '#2563eb', Exercise: '#16a34a', Nutrition: '#d97706',
      'Stress Management': '#dc2626', 'Mental Wellness': '#9333ea', 'Social Connection': '#0891b2',
    };
    if (!(p.goals || []).length) {
      list.innerHTML = '<div style="padding:16px;color:#94a3b8;font-size:13px;">No goals yet. Add one to get started.</div>';
      return;
    }
    p.goals.forEach(function (g) {
      var color = catColors[g.cat] || '#64748b';
      var div = document.createElement('div');
      div.className = 'goal-item';
      div.innerHTML =
        '<div class="goal-cat-dot" style="background:' + color + ';"></div>' +
        '<div class="goal-body">' +
          '<div class="goal-top"><span class="goal-title-text">' + lcEscape(g.title) + '</span><span class="goal-cat-tag" style="background:' + color + '18;color:' + color + ';border:1px solid ' + color + '30;">' + lcEscape(g.cat) + '</span></div>' +
          '<div class="goal-desc">' + lcEscape(g.desc) + '</div>' +
          '<div class="goal-date">Target: ' + lcEscape(g.date) + '</div>' +
          '<div class="goal-progress-row"><div class="goal-prog-track"><div class="goal-prog-bar" style="width:' + g.prog + '%;background:linear-gradient(90deg,' + color + ',' + color + '88);"></div></div><span class="goal-prog-pct" style="color:' + color + ';">' + g.prog + '%</span></div>' +
        '</div>';
      list.appendChild(div);
    });
    lcRi();
  }

  var addGoalBtn = document.getElementById('add-goal-btn');
  if (addGoalBtn) addGoalBtn.addEventListener('click', function () { lcOpenModal('goal-modal'); });

  var saveGoalBtn = document.getElementById('save-goal-btn');
  if (saveGoalBtn) {
    saveGoalBtn.addEventListener('click', function () {
      var title = document.getElementById('goal-title').value.trim();
      if (!title) { lcToast('Please enter a goal title.'); return; }
      if (!currentPatientId) return;
      var cat = document.getElementById('goal-category').value;
      var date = document.getElementById('goal-date').value;
      var desc = document.getElementById('goal-desc').value.trim();

      saveGoalBtn.disabled = true;
      lcApi(lcRoute('goalsStore'), {
        method: 'POST',
        body: JSON.stringify({
          patient_record_id: Number(currentPatientId),
          title: title,
          category: cat,
          description: desc,
          target_date: date || null,
          progress: 0,
        }),
      }).then(function (data) {
        var p = PATIENTS.find(function (x) { return String(x.id) === String(currentPatientId); });
        if (p) {
          p.goals = p.goals || [];
          p.goals.unshift(data.goal);
          buildGoals(p);
        }
        lcCloseModal('goal-modal');
        ['goal-title', 'goal-desc', 'goal-date'].forEach(function (id) {
          var el = document.getElementById(id);
          if (el) el.value = '';
        });
        lcToast(data.message || 'Goal added.');
      }).catch(function (err) {
        lcToast(err.message || 'Could not add goal.');
      }).finally(function () {
        saveGoalBtn.disabled = false;
      });
    });
  }

  function buildHabits(p) {
    var wrap = document.getElementById('pd-habits-wrap');
    if (!wrap) return;
    if (!(p.habits || []).length) {
      wrap.innerHTML = '<div style="padding:16px;color:#94a3b8;font-size:13px;">Habit tracking will appear here as coaching goals progress. Use goals and notes to track weekly routines for now.</div>';
      return;
    }
    var table = document.createElement('table');
    table.className = 'habits-table';
    var thead = '<thead><tr><th>Habit</th>';
    DAYS.forEach(function (d) { thead += '<th>' + d + '</th>'; });
    thead += '</tr></thead>';
    table.innerHTML = thead;
    var tbody = document.createElement('tbody');
    p.habits.forEach(function (habit, hi) {
      var tr = document.createElement('tr');
      var row = '<td>' + lcEscape(habit) + '</td>';
      DAYS.forEach(function (d, di) {
        var checked = p.habitData[hi] && p.habitData[hi][di];
        row += '<td><span class="habit-check ' + (checked ? 'habit-done' : 'habit-miss') + '">' + (checked ? '✓' : '○') + '</span></td>';
      });
      tr.innerHTML = row;
      tbody.appendChild(tr);
    });
    table.appendChild(tbody);
    wrap.innerHTML = '';
    wrap.appendChild(table);
  }

  function buildPatientNotes(p) {
    var list = document.getElementById('pd-notes-list');
    if (!list) return;
    list.innerHTML = '';
    if (!(p.notes || []).length) {
      list.innerHTML = '<div style="padding:16px;color:#94a3b8;font-size:13px;">No coaching notes yet.</div>';
      return;
    }
    p.notes.forEach(function (n) {
      var div = document.createElement('div');
      div.className = 'global-note-item';
      div.innerHTML =
        '<div class="gn-top"><span class="gn-patient">' + lcEscape(p.name) + '</span><div class="gn-meta"><span class="gn-type">' + lcEscape(n.type) + '</span><span class="gn-date">' + lcEscape(n.date) + '</span></div></div>' +
        '<div class="gn-text">' + lcEscape(n.text) + '</div>' +
        '<div class="gn-actions"><button type="button" class="btn-outline-sm" data-delete-note="' + n.id + '">Delete</button></div>';
      list.appendChild(div);
    });
  }

  document.getElementById('pd-notes-list').addEventListener('click', function (e) {
    var btn = e.target.closest('[data-delete-note]');
    if (!btn) return;
    var id = btn.getAttribute('data-delete-note');
    if (!confirm('Delete this note?')) return;
    lcApi(lcRoute('notesDestroy', id), { method: 'DELETE' })
      .then(function (data) {
        var p = PATIENTS.find(function (x) { return String(x.id) === String(currentPatientId); });
        if (p) {
          p.notes = (p.notes || []).filter(function (n) { return String(n.id) !== String(id); });
          buildPatientNotes(p);
          populateDetail(p);
        }
        lcToast(data.message || 'Note deleted.');
      })
      .catch(function (err) { lcToast(err.message || 'Could not delete note.'); });
  });

  var pdAddNoteBtn = document.getElementById('pd-add-note-btn');
  if (pdAddNoteBtn) {
    pdAddNoteBtn.addEventListener('click', function () {
      lcFillPatientSelect(document.getElementById('note-patient'), currentPatientId);
      lcOpenModal('note-modal');
    });
  }

  var saveNoteBtn = document.getElementById('save-note-btn');
  if (saveNoteBtn) {
    saveNoteBtn.addEventListener('click', function () {
      var patientId = document.getElementById('note-patient').value;
      var type = document.getElementById('note-type').value;
      var text = document.getElementById('note-text').value.trim();
      if (!patientId) { lcToast('Select a patient.'); return; }
      if (!text) { lcToast('Please enter a note.'); return; }

      saveNoteBtn.disabled = true;
      lcApi(lcRoute('notesStore'), {
        method: 'POST',
        body: JSON.stringify({
          patient_record_id: Number(patientId),
          session_type: type,
          body: text,
        }),
      }).then(function (data) {
        var p = PATIENTS.find(function (x) { return String(x.id) === String(patientId); });
        if (p) {
          p.notes = p.notes || [];
          p.notes.unshift(data.note);
          if (String(currentPatientId) === String(p.id)) {
            buildPatientNotes(p);
            populateDetail(p);
          }
        }
        lcCloseModal('note-modal');
        document.getElementById('note-text').value = '';
        lcToast(data.message || 'Note saved.');
      }).catch(function (err) {
        lcToast(err.message || 'Could not save note.');
      }).finally(function () {
        saveNoteBtn.disabled = false;
      });
    });
  }

  lcRi();
});

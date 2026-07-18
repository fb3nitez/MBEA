/* lifecoach_patients.js */
document.addEventListener('DOMContentLoaded', function () {
  lcInitSidebar('patients');

  var currentPatientId = null;
  var complianceChart  = null;
  var DAYS = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];

  // Check URL param for direct patient open
  var urlParams = new URLSearchParams(window.location.search);
  var urlId = urlParams.get('id');

  buildPatientCards();

  if (urlId) {
    var found = LC_DATA.PATIENTS.find(function(p){ return p.id === urlId; });
    if (found) openPatientDetail(urlId);
  }

  /* ---- PATIENT CARDS ---- */
  function buildPatientCards() {
    var grid = document.getElementById('lc-patients-grid');
    if (!grid) return;
    grid.innerHTML = '';
    var badge = document.getElementById('patient-count-badge');
    if (badge) badge.textContent = LC_DATA.PATIENTS.length + ' patients';

    LC_DATA.PATIENTS.forEach(function(p) {
      var accentClass = p.status === 'Active' ? 'accent-active' : p.status === 'Critical' ? 'accent-critical' : 'accent-inactive';
      var div = document.createElement('div');
      div.className = 'lc-patient-card';
      div.innerHTML =
        '<div class="lc-patient-card-accent ' + accentClass + '"></div>' +
        '<div class="lc-patient-card-body">' +
          '<div class="lc-pc-top">' +
            '<div><div class="lc-pc-name">' + p.name + '</div><div class="lc-pc-id">' + p.id + '</div></div>' +
            lcStatusBadge(p.status) +
          '</div>' +
          '<div class="lc-pc-details">' +
            '<div class="lc-pc-detail-row"><span class="lc-pc-detail-label">Age</span><span class="lc-pc-detail-val">' + p.age + '</span></div>' +
            '<div class="lc-pc-detail-row"><span class="lc-pc-detail-label">Chief Complaint</span><span class="lc-pc-detail-val">' + p.complaint + '</span></div>' +
            '<div class="lc-pc-detail-row"><span class="lc-pc-detail-label">Next Appointment</span><span class="lc-pc-detail-val">' + p.nextAppt + '</span></div>' +
          '</div>' +
          '<button class="btn-green-full" onclick="openPatientDetail(\'' + p.id + '\')">View Details</button>' +
        '</div>';
      grid.appendChild(div);
    });
  }

  /* ---- OPEN DETAIL ---- */
  window.openPatientDetail = function(id) {
    var p = LC_DATA.PATIENTS.find(function(x){ return x.id === id; });
    if (!p) return;
    currentPatientId = id;

    lcHide(document.getElementById('patient-list-view'));
    lcShow(document.getElementById('patient-detail-view'));

    document.getElementById('pd-avatar').textContent = lcInitials(p.name);
    document.getElementById('pd-name').textContent   = p.name;
    document.getElementById('pd-meta').textContent   = p.id + ' · ' + p.status + ' · Age ' + p.age;
    document.getElementById('page-title').textContent = 'Patient: ' + p.name;

    // Update URL without reload
    history.replaceState(null, '', '/lifecoach/patients?id=' + id);

    switchPatientTab('overview');
    populateDetail(p);
    lcRi();
  };

  // Back button
  var backBtn = document.getElementById('patient-back-btn');
  if (backBtn) backBtn.addEventListener('click', function() {
    lcHide(document.getElementById('patient-detail-view'));
    lcShow(document.getElementById('patient-list-view'));
    document.getElementById('page-title').textContent = 'Assigned Patients';
    history.replaceState(null, '', '/lifecoach/patients');
  });

  /* ---- TAB SWITCHING ---- */
  window.switchPatientTab = function(tab) {
    document.querySelectorAll('.tab-btn[data-ptab]').forEach(function(b){ b.classList.remove('active'); });
    document.querySelectorAll('.ptab-panel').forEach(function(panel){ panel.classList.remove('active'); });
    var btn = document.querySelector('[data-ptab="' + tab + '"]');
    var panel = document.getElementById('ptab-' + tab);
    if (btn) btn.classList.add('active');
    if (panel) panel.classList.add('active');
    if (tab === 'metrics') buildComplianceChart();
    lcRi();
  };

  document.querySelectorAll('.tab-btn[data-ptab]').forEach(function(btn){
    btn.addEventListener('click', function(){ switchPatientTab(this.getAttribute('data-ptab')); });
  });

  /* ---- POPULATE DETAIL ---- */
  function populateDetail(p) {
    // Info list
    var infoList = document.getElementById('pd-info-list');
    if (infoList) {
      var fields = [
        ['Patient ID', p.id], ['Status', lcStatusBadge(p.status)],
        ['Age / Sex', p.age + ' · ' + p.sex], ['Email', p.email],
        ['Phone', p.phone], ['Chief Complaint', p.complaint],
        ['Program', p.program], ['Coach', p.coach], ['Next Appt', p.nextAppt],
      ];
      infoList.innerHTML = fields.map(function(f){
        return '<div class="pi-row"><span class="pi-label">' + f[0] + '</span><span class="pi-val">' + f[1] + '</span></div>';
      }).join('');
    }

    // Prescriptions
    var rxList = document.getElementById('pd-rx-list');
    if (rxList) {
      rxList.innerHTML = p.prescriptions.map(function(rx){
        return '<div class="rx-item"><div class="rx-item-left"><span class="rx-item-tag">' + rx.tag + '</span><span class="rx-item-name">' + rx.name + '</span></div><span class="rx-active-badge">Active</span></div>';
      }).join('');
    }

    // Recent notes
    var recentNotes = document.getElementById('pd-recent-notes');
    if (recentNotes) {
      recentNotes.innerHTML = p.notes.slice(0, 2).map(function(n){
        return '<div class="pd-note-row"><div class="pd-note-top"><span class="pd-note-type">' + n.type + '</span><span class="pd-note-date">' + n.date + '</span></div><div class="pd-note-text">' + n.text + '</div></div>';
      }).join('');
    }

    // Metrics
    var metList = document.getElementById('pd-metrics-list');
    if (metList) {
      metList.innerHTML = p.metrics.map(function(m){
        return '<div class="metric-item"><div class="metric-icon" style="background:#f0fdf4;color:#16a34a;"><i data-feather="' + m.icon + '"></i></div><div class="metric-body"><div class="metric-label-row"><span class="metric-name">' + m.name + '</span><span class="metric-value ' + m.val + '">' + m.value + '</span></div><div class="metric-track"><div class="metric-bar ' + m.bar + '" style="width:' + m.pct + '%"></div></div></div></div>';
      }).join('');
    }

    buildGoals(p);
    buildHabits(p);
    buildPatientNotes(p);
  }

  /* ---- COMPLIANCE CHART ---- */
  function buildComplianceChart() {
    var p = LC_DATA.PATIENTS.find(function(x){ return x.id === currentPatientId; });
    if (!p) return;
    var canvas = document.getElementById('compliance-chart');
    if (!canvas) return;
    if (complianceChart) { complianceChart.destroy(); complianceChart = null; }
    complianceChart = new Chart(canvas, {
      type: 'line',
      data: {
        labels: DAYS,
        datasets: [{ label: 'Compliance %', data: p.compliance, borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,.08)', borderWidth: 2.5, pointBackgroundColor: '#16a34a', pointRadius: 4, tension: 0.4, fill: true }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { min: 0, max: 100, grid: { color: '#f1f5f9' }, ticks: { callback: function(v){ return v + '%'; }, font: { size: 11 } } },
          x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
      }
    });
  }

  /* ---- GOALS ---- */
  function buildGoals(p) {
    var list = document.getElementById('pd-goals-list');
    if (!list) return;
    list.innerHTML = '';
    var catColors = { Sleep:'#2563eb', Exercise:'#16a34a', Nutrition:'#d97706', 'Stress Management':'#dc2626', 'Mental Wellness':'#9333ea', 'Social Connection':'#0891b2' };
    p.goals.forEach(function(g, i) {
      var color = catColors[g.cat] || '#64748b';
      var div = document.createElement('div');
      div.className = 'goal-item';
      div.innerHTML =
        '<div class="goal-cat-dot" style="background:' + color + ';"></div>' +
        '<div class="goal-body">' +
          '<div class="goal-top"><span class="goal-title-text">' + g.title + '</span><span class="goal-cat-tag" style="background:' + color + '18;color:' + color + ';border:1px solid ' + color + '30;">' + g.cat + '</span></div>' +
          '<div class="goal-desc">' + g.desc + '</div>' +
          '<div class="goal-date">Target: ' + g.date + '</div>' +
          '<div class="goal-progress-row"><div class="goal-prog-track"><div class="goal-prog-bar" style="width:' + g.prog + '%;background:linear-gradient(90deg,' + color + ',' + color + '88);"></div></div><span class="goal-prog-pct" style="color:' + color + ';">' + g.prog + '%</span></div>' +
        '</div>';
      list.appendChild(div);
    });
    lcRi();
  }

  var addGoalBtn = document.getElementById('add-goal-btn');
  if (addGoalBtn) addGoalBtn.addEventListener('click', function(){ lcOpenModal('goal-modal'); });

  var saveGoalBtn = document.getElementById('save-goal-btn');
  if (saveGoalBtn) {
    saveGoalBtn.addEventListener('click', function(){
      var title = document.getElementById('goal-title').value.trim();
      if (!title) { lcToast('Please enter a goal title.'); return; }
      var cat  = document.getElementById('goal-category').value;
      var date = document.getElementById('goal-date').value;
      var desc = document.getElementById('goal-desc').value.trim();
      var p = LC_DATA.PATIENTS.find(function(x){ return x.id === currentPatientId; });
      if (!p) return;
      p.goals.unshift({ title: title, cat: cat, desc: desc, date: date || 'TBD', prog: 0 });
      buildGoals(p);
      lcCloseModal('goal-modal');
      ['goal-title','goal-desc','goal-date'].forEach(function(id){ var el=document.getElementById(id); if(el) el.value=''; });
      lcToast('Goal added.');
    });
  }

  /* ---- HABITS ---- */
  function buildHabits(p) {
    var wrap = document.getElementById('pd-habits-wrap');
    if (!wrap) return;
    var table = document.createElement('table');
    table.className = 'habits-table';
    var thead = '<thead><tr><th>Habit</th>';
    DAYS.forEach(function(d){ thead += '<th>' + d + '</th>'; });
    thead += '</tr></thead>';
    table.innerHTML = thead;
    var tbody = document.createElement('tbody');
    p.habits.forEach(function(habit, hi){
      var tr = document.createElement('tr');
      var row = '<td>' + habit + '</td>';
      DAYS.forEach(function(d, di){
        var checked = p.habitData[hi] && p.habitData[hi][di];
        row += '<td><span class="habit-check ' + (checked ? 'habit-done' : 'habit-miss') + '" data-pid="' + p.id + '" data-hi="' + hi + '" data-di="' + di + '" onclick="toggleHabit(this)">' + (checked ? '✓' : '○') + '</span></td>';
      });
      tr.innerHTML = row;
      tbody.appendChild(tr);
    });
    table.appendChild(tbody);
    wrap.innerHTML = '';
    wrap.appendChild(table);
  }

  window.toggleHabit = function(el) {
    var pid = el.getAttribute('data-pid');
    var hi  = parseInt(el.getAttribute('data-hi'));
    var di  = parseInt(el.getAttribute('data-di'));
    var p   = LC_DATA.PATIENTS.find(function(x){ return x.id === pid; });
    if (!p) return;
    p.habitData[hi][di] = !p.habitData[hi][di];
    var checked = p.habitData[hi][di];
    el.textContent = checked ? '✓' : '○';
    el.className = 'habit-check ' + (checked ? 'habit-done' : 'habit-miss');
  };

  /* ---- PATIENT NOTES ---- */
  function buildPatientNotes(p) {
    var list = document.getElementById('pd-notes-list');
    if (!list) return;
    list.innerHTML = '';
    p.notes.forEach(function(n, i){
      var div = document.createElement('div');
      div.className = 'global-note-item';
      div.innerHTML =
        '<div class="gn-top"><span class="gn-patient">' + p.name + '</span><div class="gn-meta"><span class="gn-type">' + n.type + '</span><span class="gn-date">' + n.date + '</span></div></div>' +
        '<div class="gn-text">' + n.text + '</div>' +
        '<div class="gn-actions"><button class="btn-outline-sm" onclick="deleteNote(\'' + p.id + '\',' + i + ')">Delete</button></div>';
      list.appendChild(div);
    });
  }

  window.deleteNote = function(pid, i) {
    var p = LC_DATA.PATIENTS.find(function(x){ return x.id === pid; });
    if (!p) return;
    if (confirm('Delete this note?')) {
      p.notes.splice(i, 1);
      buildPatientNotes(p);
      lcToast('Note deleted.');
    }
  };

  var pdAddNoteBtn = document.getElementById('pd-add-note-btn');
  if (pdAddNoteBtn) {
    pdAddNoteBtn.addEventListener('click', function(){
      var p = LC_DATA.PATIENTS.find(function(x){ return x.id === currentPatientId; });
      if (p) { var sel = document.getElementById('note-patient'); if (sel) sel.value = p.name; }
      lcOpenModal('note-modal');
    });
  }

  var saveNoteBtn = document.getElementById('save-note-btn');
  if (saveNoteBtn) {
    saveNoteBtn.addEventListener('click', function(){
      var patient = document.getElementById('note-patient').value;
      var type    = document.getElementById('note-type').value;
      var text    = document.getElementById('note-text').value.trim();
      if (!text) { lcToast('Please enter a note.'); return; }
      var d = new Date();
      var dateStr = d.toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
      var p = LC_DATA.PATIENTS.find(function(x){ return x.name === patient; });
      if (p) {
        p.notes.unshift({ type: type, date: dateStr, text: text });
        if (currentPatientId === p.id) buildPatientNotes(p);
      }
      lcCloseModal('note-modal');
      document.getElementById('note-text').value = '';
      lcToast('Note saved for ' + patient + '.');
    });
  }

  lcRi();
});

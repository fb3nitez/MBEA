/* ============================================================
   MEDCARE LIFE COACH DASHBOARD — lifecoach.js
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  /* ============================================================
     DATA
  ============================================================ */

  var PATIENTS = [
    {
      id: 'P001', name: 'Sarah Johnson', age: 34, sex: 'Female', status: 'Active',
      complaint: 'Depression and anxiety',
      nextAppt: '2026-06-10', coach: 'Michael Chen',
      email: 'sarah.johnson@email.com', phone: '+63 912 345 6789',
      program: 'Cognitive Behavioral Coaching',
      prescriptions: [
        { tag: 'Sleep Hygiene',  name: 'Sleep Routine Protocol' },
        { tag: 'Exercise',       name: 'Daily Walking Program' },
        { tag: 'Nutrition',      name: 'Mediterranean Diet Plan' },
      ],
      metrics: [
        { name: 'Sleep Quality', value: '7.5 hrs', pct: 75, bar: 'mbar-green', val: 'mval-green', icon: 'moon' },
        { name: 'Exercise',      value: '4×/week', pct: 50, bar: 'mbar-amber', val: 'mval-amber', icon: 'activity' },
        { name: 'Nutrition',     value: 'Good',    pct: 80, bar: 'mbar-green', val: 'mval-green', icon: 'heart' },
        { name: 'Stress Level',  value: 'Moderate',pct: 60, bar: 'mbar-amber', val: 'mval-amber', icon: 'zap' },
        { name: 'Hydration',     value: '2L/day',  pct: 85, bar: 'mbar-green', val: 'mval-green', icon: 'droplet' },
      ],
      goals: [
        { title: 'Establish consistent sleep schedule', cat: 'Sleep',    desc: 'Sleep by 10:30 PM and wake at 6:30 AM daily.', date: 'Jun 30, 2026', prog: 70 },
        { title: 'Walk 30 minutes daily',               cat: 'Exercise', desc: '30-minute brisk walk each morning before work.', date: 'Jun 20, 2026', prog: 55 },
        { title: 'Mediterranean diet adherence',        cat: 'Nutrition',desc: 'Follow meal plan 6 out of 7 days per week.', date: 'Jul 15, 2026', prog: 80 },
      ],
      habits: ['Sleep 10:30 PM', 'Morning Walk', 'Meal Plan', 'Journaling', 'No caffeine after 2 PM'],
      habitData: [
        [true,  true,  true,  false, true,  true,  true ],
        [true,  false, true,  true,  true,  false, true ],
        [true,  true,  true,  true,  false, true,  false],
        [false, true,  false, true,  true,  true,  true ],
        [true,  true,  true,  true,  true,  true,  false],
      ],
      compliance: [78, 75, 92, 88, 100, 72, 85],
      notes: [
        { type: 'Follow-up',   date: 'Jun 5, 2026',  text: 'Excellent progress with sleep routine. Patient maintaining 7–8 hours consistently. Mood significantly improved from last session.' },
        { type: 'Goal Review', date: 'Jun 1, 2026',  text: 'Reviewed nutrition plan. Patient adapting well to Mediterranean diet. Reduced processed food intake by 60%.' },
        { type: 'Check-in',    date: 'May 25, 2026', text: 'Patient reports feeling less anxious in the morning. Sleep hygiene protocol showing results after 2 weeks.' },
      ],
    },
    {
      id: 'P003', name: 'Emily Thompson', age: 28, sex: 'Female', status: 'Critical',
      complaint: 'Panic attacks',
      nextAppt: '2026-06-08', coach: 'Michael Chen',
      email: 'emily.thompson@email.com', phone: '+63 917 234 5678',
      program: 'Anxiety Management Program',
      prescriptions: [
        { tag: 'Mindfulness', name: 'Daily Meditation Protocol' },
        { tag: 'Breathing',   name: 'Box Breathing Exercises' },
      ],
      metrics: [
        { name: 'Sleep Quality', value: '5.5 hrs', pct: 55, bar: 'mbar-amber', val: 'mval-amber', icon: 'moon' },
        { name: 'Exercise',      value: '2×/week', pct: 25, bar: 'mbar-red',   val: 'mval-red',   icon: 'activity' },
        { name: 'Nutrition',     value: 'Fair',    pct: 60, bar: 'mbar-amber', val: 'mval-amber', icon: 'heart' },
        { name: 'Stress Level',  value: 'High',    pct: 85, bar: 'mbar-red',   val: 'mval-red',   icon: 'zap' },
        { name: 'Hydration',     value: '1.2L/day',pct: 40, bar: 'mbar-red',   val: 'mval-red',   icon: 'droplet' },
      ],
      goals: [
        { title: 'Complete daily meditation', cat: 'Mental Wellness', desc: '10 minutes of mindfulness each morning.', date: 'Jun 15, 2026', prog: 40 },
        { title: 'Reduce panic episode frequency', cat: 'Stress Management', desc: 'Use breathing exercises during triggers.', date: 'Jul 1, 2026', prog: 30 },
      ],
      habits: ['Morning Meditation', 'Box Breathing', 'Limit Caffeine', 'Evening Walk'],
      habitData: [
        [true,  false, true,  false, true,  false, false],
        [false, true,  false, false, true,  true,  false],
        [true,  true,  false, true,  false, false, true ],
        [false, false, true,  false, true,  true,  false],
      ],
      compliance: [45, 50, 58, 62, 55, 48, 60],
      notes: [
        { type: 'Crisis Support', date: 'Jun 4, 2026',  text: 'Patient experienced panic episode at work. Discussed anxiety management and introduced box breathing exercises. Patient receptive.' },
        { type: 'Follow-up',      date: 'May 28, 2026', text: 'Reviewed triggers journal. Work stress identified as primary trigger. Discussing workplace boundaries.' },
      ],
    },
    {
      id: 'P002', name: 'David Martinez', age: 42, sex: 'Male', status: 'Active',
      complaint: 'Stress management',
      nextAppt: '2026-06-12', coach: 'Michael Chen',
      email: 'david.martinez@email.com', phone: '+63 920 987 6543',
      program: 'Stress & Lifestyle Balance',
      prescriptions: [
        { tag: 'Exercise',    name: 'Strength Training 3×/week' },
        { tag: 'Mindfulness', name: 'Evening Wind-down Routine' },
      ],
      metrics: [
        { name: 'Sleep Quality', value: '6.5 hrs', pct: 65, bar: 'mbar-amber', val: 'mval-amber', icon: 'moon' },
        { name: 'Exercise',      value: '3×/week', pct: 60, bar: 'mbar-amber', val: 'mval-amber', icon: 'activity' },
        { name: 'Nutrition',     value: 'Good',    pct: 75, bar: 'mbar-green', val: 'mval-green', icon: 'heart' },
        { name: 'Stress Level',  value: 'Moderate',pct: 65, bar: 'mbar-amber', val: 'mval-amber', icon: 'zap' },
        { name: 'Hydration',     value: '2.5L/day',pct: 90, bar: 'mbar-green', val: 'mval-green', icon: 'droplet' },
      ],
      goals: [
        { title: 'Strength training consistency', cat: 'Exercise', desc: 'Complete 3 gym sessions per week for 8 weeks.', date: 'Jul 30, 2026', prog: 60 },
        { title: 'Work-life balance', cat: 'Stress Management', desc: 'No work emails after 7 PM. Leave office by 6 PM.', date: 'Jun 30, 2026', prog: 45 },
      ],
      habits: ['Gym Session', 'Evening Routine', 'No Late Emails', 'Healthy Lunch'],
      habitData: [
        [true,  false, true,  false, true,  true,  false],
        [true,  true,  true,  false, false, true,  true ],
        [false, true,  false, true,  true,  false, true ],
        [true,  true,  true,  true,  false, true,  false],
      ],
      compliance: [60, 65, 70, 68, 75, 72, 78],
      notes: [
        { type: 'Follow-up', date: 'Jun 3, 2026',  text: 'Patient showing improvement in stress management. Strength training program going well. Sleep quality improving.' },
        { type: 'Check-in',  date: 'May 27, 2026', text: 'Reviewed work-life balance strategies. Patient finding it difficult to disconnect from work after hours.' },
      ],
    },
  ];

  var TASKS = [
    { patient: 'Sarah Johnson',   desc: 'Follow-up on sleep hygiene progress',       priority: 'High',   due: '2026-06-08', done: false },
    { patient: 'Emily Thompson',  desc: 'Review anxiety management exercises',        priority: 'High',   due: '2026-06-09', done: false },
    { patient: 'David Martinez',  desc: 'Check nutrition plan adherence',             priority: 'Medium', due: '2026-06-12', done: false },
    { patient: 'Sarah Johnson',   desc: 'Prepare sleep diary review worksheet',       priority: 'Medium', due: '2026-06-14', done: false },
    { patient: 'Emily Thompson',  desc: 'Send breathing exercise reminder materials', priority: 'Low',    due: '2026-06-16', done: false },
  ];

  var SCHEDULES = [
    { patient: 'Sarah Johnson',  topic: 'Sleep hygiene review',        date: 'Jun 8',  time: '2:00 PM'  },
    { patient: 'Emily Thompson', topic: 'Anxiety management check-in', date: 'Jun 9',  time: '10:00 AM' },
    { patient: 'David Martinez', topic: 'Stress management review',    date: 'Jun 11', time: '3:00 PM'  },
  ];

  var GLOBAL_NOTES = [];
  // Populate from all patient notes
  PATIENTS.forEach(function(p) {
    p.notes.forEach(function(n) {
      GLOBAL_NOTES.push({ patient: p.name, type: n.type, date: n.date, text: n.text });
    });
  });

  var currentPatientId = null;
  var complianceChart  = null;

  /* ============================================================
     HELPERS
  ============================================================ */

  function ri() { if (window.feather) window.feather.replace(); }
  function qs(sel, ctx) { return (ctx || document).querySelector(sel); }
  function qsa(sel, ctx) { return Array.from((ctx || document).querySelectorAll(sel)); }
  function show(el) { if (el) el.classList.remove('hidden'); }
  function hide(el) { if (el) el.classList.add('hidden'); }

  window.showToast = function(msg) {
    var t = document.getElementById('toast');
    if (!t) return;
    t.textContent = msg;
    show(t);
    clearTimeout(t._t);
    t._t = setTimeout(function() { hide(t); }, 2800);
  };

  function priorityBadge(p) {
    var cls = p === 'High' ? 'badge-high' : p === 'Medium' ? 'badge-medium' : 'badge-low';
    return '<span class="badge ' + cls + '">' + p + '</span>';
  }

  function statusBadge(s) {
    var cls = s === 'Active' ? 'badge-active' : s === 'Critical' ? 'badge-critical' : 'badge-inactive';
    return '<span class="badge ' + cls + '">' + s + '</span>';
  }

  function initials(name) {
    return name.split(' ').map(function(w) { return w[0]; }).join('').slice(0, 2).toUpperCase();
  }

  function openModal(id) { var el = document.getElementById(id); if (el) { show(el); ri(); } }
  function closeModal(id) { var el = document.getElementById(id); if (el) hide(el); }

  /* ============================================================
     SECTION SWITCHING
  ============================================================ */
  var TITLES = { dashboard: 'Dashboard', patients: 'My Patients', notes: 'Coaching Notes', tasks: 'Tasks' };

  function switchSection(key) {
    qsa('.lc-section').forEach(function(s) { s.classList.remove('active'); });
    qsa('.nav-item[data-section]').forEach(function(n) { n.classList.remove('active'); });
    var sec = document.getElementById('section-' + key);
    if (sec) sec.classList.add('active');
    var btn = qs('[data-section="' + key + '"]');
    if (btn) btn.classList.add('active');
    var title = document.getElementById('page-title');
    if (title) title.textContent = TITLES[key] || key;
    document.getElementById('sidebar').classList.remove('open');

    // When switching to patients, reset to list view
    if (key === 'patients') showPatientList();
  }

  qsa('.nav-item[data-section]').forEach(function(btn) {
    btn.addEventListener('click', function() { switchSection(this.getAttribute('data-section')); });
  });

  document.addEventListener('click', function(e) {
    var el = e.target.closest('[data-goto]');
    if (el) switchSection(el.getAttribute('data-goto'));
  });

  /* ============================================================
     HAMBURGER
  ============================================================ */
  var hamBtn = document.getElementById('hamburger-btn');
  if (hamBtn) hamBtn.addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('open');
  });

  /* ============================================================
     LOGOUT / PROFILE
  ============================================================ */
  document.getElementById('logout-btn').addEventListener('click', function() { window.location.href = '/'; });
  document.getElementById('profile-btn').addEventListener('click', function() { openModal('profile-modal'); });

  /* ============================================================
     MODAL CLOSE
  ============================================================ */
  document.addEventListener('click', function(e) {
    var cl = e.target.closest('[data-close]');
    if (cl) { closeModal(cl.getAttribute('data-close')); return; }
    if (e.target.classList.contains('modal-overlay')) {
      qsa('.modal-overlay').forEach(function(m) { hide(m); });
    }
  });

  /* ============================================================
     BUILD DASHBOARD
  ============================================================ */
  function buildDashboard() {
    // Patient rows
    var dpList = document.getElementById('dash-patient-list');
    if (dpList) {
      dpList.innerHTML = '';
      PATIENTS.forEach(function(p) {
        var row = document.createElement('div');
        row.className = 'dash-patient-row';
        row.innerHTML =
          '<div>' +
            '<div class="dash-patient-name">' + p.name + '</div>' +
            '<div class="dash-patient-complaint">' + p.complaint + '</div>' +
          '</div>' +
          '<button class="btn-outline-sm" onclick="openPatientDetail(\'' + p.id + '\')">View</button>';
        dpList.appendChild(row);
      });
    }

    // Task rows
    var dtList = document.getElementById('dash-task-list');
    if (dtList) {
      dtList.innerHTML = '';
      TASKS.filter(function(t) { return !t.done; }).slice(0, 3).forEach(function(t) {
        var row = document.createElement('div');
        row.className = 'dash-task-row';
        row.innerHTML =
          '<div class="dash-task-top">' +
            '<span class="dash-task-patient">' + t.patient + '</span>' +
            priorityBadge(t.priority) +
          '</div>' +
          '<div class="dash-task-desc">' + t.desc + '</div>' +
          '<div class="dash-task-due">Due: ' + t.due + '</div>';
        dtList.appendChild(row);
      });
    }

    // Schedule
    buildSchedule();
  }

  function buildSchedule() {
    var list = document.getElementById('schedule-list');
    if (!list) return;
    list.innerHTML = '';
    SCHEDULES.forEach(function(s) {
      var row = document.createElement('div');
      row.className = 'schedule-row';
      row.innerHTML =
        '<div class="schedule-info">' +
          '<div class="schedule-patient">' + s.patient + '</div>' +
          '<div class="schedule-topic">' + s.topic + '</div>' +
        '</div>' +
        '<div class="schedule-time-block">' +
          '<div class="schedule-date">' + s.date + '</div>' +
          '<div class="schedule-time">' + s.time + '</div>' +
        '</div>';
      list.appendChild(row);
    });
  }

  /* ============================================================
     PATIENTS SECTION
  ============================================================ */
  function showPatientList() {
    show(document.getElementById('patient-list-view'));
    hide(document.getElementById('patient-detail-view'));
    document.getElementById('page-title').textContent = 'My Patients';
    buildPatientCards();
    ri();
  }

  function buildPatientCards() {
    var grid = document.getElementById('lc-patients-grid');
    if (!grid) return;
    grid.innerHTML = '';
    var badge = document.getElementById('patient-count-badge');
    if (badge) badge.textContent = PATIENTS.length + ' patients';

    PATIENTS.forEach(function(p) {
      var accentClass = p.status === 'Active' ? 'accent-active' : p.status === 'Critical' ? 'accent-critical' : 'accent-inactive';
      var div = document.createElement('div');
      div.className = 'lc-patient-card';
      div.innerHTML =
        '<div class="lc-patient-card-accent ' + accentClass + '"></div>' +
        '<div class="lc-patient-card-body">' +
          '<div class="lc-pc-top">' +
            '<div><div class="lc-pc-name">' + p.name + '</div><div class="lc-pc-id">' + p.id + '</div></div>' +
            statusBadge(p.status) +
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

  window.openPatientDetail = function(id) {
    var p = PATIENTS.find(function(x) { return x.id === id; });
    if (!p) return;
    currentPatientId = id;

    hide(document.getElementById('patient-list-view'));
    show(document.getElementById('patient-detail-view'));

    // Switch to patients section if not already there
    switchSection('patients');
    hide(document.getElementById('patient-list-view'));
    show(document.getElementById('patient-detail-view'));

    // Header
    document.getElementById('pd-avatar').textContent = initials(p.name);
    document.getElementById('pd-name').textContent   = p.name;
    document.getElementById('pd-meta').textContent   = p.id + ' · ' + p.status + ' · Age ' + p.age;
    document.getElementById('page-title').textContent = 'Patient: ' + p.name;

    // Reset to overview tab
    switchPatientTab('overview');
    populatePatientDetail(p);
    ri();
  };

  window.switchPatientTab = function(tab) {
    qsa('.tab-btn[data-ptab]').forEach(function(b) { b.classList.remove('active'); });
    qsa('.ptab-panel').forEach(function(panel) { panel.classList.remove('active'); });
    var btn = qs('[data-ptab="' + tab + '"]');
    var panel = document.getElementById('ptab-' + tab);
    if (btn) btn.classList.add('active');
    if (panel) panel.classList.add('active');

    if (tab === 'metrics') buildComplianceChart();
  };

  qsa('.tab-btn[data-ptab]').forEach(function(btn) {
    btn.addEventListener('click', function() { switchPatientTab(this.getAttribute('data-ptab')); });
  });

  var patientBackBtn = document.getElementById('patient-back-btn');
  if (patientBackBtn) patientBackBtn.addEventListener('click', showPatientList);

  function populatePatientDetail(p) {
    // Info list
    var infoList = document.getElementById('pd-info-list');
    if (infoList) {
      var fields = [
        ['Patient ID',     p.id],
        ['Status',         statusBadge(p.status)],
        ['Age / Sex',      p.age + ' · ' + p.sex],
        ['Email',          p.email],
        ['Phone',          p.phone],
        ['Chief Complaint',p.complaint],
        ['Program',        p.program],
        ['Coach',          p.coach],
        ['Next Appt',      p.nextAppt],
      ];
      infoList.innerHTML = fields.map(function(f) {
        return '<div class="pi-row"><span class="pi-label">' + f[0] + '</span><span class="pi-val">' + f[1] + '</span></div>';
      }).join('');
    }

    // Prescriptions
    var rxList = document.getElementById('pd-rx-list');
    if (rxList) {
      rxList.innerHTML = p.prescriptions.map(function(rx) {
        return '<div class="rx-item">' +
          '<div class="rx-item-left">' +
            '<span class="rx-item-tag">' + rx.tag + '</span>' +
            '<span class="rx-item-name">' + rx.name + '</span>' +
          '</div>' +
          '<span class="rx-active-badge">Active</span>' +
        '</div>';
      }).join('');
    }

    // Recent notes (overview)
    var recentNotes = document.getElementById('pd-recent-notes');
    if (recentNotes) {
      recentNotes.innerHTML = p.notes.slice(0, 2).map(function(n) {
        return '<div class="pd-note-row">' +
          '<div class="pd-note-top">' +
            '<span class="pd-note-type">' + n.type + '</span>' +
            '<span class="pd-note-date">' + n.date + '</span>' +
          '</div>' +
          '<div class="pd-note-text">' + n.text + '</div>' +
        '</div>';
      }).join('');
    }

    // Metrics
    var metList = document.getElementById('pd-metrics-list');
    if (metList) {
      metList.innerHTML = p.metrics.map(function(m) {
        return '<div class="metric-item">' +
          '<div class="metric-icon" style="background:#f0fdf4;color:#16a34a;"><i data-feather="' + m.icon + '"></i></div>' +
          '<div class="metric-body">' +
            '<div class="metric-label-row">' +
              '<span class="metric-name">' + m.name + '</span>' +
              '<span class="metric-value ' + m.val + '">' + m.value + '</span>' +
            '</div>' +
            '<div class="metric-track"><div class="metric-bar ' + m.bar + '" style="width:' + m.pct + '%"></div></div>' +
          '</div>' +
        '</div>';
      }).join('');
    }

    // Goals
    buildGoals(p);

    // Habits
    buildHabits(p);

    // Coaching notes
    buildPatientNotes(p);
  }

  /* ============================================================
     COMPLIANCE CHART
  ============================================================ */
  function buildComplianceChart() {
    var p = PATIENTS.find(function(x) { return x.id === currentPatientId; });
    if (!p) return;
    var canvas = document.getElementById('compliance-chart');
    if (!canvas) return;

    if (complianceChart) { complianceChart.destroy(); complianceChart = null; }

    complianceChart = new Chart(canvas, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Compliance %',
          data: p.compliance,
          borderColor: '#16a34a',
          backgroundColor: 'rgba(22,163,74,.08)',
          borderWidth: 2.5,
          pointBackgroundColor: '#16a34a',
          pointRadius: 4,
          pointHoverRadius: 6,
          tension: 0.4,
          fill: true,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { callbacks: { label: function(c) { return c.parsed.y + '%'; } } }
        },
        scales: {
          y: { min: 0, max: 100, grid: { color: '#f1f5f9' }, ticks: { callback: function(v) { return v + '%'; }, font: { size: 11 } } },
          x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
      }
    });
  }

  /* ============================================================
     GOALS
  ============================================================ */
  function buildGoals(p) {
    var list = document.getElementById('pd-goals-list');
    if (!list) return;
    list.innerHTML = '';
    var catColors = { Sleep: '#2563eb', Exercise: '#16a34a', Nutrition: '#d97706', 'Stress Management': '#dc2626', 'Mental Wellness': '#9333ea', 'Social Connection': '#0891b2' };
    p.goals.forEach(function(g, i) {
      var color = catColors[g.cat] || '#64748b';
      var div = document.createElement('div');
      div.className = 'goal-item';
      div.innerHTML =
        '<div class="goal-cat-dot" style="background:' + color + ';"></div>' +
        '<div class="goal-body">' +
          '<div class="goal-top">' +
            '<span class="goal-title-text">' + g.title + '</span>' +
            '<span class="goal-cat-tag" style="background:' + color + '18;color:' + color + ';border:1px solid ' + color + '30;">' + g.cat + '</span>' +
          '</div>' +
          '<div class="goal-desc">' + g.desc + '</div>' +
          '<div class="goal-date">Target: ' + g.date + '</div>' +
          '<div class="goal-progress-row">' +
            '<div class="goal-prog-track"><div class="goal-prog-bar" style="width:' + g.prog + '%;background:linear-gradient(90deg,' + color + ',' + color + '88);"></div></div>' +
            '<span class="goal-prog-pct" style="color:' + color + ';">' + g.prog + '%</span>' +
          '</div>' +
        '</div>' +
        '<div class="goal-actions">' +
          '<button class="btn-outline-sm" onclick="editGoal(\'' + p.id + '\',' + i + ')">Edit</button>' +
        '</div>';
      list.appendChild(div);
    });
    ri();
  }

  window.editGoal = function(pid, i) {
    showToast('Editing goal...');
  };

  /* ============================================================
     HABITS
  ============================================================ */
  var DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

  function buildHabits(p) {
    var wrap = document.getElementById('pd-habits-wrap');
    if (!wrap) return;

    var table = document.createElement('table');
    table.className = 'habits-table';

    // Header
    var thead = '<thead><tr><th>Habit</th>';
    DAYS.forEach(function(d) { thead += '<th>' + d + '</th>'; });
    thead += '</tr></thead>';
    table.innerHTML = thead;

    var tbody = document.createElement('tbody');
    p.habits.forEach(function(habit, hi) {
      var tr = document.createElement('tr');
      var row = '<td>' + habit + '</td>';
      DAYS.forEach(function(d, di) {
        var checked = p.habitData[hi] && p.habitData[hi][di];
        row += '<td><span class="habit-check ' + (checked ? 'habit-done' : 'habit-miss') + '" ' +
          'data-pid="' + p.id + '" data-hi="' + hi + '" data-di="' + di + '" ' +
          'onclick="toggleHabit(this)">' +
          (checked ? '✓' : '○') + '</span></td>';
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
    var p   = PATIENTS.find(function(x) { return x.id === pid; });
    if (!p) return;
    p.habitData[hi][di] = !p.habitData[hi][di];
    var checked = p.habitData[hi][di];
    el.textContent = checked ? '✓' : '○';
    el.className = 'habit-check ' + (checked ? 'habit-done' : 'habit-miss');
  };

  /* ============================================================
     PATIENT COACHING NOTES
  ============================================================ */
  function buildPatientNotes(p) {
    var list = document.getElementById('pd-notes-list');
    if (!list) return;
    list.innerHTML = '';
    p.notes.forEach(function(n, i) {
      var div = document.createElement('div');
      div.className = 'global-note-item';
      div.innerHTML =
        '<div class="gn-top">' +
          '<span class="gn-patient">' + p.name + '</span>' +
          '<div class="gn-meta">' +
            '<span class="gn-type">' + n.type + '</span>' +
            '<span class="gn-date">' + n.date + '</span>' +
          '</div>' +
        '</div>' +
        '<div class="gn-text">' + n.text + '</div>' +
        '<div class="gn-actions">' +
          '<button class="btn-outline-sm" onclick="deletePatientNote(\'' + p.id + '\',' + i + ')">Delete</button>' +
        '</div>';
      list.appendChild(div);
    });
  }

  window.deletePatientNote = function(pid, i) {
    var p = PATIENTS.find(function(x) { return x.id === pid; });
    if (!p) return;
    if (confirm('Delete this note?')) {
      p.notes.splice(i, 1);
      buildPatientNotes(p);
      buildGlobalNotes();
      showToast('Note deleted.');
    }
  };

  // Add note from patient detail
  var pdAddNoteBtn = document.getElementById('pd-add-note-btn');
  if (pdAddNoteBtn) {
    pdAddNoteBtn.addEventListener('click', function() {
      var p = PATIENTS.find(function(x) { return x.id === currentPatientId; });
      if (p) {
        var sel = document.getElementById('note-patient');
        if (sel) sel.value = p.name;
      }
      openModal('note-modal');
    });
  }

  /* ============================================================
     ADD RX (from patient detail)
  ============================================================ */
  var addRxBtn = document.getElementById('add-rx-btn');
  if (addRxBtn) {
    addRxBtn.addEventListener('click', function() {
      showToast('Rx management — contact the assigned psychiatrist.');
    });
  }

  /* ============================================================
     GLOBAL COACHING NOTES
  ============================================================ */
  function buildGlobalNotes(filter) {
    var list = document.getElementById('global-notes-list');
    if (!list) return;
    var q   = (document.getElementById('notes-search') ? document.getElementById('notes-search').value.toLowerCase() : '');
    var pf  = (document.getElementById('notes-patient-filter') ? document.getElementById('notes-patient-filter').value : '');
    var src = filter || GLOBAL_NOTES;

    var shown = src.filter(function(n) {
      var mq = !q || n.text.toLowerCase().includes(q) || n.patient.toLowerCase().includes(q);
      var mp = !pf || n.patient === pf;
      return mq && mp;
    });

    list.innerHTML = '';
    if (!shown.length) {
      list.innerHTML = '<div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">No notes found.</div>';
      return;
    }

    shown.forEach(function(n, i) {
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

  var notesSearch = document.getElementById('notes-search');
  var notesFilter = document.getElementById('notes-patient-filter');
  if (notesSearch) notesSearch.addEventListener('input', function() { buildGlobalNotes(); });
  if (notesFilter) notesFilter.addEventListener('change', function() { buildGlobalNotes(); });

  // Add global note
  var addNoteGlobalBtn = document.getElementById('add-note-global-btn');
  if (addNoteGlobalBtn) {
    addNoteGlobalBtn.addEventListener('click', function() { openModal('note-modal'); });
  }

  var saveNoteBtn = document.getElementById('save-note-btn');
  if (saveNoteBtn) {
    saveNoteBtn.addEventListener('click', function() {
      var patient = document.getElementById('note-patient').value;
      var type    = document.getElementById('note-type').value;
      var text    = document.getElementById('note-text').value.trim();
      if (!text) { showToast('Please enter a note.'); return; }

      var d = new Date();
      var dateStr = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
      var newNote = { patient: patient, type: type, date: dateStr, text: text };

      // Add to global notes
      GLOBAL_NOTES.unshift(newNote);

      // Also add to patient's own notes
      var p = PATIENTS.find(function(x) { return x.name === patient; });
      if (p) {
        p.notes.unshift({ type: type, date: dateStr, text: text });
        if (currentPatientId === p.id) buildPatientNotes(p);
      }

      buildGlobalNotes();
      buildDashboard();
      closeModal('note-modal');
      document.getElementById('note-text').value = '';
      showToast('Note saved for ' + patient + '.');
    });
  }

  /* ============================================================
     TASKS
  ============================================================ */
  var activeTaskFilter = 'all';

  function buildTasks() {
    var list = document.getElementById('tasks-full-list');
    if (!list) return;
    list.innerHTML = '';

    var filtered = TASKS.filter(function(t) {
      if (activeTaskFilter === 'all')  return true;
      if (activeTaskFilter === 'done') return t.done;
      return t.priority === activeTaskFilter && !t.done;
    });

    if (!filtered.length) {
      list.innerHTML = '<div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">No tasks found.</div>';
      return;
    }

    filtered.forEach(function(t, i) {
      var realIdx = TASKS.indexOf(t);
      var div = document.createElement('div');
      div.className = 'task-full-row' + (t.done ? ' task-done' : '');
      div.innerHTML =
        '<div class="task-checkbox-wrap">' +
          '<input type="checkbox" class="task-cb" ' + (t.done ? 'checked' : '') + ' onchange="toggleTask(' + realIdx + ', this)"/>' +
        '</div>' +
        '<div class="task-full-body">' +
          '<div class="task-full-patient">' + t.patient + '</div>' +
          '<div class="task-full-desc">' + t.desc + '</div>' +
          '<div class="task-full-due"><i data-feather="calendar" style="width:12px;height:12px;"></i> Due: ' + t.due + '</div>' +
        '</div>' +
        priorityBadge(t.priority);
      list.appendChild(div);
    });
    ri();
  }

  window.toggleTask = function(i, cb) {
    TASKS[i].done = cb.checked;
    buildTasks();
    buildDashboard();
    showToast(cb.checked ? '✓ Task completed!' : 'Task reopened.');
  };

  qsa('.task-filter-pill').forEach(function(pill) {
    pill.addEventListener('click', function() {
      qsa('.task-filter-pill').forEach(function(p) { p.classList.remove('active'); });
      this.classList.add('active');
      activeTaskFilter = this.getAttribute('data-filter');
      buildTasks();
    });
  });

  // Add Task
  var addTaskBtn = document.getElementById('add-task-btn');
  if (addTaskBtn) addTaskBtn.addEventListener('click', function() { openModal('task-modal'); });

  var saveTaskBtn = document.getElementById('save-task-btn');
  if (saveTaskBtn) {
    saveTaskBtn.addEventListener('click', function() {
      var patient  = document.getElementById('task-patient').value;
      var desc     = document.getElementById('task-desc').value.trim();
      var priority = document.getElementById('task-priority').value;
      var due      = document.getElementById('task-due').value;
      if (!desc) { showToast('Please enter a task description.'); return; }
      TASKS.unshift({ patient: patient, desc: desc, priority: priority, due: due || 'TBD', done: false });
      buildTasks();
      buildDashboard();
      closeModal('task-modal');
      document.getElementById('task-desc').value = '';
      showToast('Task added for ' + patient + '.');
    });
  }

  /* ============================================================
     ADD GOAL
  ============================================================ */
  var addGoalBtn = document.getElementById('add-goal-btn');
  if (addGoalBtn) {
    addGoalBtn.addEventListener('click', function() { openModal('goal-modal'); });
  }

  var saveGoalBtn = document.getElementById('save-goal-btn');
  if (saveGoalBtn) {
    saveGoalBtn.addEventListener('click', function() {
      var title = document.getElementById('goal-title').value.trim();
      var cat   = document.getElementById('goal-category').value;
      var date  = document.getElementById('goal-date').value;
      var desc  = document.getElementById('goal-desc').value.trim();
      if (!title) { showToast('Please enter a goal title.'); return; }
      var p = PATIENTS.find(function(x) { return x.id === currentPatientId; });
      if (!p) return;
      p.goals.unshift({ title: title, cat: cat, desc: desc, date: date || 'TBD', prog: 0 });
      buildGoals(p);
      closeModal('goal-modal');
      ['goal-title','goal-desc','goal-date'].forEach(function(id) { var el = document.getElementById(id); if (el) el.value = ''; });
      showToast('Goal added.');
    });
  }

  /* ============================================================
     SCHEDULE FOLLOW-UP
  ============================================================ */
  var addScheduleBtn = document.getElementById('add-schedule-btn');
  if (addScheduleBtn) addScheduleBtn.addEventListener('click', function() { openModal('schedule-modal'); });

  var saveSchedBtn = document.getElementById('save-sched-btn');
  if (saveSchedBtn) {
    saveSchedBtn.addEventListener('click', function() {
      var patient = document.getElementById('sched-patient').value;
      var topic   = document.getElementById('sched-topic').value.trim();
      var date    = document.getElementById('sched-date').value;
      var time    = document.getElementById('sched-time').value;
      if (!topic) { showToast('Please enter a topic.'); return; }

      // Format date
      var dateLabel = date ? new Date(date + 'T00:00').toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : 'TBD';
      // Format time
      var timeLabel = 'TBD';
      if (time) {
        var parts = time.split(':');
        var h = parseInt(parts[0]); var m = parts[1];
        var ap = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        timeLabel = h + ':' + m + ' ' + ap;
      }
      SCHEDULES.push({ patient: patient, topic: topic, date: dateLabel, time: timeLabel });
      buildSchedule();
      closeModal('schedule-modal');
      showToast('Follow-up scheduled for ' + patient + '.');
    });
  }

  /* ============================================================
     INITIAL RENDER
  ============================================================ */
  buildDashboard();
  buildPatientCards();
  buildGlobalNotes();
  buildTasks();
  ri();

});
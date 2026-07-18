/* ============================================================
   lifecoach_data.js — Shared data store
   Included on every lifecoach page. All pages read from this.
   ============================================================ */

var LC_DATA = {

  PATIENTS: [
    {
      id: 'P001', name: 'Sarah Johnson', age: 34, sex: 'Female', status: 'Active',
      complaint: 'Depression and anxiety', nextAppt: '2026-06-10', coach: 'Michael Chen',
      email: 'sarah.johnson@email.com', phone: '+63 912 345 6789',
      program: 'Cognitive Behavioral Coaching',
      prescriptions: [
        { tag: 'Sleep Hygiene', name: 'Sleep Routine Protocol' },
        { tag: 'Exercise',      name: 'Daily Walking Program' },
        { tag: 'Nutrition',     name: 'Mediterranean Diet Plan' },
      ],
      metrics: [
        { name: 'Sleep Quality', value: '7.5 hrs', pct: 75, bar: 'mbar-green', val: 'mval-green', icon: 'moon' },
        { name: 'Exercise',      value: '4×/week', pct: 50, bar: 'mbar-amber', val: 'mval-amber', icon: 'activity' },
        { name: 'Nutrition',     value: 'Good',    pct: 80, bar: 'mbar-green', val: 'mval-green', icon: 'heart' },
        { name: 'Stress Level',  value: 'Moderate',pct: 60, bar: 'mbar-amber', val: 'mval-amber', icon: 'zap' },
        { name: 'Hydration',     value: '2L/day',  pct: 85, bar: 'mbar-green', val: 'mval-green', icon: 'droplet' },
      ],
      goals: [
        { title: 'Establish consistent sleep schedule', cat: 'Sleep',     desc: 'Sleep by 10:30 PM and wake at 6:30 AM daily.', date: 'Jun 30, 2026', prog: 70 },
        { title: 'Walk 30 minutes daily',               cat: 'Exercise',  desc: '30-minute brisk walk each morning before work.', date: 'Jun 20, 2026', prog: 55 },
        { title: 'Mediterranean diet adherence',        cat: 'Nutrition', desc: 'Follow meal plan 6 out of 7 days per week.', date: 'Jul 15, 2026', prog: 80 },
      ],
      habits: ['Sleep 10:30 PM', 'Morning Walk', 'Meal Plan', 'Journaling', 'No caffeine after 2 PM'],
      habitData: [
        [true, true, true, false, true, true, true],
        [true, false, true, true, true, false, true],
        [true, true, true, true, false, true, false],
        [false, true, false, true, true, true, true],
        [true, true, true, true, true, true, false],
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
      complaint: 'Panic attacks', nextAppt: '2026-06-08', coach: 'Michael Chen',
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
        { title: 'Complete daily meditation',        cat: 'Mental Wellness',    desc: '10 minutes of mindfulness each morning.', date: 'Jun 15, 2026', prog: 40 },
        { title: 'Reduce panic episode frequency',   cat: 'Stress Management',  desc: 'Use breathing exercises during triggers.', date: 'Jul 1, 2026',  prog: 30 },
      ],
      habits: ['Morning Meditation', 'Box Breathing', 'Limit Caffeine', 'Evening Walk'],
      habitData: [
        [true, false, true, false, true, false, false],
        [false, true, false, false, true, true, false],
        [true, true, false, true, false, false, true],
        [false, false, true, false, true, true, false],
      ],
      compliance: [45, 50, 58, 62, 55, 48, 60],
      notes: [
        { type: 'Crisis Support', date: 'Jun 4, 2026',  text: 'Patient experienced panic episode at work. Discussed anxiety management and introduced box breathing exercises. Patient receptive.' },
        { type: 'Follow-up',      date: 'May 28, 2026', text: 'Reviewed triggers journal. Work stress identified as primary trigger. Discussing workplace boundaries.' },
      ],
    },
    {
      id: 'P002', name: 'David Martinez', age: 42, sex: 'Male', status: 'Active',
      complaint: 'Stress management', nextAppt: '2026-06-12', coach: 'Michael Chen',
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
        { title: 'Strength training consistency', cat: 'Exercise',          desc: 'Complete 3 gym sessions per week for 8 weeks.', date: 'Jul 30, 2026', prog: 60 },
        { title: 'Work-life balance',             cat: 'Stress Management', desc: 'No work emails after 7 PM.', date: 'Jun 30, 2026', prog: 45 },
      ],
      habits: ['Gym Session', 'Evening Routine', 'No Late Emails', 'Healthy Lunch'],
      habitData: [
        [true, false, true, false, true, true, false],
        [true, true, true, false, false, true, true],
        [false, true, false, true, true, false, true],
        [true, true, true, true, false, true, false],
      ],
      compliance: [60, 65, 70, 68, 75, 72, 78],
      notes: [
        { type: 'Follow-up', date: 'Jun 3, 2026',  text: 'Patient showing improvement in stress management. Strength training program going well. Sleep quality improving.' },
        { type: 'Check-in',  date: 'May 27, 2026', text: 'Reviewed work-life balance strategies. Patient finding it difficult to disconnect from work after hours.' },
      ],
    },
  ],

  TASKS: [
    { patient: 'Sarah Johnson',  desc: 'Follow-up on sleep hygiene progress',       priority: 'High',   due: '2026-06-08', done: false },
    { patient: 'Emily Thompson', desc: 'Review anxiety management exercises',        priority: 'High',   due: '2026-06-09', done: false },
    { patient: 'David Martinez', desc: 'Check nutrition plan adherence',             priority: 'Medium', due: '2026-06-12', done: false },
    { patient: 'Sarah Johnson',  desc: 'Prepare sleep diary review worksheet',       priority: 'Medium', due: '2026-06-14', done: false },
    { patient: 'Emily Thompson', desc: 'Send breathing exercise reminder materials', priority: 'Low',    due: '2026-06-16', done: false },
  ],

  SCHEDULES: [
    { patient: 'Sarah Johnson',  topic: 'Sleep hygiene review',        date: 'Jun 8',  time: '2:00 PM'  },
    { patient: 'Emily Thompson', topic: 'Anxiety management check-in', date: 'Jun 9',  time: '10:00 AM' },
    { patient: 'David Martinez', topic: 'Stress management review',    date: 'Jun 11', time: '3:00 PM'  },
  ],

  COACH: {
    name:    'Michael Chen',
    role:    'Life Coach',
    clinic:  'MedCare Clinic',
    email:   'coach@medcare.ph',
    license: 'LC-2026-001',
    specializations: ['Cognitive Behavioral Coaching', 'Stress Management', 'Anxiety Management', 'Sleep Coaching'],
    initials: 'MC',
  },

  // Build global notes from patient notes
  globalNotes: function() {
    var all = [];
    LC_DATA.PATIENTS.forEach(function(p) {
      p.notes.forEach(function(n) {
        all.push({ patient: p.name, patientId: p.id, type: n.type, date: n.date, text: n.text });
      });
    });
    return all;
  },
};

/* ============================================================
   SHARED HELPERS — available on every page
   ============================================================ */

function lcShow(el) { if (el) el.classList.remove('hidden'); }
function lcHide(el) { if (el) el.classList.add('hidden'); }

function lcToast(msg) {
  var t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  lcShow(t);
  clearTimeout(t._t);
  t._t = setTimeout(function() { lcHide(t); }, 2800);
}

function lcInitials(name) {
  return name.split(' ').map(function(w) { return w[0]; }).join('').slice(0, 2).toUpperCase();
}

function lcPriorityBadge(p) {
  var cls = p === 'High' ? 'badge-high' : p === 'Medium' ? 'badge-medium' : 'badge-low';
  return '<span class="badge ' + cls + '">' + p + '</span>';
}

function lcStatusBadge(s) {
  var cls = s === 'Active' ? 'badge-active' : s === 'Critical' ? 'badge-critical' : 'badge-inactive';
  return '<span class="badge ' + cls + '">' + s + '</span>';
}

function lcOpenModal(id) {
  var el = document.getElementById(id);
  if (el) { lcShow(el); if (window.feather) window.feather.replace(); }
}

function lcCloseModal(id) {
  var el = document.getElementById(id);
  if (el) lcHide(el);
}

function lcRi() { if (window.feather) window.feather.replace(); }

// Sidebar active state — call on each page with current page key
function lcInitSidebar(activeKey) {
  var map = {
    dashboard: '/lifecoach/dashboard',
    patients:  '/lifecoach/patients',
    notes:     '/lifecoach/notes',
    tasks:     '/lifecoach/tasks',
    profile:   '/lifecoach/profile',
  };

  document.querySelectorAll('.nav-item[data-page]').forEach(function(btn) {
    btn.classList.toggle('active', btn.getAttribute('data-page') === activeKey);
    btn.addEventListener('click', function() {
      var page = this.getAttribute('data-page');
      if (page && map[page]) window.location.href = map[page];
    });
  });

  var logout = document.getElementById('logout-btn');
  if (logout) logout.addEventListener('click', function() { window.location.href = '/'; });

  var ham = document.getElementById('hamburger-btn');
  var sidebar = document.getElementById('sidebar');
  if (ham && sidebar) {
    ham.addEventListener('click', function() { sidebar.classList.toggle('open'); });
  }

  // Close modal on overlay click or data-close
  document.addEventListener('click', function(e) {
    var cl = e.target.closest('[data-close]');
    if (cl) { lcCloseModal(cl.getAttribute('data-close')); return; }
    if (e.target.classList.contains('modal-overlay')) {
      document.querySelectorAll('.modal-overlay').forEach(function(m) { lcHide(m); });
    }
  });
}

/* lifecoach_dashboard.js */
document.addEventListener('DOMContentLoaded', function () {
  lcInitSidebar('dashboard');

  var SCHEDULES = LC_DATA.SCHEDULES.slice();

  // Stats
  var statTasks = document.getElementById('stat-tasks');
  if (statTasks) statTasks.textContent = LC_DATA.TASKS.filter(function(t){ return !t.done; }).length;

  // Patient rows
  var dpList = document.getElementById('dash-patient-list');
  if (dpList) {
    LC_DATA.PATIENTS.forEach(function(p) {
      var row = document.createElement('div');
      row.className = 'dash-patient-row';
      row.innerHTML =
        '<div>' +
          '<div class="dash-patient-name">' + p.name + '</div>' +
          '<div class="dash-patient-complaint">' + p.complaint + '</div>' +
        '</div>' +
        '<a href="/lifecoach/patients?id=' + p.id + '" class="btn-outline-sm">View</a>';
      dpList.appendChild(row);
    });
  }

  // Task rows (top 3 pending)
  var dtList = document.getElementById('dash-task-list');
  if (dtList) {
    LC_DATA.TASKS.filter(function(t){ return !t.done; }).slice(0, 3).forEach(function(t) {
      var row = document.createElement('div');
      row.className = 'dash-task-row';
      row.innerHTML =
        '<div class="dash-task-top">' +
          '<span class="dash-task-patient">' + t.patient + '</span>' +
          lcPriorityBadge(t.priority) +
        '</div>' +
        '<div class="dash-task-desc">' + t.desc + '</div>' +
        '<div class="dash-task-due">Due: ' + t.due + '</div>';
      dtList.appendChild(row);
    });
  }

  // Schedule
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
  buildSchedule();

  // Add Schedule
  var addSchedBtn = document.getElementById('add-schedule-btn');
  if (addSchedBtn) addSchedBtn.addEventListener('click', function(){ lcOpenModal('schedule-modal'); });

  var saveSchedBtn = document.getElementById('save-sched-btn');
  if (saveSchedBtn) {
    saveSchedBtn.addEventListener('click', function() {
      var patient = document.getElementById('sched-patient').value;
      var topic   = document.getElementById('sched-topic').value.trim();
      var date    = document.getElementById('sched-date').value;
      var time    = document.getElementById('sched-time').value;
      if (!topic) { lcToast('Please enter a topic.'); return; }
      var dateLabel = date ? new Date(date + 'T00:00').toLocaleDateString('en-US',{month:'short',day:'numeric'}) : 'TBD';
      var timeLabel = 'TBD';
      if (time) {
        var parts = time.split(':'); var h = parseInt(parts[0]); var m = parts[1];
        var ap = h >= 12 ? 'PM' : 'AM'; h = h % 12 || 12;
        timeLabel = h + ':' + m + ' ' + ap;
      }
      SCHEDULES.push({ patient: patient, topic: topic, date: dateLabel, time: timeLabel });
      buildSchedule();
      lcCloseModal('schedule-modal');
      lcToast('Follow-up scheduled for ' + patient + '.');
    });
  }

  lcRi();
});

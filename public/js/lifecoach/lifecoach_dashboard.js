/* lifecoach_dashboard.js */
document.addEventListener('DOMContentLoaded', function () {
  lcInitSidebar('dashboard');

  var SCHEDULES = (LC_DATA.SCHEDULES || []).slice();
  var PATIENTS = LC_DATA.PATIENTS || [];
  var TASKS = LC_DATA.TASKS || [];

  var statPatients = document.getElementById('stat-patients');
  var statTasks = document.getElementById('stat-tasks');
  var statCompleted = document.getElementById('stat-completed');
  var statProgress = document.getElementById('stat-progress');
  var stats = LC_DATA.STATS || {};

  if (statPatients) statPatients.textContent = stats.patient_count != null ? stats.patient_count : PATIENTS.length;
  if (statTasks) statTasks.textContent = stats.pending_tasks != null
    ? stats.pending_tasks
    : TASKS.filter(function (t) { return !t.done; }).length;
  if (statCompleted) statCompleted.textContent = stats.completed_this_week != null ? stats.completed_this_week : 0;
  if (statProgress) {
    var avg = stats.avg_progress != null ? stats.avg_progress : 0;
    statProgress.textContent = avg ? (avg + '%') : '—';
  }

  var dpList = document.getElementById('dash-patient-list');
  if (dpList) {
    dpList.innerHTML = '';
    if (!PATIENTS.length) {
      dpList.innerHTML = '<div style="padding:16px;color:#94a3b8;font-size:13px;">No assigned patients yet.</div>';
    } else {
      PATIENTS.forEach(function (p) {
        var row = document.createElement('div');
        row.className = 'dash-patient-row';
        row.innerHTML =
          '<div>' +
            '<div class="dash-patient-name">' + lcEscape(p.name) + '</div>' +
            '<div class="dash-patient-complaint">' + lcEscape(p.complaint || '—') + '</div>' +
          '</div>' +
          '<a href="' + lcRoute('patients') + '?id=' + p.id + '" class="btn-outline-sm">View</a>';
        dpList.appendChild(row);
      });
    }
  }

  var dtList = document.getElementById('dash-task-list');
  if (dtList) {
    dtList.innerHTML = '';
    var pending = TASKS.filter(function (t) { return !t.done; }).slice(0, 3);
    if (!pending.length) {
      dtList.innerHTML = '<div style="padding:16px;color:#94a3b8;font-size:13px;">No pending tasks.</div>';
    } else {
      pending.forEach(function (t) {
        var row = document.createElement('div');
        row.className = 'dash-task-row';
        row.innerHTML =
          '<div class="dash-task-top">' +
            '<span class="dash-task-patient">' + lcEscape(t.patient) + '</span>' +
            lcPriorityBadge(t.priority) +
          '</div>' +
          '<div class="dash-task-desc">' + lcEscape(t.desc) + '</div>' +
          '<div class="dash-task-due">Due: ' + lcEscape(t.due) + '</div>';
        dtList.appendChild(row);
      });
    }
  }

  function buildSchedule() {
    var list = document.getElementById('schedule-list');
    if (!list) return;
    list.innerHTML = '';
    if (!SCHEDULES.length) {
      list.innerHTML = '<div style="padding:16px;color:#94a3b8;font-size:13px;">No follow-ups scheduled this week.</div>';
      return;
    }
    SCHEDULES.forEach(function (s) {
      var row = document.createElement('div');
      row.className = 'schedule-row';
      row.innerHTML =
        '<div class="schedule-info">' +
          '<div class="schedule-patient">' + lcEscape(s.patient) + '</div>' +
          '<div class="schedule-topic">' + lcEscape(s.topic) + '</div>' +
        '</div>' +
        '<div class="schedule-time-block">' +
          '<div class="schedule-date">' + lcEscape(s.date) + '</div>' +
          '<div class="schedule-time">' + lcEscape(s.time) + '</div>' +
        '</div>';
      list.appendChild(row);
    });
  }
  buildSchedule();

  lcFillPatientSelect(document.getElementById('sched-patient'));

  var addSchedBtn = document.getElementById('add-schedule-btn');
  if (addSchedBtn) {
    addSchedBtn.addEventListener('click', function () {
      if (!(LC_DATA.PATIENT_OPTIONS || []).length && !(LC_DATA.PATIENTS || []).length) {
        lcToast('Assign a patient first.');
        return;
      }
      lcOpenModal('schedule-modal');
    });
  }

  var saveSchedBtn = document.getElementById('save-sched-btn');
  if (saveSchedBtn) {
    saveSchedBtn.addEventListener('click', function () {
      var patientId = document.getElementById('sched-patient').value;
      var topic = document.getElementById('sched-topic').value.trim();
      var date = document.getElementById('sched-date').value;
      var time = document.getElementById('sched-time').value;
      if (!patientId) { lcToast('Select a patient.'); return; }
      if (!topic) { lcToast('Please enter a topic.'); return; }
      if (!date) { lcToast('Please select a date.'); return; }

      saveSchedBtn.disabled = true;
      lcApi(lcRoute('schedulesStore'), {
        method: 'POST',
        body: JSON.stringify({
          patient_record_id: Number(patientId),
          topic: topic,
          date: date,
          time: time || null,
        }),
      }).then(function (data) {
        SCHEDULES.push(data.schedule);
        LC_DATA.SCHEDULES = SCHEDULES;
        buildSchedule();
        lcCloseModal('schedule-modal');
        document.getElementById('sched-topic').value = '';
        lcToast(data.message || 'Follow-up scheduled.');
      }).catch(function (err) {
        lcToast(err.message || 'Could not schedule.');
      }).finally(function () {
        saveSchedBtn.disabled = false;
      });
    });
  }

  lcRi();
});

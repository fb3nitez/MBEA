/* lifecoach_dashboard.js */
document.addEventListener('DOMContentLoaded', function () {
  lcInitSidebar('dashboard');

  var SCHEDULES = (LC_DATA.SCHEDULES || []).slice();
  var PATIENTS = LC_DATA.PATIENTS || [];
  var TASKS = LC_DATA.TASKS || [];
  var updates = (LC_DATA.UPDATES || {});

  var statPatients = document.getElementById('stat-patients');
  var statTasks = document.getElementById('stat-tasks');
  var statCompleted = document.getElementById('stat-completed');
  var statProgress = document.getElementById('stat-progress');
  var stats = LC_DATA.STATS || {};

  if (statPatients) statPatients.textContent = stats.patient_count != null ? stats.patient_count : PATIENTS.length;
  if (statTasks) statTasks.textContent = (updates.unread_count || 0) + (updates.pending_tasks || TASKS.filter(function (t) { return !t.done; }).length);
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
  function updateDestination(item) {
    if (item.type === 'task') return lcRoute('tasks');
    if (item.related_type === 'patient') return lcRoute('patients') + '?id=' + encodeURIComponent(item.related_id);
    if (item.related_type === 'note') return lcRoute('notes');
    return lcRoute('dashboard');
  }

  function renderUpdatesCard() {
    if (!dtList) return;
    dtList.innerHTML = '';
    var pending = updates.items || [];
    if (!pending.length) {
      dtList.innerHTML = '<div class="lc-updates-empty">You\'re all caught up.</div>';
    } else {
      pending.forEach(function (t) {
        var row = document.createElement('div');
        row.className = 'dash-task-row lc-update-row' + (t.read ? '' : ' is-unread');
        row.innerHTML =
          '<div class="dash-task-top">' +
            '<span class="dash-task-patient"><i data-feather="' + lcEscape(t.icon || 'bell') + '"></i>' + lcEscape(t.title) + '</span>' +
            (t.read ? '' : '<span class="lc-unread-dot" aria-label="Unread"></span>') +
          '</div>' +
          '<div class="dash-task-desc">' + lcEscape(t.body || '') + '</div>' +
          '<div class="dash-task-due">' + lcEscape(t.relative_time || '') + '</div>' +
          '<button type="button" class="btn-outline-sm lc-update-open">Open</button>';
        row.querySelector('.lc-update-open').addEventListener('click', function (event) { event.stopPropagation(); openUpdate(t); });
        row.addEventListener('click', function () { openUpdate(t); });
        dtList.appendChild(row);
      });
    }
    lcRi();
  }

  function openUpdate(item) {
    var destination = updateDestination(item);
    if (item.type !== 'task' && item.id) {
      markUpdateRead(item.id).finally(function () { window.location.href = destination; });
      return;
    }
    window.location.href = destination;
  }

  renderUpdatesCard();

  function markUpdateRead(id) { return lcApi((LC_ROUTES.updatesRead || '').replace('__ID__', id), { method: 'PUT' }).then(function () { refreshUpdates(); }); }
  function refreshUpdates(type, append) { var offset = append ? (updates.offset || 0) : 0; return lcApi((LC_ROUTES.updates || '') + '?offset=' + offset + (type ? '&type=' + encodeURIComponent(type) : '')).then(function (data) { updates = Object.assign({}, data, { offset: offset + (data.items || []).length }); LC_DATA.UPDATES = updates; renderUpdatesCard(); renderUpdatesPanel(data.items || [], append); }); }
  function renderUpdatesPanel(items, append) { var list = document.getElementById('updates-list'); if (!list) return; if (!append) list.innerHTML = ''; if (!items.length && !append) list.innerHTML = '<div class="lc-updates-empty">You\'re all caught up.</div>'; (items || []).forEach(function (item) { var row = document.createElement('div'); row.className = 'lc-panel-update ' + (item.read ? '' : 'is-unread'); row.innerHTML = '<i data-feather="' + lcEscape(item.icon || 'bell') + '"></i><div><strong>' + lcEscape(item.title) + '</strong><small>' + lcEscape(item.body || '') + '</small></div><button class="lc-update-open" type="button">Open</button><button class="lc-update-read" aria-label="Mark update as read"><i data-feather="check"></i></button>'; row.querySelector('.lc-update-open').addEventListener('click', function (e) { e.stopPropagation(); openUpdate(item); }); row.querySelector('.lc-update-read').addEventListener('click', function (e) { e.stopPropagation(); markUpdateRead(item.id); }); row.addEventListener('click', function () { openUpdate(item); }); list.appendChild(row); }); lcRi(); }
  var panel = document.getElementById('updates-panel');
  function openUpdates() { panel.classList.add('is-open'); panel.setAttribute('aria-hidden', 'false'); refreshUpdates().then(function () { renderUpdatesPanel(updates.items || [], false); }); document.getElementById('close-updates-btn').focus(); }
  function closeUpdates() { panel.classList.remove('is-open'); panel.setAttribute('aria-hidden', 'true'); document.getElementById('open-updates-btn').focus(); }
  document.getElementById('open-updates-btn')?.addEventListener('click', openUpdates); document.getElementById('close-updates-btn')?.addEventListener('click', closeUpdates); document.getElementById('updates-backdrop')?.addEventListener('click', closeUpdates); document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && panel.classList.contains('is-open')) closeUpdates(); });
  document.querySelectorAll('[data-update-filter]').forEach(function (button) { button.addEventListener('click', function () { document.querySelectorAll('[data-update-filter]').forEach(function (b) { b.classList.remove('active'); }); button.classList.add('active'); refreshUpdates(button.dataset.updateFilter).then(function () { renderUpdatesPanel(updates.items || [], false); }); }); });
  document.getElementById('mark-all-updates-btn')?.addEventListener('click', function () { lcApi(LC_ROUTES.updatesReadAll, { method: 'PUT' }).then(refreshUpdates); }); document.getElementById('load-more-updates')?.addEventListener('click', function () { refreshUpdates(null, true); });

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

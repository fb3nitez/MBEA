/* lifecoach_tasks.js */
document.addEventListener('DOMContentLoaded', function () {
  lcInitSidebar('tasks');

  var TASKS = (LC_DATA.TASKS || []).slice();
  var activeFilter = 'all';

  function buildTasks() {
    var list = document.getElementById('tasks-full-list');
    var summary = document.getElementById('tasks-summary');
    if (!list) return;

    var filtered = TASKS.filter(function (t) {
      if (activeFilter === 'all') return true;
      if (activeFilter === 'done') return t.done;
      return t.priority === activeFilter && !t.done;
    });

    if (summary) {
      var pending = TASKS.filter(function (t) { return !t.done; }).length;
      var completed = TASKS.filter(function (t) { return t.done; }).length;
      var high = TASKS.filter(function (t) { return t.priority === 'High' && !t.done; }).length;
      summary.innerHTML =
        '<div class="task-summary-item"><span class="ts-val">' + pending + '</span><span class="ts-lbl">Pending</span></div>' +
        '<div class="task-summary-item"><span class="ts-val ts-high">' + high + '</span><span class="ts-lbl">High Priority</span></div>' +
        '<div class="task-summary-item"><span class="ts-val ts-done">' + completed + '</span><span class="ts-lbl">Completed</span></div>';
    }

    list.innerHTML = '';
    if (!filtered.length) {
      list.innerHTML = '<div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">No tasks found.</div>';
      return;
    }

    filtered.forEach(function (t) {
      var div = document.createElement('div');
      div.className = 'task-full-row' + (t.done ? ' task-done' : '');
      div.innerHTML =
        '<div class="task-checkbox-wrap">' +
          '<input type="checkbox" class="task-cb" data-task-id="' + t.id + '" ' + (t.done ? 'checked' : '') + '/>' +
        '</div>' +
        '<div class="task-full-body">' +
          '<div class="task-full-patient">' + lcEscape(t.patient) + '</div>' +
          '<div class="task-full-desc">' + lcEscape(t.desc) + '</div>' +
          '<div class="task-full-due">' +
            '<i data-feather="calendar" style="width:12px;height:12px;"></i> Due: ' + lcEscape(t.due) +
          '</div>' +
        '</div>' +
        lcPriorityBadge(t.priority);
      list.appendChild(div);
    });

    lcRi();
  }

  document.getElementById('tasks-full-list').addEventListener('change', function (e) {
    var cb = e.target.closest('.task-cb');
    if (!cb) return;
    var id = cb.getAttribute('data-task-id');
    var done = cb.checked;
    lcApi(lcRoute('tasksToggle', id), {
      method: 'PUT',
      body: JSON.stringify({ done: done }),
    }).then(function (data) {
      var idx = TASKS.findIndex(function (t) { return String(t.id) === String(id); });
      if (idx >= 0) TASKS[idx] = data.task;
      LC_DATA.TASKS = TASKS;
      buildTasks();
      lcToast(data.message || (done ? 'Task completed!' : 'Task reopened.'));
    }).catch(function (err) {
      cb.checked = !done;
      lcToast(err.message || 'Could not update task.');
    });
  });

  document.querySelectorAll('.task-filter-pill').forEach(function (pill) {
    pill.addEventListener('click', function () {
      document.querySelectorAll('.task-filter-pill').forEach(function (p) { p.classList.remove('active'); });
      this.classList.add('active');
      activeFilter = this.getAttribute('data-filter');
      buildTasks();
    });
  });

  lcFillPatientSelect(document.getElementById('task-patient'));

  document.getElementById('add-task-btn').addEventListener('click', function () {
    if (!(LC_DATA.PATIENT_OPTIONS || []).length) {
      lcToast('Assign a patient first.');
      return;
    }
    lcOpenModal('task-modal');
  });

  document.getElementById('save-task-btn').addEventListener('click', function () {
    var patientId = document.getElementById('task-patient').value;
    var desc = document.getElementById('task-desc').value.trim();
    var priority = document.getElementById('task-priority').value;
    var due = document.getElementById('task-due').value;
    if (!patientId) { lcToast('Select a patient.'); return; }
    if (!desc) { lcToast('Please enter a task description.'); return; }

    var btn = document.getElementById('save-task-btn');
    btn.disabled = true;
    lcApi(lcRoute('tasksStore'), {
      method: 'POST',
      body: JSON.stringify({
        patient_record_id: Number(patientId),
        description: desc,
        priority: priority,
        due_date: due || null,
      }),
    }).then(function (data) {
      TASKS.unshift(data.task);
      LC_DATA.TASKS = TASKS;
      buildTasks();
      lcCloseModal('task-modal');
      document.getElementById('task-desc').value = '';
      lcToast(data.message || 'Task added.');
    }).catch(function (err) {
      lcToast(err.message || 'Could not add task.');
    }).finally(function () {
      btn.disabled = false;
    });
  });

  buildTasks();
  lcRi();
});

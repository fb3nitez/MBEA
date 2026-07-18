/* lifecoach_tasks.js */
document.addEventListener('DOMContentLoaded', function () {
  lcInitSidebar('tasks');

  var activeFilter = 'all';

  function buildTasks() {
    var list = document.getElementById('tasks-full-list');
    var summary = document.getElementById('tasks-summary');
    if (!list) return;

    var filtered = LC_DATA.TASKS.filter(function(t) {
      if (activeFilter === 'all')  return true;
      if (activeFilter === 'done') return t.done;
      return t.priority === activeFilter && !t.done;
    });

    // Summary counts
    if (summary) {
      var pending   = LC_DATA.TASKS.filter(function(t){ return !t.done; }).length;
      var completed = LC_DATA.TASKS.filter(function(t){ return t.done; }).length;
      var high      = LC_DATA.TASKS.filter(function(t){ return t.priority === 'High' && !t.done; }).length;
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

    filtered.forEach(function(t) {
      var realIdx = LC_DATA.TASKS.indexOf(t);
      var div = document.createElement('div');
      div.className = 'task-full-row' + (t.done ? ' task-done' : '');
      div.innerHTML =
        '<div class="task-checkbox-wrap">' +
          '<input type="checkbox" class="task-cb" ' + (t.done ? 'checked' : '') +
          ' onchange="toggleTask(' + realIdx + ', this)"/>' +
        '</div>' +
        '<div class="task-full-body">' +
          '<div class="task-full-patient">' + t.patient + '</div>' +
          '<div class="task-full-desc">' + t.desc + '</div>' +
          '<div class="task-full-due">' +
            '<i data-feather="calendar" style="width:12px;height:12px;"></i> Due: ' + t.due +
          '</div>' +
        '</div>' +
        lcPriorityBadge(t.priority);
      list.appendChild(div);
    });

    lcRi();
  }

  window.toggleTask = function(i, cb) {
    LC_DATA.TASKS[i].done = cb.checked;
    buildTasks();
    lcToast(cb.checked ? '✓ Task completed!' : 'Task reopened.');
  };

  // Filter pills
  document.querySelectorAll('.task-filter-pill').forEach(function(pill) {
    pill.addEventListener('click', function() {
      document.querySelectorAll('.task-filter-pill').forEach(function(p){ p.classList.remove('active'); });
      this.classList.add('active');
      activeFilter = this.getAttribute('data-filter');
      buildTasks();
    });
  });

  // Add Task
  document.getElementById('add-task-btn').addEventListener('click', function() {
    lcOpenModal('task-modal');
  });

  document.getElementById('save-task-btn').addEventListener('click', function() {
    var patient  = document.getElementById('task-patient').value;
    var desc     = document.getElementById('task-desc').value.trim();
    var priority = document.getElementById('task-priority').value;
    var due      = document.getElementById('task-due').value;
    if (!desc) { lcToast('Please enter a task description.'); return; }
    LC_DATA.TASKS.unshift({ patient: patient, desc: desc, priority: priority, due: due || 'TBD', done: false });
    buildTasks();
    lcCloseModal('task-modal');
    document.getElementById('task-desc').value = '';
    lcToast('Task added for ' + patient + '.');
  });

  buildTasks();
  lcRi();
});

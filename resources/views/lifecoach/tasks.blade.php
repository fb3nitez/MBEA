<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MedCare — Tasks</title>
  <link rel="stylesheet" href="{{ asset('css/lifecoach.css') }}"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
  @include('lifecoach.partials.boot')
</head>
<body>
<div class="app-shell">

  @include('partials.lifecoach_sidebar', ['activePage' => 'tasks'])

  <div class="main-area">
    <header class="main-topbar">
      <div class="topbar-left">
        <button class="hamburger-btn" id="hamburger-btn"><i data-feather="menu"></i></button>
        <h1 class="page-title">Tasks</h1>
      </div>
      <div class="topbar-right">
        <span class="topbar-date" id="topbar-date"></span>
      </div>
    </header>

    <div class="content-wrap">
      <div class="card">
        <div class="card-header">
          <span class="card-title">My Tasks</span>
          <button class="btn-green" id="add-task-btn">
            <i data-feather="plus"></i> Add Task
          </button>
        </div>

        <div class="tasks-filter-row">
          <div class="task-filter-pills">
            <button class="task-filter-pill active" data-filter="all">All</button>
            <button class="task-filter-pill" data-filter="High">High</button>
            <button class="task-filter-pill" data-filter="Medium">Medium</button>
            <button class="task-filter-pill" data-filter="Low">Low</button>
            <button class="task-filter-pill" data-filter="done">Completed</button>
          </div>
        </div>

        <div class="tasks-summary-row" id="tasks-summary"></div>

        <div class="tasks-full-list" id="tasks-full-list"></div>
      </div>
    </div>
  </div>
</div>

<div class="modal-overlay hidden" id="task-modal">
  <div class="modal-box">
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:10px;">
        <div class="modal-icon green-modal-icon"><i data-feather="check-square"></i></div>
        <h3>Add Task</h3>
      </div>
      <button class="modal-close" data-close="task-modal"><i data-feather="x"></i></button>
    </div>
    <div class="modal-body">
      <div class="field-group">
        <label class="field-label">Patient</label>
        <select class="field-input" id="task-patient"></select>
      </div>
      <div class="field-group">
        <label class="field-label">Task Description</label>
        <input type="text" class="field-input" id="task-desc" placeholder="Describe the task..."/>
      </div>
      <div class="modal-grid-2">
        <div class="field-group">
          <label class="field-label">Priority</label>
          <select class="field-input" id="task-priority">
            <option value="High">High</option>
            <option value="Medium" selected>Medium</option>
            <option value="Low">Low</option>
          </select>
        </div>
        <div class="field-group">
          <label class="field-label">Due Date</label>
          <input type="date" class="field-input" id="task-due"/>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" data-close="task-modal">Cancel</button>
      <button class="btn-green" id="save-task-btn">Add Task</button>
    </div>
  </div>
</div>

<div class="toast hidden" id="toast"></div>

<script src="{{ asset('js/lifecoach_data.js?v=2.0') }}"></script>
<script src="{{ asset('js/lifecoach_tasks.js?v=2.0') }}"></script>
<script>feather.replace();</script>
</body>
</html>

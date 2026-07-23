<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MedCare — Life Coach Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/lifecoach.css') }}"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
  @include('lifecoach.partials.boot')
</head>
<body>
<div class="app-shell">

  @include('partials.lifecoach_sidebar', ['activePage' => 'dashboard'])

  <div class="main-area">
    <header class="main-topbar">
      <div class="topbar-left">
        <button class="hamburger-btn" id="hamburger-btn"><i data-feather="menu"></i></button>
        <h1 class="page-title">Dashboard</h1>
      </div>
      <div class="topbar-right">
        <span class="topbar-date" id="topbar-date"></span>
      </div>
    </header>

    <div class="content-wrap">

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-text">
            <div class="stat-label">Assigned Patients</div>
            <div class="stat-value" id="stat-patients">{{ $stats['patient_count'] ?? 0 }}</div>
          </div>
          <div class="stat-icon si-green"><i data-feather="users"></i></div>
        </div>
        <div class="stat-card">
          <div class="stat-text">
            <div class="stat-label">Pending Tasks</div>
            <div class="stat-value" id="stat-tasks">{{ $stats['pending_tasks'] ?? 0 }}</div>
          </div>
          <div class="stat-icon si-orange"><i data-feather="list"></i></div>
        </div>
        <div class="stat-card">
          <div class="stat-text">
            <div class="stat-label">Completed This Week</div>
            <div class="stat-value" id="stat-completed">{{ $stats['completed_this_week'] ?? 0 }}</div>
          </div>
          <div class="stat-icon si-blue"><i data-feather="check-circle"></i></div>
        </div>
        <div class="stat-card">
          <div class="stat-text">
            <div class="stat-label">Avg. Goal Progress</div>
            <div class="stat-value" id="stat-progress">
              @if(($stats['avg_progress'] ?? 0) > 0)
                {{ $stats['avg_progress'] }}%
              @else
                —
              @endif
            </div>
          </div>
          <div class="stat-icon si-purple"><i data-feather="trending-up"></i></div>
        </div>
      </div>

      <div class="two-col-grid">
        <div class="card">
          <div class="card-header">
            <span class="card-title">My Patients</span>
            <a href="{{ route('lifecoach.patients') }}" class="btn-ghost">View All</a>
          </div>
          <div class="dash-patient-list" id="dash-patient-list"></div>
        </div>

        <div class="card">
          <div class="card-header">
            <span class="card-title">Pending Tasks</span>
            <a href="{{ route('lifecoach.tasks') }}" class="btn-ghost">View All</a>
          </div>
          <div class="dash-task-list" id="dash-task-list"></div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">This Week's Follow-up Schedule</span>
          <button class="btn-ghost" id="add-schedule-btn">
            <i data-feather="plus"></i> Add
          </button>
        </div>
        <div class="schedule-list" id="schedule-list"></div>
      </div>

    </div>
  </div>
</div>

<div class="modal-overlay hidden" id="schedule-modal">
  <div class="modal-box">
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:10px;">
        <div class="modal-icon green-modal-icon"><i data-feather="calendar"></i></div>
        <h3>Schedule Follow-up</h3>
      </div>
      <button class="modal-close" data-close="schedule-modal"><i data-feather="x"></i></button>
    </div>
    <div class="modal-body">
      <div class="field-group">
        <label class="field-label">Patient</label>
        <select class="field-input" id="sched-patient"></select>
      </div>
      <div class="field-group">
        <label class="field-label">Focus / Topic</label>
        <input type="text" class="field-input" id="sched-topic" placeholder="e.g. Sleep hygiene review"/>
      </div>
      <div class="modal-grid-2">
        <div class="field-group">
          <label class="field-label">Date</label>
          <input type="date" class="field-input" id="sched-date"/>
        </div>
        <div class="field-group">
          <label class="field-label">Time</label>
          <input type="time" class="field-input" id="sched-time"/>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" data-close="schedule-modal">Cancel</button>
      <button class="btn-green" id="save-sched-btn">Schedule</button>
    </div>
  </div>
</div>

<div class="toast hidden" id="toast"></div>

<script src="{{ asset('js/lifecoach_data.js?v=2.0') }}"></script>
<script src="{{ asset('js/lifecoach_dashboard.js?v=2.0') }}"></script>
<script>feather.replace();</script>
</body>
</html>

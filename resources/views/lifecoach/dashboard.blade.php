<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MB.EA — Life Coach Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/lifecoach.css') }}" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
  @include('lifecoach.partials.boot')
  <link rel="icon" type="image/png" href="{{ asset('assets/mbea_logo.png') }}" />

</head>

<body>
  <div class="app-shell">

    @include('lifecoach.partials.lifecoach_sidebar', ['activePage' => 'dashboard'])

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
              <div class="stat-label">Updates and pending</div>
              <div class="stat-value" id="stat-tasks">{{ ($updates['unread_count'] ?? 0) + ($updates['pending_tasks'] ?? 0) }}</div>
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
              <span class="card-title">Updates and pending <span class="lc-update-count" id="update-count" aria-live="polite">{{ ($updates['unread_count'] ?? 0) + ($updates['pending_tasks'] ?? 0) }}</span></span>
              <button type="button" class="btn-ghost" id="open-updates-btn">View All</button>
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

  <aside class="lc-updates-panel" id="updates-panel" aria-hidden="true" aria-labelledby="updates-title">
    <div class="lc-updates-panel-head"><h2 id="updates-title">Updates and pending</h2><button type="button" class="modal-close" id="close-updates-btn" aria-label="Close updates"><i data-feather="x"></i></button></div>
    <div class="lc-updates-tabs" role="tablist"><button class="active" data-update-filter="all" role="tab">All</button><button data-update-filter="tasks" role="tab">Tasks</button><button data-update-filter="updates" role="tab">Updates</button></div>
    <div class="lc-updates-actions"><button type="button" class="btn-ghost" id="mark-all-updates-btn">Mark all as read</button></div>
    <div class="lc-updates-list" id="updates-list" aria-live="polite"></div>
    <button type="button" class="btn-outline-sm lc-load-more" id="load-more-updates">Load more</button>
  </aside>
  <div class="lc-updates-backdrop" id="updates-backdrop"></div>

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
          <input type="text" class="field-input" id="sched-topic" placeholder="e.g. Sleep hygiene review" />
        </div>
        <div class="modal-grid-2">
          <div class="field-group">
            <label class="field-label">Date</label>
            <input type="date" class="field-input" id="sched-date" />
          </div>
          <div class="field-group">
            <label class="field-label">Time</label>
            <input type="time" class="field-input" id="sched-time" />
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

  <script src="{{ asset('js/lifecoach/lifecoach_data.js?v=2.0') }}"></script>
  <script src="{{ asset('js/lifecoach/lifecoach_dashboard.js?v=2.0') }}"></script>
  <script>feather.replace();</script>
</body>

</html>
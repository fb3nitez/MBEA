<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MedCare — Patients</title>
  <link rel="stylesheet" href="{{ asset('css/lifecoach.css') }}"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  @include('lifecoach.partials.boot')
</head>
<body>
<div class="app-shell">

  @include('partials.lifecoach_sidebar', ['activePage' => 'patients'])

  <div class="main-area">
    <header class="main-topbar">
      <div class="topbar-left">
        <button class="hamburger-btn" id="hamburger-btn"><i data-feather="menu"></i></button>
        <h1 class="page-title" id="page-title">Assigned Patients</h1>
      </div>
      <div class="topbar-right">
        <span class="topbar-date" id="topbar-date"></span>
      </div>
    </header>

    <div class="content-wrap">

      <div id="patient-list-view">
        <div class="card">
          <div class="card-header">
            <span class="card-title">My Patients</span>
            <span class="patient-count-badge" id="patient-count-badge">0 patients</span>
          </div>
          <div class="lc-patients-grid" id="lc-patients-grid"></div>
        </div>
      </div>

      <div id="patient-detail-view" class="hidden">
        <div class="patient-detail-header">
          <button class="btn-ghost-back" id="patient-back-btn">
            <i data-feather="arrow-left"></i> Back to Patients
          </button>
          <div class="patient-detail-title-block">
            <div class="patient-detail-avatar" id="pd-avatar">—</div>
            <div>
              <div class="patient-detail-name" id="pd-name"></div>
              <div class="patient-detail-meta" id="pd-meta"></div>
            </div>
          </div>
        </div>

        <div class="tab-bar">
          <button class="tab-btn active" data-ptab="overview">Overview</button>
          <button class="tab-btn" data-ptab="metrics">Lifestyle Metrics</button>
          <button class="tab-btn" data-ptab="goals">Goals</button>
          <button class="tab-btn" data-ptab="habits">Habit Tracking</button>
          <button class="tab-btn" data-ptab="coachNotes">Coaching Notes</button>
        </div>

        <div class="ptab-panel active" id="ptab-overview">
          <div class="two-col-grid">
            <div class="card">
              <div class="card-header"><span class="card-title">Patient Information</span></div>
              <div class="patient-info-list" id="pd-info-list"></div>
            </div>
            <div class="card">
              <div class="card-header">
                <span class="card-title">Active Prescriptions</span>
              </div>
              <div class="rx-list" id="pd-rx-list"></div>
            </div>
          </div>
          <div class="card" style="margin-top:16px;">
            <div class="card-header">
              <span class="card-title">Recent Coaching Notes</span>
              <button class="btn-green-sm" onclick="switchPatientTab('coachNotes')">View All</button>
            </div>
            <div class="pd-recent-notes" id="pd-recent-notes"></div>
          </div>
        </div>

        <div class="ptab-panel" id="ptab-metrics">
          <div class="two-col-grid">
            <div class="card chart-card">
              <div class="card-header"><span class="card-title">Weekly Compliance</span></div>
              <div class="chart-container"><canvas id="compliance-chart"></canvas></div>
            </div>
            <div class="card">
              <div class="card-header"><span class="card-title">Current Metrics</span></div>
              <div class="metrics-list" id="pd-metrics-list"></div>
            </div>
          </div>
        </div>

        <div class="ptab-panel" id="ptab-goals">
          <div class="card">
            <div class="card-header">
              <span class="card-title">Coaching Goals</span>
              <button class="btn-green-sm" id="add-goal-btn"><i data-feather="plus"></i> Add Goal</button>
            </div>
            <div class="goals-list" id="pd-goals-list"></div>
          </div>
        </div>

        <div class="ptab-panel" id="ptab-habits">
          <div class="card">
            <div class="card-header">
              <span class="card-title">Weekly Habit Tracker</span>
            </div>
            <div class="habits-table-wrap" id="pd-habits-wrap"></div>
          </div>
        </div>

        <div class="ptab-panel" id="ptab-coachNotes">
          <div class="card">
            <div class="card-header">
              <span class="card-title">Coaching Notes</span>
              <button class="btn-green-sm" id="pd-add-note-btn"><i data-feather="plus"></i> Add Note</button>
            </div>
            <div class="pd-notes-list" id="pd-notes-list"></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="modal-overlay hidden" id="note-modal">
  <div class="modal-box">
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:10px;">
        <div class="modal-icon green-modal-icon"><i data-feather="clipboard"></i></div>
        <h3>Add Coaching Note</h3>
      </div>
      <button class="modal-close" data-close="note-modal"><i data-feather="x"></i></button>
    </div>
    <div class="modal-body">
      <div class="field-group">
        <label class="field-label">Patient</label>
        <select class="field-input" id="note-patient"></select>
      </div>
      <div class="field-group">
        <label class="field-label">Session Type</label>
        <select class="field-input" id="note-type">
          <option>Follow-up</option>
          <option>Initial Assessment</option>
          <option>Goal Review</option>
          <option>Crisis Support</option>
          <option>Check-in</option>
        </select>
      </div>
      <div class="field-group">
        <label class="field-label">Note</label>
        <textarea class="field-textarea" id="note-text" rows="5" placeholder="Describe session observations, progress, and next steps..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" data-close="note-modal">Cancel</button>
      <button class="btn-green" id="save-note-btn">Save Note</button>
    </div>
  </div>
</div>

<div class="modal-overlay hidden" id="goal-modal">
  <div class="modal-box">
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:10px;">
        <div class="modal-icon green-modal-icon"><i data-feather="target"></i></div>
        <h3>Add Coaching Goal</h3>
      </div>
      <button class="modal-close" data-close="goal-modal"><i data-feather="x"></i></button>
    </div>
    <div class="modal-body">
      <div class="field-group">
        <label class="field-label">Goal Title</label>
        <input type="text" class="field-input" id="goal-title" placeholder="e.g. Improve sleep consistency"/>
      </div>
      <div class="field-group">
        <label class="field-label">Category</label>
        <select class="field-input" id="goal-category">
          <option>Sleep</option><option>Exercise</option><option>Nutrition</option>
          <option>Stress Management</option><option>Mental Wellness</option><option>Social Connection</option>
        </select>
      </div>
      <div class="field-group">
        <label class="field-label">Target Date</label>
        <input type="date" class="field-input" id="goal-date"/>
      </div>
      <div class="field-group">
        <label class="field-label">Description</label>
        <textarea class="field-textarea" id="goal-desc" rows="3" placeholder="Describe the goal and success criteria..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" data-close="goal-modal">Cancel</button>
      <button class="btn-green" id="save-goal-btn">Add Goal</button>
    </div>
  </div>
</div>

<div class="toast hidden" id="toast"></div>

<script src="{{ asset('js/lifecoach_data.js?v=2.0') }}"></script>
<script src="{{ asset('js/lifecoach_patients.js?v=2.0') }}"></script>
<script>feather.replace();</script>
</body>
</html>

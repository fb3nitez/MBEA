<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MB.EA — Patients</title>
  <link rel="stylesheet" href="{{ asset('css/lifecoach.css') }}" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  @include('lifecoach.partials.boot')
  <link rel="icon" type="image/png" href="{{ asset('assets/mbea_logo.png') }}" />

  {{-- <edit-marker SimpforLyla> intake modal styles --}}
  <style>
    .intake-modal-box {
      width: 760px;
      max-width: 96vw;
      max-height: 88vh;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      animation: intakeModalIn .22s ease;
    }

    @keyframes intakeModalIn {
      from {
        opacity: 0;
        transform: translateY(10px) scale(.985);
      }

      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    /* keep the header/footer visible while the body scrolls */
    .intake-modal-box .modal-header {
      position: sticky;
      top: 0;
      z-index: 2;
      background: var(--white, #fff);
    }

    .intake-modal-box .modal-footer {
      position: sticky;
      bottom: 0;
      z-index: 2;
      background: var(--white, #fff);
    }

    .intake-tabs {
      display: flex;
      gap: 4px;
      flex-wrap: wrap;
      flex: 0 0 auto;
      padding: 0 18px;
      border-bottom: 1px solid var(--border, #e2e8f0);
      background: var(--white, #fff);
      z-index: 2;
    }

    .intake-modal-box>#intake-modal-body {
      flex: 1 1 auto;
      min-height: 0;
      overflow-y: auto;
    }

    .intake-tab {
      flex: 0 0 auto;
      border: 0;
      border-bottom: 2px solid transparent;
      background: transparent;
      color: var(--text-500, #64748b);
      cursor: pointer;
      font-size: 12px;
      font-weight: 700;
      padding: 12px 8px 10px;
    }

    .intake-tab.active {
      border-bottom-color: var(--green-600, #16a34a);
      color: var(--green-700, #15803d);
    }

    .intake-tab-panel {
      display: none;
    }

    .intake-tab-panel.active {
      display: block;
    }

    .intake-tiptap-viewer {
      min-height: 12rem;
      padding: 1rem 1.25rem;
      line-height: 1.6;
      background: #fff;
      border: 1px solid var(--border, #e2e8f0);
      border-radius: var(--radius-sm, 6px);
    }

    .intake-tiptap-viewer p {
      margin: .5rem 0;
    }

    .intake-tiptap-viewer h1,
    .intake-tiptap-viewer h2,
    .intake-tiptap-viewer h3 {
      margin: 1rem 0 .5rem;
      line-height: 1.25;
    }

    .intake-tiptap-viewer ul,
    .intake-tiptap-viewer ol {
      padding-left: 1.5rem;
    }

    .intake-tiptap-viewer blockquote {
      border-left: 3px solid var(--green-500, #22c55e);
      margin: .75rem 0;
      padding-left: 1rem;
      color: var(--text-500, #64748b);
    }

    .intake-tiptap-viewer pre {
      background: #0f172a;
      color: #f1f5f9;
      padding: .75rem 1rem;
      border-radius: .5rem;
      overflow-x: auto;
    }

    .intake-tiptap-viewer code {
      background: #f1f5f9;
      padding: .125rem .35rem;
      border-radius: .3rem;
      font-size: .875rem;
    }

    .intake-tiptap-viewer pre code {
      background: transparent;
      padding: 0;
    }

    .intake-tiptap-viewer img {
      display: block;
      max-width: 100%;
      height: auto;
      margin: .75rem 0;
    }

    .intake-table-wrap {
      overflow-x: auto;
      border: 1px solid var(--border, #e2e8f0);
      border-radius: 6px;
      background: #fff;
    }

    .intake-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
    }

    .intake-table th,
    .intake-table td {
      padding: 9px 11px;
      border-bottom: 1px solid var(--border, #e2e8f0);
      text-align: left;
    }

    .intake-table th {
      color: var(--text-600, #475569);
      background: var(--green-50, #f0fdf4);
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: .04em;
    }

    .intake-table tr:last-child td {
      border-bottom: 0;
    }

    .intake-form-hint {
      margin-bottom: 10px;
      color: var(--text-500, #64748b);
      font-size: 13px;
    }

    /* slim green scrollbar for the modal */
    .intake-modal-box>#intake-modal-body::-webkit-scrollbar {
      width: 8px;
    }

    .intake-modal-box>#intake-modal-body::-webkit-scrollbar-track {
      background: transparent;
    }

    .intake-modal-box>#intake-modal-body::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 999px;
    }

    .intake-modal-box>#intake-modal-body::-webkit-scrollbar-thumb:hover {
      background: var(--green-500, #22c55e);
    }

    .intake-section {
      margin-bottom: 14px;
      background: #f8fafc;
      border: 1px solid var(--border, #e2e8f0);
      border-radius: var(--radius-sm, 6px);
      padding: 14px 16px 10px;
    }

    .intake-form-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
    }

    .intake-field {
      min-width: 0;
    }

    .intake-field--full {
      grid-column: 1 / -1;
    }

    .intake-field-label {
      display: block;
      margin-bottom: 5px;
      color: var(--text-600, #475569);
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .04em;
      text-transform: uppercase;
    }

    .intake-readonly {
      min-height: 38px;
      padding: 9px 11px;
      border: 1px solid var(--border, #e2e8f0);
      border-radius: 6px;
      background: #fff;
      color: var(--text-800, #1e293b);
      line-height: 1.45;
      white-space: pre-wrap;
      overflow-wrap: anywhere;
    }

    .intake-check-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 7px;
    }

    .intake-section:last-child {
      margin-bottom: 0;
    }

    .intake-section-title {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: var(--green-600, #16a34a);
      padding-bottom: 8px;
      margin-bottom: 8px;
      border-bottom: 1px solid var(--border, #e2e8f0);
    }

    .intake-section-title::before {
      content: "";
      width: 4px;
      height: 14px;
      border-radius: 999px;
      background: linear-gradient(180deg, #16a34a, #4ade80);
      flex-shrink: 0;
    }

    .intake-row {
      display: flex;
      gap: 12px;
      padding: 7px 8px;
      border-radius: var(--radius-sm, 6px);
      font-size: 13px;
      transition: background .12s;
    }

    @media (max-width: 640px) {
      .intake-form-grid {
        grid-template-columns: 1fr;
      }

      .intake-field--full {
        grid-column: auto;
      }
    }

    .intake-row:hover {
      background: var(--green-50, #f0fdf4);
    }

    .intake-label {
      width: 180px;
      flex-shrink: 0;
      color: var(--text-500, #64748b);
      font-weight: 600;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: .04em;
      padding-top: 1px;
    }

    .intake-value {
      color: var(--text-800, #1e293b);
      flex: 1;
      line-height: 1.5;
      white-space: pre-wrap;
    }

    .intake-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }

    .intake-tag {
      background: var(--green-50, #f0fdf4);
      color: var(--green-700, #15803d);
      border: 1px solid var(--green-200, #bbf7d0);
      border-radius: 999px;
      padding: 2px 10px;
      font-size: 12px;
      font-weight: 500;
      box-shadow: 0 1px 2px rgba(22, 163, 74, .08);
    }

    .intake-empty {
      color: var(--text-400, #94a3b8);
      font-size: 13px;
      font-style: italic;
    }

    .btn-intake {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      border: 1.5px solid var(--green-600, #16a34a);
      border-radius: 8px;
      background: #fff;
      color: var(--green-600, #16a34a);
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: background .15s, color .15s;
    }

    .btn-intake:hover {
      background: var(--green-600, #16a34a);
      color: #fff;
    }
  </style>
  {{--
  </edit-marker> --}}
</head>

<body>
  <div class="app-shell">

    @include('lifecoach.partials.lifecoach_sidebar', ['activePage' => 'patients'])

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
            {{-- <edit-marker SimpforLyla> added View Intake Form button to Patient Information card header --}}
            <div class="two-col-grid">
              <div class="card">
                <div class="card-header">
                  <span class="card-title">Patient Information</span>
                  <button type="button" class="btn-intake" id="view-intake-btn">
                    <i data-feather="file-text"></i> View Intake Form
                  </button>
                </div>
                <div class="patient-info-list" id="pd-info-list"></div>
              </div>
              {{--
            </edit-marker> --}}
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

  {{-- existing modals unchanged --}}
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
          <textarea class="field-textarea" id="note-text" rows="5"
            placeholder="Describe session observations, progress, and next steps..."></textarea>
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
          <input type="text" class="field-input" id="goal-title" placeholder="e.g. Improve sleep consistency" />
        </div>
        <div class="field-group">
          <label class="field-label">Category</label>
          <select class="field-input" id="goal-category">
            <option>Sleep</option>
            <option>Exercise</option>
            <option>Nutrition</option>
            <option>Stress Management</option>
            <option>Mental Wellness</option>
            <option>Social Connection</option>
          </select>
        </div>
        <div class="field-group">
          <label class="field-label">Target Date</label>
          <input type="date" class="field-input" id="goal-date" />
        </div>
        <div class="field-group">
          <label class="field-label">Progress (%)</label>
          <input type="number" class="field-input" id="goal-progress" min="0" max="100" value="0" />
        </div>
        <div class="field-group">
          <label class="field-label">Description</label>
          <textarea class="field-textarea" id="goal-desc" rows="3"
            placeholder="Describe the goal and success criteria..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-outline" data-close="goal-modal">Cancel</button>
        <button class="btn-green" id="save-goal-btn">Add Goal</button>
      </div>
    </div>
  </div>

  {{-- <edit-marker SimpforLyla> intake form read-only modal --}}
  <div class="modal-overlay hidden" id="intake-modal">
    <div class="modal-box intake-modal-box">
      <div class="modal-header">
        <div style="display:flex;align-items:center;gap:10px;">
          <div class="modal-icon green-modal-icon"><i data-feather="file-text"></i></div>
          <h3>Patient Intake Form</h3>
        </div>
        <button class="modal-close" data-close="intake-modal"><i data-feather="x"></i></button>
      </div>
      <div class="intake-tabs" role="tablist" aria-label="Patient intake sections">
        <button type="button" class="intake-tab active" data-intake-tab="record">Patient Record</button>
        <button type="button" class="intake-tab" data-intake-tab="clinical">Clinical Notes</button>
        <button type="button" class="intake-tab" data-intake-tab="medical">Medical History</button>
        <button type="button" class="intake-tab" data-intake-tab="psychiatric">Personal History</button>
        <button type="button" class="intake-tab" data-intake-tab="lifestyle">Lifestyle</button>
        <button type="button" class="intake-tab" data-intake-tab="spiritual">Spiritual</button>
        <button type="button" class="intake-tab" data-intake-tab="interventions">Interventions</button>
      </div>
      <div class="modal-body" id="intake-modal-body">
        {{-- filled by JS --}}
      </div>
      <div class="modal-footer">
        <button class="btn-outline" data-close="intake-modal">Close</button>
      </div>
    </div>
  </div>
  {{--
  </edit-marker> --}}

  <div class="toast hidden" id="toast"></div>

  <script src="{{ asset('js/lifecoach/lifecoach_data.js?v=3.0') }}"></script>
  <script
    src="{{ asset('js/lifecoach/lifecoach_patients_list.js') }}?v={{ filemtime(public_path('js/lifecoach/lifecoach_patients_list.js')) }}-tabs"></script>
  <script>
    feather.replace();
  </script>
</body>

</html>
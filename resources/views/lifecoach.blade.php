<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MedCare — Life Coach Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/lifecoach.css') }}"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>

<div class="app-shell">

  <!-- ===================== SIDEBAR ===================== -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand-block">
      <div class="sidebar-brand">MedCare System</div>
      <div class="sidebar-role">Life Coach</div>
    </div>

    <nav class="sidebar-nav">
      <button class="nav-item active" data-section="dashboard">
        <i data-feather="grid"></i><span>Dashboard</span>
      </button>
      <button class="nav-item" data-section="patients">
        <i data-feather="users"></i><span>Assigned Patients</span>
      </button>
      <button class="nav-item" data-section="notes">
        <i data-feather="clipboard"></i><span>Coaching Notes</span>
      </button>
      <button class="nav-item" data-section="tasks">
        <i data-feather="check-square"></i><span>Tasks</span>
      </button>
    </nav>

    <div class="sidebar-footer">
      <button class="nav-item" id="profile-btn">
        <i data-feather="user"></i><span>Profile</span>
      </button>
      <button class="nav-item logout-item" id="logout-btn">
        <i data-feather="log-out"></i><span>Logout</span>
      </button>
    </div>
  </aside>

  <!-- ===================== MAIN ===================== -->
  <div class="main-area">

    <header class="main-topbar">
      <div class="topbar-left">
        <button class="hamburger-btn" id="hamburger-btn"><i data-feather="menu"></i></button>
        <h1 class="page-title" id="page-title">Dashboard</h1>
      </div>
      <div class="topbar-right">
        <span class="topbar-date">Sunday, June 7, 2026</span>
      </div>
    </header>

    <div class="content-wrap">

      <!-- ================================================
           SECTION 1: DASHBOARD
           ================================================ -->
      <section class="lc-section active" id="section-dashboard">

        <!-- Stats -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-text">
              <div class="stat-label">Assigned Patients</div>
              <div class="stat-value">3</div>
            </div>
            <div class="stat-icon si-green"><i data-feather="users"></i></div>
          </div>
          <div class="stat-card">
            <div class="stat-text">
              <div class="stat-label">Pending Tasks</div>
              <div class="stat-value">3</div>
            </div>
            <div class="stat-icon si-orange"><i data-feather="list"></i></div>
          </div>
          <div class="stat-card">
            <div class="stat-text">
              <div class="stat-label">Completed This Week</div>
              <div class="stat-value">8</div>
              <div class="stat-trend trend-up">+25% from last week</div>
            </div>
            <div class="stat-icon si-blue"><i data-feather="check-circle"></i></div>
          </div>
          <div class="stat-card">
            <div class="stat-text">
              <div class="stat-label">Avg. Progress Score</div>
              <div class="stat-value">7.8/10</div>
              <div class="stat-trend trend-up">+0.5 improvement</div>
            </div>
            <div class="stat-icon si-purple"><i data-feather="trending-up"></i></div>
          </div>
        </div>

        <!-- Two-col -->
        <div class="two-col-grid">

          <!-- My Patients -->
          <div class="card">
            <div class="card-header">
              <span class="card-title">My Patients</span>
              <button class="btn-ghost" data-goto="patients">View All</button>
            </div>
            <div class="dash-patient-list" id="dash-patient-list">
              <!-- filled by JS -->
            </div>
          </div>

          <!-- Pending Tasks -->
          <div class="card">
            <div class="card-header">
              <span class="card-title">Pending Tasks</span>
              <button class="btn-ghost" data-goto="tasks">View All</button>
            </div>
            <div class="dash-task-list" id="dash-task-list">
              <!-- filled by JS -->
            </div>
          </div>
        </div>

        <!-- Follow-up Schedule -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">This Week's Follow-up Schedule</span>
            <button class="btn-ghost" id="add-schedule-btn"><i data-feather="plus"></i> Add</button>
          </div>
          <div class="schedule-list" id="schedule-list">
            <!-- filled by JS -->
          </div>
        </div>

      </section>


      <!-- ================================================
           SECTION 2: ASSIGNED PATIENTS (List + Detail)
           ================================================ -->
      <section class="lc-section" id="section-patients">

        <!-- Patient List View -->
        <div id="patient-list-view">
          <div class="section-heading">Assigned Patients</div>
          <div class="card">
            <div class="card-header">
              <span class="card-title">My Patients</span>
              <span class="patient-count-badge" id="patient-count-badge">3 patients</span>
            </div>
            <div class="lc-patients-grid" id="lc-patients-grid">
              <!-- filled by JS -->
            </div>
          </div>
        </div>

        <!-- Patient Detail View -->
        <div id="patient-detail-view" class="hidden">
          <div class="patient-detail-header">
            <button class="btn-ghost-back" id="patient-back-btn">
              <i data-feather="arrow-left"></i> Back to Patients
            </button>
            <div class="patient-detail-title-block">
              <div class="patient-detail-avatar" id="pd-avatar">SJ</div>
              <div>
                <div class="patient-detail-name" id="pd-name">Sarah Johnson</div>
                <div class="patient-detail-meta" id="pd-meta">P001 · Active · Age 34</div>
              </div>
            </div>
          </div>

          <!-- Detail sub-tabs -->
          <div class="tab-bar" id="patient-tab-bar">
            <button class="tab-btn active" data-ptab="overview">Overview</button>
            <button class="tab-btn" data-ptab="metrics">Lifestyle Metrics</button>
            <button class="tab-btn" data-ptab="goals">Goals</button>
            <button class="tab-btn" data-ptab="habits">Habit Tracking</button>
            <button class="tab-btn" data-ptab="coachNotes">Coaching Notes</button>
          </div>

          <!-- OVERVIEW TAB -->
          <div class="ptab-panel active" id="ptab-overview">
            <div class="two-col-grid">
              <div class="card">
                <div class="card-header"><span class="card-title">Patient Information</span></div>
                <div class="patient-info-list" id="pd-info-list"></div>
              </div>
              <div class="card">
                <div class="card-header">
                  <span class="card-title">Active Prescriptions</span>
                  <button class="btn-green-sm" id="add-rx-btn"><i data-feather="plus"></i> Add</button>
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

          <!-- LIFESTYLE METRICS TAB -->
          <div class="ptab-panel" id="ptab-metrics">
            <div class="two-col-grid">
              <div class="card chart-card">
                <div class="card-header"><span class="card-title">Weekly Compliance</span></div>
                <div class="chart-container">
                  <canvas id="compliance-chart"></canvas>
                </div>
              </div>
              <div class="card">
                <div class="card-header"><span class="card-title">Current Metrics</span></div>
                <div class="metrics-list" id="pd-metrics-list"></div>
              </div>
            </div>
          </div>

          <!-- GOALS TAB -->
          <div class="ptab-panel" id="ptab-goals">
            <div class="card">
              <div class="card-header">
                <span class="card-title">Coaching Goals</span>
                <button class="btn-green-sm" id="add-goal-btn"><i data-feather="plus"></i> Add Goal</button>
              </div>
              <div class="goals-list" id="pd-goals-list"></div>
            </div>
          </div>

          <!-- HABIT TRACKING TAB -->
          <div class="ptab-panel" id="ptab-habits">
            <div class="card">
              <div class="card-header">
                <span class="card-title">Weekly Habit Tracker</span>
                <span class="habit-week-label">Week of Jun 2–8, 2026</span>
              </div>
              <div class="habits-table-wrap" id="pd-habits-wrap"></div>
            </div>
          </div>

          <!-- COACHING NOTES TAB -->
          <div class="ptab-panel" id="ptab-coachNotes">
            <div class="card">
              <div class="card-header">
                <span class="card-title">Coaching Notes</span>
                <button class="btn-green-sm" id="pd-add-note-btn"><i data-feather="plus"></i> Add Note</button>
              </div>
              <div class="pd-notes-list" id="pd-notes-list"></div>
            </div>
          </div>

        </div><!-- /patient-detail-view -->
      </section>


      <!-- ================================================
           SECTION 3: COACHING NOTES (global)
           ================================================ -->
      <section class="lc-section" id="section-notes">
        <div class="section-heading">Coaching Notes</div>
        <div class="card">
          <div class="card-header">
            <span class="card-title">All Coaching Notes</span>
            <button class="btn-green" id="add-note-global-btn">
              <i data-feather="plus"></i> Add Note
            </button>
          </div>
          <div class="notes-filter-row">
            <div class="search-wrap">
              <i data-feather="search" class="search-icon"></i>
              <input type="text" id="notes-search" class="search-input" placeholder="Search notes..."/>
            </div>
            <select id="notes-patient-filter" class="filter-select">
              <option value="">All Patients</option>
              <option value="Sarah Johnson">Sarah Johnson</option>
              <option value="Emily Thompson">Emily Thompson</option>
              <option value="David Martinez">David Martinez</option>
            </select>
          </div>
          <div class="global-notes-list" id="global-notes-list">
            <!-- filled by JS -->
          </div>
        </div>
      </section>


      <!-- ================================================
           SECTION 4: TASKS
           ================================================ -->
      <section class="lc-section" id="section-tasks">
        <div class="section-heading">Tasks</div>
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
          <div class="tasks-full-list" id="tasks-full-list">
            <!-- filled by JS -->
          </div>
        </div>
      </section>

    </div><!-- /content-wrap -->
  </div><!-- /main-area -->
</div><!-- /app-shell -->


<!-- ================================================
     MODALS
     ================================================ -->

<!-- Add Note Modal -->
<div class="modal-overlay hidden" id="note-modal">
  <div class="modal-box">
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:10px;">
        <div class="modal-icon green-modal-icon"><i data-feather="clipboard"></i></div>
        <h3 id="note-modal-title">Add Coaching Note</h3>
      </div>
      <button class="modal-close" data-close="note-modal"><i data-feather="x"></i></button>
    </div>
    <div class="modal-body">
      <div class="field-group">
        <label class="field-label">Patient</label>
        <select class="field-input" id="note-patient">
          <option value="Sarah Johnson">Sarah Johnson</option>
          <option value="Emily Thompson">Emily Thompson</option>
          <option value="David Martinez">David Martinez</option>
        </select>
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

<!-- Add Task Modal -->
<div class="modal-overlay hidden" id="task-modal">
  <div class="modal-box">
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:10px;">
        <div class="modal-icon green-modal-icon"><i data-feather="check-square"></i></div>
        <h3 id="task-modal-title">Add Task</h3>
      </div>
      <button class="modal-close" data-close="task-modal"><i data-feather="x"></i></button>
    </div>
    <div class="modal-body">
      <div class="field-group">
        <label class="field-label">Patient</label>
        <select class="field-input" id="task-patient">
          <option value="Sarah Johnson">Sarah Johnson</option>
          <option value="Emily Thompson">Emily Thompson</option>
          <option value="David Martinez">David Martinez</option>
        </select>
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

<!-- Add Goal Modal -->
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

<!-- Add Schedule Modal -->
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
        <select class="field-input" id="sched-patient">
          <option>Sarah Johnson</option>
          <option>Emily Thompson</option>
          <option>David Martinez</option>
        </select>
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

<!-- Profile Modal -->
<div class="modal-overlay hidden" id="profile-modal">
  <div class="modal-box">
    <div class="modal-header">
      <h3>My Profile</h3>
      <button class="modal-close" data-close="profile-modal"><i data-feather="x"></i></button>
    </div>
    <div class="modal-body" style="text-align:center;">
      <div class="profile-avatar">MC</div>
      <div style="font-size:18px;font-weight:700;margin-top:12px;">Michael Chen</div>
      <div style="color:#64748b;font-size:14px;">Life Coach · MedCare Clinic</div>
      <div style="color:#64748b;font-size:13px;margin-top:4px;">coach@medcare.ph</div>
      <div style="margin-top:16px;" class="profile-stat-row">
        <div class="profile-stat"><div class="profile-stat-val">3</div><div class="profile-stat-lbl">Patients</div></div>
        <div class="profile-stat"><div class="profile-stat-val">8</div><div class="profile-stat-lbl">Sessions/wk</div></div>
        <div class="profile-stat"><div class="profile-stat-val">7.8</div><div class="profile-stat-lbl">Avg Score</div></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" data-close="profile-modal">Close</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast hidden" id="toast"></div>

<script src="{{ asset('js/lifecoach.js') }}"></script>
<script>feather.replace();</script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MedCare — Coaching Notes</title>
  <link rel="stylesheet" href="{{ asset('css/lifecoach.css') }}"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
  @include('lifecoach.partials.boot')
</head>
<body>
<div class="app-shell">

  @include('partials.lifecoach_sidebar', ['activePage' => 'notes'])

  <div class="main-area">
    <header class="main-topbar">
      <div class="topbar-left">
        <button class="hamburger-btn" id="hamburger-btn"><i data-feather="menu"></i></button>
        <h1 class="page-title">Coaching Notes</h1>
      </div>
      <div class="topbar-right">
        <span class="topbar-date" id="topbar-date"></span>
      </div>
    </header>

    <div class="content-wrap">
      <div class="card">
        <div class="card-header">
          <span class="card-title">All Coaching Notes</span>
          <button class="btn-green" id="add-note-btn">
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
          </select>
        </div>
        <div class="global-notes-list" id="global-notes-list"></div>
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

<div class="toast hidden" id="toast"></div>

<script src="{{ asset('js/lifecoach_data.js') }}"></script>
<script src="{{ asset('js/lifecoach_notes.js') }}"></script>
<script>feather.replace();</script>
</body>
</html>

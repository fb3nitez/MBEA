<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MedCare — My Profile</title>
  <link rel="stylesheet" href="{{ asset('css/lifecoach.css') }}"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
  @include('lifecoach.partials.boot')
</head>
<body>
<div class="app-shell">

  @include('partials.lifecoach_sidebar', ['activePage' => 'profile'])

  <div class="main-area">
    <header class="main-topbar">
      <div class="topbar-left">
        <button class="hamburger-btn" id="hamburger-btn"><i data-feather="menu"></i></button>
        <h1 class="page-title">My Profile</h1>
      </div>
      <div class="topbar-right">
        <span class="topbar-date" id="topbar-date"></span>
      </div>
    </header>

    <div class="content-wrap">
      <div class="two-col-grid" style="align-items:start;">

        <div style="display:flex;flex-direction:column;gap:16px;">

          <div class="card">
            <div class="card-header"><span class="card-title">Profile</span></div>
            <div style="padding:24px;display:flex;flex-direction:column;align-items:center;gap:12px;text-align:center;">
              <div class="profile-avatar-lg" id="prof-avatar">{{ $coach['initials'] ?? 'LC' }}</div>
              <div>
                <div id="prof-name" style="font-size:20px;font-weight:800;color:#0f172a;">{{ $coach['name'] ?? '' }}</div>
                <div id="prof-role" style="font-size:14px;color:#16a34a;font-weight:600;margin-top:2px;">Life Coach · {{ $coach['clinic'] ?? 'MedCare Clinic' }}</div>
              </div>
              <div id="prof-email" style="font-size:13px;color:#64748b;">{{ $coach['email'] ?? '' }}</div>

              <div class="profile-stats-row">
                <div class="profile-stat-box">
                  <div class="profile-stat-val">{{ count($patientOptions ?? []) }}</div>
                  <div class="profile-stat-lbl">Patients</div>
                </div>
                <div class="profile-stat-box">
                  <div class="profile-stat-val">{{ count($notes ?? []) }}</div>
                  <div class="profile-stat-lbl">Notes</div>
                </div>
                <div class="profile-stat-box">
                  <div class="profile-stat-val">{{ ($stats['avg_progress'] ?? 0) ?: '—' }}</div>
                  <div class="profile-stat-lbl">Avg Progress</div>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header"><span class="card-title">Specializations</span></div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:8px;" id="prof-specs"></div>
          </div>

        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">

          <div class="card">
            <div class="card-header">
              <span class="card-title">Account</span>
            </div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:14px;">
              <div class="field-group">
                <label class="field-label">Full Name</label>
                <input type="text" class="field-input" id="edit-name" value="{{ $coach['name'] ?? '' }}" readonly/>
              </div>
              <div class="field-group">
                <label class="field-label">Email</label>
                <input type="email" class="field-input" id="edit-email" value="{{ $coach['email'] ?? '' }}" readonly/>
              </div>
              <div class="field-group">
                <label class="field-label">License Number</label>
                <input type="text" class="field-input" id="edit-license" value="{{ $coach['license'] ?? '' }}" readonly/>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header"><span class="card-title">Recent Notes</span></div>
            <div id="prof-activity" style="display:flex;flex-direction:column;gap:0;"></div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<div class="toast hidden" id="toast"></div>

<script src="{{ asset('js/lifecoach_data.js') }}"></script>
<script src="{{ asset('js/lifecoach_profile.js') }}"></script>
<script>feather.replace();</script>
</body>
</html>

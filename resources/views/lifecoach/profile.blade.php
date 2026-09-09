<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MedCare — My Profile</title>
  <link rel="icon" type="image/png" href="{{ asset('assets/mbea_logo.png') }}" />
  <link rel="stylesheet" href="{{ asset('css/lifecoach.css') }}" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
  @include('lifecoach.partials.boot')
</head>

<body>
  <div class="app-shell">

    @include('lifecoach.partials.lifecoach_sidebar', ['activePage' => 'profile'])

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
              <div
                style="padding:24px;display:flex;flex-direction:column;align-items:center;gap:8px;text-align:center;">
                <div class="profile-avatar-lg" id="prof-avatar">{{ $coach['initials'] ?? 'LC' }}</div>
                <button type="button" class="btn-outline-sm" id="change-photo-btn">Change Photo</button>
                <input type="file" id="photo-input" accept="image/*" class="hidden" />
                <div style="margin-top:8px;">
                  <div id="prof-name" style="font-size:20px;font-weight:800;color:#0f172a;">{{ $coach['name'] ?? '' }}
                  </div>
                  <div id="prof-role" style="font-size:14px;color:#16a34a;font-weight:600;margin-top:2px;">Life Coach ·
                    {{ $coach['clinic'] ?? 'MedCare Clinic' }}</div>
                </div>
                <div id="prof-email" style="font-size:13px;color:#64748b;">{{ $coach['email'] ?? '' }}</div>
              </div>
            </div>

            <div class="card">
              <div class="card-header"><span class="card-title">Recent Activity</span></div>
              <div id="prof-activity" style="display:flex;flex-direction:column;gap:0;"></div>
            </div>

          </div>

          <div style="display:flex;flex-direction:column;gap:16px;">

            <div class="card">
              <div class="card-header" style="display:flex;justify-content:space-between;align-items:baseline;">
                <span class="card-title">Account</span>
                <span style="font-size:12px;color:#94a3b8;">Created {{ $coach['created_at'] ?? '—' }}</span>
              </div>
              <div style="padding:16px;display:flex;flex-direction:column;gap:14px;">
                <div class="field-group">
                  <label class="field-label">Full Name</label>
                  <input type="text" class="field-input" id="edit-name" value="{{ $coach['name'] ?? '' }}" />
                </div>
                <div class="modal-grid-2">
                  <div class="field-group">
                    <label class="field-label">Email</label>
                    <input type="email" class="field-input" id="edit-email" value="{{ $coach['email'] ?? '' }}" />
                  </div>
                  <div class="field-group">
                    <label class="field-label">Phone Number</label>
                    <input type="text" class="field-input" id="edit-phone" value="{{ $coach['phone'] ?? '' }}"
                      placeholder="09XX XXX XXXX" />
                  </div>
                </div>
                <button type="button" class="btn-green" id="save-account-btn" style="align-self:flex-start;">Save
                  Changes</button>
              </div>
            </div>

            <div class="card">
              <div class="card-header"><span class="card-title">Security</span></div>
              <div style="padding:16px;display:flex;flex-direction:column;gap:14px;">
                <div class="field-group">
                  <label class="field-label">Current Password</label>
                  <input type="password" class="field-input" id="current-password"
                    placeholder="Enter current password" />
                </div>
                <div class="modal-grid-2">
                  <div class="field-group">
                    <label class="field-label">New Password</label>
                    <input type="password" class="field-input" id="new-password" placeholder="New password" />
                  </div>
                  <div class="field-group">
                    <label class="field-label">Confirm New Password</label>
                    <input type="password" class="field-input" id="confirm-password" placeholder="Confirm password" />
                  </div>
                </div>
                <button type="button" class="btn-green" id="save-password-btn" style="align-self:flex-start;">Update
                  Password</button>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="toast hidden" id="toast"></div>

  <script src="{{ asset('js/lifecoach_data.js?v=2.0') }}"></script>
  <script src="{{ asset('js/lifecoach_profile.js?v=2.1') }}"></script>
  <script>feather.replace();</script>
</body>

</html>
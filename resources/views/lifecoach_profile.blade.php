<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MedCare — My Profile</title>
  <link rel="stylesheet" href="{{ asset('css/lifecoach.css') }}"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
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
        <span class="topbar-date">Sunday, June 7, 2026</span>
      </div>
    </header>

    <div class="content-wrap">
      <div class="two-col-grid" style="align-items:start;">

        <!-- Left: Profile Card -->
        <div style="display:flex;flex-direction:column;gap:16px;">

          <div class="card">
            <div class="card-header"><span class="card-title">Profile</span></div>
            <div style="padding:24px;display:flex;flex-direction:column;align-items:center;gap:12px;text-align:center;">
              <div class="profile-avatar-lg" id="prof-avatar">MC</div>
              <div>
                <div id="prof-name" style="font-size:20px;font-weight:800;color:#0f172a;">Michael Chen</div>
                <div id="prof-role" style="font-size:14px;color:#16a34a;font-weight:600;margin-top:2px;">Life Coach · MedCare Clinic</div>
              </div>
              <div id="prof-email" style="font-size:13px;color:#64748b;">coach@medcare.ph</div>

              <div class="profile-stats-row">
                <div class="profile-stat-box">
                  <div class="profile-stat-val">3</div>
                  <div class="profile-stat-lbl">Patients</div>
                </div>
                <div class="profile-stat-box">
                  <div class="profile-stat-val">8</div>
                  <div class="profile-stat-lbl">Sessions/wk</div>
                </div>
                <div class="profile-stat-box">
                  <div class="profile-stat-val">7.8</div>
                  <div class="profile-stat-lbl">Avg Score</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Specializations -->
          <div class="card">
            <div class="card-header"><span class="card-title">Specializations</span></div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:8px;" id="prof-specs"></div>
          </div>

        </div>

        <!-- Right: Edit Profile + Activity -->
        <div style="display:flex;flex-direction:column;gap:16px;">

          <div class="card">
            <div class="card-header">
              <span class="card-title">Edit Profile</span>
              <button class="btn-green" id="save-profile-btn">Save Changes</button>
            </div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:14px;">
              <div class="field-group">
                <label class="field-label">Full Name</label>
                <input type="text" class="field-input" id="edit-name" value="Michael Chen"/>
              </div>
              <div class="field-group">
                <label class="field-label">Email</label>
                <input type="email" class="field-input" id="edit-email" value="coach@medcare.ph"/>
              </div>
              <div class="field-group">
                <label class="field-label">Phone</label>
                <input type="text" class="field-input" id="edit-phone" value="+63 912 000 0000"/>
              </div>
              <div class="field-group">
                <label class="field-label">License Number</label>
                <input type="text" class="field-input" id="edit-license" value="LC-2026-001"/>
              </div>
              <div class="field-group">
                <label class="field-label">Bio / About</label>
                <textarea class="field-textarea" id="edit-bio" rows="4" placeholder="A short description of your approach...">Certified Life Coach specializing in cognitive behavioral coaching, stress management, and anxiety reduction. Dedicated to helping patients achieve lasting lifestyle changes through evidence-based methods.</textarea>
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="card">
            <div class="card-header"><span class="card-title">Recent Activity</span></div>
            <div id="prof-activity" style="display:flex;flex-direction:column;gap:0;"></div>
          </div>

          <!-- Change Password -->
          <div class="card">
            <div class="card-header"><span class="card-title">Change Password</span></div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:12px;">
              <div class="field-group">
                <label class="field-label">Current Password</label>
                <input type="password" class="field-input" id="pw-current" placeholder="Enter current password"/>
              </div>
              <div class="field-group">
                <label class="field-label">New Password</label>
                <input type="password" class="field-input" id="pw-new" placeholder="Enter new password"/>
              </div>
              <div class="field-group">
                <label class="field-label">Confirm New Password</label>
                <input type="password" class="field-input" id="pw-confirm" placeholder="Confirm new password"/>
              </div>
              <button class="btn-green" style="align-self:flex-start;" id="change-pw-btn">Update Password</button>
            </div>
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

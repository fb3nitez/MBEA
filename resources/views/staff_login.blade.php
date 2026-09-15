<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>MB.EA — Staff Portal</title>
  <link rel="stylesheet" href="{{ asset('css/staff_login.css') }}" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
  <link rel="icon" type="image/png" href="{{ asset('assets/mbea_logo.png') }}" />
</head>

<body>

  <!-- ======================================================
     SECTION: STAFF PORTAL (Step 1 — choose role)
====================================================== -->
  <section id="section-portal" class="page-section">
    <header class="topbar">
      <div class="topbar-left">
        <div class="brand-icon"><img src="{{ asset('assets/mbea_logo.png') }}" alt="MB.EA" class="brand-icon-img" />
        </div>
        <span class="brand-name">MB.EA Wellness Center</span>
        <span class="topbar-dot">·</span>
        <span class="topbar-label">STAFF PORTAL</span>
      </div>
      <div class="topbar-right">
        <a href="/" class="topbar-link">
          <i data-feather="arrow-left" class="link-icon"></i> Patient Portal
        </a>
      </div>
    </header>

    <main class="portal-main">
      <div class="portal-card">
        <div class="logo-block">
          <div class="logo-icon large"><i data-feather="shield"></i></div>
          <h1 class="portal-heading">Staff Access</h1>
          <p class="portal-sub">Select your role to continue</p>
        </div>

        <!-- Clickable role cards -->
        <div class="role-cards">
          <button class="role-card role-card-btn" id="btn-role-psychiatrist">
            <div class="role-icon blue"><i data-feather="cpu"></i></div>
            <div class="role-info">
              <div class="role-title">Psychiatrist</div>
              <div class="role-desc">Patient assessments · Prescriptions · Records</div>
            </div>
            <i data-feather="chevron-right" class="role-chevron"></i>
          </button>

          <button class="role-card role-card-btn" id="btn-role-coach">
            <div class="role-icon green"><i data-feather="heart"></i></div>
            <div class="role-info">
              <div class="role-title">Life Coach</div>
              <div class="role-desc">Coaching sessions · Progress tracking · Tasks</div>
            </div>
            <i data-feather="chevron-right" class="role-chevron"></i>
          </button>
        </div>

        <p class="security-note">Secure access · All activity is logged and monitored</p>
      </div>
    </main>

    <footer class="page-footer">
      © 2026 MB.EA Wellness Center
    </footer>
  </section>


  <!-- ======================================================
     SECTION: LOGIN FORM (Step 2 — enter credentials)
====================================================== -->
  <section id="section-login" class="page-section hidden">
    <header class="topbar">
      <div class="topbar-left">
        <div class="brand-icon"><img src="{{ asset('assets/mbea_logo.png') }}" alt="MB.EA" class="brand-icon-img" />
        </div>
        <span class="brand-name">MB.EA Wellness Center</span>
        <span class="topbar-dot">·</span>
        <span class="topbar-label">STAFF PORTAL</span>
      </div>
      <div class="topbar-right">
        <a href="#" class="topbar-link" id="btn-back-portal">
          <i data-feather="arrow-left" class="link-icon"></i> Back
        </a>
      </div>
    </header>

    <main class="portal-main">
      <div class="portal-card">
        <div class="logo-block">
          <div class="logo-icon medium" id="login-role-icon-wrap">
            <i data-feather="lock" id="login-role-icon"></i>
          </div>
          <h1 class="portal-heading">Sign In</h1>
          <p class="portal-sub" id="role-subtitle">Access your MB.EA staff dashboard</p>
          <!-- Selected role pill -->
          <div class="role-pill-wrap">
            <span class="role-pill" id="role-pill">Psychiatrist</span>
          </div>
        </div>

        <!-- Error box -->
        <div class="error-box hidden" id="error-box">
          <i data-feather="alert-circle" class="error-icon"></i>
          <span id="error-message">Invalid credentials. Please try again.</span>
        </div>

        <!-- Login Form -->
        <form action="{{ route('auth.login') }}" method="post" id="login-form" class="login-form" novalidate>
          @csrf
          <input type="hidden" id="selected-role" name="selected_role" value="Psychiatrist" />

          <!-- Email — floating label style -->
          <div class="fl-group">
            <input type="email" id="email" name="email" class="fl-input" required autocomplete="email"
              placeholder=" " />
            <label for="email" class="fl-label">Email Address</label>
            <span class="fl-bar"></span>
          </div>

          <!-- Password — floating label style -->
          <div class="fl-group" style="margin-top:28px;">
            <input type="password" id="password" name="password" class="fl-input" required
              autocomplete="current-password" placeholder=" " />
            <label for="password" class="fl-label">Password</label>
            <span class="fl-bar"></span>
            <button type="button" class="eye-toggle" id="eye-toggle"
              aria-label="Toggle password visibility">Show</button>
          </div>

          <!-- Remember + Forgot -->
          <div class="form-row-split" style="margin-top:24px;">
            <label class="remember-label">
              <input type="checkbox" id="remember-me" class="remember-checkbox" />
              <span>Remember me</span>
            </label>
            <a href="#" class="forgot-link">Forgot password?</a>
          </div>

          <!-- Submit -->
          <button type="submit" class="btn-submit" id="btn-submit">
            <span id="submit-text">Sign In</span>
          </button>
        </form>

        <p class="form-footer-note">
          Not a staff member? <a href="/" class="blue-link">Go to Patient Portal</a>
        </p>
      </div>
    </main>
  </section>


  <!-- ======================================================
     ROLE MISMATCH MODAL
====================================================== -->
  <div id="mismatch-overlay" class="mismatch-overlay hidden">
    <div class="mismatch-card">
      <!-- Icon -->
      <div class="mismatch-icon-wrap">
        <div class="mismatch-icon"><i data-feather="alert-triangle"></i></div>
      </div>

      <!-- Content -->
      <h2 class="mismatch-title">Wrong Role Selected</h2>
      <p class="mismatch-body">
        You selected <strong id="mismatch-selected">Psychiatrist</strong> but these
        credentials belong to a <strong id="mismatch-actual">Life Coach</strong> account.
      </p>
      <p class="mismatch-body" style="margin-top:6px;">
        Would you like to proceed to the
        <strong id="mismatch-correct-label">Life Coach</strong> dashboard instead?
      </p>

      <!-- Actions -->
      <div class="mismatch-actions">
        <button class="mismatch-btn-yes" id="mismatch-yes">
          <i data-feather="check-circle"></i>
          Yes, take me there
        </button>
        <button class="mismatch-btn-no" id="mismatch-no">
          <i data-feather="x-circle"></i>
          No, go back
        </button>
      </div>
    </div>
  </div>


  <!-- ======================================================
     LOADING SCREEN
====================================================== -->
  <div id="loading-screen" class="loading-screen hidden">
    <div class="loading-ring-container">
      <div class="loading-circle" id="loading-circle">
        <i data-feather="cpu" id="loading-role-icon-spinner"></i>
      </div>
      <svg class="loading-svg" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg">
        <circle class="loading-ring" id="loading-ring-svg" cx="48" cy="48" r="44" fill="none" stroke-width="4"
          stroke-linecap="round" stroke-dasharray="276" stroke-dashoffset="69" />
      </svg>
    </div>

    <div class="loading-text hidden" id="loading-text">
      <p class="loading-label">LOGGING IN AS</p>
      <p class="loading-role" id="loading-role-name">Psychiatrist</p>
    </div>

    <div class="loading-progress-wrap">
      <div class="loading-progress-track">
        <div class="loading-progress-bar" id="loading-progress-bar"></div>
      </div>
    </div>

    <p class="loading-check hidden" id="loading-check">
      <i data-feather="check-circle" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i>
      Preparing your dashboard...
    </p>
  </div>

  <script src="{{ asset('js/staff_login.js') }}"></script>
</body>

</html>
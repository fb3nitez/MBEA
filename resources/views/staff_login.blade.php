<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MedCare — Staff Portal</title>
  <link rel="stylesheet" href="{{ asset('css/staff_login.css') }}" />
  <!-- Feather Icons -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
</head>
<body>

<!-- ======================================================
     SECTION: STAFF PORTAL
====================================================== -->
<section id="section-portal" class="page-section">

  <!-- Top Bar -->
  <header class="topbar">
    <div class="topbar-left">
      <div class="brand-icon">
        <i data-feather="activity"></i>
      </div>
      <span class="brand-name">MedCare Clinic</span>
      <span class="topbar-dot">·</span>
      <span class="topbar-label">STAFF PORTAL</span>
    </div>
    <div class="topbar-right">
      <a href="/" class="topbar-link"><i data-feather="arrow-left" class="link-icon"></i> Patient Portal</a>
    </div>
  </header>

  <!-- Main Content -->
  <main class="portal-main">
    <div class="portal-card">

      <!-- Logo Block -->
      <div class="logo-block">
        <div class="logo-icon large">
          <i data-feather="shield"></i>
        </div>
        <h1 class="portal-heading">Staff Access</h1>
        <p class="portal-sub">Authorized healthcare professionals only</p>
      </div>

      <!-- Role Cards -->
      <div class="role-cards">
        <!-- Psychiatrist -->
        <div class="role-card">
          <div class="role-icon blue">
            <i data-feather="cpu"></i>
          </div>
          <div class="role-info">
            <div class="role-title">Psychiatrist</div>
            <div class="role-desc">Patient assessments · Prescriptions · Records</div>
          </div>
        </div>
        <!-- Life Coach -->
        <div class="role-card">
          <div class="role-icon green">
            <i data-feather="heart"></i>
          </div>
          <div class="role-info">
            <div class="role-title">Life Coach</div>
            <div class="role-desc">Coaching sessions · Progress tracking · Tasks</div>
          </div>
        </div>
      </div>

      <!-- Sign In Button -->
      <button class="btn-signin" id="btn-goto-login">
        🔒 Sign In to Staff Portal →
      </button>

      <!-- Security Note -->
      <p class="security-note">Secure access · All activity is logged and monitored</p>

    </div>
  </main>

  <!-- Footer -->
  <footer class="page-footer">
    © 2026 MedCare Integrated Psychiatric &amp; Lifestyle Medicine Clinic
  </footer>

</section>


<!-- ======================================================
     SECTION: LOGIN FORM
====================================================== -->
<section id="section-login" class="page-section hidden">

  <!-- Top Bar -->
  <header class="topbar">
    <div class="topbar-left">
      <div class="brand-icon">
        <i data-feather="activity"></i>
      </div>
      <span class="brand-name">MedCare Clinic</span>
      <span class="topbar-dot">·</span>
      <span class="topbar-label">STAFF PORTAL</span>
    </div>
    <div class="topbar-right">
      <a href="#" class="topbar-link" id="btn-back-portal"><i data-feather="arrow-left" class="link-icon"></i> Back</a>
    </div>
  </header>

  <!-- Main Content -->
  <main class="portal-main">
    <div class="portal-card">

      <!-- Header Block -->
      <div class="logo-block">
        <div class="logo-icon medium">
          <i data-feather="lock"></i>
        </div>
        <h1 class="portal-heading">Sign In</h1>
        <p class="portal-sub">Access your MedCare staff dashboard</p>
      </div>

      <!-- Error State -->
      <div class="error-box hidden" id="error-box">
        <i data-feather="shield" class="error-icon"></i>
        <span id="error-message">Invalid credentials. Please try again.</span>
      </div>

      <!-- Login Form -->
      <form id="login-form" class="login-form" novalidate>

        <!-- Email -->
        <div class="field-group">
          <label class="field-label" for="email">Email Address</label>
          <div class="input-wrapper">
            <i data-feather="mail" class="input-icon"></i>
            <input
              type="email"
              id="email"
              name="email"
              class="field-input"
              placeholder="youremail@gmail.com"
              autocomplete="email"
            />
          </div>
        </div>

        <!-- Password -->
        <div class="field-group">
          <label class="field-label" for="password">Password</label>
          <div class="input-wrapper">
            <i data-feather="lock" class="input-icon"></i>
            <input
              type="password"
              id="password"
              name="password"
              class="field-input input-with-toggle"
              placeholder="••••••••"
              autocomplete="current-password"
            />
            <button type="button" class="eye-toggle" id="eye-toggle" aria-label="Toggle password visibility">
              <i data-feather="eye" id="eye-icon"></i>
            </button>
          </div>
        </div>

        <!-- Remember + Forgot -->
        <div class="form-row-split">
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

      <!-- Demo Credentials -->
      <div class="demo-box">
        <div class="demo-header">
          <span class="demo-dot"></span>
          <span class="demo-label">DEMO CREDENTIALS</span>
        </div>
        <div class="demo-row">
          <div class="demo-role-icon blue-sm">
            <i data-feather="cpu"></i>
          </div>
          <div class="demo-info">
            <span class="demo-email">test1</span>
            <span class="demo-role-name">Psychiatrist Dashboard</span>
          </div>
        </div>
        <div class="demo-row">
          <div class="demo-role-icon green-sm">
            <i data-feather="heart"></i>
          </div>
          <div class="demo-info">
            <span class="demo-email">test2</span>
            <span class="demo-role-name">Life Coach Dashboard</span>
          </div>
        </div>
        <hr class="demo-divider" />
        <p class="demo-note">Password: any value · Role is detected automatically</p>
      </div>

      <!-- Footer Note -->
      <p class="form-footer-note">
        Not a staff member? <a href="/" class="blue-link">Go to Patient Portal</a>
      </p>

    </div>
  </main>

</section>


<!-- ======================================================
     LOADING SCREEN
====================================================== -->
<div id="loading-screen" class="loading-screen hidden">
  <div class="loading-ring-container">
    <div class="loading-circle" id="loading-circle">
      <i data-feather="cpu" id="loading-role-icon"></i>
    </div>
    <svg class="loading-svg" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg">
      <circle
        class="loading-ring"
        id="loading-ring-svg"
        cx="48" cy="48" r="44"
        fill="none"
        stroke-width="4"
        stroke-linecap="round"
        stroke-dasharray="276"
        stroke-dashoffset="69"
      />
    </svg>
  </div>

  <div class="loading-text hidden" id="loading-text">
    <p class="loading-label">LOGGING IN AS</p>
    <p class="loading-role" id="loading-role-name">Psychiatrist</p>
    <p class="loading-name" id="loading-staff-name">Dr. Maria Santos</p>
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
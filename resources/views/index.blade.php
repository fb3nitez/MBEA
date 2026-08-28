<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MB.EA Integrated Psychiatric &amp; Lifestyle Medicine Clinic</title>
  <link rel="stylesheet" href="{{ asset('css/index.css') }}" />
</head>
<body>

  <div class="page">

    <!-- ============================= HEADER ============================= -->
    <header class="topbar">
      <a href="/" class="brand">
        <img src="{{ asset('assets/mbea_logo.png') }}" alt="MB.EA" class="brand-logo" />
        <span class="brand-text">
          <span class="brand-word">MB.EA</span>
          <span class="brand-tagline">Integrated Psychiatric &amp; Lifestyle Medicine</span>
        </span>
      </a>
    </header>

    <!-- ============================= BENTO ============================= -->
    <main class="bento">

      <!-- Hero copy (photo now lives as this tile's background) -->
      <section class="cell cell-hero" style="--d:0">
        <p class="tag">Compassionate &middot; Holistic &middot; Evidence&#8209;Based</p>
        <h1>Healing the whole person.</h1>
        <p class="hero-copy">
          Psychiatric care and lifestyle medicine for mind, body, and spirit —
          under one roof, tailored to you.
        </p>
        <div class="hero-actions">
          <a href="/intake-form" class="btn-primary">
            Begin Intake Form
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </a>
          <span class="hero-note">Takes about 10&ndash;15 minutes &middot; free to start</span>
        </div>
      </section>

      <!-- Live clock tile -->
      <section class="cell cell-clock" style="--d:1">
        <span class="clock-label">Local Time</span>
        <span class="clock-time" id="clock-time">--:--</span>
        <span class="clock-date" id="clock-date">Loading&hellip;</span>
      </section>

      <!-- Service tiles — each opens a detail modal -->
      <button type="button" class="cell cell-svc cell-svc-a" style="--d:2" data-service="psychiatric">
        <div class="cell-icon icon-blue">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9.5 2a3.5 3.5 0 0 0-3.5 3.5V6a3 3 0 0 0-2 5.24V13a3 3 0 0 0 2 2.83V17a3.5 3.5 0 0 0 3.5 3.5"></path>
            <path d="M14.5 2A3.5 3.5 0 0 1 18 5.5V6a3 3 0 0 1 2 5.24V13a3 3 0 0 1-2 2.83V17a3.5 3.5 0 0 1-3.5 3.5"></path>
          </svg>
        </div>
        <h3>Psychiatric Care</h3>
        <p class="cell-svc-desc">Evaluation, diagnosis &amp; treatment</p>
        <span class="cell-svc-more">
          Learn more
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </span>
      </button>

      <button type="button" class="cell cell-svc cell-svc-b" style="--d:3" data-service="lifestyle">
        <div class="cell-icon icon-rose">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.6z"></path>
          </svg>
        </div>
        <h3>Lifestyle Medicine</h3>
        <p class="cell-svc-desc">Sleep, nutrition &amp; movement</p>
        <span class="cell-svc-more">
          Learn more
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </span>
      </button>

      <button type="button" class="cell cell-svc cell-svc-c" style="--d:4" data-service="coaching">
        <div class="cell-icon icon-green">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M11 20A7 7 0 0 1 4 13V9a2 2 0 0 1 2-2h1a4 4 0 0 1 4 4v9z"></path>
            <path d="M11 12.5C11 8 15 4 20 4c0 5-4 9-9 8.5z"></path>
          </svg>
        </div>
        <h3>Life Coaching</h3>
        <p class="cell-svc-desc">Habits, stress &amp; accountability</p>
        <span class="cell-svc-more">
          Learn more
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </span>
      </button>

      <button type="button" class="cell cell-svc cell-svc-d" style="--d:5" data-service="spiritual">
        <div class="cell-icon icon-violet">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2v4"></path><path d="M12 18v4"></path>
            <path d="M4.9 4.9l2.8 2.8"></path><path d="M16.3 16.3l2.8 2.8"></path>
            <path d="M2 12h4"></path><path d="M18 12h4"></path>
            <path d="M4.9 19.1l2.8-2.8"></path><path d="M16.3 7.7l2.8-2.8"></path>
          </svg>
        </div>
        <h3>Spiritual Wellness</h3>
        <p class="cell-svc-desc">Faith-integrated, pastoral care</p>
        <span class="cell-svc-more">
          Learn more
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </span>
      </button>

      <!-- Unified footer -->
      <section class="cell cell-footer" style="--d:6">
        <div class="footer-col">
          <span class="brand-word">MB.EA Clinic</span>
          <p>Tacloban City, Leyte</p>
        </div>

        <div class="footer-col">
          <p class="label">Contact</p>
          <p class="footer-value">(053) 555-0100</p>
          <p>info@mbea.clinic.example</p>
        </div>

        <div class="footer-col">
          <p class="label">Hours</p>
          <p class="footer-value">Mon&ndash;Fri 8AM&ndash;5PM</p>
          <p>Sat 8AM&ndash;12PM &middot; Sun Closed</p>
        </div>

        <div class="footer-col footer-trust">
          <span class="trust-item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="10" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            Confidential
          </span>
          <span class="trust-item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Evidence&#8209;based
          </span>
          <span class="trust-item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Licensed
          </span>
        </div>
      </section>

    </main>
  </div>

  <!-- ============================= SERVICE DETAIL MODAL ============================= -->
  <div class="modal-overlay" id="service-modal" aria-hidden="true">
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="modal-title">
      <button type="button" class="modal-close" id="modal-close" aria-label="Close">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
      <div class="modal-icon" id="modal-icon"></div>
      <h2 id="modal-title"></h2>
      <p id="modal-body"></p>
    </div>
  </div>

  <script src="{{ asset('js/index.js') }}"></script>
</body>
</html>
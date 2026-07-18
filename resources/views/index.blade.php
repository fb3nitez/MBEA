<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MedCare Integrated Psychiatric &amp; Lifestyle Medicine Clinic</title>
  <link rel="stylesheet" href="{{ asset('css/index.css') }}" />
</head>
<body>

  <!-- ============================= NAV ============================= -->
  <header class="site-nav" id="site-nav">
    <div class="nav-inner">
      <a href="/" class="nav-brand">
        <span class="nav-brand-mark">ME</span>
        <span class="nav-brand-text">MBEA</span>
      </a>

      <nav class="nav-links" id="nav-links">
        <a href="#services">Services</a>
        <a href="#how-it-works">How It Works</a>
        <a href="#contact">Contact</a>
      </nav>

      <button class="nav-toggle" id="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </header>

  <main>

    <!-- ============================= HERO ============================= -->
    <section class="hero">
      <div class="hero-inner">
        <div class="eyebrow-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.9 6.26L21 9.27l-4.5 4.39L17.8 21 12 17.77 6.2 21l1.3-7.34L3 9.27l6.1-1.01z"/></svg>
          Compassionate &middot; Holistic &middot; Evidence&#8209;Based
        </div>

        <h1>Healing the Whole Person</h1>

        <p class="hero-subhead">Mind, Body &amp; Spirit</p>

        <p class="hero-description">
          MedCare Integrated Psychiatric &amp; Lifestyle Medicine Clinic provides compassionate,
          evidence-based mental health care that addresses every dimension of your well-being —
          biological, psychological, social, and spiritual.
        </p>

        <div class="hero-cta-card">
          <div class="hero-cta-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 2h6a1 1 0 0 1 1 1v2H8V3a1 1 0 0 1 1-1z"></path>
              <rect x="5" y="4" width="14" height="18" rx="2"></rect>
              <line x1="9" y1="11" x2="15" y2="11"></line>
              <line x1="9" y1="15" x2="15" y2="15"></line>
            </svg>
          </div>
          <h2>New Patient? Start Here</h2>
          <p>Complete your intake form on this kiosk before your consultation. It only takes about 10&ndash;15 minutes.</p>
          <a href="/intake-form" class="btn-primary btn-lg hero-cta-btn">
            Begin Intake Form
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </a>
          <div class="trust-row">
            <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="10" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg> Confidential</span>
            <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"></circle><polyline points="12 7 12 12 15 15"></polyline></svg> 10&ndash;15 min</span>
            <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg> Free to start</span>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================= SERVICES ============================= -->
    <section class="services" id="services">
      <div class="section-inner reveal">
        <p class="eyebrow">What We Offer</p>
        <h2>Our Core Services</h2>
        <p class="section-subtext">A comprehensive approach to mental health and wellness, tailored to your unique needs.</p>

        <div class="services-grid">
          <div class="service-card reveal">
            <div class="service-icon icon-blue">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9.5 2a3.5 3.5 0 0 0-3.5 3.5V6a3 3 0 0 0-2 5.24V13a3 3 0 0 0 2 2.83V17a3.5 3.5 0 0 0 3.5 3.5"></path>
                <path d="M14.5 2A3.5 3.5 0 0 1 18 5.5V6a3 3 0 0 1 2 5.24V13a3 3 0 0 1-2 2.83V17a3.5 3.5 0 0 1-3.5 3.5"></path>
                <path d="M9.5 5.5v13"></path>
                <path d="M14.5 5.5v13"></path>
              </svg>
            </div>
            <h3>Psychiatric Care</h3>
            <p>Comprehensive evaluation and treatment for depression, anxiety, bipolar disorder, schizophrenia, ADHD, and other mental health conditions.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-icon icon-rose">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.6z"></path>
              </svg>
            </div>
            <h3>Lifestyle Medicine</h3>
            <p>Evidence-based lifestyle interventions addressing sleep, nutrition, exercise, and stress — treating the whole person, not just the symptoms.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-icon icon-green">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 20A7 7 0 0 1 4 13V9a2 2 0 0 1 2-2h1a4 4 0 0 1 4 4v9z"></path>
                <path d="M11 12.5C11 8 15 4 20 4c0 5-4 9-9 8.5z"></path>
              </svg>
            </div>
            <h3>Life Coaching</h3>
            <p>Personalized coaching programs in cognitive-behavioral coaching, stress management, spiritual wellness, and healthy habit formation.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-icon icon-violet">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2v4"></path>
                <path d="M12 18v4"></path>
                <path d="M4.9 4.9l2.8 2.8"></path>
                <path d="M16.3 16.3l2.8 2.8"></path>
                <path d="M2 12h4"></path>
                <path d="M18 12h4"></path>
                <path d="M4.9 19.1l2.8-2.8"></path>
                <path d="M16.3 7.7l2.8-2.8"></path>
              </svg>
            </div>
            <h3>Spiritual Wellness</h3>
            <p>Integrating faith and spirituality into the healing process through pastoral accompaniment and biopsychosociospiritual care.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================= HOW IT WORKS ============================= -->
    <section class="how-it-works" id="how-it-works">
      <div class="section-inner reveal">
        <p class="eyebrow">Your First Visit</p>
        <h2>How It Works</h2>
        <p class="section-subtext">A simple, welcoming process designed with your comfort in mind.</p>

        <div class="steps">
          <div class="step-card reveal">
            <div class="step-number">1</div>
            <h3>Complete Your Intake Form</h3>
            <p>Use our self-service kiosk to fill out your patient information, medical history, and current concerns. It takes about 10&ndash;15 minutes and everything is kept confidential.</p>
          </div>

          <div class="step-connector" aria-hidden="true"></div>

          <div class="step-card reveal">
            <div class="step-number">2</div>
            <h3>Meet Your Care Team</h3>
            <p>A member of our clinical team reviews your intake form with you and answers any questions before your consultation begins.</p>
          </div>

          <div class="step-connector" aria-hidden="true"></div>

          <div class="step-card reveal">
            <div class="step-number">3</div>
            <h3>Build Your Care Plan</h3>
            <p>Your provider works with you to create a personalized plan spanning psychiatric care, lifestyle medicine, and — if desired — spiritual support.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================= FINAL CTA ============================= -->
    <section class="final-cta">
      <div class="section-inner reveal">
        <h2>Ready to take the first step?</h2>
        <p>Begin your intake form now — you can pause and pick up any time from our home page.</p>
        <a href="/intake-form" class="btn-primary btn-lg">
          Begin Intake Form
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </a>
      </div>
    </section>

  </main>

  <!-- ============================= FOOTER ============================= -->
  <footer class="site-footer" id="contact">
    <div class="footer-inner">
      <div class="footer-col">
        <div class="nav-brand">
          <span class="nav-brand-mark">MC</span>
          <span class="nav-brand-text">MedCare Clinic</span>
        </div>
        <p>MedCare Integrated Psychiatric &amp; Lifestyle Medicine Clinic. Compassionate, evidence-based care for mind, body, and spirit.</p>
      </div>

      <div class="footer-col">
        <h4>Contact</h4>
        <p>123 Wellness Avenue<br>Tacloban City, Leyte</p>
        <p>(053) 555-0100<br>info@medcareclinic.example</p>
      </div>

      <div class="footer-col">
        <h4>Clinic Hours</h4>
        <p>Mon &ndash; Fri: 8:00 AM &ndash; 5:00 PM<br>Sat: 8:00 AM &ndash; 12:00 PM<br>Sun: Closed</p>
      </div>

      <div class="footer-col">
        <h4>Quick Links</h4>
        <p><a href="#services">Services</a></p>
        <p><a href="#how-it-works">How It Works</a></p>
        <p><a href="/intake-form">Begin Intake Form</a></p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <span id="footer-year"></span> MedCare Integrated Psychiatric &amp; Lifestyle Medicine Clinic. All information is kept confidential and protected under medical privacy laws.</p>
    </div>
  </footer>

  <script src="{{ asset('js/index.js') }}"></script>
</body>
</html>
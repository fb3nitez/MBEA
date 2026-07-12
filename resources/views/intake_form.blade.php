<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Patient Intake Form — MedCare Clinic</title>
  <link rel="stylesheet" href="{{ asset('css/intake_form.css') }}"/>
</head>
<body>

<!-- Ambient background orbs -->
<div class="bg-orb orb-1"></div>
<div class="bg-orb orb-2"></div>
<div class="bg-orb orb-3"></div>

<div class="kiosk-page">

  <!-- Header -->
  <div class="form-header">
    <a href="/" class="back-link">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
      Back to Home
    </a>
    <div class="header-clinic">
      <div class="header-logo">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
      </div>
      <span>MedCare Clinic</span>
    </div>
  </div>

  <!-- Hero title -->
  <div class="form-hero">
    <h1>Patient Intake Form</h1>
    <p>We're glad you're here. Take your time — there are no wrong answers.</p>
  </div>

  <!-- Step indicators -->
  <div class="step-indicators">
    <div class="step-dot active" data-step="1">
      <div class="step-dot-inner">1</div>
      <span>Personal</span>
    </div>
    <div class="step-connector"></div>
    <div class="step-dot" data-step="2">
      <div class="step-dot-inner">2</div>
      <span>Medical</span>
    </div>
    <div class="step-connector"></div>
    <div class="step-dot" data-step="3">
      <div class="step-dot-inner">3</div>
      <span>Psychiatric</span>
    </div>
    <div class="step-connector"></div>
    <div class="step-dot" data-step="4">
      <div class="step-dot-inner">4</div>
      <span>Lifestyle</span>
    </div>
    <div class="step-connector"></div>
    <div class="step-dot" data-step="5">
      <div class="step-dot-inner">5</div>
      <span>Review</span>
    </div>
  </div>

  <!-- Progress bar -->
  <div class="progress-wrap">
    <div class="progress-track">
      <div class="progress-fill" id="progress-fill" style="width:20%"></div>
    </div>
    <div class="progress-meta">
      <span id="step-label">Step 1 of 5</span>
      <span id="percent-label">20% Complete</span>
    </div>
  </div>

  <!-- Validation error banner -->
  <div class="validation-banner hidden" id="validation-banner">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <span id="validation-message">Please fill in all required fields before continuing.</span>
  </div>

  <!-- Form card -->
  <div class="form-card">

    <!-- ═══════════════════════════════════════════
         STEP 1: Personal Information
    ═══════════════════════════════════════════ -->
    <div class="step-panel active" id="step-1">
      <div class="step-header">
        <div class="step-icon blue-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div>
          <h2>Personal Information</h2>
          <p>Let's start with the basics. Required fields are marked with *</p>
        </div>
      </div>

      <div class="field-grid">
        <div class="field-group required">
          <label for="name">Full Name <span class="req-star">*</span></label>
          <input type="text" id="name" placeholder="Enter your full name"/>
          <div class="field-error hidden" id="err-name">Please enter your full name.</div>
        </div>
        <div class="field-group required">
          <label for="age">Age <span class="req-star">*</span></label>
          <input type="number" id="age" placeholder="Your age" min="1" max="120"/>
          <div class="field-error hidden" id="err-age">Please enter a valid age.</div>
        </div>
        <div class="field-group required">
          <label for="birthday">Date of Birth <span class="req-star">*</span></label>
          <input type="date" id="birthday"/>
          <div class="field-error hidden" id="err-birthday">Please enter your date of birth.</div>
        </div>
        <div class="field-group">
          <label for="religion">Religion</label>
          <input type="text" id="religion" placeholder="Your religion (optional)"/>
        </div>
        <div class="field-group required">
          <label for="sex">Sex <span class="req-star">*</span></label>
          <select id="sex">
            <option value="">Select sex</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="intersex">Intersex</option>
          </select>
          <div class="field-error hidden" id="err-sex">Please select your sex.</div>
        </div>
        <div class="field-group">
          <label for="gender">Gender Identity</label>
          <input type="text" id="gender" placeholder="Your gender identity (optional)"/>
        </div>
        <div class="field-group">
          <label for="marital-status">Marital Status</label>
          <select id="marital-status">
            <option value="">Select status</option>
            <option value="single">Single</option>
            <option value="married">Married</option>
            <option value="divorced">Divorced</option>
            <option value="widowed">Widowed</option>
            <option value="separated">Separated</option>
          </select>
        </div>
        <div class="field-group">
          <label for="year-level">Student Year Level</label>
          <input type="text" id="year-level" placeholder="e.g., 3rd Year, N/A"/>
        </div>
        <div class="field-group full-width">
          <label for="course">Course / Program</label>
          <input type="text" id="course" placeholder="Your course or program"/>
        </div>
        <div class="field-group full-width">
          <label for="occupation">Occupation</label>
          <input type="text" id="occupation" placeholder="Your occupation"/>
        </div>
        <div class="field-group full-width required">
          <label for="chief-complaint">Chief Complaint / Reason for Consultation <span class="req-star">*</span></label>
          <textarea id="chief-complaint" rows="3" placeholder="What brings you here today? Please describe in your own words."></textarea>
          <div class="field-error hidden" id="err-chief-complaint">Please describe your reason for visiting.</div>
        </div>
        <div class="field-group full-width">
          <label for="primary-diagnosis">Primary Diagnosis (if known)</label>
          <input type="text" id="primary-diagnosis" placeholder="Enter diagnosis if applicable (optional)"/>
        </div>
      </div>
    </div>


    <!-- ═══════════════════════════════════════════
         STEP 2: Medical History
    ═══════════════════════════════════════════ -->
    <div class="step-panel" id="step-2">
      <div class="step-header">
        <div class="step-icon green-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        </div>
        <div>
          <h2>Medical History</h2>
          <p>Please indicate any relevant medical conditions — check all that apply</p>
        </div>
      </div>

      <!-- A. Personal Medical History -->
      <div class="form-section">
        <div class="section-label">
          <span class="section-letter">A</span>
          Personal Medical History
        </div>
        <p class="section-hint">Select any conditions you have been diagnosed with:</p>
        <div class="checkbox-grid">
          <label class="check-card"><input type="checkbox" id="pmh-hypertension"/><div class="check-card-inner"><div class="check-icon">🫀</div><span>Hypertension</span></div></label>
          <label class="check-card"><input type="checkbox" id="pmh-stroke"/><div class="check-card-inner"><div class="check-icon">🧠</div><span>Stroke or TIA</span></div></label>
          <label class="check-card"><input type="checkbox" id="pmh-tuberculosis"/><div class="check-card-inner"><div class="check-icon">🫁</div><span>Tuberculosis</span></div></label>
          <label class="check-card"><input type="checkbox" id="pmh-thyroid"/><div class="check-card-inner"><div class="check-icon">🦋</div><span>Thyroid Disorders</span></div></label>
          <label class="check-card"><input type="checkbox" id="pmh-diabetes"/><div class="check-card-inner"><div class="check-icon">💉</div><span>Diabetes Mellitus</span></div></label>
          <label class="check-card"><input type="checkbox" id="pmh-chronic-pain"/><div class="check-card-inner"><div class="check-icon">😣</div><span>Chronic Pain / Fibromyalgia</span></div></label>
          <label class="check-card"><input type="checkbox" id="pmh-asthma"/><div class="check-card-inner"><div class="check-icon">💨</div><span>Bronchial Asthma</span></div></label>
          <label class="check-card"><input type="checkbox" id="pmh-epilepsy"/><div class="check-card-inner"><div class="check-icon">⚡</div><span>Epilepsy / Seizure Disorder</span></div></label>
        </div>

        <div class="expand-check-group">
          <label class="check-card expand-trigger"><input type="checkbox" id="pmh-autoimmune" data-expands="autoimmune-expand"/><div class="check-card-inner"><div class="check-icon">🛡️</div><span>Autoimmune Disease</span></div></label>
          <div class="expand-target hidden" id="autoimmune-expand">
            <input type="text" id="pmh-autoimmune-specify" placeholder="Please specify autoimmune condition"/>
          </div>
        </div>
        <div class="expand-check-group">
          <label class="check-card expand-trigger"><input type="checkbox" id="pmh-cancer" data-expands="cancer-expand"/><div class="check-card-inner"><div class="check-icon">🎗️</div><span>Cancer</span></div></label>
          <div class="expand-target hidden" id="cancer-expand">
            <input type="text" id="pmh-cancer-specify" placeholder="Please specify cancer type"/>
          </div>
        </div>
        <div class="expand-check-group">
          <label class="check-card expand-trigger"><input type="checkbox" id="pmh-other" data-expands="pmh-other-expand"/><div class="check-card-inner"><div class="check-icon">➕</div><span>Other</span></div></label>
          <div class="expand-target hidden" id="pmh-other-expand">
            <input type="text" id="pmh-other-specify" placeholder="Please specify other condition"/>
          </div>
        </div>

        <div class="field-group" style="margin-top:1.5rem;">
          <label for="current-medications">Medications Currently Taking</label>
          <textarea id="current-medications" rows="3" placeholder="List all current medications and dosages (or write 'None')"></textarea>
        </div>
      </div>

      <!-- B. Family History -->
      <div class="form-section">
        <div class="section-label">
          <span class="section-letter">B</span>
          Family Medical &amp; Psychiatric History
        </div>
        <p class="section-hint">Check all that apply to your family members:</p>
        <div class="checkbox-grid">
          <label class="check-card"><input type="checkbox" id="fh-hypertension"/><div class="check-card-inner"><div class="check-icon">🫀</div><span>Hypertension</span></div></label>
          <label class="check-card"><input type="checkbox" id="fh-stroke"/><div class="check-card-inner"><div class="check-icon">🧠</div><span>Stroke</span></div></label>
          <label class="check-card"><input type="checkbox" id="fh-diabetes"/><div class="check-card-inner"><div class="check-icon">💉</div><span>Diabetes</span></div></label>
          <label class="check-card"><input type="checkbox" id="fh-substance"/><div class="check-card-inner"><div class="check-icon">⚠️</div><span>Substance Use Disorder</span></div></label>
        </div>
        <div class="expand-check-group">
          <label class="check-card expand-trigger"><input type="checkbox" id="fh-cancer" data-expands="fh-cancer-expand"/><div class="check-card-inner"><div class="check-icon">🎗️</div><span>Cancer</span></div></label>
          <div class="expand-target hidden" id="fh-cancer-expand">
            <input type="text" id="fh-cancer-type" placeholder="Specify type"/>
            <input type="text" id="fh-cancer-relation" placeholder="Relation (e.g., mother, father)" style="margin-top:8px;"/>
          </div>
        </div>
        <div class="expand-check-group">
          <label class="check-card expand-trigger"><input type="checkbox" id="fh-psychiatric" data-expands="fh-psychiatric-expand"/><div class="check-card-inner"><div class="check-icon">💙</div><span>Psychiatric Disorders</span></div></label>
          <div class="expand-target hidden" id="fh-psychiatric-expand">
            <input type="text" id="fh-psychiatric-type" placeholder="e.g., Depression, Bipolar, Schizophrenia"/>
            <input type="text" id="fh-psychiatric-relation" placeholder="Relation (e.g., sibling, parent)" style="margin-top:8px;"/>
          </div>
        </div>
        <div class="expand-check-group">
          <label class="check-card expand-trigger"><input type="checkbox" id="fh-other" data-expands="fh-other-expand"/><div class="check-card-inner"><div class="check-icon">➕</div><span>Other</span></div></label>
          <div class="expand-target hidden" id="fh-other-expand">
            <input type="text" id="fh-other-specify" placeholder="Specify condition"/>
            <input type="text" id="fh-other-relation" placeholder="Relation" style="margin-top:8px;"/>
          </div>
        </div>
      </div>
    </div>


    <!-- ═══════════════════════════════════════════
         STEP 3: Psychiatric History
    ═══════════════════════════════════════════ -->
    <div class="step-panel" id="step-3">
      <div class="step-header">
        <div class="step-icon purple-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/><circle cx="12" cy="12" r="10"/></svg>
        </div>
        <div>
          <h2>Psychiatric History</h2>
          <p>Your responses are completely confidential and help us provide better care</p>
        </div>
      </div>

      <!-- Comfort notice -->
      <div class="comfort-notice">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        <p>You are safe here. Share only what you are comfortable with. All information is kept strictly confidential.</p>
      </div>

      <!-- A. Mental Health Diagnosis -->
      <div class="form-section">
        <div class="section-label"><span class="section-letter">A</span>Mental Health Diagnosis</div>

        <div class="radio-question">
          <p class="q-label">Have you been diagnosed with a mental health condition?</p>
          <div class="yn-group">
            <label class="yn-card"><input type="radio" name="diagnosed-mh" value="yes" id="diagnosed-yes"/><span>Yes</span></label>
            <label class="yn-card"><input type="radio" name="diagnosed-mh" value="no" id="diagnosed-no"/><span>No</span></label>
          </div>
          <div class="expand-target hidden" id="diagnosed-expand">
            <div class="field-group">
              <label for="diagnosis-specify">Please specify your diagnosis</label>
              <input type="text" id="diagnosis-specify" placeholder="e.g., Depression, Anxiety, Bipolar Disorder"/>
            </div>
          </div>
        </div>

        <div class="radio-question" style="margin-top:1.5rem;">
          <p class="q-label">Have you been hospitalized for psychiatric reasons?</p>
          <div class="yn-group">
            <label class="yn-card"><input type="radio" name="hospitalized" value="yes" id="hospitalized-yes"/><span>Yes</span></label>
            <label class="yn-card"><input type="radio" name="hospitalized" value="no" id="hospitalized-no"/><span>No</span></label>
          </div>
          <div class="expand-target hidden" id="hospitalized-expand">
            <div class="field-grid" style="margin-top:0.75rem;">
              <div class="field-group">
                <label for="hosp-times">Number of times</label>
                <input type="number" id="hosp-times" placeholder="Enter number" min="1"/>
              </div>
              <div class="field-group">
                <label for="hosp-when">When (approximate year)</label>
                <input type="text" id="hosp-when" placeholder="e.g., 2020, January 2023"/>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- B. Trauma -->
      <div class="form-section">
        <div class="section-label"><span class="section-letter">B</span>History of Trauma &amp; Adverse Experiences</div>
        <p class="section-hint">Check any that apply. You may skip any you're not comfortable answering.</p>

        <div class="trauma-grid">
          <div class="trauma-card">
            <label class="trauma-header"><input type="checkbox" id="trauma-physical-check" data-expands="trauma-physical-expand"/>
              <div class="trauma-icon" style="background:#fff1f2;color:#e11d48;">🤛</div>
              <span>Physical Abuse</span>
            </label>
            <div class="expand-target hidden" id="trauma-physical-expand">
              <p class="section-hint" style="margin-bottom:8px;">When did this occur?</p>
              <div class="time-chips">
                <label class="time-chip"><input type="checkbox" id="tp-child"/>As a child</label>
                <label class="time-chip"><input type="checkbox" id="tp-adult"/>As an adult</label>
                <label class="time-chip"><input type="checkbox" id="tp-ongoing"/>Ongoing</label>
                <label class="time-chip"><input type="checkbox" id="tp-past"/>Past experience</label>
              </div>
              <textarea id="tp-details" rows="2" placeholder="Additional details (optional)" style="margin-top:10px;"></textarea>
            </div>
          </div>

          <div class="trauma-card">
            <label class="trauma-header"><input type="checkbox" id="trauma-emotional-check" data-expands="trauma-emotional-expand"/>
              <div class="trauma-icon" style="background:#fef3c7;color:#d97706;">💬</div>
              <span>Emotional Abuse</span>
            </label>
            <div class="expand-target hidden" id="trauma-emotional-expand">
              <p class="section-hint" style="margin-bottom:8px;">When did this occur?</p>
              <div class="time-chips">
                <label class="time-chip"><input type="checkbox" id="te-child"/>As a child</label>
                <label class="time-chip"><input type="checkbox" id="te-adult"/>As an adult</label>
                <label class="time-chip"><input type="checkbox" id="te-ongoing"/>Ongoing</label>
                <label class="time-chip"><input type="checkbox" id="te-past"/>Past experience</label>
              </div>
              <textarea id="te-details" rows="2" placeholder="Additional details (optional)" style="margin-top:10px;"></textarea>
            </div>
          </div>

          <div class="trauma-card">
            <label class="trauma-header"><input type="checkbox" id="trauma-sexual-check" data-expands="trauma-sexual-expand"/>
              <div class="trauma-icon" style="background:#fdf2f8;color:#c026d3;">⚠️</div>
              <span>Sexual Abuse</span>
            </label>
            <div class="expand-target hidden" id="trauma-sexual-expand">
              <p class="section-hint" style="margin-bottom:8px;">When did this occur?</p>
              <div class="time-chips">
                <label class="time-chip"><input type="checkbox" id="ts-child"/>As a child</label>
                <label class="time-chip"><input type="checkbox" id="ts-adult"/>As an adult</label>
                <label class="time-chip"><input type="checkbox" id="ts-ongoing"/>Ongoing</label>
                <label class="time-chip"><input type="checkbox" id="ts-past"/>Past experience</label>
              </div>
              <textarea id="ts-details" rows="2" placeholder="Additional details (optional)" style="margin-top:10px;"></textarea>
            </div>
          </div>

          <div class="trauma-card">
            <label class="trauma-header"><input type="checkbox" id="trauma-neglect-check" data-expands="trauma-neglect-expand"/>
              <div class="trauma-icon" style="background:#f0fdf4;color:#16a34a;">🌿</div>
              <span>Neglect</span>
            </label>
            <div class="expand-target hidden" id="trauma-neglect-expand">
              <p class="section-hint" style="margin-bottom:8px;">When did this occur?</p>
              <div class="time-chips">
                <label class="time-chip"><input type="checkbox" id="tn-child"/>As a child</label>
                <label class="time-chip"><input type="checkbox" id="tn-adult"/>As an adult</label>
                <label class="time-chip"><input type="checkbox" id="tn-ongoing"/>Ongoing</label>
                <label class="time-chip"><input type="checkbox" id="tn-past"/>Past experience</label>
              </div>
              <textarea id="tn-details" rows="2" placeholder="Additional details (optional)" style="margin-top:10px;"></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- ═══════════════════════════════════════════
         STEP 4: Lifestyle Assessment
    ═══════════════════════════════════════════ -->
    <div class="step-panel" id="step-4">
      <div class="step-header">
        <div class="step-icon teal-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        </div>
        <div>
          <h2>Lifestyle Assessment</h2>
          <p>Help us understand your current lifestyle and wellness habits</p>
        </div>
      </div>

      <!-- Overall Health Score -->
      <div class="lifestyle-block blue-block">
        <div class="lifestyle-block-header">
          <span class="lifestyle-block-icon">⭐</span>
          <div>
            <div class="lifestyle-block-title">Overall Health Score</div>
            <div class="lifestyle-block-sub">How would you rate your overall health right now?</div>
          </div>
          <div class="health-score-display" id="health-score-display">5</div>
        </div>
        <input type="range" id="health-score" min="0" max="10" step="1" value="5" class="styled-slider blue-slider"/>
        <div class="slider-labels"><span>0 — Poor</span><span>10 — Excellent</span></div>
      </div>

      <!-- Sleep -->
      <div class="lifestyle-block">
        <div class="lifestyle-block-header">
          <span class="lifestyle-block-icon">🌙</span>
          <div>
            <div class="lifestyle-block-title">Sleep</div>
            <div class="lifestyle-block-sub">Your sleep patterns</div>
          </div>
        </div>
        <div class="field-grid">
          <div class="field-group">
            <label for="sleep-hours">Average hours of sleep per night</label>
            <input type="number" id="sleep-hours" placeholder="e.g., 7" min="0" max="24"/>
          </div>
          <div class="field-group">
            <label for="tired-frequency">How often do you feel tired?</label>
            <select id="tired-frequency">
              <option value="">Select frequency</option>
              <option value="rarely">Rarely</option>
              <option value="sometimes">Sometimes</option>
              <option value="often">Often</option>
              <option value="always">Always</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Nutrition -->
      <div class="lifestyle-block">
        <div class="lifestyle-block-header">
          <span class="lifestyle-block-icon">🥗</span>
          <div>
            <div class="lifestyle-block-title">Weight &amp; Nutrition</div>
            <div class="lifestyle-block-sub">Your eating habits</div>
          </div>
        </div>
        <div class="field-grid">
          <div class="field-group">
            <label for="weight-perception">Current weight perception</label>
            <select id="weight-perception">
              <option value="">Select perception</option>
              <option value="underweight">Underweight</option>
              <option value="normal">Normal weight</option>
              <option value="overweight">Overweight</option>
              <option value="obese">Obese</option>
            </select>
          </div>
          <div class="field-group">
            <label for="fast-food">How often do you eat fast food?</label>
            <select id="fast-food">
              <option value="">Select frequency</option>
              <option value="never">Never</option>
              <option value="1-2-month">1-2× per month</option>
              <option value="1-2-week">1-2× per week</option>
              <option value="3-4-week">3-4× per week</option>
              <option value="daily">Daily</option>
            </select>
          </div>
          <div class="field-group full-width">
            <label for="fruits-veg">Daily servings of fruits and vegetables</label>
            <select id="fruits-veg">
              <option value="">Select servings</option>
              <option value="0-1">0-1 servings</option>
              <option value="2-3">2-3 servings</option>
              <option value="4-5">4-5 servings</option>
              <option value="6+">6+ servings</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Exercise -->
      <div class="lifestyle-block green-block">
        <div class="lifestyle-block-header">
          <span class="lifestyle-block-icon">🏃</span>
          <div>
            <div class="lifestyle-block-title">Exercise</div>
            <div class="lifestyle-block-sub">Your physical activity</div>
          </div>
        </div>
        <div class="field-group">
          <label for="exercise-freq">How many days per week do you exercise?</label>
          <select id="exercise-freq">
            <option value="">Select frequency</option>
            <option value="0">0 days — sedentary</option>
            <option value="1-2">1-2 days</option>
            <option value="3-4">3-4 days</option>
            <option value="5-6">5-6 days</option>
            <option value="7">7 days</option>
          </select>
        </div>
      </div>

      <!-- PHQ-9 -->
      <div class="lifestyle-block purple-block">
        <div class="lifestyle-block-header">
          <span class="lifestyle-block-icon">🧠</span>
          <div>
            <div class="lifestyle-block-title">Mental Health &amp; Well-being</div>
            <div class="lifestyle-block-sub">Over the past 2 weeks, how often have you experienced the following?</div>
          </div>
        </div>

        <div class="phq-list">
          <div class="phq-item">
            <div class="phq-q">Little interest or pleasure in doing things</div>
            <div class="phq-options">
              <label class="phq-opt"><input type="radio" name="phq-little-interest" value="not-at-all"/><span>Not at all</span></label>
              <label class="phq-opt"><input type="radio" name="phq-little-interest" value="several-days"/><span>Several days</span></label>
              <label class="phq-opt"><input type="radio" name="phq-little-interest" value="more-than-half"/><span>More than half</span></label>
              <label class="phq-opt"><input type="radio" name="phq-little-interest" value="nearly-every-day"/><span>Nearly every day</span></label>
            </div>
          </div>
          <div class="phq-item">
            <div class="phq-q">Feeling down, depressed, or hopeless</div>
            <div class="phq-options">
              <label class="phq-opt"><input type="radio" name="phq-feeling-down" value="not-at-all"/><span>Not at all</span></label>
              <label class="phq-opt"><input type="radio" name="phq-feeling-down" value="several-days"/><span>Several days</span></label>
              <label class="phq-opt"><input type="radio" name="phq-feeling-down" value="more-than-half"/><span>More than half</span></label>
              <label class="phq-opt"><input type="radio" name="phq-feeling-down" value="nearly-every-day"/><span>Nearly every day</span></label>
            </div>
          </div>
          <div class="phq-item">
            <div class="phq-q">Trouble falling or staying asleep, or sleeping too much</div>
            <div class="phq-options">
              <label class="phq-opt"><input type="radio" name="phq-trouble-sleeping" value="not-at-all"/><span>Not at all</span></label>
              <label class="phq-opt"><input type="radio" name="phq-trouble-sleeping" value="several-days"/><span>Several days</span></label>
              <label class="phq-opt"><input type="radio" name="phq-trouble-sleeping" value="more-than-half"/><span>More than half</span></label>
              <label class="phq-opt"><input type="radio" name="phq-trouble-sleeping" value="nearly-every-day"/><span>Nearly every day</span></label>
            </div>
          </div>
          <div class="phq-item">
            <div class="phq-q">Feeling tired or having little energy</div>
            <div class="phq-options">
              <label class="phq-opt"><input type="radio" name="phq-feeling-tired" value="not-at-all"/><span>Not at all</span></label>
              <label class="phq-opt"><input type="radio" name="phq-feeling-tired" value="several-days"/><span>Several days</span></label>
              <label class="phq-opt"><input type="radio" name="phq-feeling-tired" value="more-than-half"/><span>More than half</span></label>
              <label class="phq-opt"><input type="radio" name="phq-feeling-tired" value="nearly-every-day"/><span>Nearly every day</span></label>
            </div>
          </div>
          <div class="phq-item">
            <div class="phq-q">Poor appetite or overeating</div>
            <div class="phq-options">
              <label class="phq-opt"><input type="radio" name="phq-poor-appetite" value="not-at-all"/><span>Not at all</span></label>
              <label class="phq-opt"><input type="radio" name="phq-poor-appetite" value="several-days"/><span>Several days</span></label>
              <label class="phq-opt"><input type="radio" name="phq-poor-appetite" value="more-than-half"/><span>More than half</span></label>
              <label class="phq-opt"><input type="radio" name="phq-poor-appetite" value="nearly-every-day"/><span>Nearly every day</span></label>
            </div>
          </div>
          <div class="phq-item">
            <div class="phq-q">Feeling bad about yourself or that you are a failure</div>
            <div class="phq-options">
              <label class="phq-opt"><input type="radio" name="phq-feeling-bad" value="not-at-all"/><span>Not at all</span></label>
              <label class="phq-opt"><input type="radio" name="phq-feeling-bad" value="several-days"/><span>Several days</span></label>
              <label class="phq-opt"><input type="radio" name="phq-feeling-bad" value="more-than-half"/><span>More than half</span></label>
              <label class="phq-opt"><input type="radio" name="phq-feeling-bad" value="nearly-every-day"/><span>Nearly every day</span></label>
            </div>
          </div>
          <div class="phq-item">
            <div class="phq-q">Trouble concentrating on things</div>
            <div class="phq-options">
              <label class="phq-opt"><input type="radio" name="phq-trouble-concentrating" value="not-at-all"/><span>Not at all</span></label>
              <label class="phq-opt"><input type="radio" name="phq-trouble-concentrating" value="several-days"/><span>Several days</span></label>
              <label class="phq-opt"><input type="radio" name="phq-trouble-concentrating" value="more-than-half"/><span>More than half</span></label>
              <label class="phq-opt"><input type="radio" name="phq-trouble-concentrating" value="nearly-every-day"/><span>Nearly every day</span></label>
            </div>
          </div>
          <div class="phq-item">
            <div class="phq-q">Moving or speaking slowly, or being fidgety/restless</div>
            <div class="phq-options">
              <label class="phq-opt"><input type="radio" name="phq-moving-slow" value="not-at-all"/><span>Not at all</span></label>
              <label class="phq-opt"><input type="radio" name="phq-moving-slow" value="several-days"/><span>Several days</span></label>
              <label class="phq-opt"><input type="radio" name="phq-moving-slow" value="more-than-half"/><span>More than half</span></label>
              <label class="phq-opt"><input type="radio" name="phq-moving-slow" value="nearly-every-day"/><span>Nearly every day</span></label>
            </div>
          </div>
          <div class="phq-item phq-sensitive">
            <div class="phq-q">Thoughts of hurting yourself
              <span class="sensitive-tag">Sensitive</span>
            </div>
            <div class="phq-options">
              <label class="phq-opt"><input type="radio" name="phq-thoughts-hurting" value="not-at-all"/><span>Not at all</span></label>
              <label class="phq-opt"><input type="radio" name="phq-thoughts-hurting" value="several-days"/><span>Several days</span></label>
              <label class="phq-opt"><input type="radio" name="phq-thoughts-hurting" value="more-than-half"/><span>More than half</span></label>
              <label class="phq-opt"><input type="radio" name="phq-thoughts-hurting" value="nearly-every-day"/><span>Nearly every day</span></label>
            </div>
          </div>
        </div>
      </div>

      <!-- Substance Use -->
      <div class="lifestyle-block">
        <div class="lifestyle-block-header">
          <span class="lifestyle-block-icon">🔍</span>
          <div>
            <div class="lifestyle-block-title">Substance Use &amp; Addictive Behaviors</div>
            <div class="lifestyle-block-sub">Check any that apply and rate your level of concern</div>
          </div>
        </div>

        <div class="substance-grid">
          <div class="substance-card">
            <label class="substance-toggle"><input type="checkbox" id="sub-nicotine" data-expands="sub-nicotine-expand"/>
              <div class="substance-icon">🚬</div><span>Nicotine</span>
            </label>
            <div class="expand-target hidden" id="sub-nicotine-expand">
              <input type="text" id="sub-nicotine-amount" placeholder="Amount per day"/>
              <label class="concern-label">Concern level: <span class="concern-val" id="val-sub-nicotine-concern">0</span>/5</label>
              <input type="range" class="concern-slider styled-slider" id="sub-nicotine-concern" min="0" max="5" step="1" value="0"/>
            </div>
          </div>
          <div class="substance-card">
            <label class="substance-toggle"><input type="checkbox" id="sub-alcohol" data-expands="sub-alcohol-expand"/>
              <div class="substance-icon">🍺</div><span>Alcohol</span>
            </label>
            <div class="expand-target hidden" id="sub-alcohol-expand">
              <input type="text" id="sub-alcohol-amount" placeholder="Drinks per week"/>
              <label class="concern-label">Concern level: <span class="concern-val" id="val-sub-alcohol-concern">0</span>/5</label>
              <input type="range" class="concern-slider styled-slider" id="sub-alcohol-concern" min="0" max="5" step="1" value="0"/>
            </div>
          </div>
          <div class="substance-card">
            <label class="substance-toggle"><input type="checkbox" id="sub-recreational" data-expands="sub-recreational-expand"/>
              <div class="substance-icon">💊</div><span>Recreational Drugs</span>
            </label>
            <div class="expand-target hidden" id="sub-recreational-expand">
              <input type="text" id="sub-recreational-amount" placeholder="Frequency"/>
              <label class="concern-label">Concern level: <span class="concern-val" id="val-sub-recreational-concern">0</span>/5</label>
              <input type="range" class="concern-slider styled-slider" id="sub-recreational-concern" min="0" max="5" step="1" value="0"/>
            </div>
          </div>
          <div class="substance-card">
            <label class="substance-toggle"><input type="checkbox" id="sub-marijuana" data-expands="sub-marijuana-expand"/>
              <div class="substance-icon">🌿</div><span>Marijuana</span>
            </label>
            <div class="expand-target hidden" id="sub-marijuana-expand">
              <input type="text" id="sub-marijuana-amount" placeholder="Frequency"/>
              <label class="concern-label">Concern level: <span class="concern-val" id="val-sub-marijuana-concern">0</span>/5</label>
              <input type="range" class="concern-slider styled-slider" id="sub-marijuana-concern" min="0" max="5" step="1" value="0"/>
            </div>
          </div>
          <div class="substance-card">
            <label class="substance-toggle"><input type="checkbox" id="sub-screentime" data-expands="sub-screentime-expand"/>
              <div class="substance-icon">📱</div><span>Social Media / Screen Time</span>
            </label>
            <div class="expand-target hidden" id="sub-screentime-expand">
              <input type="text" id="sub-screentime-amount" placeholder="Hours per day"/>
              <label class="concern-label">Concern level: <span class="concern-val" id="val-sub-screentime-concern">0</span>/5</label>
              <input type="range" class="concern-slider styled-slider" id="sub-screentime-concern" min="0" max="5" step="1" value="0"/>
            </div>
          </div>
          <div class="substance-card">
            <label class="substance-toggle"><input type="checkbox" id="sub-gambling" data-expands="sub-gambling-expand"/>
              <div class="substance-icon">🎲</div><span>Gambling</span>
            </label>
            <div class="expand-target hidden" id="sub-gambling-expand">
              <input type="text" id="sub-gambling-amount" placeholder="Frequency"/>
              <label class="concern-label">Concern level: <span class="concern-val" id="val-sub-gambling-concern">0</span>/5</label>
              <input type="range" class="concern-slider styled-slider" id="sub-gambling-concern" min="0" max="5" step="1" value="0"/>
            </div>
          </div>
          <div class="substance-card">
            <label class="substance-toggle"><input type="checkbox" id="sub-others" data-expands="sub-others-expand"/>
              <div class="substance-icon">➕</div><span>Others</span>
            </label>
            <div class="expand-target hidden" id="sub-others-expand">
              <input type="text" id="sub-others-specify" placeholder="Please specify"/>
              <label class="concern-label">Concern level: <span class="concern-val" id="val-sub-others-concern">0</span>/5</label>
              <input type="range" class="concern-slider styled-slider" id="sub-others-concern" min="0" max="5" step="1" value="0"/>
            </div>
          </div>
        </div>
      </div>

      <!-- Motivation -->
      <div class="lifestyle-block green-block">
        <div class="lifestyle-block-header">
          <span class="lifestyle-block-icon">💪</span>
          <div>
            <div class="lifestyle-block-title">Motivation for Change</div>
            <div class="lifestyle-block-sub">What areas of your life are you most motivated to improve?</div>
          </div>
        </div>
        <div class="field-group">
          <label for="lifestyle-motivation">Top 3 lifestyle areas you want to work on (rank 1 = most important)</label>
          <textarea id="lifestyle-motivation" rows="3" placeholder="1. Sleep better&#10;2. Exercise more&#10;3. Reduce stress"></textarea>
        </div>
        <div class="field-group" style="margin-top:12px;">
          <label for="motivation-level">How motivated are you to make changes?</label>
          <select id="motivation-level">
            <option value="">Select motivation level</option>
            <option value="not-motivated">Not motivated right now</option>
            <option value="somewhat">Somewhat motivated</option>
            <option value="motivated">Motivated</option>
            <option value="very-motivated">Very motivated</option>
          </select>
        </div>
      </div>
    </div>


    <!-- ═══════════════════════════════════════════
         STEP 5: Review & Submit
    ═══════════════════════════════════════════ -->
    <div class="step-panel" id="step-5">
      <div class="step-header">
        <div class="step-icon gold-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
          <h2>Review &amp; Submit</h2>
          <p>Almost done — please review your responses before submitting</p>
        </div>
      </div>

      <div class="almost-done-banner">
        <div class="adb-icon">🎉</div>
        <div>
          <strong>Almost done!</strong>
          <p>Review each section below. Click <strong>Edit</strong> on any card to go back and make changes.</p>
        </div>
      </div>

      <div class="summary-card">
        <div class="summary-card-header">
          <div class="summary-card-title"><span>👤</span> Personal Information</div>
          <button class="edit-btn" onclick="goToStep(1)">✏ Edit</button>
        </div>
        <div id="summary-patient"></div>
      </div>

      <div class="summary-card">
        <div class="summary-card-header">
          <div class="summary-card-title"><span>🩺</span> Medical History</div>
          <button class="edit-btn" onclick="goToStep(2)">✏ Edit</button>
        </div>
        <div id="summary-medical"></div>
      </div>

      <div class="summary-card">
        <div class="summary-card-header">
          <div class="summary-card-title"><span>🧠</span> Psychiatric History</div>
          <button class="edit-btn" onclick="goToStep(3)">✏ Edit</button>
        </div>
        <div id="summary-psychiatric"></div>
      </div>

      <div class="summary-card">
        <div class="summary-card-header">
          <div class="summary-card-title"><span>🌿</span> Lifestyle Assessment</div>
          <button class="edit-btn" onclick="goToStep(4)">✏ Edit</button>
        </div>
        <div id="summary-lifestyle"></div>
      </div>

      <div class="privacy-block">
        <div class="privacy-block-icon">🔒</div>
        <div>
          <strong>Privacy &amp; Confidentiality</strong>
          <p>All information you've shared is strictly confidential and protected under the Data Privacy Act of 2012 (RA 10173). Your data will only be accessed by authorized healthcare professionals involved in your care at MedCare Clinic.</p>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <div class="form-nav">
      <button class="nav-btn back-btn hidden" id="btn-back" onclick="prevStep()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Back
      </button>
      <div class="nav-spacer"></div>
      <button class="nav-btn next-btn" id="btn-next" onclick="nextStep()">
        Continue
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
      </button>
      <button class="nav-btn submit-btn hidden" id="btn-submit" onclick="submitForm()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Submit Form
      </button>
    </div>
  </div>

  <p class="form-footer">🔒 Your information is confidential and protected under the Data Privacy Act of 2012 (RA 10173).</p>
</div>

<!-- Success overlay -->
<div class="success-overlay hidden" id="success-overlay">
  <div class="success-card">
    <div class="success-icon">✅</div>
    <h2>Form Submitted!</h2>
    <p>Thank you for completing your intake form. Please take a seat and wait to be called for your consultation.</p>
    <p class="success-sub">Our team has received your information and will review it before your appointment.</p>
    <a href="/" class="success-btn">Return to Home</a>
  </div>
</div>

<script src="{{ asset('js/intake_form.js') }}"></script>
</body>
</html>
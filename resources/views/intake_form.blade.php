<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Patient Intake Form — MedCare Clinic</title>
  <link rel="stylesheet" href="{{ asset('css/intake_form.css') }}" />
</head>
<body>
  <!-- Page wrapper -->
  <div class="kiosk-page">

    <!-- Back to Home -->
    <div class="back-home">
      <a href="/" class="btn-ghost">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
          <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
        Back to Home
      </a>
    </div>

    <!-- Header -->
    <div class="form-header">
      <h1>Patient Intake Form</h1>
      <p>Please complete all sections before your consultation</p>
    </div>

    <!-- Progress bar -->
    <div class="progress-container">
      <div class="progress-labels">
        <span id="step-label">Step 1 of 5</span>
        <span id="percent-label">20% Complete</span>
      </div>
      <div class="progress-track">
        <div class="progress-fill" id="progress-fill" style="width: 20%"></div>
      </div>
    </div>

    <!-- Form card -->
    <div class="form-card">

      <!-- STEP 1: Patient Information -->
      <div class="step-panel active" id="step-1">
        <div class="step-heading">
          <h2>Patient Information</h2>
          <p>Please provide your basic information</p>
        </div>
        <div class="field-grid">
          <!-- Full Name -->
          <div class="field-group">
            <label for="name">Full Name *</label>
            <input type="text" id="name" placeholder="Enter your full name" />
          </div>
          <!-- Age -->
          <div class="field-group">
            <label for="age">Age *</label>
            <input type="number" id="age" placeholder="Your age" />
          </div>
          <!-- Birthday -->
          <div class="field-group">
            <label for="birthday">Birthday *</label>
            <input type="date" id="birthday" />
          </div>
          <!-- Religion -->
          <div class="field-group">
            <label for="religion">Religion</label>
            <input type="text" id="religion" placeholder="Your religion" />
          </div>
          <!-- Sex -->
          <div class="field-group">
            <label for="sex">Sex *</label>
            <select id="sex">
              <option value="">Select sex</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="intersex">Intersex</option>
            </select>
          </div>
          <!-- Gender -->
          <div class="field-group">
            <label for="gender">Gender</label>
            <input type="text" id="gender" placeholder="Your gender identity" />
          </div>
          <!-- Marital Status -->
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
          <!-- Student Year Level -->
          <div class="field-group">
            <label for="year-level">Student Year Level</label>
            <input type="text" id="year-level" placeholder="e.g., 3rd Year, N/A" />
          </div>
          <!-- Course (full width) -->
          <div class="field-group full-width">
            <label for="course">Course</label>
            <input type="text" id="course" placeholder="Your course or program" />
          </div>
          <!-- Occupation (full width) -->
          <div class="field-group full-width">
            <label for="occupation">Occupation</label>
            <input type="text" id="occupation" placeholder="Your occupation" />
          </div>
          <!-- Chief Complaint (full width) -->
          <div class="field-group full-width">
            <label for="chief-complaint">Chief Complaint / Reason for Consultation *</label>
            <textarea id="chief-complaint" rows="3" placeholder="What brings you here today?"></textarea>
          </div>
          <!-- Primary Diagnosis (full width) -->
          <div class="field-group full-width">
            <label for="primary-diagnosis">Primary Diagnosis (if known)</label>
            <input type="text" id="primary-diagnosis" placeholder="Enter diagnosis if applicable" />
          </div>
        </div>
      </div>

      <!-- STEP 2: Medical History -->
      <div class="step-panel" id="step-2">
        <div class="step-heading">
          <h2>Medical History</h2>
          <p>Please indicate any relevant medical conditions</p>
        </div>

        <!-- A. Personal Medical History -->
        <div class="subsection">
          <h3>A. Personal Medical History</h3>
          <p class="hint">Check all that apply:</p>
          <div class="checkbox-grid">
            <label class="checkbox-row"><input type="checkbox" id="pmh-hypertension" /> Hypertension</label>
            <label class="checkbox-row"><input type="checkbox" id="pmh-stroke" /> Stroke or TIA</label>
            <label class="checkbox-row"><input type="checkbox" id="pmh-tuberculosis" /> Tuberculosis</label>
            <label class="checkbox-row"><input type="checkbox" id="pmh-thyroid" /> Thyroid Disorders</label>
            <label class="checkbox-row"><input type="checkbox" id="pmh-diabetes" /> Diabetes Mellitus</label>
            <label class="checkbox-row"><input type="checkbox" id="pmh-chronic-pain" /> Chronic Pain / Fibromyalgia</label>
            <label class="checkbox-row"><input type="checkbox" id="pmh-asthma" /> Bronchial Asthma</label>
            <label class="checkbox-row"><input type="checkbox" id="pmh-epilepsy" /> Epilepsy / Seizure Disorder</label>
          </div>
          <!-- Conditional expand checkboxes -->
          <div class="expand-checkbox-group">
            <label class="checkbox-row"><input type="checkbox" id="pmh-autoimmune" data-expands="autoimmune-expand" /> Autoimmune Disease</label>
            <div class="expand-target hidden" id="autoimmune-expand">
              <input type="text" id="pmh-autoimmune-specify" placeholder="Please specify" />
            </div>
          </div>
          <div class="expand-checkbox-group">
            <label class="checkbox-row"><input type="checkbox" id="pmh-cancer" data-expands="cancer-expand" /> Cancer</label>
            <div class="expand-target hidden" id="cancer-expand">
              <input type="text" id="pmh-cancer-specify" placeholder="Please specify type" />
            </div>
          </div>
          <div class="expand-checkbox-group">
            <label class="checkbox-row"><input type="checkbox" id="pmh-other" data-expands="pmh-other-expand" /> Other</label>
            <div class="expand-target hidden" id="pmh-other-expand">
              <input type="text" id="pmh-other-specify" placeholder="Please specify" />
            </div>
          </div>
          <!-- Medications -->
          <div class="field-group" style="margin-top: 1.5rem">
            <label for="current-medications">Medications Currently Taking</label>
            <textarea id="current-medications" rows="3" placeholder="List all current medications, including dosage"></textarea>
          </div>
        </div>

        <!-- B. Family Medical and Psychiatric History -->
        <div class="subsection border-top">
          <h3>B. Family Medical and Psychiatric History</h3>
          <p class="hint">Check all that apply to family members:</p>
          <div class="checkbox-grid">
            <label class="checkbox-row"><input type="checkbox" id="fh-hypertension" /> Hypertension</label>
            <label class="checkbox-row"><input type="checkbox" id="fh-stroke" /> Stroke</label>
            <label class="checkbox-row"><input type="checkbox" id="fh-diabetes" /> Diabetes Mellitus</label>
            <label class="checkbox-row"><input type="checkbox" id="fh-substance" /> Substance Use Disorder</label>
          </div>
          <!-- Cancer -->
          <div class="expand-checkbox-group">
            <label class="checkbox-row"><input type="checkbox" id="fh-cancer" data-expands="fh-cancer-expand" /> Cancer</label>
            <div class="expand-target hidden" id="fh-cancer-expand">
              <input type="text" id="fh-cancer-type" placeholder="Specify type" />
              <input type="text" id="fh-cancer-relation" placeholder="Relation (e.g., mother, father)" />
            </div>
          </div>
          <!-- Psychiatric -->
          <div class="expand-checkbox-group">
            <label class="checkbox-row"><input type="checkbox" id="fh-psychiatric" data-expands="fh-psychiatric-expand" /> Psychiatric Disorders (Depression/Bipolar/Schizophrenia)</label>
            <div class="expand-target hidden" id="fh-psychiatric-expand">
              <input type="text" id="fh-psychiatric-type" placeholder="Specify disorder" />
              <input type="text" id="fh-psychiatric-relation" placeholder="Relation (e.g., sibling, parent)" />
            </div>
          </div>
          <!-- Other -->
          <div class="expand-checkbox-group">
            <label class="checkbox-row"><input type="checkbox" id="fh-other" data-expands="fh-other-expand" /> Other</label>
            <div class="expand-target hidden" id="fh-other-expand">
              <input type="text" id="fh-other-specify" placeholder="Specify condition" />
              <input type="text" id="fh-other-relation" placeholder="Relation" />
            </div>
          </div>
        </div>
      </div>

      <!-- STEP 3: Psychiatric History -->
      <div class="step-panel" id="step-3">
        <div class="step-heading">
          <h2>Psychiatric History</h2>
          <p>Your responses are confidential and help us provide better care</p>
        </div>

        <!-- A. Mental Health Diagnosis -->
        <div class="subsection">
          <h3>A. Mental Health Diagnosis</h3>
          <div class="radio-question">
            <label class="question-label">Have you been diagnosed with a mental health condition?</label>
            <div class="radio-group">
              <label class="radio-row"><input type="radio" name="diagnosed-mh" value="yes" id="diagnosed-yes" /> Yes</label>
              <label class="radio-row"><input type="radio" name="diagnosed-mh" value="no" id="diagnosed-no" /> No</label>
            </div>
            <div class="expand-target hidden" id="diagnosed-expand">
              <div class="field-group">
                <label for="diagnosis-specify">Please specify the diagnosis</label>
                <input type="text" id="diagnosis-specify" placeholder="e.g., Depression, Anxiety, etc." />
              </div>
            </div>
          </div>

          <div class="radio-question" style="margin-top: 1.5rem">
            <label class="question-label">Have you been hospitalized for psychiatric reasons?</label>
            <div class="radio-group">
              <label class="radio-row"><input type="radio" name="hospitalized" value="yes" id="hospitalized-yes" /> Yes</label>
              <label class="radio-row"><input type="radio" name="hospitalized" value="no" id="hospitalized-no" /> No</label>
            </div>
            <div class="expand-target hidden" id="hospitalized-expand">
              <div class="field-grid" style="margin-top: 0.75rem">
                <div class="field-group">
                  <label for="hosp-times">Number of times</label>
                  <input type="number" id="hosp-times" placeholder="Enter number" />
                </div>
                <div class="field-group">
                  <label for="hosp-when">When (approximate date/year)</label>
                  <input type="text" id="hosp-when" placeholder="e.g., 2020, January 2023" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- B. Trauma and Abuse -->
        <div class="subsection border-top">
          <h3>B. History of Trauma and Abuse</h3>
          <p class="hint">The following questions help us understand your background. All information is confidential.</p>

          <!-- Physical Abuse -->
          <div class="trauma-card" id="trauma-physical">
            <label class="checkbox-row trauma-toggle">
              <input type="checkbox" id="trauma-physical-check" data-expands="trauma-physical-expand" />
              <span>Physical Abuse</span>
            </label>
            <div class="expand-target hidden" id="trauma-physical-expand">
              <label class="question-label">When did this occur?</label>
              <div class="checkbox-grid">
                <label class="checkbox-row sm"><input type="checkbox" id="tp-child" value="As a child" /> As a child</label>
                <label class="checkbox-row sm"><input type="checkbox" id="tp-adult" value="As an adult" /> As an adult</label>
                <label class="checkbox-row sm"><input type="checkbox" id="tp-ongoing" value="Ongoing" /> Ongoing</label>
                <label class="checkbox-row sm"><input type="checkbox" id="tp-past" value="Past experience" /> Past experience</label>
              </div>
              <div class="field-group">
                <label for="tp-details">Additional details (optional)</label>
                <textarea id="tp-details" rows="2" placeholder="You may provide additional context if comfortable"></textarea>
              </div>
            </div>
          </div>

          <!-- Emotional Abuse -->
          <div class="trauma-card" id="trauma-emotional">
            <label class="checkbox-row trauma-toggle">
              <input type="checkbox" id="trauma-emotional-check" data-expands="trauma-emotional-expand" />
              <span>Emotional Abuse</span>
            </label>
            <div class="expand-target hidden" id="trauma-emotional-expand">
              <label class="question-label">When did this occur?</label>
              <div class="checkbox-grid">
                <label class="checkbox-row sm"><input type="checkbox" id="te-child" value="As a child" /> As a child</label>
                <label class="checkbox-row sm"><input type="checkbox" id="te-adult" value="As an adult" /> As an adult</label>
                <label class="checkbox-row sm"><input type="checkbox" id="te-ongoing" value="Ongoing" /> Ongoing</label>
                <label class="checkbox-row sm"><input type="checkbox" id="te-past" value="Past experience" /> Past experience</label>
              </div>
              <div class="field-group">
                <label for="te-details">Additional details (optional)</label>
                <textarea id="te-details" rows="2" placeholder="You may provide additional context if comfortable"></textarea>
              </div>
            </div>
          </div>

          <!-- Sexual Abuse -->
          <div class="trauma-card" id="trauma-sexual">
            <label class="checkbox-row trauma-toggle">
              <input type="checkbox" id="trauma-sexual-check" data-expands="trauma-sexual-expand" />
              <span>Sexual Abuse</span>
            </label>
            <div class="expand-target hidden" id="trauma-sexual-expand">
              <label class="question-label">When did this occur?</label>
              <div class="checkbox-grid">
                <label class="checkbox-row sm"><input type="checkbox" id="ts-child" value="As a child" /> As a child</label>
                <label class="checkbox-row sm"><input type="checkbox" id="ts-adult" value="As an adult" /> As an adult</label>
                <label class="checkbox-row sm"><input type="checkbox" id="ts-ongoing" value="Ongoing" /> Ongoing</label>
                <label class="checkbox-row sm"><input type="checkbox" id="ts-past" value="Past experience" /> Past experience</label>
              </div>
              <div class="field-group">
                <label for="ts-details">Additional details (optional)</label>
                <textarea id="ts-details" rows="2" placeholder="You may provide additional context if comfortable"></textarea>
              </div>
            </div>
          </div>

          <!-- Neglect -->
          <div class="trauma-card" id="trauma-neglect">
            <label class="checkbox-row trauma-toggle">
              <input type="checkbox" id="trauma-neglect-check" data-expands="trauma-neglect-expand" />
              <span>Neglect</span>
            </label>
            <div class="expand-target hidden" id="trauma-neglect-expand">
              <label class="question-label">When did this occur?</label>
              <div class="checkbox-grid">
                <label class="checkbox-row sm"><input type="checkbox" id="tn-child" value="As a child" /> As a child</label>
                <label class="checkbox-row sm"><input type="checkbox" id="tn-adult" value="As an adult" /> As an adult</label>
                <label class="checkbox-row sm"><input type="checkbox" id="tn-ongoing" value="Ongoing" /> Ongoing</label>
                <label class="checkbox-row sm"><input type="checkbox" id="tn-past" value="Past experience" /> Past experience</label>
              </div>
              <div class="field-group">
                <label for="tn-details">Additional details (optional)</label>
                <textarea id="tn-details" rows="2" placeholder="You may provide additional context if comfortable"></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- STEP 4: Lifestyle Medicine Assessment -->
      <div class="step-panel" id="step-4">
        <div class="step-heading">
          <h2>Lifestyle Medicine Assessment</h2>
          <p>Help us understand your current lifestyle and wellness</p>
        </div>

        <!-- Overall Health Score -->
        <div class="lifestyle-card blue-gradient">
          <label class="card-label">Overall Health Score</label>
          <p class="hint">How would you rate your overall health? (0 = Poor, 10 = Excellent)</p>
          <input type="range" id="health-score" min="0" max="10" step="1" value="5" />
          <div class="slider-labels">
            <span>0 - Poor</span>
            <span id="health-score-display" class="slider-value">5</span>
            <span>10 - Excellent</span>
          </div>
        </div>

        <!-- Sleep -->
        <div class="lifestyle-card">
          <h3>Sleep</h3>
          <div class="field-grid">
            <div class="field-group">
              <label for="sleep-hours">Average hours of sleep per night</label>
              <input type="number" id="sleep-hours" placeholder="e.g., 7" />
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

        <!-- Weight & Nutrition -->
        <div class="lifestyle-card">
          <h3>Weight Management &amp; Nutrition</h3>
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
              <option value="1-2-month">1-2 times per month</option>
              <option value="1-2-week">1-2 times per week</option>
              <option value="3-4-week">3-4 times per week</option>
              <option value="daily">Daily</option>
            </select>
          </div>
          <div class="field-group">
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

        <!-- Exercise -->
        <div class="lifestyle-card">
          <h3>Exercise</h3>
          <div class="field-group">
            <label for="exercise-freq">How many days per week do you exercise?</label>
            <select id="exercise-freq">
              <option value="">Select frequency</option>
              <option value="0">0 days</option>
              <option value="1-2">1-2 days</option>
              <option value="3-4">3-4 days</option>
              <option value="5-6">5-6 days</option>
              <option value="7">7 days</option>
            </select>
          </div>
        </div>

        <!-- Mental Health / PHQ-9 -->
        <div class="lifestyle-card">
          <h3>Mental Health &amp; Well-being</h3>
          <p class="hint">Over the past 2 weeks, how often have you experienced the following?</p>

          <div class="phq-question" data-id="little-interest">
            <label class="question-label">Little interest or pleasure in doing things</label>
            <div class="radio-grid-4">
              <label class="radio-row"><input type="radio" name="phq-little-interest" value="not-at-all" /> Not at all</label>
              <label class="radio-row"><input type="radio" name="phq-little-interest" value="several-days" /> Several days</label>
              <label class="radio-row"><input type="radio" name="phq-little-interest" value="more-than-half" /> More than half</label>
              <label class="radio-row"><input type="radio" name="phq-little-interest" value="nearly-every-day" /> Nearly every day</label>
            </div>
          </div>

          <div class="phq-question" data-id="feeling-down">
            <label class="question-label">Feeling down, depressed, or hopeless</label>
            <div class="radio-grid-4">
              <label class="radio-row"><input type="radio" name="phq-feeling-down" value="not-at-all" /> Not at all</label>
              <label class="radio-row"><input type="radio" name="phq-feeling-down" value="several-days" /> Several days</label>
              <label class="radio-row"><input type="radio" name="phq-feeling-down" value="more-than-half" /> More than half</label>
              <label class="radio-row"><input type="radio" name="phq-feeling-down" value="nearly-every-day" /> Nearly every day</label>
            </div>
          </div>

          <div class="phq-question" data-id="trouble-sleeping">
            <label class="question-label">Trouble falling or staying asleep, or sleeping too much</label>
            <div class="radio-grid-4">
              <label class="radio-row"><input type="radio" name="phq-trouble-sleeping" value="not-at-all" /> Not at all</label>
              <label class="radio-row"><input type="radio" name="phq-trouble-sleeping" value="several-days" /> Several days</label>
              <label class="radio-row"><input type="radio" name="phq-trouble-sleeping" value="more-than-half" /> More than half</label>
              <label class="radio-row"><input type="radio" name="phq-trouble-sleeping" value="nearly-every-day" /> Nearly every day</label>
            </div>
          </div>

          <div class="phq-question" data-id="feeling-tired">
            <label class="question-label">Feeling tired or having little energy</label>
            <div class="radio-grid-4">
              <label class="radio-row"><input type="radio" name="phq-feeling-tired" value="not-at-all" /> Not at all</label>
              <label class="radio-row"><input type="radio" name="phq-feeling-tired" value="several-days" /> Several days</label>
              <label class="radio-row"><input type="radio" name="phq-feeling-tired" value="more-than-half" /> More than half</label>
              <label class="radio-row"><input type="radio" name="phq-feeling-tired" value="nearly-every-day" /> Nearly every day</label>
            </div>
          </div>

          <div class="phq-question" data-id="poor-appetite">
            <label class="question-label">Poor appetite or overeating</label>
            <div class="radio-grid-4">
              <label class="radio-row"><input type="radio" name="phq-poor-appetite" value="not-at-all" /> Not at all</label>
              <label class="radio-row"><input type="radio" name="phq-poor-appetite" value="several-days" /> Several days</label>
              <label class="radio-row"><input type="radio" name="phq-poor-appetite" value="more-than-half" /> More than half</label>
              <label class="radio-row"><input type="radio" name="phq-poor-appetite" value="nearly-every-day" /> Nearly every day</label>
            </div>
          </div>

          <div class="phq-question" data-id="feeling-bad">
            <label class="question-label">Feeling bad about yourself or that you are a failure</label>
            <div class="radio-grid-4">
              <label class="radio-row"><input type="radio" name="phq-feeling-bad" value="not-at-all" /> Not at all</label>
              <label class="radio-row"><input type="radio" name="phq-feeling-bad" value="several-days" /> Several days</label>
              <label class="radio-row"><input type="radio" name="phq-feeling-bad" value="more-than-half" /> More than half</label>
              <label class="radio-row"><input type="radio" name="phq-feeling-bad" value="nearly-every-day" /> Nearly every day</label>
            </div>
          </div>

          <div class="phq-question" data-id="trouble-concentrating">
            <label class="question-label">Trouble concentrating on things</label>
            <div class="radio-grid-4">
              <label class="radio-row"><input type="radio" name="phq-trouble-concentrating" value="not-at-all" /> Not at all</label>
              <label class="radio-row"><input type="radio" name="phq-trouble-concentrating" value="several-days" /> Several days</label>
              <label class="radio-row"><input type="radio" name="phq-trouble-concentrating" value="more-than-half" /> More than half</label>
              <label class="radio-row"><input type="radio" name="phq-trouble-concentrating" value="nearly-every-day" /> Nearly every day</label>
            </div>
          </div>

          <div class="phq-question" data-id="moving-slow">
            <label class="question-label">Moving or speaking slowly, or being fidgety/restless</label>
            <div class="radio-grid-4">
              <label class="radio-row"><input type="radio" name="phq-moving-slow" value="not-at-all" /> Not at all</label>
              <label class="radio-row"><input type="radio" name="phq-moving-slow" value="several-days" /> Several days</label>
              <label class="radio-row"><input type="radio" name="phq-moving-slow" value="more-than-half" /> More than half</label>
              <label class="radio-row"><input type="radio" name="phq-moving-slow" value="nearly-every-day" /> Nearly every day</label>
            </div>
          </div>

          <div class="phq-question" data-id="thoughts-hurting">
            <label class="question-label">Thoughts of hurting yourself</label>
            <div class="radio-grid-4">
              <label class="radio-row"><input type="radio" name="phq-thoughts-hurting" value="not-at-all" /> Not at all</label>
              <label class="radio-row"><input type="radio" name="phq-thoughts-hurting" value="several-days" /> Several days</label>
              <label class="radio-row"><input type="radio" name="phq-thoughts-hurting" value="more-than-half" /> More than half</label>
              <label class="radio-row"><input type="radio" name="phq-thoughts-hurting" value="nearly-every-day" /> Nearly every day</label>
            </div>
          </div>
        </div>

        <!-- Substance Use -->
        <div class="lifestyle-card">
          <h3>Substance Use and Addictive Behaviors</h3>
          <p class="hint">Please indicate if you use any of the following, and your level of concern</p>

          <!-- Nicotine -->
          <div class="substance-card">
            <label class="checkbox-row substance-toggle">
              <input type="checkbox" id="sub-nicotine" data-expands="sub-nicotine-expand" />
              <span>Nicotine (cigarettes, vaping)</span>
            </label>
            <div class="expand-target hidden" id="sub-nicotine-expand">
              <div class="field-group">
                <label for="sub-nicotine-amount">Amount per day</label>
                <input type="text" id="sub-nicotine-amount" placeholder="Enter amount" />
              </div>
              <div class="field-group">
                <label for="sub-nicotine-concern">Level of concern (0 = No concern, 5 = Very concerned)</label>
                <input type="range" class="concern-slider" id="sub-nicotine-concern" min="0" max="5" step="1" value="0" />
                <div class="slider-labels">
                  <span>0</span>
                  <span class="slider-value concern-display" data-for="sub-nicotine-concern">0</span>
                  <span>5</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Alcohol -->
          <div class="substance-card">
            <label class="checkbox-row substance-toggle">
              <input type="checkbox" id="sub-alcohol" data-expands="sub-alcohol-expand" />
              <span>Alcohol</span>
            </label>
            <div class="expand-target hidden" id="sub-alcohol-expand">
              <div class="field-group">
                <label for="sub-alcohol-amount">Drinks per week</label>
                <input type="text" id="sub-alcohol-amount" placeholder="Enter amount" />
              </div>
              <div class="field-group">
                <label for="sub-alcohol-concern">Level of concern (0 = No concern, 5 = Very concerned)</label>
                <input type="range" class="concern-slider" id="sub-alcohol-concern" min="0" max="5" step="1" value="0" />
                <div class="slider-labels">
                  <span>0</span>
                  <span class="slider-value concern-display" data-for="sub-alcohol-concern">0</span>
                  <span>5</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Recreational Drugs -->
          <div class="substance-card">
            <label class="checkbox-row substance-toggle">
              <input type="checkbox" id="sub-recreational" data-expands="sub-recreational-expand" />
              <span>Recreational Drugs</span>
            </label>
            <div class="expand-target hidden" id="sub-recreational-expand">
              <div class="field-group">
                <label for="sub-recreational-amount">Frequency</label>
                <input type="text" id="sub-recreational-amount" placeholder="Enter frequency" />
              </div>
              <div class="field-group">
                <label for="sub-recreational-concern">Level of concern (0 = No concern, 5 = Very concerned)</label>
                <input type="range" class="concern-slider" id="sub-recreational-concern" min="0" max="5" step="1" value="0" />
                <div class="slider-labels">
                  <span>0</span>
                  <span class="slider-value concern-display" data-for="sub-recreational-concern">0</span>
                  <span>5</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Marijuana -->
          <div class="substance-card">
            <label class="checkbox-row substance-toggle">
              <input type="checkbox" id="sub-marijuana" data-expands="sub-marijuana-expand" />
              <span>Marijuana</span>
            </label>
            <div class="expand-target hidden" id="sub-marijuana-expand">
              <div class="field-group">
                <label for="sub-marijuana-amount">Frequency</label>
                <input type="text" id="sub-marijuana-amount" placeholder="Enter frequency" />
              </div>
              <div class="field-group">
                <label for="sub-marijuana-concern">Level of concern (0 = No concern, 5 = Very concerned)</label>
                <input type="range" class="concern-slider" id="sub-marijuana-concern" min="0" max="5" step="1" value="0" />
                <div class="slider-labels">
                  <span>0</span>
                  <span class="slider-value concern-display" data-for="sub-marijuana-concern">0</span>
                  <span>5</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Social Media / Screen Time -->
          <div class="substance-card">
            <label class="checkbox-row substance-toggle">
              <input type="checkbox" id="sub-screentime" data-expands="sub-screentime-expand" />
              <span>Social Media / Screen Time</span>
            </label>
            <div class="expand-target hidden" id="sub-screentime-expand">
              <div class="field-group">
                <label for="sub-screentime-amount">Hours per day</label>
                <input type="text" id="sub-screentime-amount" placeholder="Enter hours" />
              </div>
              <div class="field-group">
                <label for="sub-screentime-concern">Level of concern (0 = No concern, 5 = Very concerned)</label>
                <input type="range" class="concern-slider" id="sub-screentime-concern" min="0" max="5" step="1" value="0" />
                <div class="slider-labels">
                  <span>0</span>
                  <span class="slider-value concern-display" data-for="sub-screentime-concern">0</span>
                  <span>5</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Gambling -->
          <div class="substance-card">
            <label class="checkbox-row substance-toggle">
              <input type="checkbox" id="sub-gambling" data-expands="sub-gambling-expand" />
              <span>Gambling</span>
            </label>
            <div class="expand-target hidden" id="sub-gambling-expand">
              <div class="field-group">
                <label for="sub-gambling-amount">Frequency</label>
                <input type="text" id="sub-gambling-amount" placeholder="Enter frequency" />
              </div>
              <div class="field-group">
                <label for="sub-gambling-concern">Level of concern (0 = No concern, 5 = Very concerned)</label>
                <input type="range" class="concern-slider" id="sub-gambling-concern" min="0" max="5" step="1" value="0" />
                <div class="slider-labels">
                  <span>0</span>
                  <span class="slider-value concern-display" data-for="sub-gambling-concern">0</span>
                  <span>5</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Others -->
          <div class="substance-card">
            <label class="checkbox-row substance-toggle">
              <input type="checkbox" id="sub-others" data-expands="sub-others-expand" />
              <span>Others</span>
            </label>
            <div class="expand-target hidden" id="sub-others-expand">
              <div class="field-group">
                <label for="sub-others-specify">Please specify</label>
                <input type="text" id="sub-others-specify" placeholder="Please specify" />
              </div>
              <div class="field-group">
                <label for="sub-others-concern">Level of concern (0 = No concern, 5 = Very concerned)</label>
                <input type="range" class="concern-slider" id="sub-others-concern" min="0" max="5" step="1" value="0" />
                <div class="slider-labels">
                  <span>0</span>
                  <span class="slider-value concern-display" data-for="sub-others-concern">0</span>
                  <span>5</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Motivation -->
        <div class="lifestyle-card green-gradient">
          <h3>Motivation for Change</h3>
          <div class="field-group">
            <label for="lifestyle-motivation">What are your top 3 lifestyle areas you're motivated to change?</label>
            <p class="hint">Rank from 1 (most important) to 3</p>
            <textarea id="lifestyle-motivation" rows="3" placeholder="1. Sleep better&#10;2. Exercise more&#10;3. Reduce stress"></textarea>
          </div>
          <div class="field-group">
            <label for="motivation-level">How motivated are you to be healthier?</label>
            <select id="motivation-level">
              <option value="">Select motivation level</option>
              <option value="not-motivated">Not motivated</option>
              <option value="somewhat">Somewhat motivated</option>
              <option value="motivated">Motivated</option>
              <option value="very-motivated">Very motivated</option>
            </select>
          </div>
        </div>
      </div>

      <!-- STEP 5: Review & Submit -->
      <div class="step-panel" id="step-5">
        <div class="step-heading center">
          <h2>Review Your Information</h2>
          <p>Please review your responses before submitting</p>
        </div>

        <!-- Almost Done notice -->
        <div class="notice-card blue">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1e40af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
          <div>
            <strong>Almost Done!</strong>
            <p>Review each section below. You can edit any section by clicking the "Edit" button.</p>
          </div>
        </div>

        <!-- Summary Card 1: Patient Information -->
        <div class="summary-card">
          <div class="summary-header">
            <h3>Patient Information</h3>
            <button class="btn-outline-sm" onclick="goToStep(1)">✏ Edit</button>
          </div>
          <div class="summary-rows" id="summary-patient"></div>
        </div>

        <!-- Summary Card 2: Medical History -->
        <div class="summary-card">
          <div class="summary-header">
            <h3>Medical History</h3>
            <button class="btn-outline-sm" onclick="goToStep(2)">✏ Edit</button>
          </div>
          <div class="summary-rows" id="summary-medical"></div>
        </div>

        <!-- Summary Card 3: Psychiatric History -->
        <div class="summary-card">
          <div class="summary-header">
            <h3>Psychiatric History</h3>
            <button class="btn-outline-sm" onclick="goToStep(3)">✏ Edit</button>
          </div>
          <div class="summary-rows" id="summary-psychiatric"></div>
        </div>

        <!-- Summary Card 4: Lifestyle Assessment -->
        <div class="summary-card">
          <div class="summary-header">
            <h3>Lifestyle Assessment</h3>
            <button class="btn-outline-sm" onclick="goToStep(4)">✏ Edit</button>
          </div>
          <div class="summary-rows" id="summary-lifestyle"></div>
        </div>

        <!-- Privacy Notice -->
        <div class="privacy-card">
          <h4>Privacy &amp; Confidentiality</h4>
          <p>All information provided is confidential and protected under medical privacy laws (HIPAA). Your data will only be accessed by authorized healthcare professionals involved in your care.</p>
        </div>
      </div>

      <!-- Navigation -->
      <div class="form-nav">
        <button class="btn-outline btn-lg hidden" id="btn-back" onclick="prevStep()">← Back</button>
        <button class="btn-primary btn-lg" id="btn-next" onclick="nextStep()">Next →</button>
        <button class="btn-success btn-lg hidden" id="btn-submit" onclick="submitForm()">✔ Submit</button>
      </div>
    </div>

    <!-- Privacy footer -->
    <p class="form-footer">Your information is confidential and protected under medical privacy laws.</p>
  </div>

  <script src="{{ asset('js/intake_form.js') }}"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MedCare — Psychiatrist Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/psychiatrist.css') }}" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<body>

  <div class="app-shell">

    <!-- ===================== SIDEBAR ===================== -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-brand-block">
        <div class="sidebar-brand">MedCare System</div>
        <div class="sidebar-role">Psychiatrist</div>
      </div>

      <nav class="sidebar-nav">
        <button class="nav-item active" data-section="dashboard">
          <i data-feather="grid"></i><span>Dashboard</span>
        </button>
        <button class="nav-item" data-section="patients">
          <i data-feather="users"></i><span>Patients</span>
        </button>
        <button class="nav-item" data-section="consultations">
          <i data-feather="calendar"></i><span>Consultations</span>
        </button>
        <button class="nav-item" data-section="records">
          <i data-feather="file-text"></i><span>Medical Records</span>
        </button>
        <button class="nav-item" data-section="lifestyle">
          <i data-feather="trending-up"></i><span>Lifestyle Monitoring</span>
        </button>
        <button class="nav-item" data-section="assessments">
          <i data-feather="clipboard"></i><span>Assessments</span>
        </button>
        <button class="nav-item" data-section="prescriptions">
          <i data-feather="tag"></i><span>Prescriptions</span>
        </button>
      </nav>

      <div class="sidebar-footer">
        <button class="nav-item" id="profile-btn">
          <i data-feather="user"></i><span>Profile</span>
        </button>
        <button class="nav-item logout-item" id="logout-btn">
          <i data-feather="log-out"></i><span>Logout</span>
        </button>
      </div>
    </aside>

    <!-- ===================== MAIN ===================== -->
    <div class="main-area">

      <!-- TOP BAR -->
      <header class="main-topbar">
        <div class="topbar-left">
          <button class="hamburger-btn" id="hamburger-btn"><i data-feather="menu"></i></button>
          <h1 class="page-title" id="page-title">Dashboard</h1>
        </div>
        <div class="topbar-right">
          <span class="topbar-date">Sunday, June 7, 2026</span>
        </div>
      </header>

      <div class="content-wrap">

        <!-- ================================================
           SECTION 1: DASHBOARD
           ================================================ -->
        <section class="psych-section active" id="section-dashboard">

          <!-- Stats -->
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-text">
                <div class="stat-label">Total Patients Today</div>
                <div class="stat-value">4</div>
                <div class="stat-trend trend-up">From kiosk intake</div>
              </div>
              <div class="stat-icon si-blue"><i data-feather="users"></i></div>
            </div>
            <div class="stat-card">
              <div class="stat-text">
                <div class="stat-label">Pending Consultations</div>
                <div class="stat-value">2</div>
              </div>
              <div class="stat-icon si-amber"><i data-feather="calendar"></i></div>
            </div>
            <div class="stat-card">
              <div class="stat-text">
                <div class="stat-label">Completed Consultations</div>
                <div class="stat-value">2</div>
              </div>
              <div class="stat-icon si-green"><i data-feather="activity"></i></div>
            </div>
            <div class="stat-card">
              <div class="stat-text">
                <div class="stat-label">High-Risk Patients</div>
                <div class="stat-value">1</div>
                <div class="stat-trend trend-down">Requires attention</div>
              </div>
              <div class="stat-icon si-red"><i data-feather="alert-triangle"></i></div>
            </div>
          </div>

          <!-- Two-col -->
          <div class="two-col-grid">
            <!-- Pending Consultations -->
            <div class="card">
              <div class="card-header">
                <span class="card-title">Pending Consultations</span>
                <button class="btn-ghost" data-goto="consultations">View All</button>
              </div>
              <div class="consult-list">
                <div class="consult-row">
                  <div class="consult-info">
                    <div class="consult-top">
                      <span class="consult-name">Sarah Johnson</span>
                      <span class="badge badge-outline">New</span>
                    </div>
                    <div class="consult-time">9:00 AM</div>
                    <div class="consult-complaint">Chief Complaint: Sleep disturbances, anxiety</div>
                  </div>
                  <button class="btn-blue-sm" data-goto="consultations">Review</button>
                </div>
                <div class="consult-row">
                  <div class="consult-info">
                    <div class="consult-top">
                      <span class="consult-name">Robert Martinez</span>
                      <span class="badge badge-outline">New</span>
                    </div>
                    <div class="consult-time">10:30 AM</div>
                    <div class="consult-complaint">Chief Complaint: Depression, suicidal ideation</div>
                  </div>
                  <button class="btn-blue-sm" data-goto="consultations">Review</button>
                </div>
              </div>
            </div>

            <!-- High-Risk Patients -->
            <div class="card">
              <div class="card-header">
                <span class="card-title">High-Risk Patients</span>
                <button class="btn-ghost" data-goto="patients">View All</button>
              </div>
              <div class="highrisk-list">
                <div class="highrisk-row">
                  <div class="highrisk-info">
                    <div class="highrisk-top">
                      <span class="consult-name">Robert Martinez</span>
                      <span class="badge badge-critical">High Risk</span>
                    </div>
                    <div class="highrisk-meta">Age: 45 | Sex: Male</div>
                    <div class="consult-complaint">Chief Complaint: Depression, suicidal ideation</div>
                  </div>
                  <button class="btn-red-sm">Urgent</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Today's Intakes Table -->
          <div class="card">
            <div class="card-header">
              <span class="card-title">Today's Patient Intakes</span>
            </div>
            <div class="table-wrap">
              <table class="data-table" id="intakes-table">
                <thead>
                  <tr>
                    <th>Patient</th>
                    <th>Age/Sex</th>
                    <th>Chief Complaint</th>
                    <th>Intake Time</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="intakes-tbody">
                  <!-- Filled by JS -->
                </tbody>
              </table>
            </div>
          </div>

        </section><!-- /dashboard -->


        <!-- ================================================
           SECTION 2: PATIENTS
           ================================================ -->
        <section class="psych-section" id="section-patients">
          <div class="card">
            <div class="card-header">
              <span class="card-title">All Patients</span>
              <button class="btn-blue" id="add-patient-btn">
                <i data-feather="plus"></i> Add Patient
              </button>
            </div>
            <div class="filter-row">
              <div class="search-wrap">
                <i data-feather="search" class="search-icon"></i>
                <input type="text" id="patient-search" class="search-input"
                  placeholder="Search by name or patient ID..." />
              </div>
              <select id="patient-status-filter" class="filter-select">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Critical">Critical</option>
              </select>
            </div>
            <div class="table-wrap">
              <table class="data-table" id="patients-table">
                <thead>
                  <tr>
                    <th>Patient ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Status</th>
                    <th>Assigned Life Coach</th>
                    <th>Chief Complaint</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="patients-tbody">
                  <!-- Filled by JS -->
                </tbody>
              </table>
            </div>
          </div>
        </section><!-- /patients -->


        <!-- ================================================
           SECTION 3: CONSULTATIONS
           ================================================ -->
        <section class="psych-section" id="section-consultations">
          <div class="card">
            <div class="card-header">
              <span class="card-title">All Consultations</span>
              <button class="btn-blue" id="schedule-consult-btn">
                <i data-feather="plus"></i> Schedule Consultation
              </button>
            </div>
            <div class="table-wrap">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="consults-tbody">
                  <!-- Filled by JS -->
                </tbody>
              </table>
            </div>
          </div>
        </section><!-- /consultations -->


        <!-- ================================================
           SECTION 4: MEDICAL RECORDS
           ================================================ -->
        <section class="psych-section" id="section-records">
          <div class="section-heading">Medical Records</div>
          <div class="records-grid" id="records-grid">
            <!-- Filled by JS -->
          </div>
        </section><!-- /records -->


        <!-- ================================================
           SECTION 5: LIFESTYLE MONITORING
           ================================================ -->
        <section class="psych-section" id="section-lifestyle">
          <div class="section-heading">Patient Lifestyle Data</div>
          <div class="lifestyle-grid" id="lifestyle-grid">
            <!-- Filled by JS -->
          </div>
        </section><!-- /lifestyle -->


        <!-- ================================================
           SECTION 6: ASSESSMENTS
           ================================================ -->
        <section class="psych-section" id="section-assessments">

          <!-- Assessment List View -->
          <div id="assessment-list-view">
            <div class="filter-row" style="margin-bottom:16px;">
              <div class="search-wrap">
                <i data-feather="search" class="search-icon"></i>
                <input type="text" id="assess-search" class="search-input"
                  placeholder="Search by name or patient ID..." />
              </div>
              <select id="assess-status-filter" class="filter-select">
                <option value="">All Status</option>
                <option value="Stable">Stable</option>
                <option value="Monitoring">Monitoring</option>
                <option value="Critical">Critical</option>
                <option value="Maintenance">Maintenance</option>
              </select>
            </div>
            <div class="assessment-cards-grid" id="assessment-cards-grid">
              <!-- Filled by JS -->
            </div>
          </div>

          <!-- Assessment Detail View (hidden until card clicked) -->
          <div id="assessment-detail-view" class="hidden">
            <div class="assess-detail-header">
              <button class="btn-ghost-back" id="assess-back-btn">
                <i data-feather="arrow-left"></i> Back to List
              </button>
              <div class="assess-detail-patient-info">
                <span class="assess-detail-name" id="assess-detail-name">Sarah Johnson</span>
                <span class="assess-detail-id" id="assess-detail-id">P001</span>
              </div>
            </div>

            <!-- 6-Tab System -->
            <div class="tab-bar" id="assess-tab-bar">
              <button class="tab-btn active" data-tab="biological">Biological</button>
              <button class="tab-btn" data-tab="psychological">Psychological</button>
              <button class="tab-btn" data-tab="social">Social</button>
              <button class="tab-btn" data-tab="spiritual">Spiritual</button>
              <button class="tab-btn" data-tab="prayer">Prayer Points</button>
              <button class="tab-btn" data-tab="intervention">Intervention</button>
            </div>

            <!-- TAB: BIOLOGICAL -->
            <div class="tab-panel active" id="tab-biological">
              <div class="card" style="margin-bottom:16px;">
                <div class="card-header"><span class="card-title">Vitals</span></div>
                <div class="vitals-grid">
                  <div class="vital-box">
                    <div class="vital-label">Blood Pressure</div><input class="vital-input" id="v-bp"
                      value="120/80 mmHg" />
                  </div>
                  <div class="vital-box">
                    <div class="vital-label">Heart Rate</div><input class="vital-input" id="v-hr" value="74 bpm" />
                  </div>
                  <div class="vital-box">
                    <div class="vital-label">Temperature</div><input class="vital-input" id="v-temp" value="36.6 °C" />
                  </div>
                  <div class="vital-box">
                    <div class="vital-label">Weight</div><input class="vital-input" id="v-weight" value="58 kg" />
                  </div>
                  <div class="vital-box">
                    <div class="vital-label">Height</div><input class="vital-input" id="v-height" value="162 cm" />
                  </div>
                  <div class="vital-box">
                    <div class="vital-label">BMI</div><input class="vital-input" id="v-bmi" value="22.1" />
                  </div>
                </div>
              </div>
              <div class="card">
                <div class="card-header"><span class="card-title">History &amp; Examination</span></div>
                <div class="assess-fields">
                  <div class="assess-field-group"><label>Chief Complaint</label><textarea
                      id="bio-cc">Patient reports persistent difficulty sleeping for 3 months, waking at 2–3 AM with racing thoughts and anxiety.</textarea>
                  </div>
                  <div class="assess-field-group"><label>History of Present Illness</label><textarea
                      id="bio-hpi">Gradual onset insomnia correlated with work-related stressors. Patient denies substance use. No prior psychiatric treatment.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Past Psychiatric History</label><textarea
                      id="bio-pph">No prior hospitalizations. No previous psychiatric diagnoses.</textarea></div>
                  <div class="assess-field-group"><label>Past Medical History</label><textarea
                      id="bio-pmh">No significant medical conditions. No surgeries. No chronic medications.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Family History</label><textarea
                      id="bio-fh">Mother with generalized anxiety disorder. No family history of psychosis or suicide.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Social History</label><textarea
                      id="bio-sh">Non-smoker, occasional alcohol (1–2 drinks/week). Works as marketing manager. Single, lives alone.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Review of Systems</label><textarea
                      id="bio-ros">Positive for insomnia, fatigue, and mild headaches. Negative for chest pain, palpitations, and GI complaints.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Physical Examination</label><textarea
                      id="bio-pe">Alert and oriented ×3. Thyroid non-enlarged. Cardiovascular and respiratory exam unremarkable.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Lab Results</label><textarea
                      id="bio-lab">TSH: 2.1 mIU/L (normal). CBC: within normal limits. Fasting glucose: 92 mg/dL.</textarea>
                  </div>
                </div>
                <div class="assess-save-row">
                  <button class="btn-blue" onclick="showToast('Biological data saved.')">Save Changes</button>
                </div>
              </div>
            </div>

            <!-- TAB: PSYCHOLOGICAL -->
            <div class="tab-panel" id="tab-psychological">
              <div class="card">
                <div class="card-header"><span class="card-title">Mental Status Examination</span></div>
                <div class="assess-fields">
                  <div class="assess-field-group">
                    <label>Appearance</label><textarea>Well-groomed, age-appropriate attire. No psychomotor agitation or retardation.</textarea>
                  </div>
                  <div class="assess-field-group">
                    <label>Behavior</label><textarea>Cooperative and engaged. Good eye contact. No unusual behaviors noted.</textarea>
                  </div>
                  <div class="assess-field-group">
                    <label>Speech</label><textarea>Normal rate, rhythm, and volume. Articulate and coherent.</textarea>
                  </div>
                  <div class="assess-field-group">
                    <label>Mood</label><textarea>"Anxious and tired" — patient's own words.</textarea></div>
                  <div class="assess-field-group">
                    <label>Affect</label><textarea>Congruent with mood. Mildly restricted range.</textarea></div>
                  <div class="assess-field-group"><label>Thought
                      Process</label><textarea>Linear and goal-directed. No loosening of associations.</textarea></div>
                  <div class="assess-field-group"><label>Thought
                      Content</label><textarea>Preoccupied with work stressors. No paranoid ideation. No obsessions or compulsions.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Perceptual
                      Disturbances</label><textarea>No hallucinations or illusions reported.</textarea></div>
                  <div class="assess-field-group">
                    <label>Cognition</label><textarea>Oriented to person, place, and time. Immediate and recent memory intact.</textarea>
                  </div>
                  <div class="assess-field-group">
                    <label>Insight</label><textarea>Good insight into illness. Acknowledges need for treatment.</textarea>
                  </div>
                  <div class="assess-field-group">
                    <label>Judgment</label><textarea>Intact. Patient able to make reasonable decisions regarding daily activities.</textarea>
                  </div>
                </div>
                <div class="card-header" style="margin-top:16px;"><span class="card-title">Risk Assessment</span></div>
                <div class="assess-fields">
                  <div class="assess-field-group">
                    <label>Suicidal Ideation</label>
                    <div class="radio-group">
                      <label class="radio-label"><input type="radio" name="si" value="none" checked /> None</label>
                      <label class="radio-label"><input type="radio" name="si" value="passive" /> Passive</label>
                      <label class="radio-label"><input type="radio" name="si" value="active" /> Active</label>
                    </div>
                  </div>
                  <div class="assess-field-group"><label>Risk
                      Assessment</label><textarea>Low overall risk. Protective factors: strong social support, no prior attempts, good insight.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Safety
                      Plan</label><textarea>Patient instructed to contact emergency line if ideation develops. Crisis number provided. Next appointment in 2 weeks.</textarea>
                  </div>
                </div>
                <div class="assess-save-row">
                  <button class="btn-blue" onclick="showToast('Psychological data saved.')">Save Changes</button>
                </div>
              </div>
            </div>

            <!-- TAB: SOCIAL -->
            <div class="tab-panel" id="tab-social">
              <div class="card">
                <div class="card-header"><span class="card-title">Social Assessment</span></div>
                <div class="assess-fields">
                  <div class="assess-field-group"><label>Living
                      Situation</label><textarea>Lives alone in a 1-bedroom apartment. Has supportive family nearby.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Occupation /
                      Employment</label><textarea>Marketing Manager at a mid-sized firm. Reports high workload and tight deadlines.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Financial
                      Status</label><textarea>Stable income. No reported financial stressors.</textarea></div>
                  <div class="assess-field-group"><label>Relationships / Support
                      System</label><textarea>Close relationship with parents and one sibling. Small but reliable social circle.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Cultural
                      Background</label><textarea>Filipino-American. Identifies cultural expectations around work and family as stressors.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Legal
                      Issues</label><textarea>No legal issues reported.</textarea></div>
                  <div class="assess-field-group"><label>Substance
                      Use</label><textarea>Occasional alcohol (1–2 drinks per week social). No illicit substance use.</textarea>
                  </div>
                  <div class="assess-field-group">
                    <label>Stressors</label><textarea>Primary stressor: occupational. Secondary: relationship loneliness. No acute trauma.</textarea>
                  </div>
                </div>
                <div class="assess-save-row">
                  <button class="btn-blue" onclick="showToast('Social data saved.')">Save Changes</button>
                </div>
              </div>
            </div>

            <!-- TAB: SPIRITUAL -->
            <div class="tab-panel" id="tab-spiritual">
              <div class="card">
                <div class="card-header"><span class="card-title">Spiritual Assessment</span></div>
                <div class="assess-fields">
                  <div class="assess-field-group"><label>Religious / Spiritual
                      Beliefs</label><textarea>Catholic background. Identifies as moderately religious. Finds comfort in prayer.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Practices &amp;
                      Rituals</label><textarea>Attends Sunday Mass occasionally. Personal prayer before sleep.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Spiritual
                      Coping</label><textarea>Uses prayer and reflection as primary coping strategies during stress.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Spiritual
                      Needs</label><textarea>Desires more consistent spiritual community and connection.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Spiritual
                      Strengths</label><textarea>Faith provides sense of meaning and resilience. Believes in healing and recovery.</textarea>
                  </div>
                  <div class="assess-field-group"><label>Meaning &amp;
                      Purpose</label><textarea>Finds purpose in her work and family relationships. Aspires to help others in the future.</textarea>
                  </div>
                </div>
                <div class="assess-save-row">
                  <button class="btn-blue" onclick="showToast('Spiritual data saved.')">Save Changes</button>
                </div>
              </div>
            </div>

            <!-- TAB: PRAYER POINTS -->
            <div class="tab-panel" id="tab-prayer">
              <div class="card">
                <div class="card-header"><span class="card-title">Prayer Points</span></div>
                <div class="prayer-input-row">
                  <input type="text" id="prayer-input" class="field-input" placeholder="Add a prayer point..." />
                  <button class="btn-purple" id="add-prayer-btn">Add Prayer Point</button>
                </div>
                <div class="prayer-list" id="prayer-list">
                  <div class="prayer-entry">
                    <div class="prayer-meta">Jun 5, 2026 · Dr. Maria Santos</div>
                    <div class="prayer-text">Pray for healing of sleep disturbances and restoration of peace in the
                      patient's mind.</div>
                  </div>
                  <div class="prayer-entry">
                    <div class="prayer-meta">Jun 1, 2026 · Dr. Maria Santos</div>
                    <div class="prayer-text">Pray for strength and grace through work-related stressors and anxious
                      thoughts.</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- TAB: INTERVENTION -->
            <div class="tab-panel" id="tab-intervention">
              <div class="card">
                <div class="card-header"><span class="card-title">Treatment Plan &amp; Interventions</span></div>
                <div class="assess-fields">
                  <div class="assess-field-group"><label>Treatment Plan</label><textarea
                      id="intervention-plan">Combined pharmacotherapy (Sertraline 50mg QD) and psychotherapy (CBT). Lifestyle modifications including sleep hygiene protocol and exercise routine. Monthly follow-up.</textarea>
                  </div>
                  <div class="assess-field-group">
                    <label>Add Intervention Entry</label>
                    <div class="intervention-add-row">
                      <input type="text" id="intervention-input" class="field-input"
                        placeholder="Describe intervention..." />
                      <button class="btn-blue" id="add-intervention-btn">Add Entry</button>
                    </div>
                  </div>
                </div>
                <div class="intervention-timeline" id="intervention-timeline">
                  <div class="timeline-entry">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                      <div class="timeline-date">Jun 5, 2026</div>
                      <div class="timeline-text">Initiated Sertraline 50mg QD. Psychoeducation provided on anxiety and
                        sleep hygiene.</div>
                    </div>
                  </div>
                  <div class="timeline-entry">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                      <div class="timeline-date">May 20, 2026</div>
                      <div class="timeline-text">Initial psychiatric evaluation completed. Diagnosis: Anxiety Disorder
                        with Insomnia. Referral to life coach for lifestyle coaching.</div>
                    </div>
                  </div>
                </div>
                <div class="assess-save-row">
                  <button class="btn-blue" onclick="showToast('Intervention plan saved.')">Save Plan</button>
                </div>
              </div>

              <!-- Medical History Accordion -->
              <div class="card" style="margin-top:16px;">
                <div class="card-header">
                  <span class="card-title">Medical History</span>
                  <button class="btn-ghost" id="medhist-toggle-all">Expand All</button>
                </div>
                <div class="accordion-list" id="medhist-accordion">
                  <div class="accordion-item">
                    <button class="accordion-trigger">Allergies <i data-feather="chevron-down"></i></button>
                    <div class="accordion-panel">
                      <textarea>NKDA (No Known Drug Allergies). No known food allergies.</textarea></div>
                  </div>
                  <div class="accordion-item">
                    <button class="accordion-trigger">Current Medications <i data-feather="chevron-down"></i></button>
                    <div class="accordion-panel">
                      <textarea>Sertraline 50mg QD (started Jun 2026). Melatonin 3mg PRN bedtime.</textarea></div>
                  </div>
                  <div class="accordion-item">
                    <button class="accordion-trigger">Surgical History <i data-feather="chevron-down"></i></button>
                    <div class="accordion-panel"><textarea>No prior surgeries.</textarea></div>
                  </div>
                  <div class="accordion-item">
                    <button class="accordion-trigger">Hospitalizations <i data-feather="chevron-down"></i></button>
                    <div class="accordion-panel"><textarea>No prior hospitalizations.</textarea></div>
                  </div>
                  <div class="accordion-item">
                    <button class="accordion-trigger">Immunizations <i data-feather="chevron-down"></i></button>
                    <div class="accordion-panel">
                      <textarea>Up to date. COVID-19 vaccinated (3 doses). Flu vaccine annually.</textarea></div>
                  </div>
                  <div class="accordion-item">
                    <button class="accordion-trigger">Family Medical History <i
                        data-feather="chevron-down"></i></button>
                    <div class="accordion-panel">
                      <textarea>Mother: GAD. Father: Hypertension, Type 2 Diabetes. No family history of malignancy.</textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /assessment-detail-view -->
        </section><!-- /assessments -->


        <!-- ================================================
           SECTION 7: PRESCRIPTIONS
           ================================================ -->
        <section class="psych-section" id="section-prescriptions">

          <!-- Sub-tabs -->
          <div class="subtab-bar">
            <button class="subtab-btn active" data-subtab="rx">
              <i data-feather="tag"></i> Medication Prescription (Rx)
            </button>
            <button class="subtab-btn" data-subtab="diagnostic">
              <i data-feather="activity"></i> Diagnostic Request
            </button>
          </div>

          <!-- SUB-TAB: MEDICATION RX -->
          <div class="subtab-panel active" id="subtab-rx">
            <div class="rx-layout">

              <!-- Left: Form -->
              <div class="rx-form-col">
                <div class="card">
                  <!-- Doctor Info -->
                  <div class="rx-doctor-block">
                    <div class="rx-stamp">Rx</div>
                    <div class="rx-doctor-info">
                      <div class="rx-doctor-name">Dr. Maria Santos, MD</div>
                      <div class="rx-doctor-role">Psychiatrist · MedCare Clinic</div>
                      <div class="rx-doctor-lic">Lic #: 0012345</div>
                    </div>
                  </div>

                  <!-- Patient & Date -->
                  <div class="rx-patient-row">
                    <div class="field-group">
                      <label class="field-label">Patient Name</label>
                      <select class="field-input" id="rx-patient">
                        <option value="">Select patient...</option>
                        <option value="Sarah Johnson (P001)">Sarah Johnson (P001)</option>
                        <option value="Robert Martinez (P002)">Robert Martinez (P002)</option>
                        <option value="Emily Thompson (P003)">Emily Thompson (P003)</option>
                        <option value="David Lee (P004)">David Lee (P004)</option>
                        <option value="Maria Garcia (P005)">Maria Garcia (P005)</option>
                      </select>
                    </div>
                    <div class="field-group">
                      <label class="field-label">Age</label>
                      <input type="number" class="field-input" id="rx-age" placeholder="Age" />
                    </div>
                    <div class="field-group">
                      <label class="field-label">Date</label>
                      <input type="text" class="field-input" id="rx-date" readonly />
                    </div>
                  </div>

                  <!-- Diagnosis -->
                  <div class="field-group" style="margin-bottom:12px;">
                    <label class="field-label">Diagnosis</label>
                    <div class="typeahead-wrap">
                      <input type="text" class="field-input" id="rx-diagnosis" placeholder="Type or select diagnosis..."
                        autocomplete="off" />
                      <div class="typeahead-dropdown hidden" id="rx-diag-dropdown"></div>
                    </div>
                  </div>

                  <!-- Medications -->
                  <div class="rx-meds-header">
                    <span class="field-label">Medications</span>
                  </div>
                  <div class="rx-meds-table-header">
                    <span>MEDICATION</span><span>DOSAGE</span><span>FREQUENCY /
                      TIMING</span><span>QTY</span><span></span>
                  </div>
                  <div id="rx-meds-list">
                    <!-- Med rows injected by JS -->
                  </div>
                  <button class="btn-outline-add" id="add-med-btn">
                    <i data-feather="plus"></i> Add Medication
                  </button>

                  <!-- Special Instructions -->
                  <div class="field-group" style="margin-top:12px;">
                    <label class="field-label">Notes / Instructions</label>
                    <textarea class="field-textarea" id="rx-notes" rows="3"
                      placeholder="Additional notes, special instructions, follow-up schedule..."></textarea>
                  </div>

                  <button class="btn-generate" id="generate-rx-btn">
                    <i data-feather="file-text"></i> Generate Prescription
                  </button>
                </div>
              </div>

              <!-- Right: Preview + Templates -->
              <div class="rx-right-col">
                <!-- Rx Preview -->
                <div class="card rx-preview-card" id="rx-preview-card">
                  <div id="print-area-rx">
                    <div class="rx-preview-clinic">MedCare Integrated Psychiatric &amp; Lifestyle Medicine Clinic</div>
                    <div class="rx-preview-addr">123 Wellness Ave, Quezon City · +63-2-8888-9999</div>
                    <div class="rx-preview-stamp">Rx</div>
                    <div class="rx-preview-patient-row">
                      <span>Patient: <strong id="preview-patient">—</strong></span>
                      <span>Age: <strong id="preview-age">—</strong></span>
                      <span>Date: <strong id="preview-date">—</strong></span>
                    </div>
                    <div class="rx-preview-diag">Diagnosis: <strong id="preview-diag">—</strong></div>
                    <div class="rx-preview-meds-label">Medications:</div>
                    <ol id="preview-meds-list" class="rx-preview-meds-list"></ol>
                    <div class="rx-preview-notes-label">Instructions:</div>
                    <div id="preview-notes" class="rx-preview-notes-text">—</div>
                    <div class="rx-preview-sig-line">
                      <div class="rx-sig-line-bar"></div>
                      <div class="rx-sig-name">Dr. Maria Santos, MD</div>
                      <div class="rx-sig-lic">License No. 0012345</div>
                    </div>
                  </div>
                  <button class="btn-print" onclick="printRx()">
                    <i data-feather="printer"></i> Print Rx
                  </button>
                </div>

                <!-- Template Library -->
                <div class="card rx-templates-card">
                  <div class="card-header">
                    <span class="card-title"><i data-feather="star"
                        style="width:14px;height:14px;vertical-align:middle;color:#f59e0b;"></i> Rx Template
                      Library</span>
                  </div>
                  <div class="template-list" id="rx-template-list">
                    <!-- Filled by JS -->
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- SUB-TAB: DIAGNOSTIC REQUEST -->
          <div class="subtab-panel" id="subtab-diagnostic">
            <div class="rx-layout">
              <div class="rx-form-col">
                <div class="card">
                  <div class="rx-doctor-block">
                    <div class="rx-stamp">Dx</div>
                    <div class="rx-doctor-info">
                      <div class="rx-doctor-name">Dr. Maria Santos, MD</div>
                      <div class="rx-doctor-role">Psychiatrist · MedCare Clinic</div>
                      <div class="rx-doctor-lic">Lic #: 0012345</div>
                    </div>
                  </div>

                  <div class="rx-patient-row">
                    <div class="field-group">
                      <label class="field-label">Patient Name</label>
                      <select class="field-input" id="dx-patient">
                        <option value="">Select patient...</option>
                        <option>Sarah Johnson (P001)</option>
                        <option>Robert Martinez (P002)</option>
                        <option>Emily Thompson (P003)</option>
                        <option>David Lee (P004)</option>
                        <option>Maria Garcia (P005)</option>
                      </select>
                    </div>
                    <div class="field-group">
                      <label class="field-label">Age</label>
                      <input type="number" class="field-input" id="dx-age" placeholder="Age" />
                    </div>
                    <div class="field-group">
                      <label class="field-label">Date</label>
                      <input type="text" class="field-input" id="dx-date" readonly />
                    </div>
                  </div>

                  <div class="field-group" style="margin-bottom:16px;">
                    <label class="field-label">Clinical Notes / Reason for Request</label>
                    <textarea class="field-textarea" id="dx-notes" rows="3"
                      placeholder="Clinical notes and reason for diagnostic request..."></textarea>
                  </div>

                  <!-- Lab Tests -->
                  <div class="dx-section-label">Laboratory Tests</div>
                  <div class="dx-checklist" id="dx-checklist">
                    <!-- Filled by JS -->
                  </div>

                  <!-- Imaging -->
                  <div class="dx-section-label" style="margin-top:12px;">Imaging</div>
                  <div class="dx-imaging-grid">
                    <label class="dx-check-item"><input type="checkbox" class="dx-cb" data-test="Chest X-ray" /> Chest
                      X-ray</label>
                    <label class="dx-check-item"><input type="checkbox" class="dx-cb"
                        data-test="Whole Abdomen Ultrasound" /> Whole Abdomen Ultrasound</label>
                    <div class="field-group" style="grid-column:1/-1;">
                      <label class="field-label" style="font-size:12px;">Other Imaging</label>
                      <input type="text" class="field-input" id="dx-other-imaging"
                        placeholder="Specify other imaging..." />
                    </div>
                  </div>

                  <button class="btn-generate" id="generate-dx-btn">
                    <i data-feather="file-text"></i> Generate Request
                  </button>
                </div>
              </div>

              <div class="rx-right-col">
                <!-- Diagnostic Preview -->
                <div class="card rx-preview-card" id="dx-preview-card">
                  <div id="print-area-dx">
                    <div class="rx-preview-clinic">MedCare Integrated Psychiatric &amp; Lifestyle Medicine Clinic</div>
                    <div class="rx-preview-addr">123 Wellness Ave, Quezon City · +63-2-8888-9999</div>
                    <div class="rx-preview-stamp" style="font-size:20px;letter-spacing:1px;">DIAGNOSTIC REQUEST FORM
                    </div>
                    <div class="rx-preview-patient-row">
                      <span>Patient: <strong id="dx-prev-patient">—</strong></span>
                      <span>Date: <strong id="dx-prev-date">—</strong></span>
                    </div>
                    <div class="rx-preview-diag">Requesting Physician: <strong>Dr. Maria Santos, MD</strong></div>
                    <div class="rx-preview-notes-label">Clinical Notes:</div>
                    <div id="dx-prev-notes" class="rx-preview-notes-text">—</div>
                    <div class="rx-preview-meds-label">Tests Ordered:</div>
                    <ul id="dx-prev-tests" class="rx-preview-meds-list"></ul>
                    <div class="rx-preview-sig-line">
                      <div class="rx-sig-line-bar"></div>
                      <div class="rx-sig-name">Dr. Maria Santos, MD</div>
                      <div class="rx-sig-lic">License No. 0012345</div>
                    </div>
                  </div>
                  <button class="btn-print" onclick="printDx()">
                    <i data-feather="printer"></i> Print Request
                  </button>
                </div>

                <!-- Dx Template Library -->
                <div class="card rx-templates-card">
                  <div class="card-header">
                    <span class="card-title"><i data-feather="star"
                        style="width:14px;height:14px;vertical-align:middle;color:#f59e0b;"></i> Diagnostic
                      Templates</span>
                  </div>
                  <div class="template-list" id="dx-template-list">
                    <!-- Filled by JS -->
                  </div>
                </div>
              </div>
            </div>
          </div>

        </section><!-- /prescriptions -->

      </div><!-- /content-wrap -->
    </div><!-- /main-area -->
  </div><!-- /app-shell -->


  <!-- ================================================
     MODALS
     ================================================ -->

  <!-- Add Patient Modal -->
  <div class="modal-overlay hidden" id="add-patient-modal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Add New Patient</h3>
        <button class="modal-close" data-close="add-patient-modal"><i data-feather="x"></i></button>
      </div>
      <div class="modal-body">
        <div class="modal-grid-2">
          <div class="field-group"><label class="field-label">Full Name</label><input type="text" class="field-input"
              id="new-name" placeholder="Full name" /></div>
          <div class="field-group"><label class="field-label">Age</label><input type="number" class="field-input"
              id="new-age" placeholder="Age" /></div>
          <div class="field-group"><label class="field-label">Email</label><input type="email" class="field-input"
              id="new-email" placeholder="email@example.com" /></div>
          <div class="field-group"><label class="field-label">Phone</label><input type="text" class="field-input"
              id="new-phone" placeholder="+63 9XX XXX XXXX" /></div>
        </div>
        <div class="field-group"><label class="field-label">Chief Complaint</label><input type="text"
            class="field-input" id="new-complaint" placeholder="Chief complaint" /></div>
        <div class="field-group">
          <label class="field-label">Assigned Life Coach</label>
          <select class="field-input" id="new-coach">
            <option value="Michael Chen">Michael Chen — Lifestyle</option>
            <option value="Emily Roberts">Emily Roberts — Psychiatric Life Coaching</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-outline" data-close="add-patient-modal">Cancel</button>
        <button class="btn-blue" id="save-patient-btn">Add Patient</button>
      </div>
    </div>
  </div>

  <!-- Schedule Consultation Modal -->
  <div class="modal-overlay hidden" id="schedule-consult-modal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Schedule Consultation</h3>
        <button class="modal-close" data-close="schedule-consult-modal"><i data-feather="x"></i></button>
      </div>
      <div class="modal-body">
        <div class="modal-grid-2">
          <div class="field-group">
            <label class="field-label">Patient</label>
            <select class="field-input" id="consult-patient">
              <option>Sarah Johnson</option>
              <option>Robert Martinez</option>
              <option>Emily Thompson</option>
              <option>David Lee</option>
              <option>Maria Garcia</option>
            </select>
          </div>
          <div class="field-group"><label class="field-label">Date</label><input type="date" class="field-input"
              id="consult-date" /></div>
          <div class="field-group"><label class="field-label">Time</label><input type="time" class="field-input"
              id="consult-time" /></div>
          <div class="field-group">
            <label class="field-label">Type</label>
            <select class="field-input" id="consult-type">
              <option>Follow-up</option>
              <option>Initial</option>
              <option>Emergency</option>
            </select>
          </div>
        </div>
        <div class="field-group"><label class="field-label">Notes</label><textarea class="field-textarea"
            id="consult-notes" rows="3" placeholder="Consultation notes..."></textarea></div>
      </div>
      <div class="modal-footer">
        <button class="btn-outline" data-close="schedule-consult-modal">Cancel</button>
        <button class="btn-blue" id="save-consult-btn">Schedule</button>
      </div>
    </div>
  </div>

  <!-- Medical Record Modal -->
  <div class="modal-overlay hidden" id="record-modal">
    <div class="modal-box modal-box-lg">
      <div class="modal-header">
        <h3 id="record-modal-title">Medical Record</h3>
        <button class="modal-close" data-close="record-modal"><i data-feather="x"></i></button>
      </div>
      <div class="modal-body">
        <div class="field-group"><label class="field-label">Chief Complaint</label><textarea class="field-textarea"
            id="record-complaint" rows="2"></textarea></div>
        <div class="field-group"><label class="field-label">Diagnosis</label><textarea class="field-textarea"
            id="record-diagnosis" rows="2"></textarea></div>
        <div class="field-group"><label class="field-label">Clinical Notes</label><textarea class="field-textarea"
            id="record-notes" rows="4" placeholder="Additional clinical notes..."></textarea></div>
      </div>
      <div class="modal-footer">
        <button class="btn-outline" data-close="record-modal">Close</button>
        <button class="btn-blue" id="save-record-btn">Save Record</button>
      </div>
    </div>
  </div>

  <!-- Patient Detail Modal -->
  <div class="modal-overlay hidden" id="patient-detail-modal">
    <div class="modal-box modal-box-lg">
      <div class="modal-header">
        <div style="display:flex;align-items:center;gap:12px;">
          <div class="patient-modal-avatar" id="pm-avatar">SJ</div>
          <div>
            <h3 id="pm-name">Sarah Johnson</h3>
            <div style="font-size:13px;color:#64748b;margin-top:2px;" id="pm-sub">P001 · Active</div>
          </div>
        </div>
        <button class="modal-close" data-close="patient-detail-modal"><i data-feather="x"></i></button>
      </div>
      <div class="modal-body">

        <!-- Info Grid -->
        <div class="pm-info-grid">
          <div class="pm-info-block">
            <div class="pm-info-label">Patient ID</div>
            <div class="pm-info-value" id="pm-id">P001</div>
          </div>
          <div class="pm-info-block">
            <div class="pm-info-label">Age</div>
            <div class="pm-info-value" id="pm-age">34</div>
          </div>
          <div class="pm-info-block">
            <div class="pm-info-label">Sex</div>
            <div class="pm-info-value" id="pm-sex">Female</div>
          </div>
          <div class="pm-info-block">
            <div class="pm-info-label">Status</div>
            <div id="pm-status"></div>
          </div>
          <div class="pm-info-block" style="grid-column:1/-1;">
            <div class="pm-info-label">Assigned Life Coach</div>
            <div class="pm-info-value" id="pm-coach">Michael Chen</div>
          </div>
        </div>

        <!-- Chief Complaint -->
        <div class="pm-section">
          <div class="pm-section-label">Chief Complaint</div>
          <div class="pm-section-text" id="pm-complaint">—</div>
        </div>

        <!-- Quick Actions -->
        <div class="pm-section">
          <div class="pm-section-label">Quick Actions</div>
          <div class="pm-actions-row">
            <button class="btn-blue" id="pm-btn-consult">
              <i data-feather="calendar"></i> Schedule Consultation
            </button>
            <button class="btn-outline" id="pm-btn-record">
              <i data-feather="file-text"></i> View Medical Record
            </button>
            <button class="btn-outline" id="pm-btn-rx">
              <i data-feather="tag"></i> Write Prescription
            </button>
            <button class="btn-outline" id="pm-btn-assess">
              <i data-feather="clipboard"></i> Open Assessment
            </button>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn-outline" data-close="patient-detail-modal">Close</button>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <div class="toast hidden" id="toast"></div>

  <!-- Profile Modal -->
  <div class="modal-overlay hidden" id="profile-modal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>My Profile</h3>
        <button class="modal-close" data-close="profile-modal"><i data-feather="x"></i></button>
      </div>
      <div class="modal-body" style="text-align:center;">
        <div class="profile-avatar">MS</div>
        <div style="font-size:18px;font-weight:600;margin-top:12px;">Dr. Maria Santos, MD</div>
        <div style="color:#64748b;font-size:14px;">Psychiatrist · MedCare Clinic</div>
        <div style="color:#64748b;font-size:13px;margin-top:4px;">License No. 0012345</div>
        <div style="margin-top:16px;font-size:13px;color:#64748b;">doctor@medcare.ph</div>
      </div>
      <div class="modal-footer">
        <button class="btn-outline" data-close="profile-modal">Close</button>
      </div>
    </div>
  </div>

  <!-- Edit Consultation Modal — paste this block just before </body> in psychiatrist_blade.php -->
  <div class="modal-overlay hidden" id="edit-consult-modal">
    <div class="modal-box modal-box-lg">
      <div class="modal-header">
        <div style="display:flex;align-items:center;gap:12px;">
          <div class="ec-icon"><i data-feather="calendar"></i></div>
          <div>
            <h3 id="ec-modal-title">Edit Consultation</h3>
            <div style="font-size:13px;color:#64748b;margin-top:2px;" id="ec-modal-sub">Sarah Johnson · Follow-up</div>
          </div>
        </div>
        <button class="modal-close" data-close="edit-consult-modal"><i data-feather="x"></i></button>
      </div>

      <div class="modal-body">

        <!-- Patient Info Strip -->
        <div class="ec-patient-strip" id="ec-patient-strip">
          <div class="ec-strip-item"><span class="ec-strip-label">Patient</span><span class="ec-strip-val"
              id="ec-patient-name">—</span></div>
          <div class="ec-strip-divider"></div>
          <div class="ec-strip-item"><span class="ec-strip-label">Type</span><span id="ec-type-badge"></span></div>
          <div class="ec-strip-divider"></div>
          <div class="ec-strip-item"><span class="ec-strip-label">Status</span><span id="ec-status-badge"></span></div>
        </div>

        <!-- Editable Fields -->
        <div class="modal-grid-2">
          <div class="field-group">
            <label class="field-label">Date</label>
            <input type="date" class="field-input" id="ec-date" />
          </div>
          <div class="field-group">
            <label class="field-label">Time</label>
            <input type="time" class="field-input" id="ec-time" />
          </div>
          <div class="field-group">
            <label class="field-label">Type</label>
            <select class="field-input" id="ec-type">
              <option value="Follow-up">Follow-up</option>
              <option value="Initial">Initial</option>
              <option value="Emergency">Emergency</option>
            </select>
          </div>
          <div class="field-group">
            <label class="field-label">Status</label>
            <select class="field-input" id="ec-status">
              <option value="Scheduled">Scheduled</option>
              <option value="Completed">Completed</option>
              <option value="Cancelled">Cancelled</option>
            </select>
          </div>
        </div>

        <div class="field-group">
          <label class="field-label">Notes</label>
          <textarea class="field-textarea" id="ec-notes" rows="4" placeholder="Consultation notes..."></textarea>
        </div>

        <!-- Outcome Section (shown when Completed) -->
        <div id="ec-outcome-section" class="hidden">
          <div class="ec-outcome-header">
            <i data-feather="check-circle" style="width:14px;height:14px;color:#16a34a;"></i>
            Consultation Outcome
          </div>
          <div class="field-group" style="margin-top:8px;">
            <label class="field-label">Diagnosis / Assessment</label>
            <input type="text" class="field-input" id="ec-diagnosis" placeholder="e.g. Anxiety Disorder, Insomnia" />
          </div>
          <div class="field-group" style="margin-top:8px;">
            <label class="field-label">Treatment Given / Recommendations</label>
            <textarea class="field-textarea" id="ec-treatment" rows="3"
              placeholder="Treatment provided, referrals, follow-up plan..."></textarea>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn-red-sm" id="ec-delete-btn" style="margin-right:auto;">
          <i data-feather="trash-2" style="width:13px;height:13px;margin-right:4px;"></i> Delete
        </button>
        <button class="btn-outline" data-close="edit-consult-modal">Cancel</button>
        <button class="btn-blue" id="ec-save-btn">Save Changes</button>
      </div>
    </div>
  </div>

  <script src="{{ asset('js/psychiatrist.js') }}"></script>
  <script>feather.replace();</script>
</body>

</html>
@extends('layouts.psychiatrist')

@section('title', 'Biopsychosociospiritual Assessments')
@section('page', 'assessments')
@section('page_title', 'Biopsychosociospiritual Assessments')

@section('content')
<section class="psych-section active" id="section-assessments">
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

              <!-- Vitals Card — redesigned -->
              <div class="assess-section-card vitals-card">
                <div class="assess-section-header blue-header">
                  <div class="assess-section-header-left">
                    <div class="assess-section-icon blue-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                      </svg>
                    </div>
                    <div>
                      <div class="assess-section-title">Vital Signs</div>
                      <div class="assess-section-sub">Recorded at last consultation</div>
                    </div>
                  </div>
                  <span class="assess-section-badge blue-badge">Normal Range</span>
                </div>
                <div class="vitals-redesign-grid">
                  <div class="vital-redesign-box">
                    <div class="vital-redesign-icon" style="background:#eff6ff;color:#2563eb;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                          d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                      </svg>
                    </div>
                    <div class="vital-redesign-content">
                      <div class="vital-redesign-label">Blood Pressure</div>
                      <input class="vital-redesign-input" id="v-bp" value="120/80 mmHg" />
                      <div class="vital-redesign-note">Normal: &lt;120/80</div>
                    </div>
                  </div>
                  <div class="vital-redesign-box">
                    <div class="vital-redesign-icon" style="background:#f0fdf4;color:#16a34a;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                      </svg>
                    </div>
                    <div class="vital-redesign-content">
                      <div class="vital-redesign-label">Heart Rate</div>
                      <input class="vital-redesign-input" id="v-hr" value="74 bpm" />
                      <div class="vital-redesign-note">Normal: 60–100 bpm</div>
                    </div>
                  </div>
                  <div class="vital-redesign-box">
                    <div class="vital-redesign-icon" style="background:#fff7ed;color:#ea580c;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z" />
                      </svg>
                    </div>
                    <div class="vital-redesign-content">
                      <div class="vital-redesign-label">Temperature</div>
                      <input class="vital-redesign-input" id="v-temp" value="36.6 °C" />
                      <div class="vital-redesign-note">Normal: 36.1–37.2°C</div>
                    </div>
                  </div>
                  <div class="vital-redesign-box">
                    <div class="vital-redesign-icon" style="background:#faf5ff;color:#9333ea;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                      </svg>
                    </div>
                    <div class="vital-redesign-content">
                      <div class="vital-redesign-label">Weight</div>
                      <input class="vital-redesign-input" id="v-weight" value="58 kg" />
                      <div class="vital-redesign-note">&nbsp;</div>
                    </div>
                  </div>
                  <div class="vital-redesign-box">
                    <div class="vital-redesign-icon" style="background:#f0fdf4;color:#16a34a;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="2" x2="12" y2="22" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                      </svg>
                    </div>
                    <div class="vital-redesign-content">
                      <div class="vital-redesign-label">Height</div>
                      <input class="vital-redesign-input" id="v-height" value="162 cm" />
                      <div class="vital-redesign-note">&nbsp;</div>
                    </div>
                  </div>
                  <div class="vital-redesign-box bmi-box">
                    <div class="vital-redesign-icon" style="background:#eff6ff;color:#2563eb;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <path d="M3 9h18M9 21V9" />
                      </svg>
                    </div>
                    <div class="vital-redesign-content">
                      <div class="vital-redesign-label">BMI</div>
                      <input class="vital-redesign-input" id="v-bmi" value="22.1" />
                      <div class="vital-redesign-note bmi-status-normal">● Normal weight</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- History & Examination Card -->
              <div class="assess-section-card">
                <div class="assess-section-header slate-header">
                  <div class="assess-section-header-left">
                    <div class="assess-section-icon slate-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                      </svg>
                    </div>
                    <div>
                      <div class="assess-section-title">History &amp; Examination</div>
                      <div class="assess-section-sub">Clinical interview and physical findings</div>
                    </div>
                  </div>
                </div>
                <div class="assess-fields-rich">
                  <div class="assess-rich-field">
                    <div class="assess-rich-label">
                      <span class="assess-rich-dot red-dot"></span>Chief Complaint
                    </div>
                    <textarea class="assess-rich-textarea"
                      id="bio-cc">Patient reports persistent difficulty sleeping for 3 months, waking at 2–3 AM with racing thoughts and anxiety.</textarea>
                  </div>
                  <div class="assess-rich-field">
                    <div class="assess-rich-label">
                      <span class="assess-rich-dot blue-dot"></span>History of Present Illness
                    </div>
                    <textarea class="assess-rich-textarea"
                      id="bio-hpi">Gradual onset insomnia correlated with work-related stressors. Patient denies substance use. No prior psychiatric treatment.</textarea>
                  </div>
                  <div class="assess-two-col">
                    <div class="assess-rich-field">
                      <div class="assess-rich-label">
                        <span class="assess-rich-dot amber-dot"></span>Past Psychiatric History
                      </div>
                      <textarea class="assess-rich-textarea"
                        id="bio-pph">No prior hospitalizations. No previous psychiatric diagnoses.</textarea>
                    </div>
                    <div class="assess-rich-field">
                      <div class="assess-rich-label">
                        <span class="assess-rich-dot amber-dot"></span>Past Medical History
                      </div>
                      <textarea class="assess-rich-textarea"
                        id="bio-pmh">No significant medical conditions. No surgeries. No chronic medications.</textarea>
                    </div>
                    <div class="assess-rich-field">
                      <div class="assess-rich-label">
                        <span class="assess-rich-dot slate-dot"></span>Family History
                      </div>
                      <textarea class="assess-rich-textarea"
                        id="bio-fh">Mother with generalized anxiety disorder. No family history of psychosis or suicide.</textarea>
                    </div>
                    <div class="assess-rich-field">
                      <div class="assess-rich-label">
                        <span class="assess-rich-dot slate-dot"></span>Social History
                      </div>
                      <textarea class="assess-rich-textarea"
                        id="bio-sh">Non-smoker, occasional alcohol (1–2 drinks/week). Works as marketing manager. Single, lives alone.</textarea>
                    </div>
                  </div>
                  <div class="assess-rich-field">
                    <div class="assess-rich-label">
                      <span class="assess-rich-dot green-dot"></span>Review of Systems
                    </div>
                    <textarea class="assess-rich-textarea"
                      id="bio-ros">Positive for insomnia, fatigue, and mild headaches. Negative for chest pain, palpitations, and GI complaints.</textarea>
                  </div>
                  <div class="assess-rich-field">
                    <div class="assess-rich-label">
                      <span class="assess-rich-dot green-dot"></span>Physical Examination
                    </div>
                    <textarea class="assess-rich-textarea"
                      id="bio-pe">Alert and oriented ×3. Thyroid non-enlarged. Cardiovascular and respiratory exam unremarkable.</textarea>
                  </div>
                  <div class="assess-rich-field lab-field">
                    <div class="assess-rich-label">
                      <span class="assess-rich-dot purple-dot"></span>Lab Results
                    </div>
                    <textarea class="assess-rich-textarea lab-textarea"
                      id="bio-lab">TSH: 2.1 mIU/L (normal). CBC: within normal limits. Fasting glucose: 92 mg/dL.</textarea>
                  </div>
                </div>
                <div class="assess-save-bar">
                  <span class="assess-save-hint">Changes are saved per session</span>
                  <button class="btn-blue" onclick="showToast('✓ Biological data saved.')">Save Changes</button>
                </div>
              </div>

            </div><!-- /tab-biological -->


            <!-- TAB: PSYCHOLOGICAL -->
            <div class="tab-panel" id="tab-psychological">

              <!-- MSE Card -->
              <div class="assess-section-card">
                <div class="assess-section-header purple-header">
                  <div class="assess-section-header-left">
                    <div class="assess-section-icon purple-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                        <circle cx="12" cy="12" r="10" />
                      </svg>
                    </div>
                    <div>
                      <div class="assess-section-title">Mental Status Examination</div>
                      <div class="assess-section-sub">Systematic clinical observation</div>
                    </div>
                  </div>
                  <span class="assess-section-badge purple-badge">MSE</span>
                </div>

                <!-- MSE fields in 3-col grid for compact look -->
                <div class="mse-grid">
                  <div class="mse-field-card">
                    <div class="mse-field-label">
                      <span class="mse-number">1</span>Appearance
                    </div>
                    <textarea
                      class="assess-rich-textarea">Well-groomed, age-appropriate attire. No psychomotor agitation or retardation.</textarea>
                  </div>
                  <div class="mse-field-card">
                    <div class="mse-field-label">
                      <span class="mse-number">2</span>Behavior
                    </div>
                    <textarea
                      class="assess-rich-textarea">Cooperative and engaged. Good eye contact. No unusual behaviors noted.</textarea>
                  </div>
                  <div class="mse-field-card">
                    <div class="mse-field-label">
                      <span class="mse-number">3</span>Speech
                    </div>
                    <textarea
                      class="assess-rich-textarea">Normal rate, rhythm, and volume. Articulate and coherent.</textarea>
                  </div>
                  <div class="mse-field-card mood-card">
                    <div class="mse-field-label">
                      <span class="mse-number">4</span>Mood
                    </div>
                    <textarea class="assess-rich-textarea">"Anxious and tired" — patient's own words.</textarea>
                  </div>
                  <div class="mse-field-card">
                    <div class="mse-field-label">
                      <span class="mse-number">5</span>Affect
                    </div>
                    <textarea class="assess-rich-textarea">Congruent with mood. Mildly restricted range.</textarea>
                  </div>
                  <div class="mse-field-card">
                    <div class="mse-field-label">
                      <span class="mse-number">6</span>Thought Process
                    </div>
                    <textarea
                      class="assess-rich-textarea">Linear and goal-directed. No loosening of associations.</textarea>
                  </div>
                  <div class="mse-field-card">
                    <div class="mse-field-label">
                      <span class="mse-number">7</span>Thought Content
                    </div>
                    <textarea
                      class="assess-rich-textarea">Preoccupied with work stressors. No paranoid ideation. No obsessions or compulsions.</textarea>
                  </div>
                  <div class="mse-field-card">
                    <div class="mse-field-label">
                      <span class="mse-number">8</span>Perceptual Disturbances
                    </div>
                    <textarea class="assess-rich-textarea">No hallucinations or illusions reported.</textarea>
                  </div>
                  <div class="mse-field-card">
                    <div class="mse-field-label">
                      <span class="mse-number">9</span>Cognition
                    </div>
                    <textarea
                      class="assess-rich-textarea">Oriented to person, place, and time. Immediate and recent memory intact.</textarea>
                  </div>
                  <div class="mse-field-card">
                    <div class="mse-field-label">
                      <span class="mse-number">10</span>Insight
                    </div>
                    <textarea
                      class="assess-rich-textarea">Good insight into illness. Acknowledges need for treatment.</textarea>
                  </div>
                  <div class="mse-field-card">
                    <div class="mse-field-label">
                      <span class="mse-number">11</span>Judgment
                    </div>
                    <textarea
                      class="assess-rich-textarea">Intact. Patient able to make reasonable decisions regarding daily activities.</textarea>
                  </div>
                </div>

                <div class="assess-save-bar">
                  <span class="assess-save-hint">Changes are saved per session</span>
                  <button class="btn-blue" onclick="showToast('✓ MSE data saved.')">Save Changes</button>
                </div>
              </div>

              <!-- Risk Assessment Card -->
              <div class="assess-section-card risk-card">
                <div class="assess-section-header red-header">
                  <div class="assess-section-header-left">
                    <div class="assess-section-icon red-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                          d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                        <line x1="12" y1="9" x2="12" y2="13" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                      </svg>
                    </div>
                    <div>
                      <div class="assess-section-title">Risk Assessment</div>
                      <div class="assess-section-sub">Safety evaluation and crisis planning</div>
                    </div>
                  </div>
                  <span class="assess-section-badge green-badge">Low Risk</span>
                </div>

                <!-- Suicidal Ideation — visual radio pills -->
                <div class="risk-si-block">
                  <div class="risk-si-label">Suicidal Ideation Level</div>
                  <div class="si-pill-group">
                    <label class="si-pill si-none">
                      <input type="radio" name="si" value="none" checked />
                      <span class="si-pill-dot"></span>
                      <span>None</span>
                    </label>
                    <label class="si-pill si-passive">
                      <input type="radio" name="si" value="passive" />
                      <span class="si-pill-dot"></span>
                      <span>Passive</span>
                    </label>
                    <label class="si-pill si-active">
                      <input type="radio" name="si" value="active" />
                      <span class="si-pill-dot"></span>
                      <span>Active</span>
                    </label>
                  </div>
                </div>

                <div class="assess-fields-rich" style="padding-top:0;">
                  <div class="assess-rich-field">
                    <div class="assess-rich-label">
                      <span class="assess-rich-dot red-dot"></span>Risk Assessment Notes
                    </div>
                    <textarea
                      class="assess-rich-textarea">Low overall risk. Protective factors: strong social support, no prior attempts, good insight.</textarea>
                  </div>
                  <div class="assess-rich-field">
                    <div class="assess-rich-label">
                      <span class="assess-rich-dot green-dot"></span>Safety Plan
                    </div>
                    <textarea
                      class="assess-rich-textarea">Patient instructed to contact emergency line if ideation develops. Crisis number provided. Next appointment in 2 weeks.</textarea>
                  </div>
                </div>

                <div class="assess-save-bar">
                  <span class="assess-save-hint">Changes are saved per session</span>
                  <button class="btn-blue" onclick="showToast('✓ Psychological data saved.')">Save Changes</button>
                </div>
              </div>

            </div>


            <!-- TAB: SOCIAL -->
            <div class="tab-panel" id="tab-social">

              <div class="assess-section-card">
                <div class="assess-section-header green-header">
                  <div class="assess-section-header-left">
                    <div class="assess-section-icon green-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                      </svg>
                    </div>
                    <div>
                      <div class="assess-section-title">Social Assessment</div>
                      <div class="assess-section-sub">Environmental and contextual factors</div>
                    </div>
                  </div>
                  <span class="assess-section-badge green-badge">Documented</span>
                </div>

                <!-- Social fields in 2-col icon-card grid -->
                <div class="social-fields-grid">
                  <div class="social-field-card">
                    <div class="social-field-icon" style="background:#eff6ff;color:#2563eb;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                      </svg>
                    </div>
                    <div class="social-field-content">
                      <div class="social-field-label">Living Situation</div>
                      <textarea
                        class="assess-rich-textarea">Lives alone in a 1-bedroom apartment. Has supportive family nearby.</textarea>
                    </div>
                  </div>
                  <div class="social-field-card">
                    <div class="social-field-icon" style="background:#f0fdf4;color:#16a34a;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" />
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                      </svg>
                    </div>
                    <div class="social-field-content">
                      <div class="social-field-label">Occupation / Employment</div>
                      <textarea
                        class="assess-rich-textarea">Marketing Manager at a mid-sized firm. Reports high workload and tight deadlines.</textarea>
                    </div>
                  </div>
                  <div class="social-field-card">
                    <div class="social-field-icon" style="background:#fffbeb;color:#d97706;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                      </svg>
                    </div>
                    <div class="social-field-content">
                      <div class="social-field-label">Financial Status</div>
                      <textarea class="assess-rich-textarea">Stable income. No reported financial stressors.</textarea>
                    </div>
                  </div>
                  <div class="social-field-card">
                    <div class="social-field-icon" style="background:#faf5ff;color:#9333ea;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                          d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                      </svg>
                    </div>
                    <div class="social-field-content">
                      <div class="social-field-label">Relationships / Support System</div>
                      <textarea
                        class="assess-rich-textarea">Close relationship with parents and one sibling. Small but reliable social circle.</textarea>
                    </div>
                  </div>
                  <div class="social-field-card">
                    <div class="social-field-icon" style="background:#fff1f2;color:#e11d48;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="2" y1="12" x2="22" y2="12" />
                        <path
                          d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                      </svg>
                    </div>
                    <div class="social-field-content">
                      <div class="social-field-label">Cultural Background</div>
                      <textarea
                        class="assess-rich-textarea">Filipino-American. Identifies cultural expectations around work and family as stressors.</textarea>
                    </div>
                  </div>
                  <div class="social-field-card">
                    <div class="social-field-icon" style="background:#f8fafc;color:#475569;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                      </svg>
                    </div>
                    <div class="social-field-content">
                      <div class="social-field-label">Legal Issues</div>
                      <textarea class="assess-rich-textarea">No legal issues reported.</textarea>
                    </div>
                  </div>
                  <div class="social-field-card">
                    <div class="social-field-icon" style="background:#fef2f2;color:#dc2626;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1" />
                        <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z" />
                        <line x1="6" y1="1" x2="6" y2="4" />
                        <line x1="10" y1="1" x2="10" y2="4" />
                        <line x1="14" y1="1" x2="14" y2="4" />
                      </svg>
                    </div>
                    <div class="social-field-content">
                      <div class="social-field-label">Substance Use</div>
                      <textarea
                        class="assess-rich-textarea">Occasional alcohol (1–2 drinks per week, social). No illicit substance use.</textarea>
                    </div>
                  </div>
                  <div class="social-field-card">
                    <div class="social-field-icon" style="background:#fffbeb;color:#d97706;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                          d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                        <line x1="12" y1="9" x2="12" y2="13" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                      </svg>
                    </div>
                    <div class="social-field-content">
                      <div class="social-field-label">Psychosocial Stressors</div>
                      <textarea
                        class="assess-rich-textarea">Primary stressor: occupational. Secondary: relationship loneliness. No acute trauma.</textarea>
                    </div>
                  </div>
                </div>

                <div class="assess-save-bar">
                  <span class="assess-save-hint">Changes are saved per session</span>
                  <button class="btn-blue" onclick="showToast('✓ Social data saved.')">Save Changes</button>
                </div>
              </div>

            </div><!-- /tab-social -->


            <!-- TAB: SPIRITUAL -->
            <div class="tab-panel" id="tab-spiritual">

              <div class="assess-section-card">
                <div class="assess-section-header gold-header">
                  <div class="assess-section-header-left">
                    <div class="assess-section-icon gold-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon
                          points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                      </svg>
                    </div>
                    <div>
                      <div class="assess-section-title">Spiritual Assessment</div>
                      <div class="assess-section-sub">Faith, meaning, and spiritual wellbeing</div>
                    </div>
                  </div>
                  <span class="assess-section-badge gold-badge">Documented</span>
                </div>

                <div class="spiritual-fields-grid">
                  <div class="spiritual-field-card">
                    <div class="spiritual-field-header">
                      <div class="spiritual-field-number">01</div>
                      <div class="spiritual-field-label">Religious / Spiritual Beliefs</div>
                    </div>
                    <textarea
                      class="assess-rich-textarea">Catholic background. Identifies as moderately religious. Finds comfort in prayer.</textarea>
                  </div>
                  <div class="spiritual-field-card">
                    <div class="spiritual-field-header">
                      <div class="spiritual-field-number">02</div>
                      <div class="spiritual-field-label">Practices &amp; Rituals</div>
                    </div>
                    <textarea
                      class="assess-rich-textarea">Attends Sunday Mass occasionally. Personal prayer before sleep.</textarea>
                  </div>
                  <div class="spiritual-field-card">
                    <div class="spiritual-field-header">
                      <div class="spiritual-field-number">03</div>
                      <div class="spiritual-field-label">Spiritual Coping</div>
                    </div>
                    <textarea
                      class="assess-rich-textarea">Uses prayer and reflection as primary coping strategies during stress.</textarea>
                  </div>
                  <div class="spiritual-field-card">
                    <div class="spiritual-field-header">
                      <div class="spiritual-field-number">04</div>
                      <div class="spiritual-field-label">Spiritual Needs</div>
                    </div>
                    <textarea
                      class="assess-rich-textarea">Desires more consistent spiritual community and connection.</textarea>
                  </div>
                  <div class="spiritual-field-card">
                    <div class="spiritual-field-header">
                      <div class="spiritual-field-number">05</div>
                      <div class="spiritual-field-label">Spiritual Strengths</div>
                    </div>
                    <textarea
                      class="assess-rich-textarea">Faith provides sense of meaning and resilience. Believes in healing and recovery.</textarea>
                  </div>
                  <div class="spiritual-field-card">
                    <div class="spiritual-field-header">
                      <div class="spiritual-field-number">06</div>
                      <div class="spiritual-field-label">Meaning &amp; Purpose</div>
                    </div>
                    <textarea
                      class="assess-rich-textarea">Finds purpose in her work and family relationships. Aspires to help others in the future.</textarea>
                  </div>
                </div>

                <div class="assess-save-bar">
                  <span class="assess-save-hint">Changes are saved per session</span>
                  <button class="btn-blue" onclick="showToast('✓ Spiritual data saved.')">Save Changes</button>
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
                      <textarea>NKDA (No Known Drug Allergies). No known food allergies.</textarea>
                    </div>
                  </div>
                  <div class="accordion-item">
                    <button class="accordion-trigger">Current Medications <i data-feather="chevron-down"></i></button>
                    <div class="accordion-panel">
                      <textarea>Sertraline 50mg QD (started Jun 2026). Melatonin 3mg PRN bedtime.</textarea>
                    </div>
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
                      <textarea>Up to date. COVID-19 vaccinated (3 doses). Flu vaccine annually.</textarea>
                    </div>
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
</section>
@endsection

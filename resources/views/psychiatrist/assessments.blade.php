@extends('layouts.psychiatrist')

@section('title', 'Biopsychosociospiritual Assessments')
@section('page', 'assessments')
@section('page_title', 'Biopsychosociospiritual Assessments')

@section('content')
<section class="psych-section active" id="section-assessments">
  <!-- Assessment List View -->
  <div id="assessment-list-view">
    <form method="get" action="{{ route('psychiatrist.assessments') }}" class="filter-row" style="margin-bottom:16px;">
      <div class="search-wrap">
        <i data-feather="search" class="search-icon"></i>
        <input type="text" id="assess-search" name="q" value="{{ $assessSearch }}" class="search-input"
          placeholder="Search by name or patient ID..." />
      </div>
      <button type="submit" class="btn-outline-sm">Search</button>
    </form>
    <div class="assessment-cards-grid" id="assessment-cards-grid">
      <!-- Filled by JS -->
    </div>
    @if ($assessmentsPaginator->hasPages())
      <div class="pagination-wrap">
        {{ $assessmentsPaginator->links() }}
      </div>
    @endif
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
      <button type="button" class="tab-btn assess-tab-btn active" data-assess-tab="biological">Biological</button>
      <button type="button" class="tab-btn assess-tab-btn" data-assess-tab="psychological">Psychological</button>
      <button type="button" class="tab-btn assess-tab-btn" data-assess-tab="social">Social</button>
      <button type="button" class="tab-btn assess-tab-btn" data-assess-tab="spiritual">Spiritual</button>
      <button type="button" class="tab-btn assess-tab-btn" data-assess-tab="prayer">Prayer Points</button>
      <button type="button" class="tab-btn assess-tab-btn" data-assess-tab="intervention">Intervention</button>
    </div>

    <!-- TAB: BIOLOGICAL -->
    <div class="tab-panel active" id="tab-biological" data-assess-panel="biological">

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
              <input class="vital-redesign-input" id="v-bp" value="" placeholder="e.g. 120/80 mmHg" />
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
              <input class="vital-redesign-input" id="v-hr" value="" placeholder="e.g. 74 bpm" />
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
              <input class="vital-redesign-input" id="v-temp" value="" placeholder="e.g. 36.8 °C" />
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
              <input class="vital-redesign-input" id="v-weight" value="" placeholder="e.g. 62 kg" />
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
              <input class="vital-redesign-input" id="v-height" value="" placeholder="e.g. 165 cm" />
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
              <input class="vital-redesign-input" id="v-bmi" value="" placeholder="e.g. 22.8" />
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
              id="bio-cc"></textarea>
          </div>
          <div class="assess-rich-field">
            <div class="assess-rich-label">
              <span class="assess-rich-dot blue-dot"></span>History of Present Illness
            </div>
            <textarea class="assess-rich-textarea"
              id="bio-hpi"></textarea>
          </div>
          <div class="assess-two-col">
            <div class="assess-rich-field">
              <div class="assess-rich-label">
                <span class="assess-rich-dot amber-dot"></span>Past Psychiatric History
              </div>
              <textarea class="assess-rich-textarea"
                id="bio-pph"></textarea>
            </div>
            <div class="assess-rich-field">
              <div class="assess-rich-label">
                <span class="assess-rich-dot amber-dot"></span>Past Medical History
              </div>
              <textarea class="assess-rich-textarea"
                id="bio-pmh"></textarea>
            </div>
            <div class="assess-rich-field">
              <div class="assess-rich-label">
                <span class="assess-rich-dot slate-dot"></span>Family History
              </div>
              <textarea class="assess-rich-textarea"
                id="bio-fh"></textarea>
            </div>
            <div class="assess-rich-field">
              <div class="assess-rich-label">
                <span class="assess-rich-dot slate-dot"></span>Social History
              </div>
              <textarea class="assess-rich-textarea"
                id="bio-sh"></textarea>
            </div>
          </div>
          <div class="assess-rich-field">
            <div class="assess-rich-label">
              <span class="assess-rich-dot green-dot"></span>Review of Systems
            </div>
            <textarea class="assess-rich-textarea"
              id="bio-ros"></textarea>
          </div>
          <div class="assess-rich-field">
            <div class="assess-rich-label">
              <span class="assess-rich-dot green-dot"></span>Physical Examination
            </div>
            <textarea class="assess-rich-textarea"
              id="bio-pe"></textarea>
          </div>
          <div class="assess-rich-field lab-field">
            <div class="assess-rich-label">
              <span class="assess-rich-dot purple-dot"></span>Lab Results
            </div>
            <textarea class="assess-rich-textarea lab-textarea"
              id="bio-lab"></textarea>
          </div>
        </div>
        <div class="assess-save-bar">
          <span class="assess-save-hint">Changes are saved per session</span>
          <button type="button" class="btn-blue" data-assess-save="biological">Save Biological</button>
        </div>
      </div>

    </div><!-- /tab-biological -->


    <!-- TAB: PSYCHOLOGICAL -->
    <div class="tab-panel" id="tab-psychological" data-assess-panel="psychological">

      <div class="assess-section-card">
        <div class="assess-section-header purple-header">
          <div class="assess-section-header-left">
            <div class="assess-section-icon purple-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" /><line x1="12" y1="17" x2="12.01" y2="17" /><circle cx="12" cy="12" r="10" /></svg>
            </div>
            <div>
              <div class="assess-section-title">Mental Status Examination</div>
              <div class="assess-section-sub">Systematic clinical observation</div>
            </div>
          </div>
          <span class="assess-section-badge purple-badge">MSE</span>
        </div>

        <div class="mse-grid">
          @foreach ([
            'appearance' => 'Appearance',
            'behavior' => 'Behavior',
            'speech' => 'Speech',
            'mood' => 'Mood',
            'affect' => 'Affect',
            'thought_process' => 'Thought Process',
            'thought_content' => 'Thought Content',
            'perception' => 'Perceptual Disturbances',
            'cognition' => 'Cognition',
            'insight' => 'Insight',
            'judgment' => 'Judgment',
          ] as $key => $label)
          <div class="mse-field-card {{ $key === 'mood' ? 'mood-card' : '' }}">
            <div class="mse-field-label"><span class="mse-number">{{ $loop->iteration }}</span>{{ $label }}</div>
            <textarea class="assess-rich-textarea" id="psy-{{ $key }}" placeholder="Enter notes..."></textarea>
          </div>
          @endforeach
        </div>

        <div class="assess-save-bar">
          <span class="assess-save-hint">Saved to this patient assessment</span>
          <button type="button" class="btn-blue" data-assess-save="psychological">Save Psychological</button>
        </div>
      </div>

      <div class="assess-section-card risk-card">
        <div class="assess-section-header red-header">
          <div class="assess-section-header-left">
            <div class="assess-section-icon red-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" /><line x1="12" y1="9" x2="12" y2="13" /><line x1="12" y1="17" x2="12.01" y2="17" /></svg>
            </div>
            <div>
              <div class="assess-section-title">Risk Assessment</div>
              <div class="assess-section-sub">Safety evaluation and crisis planning</div>
            </div>
          </div>
        </div>

        <div class="risk-si-block">
          <div class="risk-si-label">Suicidal Ideation Level</div>
          <div class="si-pill-group">
            <label class="si-pill si-none"><input type="radio" name="psy-si" value="none" checked /><span class="si-pill-dot"></span><span>None</span></label>
            <label class="si-pill si-passive"><input type="radio" name="psy-si" value="passive" /><span class="si-pill-dot"></span><span>Passive</span></label>
            <label class="si-pill si-active"><input type="radio" name="psy-si" value="active" /><span class="si-pill-dot"></span><span>Active</span></label>
          </div>
        </div>

        <div class="assess-fields-rich" style="padding-top:0;">
          <div class="assess-rich-field">
            <div class="assess-rich-label"><span class="assess-rich-dot red-dot"></span>Risk Assessment Notes</div>
            <textarea class="assess-rich-textarea" id="psy-risk_notes" placeholder="Risk notes..."></textarea>
          </div>
          <div class="assess-rich-field">
            <div class="assess-rich-label"><span class="assess-rich-dot green-dot"></span>Safety Plan</div>
            <textarea class="assess-rich-textarea" id="psy-safety_plan" placeholder="Safety plan..."></textarea>
          </div>
        </div>

        <div class="assess-save-bar">
          <span class="assess-save-hint">Saved to this patient assessment</span>
          <button type="button" class="btn-blue" data-assess-save="psychological">Save Psychological</button>
        </div>
      </div>
    </div>

    <!-- TAB: SOCIAL -->
    <div class="tab-panel" id="tab-social" data-assess-panel="social">
      <div class="assess-section-card">
        <div class="assess-section-header green-header">
          <div class="assess-section-header-left">
            <div class="assess-section-icon green-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M23 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /></svg>
            </div>
            <div>
              <div class="assess-section-title">Social Assessment</div>
              <div class="assess-section-sub">Environmental and contextual factors</div>
            </div>
          </div>
        </div>

        <div class="social-fields-grid">
          @foreach ([
            'living' => ['Living Situation', '#eff6ff', '#2563eb'],
            'occupation' => ['Occupation / Employment', '#f0fdf4', '#16a34a'],
            'financial' => ['Financial Status', '#fffbeb', '#d97706'],
            'relationships' => ['Relationships / Support System', '#faf5ff', '#9333ea'],
            'cultural' => ['Cultural Background', '#fff1f2', '#e11d48'],
            'legal' => ['Legal Issues', '#f8fafc', '#475569'],
            'substance' => ['Substance Use', '#fef2f2', '#dc2626'],
            'stressors' => ['Psychosocial Stressors', '#fffbeb', '#d97706'],
          ] as $key => $meta)
          <div class="social-field-card">
            <div class="social-field-icon" style="background:{{ $meta[1] }};color:{{ $meta[2] }};">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>
            </div>
            <div class="social-field-content">
              <div class="social-field-label">{{ $meta[0] }}</div>
              <textarea class="assess-rich-textarea" id="soc-{{ $key }}" placeholder="Enter notes..."></textarea>
            </div>
          </div>
          @endforeach
        </div>

        <div class="assess-save-bar">
          <span class="assess-save-hint">Saved to this patient assessment</span>
          <button type="button" class="btn-blue" data-assess-save="social">Save Social</button>
        </div>
      </div>
    </div>

    <!-- TAB: SPIRITUAL -->
    <div class="tab-panel" id="tab-spiritual" data-assess-panel="spiritual">
      <div class="assess-section-card">
        <div class="assess-section-header gold-header">
          <div class="assess-section-header-left">
            <div class="assess-section-icon gold-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" /></svg>
            </div>
            <div>
              <div class="assess-section-title">Spiritual Assessment</div>
              <div class="assess-section-sub">Faith, meaning, and spiritual wellbeing</div>
            </div>
          </div>
        </div>

        <div class="spiritual-fields-grid">
          @foreach ([
            'beliefs' => 'Religious / Spiritual Beliefs',
            'practices' => 'Practices & Rituals',
            'coping' => 'Spiritual Coping',
            'needs' => 'Spiritual Needs',
            'strengths' => 'Spiritual Strengths',
            'meaning' => 'Meaning & Purpose',
          ] as $i => $label)
          <div class="spiritual-field-card">
            <div class="spiritual-field-header">
              <div class="spiritual-field-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
              <div class="spiritual-field-label">{{ $label }}</div>
            </div>
            <textarea class="assess-rich-textarea" id="spi-{{ $i }}" placeholder="Enter notes..."></textarea>
          </div>
          @endforeach
        </div>

        <div class="assess-save-bar">
          <span class="assess-save-hint">Saved to this patient assessment</span>
          <button type="button" class="btn-blue" data-assess-save="spiritual">Save Spiritual</button>
        </div>
      </div>
    </div>

    <!-- TAB: PRAYER POINTS -->
    <div class="tab-panel" id="tab-prayer" data-assess-panel="prayer">
      <div class="card">
        <div class="card-header"><span class="card-title">Prayer Points</span></div>
        <div class="prayer-input-row">
          <input type="text" id="prayer-input" class="field-input" placeholder="Add a prayer point..." />
          <button type="button" class="btn-purple" id="add-prayer-btn">Add Prayer Point</button>
        </div>
        <div class="prayer-list" id="prayer-list"></div>
        <div class="assess-save-bar" style="padding:12px 16px;">
          <span class="assess-save-hint">Added points are saved with the assessment</span>
          <button type="button" class="btn-blue" data-assess-save="prayer_points">Save Prayer Points</button>
        </div>
      </div>
    </div>

    <!-- TAB: INTERVENTION -->
    <div class="tab-panel" id="tab-intervention" data-assess-panel="intervention">
      <div class="card">
        <div class="card-header"><span class="card-title">Treatment Plan &amp; Interventions</span></div>
        <div class="assess-fields">
          <div class="assess-field-group">
            <label>Treatment Plan</label>
            <textarea id="intervention-plan" placeholder="Describe the treatment plan..."></textarea>
          </div>
          <div class="assess-field-group">
            <label>Add Intervention Entry</label>
            <div class="intervention-add-row">
              <input type="text" id="intervention-input" class="field-input" placeholder="Describe intervention..." />
              <button type="button" class="btn-outline-sm" id="add-intervention-btn">Add Entry</button>
            </div>
          </div>
        </div>
        <div class="intervention-timeline" id="intervention-timeline"></div>
        <div class="assess-save-row">
          <button type="button" class="btn-blue" data-assess-save="intervention">Save Plan</button>
        </div>
      </div>

      <div class="card" style="margin-top:16px;">
        <div class="card-header">
          <span class="card-title">Medical History</span>
          <button type="button" class="btn-ghost" id="medhist-toggle-all">Expand All</button>
        </div>
        <div class="accordion-list" id="medhist-accordion">
          @foreach ([
            'allergies' => 'Allergies',
            'medications' => 'Current Medications',
            'surgical' => 'Surgical History',
            'hospitalizations' => 'Hospitalizations',
            'immunizations' => 'Immunizations',
            'family' => 'Family Medical History',
          ] as $key => $label)
          <div class="accordion-item">
            <button type="button" class="accordion-trigger">{{ $label }} <i data-feather="chevron-down"></i></button>
            <div class="accordion-panel">
              <textarea id="medhist-{{ $key }}" placeholder="Enter details..."></textarea>
            </div>
          </div>
          @endforeach
        </div>
        <div class="assess-save-bar" style="padding:12px 16px;">
          <span class="assess-save-hint">Saved with intervention plan</span>
          <button type="button" class="btn-blue" data-assess-save="intervention">Save Medical History</button>
        </div>
      </div>
    </div>

  </div><!-- /assessment-detail-view -->
</section>
@endsection

@push('scripts')
<script>
  window.PSYCH_DATA = window.PSYCH_DATA || {};
  window.PSYCH_DATA.assessmentPatients = @json($patients);
</script>
@endpush
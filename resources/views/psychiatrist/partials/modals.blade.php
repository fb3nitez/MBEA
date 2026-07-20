@php
  try {
    $lifeCoaches = $lifeCoaches ?? \App\Models\User::role('lifecoach')->orderBy('name')->get(['id', 'name']);
  } catch (\Throwable $e) {
    $lifeCoaches = collect();
  }
@endphp

<!-- Add Patient Modal -->
<div class="modal-overlay hidden" id="add-patient-modal">
  <div class="modal-box">
    <div class="modal-header">
      <h3>Add New Patient</h3>
      <button class="modal-close" data-close="add-patient-modal"><i data-feather="x"></i></button>
    </div>
    <div class="modal-body">
      <div class="modal-grid-2">
        <div class="field-group">
          <label class="field-label">Full Name</label>
          <input type="text" class="field-input" id="new-name" placeholder="Full name" />
        </div>
        <div class="field-group">
          <label class="field-label">Age</label>
          <input type="number" class="field-input" id="new-age" placeholder="Age" />
        </div>
        <div class="field-group">
          <label class="field-label">Sex</label>
          <select class="field-input" id="new-sex">
            <option value="female">Female</option>
            <option value="male">Male</option>
          </select>
        </div>
        <div class="field-group">
          <label class="field-label">Status</label>
          <select class="field-input" id="new-status">
            <option value="Active">Active</option>
            <option value="Submitted">Submitted</option>
            <option value="Critical">Critical</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>
      <div class="field-group">
        <label class="field-label">Chief Complaint</label>
        <input type="text" class="field-input" id="new-complaint" placeholder="Chief complaint" />
      </div>
      <div class="field-group">
        <label class="field-label">Assigned Life Coach</label>
        <select class="field-input" id="new-coach">
          <option value="">Unassigned</option>
          @foreach ($lifeCoaches as $coach)
            <option value="{{ $coach->id }}">{{ $coach->name }}</option>
          @endforeach
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
          <select class="field-input" id="consult-patient"></select>
        </div>
        <div class="field-group">
          <label class="field-label">Date</label>
          <input type="date" class="field-input" id="consult-date" />
        </div>
        <div class="field-group">
          <label class="field-label">Time</label>
          <input type="time" class="field-input" id="consult-time" />
        </div>
        <div class="field-group">
          <label class="field-label">Type</label>
          <select class="field-input" id="consult-type">
            <option>Follow-up</option>
            <option>Initial</option>
            <option>Emergency</option>
          </select>
        </div>
      </div>
      <div class="field-group">
        <label class="field-label">Notes</label>
        <textarea class="field-textarea" id="consult-notes" rows="3" placeholder="Consultation notes..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" data-close="schedule-consult-modal">Cancel</button>
      <button class="btn-blue" id="save-consult-btn">Schedule</button>
    </div>
  </div>
</div>

<!-- Patient Detail Modal -->
<div class="modal-overlay hidden" id="patient-detail-modal">
  <div class="modal-box modal-box-xl">
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:12px;">
        <div class="patient-modal-avatar" id="pm-avatar">—</div>
        <div>
          <h3 id="pm-name">Patient</h3>
          <div style="font-size:13px;color:#64748b;margin-top:2px;" id="pm-sub">—</div>
        </div>
      </div>
      <button class="modal-close" data-close="patient-detail-modal"><i data-feather="x"></i></button>
    </div>

    <div class="tab-bar" id="pm-tabs" style="padding:0 20px;">
      <button type="button" class="tab-btn active" data-pm-tab="overview">Overview</button>
      <button type="button" class="tab-btn" data-pm-tab="record">Patient Record</button>
      <button type="button" class="tab-btn" data-pm-tab="medical">Medical History</button>
      <button type="button" class="tab-btn" data-pm-tab="psychiatric">Psychiatric History</button>
      <button type="button" class="tab-btn" data-pm-tab="lifestyle">Lifestyle</button>
      <button type="button" class="tab-btn" data-pm-tab="coach">Life Coach</button>
    </div>

    <div class="modal-body" style="max-height:65vh;overflow-y:auto;">

      <!-- Overview -->
      <div class="pm-tab-panel active" data-pm-panel="overview">
        <div class="pm-info-grid">
          <div class="pm-info-block">
            <div class="pm-info-label">Patient ID</div>
            <div class="pm-info-value" id="pm-id">—</div>
          </div>
          <div class="pm-info-block">
            <div class="pm-info-label">Age</div>
            <div class="pm-info-value" id="pm-age">—</div>
          </div>
          <div class="pm-info-block">
            <div class="pm-info-label">Sex</div>
            <div class="pm-info-value" id="pm-sex">—</div>
          </div>
          <div class="pm-info-block">
            <div class="pm-info-label">Status</div>
            <div id="pm-status"></div>
          </div>
          <div class="pm-info-block" style="grid-column:1/-1;">
            <div class="pm-info-label">Assigned Life Coach</div>
            <div class="pm-info-value" id="pm-coach">Unassigned</div>
          </div>
        </div>
        <div class="pm-section">
          <div class="pm-section-label">Chief Complaint</div>
          <div class="pm-section-text" id="pm-complaint">—</div>
        </div>
        <div class="pm-section">
          <div class="pm-section-label">Quick Actions</div>
          <div class="pm-actions-row">
            <button class="btn-blue" id="pm-btn-consult">
              <i data-feather="calendar"></i> Schedule Consultation
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

      <!-- Patient Record -->
      <div class="pm-tab-panel" data-pm-panel="record">
        <div class="modal-grid-2">
          <div class="field-group">
            <label class="field-label">Full Name</label>
            <input type="text" class="field-input" id="pr-fullname" />
          </div>
          <div class="field-group">
            <label class="field-label">Birthday</label>
            <input type="date" class="field-input" id="pr-birthday" />
          </div>
          <div class="field-group">
            <label class="field-label">Sex</label>
            <select class="field-input" id="pr-sex">
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
          </div>
          <div class="field-group">
            <label class="field-label">Gender</label>
            <input type="text" class="field-input" id="pr-gender" />
          </div>
          <div class="field-group">
            <label class="field-label">Marital Status</label>
            <select class="field-input" id="pr-marital">
              <option value="single">Single</option>
              <option value="married">Married</option>
              <option value="annulled">Annulled</option>
              <option value="widowed">Widowed</option>
              <option value="separated">Separated</option>
            </select>
          </div>
          <div class="field-group">
            <label class="field-label">Religion</label>
            <input type="text" class="field-input" id="pr-religion" />
          </div>
          <div class="field-group">
            <label class="field-label">Year Level</label>
            <input type="text" class="field-input" id="pr-year" />
          </div>
          <div class="field-group">
            <label class="field-label">Course</label>
            <input type="text" class="field-input" id="pr-course" />
          </div>
          <div class="field-group">
            <label class="field-label">Occupation</label>
            <input type="text" class="field-input" id="pr-occupation" />
          </div>
          <div class="field-group">
            <label class="field-label">Status</label>
            <select class="field-input" id="pr-status">
              <option value="Submitted">Submitted</option>
              <option value="Active">Active</option>
              <option value="Critical">Critical</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="field-group">
          <label class="field-label">Chief Complaint</label>
          <textarea class="field-textarea" id="pr-complaint" rows="2"></textarea>
        </div>
        <div class="field-group">
          <label class="field-label">Primary Diagnosis</label>
          <input type="text" class="field-input" id="pr-diagnosis" />
        </div>
        <div class="field-group">
          <label class="field-label">Clinical Notes</label>
          <textarea class="field-textarea" id="pr-clinical-notes" rows="2"></textarea>
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:12px;">
          <button class="btn-blue" id="pm-save-record">Save Patient Record</button>
        </div>
      </div>

      <!-- Medical History -->
      <div class="pm-tab-panel" data-pm-panel="medical">
        <div class="pm-section-label" style="margin-bottom:8px;">Personal Medical History</div>
        <div class="check-grid" id="mh-personal-checks">
          @foreach ([
            'hypertension' => 'Hypertension',
            'stroke_tia' => 'Stroke / TIA',
            'diabetes' => 'Diabetes',
            'bronchial_asthma' => 'Bronchial Asthma',
            'tuberculosis' => 'Tuberculosis',
            'thyroid_disorders' => 'Thyroid Disorders',
            'chronic_pain_fibromyalgia' => 'Chronic Pain / Fibromyalgia',
            'epilepsy_seizure' => 'Epilepsy / Seizure',
            'autoimmune_disease' => 'Autoimmune Disease',
            'cancer' => 'Cancer',
            'other_medical' => 'Other',
          ] as $key => $label)
            <label class="check-item">
              <input type="checkbox" class="mh-check" data-field="{{ $key }}" /> {{ $label }}
            </label>
          @endforeach
        </div>
        <div class="modal-grid-2" style="margin-top:10px;">
          <div class="field-group">
            <label class="field-label">Autoimmune Specify</label>
            <input type="text" class="field-input" id="mh-autoimmune_specify" />
          </div>
          <div class="field-group">
            <label class="field-label">Cancer Specify</label>
            <input type="text" class="field-input" id="mh-cancer_specify" />
          </div>
        </div>
        <div class="field-group">
          <label class="field-label">Other Specify</label>
          <input type="text" class="field-input" id="mh-other_medical_specify" />
        </div>
        <div class="field-group">
          <label class="field-label">Current Medications</label>
          <textarea class="field-textarea" id="mh-current_medications" rows="2"></textarea>
        </div>

        <div class="pm-section-label" style="margin:16px 0 8px;">Family History</div>
        <div class="check-grid">
          @foreach ([
            'family_hypertension' => 'Hypertension',
            'family_stroke' => 'Stroke',
            'family_diabetes' => 'Diabetes',
            'family_cancer' => 'Cancer',
            'family_psychiatric_disorder' => 'Psychiatric Disorder',
            'family_substance_use' => 'Substance Use',
            'family_other' => 'Other',
          ] as $key => $label)
            <label class="check-item">
              <input type="checkbox" class="mh-check" data-field="{{ $key }}" /> {{ $label }}
            </label>
          @endforeach
        </div>
        <div class="modal-grid-2" style="margin-top:10px;">
          <div class="field-group">
            <label class="field-label">Cancer Type</label>
            <input type="text" class="field-input" id="mh-family_cancer_type" />
          </div>
          <div class="field-group">
            <label class="field-label">Cancer Relation</label>
            <input type="text" class="field-input" id="mh-family_cancer_relation" />
          </div>
          <div class="field-group">
            <label class="field-label">Psychiatric Relation</label>
            <input type="text" class="field-input" id="mh-family_psychiatric_relation" />
          </div>
          <div class="field-group">
            <label class="field-label">Other Specify</label>
            <input type="text" class="field-input" id="mh-family_other_specify" />
          </div>
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:12px;">
          <button class="btn-blue" id="pm-save-medical">Save Medical History</button>
        </div>
      </div>

      <!-- Psychiatric History -->
      <div class="pm-tab-panel" data-pm-panel="psychiatric">
        <div class="modal-grid-2">
          <div class="field-group">
            <label class="check-item">
              <input type="checkbox" id="ph-diagnosed_mental_condition" /> Diagnosed mental condition
            </label>
          </div>
          <div class="field-group">
            <label class="check-item">
              <input type="checkbox" id="ph-psychiatric_hospitalized" /> Psychiatric hospitalization
            </label>
          </div>
          <div class="field-group">
            <label class="field-label">Condition</label>
            <input type="text" class="field-input" id="ph-mental_condition" />
          </div>
          <div class="field-group">
            <label class="field-label">Hospitalization Count</label>
            <input type="number" class="field-input" id="ph-hospitalization_count" min="0" />
          </div>
          <div class="field-group" style="grid-column:1/-1;">
            <label class="field-label">When Hospitalized</label>
            <input type="text" class="field-input" id="ph-hospitalization_when" />
          </div>
        </div>

        <div class="pm-section-label" style="margin:16px 0 8px;">Trauma / Abuse History</div>
        @foreach ([
          'physical' => 'Physical Abuse',
          'emotional' => 'Emotional Abuse',
          'sexual' => 'Sexual Abuse',
          'neglect' => 'Neglect',
        ] as $key => $label)
          <div class="trauma-block">
            <label class="check-item">
              <input type="checkbox" class="ph-trauma-main" data-prefix="{{ $key }}" id="ph-{{ $key }}_abuse" /> {{ $label }}
            </label>
            <div class="check-grid" style="margin:6px 0 8px 18px;">
              <label class="check-item"><input type="checkbox" id="ph-{{ $key }}_child" /> Childhood</label>
              <label class="check-item"><input type="checkbox" id="ph-{{ $key }}_adult" /> Adulthood</label>
              <label class="check-item"><input type="checkbox" id="ph-{{ $key }}_ongoing" /> Ongoing</label>
              <label class="check-item"><input type="checkbox" id="ph-{{ $key }}_past" /> Past</label>
            </div>
            <div class="field-group">
              <input type="text" class="field-input" id="ph-{{ $key }}_notes" placeholder="Notes..." />
            </div>
          </div>
        @endforeach
        <div style="display:flex;justify-content:flex-end;margin-top:12px;">
          <button class="btn-blue" id="pm-save-psychiatric">Save Psychiatric History</button>
        </div>
      </div>

      <!-- Lifestyle Assessment -->
      <div class="pm-tab-panel" data-pm-panel="lifestyle">
        <div class="modal-grid-2">
          <div class="field-group">
            <label class="field-label">Health Score (1–10)</label>
            <input type="number" class="field-input" id="ls-health_score" min="1" max="10" />
          </div>
          <div class="field-group">
            <label class="field-label">Sleep Hours</label>
            <input type="number" class="field-input" id="ls-sleep_hours" min="0" max="24" />
          </div>
          <div class="field-group">
            <label class="field-label">Tired Frequency</label>
            <input type="text" class="field-input" id="ls-tired_frequency" />
          </div>
          <div class="field-group">
            <label class="field-label">Weight Perception</label>
            <input type="text" class="field-input" id="ls-weight_perception" />
          </div>
          <div class="field-group">
            <label class="field-label">Fast Food Frequency</label>
            <input type="text" class="field-input" id="ls-fast_food_frequency" />
          </div>
          <div class="field-group">
            <label class="field-label">Fruits/Veg Servings</label>
            <input type="text" class="field-input" id="ls-fruits_veg_servings" />
          </div>
          <div class="field-group">
            <label class="field-label">Exercise Frequency</label>
            <input type="text" class="field-input" id="ls-exercise_frequency" />
          </div>
          <div class="field-group">
            <label class="field-label">Motivation Level</label>
            <input type="text" class="field-input" id="ls-motivation_level" />
          </div>
        </div>

        <div class="pm-section-label" style="margin:16px 0 8px;">PHQ-9 Items</div>
        <div class="modal-grid-2">
          @foreach ([
            'phq_little_interest' => 'Little interest',
            'phq_feeling_down' => 'Feeling down',
            'phq_trouble_sleeping' => 'Trouble sleeping',
            'phq_feeling_tired' => 'Feeling tired',
            'phq_poor_appetite' => 'Poor appetite',
            'phq_feeling_bad' => 'Feeling bad about self',
            'phq_trouble_concentrating' => 'Trouble concentrating',
            'phq_moving_slow' => 'Moving/speaking slow',
            'phq_thoughts_hurting' => 'Thoughts of self-harm',
          ] as $key => $label)
            <div class="field-group">
              <label class="field-label">{{ $label }}</label>
              <select class="field-input" id="ls-{{ $key }}">
                <option value="">—</option>
                <option>Not at all</option>
                <option>Several days</option>
                <option>More than half the days</option>
                <option>Nearly every day</option>
              </select>
            </div>
          @endforeach
        </div>

        <div class="pm-section-label" style="margin:16px 0 8px;">Substance / Habit Use</div>
        <div class="check-grid">
          @foreach ([
            'sub_nicotine' => 'Nicotine',
            'sub_alcohol' => 'Alcohol',
            'sub_recreational' => 'Recreational drugs',
            'sub_marijuana' => 'Marijuana',
            'sub_screentime' => 'Screen time',
            'sub_gambling' => 'Gambling',
            'sub_others' => 'Others',
          ] as $key => $label)
            <label class="check-item">
              <input type="checkbox" class="ls-sub-check" data-field="{{ $key }}" /> {{ $label }}
            </label>
          @endforeach
        </div>
        <div class="field-group" style="margin-top:10px;">
          <label class="field-label">Lifestyle Motivation</label>
          <textarea class="field-textarea" id="ls-lifestyle_motivation" rows="2"></textarea>
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:12px;">
          <button class="btn-blue" id="pm-save-lifestyle">Save Lifestyle Assessment</button>
        </div>
      </div>

      <!-- Assign Life Coach -->
      <div class="pm-tab-panel" data-pm-panel="coach">
        <div class="field-group">
          <label class="field-label">Assigned Life Coach</label>
          <select class="field-input" id="pm-coach-select">
            <option value="">Unassigned</option>
            @foreach ($lifeCoaches as $coach)
              <option value="{{ $coach->id }}">{{ $coach->name }}</option>
            @endforeach
          </select>
        </div>
        <p style="font-size:13px;color:#64748b;margin-top:8px;">
          Assigning a life coach links this patient for lifestyle coaching follow-up.
        </p>
        <div style="display:flex;justify-content:flex-end;margin-top:12px;">
          <button class="btn-blue" id="pm-save-coach">Save Assignment</button>
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
      <div class="profile-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'DR', 0, 2)) }}</div>
      <div style="font-size:18px;font-weight:600;margin-top:12px;">{{ auth()->user()->name ?? 'Psychiatrist' }}</div>
      <div style="color:#64748b;font-size:14px;">Psychiatrist · MedCare Clinic</div>
      <div style="margin-top:16px;font-size:13px;color:#64748b;">{{ auth()->user()->email ?? '' }}</div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" data-close="profile-modal">Close</button>
    </div>
  </div>
</div>

<!-- Edit Consultation Modal -->
<div class="modal-overlay hidden" id="edit-consult-modal">
  <div class="modal-box modal-box-lg">
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:12px;">
        <div class="ec-icon"><i data-feather="calendar"></i></div>
        <div>
          <h3 id="ec-modal-title">Edit Consultation</h3>
          <div style="font-size:13px;color:#64748b;margin-top:2px;" id="ec-modal-sub">—</div>
        </div>
      </div>
      <button class="modal-close" data-close="edit-consult-modal"><i data-feather="x"></i></button>
    </div>

    <div class="modal-body">
      <input type="hidden" id="ec-id" />
      <div class="ec-patient-strip" id="ec-patient-strip">
        <div class="ec-strip-item"><span class="ec-strip-label">Patient</span><span class="ec-strip-val" id="ec-patient-name">—</span></div>
        <div class="ec-strip-divider"></div>
        <div class="ec-strip-item"><span class="ec-strip-label">Type</span><span id="ec-type-badge"></span></div>
        <div class="ec-strip-divider"></div>
        <div class="ec-strip-item"><span class="ec-strip-label">Status</span><span id="ec-status-badge"></span></div>
      </div>

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

<div class="modal-overlay hidden" id="logout-modal">
  <div class="modal-box">
    <div class="modal-header">
      <h3>Logout</h3>
      <button class="modal-close" data-close="logout-modal"><i data-feather="x"></i></button>
    </div>
    <div class="modal-body">
      <p>Are you sure want to end your session?</p>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" data-close="logout-modal">Cancel</button>
      <form action="{{ route('auth.logout') }}" method="post">
        @csrf
        <button class="btn-red-sm">Sure</button>
      </form>
    </div>
  </div>
</div>

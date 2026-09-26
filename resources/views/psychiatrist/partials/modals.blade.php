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
          <div style="position:relative;">
            <input type="text" class="field-input" id="consult-patient-search" placeholder="Search patient by name or ID..." autocomplete="off" />
            <div id="consult-patient-dropdown" class="typeahead-dropdown hidden" style="max-height:220px;overflow:auto;"></div>
          </div>
          <input type="hidden" id="consult-patient" />
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
      <button type="button" class="tab-btn" data-pm-tab="clinical_notes">Clinical Notes</button>
      <button type="button" class="tab-btn" data-pm-tab="medical">Medical History</button>
      <button type="button" class="tab-btn" data-pm-tab="psychiatric">Personal History</button>
      <button type="button" class="tab-btn" data-pm-tab="lifestyle">Lifestyle</button>
      <button type="button" class="tab-btn" data-pm-tab="spiritual">Spiritual</button>
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
            <div class="pm-info-label">Employment Status</div>
            <div class="pm-info-value" id="pm-employment-status">—</div>
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
            <label class="field-label">Employment Status</label>
            <select class="field-input" id="pr-employment-status">
              <option value="">Not provided</option>
              <option value="employed">Employed</option>
              <option value="student">Student</option>
              <option value="unemployed">Unemployed</option>
              <option value="retired">Retired</option>
              <option value="NA">Not Applicable</option>
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
        </div>
        <div class="field-group">
          <label class="field-label">Chief Complaint</label>
          <textarea class="field-textarea" id="pr-complaint" rows="2"></textarea>
        </div>
        <div class="field-group">
          <label class="field-label">Primary Diagnosis</label>
          <input type="text" class="field-input" id="pr-diagnosis" />
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:12px;">
          <button class="btn-blue" id="pm-save-record">Save Patient Record</button>
        </div>
      </div>

      <!-- Medical History -->
      <div class="pm-tab-panel" data-pm-panel="clinical_notes">
        <x-forms.clinical-notes />
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
        <div class="space-y-3">
          @foreach ([
          'physical' => 'Physical Abuse',
          'emotional' => 'Emotional Abuse',
          'sexual' => 'Sexual Abuse',
          'neglect' => 'Neglect',
          ] as $key => $label)
          <div class="trauma-block">
            <label class="check-item">
              <input type="checkbox" class="ph-trauma-main" data-expands="ph-{{ $key }}-expand" data-prefix="{{ $key }}" id="ph-{{ $key }}_abuse" /> {{ $label }}
            </label>
            <div class="expand-target hidden mt-3 pl-6" id="ph-{{ $key }}-expand">
              <div class="pm-form-hint">When did this occur?</div>
              <div class="check-grid" style="margin:6px 0 8px 0;">
                <label class="check-item check-card"><input type="checkbox" id="ph-{{ $key }}_child" /> As a child</label>
                <label class="check-item check-card"><input type="checkbox" id="ph-{{ $key }}_adult" /> As an adult</label>
                <label class="check-item check-card"><input type="checkbox" id="ph-{{ $key }}_ongoing" /> Ongoing</label>
                <label class="check-item check-card"><input type="checkbox" id="ph-{{ $key }}_past" /> Past experience</label>
              </div>
              <div class="field-group">
                <input type="text" class="field-input" id="ph-{{ $key }}_notes" placeholder="Notes..." />
              </div>
            </div>
          </div>
          @endforeach
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:12px;">
          <button class="btn-blue" id="pm-save-psychiatric">Save Personal History</button>
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
        </div>

        <div class="pm-section-label" style="margin:16px 0 8px;">Mental Health &amp; Well-being</div>
        <p style="font-size:13px;color:#64748b;margin:0 0 10px;">Over the past 2 weeks, how often have you experienced the following?</p>
        <div class="pm-phq-table-wrap">
          <table class="pm-phq-table">
            <thead>
              <tr>
                <th>Question</th>
                <th>Not at all</th>
                <th>Several days</th>
                <th>More than half</th>
                <th>Nearly every day</th>
              </tr>
            </thead>
            <tbody>
              @foreach ([
              'phq_little_interest' => 'Little interest or pleasure in doing things',
              'phq_feeling_down' => 'Feeling down, depressed, or hopeless',
              'phq_trouble_sleeping' => 'Trouble falling or staying asleep, or sleeping too much',
              'phq_feeling_tired' => 'Feeling tired or having little energy',
              'phq_poor_appetite' => 'Poor appetite or overeating',
              'phq_feeling_bad' => 'Feeling bad about yourself or that you are a failure',
              'phq_trouble_concentrating' => 'Trouble concentrating on things',
              'phq_moving_slow' => 'Moving or speaking slowly, or being fidgety/restless',
              'phq_thoughts_hurting' => 'Thoughts of hurting yourself',
              ] as $key => $label)
              <tr>
                <th>{{ $label }}</th>
                <td><input type="radio" class="ls-phq-radio" name="ls-phq-{{ $key }}" value="Not at all" data-field="{{ $key }}" /></td>
                <td><input type="radio" class="ls-phq-radio" name="ls-phq-{{ $key }}" value="Several days" data-field="{{ $key }}" /></td>
                <td><input type="radio" class="ls-phq-radio" name="ls-phq-{{ $key }}" value="More than half the days" data-field="{{ $key }}" /></td>
                <td><input type="radio" class="ls-phq-radio" name="ls-phq-{{ $key }}" value="Nearly every day" data-field="{{ $key }}" /></td>
              </tr>
              @endforeach
            </tbody>
          </table>
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
          <label class="check-item check-card ls-sub-item">
            <input type="checkbox" class="ls-sub-check" data-field="{{ $key }}" /> {{ $label }}
          </label>
          @endforeach
        </div>
        <div class="pm-substance-details" style="margin-top:10px;">
          @foreach ([
          'sub_nicotine' => ['amount' => 'ls-sub_nicotine_amount', 'concern' => 'ls-sub_nicotine_concern', 'label' => 'Nicotine'],
          'sub_alcohol' => ['amount' => 'ls-sub_alcohol_amount', 'concern' => 'ls-sub_alcohol_concern', 'label' => 'Alcohol'],
          'sub_recreational' => ['amount' => 'ls-sub_recreational_amount', 'concern' => 'ls-sub_recreational_concern', 'label' => 'Recreational drugs'],
          'sub_marijuana' => ['amount' => 'ls-sub_marijuana_amount', 'concern' => 'ls-sub_marijuana_concern', 'label' => 'Marijuana'],
          'sub_screentime' => ['amount' => 'ls-sub_screentime_amount', 'concern' => 'ls-sub_screentime_concern', 'label' => 'Screen time'],
          'sub_gambling' => ['amount' => 'ls-sub_gambling_amount', 'concern' => 'ls-sub_gambling_concern', 'label' => 'Gambling'],
          'sub_others' => ['amount' => 'ls-sub_others_specify', 'concern' => 'ls-sub_others_concern', 'label' => 'Others'],
          ] as $key => $meta)
          <div class="pm-substance-block" data-substance="{{ $key }}" style="display:none;margin-top:10px;border:1px solid #e2e8f0;border-radius:10px;padding:10px;background:#f8fafc;">
            <div class="field-group">
              <label class="field-label">{{ $meta['label'] }} amount / details</label>
              <input type="text" class="field-input" id="{{ $meta['amount'] }}" placeholder="Enter string" />
            </div>
            <div class="field-group" style="margin-top:8px;">
              <label class="field-label">Level of concern (0 = No concern, 5 = Very concerned)</label>
              <input type="range" class="field-input" id="{{ $meta['concern'] }}" min="0" max="5" step="1" value="0" />
              <div style="display:flex;justify-content:space-between;font-size:12px;color:#64748b;margin-top:4px;">
                <span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        <div class="field-group" style="margin-top:10px;">
          <label class="field-label">Lifestyle Motivation</label>
          <textarea class="field-textarea" id="ls-lifestyle_motivation" rows="2"></textarea>
        </div>
        <div class="field-group" style="margin-top:10px;">
          <label class="field-label">Motivation Level</label>
          <select class="field-input" id="ls-motivation_level">
            <option value="">Select motivation level</option>
            <option value="Very Low">Very Low</option>
            <option value="Low">Low</option>
            <option value="Moderate">Moderate</option>
            <option value="High">High</option>
          </select>
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:12px;">
          <button class="btn-blue" id="pm-save-lifestyle">Save Lifestyle Related Behaviors</button>
        </div>
      </div>

      <!-- Spiritual Intake -->
      <div class="pm-tab-panel" data-pm-panel="spiritual">
        @foreach ([
        'Religious Background - Childhood' => [
        'religious_background_childhood_christian' => 'Christian',
        'religious_background_childhood_catholic' => 'Catholic',
        'religious_background_childhood_none' => 'None',
        'religious_background_childhood_other' => 'Other',
        ],
        'Religious Background - Adolescent' => [
        'religious_background_adolescent_christian' => 'Christian',
        'religious_background_adolescent_catholic' => 'Catholic',
        'religious_background_adolescent_none' => 'None',
        'religious_background_adolescent_other' => 'Other',
        ],
        'Religious Background - Current' => [
        'religious_background_current_christian_science' => 'Christian Science',
        'religious_background_current_mormonism' => 'Mormonism',
        'religious_background_current_children_of_god' => 'Children of God',
        'religious_background_current_new_age' => 'New Age',
        'religious_background_current_unity_church' => 'Unity Church',
        'religious_background_current_church_of_the_living_god' => 'Church of the Living God',
        'religious_background_current_church_of_new_jerusalem' => 'Church of New Jerusalem',
        'religious_background_current_the_forum_est' => 'The Forum (EST)',
        'religious_background_current_the_way_international' => 'The Way International',
        'religious_background_current_word' => 'Word',
        'religious_background_current_jehovah_witnesses' => "Jehovah's Witnesses",
        'religious_background_current_masons' => 'Masons',
        'religious_background_current_other' => 'Other',
        ],
        'Church Involvement' => [
        'church_involvement_attends_weekly' => 'Attends church weekly',
        'church_involvement_occasional_attendance' => 'Occasional attendance',
        'church_involvement_involved_ministry_service' => 'Involved in ministry/service',
        'church_involvement_no_church_involvement' => 'No church involvement',
        'church_involvement_needed_in_ministry_service' => 'Needed in ministry/service',
        'church_involvement_no_church_involvement_further' => 'No church involvement (further)',
        ],
        'Occult & New Age Practices' => [
        'new_age_horoscopes' => 'Horoscopes',
        'new_age_dungeons_dragons' => 'Dungeons & Dragons',
        'new_age_energy_healing' => 'Energy healing',
        'new_age_speaking_in_trance' => 'Speaking in trance',
        'new_age_manifestation_law_of_attraction' => 'Manifestation / Law of Attraction',
        'new_age_automatic_writing' => 'Automatic writing',
        'new_age_mediumship' => 'Mediumship',
        'new_age_magic_eight_ball' => 'Magic 8 Ball',
        'new_age_ancestral_worship' => 'Ancestral worship',
        'new_age_astrology' => 'Astrology',
        'new_age_psychic_spells_curses' => 'Psychic spells or curses',
        'new_age_reading_palm' => 'Palm reading',
        'new_age_tarot_cards' => 'Tarot cards',
        'new_age_seance' => 'Séance',
        'new_age_spiritual_guides' => 'Spirit guides',
        'new_age_meditation' => 'Meditation',
        'new_age_fortune_telling' => 'Fortune-telling',
        'new_age_divination' => 'Divination',
        'new_age_crystal_balls' => 'Crystal balls',
        'new_age_yoga' => 'Yoga',
        'new_age_reiki' => 'Reiki',
        'new_age_self_hypnosis' => 'Self-hypnosis',
        'new_age_mind_swapping' => 'Mind swapping',
        'new_age_black_magic' => 'Black and white magic',
        'new_age_healing_prayer' => 'Healing prayer',
        'new_age_new_medicine' => 'New age medicine',
        'new_age_blood_pacts' => 'Blood pacts',
        'new_age_object_worship' => 'Object worship',
        'new_age_narcotics_or_drugs' => 'Narcotics or drugs',
        'new_age_incubus_and_succubus' => 'Incubus and succubus',
        'new_age_other' => 'Other',
        ],
        'Additional Spiritual Issues' => [
        'additional_spiritual_issues_unforgiveness' => 'Unforgiveness',
        'additional_spiritual_issues_oppression' => 'Oppression',
        'additional_spiritual_issues_demonic_family_curses' => 'Demonic family curses',
        'additional_spiritual_issues_addiction' => 'Addiction',
        'additional_spiritual_issues_mediation' => 'Meditation',
        'additional_spiritual_issues_theosophical_society' => 'Theosophical Society',
        'additional_spiritual_issues_ritual_family_oppression' => 'Ritualistic family patterns of oppression',
        'additional_spiritual_issues_mental_disorders' => 'Mental disorders',
        'additional_spiritual_issues_recurrent_family_depression' => 'Recurrent family depression or sadness',
        'additional_spiritual_issues_adultery' => 'Adultery',
        'additional_spiritual_issues_poverty' => 'Poverty',
        'additional_spiritual_issues_pride' => 'Pride',
        'additional_spiritual_issues_martha' => 'Martha',
        'additional_spiritual_issues_our_house' => 'Our house',
        'additional_spiritual_issues_fear' => 'Fear',
        'additional_spiritual_issues_buddhism' => 'Buddhism',
        'additional_spiritual_issues_yoga' => 'Yoga',
        'additional_spiritual_issues_islam' => 'Islam',
        'additional_spiritual_issues_black_magic' => 'Black magic',
        'additional_spiritual_issues_eckankar' => 'Eckankar',
        'additional_spiritual_issues_religion_of_martial_arts' => 'Religion of martial arts',
        'additional_spiritual_issues_science_of_the_mind' => 'Science of the Mind',
        'additional_spiritual_issues_transcendental_meditation' => 'Transcendental Meditation',
        'additional_spiritual_issues_father_divine' => 'Father Divine',
        'additional_spiritual_issues_spiritual_mediation' => 'Spiritual mediation',
        'additional_spiritual_issues_other' => 'Other',
        ],
        ] as $heading => $fields)
        <div class="pm-section spiritual-group">
          <div class="pm-section-label" style="margin-bottom:8px;">{{ $heading }}</div>
          <div class="check-grid spiritual-check-grid">
            @foreach ($fields as $key => $label)
            <label class="check-item spiritual-check-item">
              <input type="checkbox" class="si-check" data-field="{{ $key }}" /> {{ $label }}
            </label>
            @endforeach
          </div>
        </div>
        @endforeach

        <div class="pm-section-label" style="margin:16px 0 8px;">Follow-up Questions</div>
        @foreach ([
        'spiritual_explain_hypnosis' => 'Hypnosis, New Age or parapsychology experiences',
        'spiritual_guidance_question' => 'Imaginary friend or spirit guide',
        'spiritual_voices_question' => 'Voices or foreign repeating thoughts',
        'spiritual_unusual_experiences_question' => 'Other unusual spiritual experiences',
        'spiritual_prayer_question' => 'Vow, covenant or pact',
        'spiritual_ritual_worship_question' => 'Satanic ritual worship',
        ] as $key => $label)
        <div class="field-group">
          <label class="field-label">{{ $label }}</label>
          <textarea class="field-textarea" id="si-{{ $key }}" rows="2"></textarea>
        </div>
        @endforeach
        <div style="display:flex;justify-content:flex-end;margin-top:12px;">
          <button class="btn-blue" id="pm-save-spiritual">Save Spiritual Intake</button>
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
      <div style="color:#64748b;font-size:14px;">Psychiatrist · MB.EA Wellness Center</div>
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

<!-- Clinical Template Modal -->
<div class="modal-overlay hidden" id="template-modal">
  <div class="modal-box">
    <div class="modal-header">
      <h3 id="template-modal-title">Add Template</h3>
      <button class="modal-close" data-close="template-modal"><i data-feather="x"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="tpl-id" />
      <input type="hidden" id="tpl-type" />
      <div class="field-group">
        <label class="field-label">Name</label>
        <input type="text" class="field-input" id="tpl-name" placeholder="Template name" />
      </div>
      <div class="modal-grid-2" style="margin-top:10px;">
        <div class="field-group">
          <label class="field-label">Tag</label>
          <input type="text" class="field-input" id="tpl-tag" placeholder="e.g. Anxiety" />
        </div>
        <div class="field-group">
          <label class="field-label">Description</label>
          <input type="text" class="field-input" id="tpl-desc" placeholder="Short summary" />
        </div>
      </div>
      <div class="field-group" style="margin-top:10px;" id="tpl-diag-wrap">
        <label class="field-label">Diagnosis</label>
        <input type="text" class="field-input" id="tpl-diag" placeholder="ICD / diagnosis label" />
      </div>
      <div class="field-group" style="margin-top:10px;" id="tpl-meds-wrap">
        <label class="field-label">Medications (one per line: Name | Dose | Freq | Qty)</label>
        <textarea class="field-textarea" id="tpl-meds" rows="4" placeholder="Sertraline | 50mg | Morning | 30"></textarea>
      </div>
      <div class="field-group hidden" style="margin-top:10px;" id="tpl-tests-wrap">
        <label class="field-label">Tests (comma or newline separated)</label>
        <textarea class="field-textarea" id="tpl-tests" rows="4" placeholder="CBC with differential, TSH, Fasting Blood Sugar"></textarea>
      </div>
      <div class="field-group hidden" style="margin-top:10px;" id="tpl-lifestyle-wrap">
        <label class="field-label">Lifestyle Interventions (one per line: Category | Title | Target | Frequency | Duration | Instructions)</label>
        <textarea class="field-textarea" id="tpl-lx-items" rows="5"
          placeholder="Sleep | Fixed wake-up time | 7-8 h sleep | Daily | 4 weeks | Wake at the same time every day"></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" data-close="template-modal">Cancel</button>
      <button class="btn-blue" id="tpl-save-btn">Save Template</button>
    </div>
  </div>
</div>
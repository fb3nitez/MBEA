@extends('layouts.psychiatrist')

@section('title', 'Prescriptions')
@section('page', 'prescriptions')
@section('page_title', 'Prescriptions')

@section('content')
<section class="psych-section active" id="section-prescriptions">
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
</section>
@endsection

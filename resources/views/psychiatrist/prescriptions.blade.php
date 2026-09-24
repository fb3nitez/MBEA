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
      <div class="prescription-workspace rx-layout">

        <!-- Left: Form -->
        <div class="rx-form-col">
          <div class="card prescription-form-card" role="main" aria-label="Prescription form">
            <!-- Doctor Info (logged-in psychiatrist) -->
            <div class="rx-doctor-block">
              <div class="rx-stamp">Rx</div>
              <div class="rx-doctor-info">
                <div class="rx-doctor-name">{{ $prescriber->name }}</div>
                <div class="rx-doctor-role">Psychiatrist &middot; MB.EA Wellness Center</div>
                @if($prescriber->license_no)
                <div class="rx-doctor-lic">Lic #: {{ $prescriber->license_no }}</div>@endif
              </div>
            </div>

            <!-- Patient & Date -->
            <div class="rx-patient-row">
              <div class="field-group">
                <label class="field-label">Patient Name</label>
                <div class="typeahead-wrap">
                  <input type="text" class="field-input" id="rx-patient-search"
                    placeholder="Search patient by name or ID..." autocomplete="off" />
                  <div class="typeahead-dropdown hidden" id="rx-patient-dropdown" style="max-height:220px;overflow:auto;">
                  </div>
                </div>
                <input type="hidden" id="rx-patient" />
              </div>
              <div class="field-group">
                <label class="field-label">Age</label>
                <input type="number" class="field-input" id="rx-age" placeholder="Age" />
              </div>
              <div class="field-group">
                <label class="field-label">Date</label>
                <input type="date" class="field-input" id="rx-date" />
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
            <div style="display:flex;gap:12px;margin:10px 16px;">
              <button class="btn-outline-add" id="add-med-btn" style="margin:0;">
                <i data-feather="plus"></i> Add Medication
              </button>
              <button class="btn-outline-add" id="add-lifestyle-btn" style="margin:0;">
                <i data-feather="heart"></i> Add Lifestyle Intervention
              </button>
            </div>

            <!-- Lifestyle Interventions -->
            <div class="rx-meds-header" style="padding-top:6px;">
              <span class="field-label">Lifestyle Interventions <span
                  style="font-weight:400;color:var(--text-500);">(optional)</span></span>
            </div>
            <div class="lx-items-table-header">
              <span>CATEGORY</span><span>INTERVENTION</span><span>TARGET</span><span>FREQUENCY</span><span>DURATION</span><span></span>
            </div>
            <div id="rx-lifestyle-list">
              <div class="lx-empty-state">No lifestyle interventions added yet.</div>
            </div>

            <!-- Special Instructions -->
            <div class="field-group" style="margin-top:12px;">
              <label class="field-label">Notes / Instructions</label>
              <textarea class="field-textarea" id="rx-notes" rows="3"
                placeholder="Additional notes, special instructions, follow-up schedule..."></textarea>
            </div>

            <button class="btn-generate" id="generate-rx-btn" type="button" onclick="printRx()">
              <i data-feather="printer"></i> Print preview
            </button>
            <button class="btn-outline-add rx-save-current-btn" id="save-rx-template-btn" type="button">
              <i data-feather="bookmark"></i> Save current form as template
            </button>
          </div>
        </div>

        <!-- Right: Preview + Templates -->
        <div class="rx-right-col">
          <!-- Rx Preview -->
          <aside class="card rx-preview-card workspace-preview-panel" id="rx-preview-card"
            aria-label="Prescription preview">
            <div id="print-area-rx">
              <div class="rx-preview-clinic">MB.EA Wellness Center</div>
              <div class="rx-preview-addr">123 Wellness Ave, Quezon City &middot; +63-2-8888-9999</div>
              <div class="rx-preview-stamp">Rx</div>
              <div class="rx-preview-patient-row">
                <span>Patient: <strong id="preview-patient">&mdash;</strong></span>
                <span>Age: <strong id="preview-age">&mdash;</strong></span>
                <span>Date: <strong id="preview-date">&mdash;</strong></span>
              </div>
              <div class="rx-preview-diag">Diagnosis: <strong id="preview-diag">&mdash;</strong></div>
              <div class="rx-preview-meds-label">Medications:</div>
              <ol id="preview-meds-list" class="rx-preview-meds-list"></ol>
              <div class="rx-preview-meds-label hidden" id="preview-lifestyle-label">Lifestyle Interventions:</div>
              <div id="preview-lifestyle-list" class="lx-prev-items"></div>
              <div class="rx-preview-notes-label">Instructions:</div>
              <div id="preview-notes" class="rx-preview-notes-text">&mdash;</div>
              <div class="rx-preview-sig-line">
                <div class="rx-sig-line-bar"></div>
                <div class="rx-sig-name">{{ $prescriber->name }}</div>
                @if($prescriber->license_no)
                <div class="rx-sig-lic">License No. {{ $prescriber->license_no }}</div>@endif
              </div>
            </div>
            <button class="btn-print" onclick="printRx()">
              <i data-feather="printer"></i> Print Rx
            </button>
          </aside>

          <!-- Template Library -->
          <aside class="card rx-templates-card workspace-template-panel" aria-label="Template library">
            <div class="card-header template-rail-header"
              style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
              <span class="card-title"><i data-feather="star"
                  style="width:14px;height:14px;vertical-align:middle;color:#f59e0b;"></i> Rx Template
                Library</span>
              <button type="button" class="btn-outline-sm template-manage-open" data-manage-type="rx">Manage</button>
            </div>
            <div class="template-library" data-template-type="rx">
              <div class="template-library-toolbar">
                <label class="sr-only" for="rx-template-search">Search Rx templates</label>
                <input class="field-input template-search" id="rx-template-search" type="search"
                  placeholder="Search templates" />
                <button class="template-favorite-toggle" type="button" aria-pressed="false"
                  title="Show favorite templates"><i data-feather="star"></i><span>Favorites</span></button>
              </div>
              <div class="template-filter-chips" id="rx-template-filters" role="group"
                aria-label="Rx template categories"></div>
              <div class="template-library-status" id="rx-template-status"></div>
              <div class="template-list" id="rx-template-list" aria-live="polite" role="list">
                <div class="template-skeleton" aria-hidden="true"><span></span><span></span><span></span><span></span>
                </div>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </div>

    <!-- SUB-TAB: DIAGNOSTIC REQUEST -->
    <div class="subtab-panel" id="subtab-diagnostic">
      <div class="prescription-workspace rx-layout">
        <div class="rx-form-col">
          <div class="card prescription-form-card" role="main" aria-label="Diagnostic request form">
            <div class="rx-doctor-block">
              <div class="rx-stamp">Dx</div>
              <div class="rx-doctor-info">
                <div class="rx-doctor-name">{{ $prescriber->name }}</div>
                <div class="rx-doctor-role">Psychiatrist &middot; MB.EA Wellness Center</div>
                @if($prescriber->license_no)
                <div class="rx-doctor-lic">Lic #: {{ $prescriber->license_no }}</div>@endif
              </div>
            </div>

            <div class="rx-patient-row">
              <div class="field-group">
                <label class="field-label">Patient Name</label>
                <div class="typeahead-wrap">
                  <input type="text" class="field-input" id="dx-patient-search"
                    placeholder="Search patient by name or ID..." autocomplete="off" />
                  <div class="typeahead-dropdown hidden" id="dx-patient-dropdown" style="max-height:220px;overflow:auto;">
                  </div>
                </div>
                <input type="hidden" id="dx-patient" />
              </div>
              <div class="field-group">
                <label class="field-label">Age</label>
                <input type="number" class="field-input" id="dx-age" placeholder="Age" />
              </div>
              <div class="field-group">
                <label class="field-label">Date</label>
                <input type="date" class="field-input" id="dx-date" />
              </div>
            </div>

            <div class="field-group" style="margin-bottom:16px;">
              <label class="field-label">Clinical Notes / Reason for Request</label>
              <textarea class="field-textarea" id="dx-notes" rows="3"
                placeholder="Clinical notes and reason for diagnostic request..."></textarea>
            </div>

            <!-- Lab Tests -->
            <div class="dx-section-label">Laboratory Tests</div>
            <div class="dx-tools">
              <label class="sr-only" for="dx-test-search">Search laboratory tests</label>
              <input class="field-input" id="dx-test-search" type="search" placeholder="Search tests..." />
              <span class="dx-selected-count" id="dx-selected-count">0 tests selected</span>
            </div>
            <div class="dx-checklist" id="dx-checklist">
              <!-- Filled by JS -->
            </div>

            <!-- Imaging -->
            <div class="dx-section-label" style="margin-top:12px;">Imaging</div>
            <div class="dx-imaging-grid">
              <label class="dx-check-item"><input type="checkbox" class="dx-cb" data-test="Chest X-ray" /> Chest
                X-ray</label>
              <label class="dx-check-item"><input type="checkbox" class="dx-cb" data-test="Whole Abdomen Ultrasound" />
                Whole Abdomen Ultrasound</label>
              <div class="field-group" style="grid-column:1/-1;">
                <label class="field-label" style="font-size:12px;">Other Imaging</label>
                <input type="text" class="field-input" id="dx-other-imaging" placeholder="Specify other imaging..." />
              </div>
            </div>

            <button class="btn-generate" id="generate-dx-btn" type="button" onclick="printDx()">
              <i data-feather="printer"></i> Print preview
            </button>
            <button class="btn-outline-add rx-save-current-btn" id="save-dx-template-btn" type="button">
              <i data-feather="bookmark"></i> Save current form as template
            </button>
          </div>
        </div>

        <div class="rx-right-col">
          <!-- Diagnostic Preview -->
          <aside class="card rx-preview-card workspace-preview-panel" id="dx-preview-card"
            aria-label="Diagnostic request preview">
            <div id="print-area-dx">
              <div class="rx-preview-clinic">MB.EA Wellness Center</div>
              <div class="rx-preview-addr">123 Wellness Ave, Quezon City &middot; +63-2-8888-9999</div>
              <div class="rx-preview-stamp" style="font-size:20px;letter-spacing:1px;">DIAGNOSTIC REQUEST FORM
              </div>
              <div class="rx-preview-patient-row">
                <span>Patient: <strong id="dx-prev-patient">&mdash;</strong></span>
                <span>Date: <strong id="dx-prev-date">&mdash;</strong></span>
              </div>
              <div class="rx-preview-diag">Requesting Physician: <strong>{{ $prescriber->name }}</strong></div>
              <div class="rx-preview-notes-label">Clinical Notes:</div>
              <div id="dx-prev-notes" class="rx-preview-notes-text">&mdash;</div>
              <div class="rx-preview-meds-label">Tests Ordered:</div>
              <ul id="dx-prev-tests" class="rx-preview-meds-list"></ul>
              <div class="rx-preview-sig-line">
                <div class="rx-sig-line-bar"></div>
                <div class="rx-sig-name">{{ $prescriber->name }}</div>
                @if($prescriber->license_no)
                <div class="rx-sig-lic">License No. {{ $prescriber->license_no }}</div>@endif
              </div>
            </div>
            <button class="btn-print" onclick="printDx()">
              <i data-feather="printer"></i> Print Request
            </button>
          </aside>

          <!-- Dx Template Library -->
          <aside class="card rx-templates-card workspace-template-panel" aria-label="Diagnostic template library">
            <div class="card-header template-rail-header"
              style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
              <span class="card-title"><i data-feather="star"
                  style="width:14px;height:14px;vertical-align:middle;color:#f59e0b;"></i> Diagnostic
                Templates</span>
              <button type="button" class="btn-outline-sm template-manage-open" data-manage-type="dx">Manage</button>
            </div>
            <div class="template-library" data-template-type="dx">
              <div class="template-library-toolbar">
                <label class="sr-only" for="dx-template-search">Search diagnostic templates</label>
                <input class="field-input template-search" id="dx-template-search" type="search"
                  placeholder="Search templates" />
                <button class="template-favorite-toggle" type="button" aria-pressed="false"
                  title="Show favorite templates"><i data-feather="star"></i><span>Favorites</span></button>
              </div>
              <div class="template-filter-chips" id="dx-template-filters" role="group"
                aria-label="Diagnostic template categories"></div>
              <div class="template-library-status" id="dx-template-status"></div>
              <div class="template-list" id="dx-template-list" aria-live="polite" role="list">
                <div class="template-skeleton" aria-hidden="true">
                  <span></span><span></span><span></span><span></span><span></span></div>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </div>

    <div class="modal-overlay hidden" id="rx-print-modal">
      <div class="modal-box rx-print-modal-box">
        <div class="modal-header">
          <div style="display:flex;align-items:center;gap:10px;">
            <div class="modal-icon-print"><i data-feather="printer"></i></div>
            <h3 id="rx-print-modal-title">Print Preview</h3>
          </div>
          <button class="modal-close" data-close="rx-print-modal"><i data-feather="x"></i></button>
        </div>
        <div class="rx-print-frame-wrap">
          <iframe id="rx-print-frame" title="Print preview"></iframe>
        </div>
        <div class="modal-footer">
          <button class="btn-outline" data-close="rx-print-modal">Close</button>
          <button type="button" class="btn-print-confirm" id="rx-print-now-btn"><i data-feather="printer"></i>
            Print</button>
        </div>
      </div>
    </div>

    <div class="modal-overlay hidden" id="template-manage-modal">
      <div class="modal-box template-manage-modal-box" role="dialog" aria-modal="true"
        aria-labelledby="template-manage-title">
        <div class="modal-header">
          <div>
            <h3 id="template-manage-title">Manage Templates</h3><span class="template-manage-modal-subtitle">Shared Rx and
              Diagnostic templates</span>
          </div><button class="modal-close" id="template-manage-close" aria-label="Close template management"><i
              data-feather="x"></i></button>
        </div>
        <div class="modal-body template-manage-modal-body">
          <div class="template-manage-head"><button type="button" class="btn-blue" id="manage-new-template"><i
                data-feather="plus"></i> New template</button></div>
          <div class="template-manage-toolbar card"><input class="field-input" id="manage-search" type="search"
              placeholder="Search templates" /><select class="field-input" id="manage-type">
              <option value="">Rx and Dx</option>
              <option value="rx">Rx</option>
              <option value="dx">Dx</option>
            </select><select class="field-input" id="manage-sort">
              <option value="name">Name</option>
              <option value="updated">Last updated</option>
              <option value="usage">Most used</option>
            </select></div>
          <section class="template-tags-panel card"><button type="button" class="template-tags-toggle"
              id="template-tags-toggle" aria-expanded="false" aria-controls="template-tags-list"><span
                class="card-title">Tags (<span id="template-tags-count">0</span>)</span><i
                data-feather="chevron-down"></i></button>
            <div class="template-tags-list" id="template-tags-list" hidden></div>
          </section>
          <div class="template-manage-table-scroll">
            <div class="template-manage-table card" id="template-manage-list" aria-live="polite"></div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
  <script>
    window.PSYCH_DATA = window.PSYCH_DATA || {};
    window.PSYCH_DATA.patientSuggestions = @json($patientSuggestions);
    window.PSYCH_DATA.rxTemplates = @json($rxTemplates);
    window.PSYCH_DATA.dxTemplates = @json($dxTemplates);
    window.PSYCH_DATA.prescriber = @json($prescriberData);
    window.PSYCH_DATA.clinicLogo = '{{ asset("assets/mbea_logo.png") }}';
    window.PSYCH_DATA.manageTemplates = (window.PSYCH_DATA.rxTemplates || []).concat(window.PSYCH_DATA.dxTemplates || []);
  </script>
  <script
    src="{{ asset('js/psych/psychiatrist_template_manage.js') }}?v={{ filemtime(public_path('js/psych/psychiatrist_template_manage.js')) }}"></script>
  <script
    src="{{ asset('js/psych/psychiatrist_autocomplete.js') }}?v={{ filemtime(public_path('js/psych/psychiatrist_autocomplete.js')) }}"></script>
@endpush
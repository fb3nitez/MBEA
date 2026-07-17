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

  <!-- Edit Consultation Modal -->
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
            <button class="btn-red-sm">Sure</button>
        </form>
      </div>
    </div>
  </div>


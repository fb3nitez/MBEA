@extends('layouts.psychiatrist')

@section('title', 'Dashboard')
@section('page', 'dashboard')
@section('page_title', 'Dashboard')

@php
    $statusMap = [
        'Active' => 'badge-active',
        'Critical' => 'badge-critical',
        'Inactive' => 'badge-inactive',
        'Pending' => 'badge-pending',
        'Submitted' => 'badge-pending',
        'Completed' => 'badge-completed',
        'Scheduled' => 'badge-scheduled',
        'Emergency' => 'badge-emergency',
        'Stable' => 'badge-stable',
        'Monitoring' => 'badge-monitoring',
        'Maintenance' => 'badge-maintenance',
    ];
@endphp

@section('content')
<section class="psych-section active" id="section-dashboard">
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-text">
        <div class="stat-label">Total Patients Today</div>
        <div class="stat-value">{{ $patients->count() }}</div>
        <div class="stat-trend trend-up">From kiosk intake</div>
      </div>
      <div class="stat-icon si-blue"><i data-feather="users"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-text">
        <div class="stat-label">Pending Consultations</div>
        <div class="stat-value">{{ $pendingCount }}</div>
      </div>
      <div class="stat-icon si-amber"><i data-feather="calendar"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-text">
        <div class="stat-label">Completed Consultations</div>
        <div class="stat-value">{{ $completedCount }}</div>
      </div>
      <div class="stat-icon si-green"><i data-feather="activity"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-text">
        <div class="stat-label">High-Risk Patients</div>
        <div class="stat-value">{{ $highRiskCount }}</div>
        @if($highRiskCount > 0)
          <div class="stat-trend trend-down">Requires attention</div>
        @endif
      </div>
      <div class="stat-icon si-red"><i data-feather="alert-triangle"></i></div>
    </div>
  </div>

  <div class="two-col-grid">
    <div class="card">
      <div class="card-header">
        <span class="card-title">Pending Consultations</span>
        <a href="{{ route('psychiatrist.consultations') }}" class="btn-ghost">View All</a>
      </div>
      <div class="consult-list">
        @forelse ($pendingConsultations as $consult)
          <div class="consult-row">
            <div class="consult-info">
              <div class="consult-top">
                <span class="consult-name">{{ $consult->patientRecord?->fullname ?? 'Unknown' }}</span>
                <span class="badge badge-outline">{{ $consult->type }}</span>
              </div>
              <div class="consult-time">
                {{ \Carbon\Carbon::parse($consult->date)->format('M j') }} ·
                {{ \Carbon\Carbon::parse($consult->time)->format('g:i A') }}
              </div>
              <div class="consult-complaint">
                Chief Complaint: {{ $consult->patientRecord?->chief_complaint ?: '—' }}
              </div>
            </div>
            <a href="{{ route('psychiatrist.consultations') }}" class="btn-blue-sm">Review</a>
          </div>
        @empty
          <div class="consult-row">
            <div class="consult-info">
              <div class="consult-complaint">No pending consultations.</div>
            </div>
          </div>
        @endforelse
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <span class="card-title">High-Risk Patients</span>
        <a href="{{ route('psychiatrist.patients') }}" class="btn-ghost">View All</a>
      </div>
      <div class="highrisk-list">
        @forelse ($highRiskPatients as $patient)
          <div class="highrisk-row">
            <div class="highrisk-info">
              <div class="highrisk-top">
                <span class="consult-name">{{ $patient->fullname }}</span>
                <span class="badge badge-critical">High Risk</span>
              </div>
              <div class="highrisk-meta">Age: {{ $patient->age }} | Sex: {{ ucfirst($patient->sex) }}</div>
              <div class="consult-complaint">Chief Complaint: {{ $patient->chief_complaint ?: '—' }}</div>
            </div>
            <button class="btn-red-sm" onclick="viewPatient({{ $patient->id }})">Urgent</button>
          </div>
        @empty
          <div class="highrisk-row">
            <div class="highrisk-info">
              <div class="consult-complaint">No high-risk patients flagged.</div>
            </div>
          </div>
        @endforelse
      </div>
    </div>
  </div>

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
          @forelse ($patients as $patient)
            <tr>
              <td class="td-name">{{ $patient->fullname }}</td>
              <td>{{ $patient->age }} / {{ ucfirst($patient->sex) }}</td>
              <td>{{ $patient->chief_complaint }}</td>
              <td>{{ $patient->created_at->format('h:i A') }}</td>
              <td>
                <span class="badge {{ $statusMap[$patient->status] ?? 'badge-outline' }}">
                  {{ $patient->status }}
                </span>
              </td>
              <td>
                <button class="btn-outline-sm" onclick="viewPatient({{ $patient->id }})">View Details</button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align:center;color:#64748b;">No intakes today.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
  window.PSYCH_DATA = window.PSYCH_DATA || {};
  window.PSYCH_DATA.patients = @json($allPatients);
  window.PSYCH_DATA.lifeCoaches = @json($lifeCoaches);
</script>
@endpush

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
        'Completed' => 'badge-completed',
        'Scheduled' => 'badge-scheduled',
        'Emergency' => 'badge-emergency',
        'Stable' => 'badge-stable',
        'Monitoring' => 'badge-monitoring',
        'Maintenance' => 'badge-maintenance'
    ];
@endphp

@section('content')
<section class="psych-section active" id="section-dashboard">
<!-- Stats -->
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
                <a href="{{ route('psychiatrist.consultations') }}" class="btn-ghost">View All</a>
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
                  <a href="{{ route('psychiatrist.consultations') }}" class="btn-blue-sm">Review</a>
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
                  <a href="{{ route('psychiatrist.consultations') }}" class="btn-blue-sm">Review</a>
                </div>
              </div>
            </div>

            <!-- High-Risk Patients -->
            <div class="card">
              <div class="card-header">
                <span class="card-title">High-Risk Patients</span>
                <a href="{{ route('psychiatrist.patients') }}" class="btn-ghost">View All</a>
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
                    @foreach ($patients as $patient)
                        <td class="td-name">{{ $patient->fullname }}</td>
                        <td>{{ $patient->age }} / {{ $patient->sex }}</td>
                        <td>{{ $patient->chief_complaint }}</td>
                        <td>{{ $patient->updated_at->format('h:i A') }}</td>
                        <td>
                            <span class="badge {{ $statusMap[$patient->status] }}">
                            {{ $patient->status }}
                            </span>
                        </td>
                        <td><button class="btn-outline-sm" >View Details</button></td>
                    @endforeach
                </tbody>
              </table>
            </div>
          </div>
</section>
@endsection

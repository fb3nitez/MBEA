@extends('layouts.psychiatrist')

@section('title', 'My Profile')
@section('page', 'profile')
@section('page_title', 'My Profile')

@section('content')

<div class="profile-page">

  {{-- Left column --}}
  <div class="profile-left">

    {{-- Profile card --}}
    <div class="card profile-card">
      <div class="profile-avatar-lg">
        {{ strtoupper(substr($user->name ?? 'DR', 0, 2)) }}
      </div>
      <div class="profile-name">{{ $user->name ?? 'Psychiatrist' }}</div>
      <div class="profile-role">Psychiatrist · MedCare Clinic</div>
      <div class="profile-email">{{ $user->email ?? '' }}</div>
    </div>

    {{-- Specializations --}}
    <div class="card" style="margin-top:16px;">
      <div class="card-header">
        <span class="card-title">Specializations</span>
      </div>
      <div class="profile-spec-list">
        @foreach(['Psychiatry', 'Psychopharmacology', 'Cognitive Behavioral Therapy', 'Lifestyle Medicine'] as $spec)
          <div class="profile-spec-item">
            <i data-feather="check-circle" class="profile-spec-icon"></i>
            <span>{{ $spec }}</span>
          </div>
        @endforeach
      </div>
    </div>

  </div>

  {{-- Right column --}}
  <div class="profile-right">

    {{-- Edit Profile --}}
    <div class="card">
      <div class="card-header">
        <span class="card-title">Edit Profile</span>
        <button class="btn-blue" id="save-profile-btn">Save Changes</button>
      </div>
      <div class="profile-form">
        <div class="modal-grid-2">
          <div class="field-group">
            <label class="field-label">Full Name</label>
            <input type="text" class="field-input" id="edit-name" value="{{ $user->name ?? '' }}"/>
          </div>
          <div class="field-group">
            <label class="field-label">Email</label>
            <input type="email" class="field-input" id="edit-email" value="{{ $user->email ?? '' }}"/>
          </div>
          <div class="field-group">
            <label class="field-label">Phone</label>
            <input type="text" class="field-input" id="edit-phone" placeholder="+63 912 000 0000"/>
          </div>
          <div class="field-group">
            <label class="field-label">License Number</label>
            <input type="text" class="field-input" id="edit-license" placeholder="e.g. PRC-PSY-2026-001"/>
          </div>
        </div>
        <div class="field-group" style="margin-top:16px;">
          <label class="field-label">Bio / About</label>
          <textarea class="field-textarea" id="edit-bio" rows="4"
            placeholder="A brief description of your clinical approach and expertise..."></textarea>
        </div>
      </div>
    </div>

    {{-- Recent Activity --}}
    <div class="card" style="margin-top:16px;">
      <div class="card-header">
        <span class="card-title">Recent Activity</span>
      </div>
      <div class="profile-activity-list" id="profile-activity-list">
        @forelse($recentConsultations ?? [] as $c)
          <div class="profile-activity-item">
            <div class="profile-activity-icon">
              <i data-feather="calendar"></i>
            </div>
            <div class="profile-activity-body">
              <div class="profile-activity-text">
                Consultation with <strong>{{ $c->patientRecord->name ?? '—' }}</strong>
                @if($c->status) · <span class="badge {{ $c->status === 'Completed' ? 'badge-active' : 'badge-outline' }}">{{ $c->status }}</span> @endif
              </div>
              <div class="profile-activity-date">{{ \Carbon\Carbon::parse($c->date)->format('M d, Y') }}</div>
            </div>
          </div>
        @empty
          <div class="profile-empty">No recent activity found.</div>
        @endforelse
      </div>
    </div>

    {{-- Change Password --}}
    <div class="card" style="margin-top:16px;">
      <div class="card-header">
        <span class="card-title">Change Password</span>
      </div>
      <div class="profile-form">
        <div class="field-group">
          <label class="field-label">Current Password</label>
          <input type="password" class="field-input" id="pw-current" placeholder="Enter current password"/>
        </div>
        <div class="modal-grid-2" style="margin-top:12px;">
          <div class="field-group">
            <label class="field-label">New Password</label>
            <input type="password" class="field-input" id="pw-new" placeholder="Enter new password"/>
          </div>
          <div class="field-group">
            <label class="field-label">Confirm New Password</label>
            <input type="password" class="field-input" id="pw-confirm" placeholder="Confirm new password"/>
          </div>
        </div>
        <div style="margin-top:16px;">
          <button class="btn-blue" id="change-pw-btn">Update Password</button>
        </div>
      </div>
    </div>

  </div>

</div>

@endsection

@push('scripts')
<script>
  window.PSYCH_DATA = window.PSYCH_DATA || {};
  window.PSYCH_DATA.profile = {
    name:  @json($user->name ?? ''),
    email: @json($user->email ?? ''),
  };
</script>
@endpush

{{-- add this script tag inside the @push('scripts') block --}}
<script src="{{ asset('js/psychiatrist_profile.js') }}"></script>
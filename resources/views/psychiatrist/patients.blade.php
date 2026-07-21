@extends('layouts.psychiatrist')

@section('title', 'Patient Management')
@section('page', 'patients')
@section('page_title', 'Patient Management')

@section('content')
<section class="psych-section active" id="section-patients">
  <div class="card">
    <div class="card-header">
      <span class="card-title">All Patients</span>
    </div>
    <div class="filter-row">
      <div class="search-wrap">
        <i data-feather="search" class="search-icon"></i>
        <input type="text" id="patient-search" class="search-input"
          placeholder="Search by name or patient ID..." />
      </div>
    </div>
    <div class="table-wrap">
      <table class="data-table" id="patients-table">
        <thead>
          <tr>
            <th>Patient ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Assigned Life Coach</th>
            <th>Chief Complaint</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="patients-tbody">
          <!-- Filled by JS -->
        </tbody>
      </table>
    </div>
    @if ($patientsPaginator->hasPages())
    <div class="pagination-wrap">
      {{ $patientsPaginator->links() }}
    </div>
    @endif
  </div>
</section>
@endsection

@push('scripts')
<script>
  window.PSYCH_DATA = window.PSYCH_DATA || {};
  window.PSYCH_DATA.patients = @json($patients);
  window.PSYCH_DATA.allPatients = @json($patients);
  window.PSYCH_DATA.lifeCoaches = @json($lifeCoaches);
</script>
@endpush
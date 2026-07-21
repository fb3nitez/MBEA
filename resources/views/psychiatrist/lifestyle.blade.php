@extends('layouts.psychiatrist')

@section('title', 'Lifestyle Monitoring')
@section('page', 'lifestyle')
@section('page_title', 'Lifestyle Monitoring')

@section('content')
<section class="psych-section active" id="section-lifestyle">
  <div class="section-heading-row" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
    <div class="section-heading" style="margin:0;">Patient Lifestyle Data</div>
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
      <input type="text" id="lifestyle-search" class="search-input" placeholder="Filter by patient name or ID..." style="min-width:240px;" />
    </div>
  </div>
  <div class="lifestyle-empty hidden" id="lifestyle-empty" style="padding:28px;text-align:center;color:#64748b;border:1px dashed #cbd5e1;border-radius:12px;background:#f8fafc;">
    No lifestyle assessments yet. Open a patient record and save a lifestyle assessment to see monitoring data here.
  </div>
  <div class="lifestyle-grid" id="lifestyle-grid">
    <!-- Filled by JS -->
  </div>
</section>
@endsection

@push('scripts')
<script>
  window.PSYCH_DATA = window.PSYCH_DATA || {};
  window.PSYCH_DATA.lifestyle = @json($lifestylePatients);
  window.PSYCH_DATA.patientSuggestions = @json($patientSuggestions);
</script>
@endpush

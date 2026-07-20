@extends('layouts.psychiatrist')

@section('title', 'Lifestyle Monitoring')
@section('page', 'lifestyle')
@section('page_title', 'Lifestyle Monitoring')

@section('content')
<section class="psych-section active" id="section-lifestyle">
<div class="section-heading">Patient Lifestyle Data</div>
          <div class="lifestyle-grid" id="lifestyle-grid">
            <!-- Filled by JS -->
          </div>
</section>
@endsection

@push('scripts')
<script>
  window.PSYCH_DATA = window.PSYCH_DATA || {};
  window.PSYCH_DATA.lifestyle = @json($lifestylePatients);
</script>
@endpush

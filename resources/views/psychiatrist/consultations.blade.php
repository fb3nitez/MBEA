@extends('layouts.psychiatrist')

@section('title', 'Consultations')
@section('page', 'consultations')
@section('page_title', 'Consultations')

@section('content')
<section class="psych-section active" id="section-consultations">
<div class="card">
            <div class="card-header">
              <span class="card-title">All Consultations</span>
              <button class="btn-blue" id="schedule-consult-btn">
                <i data-feather="plus"></i> Schedule Consultation
              </button>
            </div>
            <div class="table-wrap">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="consults-tbody">
                  <!-- Filled by JS -->
                </tbody>
              </table>
            </div>
          </div>
</section>
@endsection

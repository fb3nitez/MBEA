@extends('layouts.psychiatrist')

@section('title', 'Patient Management')
@section('page', 'patients')
@section('page_title', 'Patient Management')

@section('content')
<section class="psych-section active" id="section-patients">
<div class="card">
            <div class="card-header">
              <span class="card-title">All Patients</span>
              <button class="btn-blue" id="add-patient-btn">
                <i data-feather="plus"></i> Add Patient
              </button>
            </div>
            <div class="filter-row">
              <div class="search-wrap">
                <i data-feather="search" class="search-icon"></i>
                <input type="text" id="patient-search" class="search-input"
                  placeholder="Search by name or patient ID..." />
              </div>
              <select id="patient-status-filter" class="filter-select">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Critical">Critical</option>
              </select>
            </div>
            <div class="table-wrap">
              <table class="data-table" id="patients-table">
                <thead>
                  <tr>
                    <th>Patient ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Status</th>
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
          </div>
</section>
@endsection

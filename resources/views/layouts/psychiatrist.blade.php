<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>MedCare — @yield('title')</title>
  <link rel="stylesheet" href="{{ asset('css/psychiatrist.css') }}" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

@php
  $page = trim($__env->yieldContent('page'));
@endphp

<body data-page="{{ $page }}">

  <div class="app-shell">

    <!-- ===================== SIDEBAR ===================== -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-brand-block">
        <div class="sidebar-brand">MedCare System</div>
        <div class="sidebar-role">Psychiatrist</div>
      </div>

      <nav class="sidebar-nav">
        <a href="{{ route('psychiatrist.dashboard') }}"
          class="nav-item {{ $page === 'dashboard' ? 'active' : '' }}">
          <i data-feather="grid"></i><span>Dashboard</span>
        </a>
        <a href="{{ route('psychiatrist.patients') }}"
          class="nav-item {{ $page === 'patients' ? 'active' : '' }}">
          <i data-feather="users"></i><span>Patients</span>
        </a>
        <a href="{{ route('psychiatrist.consultations') }}"
          class="nav-item {{ $page === 'consultations' ? 'active' : '' }}">
          <i data-feather="calendar"></i><span>Consultations</span>
        </a>
        <a href="{{ route('psychiatrist.lifestyle') }}"
          class="nav-item {{ $page === 'lifestyle' ? 'active' : '' }}">
          <i data-feather="trending-up"></i><span>Lifestyle Monitoring</span>
        </a>
        <a href="{{ route('psychiatrist.assessments') }}"
          class="nav-item {{ $page === 'assessments' ? 'active' : '' }}">
          <i data-feather="clipboard"></i><span>Assessments</span>
        </a>
        <a href="{{ route('psychiatrist.prescriptions') }}"
          class="nav-item {{ $page === 'prescriptions' ? 'active' : '' }}">
          <i data-feather="tag"></i><span>Prescriptions</span>
        </a>
      </nav>

      <div class="sidebar-footer">
        <button type="button" class="nav-item" id="profile-btn">
          <i data-feather="user"></i><span>Profile</span>
        </button>
        <button type="button" class="nav-item logout-item" id="logout-btn">
          <i data-feather="log-out"></i><span>Logout</span>
        </button>
      </div>
    </aside>

    <!-- ===================== MAIN ===================== -->
    <div class="main-area">

      <!-- TOP BAR -->
      <header class="main-topbar">
        <div class="topbar-left">
          <button type="button" class="hamburger-btn" id="hamburger-btn"><i data-feather="menu"></i></button>
          <h1 class="page-title" id="page-title">@yield('page_title')</h1>
        </div>
        <div class="topbar-right">
          <span class="topbar-date" id="topbar-date"></span>
        </div>
      </header>

      <div class="content-wrap">
        @yield('content')
      </div><!-- /content-wrap -->
    </div><!-- /main-area -->
  </div><!-- /app-shell -->

  @include('psychiatrist.partials.modals')

  <script>
    window.PSYCH_ROUTES = {
      dashboard: @json(route('psychiatrist.dashboard')),
      patients: @json(route('psychiatrist.patients')),
      consultations: @json(route('psychiatrist.consultations')),
      lifestyle: @json(route('psychiatrist.lifestyle')),
      assessments: @json(route('psychiatrist.assessments')),
      prescriptions: @json(route('psychiatrist.prescriptions')),
      logout: @json(route('auth.logout')),
      patientsStore: @json(route('psychiatrist.patients.store')),
      patientsShow: @json(url('/psychiatrist/patients')),
      patientsUpdate: @json(url('/psychiatrist/patients')),
      consultationsStore: @json(route('psychiatrist.consultations.store')),
      consultationsUpdate: @json(url('/psychiatrist/consultations')),
      recordsUpdate: @json(url('/psychiatrist/records')),
    };
    window.PSYCH_DATA = window.PSYCH_DATA || {};
  </script>
  @stack('scripts')
  <script src="{{ asset('js/psychiatrist.js') }}"></script>
  <script>feather.replace();</script>
</body>

</html>

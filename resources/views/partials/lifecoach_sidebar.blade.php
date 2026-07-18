{{-- resources/views/partials/lifecoach_sidebar.blade.php --}}
{{-- Usage: @include('partials.lifecoach_sidebar', ['activePage' => 'dashboard']) --}}

<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand-block">
    <div class="sidebar-brand">MedCare System</div>
    <div class="sidebar-role">Life Coach</div>
  </div>

  <nav class="sidebar-nav">
    <button class="nav-item {{ $activePage === 'dashboard' ? 'active' : '' }}" data-page="dashboard">
      <i data-feather="grid"></i><span>Dashboard</span>
    </button>
    <button class="nav-item {{ $activePage === 'patients' ? 'active' : '' }}" data-page="patients">
      <i data-feather="users"></i><span>Assigned Patients</span>
    </button>
    <button class="nav-item {{ $activePage === 'notes' ? 'active' : '' }}" data-page="notes">
      <i data-feather="clipboard"></i><span>Coaching Notes</span>
    </button>
    <button class="nav-item {{ $activePage === 'tasks' ? 'active' : '' }}" data-page="tasks">
      <i data-feather="check-square"></i><span>Tasks</span>
    </button>
  </nav>

  <div class="sidebar-footer">
    <button class="nav-item {{ $activePage === 'profile' ? 'active' : '' }}" data-page="profile">
      <i data-feather="user"></i><span>Profile</span>
    </button>
    <button class="nav-item logout-item" id="logout-btn">
      <i data-feather="log-out"></i><span>Logout</span>
    </button>
  </div>
</aside>

{{-- resources/views/partials/lifecoach_sidebar.blade.php --}}
{{-- Usage: @include('partials.lifecoach_sidebar', ['activePage' => 'dashboard']) --}}

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand-block" style="display:flex;align-items:center;gap:10px;">
        <img src="{{ asset('assets/mbea_logo.png') }}" alt="MB.EA"
            style="width:36px;height:36px;object-fit:cover;border-radius:50%;flex-shrink:0;" />
        <div>
            <div class="sidebar-brand">MB.EA Wellness Center</div>
            <div class="sidebar-role">Life Coach</div>
        </div>
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

<div class="sidebar-backdrop hidden" id="sidebar-backdrop"></div>
<!-- Sidebar backdrop -->
{{-- !! Logout modal OUTSIDE the sidebar so it isn't clipped by sidebar overflow/z-index !! --}}
<div class="modal-overlay hidden" id="logout-modal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Logout</h3>
            <button class="modal-close" data-close="logout-modal"><i data-feather="x"></i></button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to end your session?</p>
        </div>
        <div class="modal-footer">
            <button class="btn-outline" data-close="logout-modal">Cancel</button>
            <form action="{{ route('auth.logout') }}" method="post">
                @csrf
                <button type="submit" class="btn-red-sm">Sure</button>
            </form>
        </div>
    </div>
</div>
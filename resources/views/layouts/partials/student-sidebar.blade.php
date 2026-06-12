@php
    $user = auth()->user();
    $student = $user->studentRecord;
@endphp

<aside class="app-sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <span class="sidebar-brand-icon"><i class="fas fa-user-graduate"></i></span>
            <span class="sidebar-brand-text">{{ $user->school->name }}</span>
        </div>
        <button type="button" class="sidebar-toggle sidebar-toggle-desktop" id="sidebarCollapseBtn" title="Collapse sidebar">
            <i class="fas fa-angles-left"></i>
        </button>
    </div>

    <div class="px-3 py-3 border-bottom border-white border-opacity-10 student-sidebar-profile">
        <div class="d-flex align-items-center gap-2">
            <div class="student-sidebar-avatar rounded-circle overflow-hidden flex-shrink-0">
                @if($student?->photoUrl())
                    <img src="{{ $student->photoUrl() }}" alt="{{ $user->name }}">
                @else
                    <span class="d-flex align-items-center justify-content-center h-100 w-100">
                        <i class="fas fa-user"></i>
                    </span>
                @endif
            </div>
            <div class="sidebar-profile-text min-w-0">
                <div class="fw-semibold small text-truncate">{{ $user->name }}</div>
                <div class="text-white-50 small">Roll: {{ $student?->roll_no ?? '—' }}</div>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('student.dashboard') }}"
           class="nav-link-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"
           title="Dashboard">
            <span class="nav-link-icon"><i class="fas fa-home"></i></span>
            <span class="nav-link-text">Dashboard</span>
        </a>
        <a href="{{ route('student.profile') }}"
           class="nav-link-item {{ request()->routeIs('student.profile') ? 'active' : '' }}"
           title="My Profile">
            <span class="nav-link-icon"><i class="fas fa-id-badge"></i></span>
            <span class="nav-link-text">My Profile</span>
        </a>
        <a href="{{ route('student.id-card') }}"
           class="nav-link-item {{ request()->routeIs('student.id-card') ? 'active' : '' }}"
           title="My ID Card"
           target="_blank">
            <span class="nav-link-icon"><i class="fas fa-id-card"></i></span>
            <span class="nav-link-text">My ID Card</span>
        </a>
    </nav>

    <div class="sidebar-footer mt-auto p-3 border-top border-white border-opacity-10">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-link-item w-100 border-0 bg-transparent text-start sidebar-logout-btn">
                <span class="nav-link-icon"><i class="fas fa-sign-out-alt"></i></span>
                <span class="nav-link-text">Logout</span>
            </button>
        </form>
    </div>
</aside>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

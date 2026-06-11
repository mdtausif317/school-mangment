<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') — Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root { --brand: #0a5f47; --sidebar-w: 260px; }
        body { background: #f4f6f9; }
        .sidebar {
            width: var(--sidebar-w); min-height: 100vh; background: var(--brand);
            position: fixed; left: 0; top: 0; color: #fff; z-index: 100;
        }
        .sidebar .brand {
            padding: 1.25rem; font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,.15);
        }
        .sidebar a {
            color: rgba(255,255,255,.85); text-decoration: none; display: block;
            padding: .65rem 1.25rem; font-size: .925rem;
        }
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,.12); color: #fff;
        }
        .sidebar .nav-label {
            padding: .75rem 1.25rem .35rem;
            font-size: .7rem; text-transform: uppercase;
            letter-spacing: .05em; color: rgba(255,255,255,.45);
        }
        .sidebar .sub a { padding-left: 2.5rem; font-size: .875rem; }
        .main-content { margin-left: var(--sidebar-w); min-height: 100vh; }
        .top-bar {
            background: #fff; border-bottom: 1px solid #dee2e6;
            padding: .75rem 1.5rem;
        }
        .btn-brand { background: var(--brand); color: #fff; border: none; }
        .btn-brand:hover { background: #0d7a5c; color: #fff; }
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); transition: .3s; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php $sidebarMenu = $sidebarMenu ?? collect(); @endphp

    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <i class="fas fa-shield-alt me-2"></i>Super Admin
        </div>
        <nav class="py-2">
            @if($sidebarMenu->isNotEmpty())
                @foreach($sidebarMenu as $menu)
                    @if($menu->children->isNotEmpty())
                        <div class="nav-label">{{ $menu->title }}</div>
                        @foreach($menu->children as $child)
                            <a href="{{ $accessMenu->resolveSuperAdminMenuUrl($child) }}"
                               class="{{ $accessMenu->isSuperAdminMenuActive($child) ? 'active' : '' }}">
                                <i class="{{ $child->icon }} me-2"></i>{{ $child->title }}
                            </a>
                        @endforeach
                    @else
                        <a href="{{ $accessMenu->resolveSuperAdminMenuUrl($menu) }}"
                           class="{{ $accessMenu->isSuperAdminMenuActive($menu) ? 'active' : '' }}">
                            <i class="{{ $menu->icon }} me-2"></i>{{ $menu->title }}
                        </a>
                    @endif
                @endforeach
            @else
                <a href="{{ route('super-admin.dashboard') }}"
                   class="{{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-school me-2"></i>Schools
                </a>
                <a href="{{ route('super-admin.schools.create') }}"
                   class="{{ request()->routeIs('super-admin.schools.*') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle me-2"></i>School Add
                </a>
                <a href="{{ route('super-admin.menu.index') }}"
                   class="{{ request()->routeIs('super-admin.menu.*') ? 'active' : '' }}">
                    <i class="fas fa-bars me-2"></i>Menu Management
                </a>
            @endif
        </nav>
    </aside>

    <div class="main-content">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-sm btn-outline-secondary d-lg-none"
                        onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="ms-2 fw-semibold">@yield('page-title', 'Dashboard')</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-dark">Super Admin</span>
                <span class="text-muted small">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">@csrf
                    <button class="btn btn-outline-secondary btn-sm">Logout</button>
                </form>
            </div>
        </div>
        <div class="p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

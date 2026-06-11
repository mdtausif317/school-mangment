<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root { --brand: #0a5f47; }
        .navbar-brand { color: var(--brand) !important; font-weight: 600; }
        .btn-brand { background: var(--brand); color: #fff; border: none; }
        .btn-brand:hover { background: #0d7a5c; color: #fff; }
    </style>
    @stack('styles')
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('super-admin.dashboard') }}">
                <i class="fas fa-shield-alt me-2"></i>Super Admin
            </a>
            <div class="navbar-nav me-auto ms-3">
                <a class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active fw-semibold' : '' }}"
                   href="{{ route('super-admin.dashboard') }}">Schools</a>
                <a class="nav-link {{ request()->routeIs('super-admin.menu.*') ? 'active fw-semibold' : '' }}"
                   href="{{ route('super-admin.menu.index') }}">Menu Management</a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-outline-secondary btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

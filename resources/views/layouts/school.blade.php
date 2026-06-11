<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ $user->school->name ?? 'School' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    @include('layouts.partials.sidebar-styles')
    @stack('styles')
</head>
<body>
    @include('layouts.partials.sidebar-nav', [
        'brandIcon' => 'fas fa-school',
        'brandText' => $user->school->name ?? 'School',
        'emptyMessage' => 'No menus assigned. Contact administrator.',
    ])

    <div class="main-content">
        <div class="top-bar">
            <div class="top-bar-left">
                <button type="button" class="btn btn-sm btn-outline-secondary sidebar-toggle-mobile" id="sidebarMobileBtn">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="top-bar-title">@yield('page-title', 'Dashboard')</span>
            </div>
            <div class="user-pill">
                <span class="badge bg-secondary text-capitalize">{{ $user->user_type }}</span>
                <span class="name">{{ $user->name }}</span>
                <form action="{{ route('logout') }}" method="POST">@csrf
                    <button class="btn btn-outline-secondary btn-sm">Logout</button>
                </form>
            </div>
        </div>
        <div class="p-3 p-md-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @include('layouts.partials.sidebar-scripts')
    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Portal') — {{ $user->school->name ?? 'School' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root { --brand: #0a5f47; --brand-light: #0d7a5c; }
        body { background: #f4f6f8; min-height: 100vh; }
        .student-nav {
            background: linear-gradient(135deg, var(--brand), var(--brand-light));
            box-shadow: 0 2px 12px rgba(10, 95, 71, .25);
        }
        .student-nav .navbar-brand, .student-nav .nav-link { color: rgba(255,255,255,.92) !important; }
        .student-nav .nav-link.active, .student-nav .nav-link:hover { color: #fff !important; }
        .student-nav .nav-link.active { font-weight: 600; border-bottom: 2px solid #fff; }
        .btn-brand { background: var(--brand); border-color: var(--brand); color: #fff; }
        .btn-brand:hover { background: var(--brand-light); border-color: var(--brand-light); color: #fff; }
        .text-brand { color: var(--brand) !important; }
    </style>
    @stack('styles')
</head>
<body>
    @php $user = auth()->user(); @endphp
    <nav class="navbar navbar-expand-lg student-nav">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('student.dashboard') }}">
                <i class="fas fa-graduation-cap me-2"></i>{{ $user->school->name }}
            </a>
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#studentNav">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="studentNav">
                <ul class="navbar-nav me-auto ms-lg-3 gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"
                           href="{{ route('student.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}"
                           href="{{ route('student.profile') }}">My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.id-card') ? 'active' : '' }}"
                           href="{{ route('student.id-card') }}" target="_blank">My ID Card</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3 text-white py-2 py-lg-0">
                    <span class="small"><i class="fas fa-user me-1"></i>{{ $user->name }}</span>
                    <form action="{{ route('logout') }}" method="POST">@csrf
                        <button class="btn btn-sm btn-outline-light">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

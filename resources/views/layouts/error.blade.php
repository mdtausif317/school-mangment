<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Error') — School Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root { --brand: #0a5f47; --brand-light: #0d7a5c; }
        body {
            background: linear-gradient(135deg, #f4f6f9 0%, #e8f0ec 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .error-card {
            max-width: 520px;
            margin: 0 auto;
            border: none;
            border-radius: 1rem;
        }
        .error-code {
            font-size: 4.5rem;
            font-weight: 700;
            color: var(--brand);
            line-height: 1;
        }
        .error-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(10, 95, 71, .1);
            color: var(--brand);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin: 0 auto 1.25rem;
        }
        .btn-brand { background: var(--brand); color: #fff; border: none; }
        .btn-brand:hover { background: var(--brand-light); color: #fff; }
        .btn-outline-brand { border-color: var(--brand); color: var(--brand); }
        .btn-outline-brand:hover { background: var(--brand); color: #fff; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="card shadow-sm error-card">
            <div class="card-body text-center p-4 p-md-5">
                <div class="error-icon">
                    <i class="fas @yield('icon', 'fa-exclamation-triangle')"></i>
                </div>
                <div class="error-code mb-2">@yield('code')</div>
                <h4 class="fw-semibold mb-2">@yield('heading')</h4>
                <p class="text-muted mb-4">@yield('message')</p>

                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                        <i class="fas fa-arrow-left me-1"></i> Go Back
                    </button>

                    @auth
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('super-admin.dashboard') }}" class="btn btn-brand">
                                <i class="fas fa-home me-1"></i> Dashboard
                            </a>
                        @elseif(auth()->user()->isSchoolUser())
                            <a href="{{ route('school.dashboard') }}" class="btn btn-brand">
                                <i class="fas fa-home me-1"></i> Dashboard
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-brand">
                            <i class="fas fa-sign-in-alt me-1"></i> School Login
                        </a>
                        <a href="{{ route('super-admin.login') }}" class="btn btn-outline-brand">
                            <i class="fas fa-shield-alt me-1"></i> Super Admin
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <p class="text-center text-muted small mt-4 mb-0">
            School Management System
        </p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

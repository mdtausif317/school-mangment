<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'School Management')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root { --brand: #0a5f47; --brand-light: #0d7a5c; }
        body { background: #f4f6f9; min-height: 100vh; }
        .btn-brand { background: var(--brand); color: #fff; border: none; }
        .btn-brand:hover { background: var(--brand-light); color: #fff; }
        .text-brand { color: var(--brand); }
        .auth-card { max-width: 420px; margin: 0 auto; }
        .auth-logo { font-size: 2rem; color: var(--brand); }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container py-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show auth-card mb-4">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show auth-card mb-4">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

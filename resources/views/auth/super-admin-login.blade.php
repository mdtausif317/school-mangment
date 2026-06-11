@extends('layouts.guest')

@section('title', 'Super Admin Login')

@section('content')
<div class="auth-card">
    <div class="text-center mb-4">
        <div class="auth-logo"><i class="fas fa-shield-alt"></i></div>
        <h4 class="mt-2">Super Admin</h4>
        <p class="text-muted small">Platform management</p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('super-admin.login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn btn-brand w-100">Login</button>
            </form>
        </div>
    </div>

    <p class="text-center mt-3 small text-muted">
        School user? <a href="{{ route('login') }}" class="text-brand">Login here</a>
    </p>
</div>
@endsection

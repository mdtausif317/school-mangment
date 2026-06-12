@extends('layouts.guest')

@section('title', 'Student Login')

@section('content')
<div class="auth-card">
    <div class="text-center mb-4">
        <div class="auth-logo"><i class="fas fa-user-graduate"></i></div>
        <h4 class="mt-2">Student Login</h4>
        <p class="text-muted small">Access your profile &amp; ID card</p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger py-2">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('student.login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                    <div class="form-text">Use the same email as on the student record in your school.</div>
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
        Staff or teacher? <a href="{{ route('school.login') }}" class="text-brand">School login</a>
    </p>
</div>
@endsection

@extends('layouts.school')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">School</div>
                <div class="fs-5 fw-semibold">{{ $user->school->name }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Your Role</div>
                <div class="fs-5 fw-semibold">{{ $user->designation?->name ?? ucfirst($user->user_type) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Account Type</div>
                <div class="fs-5 fw-semibold text-capitalize">{{ $user->user_type }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        <h5 class="card-title">Welcome, {{ $user->name }}</h5>
        <p class="text-muted mb-0">
            Use the sidebar to navigate. Menu access is controlled by your designation and assigned permissions.
        </p>
    </div>
</div>
@endsection

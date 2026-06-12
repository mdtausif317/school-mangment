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
        <p class="text-muted">Use the sidebar to navigate. Menu access is controlled by your designation and assigned permissions.</p>
        <div class="d-flex flex-wrap gap-2">
            @if(Route::has('school.attendance-manage'))
                <a href="{{ route('school.attendance-manage') }}" class="btn btn-sm btn-outline-brand">Mark Attendance</a>
            @endif
            @if(Route::has('school.fees-collect'))
                <a href="{{ route('school.fees-collect') }}" class="btn btn-sm btn-outline-brand">Collect Fees</a>
            @endif
            @if(Route::has('school.reports'))
                <a href="{{ route('school.reports') }}" class="btn btn-sm btn-outline-brand">View Reports</a>
            @endif
            @if(Route::has('school.student-add'))
                <a href="{{ route('school.student-add') }}" class="btn btn-sm btn-outline-brand">Add Student</a>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-outline-brand { border-color: #0a5f47; color: #0a5f47; }
    .btn-outline-brand:hover { background: #0a5f47; color: #fff; }
</style>
@endpush

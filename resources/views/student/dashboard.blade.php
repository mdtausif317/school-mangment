@extends('layouts.student')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mx-auto mb-3 rounded-circle overflow-hidden border"
                     style="width:120px;height:120px;background:#f1f5f9;">
                    @if($student->photoUrl())
                        <img src="{{ $student->photoUrl() }}" alt="{{ $student->name }}" class="w-100 h-100" style="object-fit:cover;">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                            <i class="fas fa-user fa-3x"></i>
                        </div>
                    @endif
                </div>
                <h5 class="mb-1">{{ $student->name }}</h5>
                <p class="text-muted small mb-0">Roll No: {{ $student->roll_no }}</p>
                <p class="text-brand small fw-semibold mb-0">{{ $student->schoolClass?->displayName() ?? '—' }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Welcome back!</h5>
                <p class="text-muted mb-4">
                    You are logged in to <strong>{{ $user->school->name }}</strong> student portal.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('student.profile') }}" class="btn btn-brand">
                        <i class="fas fa-id-badge me-1"></i> View Profile
                    </a>
                    <a href="{{ route('student.id-card') }}" class="btn btn-outline-secondary" target="_blank">
                        <i class="fas fa-id-card me-1"></i> Print ID Card
                    </a>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Guardian</div>
                        <div class="fw-semibold">{{ $student->guardian_name ?? '—' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Contact</div>
                        <div class="fw-semibold">{{ $student->phone ?? $student->email ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

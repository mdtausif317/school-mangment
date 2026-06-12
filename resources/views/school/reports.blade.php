@extends('layouts.school')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Active Students</div>
                <div class="fs-4 fw-semibold">{{ $stats['students'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Present Today</div>
                <div class="fs-4 fw-semibold text-success">{{ $stats['present_today'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Absent Today</div>
                <div class="fs-4 fw-semibold text-danger">{{ $stats['absent_today'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Fee Revenue (This Month)</div>
                <div class="fs-4 fw-semibold text-brand">₹{{ number_format($stats['fee_month'], 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-calendar-check me-2 text-brand"></i>Attendance Report</h5>
                <p class="text-muted">View class-wise attendance summary by date range.</p>
                <a href="{{ route('school.reports-attendance') }}" class="btn btn-outline-brand btn-sm">Open Report</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-rupee-sign me-2 text-brand"></i>Fee Revenue Report</h5>
                <p class="text-muted">Total fee collection: <strong>₹{{ number_format($stats['fee_total'], 2) }}</strong></p>
                <a href="{{ route('school.reports-fees') }}" class="btn btn-outline-brand btn-sm">Open Report</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-brand { color: #0a5f47; }
    .btn-outline-brand { border-color: #0a5f47; color: #0a5f47; }
    .btn-outline-brand:hover { background: #0a5f47; color: #fff; }
</style>
@endpush

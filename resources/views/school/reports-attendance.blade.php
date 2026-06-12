@extends('layouts.school')

@section('title', 'Attendance Report')
@section('page-title', 'Attendance Report')

@section('content')
<div class="mb-3">
    <a href="{{ route('school.reports') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Reports
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('school.reports-attendance') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-select" required>
                    <option value="">Select class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected($classId === $class->id)>{{ $class->displayName() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">From</label>
                <input type="date" name="from" class="form-control" value="{{ $from }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To</label>
                <input type="date" name="to" class="form-control" value="{{ $to }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-brand w-100">Generate</button>
            </div>
        </form>
    </div>
</div>

@if($classId > 0)
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">Present</small><div class="fs-5 fw-semibold text-success">{{ $summary['present'] }}</div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">Absent</small><div class="fs-5 fw-semibold text-danger">{{ $summary['absent'] }}</div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">Late</small><div class="fs-5 fw-semibold text-warning">{{ $summary['late'] }}</div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">Leave</small><div class="fs-5 fw-semibold text-secondary">{{ $summary['leave'] }}</div></div></div></div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Roll No</th>
                        <th>Name</th>
                        <th class="text-center">Present</th>
                        <th class="text-center">Absent</th>
                        <th class="text-center">Late</th>
                        <th class="text-center">Leave</th>
                        <th class="text-center">Total Days</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td><code>{{ $row['student']->roll_no }}</code></td>
                            <td class="fw-semibold">{{ $row['student']->name }}</td>
                            <td class="text-center">{{ $row['counts']['present'] }}</td>
                            <td class="text-center">{{ $row['counts']['absent'] }}</td>
                            <td class="text-center">{{ $row['counts']['late'] }}</td>
                            <td class="text-center">{{ $row['counts']['leave'] }}</td>
                            <td class="text-center fw-semibold">{{ $row['total_days'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No students in this class.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="alert alert-secondary">Select a class and date range to view the attendance report.</div>
@endif
@endsection

@push('styles')
<style>
    .btn-brand { background: #0a5f47; color: #fff; border: none; }
    .btn-brand:hover { background: #0d7a5c; color: #fff; }
</style>
@endpush

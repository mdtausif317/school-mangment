@extends('layouts.school')

@section('title', 'Mark Attendance')
@section('page-title', 'Mark Attendance')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('school.attendance-manage') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-select" required onchange="this.form.submit()">
                    <option value="">Select class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected($classId === $class->id)>{{ $class->displayName() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-brand w-100">Load</button>
            </div>
        </form>
    </div>
</div>

@if($classId > 0)
    @if($students->isEmpty())
        <div class="alert alert-info">No active students in this class.</div>
    @else
        <form method="POST" action="{{ route('school.attendance-manage.store') }}">
            @csrf
            <input type="hidden" name="class_id" value="{{ $classId }}">
            <input type="hidden" name="date" value="{{ $date }}">

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Roll No</th>
                                <th>Name</th>
                                <th style="min-width:180px">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php $current = $attendance[$student->id] ?? 'present'; @endphp
                                <tr>
                                    <td><code>{{ $student->roll_no }}</code></td>
                                    <td class="fw-semibold">{{ $student->name }}</td>
                                    <td>
                                        <select name="attendance[{{ $student->id }}]" class="form-select form-select-sm">
                                            @foreach(\App\Models\StudentAttendance::statusOptions() as $value => $label)
                                                <option value="{{ $value }}" @selected($current === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-0 text-end">
                    <button type="submit" class="btn btn-brand">
                        <i class="fas fa-save me-1"></i> Save Attendance
                    </button>
                </div>
            </div>
        </form>
    @endif
@else
    <div class="alert alert-secondary mb-0">Select a class and date to mark attendance.</div>
@endif
@endsection

@push('styles')
<style>
    .btn-brand { background: #0a5f47; color: #fff; border: none; }
    .btn-brand:hover { background: #0d7a5c; color: #fff; }
</style>
@endpush

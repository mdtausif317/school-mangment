@extends('layouts.school')

@section('title', 'Students')
@section('page-title', 'Students')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">View and manage students in your school.</p>
    @if(Route::has('school.student-add'))
        <a href="{{ route('school.student-add') }}" class="btn btn-brand btn-sm">
            <i class="fas fa-plus me-1"></i> Add Student
        </a>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Roll No</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Phone</th>
                    <th>Guardian</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td><code>{{ $student->roll_no }}</code></td>
                        <td class="fw-semibold">{{ $student->name }}</td>
                        <td>{{ $student->schoolClass?->displayName() ?? '—' }}</td>
                        <td>{{ $student->phone ?? '—' }}</td>
                        <td>{{ $student->guardian_name ?? '—' }}</td>
                        <td>
                            @if($student->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No students yet.
                            @if(Route::has('school.student-add'))
                                <a href="{{ route('school.student-add') }}">Add one</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>.btn-brand { background: #0a5f47; color: #fff; border: none; } .btn-brand:hover { background: #0d7a5c; color: #fff; }</style>
@endpush

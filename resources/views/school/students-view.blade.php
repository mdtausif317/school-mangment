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
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Photo</th>
                    <th>Roll No</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>
                            @if($student->photoUrl())
                                <img src="{{ $student->photoUrl() }}" alt="{{ $student->name }}"
                                     class="rounded border" style="width:40px;height:50px;object-fit:cover;">
                            @else
                                <span class="d-inline-flex align-items-center justify-content-center rounded border bg-light text-muted"
                                      style="width:40px;height:50px;font-size:.7rem;">N/A</span>
                            @endif
                        </td>
                        <td><code>{{ $student->roll_no }}</code></td>
                        <td class="fw-semibold">{{ $student->name }}</td>
                        <td>{{ $student->schoolClass?->displayName() ?? '—' }}</td>
                        <td>{{ $student->phone ?? '—' }}</td>
                        <td>
                            @if($student->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('school.students-view.edit', $student) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('school.students-view.card', $student) }}" class="btn btn-sm btn-outline-brand" target="_blank" title="Print ID Card">
                                <i class="fas fa-id-card"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
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
<style>
    .btn-brand { background: #0a5f47; color: #fff; border: none; }
    .btn-brand:hover { background: #0d7a5c; color: #fff; }
    .btn-outline-brand { border-color: #0a5f47; color: #0a5f47; }
    .btn-outline-brand:hover { background: #0a5f47; color: #fff; }
</style>
@endpush

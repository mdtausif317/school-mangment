@extends('layouts.school')

@section('title', 'Classes')
@section('page-title', 'Classes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Manage classes and sections for your school.</p>
    @if(Route::has('school.class-add'))
        <a href="{{ route('school.class-add') }}" class="btn btn-brand btn-sm">
            <i class="fas fa-plus me-1"></i> Add Class
        </a>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Students</th>
                    <th>Status</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classes as $class)
                    <tr>
                        <td class="fw-semibold">{{ $class->name }}</td>
                        <td>{{ $class->section ?? '—' }}</td>
                        <td>{{ $class->students_count }}</td>
                        <td>
                            @if($class->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $class->description ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No classes yet.
                            @if(Route::has('school.class-add'))
                                <a href="{{ route('school.class-add') }}">Add one</a>
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

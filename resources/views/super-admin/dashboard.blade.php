@extends('layouts.super-admin')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Schools</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('super-admin.menu.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-bars me-1"></i> Menu Management
        </a>
        <a href="{{ route('super-admin.schools.create') }}" class="btn btn-brand">
            <i class="fas fa-plus me-1"></i> Create School
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Email</th>
                    <th>Users</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schools as $school)
                    <tr>
                        <td>{{ $school->id }}</td>
                        <td class="fw-semibold">{{ $school->name }}</td>
                        <td><code>{{ $school->slug }}</code></td>
                        <td>{{ $school->email ?? '—' }}</td>
                        <td>{{ $school->users_count }}</td>
                        <td>
                            @if($school->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $school->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No schools yet. <a href="{{ route('super-admin.schools.create') }}">Create one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

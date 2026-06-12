@extends('layouts.super-admin')

@section('title', 'Schools')
@section('page-title', 'Schools')

@push('styles')
<style>
    .btn-outline-brand { border-color: #0a5f47; color: #0a5f47; }
    .btn-outline-brand:hover { background: #0a5f47; color: #fff; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('super-admin.schools.create') }}" class="btn btn-brand">
        <i class="fas fa-plus me-1"></i> School Add
    </a>
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
                    <th>Portal</th>
                    <th>Subscription</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
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
                        <td>
                            @if($school->portal_enabled)
                                <span class="badge bg-success">Enabled</span>
                            @else
                                <span class="badge bg-secondary">Disabled</span>
                            @endif
                        </td>
                        <td>
                            @php $subLabel = $subscriptionLabels[$school->id] ?? '—'; @endphp
                            @if(str_starts_with($subLabel, 'Active'))
                                <span class="badge bg-success">{{ $subLabel }}</span>
                            @elseif($school->portal_enabled)
                                <span class="badge bg-danger">{{ $subLabel }}</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $school->created_at->format('M d, Y') }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('super-admin.schools.edit', $school) }}" class="btn btn-sm btn-outline-secondary me-1">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <a href="{{ route('super-admin.schools.access', $school) }}" class="btn btn-sm btn-outline-brand">
                                <i class="fas fa-key me-1"></i> Access
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            No schools yet. <a href="{{ route('super-admin.schools.create') }}">Create one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

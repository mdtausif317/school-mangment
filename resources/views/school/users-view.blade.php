@extends('layouts.school')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Manage staff and teachers for <strong>{{ $school->name }}</strong>. Students are managed under <strong>Students</strong>.</p>
    </div>
    @if(Route::has('school.user-add'))
        <a href="{{ route('school.user-add') }}" class="btn btn-brand btn-sm">
            <i class="fas fa-plus me-1"></i> Add User
        </a>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Designation</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td class="fw-semibold">{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->designation?->name ?? '—' }}</td>
                        <td class="text-capitalize">{{ $u->user_type }}</td>
                        <td>
                            @if($u->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('school.users-view.access', $u) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-key me-1"></i> Manage Access
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No users in this school yet.</td>
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

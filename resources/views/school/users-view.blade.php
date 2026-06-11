@extends('layouts.school')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Manage school users and their page access.</p>
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
                        <td class="text-end">
                            <a href="{{ route('school.users-view.access', $u) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-key me-1"></i> Manage Access
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No users yet.</td>
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

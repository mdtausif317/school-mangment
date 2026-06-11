@extends('layouts.school')

@section('title', 'Designations')
@section('page-title', 'Designations')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Designations control default page access for users.</p>
    <a href="{{ route('school.designations.create') }}" class="btn btn-brand btn-sm">
        <i class="fas fa-plus me-1"></i> Add Designation
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Users</th>
                </tr>
            </thead>
            <tbody>
                @forelse($designations as $d)
                    <tr>
                        <td class="fw-semibold">{{ $d->name }}</td>
                        <td><code>{{ $d->slug }}</code></td>
                        <td>{{ $d->users_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">No designations yet.</td>
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

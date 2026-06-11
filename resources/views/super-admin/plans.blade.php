@extends('layouts.super-admin')

@section('title', 'Subscription Packages')
@section('page-title', 'Subscription Packages')

@push('styles')
<style>
    .text-brand { color: #0a5f47; }
    .btn-outline-brand { border-color: #0a5f47; color: #0a5f47; }
    .btn-outline-brand:hover { background: #0a5f47; color: #fff; }
</style>
@endpush

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-plus-circle me-2 text-brand"></i>Add Package
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.plans.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Package Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="price" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror"
                                   value="{{ old('price', 0) }}" required>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">Days <span class="text-danger">*</span></label>
                            <input type="number" name="duration_days" min="1" class="form-control @error('duration_days') is-invalid @enderror"
                                   value="{{ old('duration_days', 30) }}" required>
                            @error('duration_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Max Users</label>
                        <input type="number" name="max_users" min="1" class="form-control" value="{{ old('max_users') }}"
                               placeholder="Unlimited">
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" checked>
                        <label class="form-check-label" for="is_active">Active (schools can select this package)</label>
                    </div>
                    <button type="submit" class="btn btn-brand w-100">
                        <i class="fas fa-save me-1"></i> Save Package
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-box me-2 text-brand"></i>All Packages
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Max Users</th>
                            <th>Status</th>
                            <th class="text-end">Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plans as $plan)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $plan->name }}</div>
                                    @if($plan->description)
                                        <div class="text-muted small">{{ $plan->description }}</div>
                                    @endif
                                </td>
                                <td>₹{{ number_format($plan->price, 2) }}</td>
                                <td>{{ $plan->duration_days }} days</td>
                                <td>{{ $plan->max_users ?? 'Unlimited' }}</td>
                                <td>
                                    @if($plan->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-brand" data-bs-toggle="modal"
                                            data-bs-target="#editPlan{{ $plan->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>

                            <div class="modal fade" id="editPlan{{ $plan->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('super-admin.plans.update', $plan) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit — {{ $plan->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Name</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $plan->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description" class="form-control" rows="2">{{ $plan->description }}</textarea>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label">Price (₹)</label>
                                                        <input type="number" name="price" step="0.01" min="0" class="form-control" value="{{ $plan->price }}" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label">Days</label>
                                                        <input type="number" name="duration_days" min="1" class="form-control" value="{{ $plan->duration_days }}" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Max Users</label>
                                                    <input type="number" name="max_users" min="1" class="form-control" value="{{ $plan->max_users }}">
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="active{{ $plan->id }}"
                                                           {{ $plan->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="active{{ $plan->id }}">Active</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-brand">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No packages yet. Add one on the left.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

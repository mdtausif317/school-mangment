@extends('layouts.super-admin')

@section('title', 'Edit School')
@section('page-title', 'Edit School — '.$school->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="mb-3">
            <a href="{{ route('super-admin.school-view') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Schools
            </a>
        </div>

        <form action="{{ route('super-admin.schools.update', $school) }}" method="POST" class="card border-0 shadow-sm">
            @csrf
            @method('PUT')
            <div class="card-body p-4">
                <h6 class="text-muted text-uppercase small mb-3">School Details</h6>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">School Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $school->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $school->slug) }}">
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $school->email) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $school->phone) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $school->address) }}</textarea>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="text-muted text-uppercase small mb-3">Status &amp; Portal</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                                   {{ old('is_active', $school->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-medium" for="is_active">School is active</label>
                            <p class="text-muted small mb-0">Inactive schools cannot use the portal.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="portal_enabled" value="1" class="form-check-input" id="portal_enabled"
                                   {{ old('portal_enabled', $school->portal_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label fw-medium" for="portal_enabled">Enable school portal</label>
                            <p class="text-muted small mb-0">
                                Menu access &amp; subscription:
                                <a href="{{ route('super-admin.schools.access', $school) }}">Manage access</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-top p-4 d-flex justify-content-between align-items-center">
                <span class="text-muted small">Created {{ $school->created_at->format('M d, Y') }}</span>
                <div>
                    <a href="{{ route('super-admin.schools.access', $school) }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-key me-1"></i> Menu &amp; Subscription
                    </a>
                    <button type="submit" class="btn btn-brand">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-brand { background: #0a5f47; color: #fff; border: none; }
    .btn-brand:hover { background: #0d7a5c; color: #fff; }
</style>
@endpush

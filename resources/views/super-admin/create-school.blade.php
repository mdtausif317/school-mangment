@extends('layouts.super-admin')

@section('title', 'School Add')
@section('page-title', 'School Add')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form action="{{ route('super-admin.schools.store') }}" method="POST" class="card border-0 shadow-sm">
            @csrf
            <div class="card-body p-4">
                <h6 class="text-muted text-uppercase small mb-3">School Details</h6>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">School Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug') }}" placeholder="auto-generated">
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="text-muted text-uppercase small mb-3">School Admin Account</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Admin Name <span class="text-danger">*</span></label>
                        <input type="text" name="admin_name" class="form-control @error('admin_name') is-invalid @enderror"
                               value="{{ old('admin_name') }}" required>
                        @error('admin_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Admin Email <span class="text-danger">*</span></label>
                        <input type="email" name="admin_email" class="form-control @error('admin_email') is-invalid @enderror"
                               value="{{ old('admin_email') }}" required>
                        @error('admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="admin_password" class="form-control @error('admin_password') is-invalid @enderror" required>
                        @error('admin_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="admin_password_confirmation" class="form-control" required>
                    </div>
                </div>

                <hr class="my-4">

                <div class="form-check mb-3">
                    <input type="checkbox" name="portal_enabled" value="1" class="form-check-input" id="portal_enabled"
                           {{ old('portal_enabled', true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-medium" for="portal_enabled">
                        Enable school portal access
                    </label>
                    <p class="text-muted small mb-0 mt-1">
                        School admin can login and manage which users get access to which pages.
                    </p>
                </div>

                @if($plans->isNotEmpty())
                    <div class="mb-0">
                        <label class="form-label">Initial Subscription Package (optional)</label>
                        <select name="subscription_plan_id" class="form-select">
                            <option value="">No subscription — assign later</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" {{ old('subscription_plan_id') == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }} — ₹{{ number_format($plan->price, 2) }} / {{ $plan->duration_days }} days
                                </option>
                            @endforeach
                        </select>
                        <p class="text-muted small mt-1 mb-0">Activates only when portal access is enabled.</p>
                    </div>
                @endif
            </div>
            <div class="card-footer bg-white border-top p-4">
                <button type="submit" class="btn btn-brand">
                    <i class="fas fa-save me-1"></i> School Add
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

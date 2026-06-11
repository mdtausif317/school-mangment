@extends('layouts.super-admin')

@section('title', 'School Access')
@section('page-title', 'Portal Access — '.$school->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('super-admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Schools
            </a>
            <span class="badge bg-secondary">{{ $school->slug }}</span>
        </div>

        <form action="{{ route('super-admin.schools.access.update', $school) }}" method="POST" class="card border-0 shadow-sm">
            @csrf
            @method('PUT')
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-key me-2 text-brand"></i> School Portal Access
            </div>
            <div class="card-body p-4">
                <div class="form-check">
                    <input type="checkbox" name="portal_enabled" value="1" class="form-check-input" id="portal_enabled"
                           {{ old('portal_enabled', $school->portal_enabled) ? 'checked' : '' }}>
                    <label class="form-check-label fw-medium" for="portal_enabled">
                        Enable school portal access
                    </label>
                    <p class="text-muted small mb-0 mt-2">
                        When enabled, school admin can login and manage users, designations, and page access inside the school portal.
                    </p>
                </div>
            </div>
            <div class="card-footer bg-white border-top p-4">
                <button type="submit" class="btn btn-brand">
                    <i class="fas fa-save me-1"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>.text-brand { color: #0a5f47; }</style>
@endpush

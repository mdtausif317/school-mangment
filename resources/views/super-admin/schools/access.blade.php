@extends('layouts.super-admin')

@section('title', 'School Access')
@section('page-title', 'Menu Access — '.$school->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('super-admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to Schools
                </a>
            </div>
            <span class="badge bg-secondary">{{ $school->slug }}</span>
        </div>

        <form action="{{ route('super-admin.schools.access.update', $school) }}" method="POST" class="card border-0 shadow-sm">
            @csrf
            @method('PUT')
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-key me-2 text-brand"></i>
                Set page access for <strong>{{ $school->name }}</strong>
            </div>
            <div class="card-body p-4">
                @include('super-admin.schools.partials.access-matrix', [
                    'menus' => $menus,
                    'designations' => $designations,
                    'useDesignationSlugs' => $useDesignationSlugs,
                    'currentAccess' => old('menu_access', $currentAccess),
                ])
            </div>
            <div class="card-footer bg-white border-top p-4">
                <button type="submit" class="btn btn-brand">
                    <i class="fas fa-save me-1"></i> Save Access
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>.text-brand { color: #0a5f47; }</style>
@endpush

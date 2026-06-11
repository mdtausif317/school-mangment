@extends('layouts.school')

@section('title', 'Add Designation')
@section('page-title', 'Add Designation')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('school.designations.store') }}" method="POST" class="card border-0 shadow-sm">
            @csrf
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label">Designation Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required placeholder="e.g. Accountant">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                </div>

                <h6 class="text-muted text-uppercase small mb-2">Page Access</h6>
                @include('school.access-list', [
                    'menus' => $menus,
                    'selected' => old('menu_ids', []),
                ])
            </div>
            <div class="card-footer bg-white border-top p-4">
                <a href="{{ route('school.designations.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-brand">Create Designation</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>.btn-brand { background: #0a5f47; color: #fff; border: none; } .btn-brand:hover { background: #0d7a5c; color: #fff; }</style>
@endpush

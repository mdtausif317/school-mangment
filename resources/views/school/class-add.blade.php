@extends('layouts.school')

@section('title', 'Add Class')
@section('page-title', 'Add Class')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('school.class-add.store') }}" method="POST" class="card border-0 shadow-sm">
            @csrf
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Class Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="e.g. Class 10" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Section</label>
                        <input type="text" name="section" class="form-control @error('section') is-invalid @enderror"
                               value="{{ old('section') }}" placeholder="e.g. A">
                        @error('section')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"
                                  placeholder="Optional notes about this class">{{ old('description') }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-top p-4">
                <a href="{{ route('school.classes-view') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-brand">Create Class</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>.btn-brand { background: #0a5f47; color: #fff; border: none; } .btn-brand:hover { background: #0d7a5c; color: #fff; }</style>
@endpush

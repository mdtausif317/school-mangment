@extends('layouts.school')

@section('title', 'Add Student')
@section('page-title', 'Add Student')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        @if($classes->isEmpty())
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No classes found. Please <a href="{{ route('school.class-add') }}">add a class</a> first.
            </div>
        @endif

        <form action="{{ route('school.student-add.store') }}" method="POST" class="card border-0 shadow-sm">
            @csrf
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Class <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required
                                {{ $classes->isEmpty() ? 'disabled' : '' }}>
                            <option value="">Select class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->displayName() }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Roll No <span class="text-danger">*</span></label>
                        <input type="text" name="roll_no" class="form-control @error('roll_no') is-invalid @enderror"
                               value="{{ old('roll_no') }}" required>
                        @error('roll_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Student Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Guardian Name</label>
                        <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
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
                <a href="{{ route('school.students-view') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-brand" {{ $classes->isEmpty() ? 'disabled' : '' }}>Add Student</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>.btn-brand { background: #0a5f47; color: #fff; border: none; } .btn-brand:hover { background: #0d7a5c; color: #fff; }</style>
@endpush

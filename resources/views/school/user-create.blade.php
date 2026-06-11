@extends('layouts.school')

@section('title', 'Add User')
@section('page-title', 'Add User')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('school.users.store') }}" method="POST" class="card border-0 shadow-sm">
            @csrf
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Designation <span class="text-danger">*</span></label>
                        <select name="designation_id" class="form-select" required>
                            <option value="">Select designation</option>
                            @foreach($designations as $d)
                                <option value="{{ $d->id }}" {{ old('designation_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">User gets page access from this designation automatically.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">User Type <span class="text-danger">*</span></label>
                        <select name="user_type" class="form-select" required>
                            <option value="staff" {{ old('user_type') === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="teacher" {{ old('user_type') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="student" {{ old('user_type') === 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-top p-4">
                <a href="{{ route('school.users.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-brand">Create User</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>.btn-brand { background: #0a5f47; color: #fff; border: none; } .btn-brand:hover { background: #0d7a5c; color: #fff; }</style>
@endpush

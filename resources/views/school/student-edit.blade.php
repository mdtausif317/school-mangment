@extends('layouts.school')

@section('title', 'Edit Student')
@section('page-title', 'Edit Student — '.$student->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <form action="{{ route('school.students-view.update', $student) }}" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm">
            @csrf
            @method('PUT')
            <div class="card-body p-4">
                <div class="row g-3">
                    @include('school.partials.student-form-fields', ['classes' => $classes, 'student' => $student])
                </div>
            </div>
            <div class="card-footer bg-white border-top p-4 d-flex justify-content-between">
                <a href="{{ route('school.students-view') }}" class="btn btn-outline-secondary">Cancel</a>
                <div>
                    <a href="{{ route('school.students-view.card', $student) }}" class="btn btn-outline-brand me-2" target="_blank">
                        <i class="fas fa-id-card me-1"></i> Print Card
                    </a>
                    <button type="submit" class="btn btn-brand">Save Changes</button>
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
    .btn-outline-brand { border-color: #0a5f47; color: #0a5f47; }
    .btn-outline-brand:hover { background: #0a5f47; color: #fff; }
</style>
@endpush

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

        <form action="{{ route('school.student-add.store') }}" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm">
            @csrf
            <div class="card-body p-4">
                <div class="row g-3">
                    @include('school.partials.student-form-fields', ['classes' => $classes])
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

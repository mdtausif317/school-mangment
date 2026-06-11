@extends('layouts.school')

@section('title', 'Manage Access')
@section('page-title', 'Manage Access — '.$user->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-3">
            <a href="{{ route('school.users.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Users
            </a>
        </div>

        <form action="{{ route('school.users.access.update', $user) }}" method="POST" class="card border-0 shadow-sm">
            @csrf
            @method('PUT')
            <div class="card-header bg-white fw-semibold">Page Access for {{ $user->name }}</div>
            <div class="card-body p-4">
                @if($user->designation)
                    <p class="small text-muted">
                        Designation: <strong>{{ $user->designation->name }}</strong> —
                        checked pages below include designation access. You can add or remove extra access here.
                    </p>
                @endif

                @include('school.access-list', [
                    'menus' => $menus,
                    'selected' => old('menu_ids', $selected),
                ])
            </div>
            <div class="card-footer bg-white border-top p-4">
                <button type="submit" class="btn btn-brand">Save Access</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>.btn-brand { background: #0a5f47; color: #fff; border: none; } .btn-brand:hover { background: #0d7a5c; color: #fff; }</style>
@endpush

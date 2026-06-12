@extends('layouts.super-admin')

@section('title', 'School Access')
@section('page-title', 'Menu Access — '.$school->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('super-admin.school-view') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Schools
            </a>
            <span class="badge bg-secondary">{{ $school->slug }}</span>
        </div>

        <form action="{{ route('super-admin.schools.access.update', $school) }}" method="POST" class="card border-0 shadow-sm mb-4">
            @csrf
            @method('PUT')
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-key me-2 text-brand"></i> School Menu Access
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-4">
                    Select which pages this school can use in the school portal.
                    School admin can then assign these pages to users and designations.
                    @if($school->portal_enabled)
                        <span class="badge bg-success ms-1">Portal enabled</span>
                    @else
                        <span class="badge bg-secondary ms-1">Portal disabled</span>
                    @endif
                </p>

                @include('school.access-list', [
                    'menus' => $menus,
                    'selected' => old('menu_ids', $selectedMenuIds),
                    'helpText' => 'Check the menus this school is allowed to use. Unchecked menus will be hidden from the school portal.',
                ])
            </div>
            <div class="card-footer bg-white border-top p-4 d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    At least one menu must be checked to enable portal access.
                </span>
                <button type="submit" class="btn btn-brand">
                    <i class="fas fa-save me-1"></i> Save Menu Access
                </button>
            </div>
        </form>

        <form action="{{ route('super-admin.schools.id-card.update', $school) }}" method="POST"
              enctype="multipart/form-data" class="card border-0 shadow-sm mb-4">
            @csrf
            @method('PUT')
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-id-card me-2 text-brand"></i> Student ID Card Design
            </div>
            <div class="card-body p-4">
                @include('super-admin.partials.id-card-settings', [
                    'settings' => $idCardSettings,
                    'school' => $school,
                ])
            </div>
            <div class="card-footer bg-white border-top p-4 text-end">
                <button type="submit" class="btn btn-brand">
                    <i class="fas fa-save me-1"></i> Save ID Card Design
                </button>
            </div>
        </form>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="fas fa-credit-card me-2 text-brand"></i> Subscription</span>
                @if(str_starts_with($subscriptionStatus, 'Active'))
                    <span class="badge bg-success">{{ $subscriptionStatus }}</span>
                @else
                    <span class="badge bg-danger">{{ $subscriptionStatus }}</span>
                @endif
            </div>
            <div class="card-body p-4">
                @if($activeSubscription)
                    <p class="text-muted small mb-3">
                        Current plan: <strong>{{ $activeSubscription->plan->name ?? '—' }}</strong>
                    </p>
                @endif

                @if($plans->isEmpty())
                    <p class="text-muted mb-0">
                        No packages available.
                        <a href="{{ route('super-admin.plans.index') }}">Create packages</a> first.
                    </p>
                @else
                    <form action="{{ route('super-admin.schools.subscription.assign', $school) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Assign / Renew Package</label>
                            <select name="subscription_plan_id" class="form-select" required>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">
                                        {{ $plan->name }} — ₹{{ number_format($plan->price, 2) }} / {{ $plan->duration_days }} days
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Reference (optional)</label>
                            <input type="text" name="payment_reference" class="form-control" placeholder="Receipt / transaction ID">
                        </div>
                        <button type="submit" class="btn btn-brand">
                            <i class="fas fa-bolt me-1"></i> Activate Subscription
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>.text-brand { color: #0a5f47; }</style>
@endpush

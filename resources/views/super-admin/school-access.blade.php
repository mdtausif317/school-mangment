@extends('layouts.super-admin')

@section('title', 'School Access')
@section('page-title', 'Portal Access — '.$school->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
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
                    <i class="fas fa-save me-1"></i> Save Portal Access
                </button>
            </div>
        </form>

        @if($school->portal_enabled)
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
                                <p class="text-muted small mt-2 mb-0">
                                    Super admin manual activation — use after offline payment received.
                                </p>
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
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>.text-brand { color: #0a5f47; }</style>
@endpush

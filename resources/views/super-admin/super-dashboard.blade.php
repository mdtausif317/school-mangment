@extends('layouts.super-admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .text-brand { color: #0a5f47; }
    .stat-card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
        transition: transform .15s, box-shadow .15s;
        height: 100%;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 1.25rem;
    }
    .stat-icon.green { background: rgba(10,95,71,.12); color: #0a5f47; }
    .stat-icon.blue { background: rgba(13,110,253,.12); color: #0d6efd; }
    .stat-icon.orange { background: rgba(253,126,20,.12); color: #fd7e14; }
    .stat-icon.red { background: rgba(220,53,69,.12); color: #dc3545; }
    .stat-icon.purple { background: rgba(111,66,193,.12); color: #6f42c1; }
    .quick-link {
        display: flex; align-items: center; gap: .75rem;
        padding: .85rem 1rem; border-radius: 10px;
        text-decoration: none; color: inherit;
        border: 1px solid #e9ecef; background: #fff;
        transition: border-color .15s, background .15s;
    }
    .quick-link:hover { border-color: #0a5f47; background: rgba(10,95,71,.04); color: inherit; }
    .quick-link i { width: 36px; height: 36px; border-radius: 8px; background: rgba(10,95,71,.1); color: #0a5f47; display: flex; align-items: center; justify-content: center; }
    .btn-outline-brand { border-color: #0a5f47; color: #0a5f47; }
    .btn-outline-brand:hover { background: #0a5f47; color: #fff; }
</style>
@endpush

@section('content')
<div class="mb-4">
    <h5 class="fw-semibold mb-1">Welcome back, {{ auth()->user()->name }}</h5>
    <p class="text-muted mb-0 small">Platform overview — schools, subscriptions & payments at a glance.</p>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon green"><i class="fas fa-school"></i></div>
                <div>
                    <div class="text-muted small">Total Schools</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_schools'] }}</div>
                    <div class="small text-muted">{{ $stats['portal_enabled'] }} portal enabled</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon blue"><i class="fas fa-users"></i></div>
                <div>
                    <div class="text-muted small">School Users</div>
                    <div class="fs-4 fw-bold">{{ number_format($stats['total_users']) }}</div>
                    <div class="small text-muted">Across all schools</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="text-muted small">Active Subscriptions</div>
                    <div class="fs-4 fw-bold">{{ $stats['active_subscriptions'] }}</div>
                    <div class="small {{ $stats['expired_portals'] > 0 ? 'text-danger' : 'text-muted' }}">
                        {{ $stats['expired_portals'] }} expired portal{{ $stats['expired_portals'] !== 1 ? 's' : '' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon purple"><i class="fas fa-rupee-sign"></i></div>
                <div>
                    <div class="text-muted small">Total Revenue</div>
                    <div class="fs-4 fw-bold">₹{{ number_format($stats['total_revenue'], 0) }}</div>
                    <div class="small text-muted">{{ $stats['active_plans'] }} active packages</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($stats['pending_payments'] > 0)
    <div class="alert alert-warning d-flex align-items-center justify-content-between mb-4">
        <span>
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>{{ $stats['pending_payments'] }}</strong> payment request{{ $stats['pending_payments'] !== 1 ? 's' : '' }} waiting for review.
        </span>
        <a href="{{ route('super-admin.payments.index') }}" class="btn btn-sm btn-warning">Review Payments</a>
    </div>
@endif

<div class="row g-4">
    {{-- Quick Actions --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-bolt text-brand me-2"></i>Quick Actions
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('super-admin.schools.create') }}" class="quick-link">
                    <i class="fas fa-plus"></i>
                    <span>Add New School</span>
                </a>
                <a href="{{ route('super-admin.school-view') }}" class="quick-link">
                    <i class="fas fa-list"></i>
                    <span>View All Schools</span>
                </a>
                <a href="{{ route('super-admin.plans.index') }}" class="quick-link">
                    <i class="fas fa-box"></i>
                    <span>Manage Packages</span>
                </a>
                <a href="{{ route('super-admin.payments.index') }}" class="quick-link">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Subscription Payments</span>
                </a>
                <a href="{{ route('super-admin.menu.index') }}" class="quick-link">
                    <i class="fas fa-bars"></i>
                    <span>Menu Management</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Schools --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="fas fa-school text-brand me-2"></i>Recent Schools</span>
                <a href="{{ route('super-admin.school-view') }}" class="btn btn-sm btn-outline-brand">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>School</th>
                            <th>Users</th>
                            <th>Portal</th>
                            <th>Added</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSchools as $school)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $school->name }}</div>
                                    <code class="small">{{ $school->slug }}</code>
                                </td>
                                <td>{{ $school->users_count }}</td>
                                <td>
                                    @if($school->portal_enabled)
                                        <span class="badge bg-success">On</span>
                                    @else
                                        <span class="badge bg-secondary">Off</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $school->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('super-admin.schools.access', $school) }}" class="btn btn-sm btn-outline-brand">
                                        Access
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No schools yet.
                                    <a href="{{ route('super-admin.schools.create') }}">Add one</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Payments --}}
<div class="row g-4 mt-1">
    @if($pendingPayments->isNotEmpty())
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold"><i class="fas fa-clock text-warning me-2"></i>Pending Payments</span>
                    <a href="{{ route('super-admin.payments.index') }}" class="btn btn-sm btn-outline-brand">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>School</th>
                                <th>Package</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingPayments as $payment)
                                <tr>
                                    <td class="fw-semibold">{{ $payment->school->name }}</td>
                                    <td>{{ $payment->plan->name }}</td>
                                    <td>₹{{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="{{ $pendingPayments->isNotEmpty() ? 'col-lg-6' : 'col-12' }}">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="fas fa-receipt text-brand me-2"></i>Recent Payments</span>
                <a href="{{ route('super-admin.payments.index') }}" class="btn btn-sm btn-outline-brand">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>School</th>
                            <th>Package</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                            <tr>
                                <td class="fw-semibold">{{ $payment->school->name }}</td>
                                <td>{{ $payment->plan->name }}</td>
                                <td>₹{{ number_format($payment->amount, 2) }}</td>
                                <td class="small text-muted">{{ $payment->paid_at?->format('M d, Y') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No payments yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

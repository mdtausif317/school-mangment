<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Subscription Expired — {{ $school->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f8; min-height: 100vh; }
        .brand { color: #0a5f47; }
        .btn-brand { background: #0a5f47; color: #fff; border: none; }
        .btn-brand:hover { background: #084a38; color: #fff; }
        .plan-card { transition: box-shadow .2s; cursor: pointer; }
        .plan-card:has(input:checked) { box-shadow: 0 0 0 2px #0a5f47; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1"><i class="fas fa-school brand me-2"></i>{{ $school->name }}</h4>
                        <p class="text-muted mb-0">School Portal</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">@csrf
                        <button class="btn btn-outline-secondary btn-sm">Logout</button>
                    </form>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4 text-center">
                        <div class="text-warning mb-3" style="font-size: 3rem;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h5 class="fw-bold">Your subscription has expired</h5>
                        <p class="text-muted mb-0">
                            School portal access is blocked until you renew your plan.
                            Select a package below and submit a renewal request. Super admin will activate after payment verification.
                        </p>
                    </div>
                </div>

                @if($pendingPayment)
                    <div class="alert alert-info">
                        <i class="fas fa-hourglass-half me-2"></i>
                        <strong>Renewal pending:</strong> {{ $pendingPayment->plan->name }} (₹{{ number_format($pendingPayment->amount, 2) }})
                        — submitted {{ $pendingPayment->created_at->diffForHumans() }}. Please wait for super admin approval.
                    </div>
                @else
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white fw-semibold">
                            <i class="fas fa-sync-alt brand me-2"></i>Renew Subscription
                        </div>
                        <div class="card-body p-4">
                            @if($plans->isEmpty())
                                <p class="text-muted mb-0">No packages available. Contact super admin.</p>
                            @else
                                <form action="{{ route('school.subscription.renew') }}" method="POST">
                                    @csrf
                                    <div class="row g-3 mb-4">
                                        @foreach($plans as $plan)
                                            <div class="col-md-4">
                                                <label class="plan-card card border h-100 mb-0">
                                                    <div class="card-body">
                                                        <input type="radio" name="subscription_plan_id" value="{{ $plan->id }}"
                                                               class="form-check-input mb-2" {{ $loop->first ? 'checked' : '' }} required>
                                                        <h6 class="fw-bold mb-1">{{ $plan->name }}</h6>
                                                        <div class="text-brand fw-semibold fs-5 mb-2">₹{{ number_format($plan->price, 2) }}</div>
                                                        <div class="small text-muted">{{ $plan->duration_days }} days</div>
                                                        @if($plan->description)
                                                            <div class="small mt-2">{{ $plan->description }}</div>
                                                        @endif
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Payment Reference / Transaction ID</label>
                                        <input type="text" name="payment_reference" class="form-control"
                                               placeholder="Bank transfer ref, UPI ID, etc.">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Notes (optional)</label>
                                        <textarea name="notes" class="form-control" rows="2"
                                                  placeholder="Any message for super admin"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-brand">
                                        <i class="fas fa-paper-plane me-1"></i> Submit Renewal Request
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>

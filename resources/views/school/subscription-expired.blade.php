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
                            Select a package below and pay securely with Razorpay.
                        </p>
                    </div>
                </div>

                @if($pendingPayment && $pendingPayment->payment_method !== 'razorpay')
                    <div class="alert alert-info">
                        <i class="fas fa-hourglass-half me-2"></i>
                        <strong>Renewal pending:</strong> {{ $pendingPayment->plan->name }} (₹{{ number_format($pendingPayment->amount, 2) }})
                        — waiting for super admin approval.
                    </div>
                @else
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white fw-semibold">
                            <i class="fas fa-sync-alt brand me-2"></i>Renew Subscription
                        </div>
                        <div class="card-body p-4">
                            @if($plans->isEmpty())
                                <p class="text-muted mb-0">No packages available. Contact super admin.</p>
                            @elseif(!$razorpayConfigured)
                                <div class="alert alert-warning mb-0">
                                    Razorpay is not configured yet. Super admin must add API keys in <code>.env</code>.
                                </div>
                            @else
                                @if($pendingPayment)
                                    <div class="alert alert-info mb-4">
                                        <i class="fas fa-credit-card me-2"></i>
                                        Payment started for <strong>{{ $pendingPayment->plan->name }}</strong>
                                        (₹{{ number_format($pendingPayment->amount, 2) }}). Click below to complete payment.
                                    </div>
                                @endif

                                <div class="row g-3 mb-4" id="planList">
                                    @foreach($plans as $plan)
                                        <div class="col-md-4">
                                            <label class="plan-card card border h-100 mb-0">
                                                <div class="card-body">
                                                    <input type="radio" name="subscription_plan_id" value="{{ $plan->id }}"
                                                           class="form-check-input plan-radio mb-2"
                                                           data-plan-name="{{ $plan->name }}"
                                                           {{ ($pendingPayment && $pendingPayment->subscription_plan_id == $plan->id) || (!$pendingPayment && $loop->first) ? 'checked' : '' }}
                                                           {{ $pendingPayment && $pendingPayment->subscription_plan_id != $plan->id ? 'disabled' : '' }}>
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

                                <button type="button" class="btn btn-brand" id="payBtn">
                                    <i class="fas fa-credit-card me-1"></i> Pay with Razorpay
                                </button>
                                <p class="text-muted small mt-3 mb-0">
                                    <i class="fas fa-lock me-1"></i> Secured by Razorpay — UPI, Card, Netbanking supported.
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($razorpayConfigured && $plans->isNotEmpty() && (!$pendingPayment || $pendingPayment->payment_method === 'razorpay'))
    <form id="verifyForm" action="{{ route('school.subscription.razorpay.verify') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="payment_id" id="verify_payment_id">
        <input type="hidden" name="razorpay_order_id" id="verify_order_id">
        <input type="hidden" name="razorpay_payment_id" id="verify_payment_ref">
        <input type="hidden" name="razorpay_signature" id="verify_signature">
    </form>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        const payBtn = document.getElementById('payBtn');
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        payBtn.addEventListener('click', async function () {
            const selected = document.querySelector('.plan-radio:checked');
            if (!selected) return;

            payBtn.disabled = true;
            payBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Please wait...';

            try {
                const res = await fetch('{{ route('school.subscription.razorpay.order') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ subscription_plan_id: selected.value }),
                });

                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Could not start payment');

                const options = {
                    key: data.key,
                    amount: data.amount,
                    currency: data.currency,
                    name: '{{ config('app.name') }}',
                    description: data.plan_name + ' — ' + data.school_name,
                    order_id: data.order_id,
                    prefill: {
                        name: data.user_name,
                        email: data.user_email,
                    },
                    theme: { color: '#0a5f47' },
                    handler: function (response) {
                        document.getElementById('verify_payment_id').value = data.payment_id;
                        document.getElementById('verify_order_id').value = response.razorpay_order_id;
                        document.getElementById('verify_payment_ref').value = response.razorpay_payment_id;
                        document.getElementById('verify_signature').value = response.razorpay_signature;
                        document.getElementById('verifyForm').submit();
                    },
                    modal: {
                        ondismiss: function () {
                            payBtn.disabled = false;
                            payBtn.innerHTML = '<i class="fas fa-credit-card me-1"></i> Pay with Razorpay';
                        }
                    }
                };

                new Razorpay(options).open();
            } catch (err) {
                alert(err.message);
            }

            payBtn.disabled = false;
            payBtn.innerHTML = '<i class="fas fa-credit-card me-1"></i> Pay with Razorpay';
        });
    </script>
    @endif
</body>
</html>

@extends('layouts.school')

@section('title', 'Fee Revenue Report')
@section('page-title', 'Fee Revenue Report')

@section('content')
<div class="mb-3">
    <a href="{{ route('school.reports') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Reports
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('school.reports-fees') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">From</label>
                <input type="date" name="from" class="form-control" value="{{ $from }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">To</label>
                <input type="date" name="to" class="form-control" value="{{ $to }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-brand w-100">Generate</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Total Revenue</div>
                <div class="fs-4 fw-semibold text-brand">₹{{ number_format($total, 2) }}</div>
            </div>
        </div>
    </div>
    @foreach($byMethod as $method => $amount)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-capitalize">{{ $method }}</div>
                    <div class="fs-5 fw-semibold">₹{{ number_format($amount, 2) }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Fee For</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->paid_on->format('d M Y') }}</td>
                        <td class="fw-semibold">{{ $payment->student->name }}</td>
                        <td>{{ $payment->student->schoolClass?->displayName() ?? '—' }}</td>
                        <td>{{ $payment->fee_for ?? '—' }}</td>
                        <td class="text-capitalize">{{ $payment->payment_method }}</td>
                        <td>{{ $payment->reference ?? '—' }}</td>
                        <td class="text-end fw-semibold">₹{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No payments in this period.</td>
                    </tr>
                @endforelse
            </tbody>
            @if($payments->isNotEmpty())
                <tfoot class="table-light">
                    <tr>
                        <th colspan="6" class="text-end">Total</th>
                        <th class="text-end">₹{{ number_format($total, 2) }}</th>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-brand { background: #0a5f47; color: #fff; border: none; }
    .btn-brand:hover { background: #0d7a5c; color: #fff; }
    .text-brand { color: #0a5f47; }
</style>
@endpush

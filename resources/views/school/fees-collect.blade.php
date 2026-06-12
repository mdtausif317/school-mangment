@extends('layouts.school')

@section('title', 'Fee Collection')
@section('page-title', 'Fee Collection')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">This Month</div>
                <div class="fs-4 fw-semibold text-brand">₹{{ number_format($totalThisMonth, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Record Payment</div>
            <div class="card-body">
                <form method="POST" action="{{ route('school.fees-collect.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Student</label>
                        <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                            <option value="">Select student</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>
                                    {{ $student->name }} ({{ $student->roll_no }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (₹)</label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror"
                               value="{{ old('amount') }}" required>
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Paid On</label>
                        <input type="date" name="paid_on" class="form-control @error('paid_on') is-invalid @enderror"
                               value="{{ old('paid_on', now()->toDateString()) }}" required>
                        @error('paid_on')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select" required>
                            @foreach(\App\Models\FeePayment::paymentMethods() as $value => $label)
                                <option value="{{ $value }}" @selected(old('payment_method', 'cash') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fee For</label>
                        <input type="text" name="fee_for" class="form-control" placeholder="e.g. Monthly, Admission"
                               value="{{ old('fee_for') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference</label>
                        <input type="text" name="reference" class="form-control" placeholder="Receipt / transaction ID"
                               value="{{ old('reference') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-brand w-100">
                        <i class="fas fa-plus me-1"></i> Record Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Recent Payments</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Fee For</th>
                            <th>Method</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->paid_on->format('d M Y') }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $payment->student->name }}</div>
                                    <small class="text-muted">{{ $payment->student->schoolClass?->displayName() }}</small>
                                </td>
                                <td>{{ $payment->fee_for ?? '—' }}</td>
                                <td class="text-capitalize">{{ $payment->payment_method }}</td>
                                <td class="text-end fw-semibold">₹{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No fee payments recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
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

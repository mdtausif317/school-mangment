@extends('layouts.super-admin')

@section('title', 'Subscription Payments')
@section('page-title', 'Subscription Payments')

@push('styles')
<style>
    .text-brand { color: #0a5f47; }
    .btn-outline-brand { border-color: #0a5f47; color: #0a5f47; }
    .btn-outline-brand:hover { background: #0a5f47; color: #fff; }
</style>
@endpush

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="fas fa-clock me-2 text-warning"></i>Pending Renewal Requests
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>School</th>
                    <th>Package</th>
                    <th>Amount</th>
                    <th>Requested By</th>
                    <th>Notes</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingPayments as $payment)
                    <tr>
                        <td class="fw-semibold">{{ $payment->school->name }}</td>
                        <td>{{ $payment->plan->name }}</td>
                        <td>₹{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->requester?->name ?? '—' }}</td>
                        <td class="small text-muted">{{ $payment->notes ?? '—' }}</td>
                        <td class="small">{{ $payment->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <form action="{{ route('super-admin.payments.approve', $payment) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="payment_reference" value="{{ $payment->payment_reference }}">
                                <button type="submit" class="btn btn-sm btn-success" title="Approve & activate">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#reject{{ $payment->id }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>

                    <div class="modal fade" id="reject{{ $payment->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('super-admin.payments.reject', $payment) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Reject Payment — {{ $payment->school->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="form-label">Reason (optional)</label>
                                        <textarea name="notes" class="form-control" rows="3" placeholder="Why rejected?"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Reject</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No pending payment requests.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">
        <i class="fas fa-history me-2 text-brand"></i>Recent Payments
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>School</th>
                    <th>Package</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentPayments as $payment)
                    <tr>
                        <td>{{ $payment->school->name }}</td>
                        <td>{{ $payment->plan->name }}</td>
                        <td>₹{{ number_format($payment->amount, 2) }}</td>
                        <td>
                            @if($payment->status === 'completed')
                                <span class="badge bg-success">Completed</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td class="small">{{ $payment->paid_at?->format('M d, Y') ?? $payment->updated_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No payment history yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

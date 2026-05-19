@extends('admin.layout')

@section('title','Admin Payments — HybridLearn')
@section('page-heading','Payments')

@section('content')
    <p class="section-label">Inspect payment gateway activity</p>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Payment records</div>
            <span class="pill">{{ $payments->total() }} entries</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Gateway</th>
                    <th>Transaction</th>
                    <th>Order</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ ucfirst($payment->payment_gateway) }}</td>
                        <td>{{ $payment->transaction_id ?? '—' }}</td>
                        <td>{{ $payment->order?->order_number ?? '—' }}</td>
                        <td>${{ number_format($payment->amount, 2) }} {{ strtoupper($payment->currency) }}</td>
                        <td><span class="pill {{ $payment->status }}">{{ ucfirst($payment->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No payment records were found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:18px">{{ $payments->links() }}</div>
    </div>
@endsection

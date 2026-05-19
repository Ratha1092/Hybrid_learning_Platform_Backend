@extends('admin.layout')

@section('title','Admin Orders — HybridLearn')
@section('page-heading','Orders')

@section('content')
    <p class="section-label">View platform purchase history</p>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Order history</div>
            <span class="pill">{{ $orders->total() }} orders</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Student</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Paid at</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td style="font-family:monospace;color:var(--muted);">{{ $order->order_number }}</td>
                        <td>{{ $order->user?->name ?? 'Guest' }}</td>
                        <td>${{ number_format($order->final_amount, 2) }}</td>
                        <td><span class="pill {{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span></td>
                        <td>{{ $order->paid_at?->format('M d, Y') ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No orders were found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:18px">{{ $orders->links() }}</div>
    </div>
@endsection

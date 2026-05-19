@extends('admin.layout')

@section('title','Admin Payouts — HybridLearn')
@section('page-heading','Payouts')

@section('content')
    <p class="section-label">Review instructor payout requests</p>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Pending payouts</div>
            <span class="pill">{{ $payouts->total() }} requests</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Instructor</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Requested</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payouts as $payout)
                    <tr>
                        <td>{{ $payout->instructor?->name ?? 'Unknown' }}</td>
                        <td>${{ number_format($payout->amount, 2) }}</td>
                        <td>{{ ucfirst($payout->payment_method ?? 'N/A') }}</td>
                        <td><span class="pill {{ $payout->status ?? 'pending' }}">{{ ucfirst($payout->status ?? 'pending') }}</span></td>
                        <td>{{ $payout->requested_at?->format('M d, Y') ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No payout requests were found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:18px">{{ $payouts->links() }}</div>
    </div>
@endsection

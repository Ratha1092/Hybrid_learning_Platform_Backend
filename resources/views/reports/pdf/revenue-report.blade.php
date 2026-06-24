<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body>
@include('reports._pdf-layout', ['siteName' => $siteName, 'title' => $title, 'filtersSummary' => $filtersSummary])

<table class="kpi-grid"><tr>
    <td class="kpi-cell"><div class="kpi-label">Total Revenue</div><div class="kpi-value">${{ number_format($kpis['totalRevenue'], 2) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Platform Revenue</div><div class="kpi-value">${{ number_format($kpis['platformRevenue'], 2) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Instructor Payable</div><div class="kpi-value">${{ number_format($kpis['instructorPayable'], 2) }}</div></td>
    <td class="kpi-cell"><div class="kpi-label">Orders</div><div class="kpi-value">{{ number_format($kpis['orderCount']) }}</div></td>
</tr></table>

<table class="items">
    <thead>
        <tr><th>Order #</th><th>Date</th><th>Customer</th><th>Discount</th><th class="amount">Final Amount</th></tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->created_at?->format('M d, Y') ?? '—' }}</td>
                <td>{{ $order->customer_name ?? $order->user?->name ?? '—' }}</td>
                <td>{{ $order->discount_amount > 0 ? '-$' . number_format((float) $order->discount_amount, 2) : '—' }}</td>
                <td class="amount">${{ number_format((float) $order->final_amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">{{ $siteName }} — Revenue Report — Generated {{ now()->format('F j, Y') }}</div>
</body>
</html>

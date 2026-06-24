<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; padding: 40px; }
        .header { background: #ef4444; color: #fff; text-align: center; padding: 20px; border-radius: 8px 8px 0 0; margin: -40px -40px 30px; }
        .ip-badge { display: inline-block; background: #fef2f2; color: #b91c1c; padding: 6px 16px; border-radius: 6px; font-weight: bold; font-family: monospace; font-size: 16px; margin: 10px 0; border: 1px solid #fecaca; }
        .stat-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px 20px; margin: 16px 0; }
        .stat-number { font-size: 32px; font-weight: bold; color: #dc2626; }
        .stat-label { font-size: 13px; color: #6b7280; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 13px; }
        th { background: #f9fafb; text-align: left; padding: 8px 12px; border-bottom: 2px solid #e5e7eb; color: #6b7280; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 8px 12px; border-bottom: 1px solid #f3f4f6; color: #374151; }
        .btn { display: inline-block; background: #ef4444; color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 30px; font-size: 12px; color: #999; text-align: center; }
        .warning { background: #fffbeb; border: 1px solid #fcd34d; border-radius: 6px; padding: 12px 16px; color: #92400e; font-size: 13px; margin: 16px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Security Alert</h2>
            <p style="margin:0;opacity:.9;font-size:14px">Suspicious activity detected on {{ config('app.name') }}</p>
        </div>

        <p>Hello {{ $recipientName }},</p>

        <p>We detected multiple failed login attempts from the same IP address in a short time window. This may indicate a brute-force attack.</p>

        <div class="stat-box">
            <div class="stat-number">{{ $count }}</div>
            <div class="stat-label">Failed login attempts in the last 10 minutes</div>
            <div style="margin-top:12px">
                <span>From IP: </span><span class="ip-badge">{{ $ip }}</span>
            </div>
        </div>

        @if(count($recentAttempts) > 0)
        <p><strong>Recent attempts:</strong></p>
        <table>
            <thead>
                <tr>
                    <th>User / Email</th>
                    <th>Time</th>
                    <th>User Agent</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentAttempts as $attempt)
                <tr>
                    <td>{{ $attempt['user'] ?? '—' }}<br><span style="color:#9ca3af;font-size:11px">{{ $attempt['email'] ?? '' }}</span></td>
                    <td style="white-space:nowrap">{{ $attempt['time'] ?? '—' }}</td>
                    <td style="color:#9ca3af;font-size:11px">{{ \Illuminate\Support\Str::limit($attempt['ua'] ?? '', 50) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="warning">
            <strong>Recommended action:</strong> Review the Security Events page. If this IP is not recognized, consider blocking it at the firewall level.
        </div>

        <a href="{{ $securityUrl }}" class="btn">View Security Events</a>

        <p style="margin-top:24px">Best regards,<br><strong>{{ config('app.name') }} System</strong></p>

        <div class="footer">
            This is an automated security notification. You are receiving this because you have a super-admin role.<br>
            One alert per IP per hour is sent to prevent inbox flooding.
        </div>
    </div>
</body>
</html>

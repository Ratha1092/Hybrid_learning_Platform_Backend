<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
body {
    font-family:Arial, sans-serif;
    background:#f4f4f4;
    margin:0;
    padding:20px;
}
.container {
    max-width:600px;
    margin:0 auto;
    background:#fff;
    border-radius:8px;
    padding:40px;
}
.header {
    background:#D7A441;
    color:#fff;
    text-align:center;
    padding:20px;
    border-radius:8px 8px 0 0;
    margin:-40px -40px 30px;
}
.badge {
    display:inline-block;
    background:#fdf3e0;
    color:#8a6315;
    padding:6px 16px;
    border-radius:20px;
    font-weight:bold;
}
.footer {
    margin-top:30px;
    font-size:12px;
    color:#999;
    text-align:center;
}
</style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📊 Your Scheduled Report is Ready</h2>
        </div>

        <p>Hello,</p>

        <p>Your <span class="badge">{{ $reportLabel }}</span> report for <strong>{{ $periodSummary }}</strong> has been generated and is attached to this email.</p>

        <p>This report was sent automatically based on a schedule configured in the admin panel.</p>

        <p>Best regards,<br><strong>{{ config('app.name') }} Team</strong></p>

        <div class="footer">This is an automated message. Please do not reply to this email.</div>
    </div>
</body>
</html>

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
    background:#dc2626;
    color:#fff;
    text-align:center;
    padding:20px;
    border-radius:8px 8px 0 0;
    margin:-40px -40px 30px;
}
.badge {
    display:inline-block;
    background:#fee2e2;
    color:#dc2626;
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
            <h2>Account Suspended</h2>
        </div>

        <p>Dear <strong>{{ $user->name }}</strong>,</p>

        <p>Your {{ config('app.name') }} account has been <span class="badge">Suspended</span>. You will not be able to sign in or access the platform while your account remains in this state.</p>

        <p>If you believe this was a mistake or would like more information, please contact our support team.</p>

        <p>Best regards,<br><strong>{{ config('app.name') }} Team</strong></p>

        <div class="footer">This is an automated message. Please do not reply to this email.</div>
    </div>
</body>
</html>

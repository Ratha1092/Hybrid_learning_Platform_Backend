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
    background:#16a34a;
    color:#fff;
    text-align:center;
    padding:20px;
    border-radius:8px 8px 0 0;
    margin:-40px -40px 30px;
}
.badge {
    display:inline-block;
    background:#dcfce7;
    color:#16a34a;
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
            <h2>🎉 Application Approved!</h2>
        </div>

        <p>Dear <strong>{{ $user->name }}</strong>,</p>

        <p>Congratulations! Your instructor application has been <span class="badge">Approved</span>.</p>

        <p>You can now:</p>
        <ul>
            <li>Create and publish courses</li>
            <li>Manage your course content</li>
            <li>Track your earnings in the wallet</li>
        </ul>

        <p>Login to your account to get started.</p>

        <p>Best regards,<br><strong>{{ config('app.name') }} Team</strong></p>

        <div class="footer">This is an automated message. Please do not reply to this email.</div>
    </div>
</body>
</html>

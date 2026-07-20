<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Verification Code</title>
    <style>
* {
    margin:0;
    padding:0;
    box-sizing:border-box;
}
body {
    font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background:#f1f5f9;
    padding:40px 16px;
}
.wrapper {
    max-width:560px;
    margin:0 auto;
}
.card {
    background:#ffffff;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 4px 24px rgba(0,0,0,.08);
}
.header {
    background:linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    padding:40px 40px 32px;
    text-align:center;
}
.header-icon {
    width:56px;
    height:56px;
    background:rgba(255,255,255,.2);
    border-radius:50%;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    margin-bottom:16px;
}
.header h1 {
    color:#fff;
    font-size:22px;
    font-weight:700;
}
.header p {
    color:rgba(255,255,255,.8);
    font-size:14px;
    margin-top:6px;
}
.body {
    padding:40px;
}
.greeting {
    font-size:16px;
    font-weight:600;
    color:#1e293b;
    margin-bottom:12px;
}
.text {
    font-size:15px;
    color:#475569;
    line-height:1.6;
    margin-bottom:28px;
}
.code-wrap {
    text-align:center;
    margin:28px 0;
}
.code {
    display:inline-block;
    font-size:42px;
    font-weight:800;
    letter-spacing:10px;
    color:#4f46e5;
    background:#f0f0ff;
    border:2px dashed #c7d2fe;
    border-radius:12px;
    padding:18px 32px;
}
.expiry {
    text-align:center;
    font-size:13px;
    color:#94a3b8;
    margin-top:12px;
}
.divider {
    border:none;
    border-top:1px solid #e2e8f0;
    margin:28px 0;
}
.warning {
    font-size:13px;
    color:#64748b;
    line-height:1.6;
    background:#fffbeb;
    border:1px solid #fde68a;
    border-radius:8px;
    padding:12px 16px;
}
.footer {
    text-align:center;
    padding:24px 40px;
    background:#f8fafc;
    border-top:1px solid #e2e8f0;
}
.footer p {
    font-size:12px;
    color:#94a3b8;
    line-height:1.6;
}
</style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <div class="header-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </div>
                <h1>Verification Code</h1>
                <p>Use this code to complete your sign in</p>
            </div>

            <div class="body">
                <p class="greeting">Hi {{ $user->name }},</p>
                <p class="text">
                    Enter the 6-digit code below to verify your identity.
                    This code is valid for <strong>5 minutes</strong> and can only be used once.
                </p>

                <div class="code-wrap">
                    <div class="code">{{ $code }}</div>
                    <p class="expiry">Expires in 5 minutes &nbsp;·&nbsp; Single use only</p>
                </div>

                <hr class="divider">

                <div class="warning">
                    ⚠️ <strong>Never share this code.</strong> {{ config('app.name') }} will never ask for your code via phone or chat.
                    If you didn't request this, someone may be trying to access your account — change your password immediately.
                </div>
            </div>

            <div class="footer">
                <p>This email was sent to you because a login attempt was made on your account.</p>
            </div>
        </div>
    </div>
</body>
</html>

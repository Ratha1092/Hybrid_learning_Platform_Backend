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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                    </svg>
                </div>
                <h1>Verify Your Email</h1>
                <p>Enter this code to complete your registration</p>
            </div>

            <div class="body">
                <p class="text">
                    Someone (hopefully you!) requested to create an account with
                    <strong>{{ config('app.name') }}</strong> using this email address.
                    Enter the code below to confirm it's you.
                </p>

                <div class="code-wrap">
                    <div class="code">{{ $code }}</div>
                    <p class="expiry">Expires in 10 minutes &nbsp;·&nbsp; Single use only</p>
                </div>

                <hr class="divider">

                <div class="warning">
                    ⚠️ <strong>Don't share this code.</strong>
                    If you didn't request this, you can safely ignore this email.
                </div>
            </div>

            <div class="footer">
                <p>This code was sent to {{ $email }}</p>
            </div>
        </div>
    </div>
</body>
</html>

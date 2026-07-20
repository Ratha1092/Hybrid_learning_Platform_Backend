<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
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
    margin-bottom:24px;
}
.btn-wrap {
    text-align:center;
    margin:32px 0;
}
.btn {
    display:inline-block;
    background:linear-gradient(135deg, #6366f1, #4f46e5);
    color:#fff !important;
    text-decoration:none;
    padding:14px 36px;
    border-radius:8px;
    font-size:15px;
    font-weight:600;
    letter-spacing:.02em;
}
.expiry {
    text-align:center;
    font-size:13px;
    color:#94a3b8;
    margin-bottom:28px;
}
.divider {
    border:none;
    border-top:1px solid #e2e8f0;
    margin:28px 0;
}
.fallback-label {
    font-size:13px;
    color:#64748b;
    margin-bottom:8px;
}
.fallback-url {
    font-size:12px;
    color:#6366f1;
    word-break:break-all;
    background:#f8fafc;
    padding:10px 14px;
    border-radius:6px;
    border:1px solid #e2e8f0;
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
.footer strong {
    color:#64748b;
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
                <p>One quick step to activate your account</p>
            </div>

            <div class="body">
                <p class="greeting">Hi {{ $user->name }},</p>
                <p class="text">
                    Thanks for signing up to <strong>{{ config('app.name') }}</strong>!
                    Please verify your email address by clicking the button below.
                    This link will expire in <strong>24 hours</strong>.
                </p>

                <div class="btn-wrap">
                    <a href="{{ $verificationLink }}" class="btn">Verify Email Address</a>
                </div>

                <p class="expiry">Link expires in 24 hours &nbsp;·&nbsp; Single use only</p>

                <hr class="divider">

                <p class="fallback-label">If the button doesn't work, copy and paste this URL into your browser:</p>
                <p class="fallback-url">{{ $verificationLink }}</p>
            </div>

            <div class="footer">
                <p>
                    If you didn't create an account with <strong>{{ config('app.name') }}</strong>,
                    you can safely ignore this email.
                </p>
            </div>
        </div>
    </div>
</body>
</html>

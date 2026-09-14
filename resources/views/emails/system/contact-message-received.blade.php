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
    background:#2563eb;
    color:#fff;
    text-align:center;
    padding:20px;
    border-radius:8px 8px 0 0;
    margin:-40px -40px 30px;
}
table {
    width:100%;
    border-collapse:collapse;
    margin-top:16px;
    font-size:13px;
}
td {
    padding:8px 12px;
    border-bottom:1px solid #f3f4f6;
    color:#374151;
    vertical-align:top;
}
td.label {
    width:90px;
    color:#6b7280;
    font-weight:bold;
    white-space:nowrap;
}
.message-box {
    background:#f9fafb;
    border:1px solid #e5e7eb;
    border-radius:8px;
    padding:16px 20px;
    margin:16px 0;
    white-space:pre-wrap;
    color:#374151;
    font-size:14px;
}
.btn {
    display:inline-block;
    background:#2563eb;
    color:#fff;
    padding:12px 24px;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
    margin-top:20px;
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
            <h2>New Contact Message</h2>
            <p style="margin:0;opacity:.9;font-size:14px">Submitted via {{ config('app.name') }} contact form</p>
        </div>

        <table>
            <tr><td class="label">From</td><td>{{ $contactMessage->name }} &lt;{{ $contactMessage->email }}&gt;</td></tr>
            <tr><td class="label">Subject</td><td>{{ $contactMessage->subject }}</td></tr>
        </table>

        <div class="message-box">{{ $contactMessage->message }}</div>

        <a href="{{ $adminUrl }}" class="btn">View &amp; Reply in Admin Panel</a>

        <div class="footer">
            You can also reply directly to this email — it will go to {{ $contactMessage->email }}.
        </div>
    </div>
</body>
</html>

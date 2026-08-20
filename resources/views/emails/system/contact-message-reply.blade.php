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
.quote {
    border-left:3px solid #e5e7eb;
    padding-left:14px;
    margin:20px 0;
    color:#9ca3af;
    font-size:12.5px;
    white-space:pre-wrap;
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
            <h2>{{ config('app.name') }} Support</h2>
        </div>

        <p>Hello {{ $contactMessage->name }},</p>

        <p>Thanks for reaching out. Here's our reply to your message:</p>

        <div class="message-box">{{ $contactMessage->reply_message }}</div>

        <p style="margin-top:24px;color:#9ca3af;font-size:12.5px">Your original message:</p>
        <div class="quote">{{ $contactMessage->message }}</div>

        <p style="margin-top:24px">Best regards,<br><strong>{{ config('app.name') }} Support Team</strong></p>

        <div class="footer">
            This email was sent in response to your message submitted via our contact form.
        </div>
    </div>
</body>
</html>

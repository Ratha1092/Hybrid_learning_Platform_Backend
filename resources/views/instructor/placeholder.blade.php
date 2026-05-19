<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — Instructor</title>
    <style>
        body { margin: 0; font-family: system-ui, sans-serif; background: #0f1117; color: #f3f4f6; display:flex;align-items:center;justify-content:center;min-height:100vh; }
        .card { max-width: 560px; width:100%; padding:32px; background:#171b26; border:1px solid rgba(255,255,255,.06); border-radius:18px; box-shadow:0 20px 60px rgba(0,0,0,.35); }
        a { color:#a78bfa; text-decoration:none; }
        h1 { margin:0 0 16px; font-size:2rem; }
        p { margin:0 0 24px; color:#cbd5e1; line-height:1.7; }
        .button { display:inline-flex; align-items:center; justify-content:center; background:#6c63ff; color:#fff; padding:12px 18px; border-radius:999px; text-decoration:none; font-weight:600; }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ $title }}</h1>
        <p>{{ $description }}</p>
        <a href="{{ route('instructor.dashboard') }}" class="button">Back to Dashboard</a>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied</title>
    <style>
:root {
    --bg:#0f172a;
    --p1:#1e293b;
    --p2:#263245;
    --bd:rgba(255,255,255,.07);
    --bd2:rgba(255,255,255,.13);
    --t1:#f1f5f9;
    --t2:#94a3b8;
}
@media (prefers-color-scheme: light) {
    :root {
        --bg:#f1f5f9;
        --p1:#ffffff;
        --p2:#f8fafc;
        --bd:rgba(15,23,42,.08);
        --bd2:rgba(15,23,42,.14);
        --t1:#0f172a;
        --t2:#475569;
    }
}
* {
    box-sizing:border-box;
    margin:0;
    padding:0;
}
body {
    font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
    background:var(--bg);
    color:var(--t1);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:24px;
}
.card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:16px;
    box-shadow:0 4px 24px rgba(0,0,0,.3);
    padding:48px 40px;
    max-width:440px;
    width:100%;
    text-align:center;
}
.icon {
    width:64px;
    height:64px;
    border-radius:16px;
    margin:0 auto 20px;
    background:rgba(248,113,113,.16);
    color:#fca5a5;
    display:grid;
    place-items:center;
}
.icon svg {
    width:30px;
    height:30px;
}
h1 {
    font-size:19px;
    font-weight:780;
    letter-spacing:-.01em;
    color:var(--t1);
    margin-bottom:8px;
}
p {
    font-size:13.5px;
    color:var(--t2);
    line-height:1.6;
    margin-bottom:28px;
}
.btns {
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    flex-wrap:wrap;
}
.btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:9px 18px;
    border-radius:8px;
    font-size:12.5px;
    font-weight:700;
    text-decoration:none;
    transition:opacity .18s, transform .15s;
    border:none;
    font-family:inherit;
    cursor:pointer;
}
.btn:hover {
    opacity:.85;
    transform:translateY(-1px);
}
.btn-primary {
    background:#6366f1;
    color:#fff;
}
.btn-gray {
    background:var(--p2);
    color:var(--t1);
    border:1px solid var(--bd2);
}
.btn svg {
    width:13px;
    height:13px;
}
</style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
        </div>
        @php
            $reason = trim($exception->getMessage() ?? '');
        @endphp
        <h1>You don't have access to this page</h1>
        <p>
            @if($reason && $reason !== 'This action is unauthorized.')
                {{ $reason }}
            @else
                Your account doesn't have the permission required to view this. If you think this is a mistake, ask an administrator to grant you access.
            @endif
        </p>
        <div class="btns">
            <button onclick="history.length > 1 ? history.back() : window.location.href='{{ url('/admin') }}'" class="btn btn-gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                Go Back
            </button>
            <form method="POST" action="{{ route('admin.force-logout') }}">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                    Log In Again
                </button>
            </form>
        </div>
        <p style="margin-top:16px;font-size:11.5px;color:var(--t2)">
            Seeing this unexpectedly, right after being signed in? Your session likely went stale — "Log In Again" will clear it and take you to a fresh login.
        </p>
    </div>
</body>
</html>

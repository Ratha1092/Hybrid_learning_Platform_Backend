<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectHorizonBackButton
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! str_contains($response->headers->get('Content-Type', ''), 'text/html')) {
            return $response;
        }

        $adminUrl = url('/admin');

        $html = <<<HTML
<style>
#hl-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px 9px 14px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 600;
    font-family: ui-sans-serif, system-ui, sans-serif;
    text-decoration: none;
    border: 1px solid rgba(0, 0, 0, 0.12);
    background: rgba(0, 0, 0, 0.04);
    color: #4b5563;
    transition: background 0.15s, border-color 0.15s, color 0.15s;
    white-space: nowrap;
    cursor: pointer;
    line-height: 1;
    margin-right: 12px;
}
#hl-back-btn:hover {
    background: rgba(109, 40, 217, 0.08);
    border-color: rgba(109, 40, 217, 0.3);
    color: #7c3aed;
}
#hl-back-btn svg {
    width: 15px;
    height: 15px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2.2;
    stroke-linecap: round;
    stroke-linejoin: round;
    flex-shrink: 0;
}
html.dark #hl-back-btn {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(139, 92, 246, 0.25);
    color: #a78bfa;
}
html.dark #hl-back-btn:hover {
    background: rgba(109, 40, 217, 0.2);
    border-color: rgba(139, 92, 246, 0.5);
    color: #c4b5fd;
}
</style>
<script>
(function () {
    var btn = document.createElement('a');
    btn.id = 'hl-back-btn';
    btn.href = '{$adminUrl}';
    btn.innerHTML = '<svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> Admin Panel';

    function insert() {
        if (document.getElementById('hl-back-btn')) return;

        // Find the rightmost button group in the header (theme toggle lives here)
        var header = document.querySelector('header') || document.querySelector('[class*="header"]');
        if (!header) return;

        // Look for the button group that contains the theme-toggle button
        var btnGroup = header.querySelector('div > button:last-child')?.parentElement
                    || header.querySelector('[class*="flex"] > button')?.parentElement;

        if (btnGroup) {
            btnGroup.insertBefore(btn, btnGroup.firstChild);
        } else {
            // Fallback: fixed position next to right-side buttons
            btn.style.cssText = 'position:fixed;top:15px;right:108px;z-index:9999';
            document.body.appendChild(btn);
        }
    }

    // Run after Vue has had a chance to mount
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { setTimeout(insert, 50); });
    } else {
        setTimeout(insert, 50);
    }

    // Re-insert on SPA navigation (Horizon uses history pushState internally)
    var _push = history.pushState;
    history.pushState = function () {
        _push.apply(this, arguments);
        setTimeout(insert, 100);
    };
})();
</script>
HTML;

        $content = str_replace('</body>', $html . '</body>', $response->getContent() ?? '');
        $response->setContent($content);

        return $response;
    }
}

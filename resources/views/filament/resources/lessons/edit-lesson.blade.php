<x-filament-panels::page>
<style>
/* ── Page wrapper ───────────────────────────────────────── */
.les-page { display:grid; gap:0; }

/* ── Section card ───────────────────────────────────────── */
.fi-sc-section .fi-section {
    border-radius: 14px !important;
    border: 1px solid rgba(255,255,255,.07) !important;
    box-shadow: 0 4px 24px rgba(0,0,0,.18) !important;
    overflow: hidden !important;
    transition: box-shadow .2s !important;
}
html:not(.dark) .fi-sc-section .fi-section {
    border: 1px solid rgba(15,23,42,.08) !important;
    box-shadow: 0 2px 12px rgba(15,23,42,.07) !important;
}
.fi-sc-section .fi-section:hover {
    box-shadow: 0 6px 32px rgba(0,0,0,.28) !important;
}
html:not(.dark) .fi-sc-section .fi-section:hover {
    box-shadow: 0 4px 20px rgba(15,23,42,.12) !important;
}

/* ── Section header ─────────────────────────────────────── */
.fi-sc-section .fi-section-header {
    padding: 16px 22px !important;
    border-bottom: 1px solid rgba(255,255,255,.07) !important;
    background: rgba(255,255,255,.02) !important;
}
html:not(.dark) .fi-sc-section .fi-section-header {
    border-bottom: 1px solid rgba(15,23,42,.07) !important;
    background: rgba(248,250,252,.8) !important;
}
.fi-sc-section .fi-section-header-heading {
    font-size: 13.5px !important;
    font-weight: 750 !important;
    letter-spacing: -.01em !important;
}
.fi-sc-section .fi-section-header-description {
    font-size: 12px !important;
    opacity: .65 !important;
    margin-top: 2px !important;
}

/* ── Section icon ───────────────────────────────────────── */
.fi-sc-section .fi-section-header-icon {
    width: 32px !important;
    height: 32px !important;
    border-radius: 8px !important;
    background: rgba(124,58,237,.12) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #7c3aed !important;
    flex-shrink: 0 !important;
}
.fi-sc-section .fi-section-header-icon svg {
    width: 15px !important;
    height: 15px !important;
}

/* ── Section content ────────────────────────────────────── */
.fi-sc-section .fi-section-content-ctn {
    padding: 20px 22px !important;
}

/* ── Form inputs ────────────────────────────────────────── */
.fi-input-wrp,
.fi-select-input,
.fi-textarea {
    border-radius: 9px !important;
}
.fi-fo-field-wrp-label .fi-fo-field-wrp-label-content {
    font-size: 12px !important;
    font-weight: 650 !important;
    letter-spacing: .02em !important;
}

/* ── Save button ────────────────────────────────────────── */
.fi-form-actions {
    padding-top: 4px !important;
}
</style>

<div class="les-page">
    {{ $this->content }}
</div>
</x-filament-panels::page>

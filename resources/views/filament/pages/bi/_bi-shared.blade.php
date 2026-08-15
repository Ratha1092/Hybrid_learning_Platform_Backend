{{--
    Shared BI CSS + date-range-picker CSS.
    Include with: @include('filament.pages.bi._bi-shared')
--}}
<script>
window.__biRun = function(fn) {
    if (window.Chart) {
        fn();
    } else {
        window.addEventListener('load', fn, { once: true });
    }
};
</script>
@include('filament.pages.partials._csv-download-script')
<style>
.bi, .bi *, .bi *::before, .bi *::after {
    box-sizing:border-box;
    margin:0;
    padding:0;
}

.bi {
    font-family:'DM Sans', 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
    font-size:13px;
    line-height:1.5;
    padding-bottom:48px;
    display:grid;
    gap:20px;
    --bg:#0f172a;
    --p1:#1e293b;
    --p2:#263245;
    --bd:rgba(255,255,255,.07);
    --bd2:rgba(255,255,255,.13);
    --t1:#e2e8f0;
    --t2:#64748b;
    --t3:#334155;
    --acc:#2563EB;
    --acc2:#2563eb;
    --ok:#22c55e;
    --err:#ef4444;
    color:var(--t1);
}

html:not(.dark) .bi {
    --bg:#f1f5f9;
    --p1:#ffffff;
    --p2:#f8fafc;
    --bd:rgba(15,23,42,.10);
    --bd2:rgba(15,23,42,.18);
    --t1:#0f172a;
    --t2:#475569;
    --t3:#cbd5e1;
}

/* Header */
.bi-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    padding-bottom:16px;
    border-bottom:1px solid var(--bd);
}

.bi-header h1 {
    font-size:clamp(20px, 2.2vw, 26px);
    font-weight:780;
    letter-spacing:-.018em;
    color:var(--t1);
}

.bi-header p {
    font-size:12px;
    color:var(--t2);
    margin-top:4px;
}

.bi-actions {
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    align-items:center;
}

.bi-export-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:10px;
    padding:.38rem .75rem;
    font-size:12px;
    font-weight:600;
    font-family:inherit;
    color:var(--t2);
    cursor:pointer;
    white-space:nowrap;
    transition:background .15s, color .15s, border-color .15s;
}

.bi-export-btn:hover {
    background:var(--p2);
    color:var(--t1);
    border-color:var(--acc);
}

.bi-export-btn[disabled] {
    opacity:.55;
    cursor:default;
}

/* KPI Cards */
.bi-kpi-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(190px, 1fr));
    gap:12px;
}

.bi-kpi {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    padding:16px;
    position:relative;
    overflow:hidden;
    min-width:0;
}

.bi-kpi-value {
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}

.bi-kpi-label {
    font-size:10.5px;
    font-weight:700;
    letter-spacing:.055em;
    text-transform:uppercase;
    color:var(--t2);
    margin-bottom:6px;
}

.bi-kpi-value {
    font-size:22px;
    font-weight:800;
    color:var(--t1);
}

.bi-kpi-sub {
    font-size:11px;
    color:var(--t2);
    margin-top:4px;
}

.bi-kpi-grow {
    font-size:11px;
    font-weight:700;
    margin-top:4px;
}

.bi-kpi-grow.up {
    color:var(--ok);
}
.bi-kpi-grow.dn {
    color:var(--err);
}
.bi-kpi-grow.neu {
    color:var(--t2);
}

.bi-health {
    font-size:32px;
    font-weight:900;
    background:linear-gradient(135deg, var(--acc), #60a5fa);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

/* Cards / Layout─ */
.bi-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    padding:16px;
    min-width:0;
}

.bi-card h3 {
    font-size:13px;
    font-weight:700;
    margin-bottom:14px;
    color:var(--t1);
}

.bi-chart-wrap {
    position:relative;
    height:220px;
    width:100%;
    max-width:100%;
}

.bi-chart-wrap canvas {
    max-width:100% !important;
}

.bi-charts-2 {
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:16px;
    min-width:0;
}

.bi-charts-3 {
    display:grid;
    grid-template-columns:1.5fr 1fr 1fr;
    gap:16px;
    min-width:0;
}

.bi-grid-2 {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
    min-width:0;
}

.bi-grid-3 {
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:16px;
    min-width:0;
}

@media (max-width: 1100px) {
    .bi-charts-3 {
        grid-template-columns:1fr 1fr;
    }
    .bi-grid-3 {
        grid-template-columns:1fr 1fr;
    }
}

@media (max-width: 820px) {
    .bi-charts-2 {
        grid-template-columns:1fr;
    }
    .bi-grid-2 {
        grid-template-columns:1fr;
    }
}

@media (max-width: 700px) {
    .bi-charts-3 {
        grid-template-columns:1fr;
    }
    .bi-grid-3 {
        grid-template-columns:1fr;
    }
}

/* Tables */
.bi-table-card {
    background:var(--p1);
    border:1px solid var(--bd);
    border-radius:12px;
    overflow-x:auto;
}

.bi-table-card h3 {
    font-size:13px;
    font-weight:700;
    padding:14px 16px 0;
    color:var(--t1);
}

.bi-table {
    width:100%;
    border-collapse:collapse;
}

.bi-table thead tr {
    border-bottom:1px solid var(--bd);
}

.bi-table th {
    padding:10px 12px;
    text-align:left;
    font-size:10.5px;
    font-weight:800;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:var(--t2);
    white-space:nowrap;
}

.bi-table td {
    padding:10px 12px;
    border-bottom:1px solid var(--bd);
    font-size:12.5px;
    color:var(--t1);
}

.bi-table tbody tr:last-child td {
    border-bottom:none;
}

.bi-rank {
    font-size:11px;
    font-weight:700;
    color:var(--t2);
    width:20px;
    text-align:center;
}

.bi-tag {
    display:inline-block;
    padding:2px 7px;
    border-radius:99px;
    font-size:10px;
    font-weight:700;
}

.bi-tag-ok {
    background:rgba(34,197,94,.12);
    color:var(--ok);
}

.bi-tag-err {
    background:rgba(239,68,68,.12);
    color:var(--err);
}

.bi-tag-warn {
    background:rgba(37,99,235,.12);
    color:var(--acc);
}

/* Date range picker */
.rp-drp-wrap {
    position:relative;
}

.rp-drp-pill {
    display:inline-flex;
    align-items:center;
    background:var(--p1);
    border:1.5px solid var(--acc);
    border-radius:10px;
    padding:.38rem .65rem;
    cursor:pointer;
    user-select:none;
    white-space:nowrap;
    box-shadow:0 0 0 3px rgba(37,99,235,.08);
    transition:box-shadow .15s;
    font-size:12px;
    color:var(--t1);
}

.rp-drp-pill:hover {
    box-shadow:0 0 0 4px rgba(37,99,235,.14);
}

.rp-drp-icon {
    color:var(--acc);
    margin-right:.4rem;
    flex-shrink:0;
}

.rp-drp-from, .rp-drp-to {
    font-weight:700;
}

.rp-drp-sep {
    margin:0 .4rem;
    color:var(--bd2);
    font-weight:300;
}

.rp-drp-chev {
    margin-left:.45rem;
    color:var(--acc);
    transition:transform .2s;
    flex-shrink:0;
}

.rp-drp-chev.open {
    transform:rotate(180deg);
}

.rp-drp-panel {
    display:none;
    position:absolute;
    right:0;
    top:calc(100% + 8px);
    background:var(--p1);
    border:1px solid var(--bd2);
    border-radius:12px;
    box-shadow:0 8px 24px rgba(0,0,0,.25);
    padding:1rem;
    min-width:255px;
    z-index:300;
}

.rp-drp-panel.open {
    display:block;
}

.rp-drp-presets {
    display:flex;
    flex-direction:column;
    gap:2px;
    margin-bottom:.65rem;
}

.rp-drp-pre {
    display:block;
    width:100%;
    text-align:left;
    background:none;
    border:none;
    padding:.38rem .55rem;
    border-radius:6px;
    font-size:11.5px;
    font-weight:500;
    color:var(--t2);
    cursor:pointer;
    font-family:inherit;
    transition:background .12s;
}

.rp-drp-pre:hover {
    background:rgba(37,99,235,.06);
    color:var(--t1);
}

.rp-drp-pre.active {
    background:rgba(37,99,235,.10);
    color:var(--acc);
    font-weight:700;
}

.rp-drp-hr {
    border:none;
    border-top:1px solid var(--bd);
    margin:.45rem 0;
}

.rp-drp-cus-lbl {
    font-size:9.5px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--t2);
    margin-bottom:.35rem;
}

.rp-drp-cus-row {
    display:flex;
    align-items:center;
    gap:.35rem;
}

.rp-drp-dt {
    flex:1;
    background:var(--p2);
    border:1px solid var(--bd2);
    border-radius:6px;
    padding:.28rem .4rem;
    font-size:11.5px;
    color:var(--t1);
    outline:none;
}

.rp-drp-dt:focus {
    border-color:var(--acc);
}

.rp-drp-dt-sep {
    color:var(--t2);
    font-size:11px;
    flex-shrink:0;
}

.rp-drp-btns {
    display:flex;
    gap:.4rem;
    margin-top:.65rem;
}

.rp-drp-apply {
    flex:1;
    background:var(--acc);
    color:#fff;
    border:none;
    border-radius:6px;
    padding:.35rem .65rem;
    font-size:11.5px;
    font-weight:700;
    cursor:pointer;
    font-family:inherit;
}

.rp-drp-apply:hover {
    opacity:.88;
}

.rp-drp-reset {
    background:transparent;
    color:var(--t2);
    border:1px solid var(--bd2);
    border-radius:6px;
    padding:.35rem .55rem;
    font-size:11.5px;
    cursor:pointer;
    font-family:inherit;
}

.rp-drp-reset:hover {
    border-color:var(--acc);
    color:var(--acc);
}
</style>

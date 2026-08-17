<x-filament-panels::page>
<div wire:poll.{{ $refreshInterval }}s>
<style>
/* Light mode tokens─ */
:root {
    --db-bg:#F1F5F9;
    --db-card:#FFFFFF;
    --db-border:#E2E8F0;
    --db-t1:#0F172A;
    --db-t2:#334155;
    --db-t3:#64748B;
    --db-t4:#94A3B8;
    --db-blue:#3B82F6;
    --db-blue-d:#1E40AF;
    --db-blue-l:#EFF6FF;
    --db-blue-2:#DBEAFE;
    --db-green:#10B981;
    --db-green-l:#ECFDF5;
    --db-amber:#F59E0B;
    --db-amber-l:#FFFBEB;
    --db-red:#EF4444;
    --db-red-l:#FEF2F2;
    --db-purple:#8B5CF6;
    --db-purple-l:#F5F3FF;
    --db-teal:#14B8A6;
    --db-sh:0 1px 3px 0 rgb(0 0 0/.07), 0 1px 2px -1px rgb(0 0 0/.05);
    --db-sh-md:0 4px 6px -1px rgb(0 0 0/.08), 0 2px 4px -2px rgb(0 0 0/.05);
    --db-input-bg:#F8FAFC;
}
/* Dark mode token */
html.dark {
    --db-bg:#0F172A;
    --db-card:#1E293B;
    --db-border:#334155;
    --db-t1:#F1F5F9;
    --db-t2:#CBD5E1;
    --db-t3:#94A3B8;
    --db-t4:#64748B;
    --db-blue:#60A5FA;
    --db-blue-d:#93C5FD;
    --db-blue-l:#1E3A5F;
    --db-blue-2:#1E3A5F;
    --db-green:#34D399;
    --db-green-l:#064E3B;
    --db-amber:#FBBF24;
    --db-amber-l:#451A03;
    --db-red:#F87171;
    --db-red-l:#450A0A;
    --db-purple:#A78BFA;
    --db-purple-l:#2E1065;
    --db-sh:0 1px 3px 0 rgb(0 0 0/.3), 0 1px 2px -1px rgb(0 0 0/.2);
    --db-sh-md:0 4px 6px -1px rgb(0 0 0/.35), 0 2px 4px -2px rgb(0 0 0/.25);
    --db-input-bg:#0F172A;
}

/* Strip Filament's outer padding on dashboard pages only */
.fi-main:has(.db-wrap) {
    padding-inline:0 !important;
}
.fi-page-header-main-ctn:has(.db-wrap) {
    padding-block:0 !important;
    row-gap:0 !important;
}
.fi-page-content:has(.db-wrap) {
    row-gap:0 !important;
}

.db-wrap {
    background:var(--db-bg);
    min-height:100vh;
    padding:1rem 1.25rem;
    font-family:'Inter',system-ui,sans-serif;
    color:var(--db-t1);
}

/* Car─ */
.db-card {
    background:var(--db-card);
    border:1px solid var(--db-border);
    border-radius:.75rem;
    box-shadow:var(--db-sh);
    overflow:hidden;
}
.db-card-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:.875rem 1.125rem .75rem;
    border-bottom:1px solid var(--db-border);
}
.db-card-title {
    font-size:.875rem;
    font-weight:600;
    color:var(--db-t1);
    display:flex;
    align-items:center;
    gap:.4rem;
}
.db-card-body {
    padding:1rem 1.125rem;
}

/* Top ba */
.db-topbar {
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:1.25rem;
    flex-wrap:wrap;
    gap:.75rem;
}
.db-topbar-left h1 {
    font-size:1.75rem;
    font-weight:700;
    color:var(--db-t1);
    margin:0;
}
.db-topbar-left p {
    font-size:.8125rem;
    color:var(--db-t3);
    margin:.1rem 0 0;
}
.db-topbar-right {
    display:flex;
    align-items:center;
    gap:.5rem;
    flex-wrap:wrap;
}

/* Date filte */
/* Date range pil */
.db-drp-wrap {
    position:relative;
}
.db-drp-pill {
    display:inline-flex;
    align-items:center;
    gap:0;
    background:var(--db-card);
    border:1.5px solid var(--db-teal);
    border-radius:10px;
    padding:.45rem .75rem .45rem .65rem;
    cursor:pointer;
    user-select:none;
    white-space:nowrap;
    box-shadow:0 0 0 3px rgba(20,184,166,.08);
    transition:box-shadow .15s, border-color .15s;
    font-size:.8125rem;
    color:var(--db-t1);
}
.db-drp-pill:hover {
    box-shadow:0 0 0 4px rgba(20,184,166,.14);
}
.db-drp-pill-icon {
    color:var(--db-teal);
    flex-shrink:0;
    margin-right:.5rem;
}
.db-drp-pill-from {
    font-weight:600;
    color:var(--db-t1);
}
.db-drp-pill-sep {
    margin:0 .5rem;
    color:var(--db-border);
    font-size:.9rem;
    font-weight:300;
}
.db-drp-pill-to {
    font-weight:600;
    color:var(--db-t1);
}
.db-drp-pill-chevron {
    margin-left:.6rem;
    color:var(--db-teal);
    transition:transform .2s;
    flex-shrink:0;
}
.db-drp-pill-chevron.open {
    transform:rotate(180deg);
}

.db-drp-panel {
    display:none;
    position:absolute;
    right:0;
    top:calc(100% + 8px);
    background:var(--db-card);
    border:1px solid var(--db-border);
    border-radius:12px;
    box-shadow:var(--db-sh-md);
    padding:1rem;
    min-width:280px;
    z-index:200;
}
.db-drp-panel.open {
    display:block;
}

.db-drp-presets {
    display:flex;
    flex-direction:column;
    gap:2px;
    margin-bottom:.75rem;
}
.db-drp-preset {
    display:block;
    width:100%;
    text-align:left;
    background:none;
    border:none;
    padding:.45rem .65rem;
    border-radius:7px;
    font-size:.8rem;
    font-weight:500;
    color:var(--db-t2);
    cursor:pointer;
    font-family:inherit;
    transition:background .12s, color .12s;
}
.db-drp-preset:hover {
    background:var(--db-bg);
    color:var(--db-t1);
}
.db-drp-preset.active {
    background:rgba(20,184,166,.1);
    color:var(--db-teal);
    font-weight:700;
}

.db-drp-divider {
    border:none;
    border-top:1px solid var(--db-border);
    margin:.5rem 0;
}

.db-drp-custom-label {
    font-size:.7rem;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--db-t4);
    margin-bottom:.4rem;
}
.db-drp-custom-row {
    display:flex;
    align-items:center;
    gap:.4rem;
}
.db-drp-date {
    flex:1;
    background:var(--db-bg);
    border:1px solid var(--db-border);
    border-radius:7px;
    padding:.35rem .5rem;
    font-size:.8rem;
    color:var(--db-t1);
    outline:none;
    transition:border-color .15s;
    colorscheme:light dark;
}
.db-drp-date:focus {
    border-color:var(--db-teal);
}
.db-drp-date-sep {
    color:var(--db-t4);
    font-size:.8rem;
    flex-shrink:0;
}

.db-drp-actions {
    display:flex;
    gap:.5rem;
    margin-top:.75rem;
}
.db-drp-apply {
    flex:1;
    background:var(--db-teal);
    color:#fff;
    border:none;
    border-radius:7px;
    padding:.4rem .75rem;
    font-size:.8rem;
    font-weight:700;
    cursor:pointer;
    font-family:inherit;
    transition:opacity .15s;
}
.db-drp-apply:hover {
    opacity:.88;
}
.db-drp-reset {
    background:var(--db-bg);
    color:var(--db-t3);
    border:1px solid var(--db-border);
    border-radius:7px;
    padding:.4rem .65rem;
    font-size:.8rem;
    cursor:pointer;
    font-family:inherit;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    transition:border-color .15s, color .15s;
}
.db-drp-reset:hover {
    border-color:var(--db-teal);
    color:var(--db-teal);
}

/* Badge─ */
.db-badge {
    display:inline-flex;
    align-items:center;
    gap:.2rem;
    font-size:.6875rem;
    font-weight:600;
    padding:.2rem .45rem;
    border-radius:9999px;
    white-space:nowrap;
}
.db-badge-green {
    background:var(--db-green-l);
    color:#065F46;
}
.db-badge-red {
    background:var(--db-red-l);
    color:#991B1B;
}
.db-badge-amber {
    background:var(--db-amber-l);
    color:#92400E;
}
.db-badge-blue {
    background:var(--db-blue-2);
    color:var(--db-blue-d);
}
.db-badge-gray {
    background:var(--db-border);
    color:var(--db-t3);
}
.db-badge-purple {
    background:var(--db-purple-l);
    color:#5B21B6;
}
html.dark .db-badge-green {
    color:#A7F3D0;
}
html.dark .db-badge-red {
    color:#FCA5A5;
}
html.dark .db-badge-amber {
    color:#FDE68A;
}
html.dark .db-badge-blue {
    color:var(--db-blue-d);
}
html.dark .db-badge-purple {
    color:#DDD6FE;
}

/* Status do─ */
.db-dot {
    width:.5rem;
    height:.5rem;
    border-radius:9999px;
    display:inline-block;
    flex-shrink:0;
}
.db-dot-green {
    background:var(--db-green);
    box-shadow:0 0 0 2px var(--db-green-l);
}
.db-dot-red {
    background:var(--db-red);
    box-shadow:0 0 0 2px var(--db-red-l);
}
.db-dot-amber {
    background:var(--db-amber);
    box-shadow:0 0 0 2px var(--db-amber-l);
}

/* KPI gri─ */
.db-kpi-grid {
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:1rem;
    margin-bottom:1rem;
}
@media(max-width:1024px) {
    .db-kpi-grid {
        grid-template-columns:repeat(2,1fr);
    }
}
@media(max-width:640px) {
    .db-kpi-grid {
        grid-template-columns:1fr;
    }
}
.db-kpi {
    background:var(--db-card);
    border:1px solid var(--db-border);
    border-radius:.75rem;
    padding:1.125rem 1.25rem;
    box-shadow:var(--db-sh);
    display:flex;
    flex-direction:column;
    gap:.625rem;
    transition:box-shadow .15s;
}
.db-kpi:hover {
    box-shadow:var(--db-sh-md);
}
.db-kpi-top {
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:.5rem;
}
.db-kpi-icon {
    width:2.5rem;
    height:2.5rem;
    border-radius:.625rem;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
}
.db-kpi-icon svg {
    width:1.25rem;
    height:1.25rem;
}
.db-kpi-value {
    font-size:1.75rem;
    font-weight:700;
    color:var(--db-t1);
    line-height:1;
    letter-spacing:-.025em;
    font-variant-numeric:tabular-nums;
}
.db-kpi-label {
    font-size:.6875rem;
    font-weight:600;
    color:var(--db-t3);
    text-transform:uppercase;
    letter-spacing:.05em;
    margin-bottom:.25rem;
}

/* Action Required cards */
.db-action-card {
    background:var(--db-card);
    border:1px solid var(--db-border);
    border-radius:.75rem;
    padding:.875rem 1rem;
    box-shadow:var(--db-sh);
    margin-bottom:1rem;
}
.db-action-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:.875rem;
}
.db-action-title {
    font-size:.9375rem;
    font-weight:700;
    color:var(--db-t1);
}
.db-action-grid {
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:.75rem;
}
@media(max-width:1100px) {
    .db-action-grid {
        grid-template-columns:repeat(3,1fr);
    }
}
@media(max-width:640px) {
    .db-action-grid {
        grid-template-columns:repeat(2,1fr);
    }
}
.db-action-item {
    display:flex;
    align-items:center;
    gap:.75rem;
    padding:.75rem .875rem;
    border-radius:.625rem;
    border:1px solid var(--db-border);
    background:var(--db-bg);
    text-decoration:none;
    transition:box-shadow .15s;
    cursor:pointer;
}
.db-action-item:hover {
    box-shadow:var(--db-sh-md);
}
.db-action-icon {
    width:2.25rem;
    height:2.25rem;
    border-radius:.5rem;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
}
.db-action-icon svg {
    width:1.125rem;
    height:1.125rem;
}
.db-action-num {
    font-size:1.25rem;
    font-weight:700;
    color:var(--db-t1);
    line-height:1;
}
.db-action-lbl {
    font-size:.6875rem;
    color:var(--db-t3);
    margin-top:.1rem;
    line-height:1.3;
}

/* Revenue char */
.db-rev-grid {
    display:grid;
    grid-template-columns:1fr 264px;
    gap:1rem;
    margin-bottom:1rem;
}
@media(max-width:1100px) {
    .db-rev-grid {
        grid-template-columns:1fr;
    }
}
.db-period-tabs {
    display:flex;
    gap:.2rem;
    background:var(--db-bg);
    border-radius:.5rem;
    padding:.2rem;
    border:1px solid var(--db-border);
}
.db-period-tab {
    padding:.275rem .65rem;
    border-radius:.375rem;
    font-size:.75rem;
    font-weight:600;
    color:var(--db-t3);
    cursor:pointer;
    border:none;
    background:none;
    transition:all .15s;
}
.db-period-tab.active {
    background:var(--db-card);
    color:var(--db-blue-d);
    box-shadow:0 1px 3px rgb(0 0 0/.12);
}
html.dark .db-period-tab.active {
    color:var(--db-blue);
}
.db-chart-wrap {
    padding:.75rem 1.125rem 0;
    overflow:hidden;
}
.db-chart-area {
    width:100%;
    display:block;
}
.db-rev-legend {
    display:flex;
    align-items:center;
    gap:1rem;
    padding:.5rem 1.125rem .875rem;
    flex-wrap:wrap;
}
.db-legend-item {
    display:flex;
    align-items:center;
    gap:.375rem;
    font-size:.75rem;
    color:var(--db-t3);
}
.db-legend-dot {
    width:.5rem;
    height:.5rem;
    border-radius:9999px;
    flex-shrink:0;
}

.db-rev-stats {
    display:flex;
    flex-direction:column;
    gap:.625rem;
}
.db-rev-stat {
    background:var(--db-card);
    border:1px solid var(--db-border);
    border-radius:.75rem;
    padding:.75rem .875rem;
    box-shadow:var(--db-sh);
    flex:1;
}
.db-rev-stat-lbl {
    font-size:.625rem;
    font-weight:600;
    color:var(--db-t3);
    text-transform:uppercase;
    letter-spacing:.05em;
}
.db-rev-stat-val {
    font-size:1.125rem;
    font-weight:700;
    color:var(--db-t1);
    font-variant-numeric:tabular-nums;
    margin:.1rem 0 .2rem;
}
.db-rev-stat-sub {
    font-size:.6875rem;
    color:var(--db-t3);
    display:flex;
    align-items:center;
    gap:.35rem;
    flex-wrap:wrap;
}

/* Mini stat─ */
.db-mini-grid {
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:1rem;
    margin-bottom:1rem;
}
@media(max-width:768px) {
    .db-mini-grid {
        grid-template-columns:1fr;
    }
}
.db-mini {
    background:var(--db-card);
    border:1px solid var(--db-border);
    border-radius:.75rem;
    padding:1rem 1.25rem;
    box-shadow:var(--db-sh);
    display:flex;
    align-items:center;
    gap:.875rem;
}
.db-mini-icon {
    width:2.5rem;
    height:2.5rem;
    border-radius:.625rem;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
}
.db-mini-icon svg {
    width:1.25rem;
    height:1.25rem;
}
.db-mini-lbl {
    font-size:.75rem;
    color:var(--db-t3);
    font-weight:500;
}
.db-mini-val {
    font-size:1.5rem;
    font-weight:700;
    color:var(--db-t1);
    line-height:1.15;
    font-variant-numeric:tabular-nums;
}
.db-mini-sub {
    font-size:.6875rem;
    color:var(--db-t3);
    margin-top:.15rem;
    display:flex;
    align-items:center;
    gap:.3rem;
}

/* Table─ */
.db-table {
    width:100%;
    border-collapse:collapse;
}
.db-table th {
    padding:.5rem .75rem;
    font-size:.6875rem;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:.04em;
    color:var(--db-t3);
    text-align:left;
    border-bottom:1px solid var(--db-border);
    background:var(--db-bg);
    white-space:nowrap;
}
.db-table td {
    padding:.6rem .75rem;
    font-size:.8125rem;
    color:var(--db-t2);
    border-bottom:1px solid var(--db-border);
    vertical-align:middle;
}
.db-table tr:last-child td {
    border-bottom:none;
}
.db-table tbody tr:hover td {
    background:var(--db-bg);
}
.db-rank {
    width:1.5rem;
    height:1.5rem;
    border-radius:9999px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:.6875rem;
    font-weight:700;
}
.db-rank-1 {
    background:#FEF3C7;
    color:#92400E;
}
.db-rank-2 {
    background:var(--db-border);
    color:var(--db-t2);
}
.db-rank-3 {
    background:var(--db-red-l);
    color:#991B1B;
}
.db-rank-n {
    background:var(--db-border);
    color:var(--db-t3);
}
html.dark .db-rank-1 {
    color:#FDE68A;
}
html.dark .db-rank-3 {
    color:#FCA5A5;
}
.db-avatar {
    width:2rem;
    height:2rem;
    border-radius:9999px;
    background:var(--db-blue-2);
    color:var(--db-blue-d);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:.6875rem;
    font-weight:700;
    flex-shrink:0;
}
html.dark .db-avatar {
    color:var(--db-blue);
}
.db-avatar-row {
    display:flex;
    align-items:center;
    gap:.5rem;
}
.db-name {
    font-weight:500;
    color:var(--db-t1);
}
.db-sub {
    font-size:.6875rem;
    color:var(--db-t3);
}

/* Two-co */
.db-two-col {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:1rem;
    margin-bottom:1rem;
}
@media(max-width:900px) {
    .db-two-col {
        grid-template-columns:1fr;
    }
}
/* Orders wide row */
.db-orders-row {
    display:grid;
    grid-template-columns:3fr 2fr;
    gap:1rem;
    margin-bottom:1rem;
}
@media(max-width:960px) {
    .db-orders-row {
        grid-template-columns:1fr;
    }
}

/* Three-co */
.db-three-col {
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:1rem;
    margin-bottom:1rem;
}
@media(max-width:1100px) {
    .db-three-col {
        grid-template-columns:1fr 1fr;
    }
}
@media(max-width:700px) {
    .db-three-col {
        grid-template-columns:1fr;
    }
}

/* Course lis */
.db-course-row {
    display:flex;
    align-items:center;
    gap:.75rem;
    padding:.6rem 0;
    border-bottom:1px solid var(--db-border);
}
.db-course-row:last-child {
    border-bottom:none;
}
.db-course-thumb {
    width:2.5rem;
    height:2.5rem;
    border-radius:.375rem;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:.75rem;
    font-weight:700;
    flex-shrink:0;
}
.db-course-info {
    flex:1;
    min-width:0;
}
.db-course-name {
    font-size:.8125rem;
    font-weight:500;
    color:var(--db-t1);
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}
.db-course-meta {
    font-size:.6875rem;
    color:var(--db-t3);
}
.db-stars {
    display:flex;
    align-items:center;
    gap:.1rem;
}
.db-star {
    width:.8125rem;
    height:.8125rem;
}

/* System Healt */
.db-health-list {
    padding:.25rem 0;
}
.db-health-row {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:.6rem 1.125rem;
    border-bottom:1px solid var(--db-border);
    gap:.5rem;
}
.db-health-row:last-child {
    border-bottom:none;
}
.db-health-lbl {
    display:flex;
    align-items:center;
    gap:.5rem;
    font-size:.8125rem;
    color:var(--db-t2);
}
.db-health-lbl svg {
    width:1rem;
    height:1rem;
    flex-shrink:0;
}
.db-health-val {
    font-size:.8125rem;
    font-weight:600;
    color:var(--db-t1);
    text-align:right;
}
.db-progress-bar {
    height:.375rem;
    background:var(--db-border);
    border-radius:9999px;
    overflow:hidden;
    margin-top:.375rem;
}
.db-progress-fill {
    height:100%;
    border-radius:9999px;
}

/* Utilit */
.db-trunc {
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}
.db-mono {
    font-variant-numeric:tabular-nums;
}
.db-view-all {
    font-size:.75rem;
    font-weight:500;
    color:var(--db-blue);
    text-decoration:none;
    white-space:nowrap;
}
.db-view-all:hover {
    text-decoration:underline;
}
.db-empty {
    text-align:center;
    color:var(--db-t3);
    font-size:.8125rem;
    padding:2rem 0;
}
.db-link {
    color:var(--db-blue);
    text-decoration:none;
    font-weight:500;
}
.db-link:hover {
    text-decoration:underline;
}
</style>

<script>
function dbChart() {
    return {
        period: '30d',
        data: @json($revenueChartData),
        get cur() { return this.data[this.period]; },
        fmtMoney(v) {
            if (v >= 1000000) return '$'+(v/1000000).toFixed(1)+'M';
            if (v >= 1000)    return '$'+(v/1000).toFixed(1)+'K';
            return '$'+Number(v).toFixed(0);
        },
        path(vals, W, H, pL, pR, pT, pB) {
            if (!vals || vals.length < 2) return '';
            const max = Math.max(...vals, 1);
            const cW = W - pL - pR, cH = H - pT - pB;
            const pts = vals.map((v,i) => [pL + i*(cW/(vals.length-1)), pT + cH - (v/max)*cH]);
            let d = `M${pts[0][0]} ${pts[0][1]}`;
            for (let i=1;i<pts.length;i++){
                const cx=(pts[i-1][0]+pts[i][0])/2;
                d+=` C${cx} ${pts[i-1][1]} ${cx} ${pts[i][1]} ${pts[i][0]} ${pts[i][1]}`;
            }
            return d;
        },
        area(vals, W, H, pL, pR, pT, pB) {
            const p = this.path(vals, W, H, pL, pR, pT, pB);
            if (!p) return '';
            const cH = H - pT - pB;
            return `${p} L${W-pR} ${pT+cH} L${pL} ${pT+cH} Z`;
        },
        dots(vals, W, H, pL, pR, pT, pB) {
            if (!vals || vals.length < 2) return [];
            const max = Math.max(...vals, 1);
            const cW = W - pL - pR, cH = H - pT - pB;
            return vals.map((v,i) => ({
                x: pL + i*(cW/(vals.length-1)),
                y: pT + cH - (v/max)*cH,
                v,
            }));
        },
        render() {
            const svg = document.getElementById('db-chart-svg');
            if (!svg) return;
            const rect = svg.getBoundingClientRect();
            const W = rect.width > 10 ? rect.width : (svg.parentElement?.clientWidth || 620);
            const H = 200, pL = 58, pR = 12, pT = 14, pB = 26;
            svg.setAttribute('viewBox', `0 0 ${W} ${H}`);

            const gross = this.cur.gross || [], plat = this.cur.platform || [], inst = this.cur.instructor || [];
            const max = Math.max(...gross, 1);

            // Y-axis ticks
            const yEl = document.getElementById('db-chart-yaxis');
            if (yEl) {
                const ticks = 5;
                let html = '';
                for (let i=0;i<=ticks;i++) {
                    const val = (max/ticks)*(ticks-i);
                    const y = pT + ((H-pT-pB)/ticks)*i;
                    const label = val>=1000 ? '$'+(val/1000).toFixed(0)+'K' : '$'+Math.round(val);
                    html += `<text x="${pL-10}" y="${y+4}" text-anchor="end" font-size="9" fill="var(--db-t4)">${label}</text>`;
                    html += `<line x1="${pL}" y1="${y}" x2="${W-pR}" y2="${y}" stroke="var(--db-border)" stroke-width="1" stroke-dasharray="4,3"/>`;
                }
                yEl.innerHTML = html;
            }

            // X-axis labels
            const labels = this.cur.labels || [];
            const xEl = document.getElementById('db-chart-xaxis');
            if (xEl && labels.length) {
                const step = (W - pL - pR) / (labels.length - 1);
                const stride = Math.max(1, Math.ceil(labels.length / 8));
                let html = '';
                labels.forEach((lbl, i) => {
                    if (i % stride !== 0 && i !== labels.length-1) return;
                    const x = pL + i * step;
                    html += `<text x="${x}" y="${H-4}" text-anchor="middle" font-size="9" fill="var(--db-t4)">${lbl}</text>`;
                });
                xEl.innerHTML = html;
            }

            // Paths
            const set = (id, d) => { const el=document.getElementById(id); if(el) el.setAttribute('d',d); };
            set('db-p-gross-area', this.area(gross, W, H, pL, pR, pT, pB));
            set('db-p-plat-area',  this.area(plat,  W, H, pL, pR, pT, pB));
            set('db-p-inst-area',  this.area(inst,  W, H, pL, pR, pT, pB));
            set('db-p-gross', this.path(gross, W, H, pL, pR, pT, pB));
            set('db-p-plat',  this.path(plat,  W, H, pL, pR, pT, pB));
            set('db-p-inst',  this.path(inst,  W, H, pL, pR, pT, pB));

            // Dots
            const renderDots = (id, vals, color) => {
                const el = document.getElementById(id);
                if (!el) return;
                el.innerHTML = this.dots(vals, W, H, pL, pR, pT, pB)
                    .map(d => `<circle cx="${d.x}" cy="${d.y}" r="3.5" fill="${color}" stroke="var(--db-card)" stroke-width="2"/>`)
                    .join('');
            };
            renderDots('db-dots-gross', gross, '#3B82F6');
            renderDots('db-dots-plat',  plat,  '#10B981');
            renderDots('db-dots-inst',  inst,  '#F59E0B');
        },
    }
}
function dbCustomDate() {
    return {
        open:   false,
        preset: '{{ $activePreset }}',
        from:   '{{ $activeDateFrom }}',
        to:     '{{ $activeDateTo }}',

        fmtDate(iso) {
            if (!iso) return '—';
            const d = new Date(iso + 'T00:00:00');
            return d.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' });
        },
        get pillFrom() { return this.fmtDate(this.from); },
        get pillTo()   { return this.fmtDate(this.to);   },

        selectPreset(key) {
            this.preset = key;
            if (key !== 'custom') this.apply();
        },
        apply() {
            const params = new URLSearchParams();
            params.set('preset', this.preset);
            if (this.preset === 'custom') {
                if (this.from) params.set('date_from', this.from);
                if (this.to)   params.set('date_to',   this.to);
            }
            this.open = false;
            const url = window.location.pathname + '?' + params.toString();
            if (typeof Livewire !== 'undefined' && Livewire.navigate) {
                Livewire.navigate(url);
            } else {
                window.location.href = url;
            }
        },
        reset() {
            this.preset = 'this_month';
            this.open   = false;
            const url = window.location.pathname;
            if (typeof Livewire !== 'undefined' && Livewire.navigate) {
                Livewire.navigate(url);
            } else {
                window.location.href = url;
            }
        },
    }
}
</script>

<div class="db-wrap">

{{-- Top ba --}}
<div class="db-topbar">
    <div class="db-topbar-left">
        @php
            $hour = now()->hour;
            $greeting = $hour < 12 ? 'morning' : ($hour < 18 ? 'afternoon' : 'evening');
            $userName = auth()->user()->name ?? 'Admin';
        @endphp
        <h1>Good {{ $greeting }}, <span style="color:var(--db-blue);">{{ $userName }}</span> 👋</h1>
        <p>Here's what's happening on your platform.</p>
    </div>
    <div class="db-topbar-right">
        {{-- Date range pill filter --}}
        <div class="db-drp-wrap" x-data="dbCustomDate()" @click.outside="open=false">

            {{-- Pill trigger --}}
            <div class="db-drp-pill" @click="open=!open">
                <span class="db-drp-pill-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </span>
                <span class="db-drp-pill-from" x-text="pillFrom"></span>
                <span class="db-drp-pill-sep">|</span>
                <span class="db-drp-pill-to" x-text="pillTo"></span>
                <span class="db-drp-pill-chevron" :class="{open}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </span>
            </div>

            {{-- Dropdown panel --}}
            <div class="db-drp-panel" :class="{open}">

                {{-- Presets --}}
                <div class="db-drp-presets">
                    @foreach(\App\Support\Concerns\HasDateRangePresets::dateRangePresetOptions() as $key => $label)
                        @if($key !== 'custom')
                        <button type="button" class="db-drp-preset"
                            :class="{ active: preset === '{{ $key }}' }"
                            @click="selectPreset('{{ $key }}')">
                            {{ $label }}
                        </button>
                        @endif
                    @endforeach
                </div>

                <hr class="db-drp-divider">

                {{-- Custom range --}}
                <div class="db-drp-custom-label">Custom Range</div>
                <div class="db-drp-custom-row">
                    <input type="date" class="db-drp-date" x-model="from" @change="preset='custom'">
                    <span class="db-drp-date-sep">—</span>
                    <input type="date" class="db-drp-date" x-model="to"   @change="preset='custom'">
                </div>

                <div class="db-drp-actions">
                    <button type="button" class="db-drp-apply" @click="apply()">Apply</button>
                    <button type="button" class="db-drp-reset" @click="reset()">Reset</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Row 1: KPI cards─ --}}
<div class="db-kpi-grid">
    @php $revGrowth = $revenueChartData['30d']['gross_growth'] ?? 0; @endphp
    <div class="db-kpi">
        <div class="db-kpi-top">
            <div>
                <div class="db-kpi-label">Total Revenue</div>
                <div class="db-kpi-value db-mono">${{ number_format($totalRevenue, 2) }}</div>
            </div>
            <div class="db-kpi-icon" style="background:var(--db-green-l);">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-green)" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
            <span class="db-badge {{ $revGrowth >= 0 ? 'db-badge-green' : 'db-badge-red' }}">
                {{ $revGrowth >= 0 ? '↑' : '↓' }} {{ abs($revGrowth) }}%
            </span>
            <span style="font-size:.75rem;color:var(--db-t3);">from last 30 days</span>
        </div>
    </div>
    <div class="db-kpi">
        <div class="db-kpi-top">
            <div>
                <div class="db-kpi-label">Total Orders</div>
                <div class="db-kpi-value">{{ number_format($totalOrders) }}</div>
            </div>
            <div class="db-kpi-icon" style="background:var(--db-purple-l);">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-purple)" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
            <span class="db-badge db-badge-blue">+{{ $newOrdersToday }} today</span>
            <span style="font-size:.75rem;color:var(--db-t3);">{{ $completedPayments }} paid</span>
        </div>
    </div>
    <div class="db-kpi">
        <div class="db-kpi-top">
            <div>
                <div class="db-kpi-label">Total Students</div>
                <div class="db-kpi-value">{{ number_format($totalStudents) }}</div>
            </div>
            <div class="db-kpi-icon" style="background:var(--db-blue-l);">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-blue)" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
            <span class="db-badge db-badge-blue">+{{ $newStudentsToday }} today</span>
            @if($enrollmentGrowth >= 0)
                <span class="db-badge db-badge-green">↑ {{ $enrollmentGrowth }}% enrollments</span>
            @else
                <span class="db-badge db-badge-red">↓ {{ abs($enrollmentGrowth) }}% enrollments</span>
            @endif
        </div>
    </div>
    <div class="db-kpi">
        <div class="db-kpi-top">
            <div>
                <div class="db-kpi-label">Total Instructors</div>
                <div class="db-kpi-value">{{ number_format($totalInstructors) }}</div>
            </div>
            <div class="db-kpi-icon" style="background:var(--db-amber-l);">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-amber)" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
            @if($pendingVerifications > 0)
                <span class="db-badge db-badge-amber">{{ $pendingVerifications }} pending verification</span>
            @else
                <span class="db-badge db-badge-green">All verified</span>
            @endif
        </div>
    </div>
</div>

{{-- Row 2: Action Required─ --}}
<div class="db-action-card">
    <div class="db-action-header">
        <div class="db-action-title">Action Required</div>
        <a href="{{ route('filament.admin.pages.payments') }}" wire:navigate class="db-view-all">View All Alerts &rsaquo;</a>
    </div>
    <div class="db-action-grid">
        <a href="{{ route('filament.admin.pages.instructor-verifications') }}" wire:navigate class="db-action-item">
            <div class="db-action-icon" style="background:var(--db-red-l);">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-red)" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <div class="db-action-num">{{ $pendingVerifications }}</div>
                <div class="db-action-lbl">Instructor Verifications<br>Pending</div>
            </div>
        </a>
        <a href="{{ route('filament.admin.pages.courses') }}" wire:navigate class="db-action-item">
            <div class="db-action-icon" style="background:var(--db-amber-l);">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-amber)" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <div>
                <div class="db-action-num">{{ $pendingCourseReviews }}</div>
                <div class="db-action-lbl">Courses<br>Awaiting Review</div>
            </div>
        </a>
        <a href="{{ route('filament.admin.pages.payouts') }}" wire:navigate class="db-action-item">
            <div class="db-action-icon" style="background:var(--db-purple-l);">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-purple)" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <div>
                <div class="db-action-num">{{ $pendingPayoutsCount }}</div>
                <div class="db-action-lbl">Failed Payouts<br>Requires Attention</div>
            </div>
        </a>
        <a href="{{ route('filament.admin.pages.payments') }}" wire:navigate class="db-action-item">
            <div class="db-action-icon" style="background:var(--db-blue-l);">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-blue)" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <div>
                <div class="db-action-num">{{ $failedPaymentsToday }}</div>
                <div class="db-action-lbl">Payment Failures<br>Today</div>
            </div>
        </a>
    </div>
</div>

{{-- Row 3: Revenue chart + sidebar --}}
<div class="db-rev-grid">
    <div class="db-card" wire:ignore x-data="dbChart()" x-init="setTimeout(()=>{ render(); const ro=new ResizeObserver(()=>render()); ro.observe($el); }, 80)">
        <div class="db-card-header">
            <span class="db-card-title">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--db-blue)" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Revenue Overview
            </span>
            <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                <div class="db-rev-legend">
                    <div class="db-legend-item"><span class="db-legend-dot" style="background:#3B82F6;"></span>Gross Revenue</div>
                    <div class="db-legend-item"><span class="db-legend-dot" style="background:#10B981;border:1px dashed #10B981;border-radius:0;width:.75rem;height:0;"></span>Platform Revenue</div>
                    <div class="db-legend-item"><span class="db-legend-dot" style="background:#F59E0B;width:.75rem;height:0;border-top:2px dotted #F59E0B;border-radius:0;"></span>Instructor Revenue</div>
                </div>
                <div class="db-period-tabs">
                    <button class="db-period-tab" :class="{active:period==='7d'}"  @click="period='7d';  $nextTick(()=>render())" type="button">7D</button>
                    <button class="db-period-tab" :class="{active:period==='30d'}" @click="period='30d'; $nextTick(()=>render())" type="button">30D</button>
                    <button class="db-period-tab" :class="{active:period==='6m'}"  @click="period='6m';  $nextTick(()=>render())" type="button">6M</button>
                    <button class="db-period-tab" :class="{active:period==='12m'}" @click="period='12m'; $nextTick(()=>render())" type="button">12M</button>
                </div>
            </div>
        </div>
        <div class="db-chart-wrap">
            <svg id="db-chart-svg" class="db-chart-area" viewBox="0 0 620 200" height="200" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="dg-gross" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#3B82F6" stop-opacity=".15"/><stop offset="100%" stop-color="#3B82F6" stop-opacity="0"/></linearGradient>
                    <linearGradient id="dg-plat"  x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#10B981" stop-opacity=".12"/><stop offset="100%" stop-color="#10B981" stop-opacity="0"/></linearGradient>
                    <linearGradient id="dg-inst"  x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#F59E0B" stop-opacity=".12"/><stop offset="100%" stop-color="#F59E0B" stop-opacity="0"/></linearGradient>
                </defs>
                <g id="db-chart-yaxis"></g>
                <path id="db-p-inst-area"  d="" fill="url(#dg-inst)"  stroke="none"/>
                <path id="db-p-plat-area"  d="" fill="url(#dg-plat)"  stroke="none"/>
                <path id="db-p-gross-area" d="" fill="url(#dg-gross)" stroke="none"/>
                <path id="db-p-inst"  d="" fill="none" stroke="#F59E0B" stroke-width="1.5" stroke-dasharray="4,3" stroke-linecap="round"/>
                <path id="db-p-plat"  d="" fill="none" stroke="#10B981" stroke-width="1.5" stroke-dasharray="6,2" stroke-linecap="round"/>
                <path id="db-p-gross" d="" fill="none" stroke="#3B82F6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <g id="db-dots-inst"></g>
                <g id="db-dots-plat"></g>
                <g id="db-dots-gross"></g>
                <g id="db-chart-xaxis"></g>
            </svg>
        </div>
    </div>

    <div class="db-rev-stats">
        @php
            $g30       = $revenueChartData['30d']['gross_growth']      ?? 0;
            $platGrowth = $revenueChartData['30d']['platform_growth']   ?? 0;
            $instGrowth = $revenueChartData['30d']['instructor_growth'] ?? 0;
            $gross30    = $revenueChartData['30d']['total_gross']       ?? 0;
            $plat30     = $revenueChartData['30d']['total_platform']    ?? 0;
            $inst30     = $revenueChartData['30d']['total_instructor']  ?? 0;
        @endphp
        <div class="db-rev-stat">
            <div class="db-rev-stat-lbl">Gross Revenue</div>
            <div class="db-rev-stat-val db-mono">${{ number_format($gross30, 2) }}</div>
            <div class="db-rev-stat-sub">
                <span class="db-badge {{ $g30 >= 0 ? 'db-badge-green' : 'db-badge-red' }}" style="font-size:.625rem;">
                    {{ $g30 >= 0 ? '↑' : '↓' }} {{ abs($g30) }}%
                </span>
            </div>
        </div>
        <div class="db-rev-stat">
            <div class="db-rev-stat-lbl">Platform Revenue</div>
            <div class="db-rev-stat-val db-mono">${{ number_format($plat30, 2) }}</div>
            <div class="db-rev-stat-sub">
                <span class="db-badge {{ $platGrowth >= 0 ? 'db-badge-green' : 'db-badge-red' }}" style="font-size:.625rem;">
                    {{ $platGrowth >= 0 ? '↑' : '↓' }} {{ abs($platGrowth) }}%
                </span>
            </div>
        </div>
        <div class="db-rev-stat">
            <div class="db-rev-stat-lbl">Instructor Revenue</div>
            <div class="db-rev-stat-val db-mono">${{ number_format($inst30, 2) }}</div>
            <div class="db-rev-stat-sub">
                <span class="db-badge {{ $instGrowth >= 0 ? 'db-badge-green' : 'db-badge-red' }}" style="font-size:.625rem;">
                    {{ $instGrowth >= 0 ? '↑' : '↓' }} {{ abs($instGrowth) }}%
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Row 4: Mini stats --}}
<div class="db-mini-grid">
    <div class="db-mini">
        <div class="db-mini-icon" style="background:var(--db-green-l);">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-green)" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        </div>
        <div>
            <div class="db-mini-lbl">Published Courses</div>
            <div class="db-mini-val">{{ number_format($publishedCoursesCount) }}</div>
            <div class="db-mini-sub">
                <span class="db-badge db-badge-green" style="font-size:.625rem;">+{{ $newCoursesThisMonth }} this month</span>
            </div>
        </div>
    </div>
    <div class="db-mini">
        <div class="db-mini-icon" style="background:var(--db-blue-l);">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-blue)" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        </div>
        <div>
            <div class="db-mini-lbl">Enrollments (This Month)</div>
            <div class="db-mini-val">{{ number_format($enrollmentsThisMonth) }}</div>
            <div class="db-mini-sub">
                @if($enrollmentGrowth >= 0)
                    <span class="db-badge db-badge-green" style="font-size:.625rem;">↑ {{ $enrollmentGrowth }}% this month</span>
                @else
                    <span class="db-badge db-badge-red" style="font-size:.625rem;">↓ {{ abs($enrollmentGrowth) }}% this month</span>
                @endif
            </div>
        </div>
    </div>
    <div class="db-mini">
        <div class="db-mini-icon" style="background:var(--db-amber-l);">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-amber)" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        </div>
        <div>
            <div class="db-mini-lbl">Average Completion Rate</div>
            <div class="db-mini-val">{{ $avgCompletionRate }}%</div>
            <div class="db-mini-sub">
                @if($completionRateGrowth > 0)
                    <span class="db-badge db-badge-green" style="font-size:.625rem;">↑ {{ $completionRateGrowth }}% from last month</span>
                @elseif($completionRateGrowth < 0)
                    <span class="db-badge db-badge-red" style="font-size:.625rem;">↓ {{ abs($completionRateGrowth) }}% from last month</span>
                @else
                    <span class="db-badge db-badge-gray" style="font-size:.625rem;">No change</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Row 5: Top Instructors─ --}}
<div class="db-card" style="margin-bottom:1rem;">
    <div class="db-card-header">
        <span class="db-card-title">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--db-amber)" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Top Instructors By Revenue
        </span>
        <a href="{{ route('filament.admin.pages.instructors') }}" class="db-view-all">View All &rsaquo;</a>
    </div>
    <div style="overflow-x:auto">
    <table class="db-table">
        <thead><tr>
            <th style="width:2.25rem;">#</th>
            <th>Instructor</th>
            <th>Students</th>
            <th>Courses</th>
            <th>Revenue</th>
            <th>Growth</th>
        </tr></thead>
        <tbody>
            @forelse($topInstructors as $idx => $inst)
            @php
                $words    = array_filter(explode(' ', $inst['name'] ?? 'U'));
                $initials = strtoupper(implode('', array_map(fn($w)=>$w[0], array_slice($words,0,2))));
                $growth   = $inst['growth'] ?? 0;
                $rankCls  = match($idx){ 0=>'db-rank-1', 1=>'db-rank-2', 2=>'db-rank-3', default=>'db-rank-n' };
            @endphp
            <tr>
                <td><span class="db-rank {{ $rankCls }}">{{ $idx+1 }}</span></td>
                <td>
                    <div class="db-avatar-row">
                        <div class="db-avatar">{{ $initials }}</div>
                        <div>
                            <div class="db-name db-trunc" style="max-width:200px;">{{ $inst['name'] }}</div>
                            <div class="db-sub db-trunc" style="max-width:200px;">{{ $inst['email'] }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ number_format($inst['students']) }}</td>
                <td>{{ number_format($inst['courses']) }}</td>
                <td class="db-mono" style="font-weight:600;color:var(--db-t1);">${{ number_format($inst['revenue'], 2) }}</td>
                <td><span class="db-badge {{ $growth >= 0 ? 'db-badge-green' : 'db-badge-red' }}">{{ $growth >= 0 ? '↑' : '↓' }} {{ abs($growth) }}%</span></td>
            </tr>
            @empty
            <tr><td colspan="6" class="db-empty">No revenue data yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

{{-- Row 6: Most Popular Courses (full-width) --}}
<div class="db-card" style="margin-bottom:1rem;">
    <div class="db-card-header">
        <span class="db-card-title">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--db-green)" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            Most Popular Courses
        </span>
        <a href="{{ route('filament.admin.pages.moderation') }}" class="db-view-all">View All &rsaquo;</a>
    </div>
    <div style="overflow-x:auto">
    <table class="db-table">
        <thead><tr>
            <th>Course</th>
            <th>Instructor</th>
            <th>Enrollments</th>
            <th>Revenue</th>
        </tr></thead>
        <tbody>
            @forelse($popularCourses as $course)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:.625rem;">
                        <div class="db-course-thumb" style="background:var(--db-blue-2);color:var(--db-blue-d);width:2.25rem;height:2.25rem;">
                            {{ strtoupper(substr($course->title ?? 'C', 0, 2)) }}
                        </div>
                        <div>
                            <div class="db-name">{{ $course->title }}</div>
                            @if($course->category)
                                <div class="db-sub">{{ $course->category->name ?? '' }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    <div class="db-avatar-row">
                        @php
                            $iWords = array_filter(explode(' ', optional($course->instructor)->name ?? 'U'));
                            $iInit  = strtoupper(implode('', array_map(fn($w)=>$w[0], array_slice($iWords,0,2))));
                        @endphp
                        <div class="db-avatar" style="width:1.75rem;height:1.75rem;font-size:.625rem;">{{ $iInit }}</div>
                        <span class="db-sub">{{ optional($course->instructor)->name ?? '—' }}</span>
                    </div>
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <span class="db-mono" style="font-weight:600;">{{ number_format($course->enrollments_count) }}</span>
                        <span class="db-badge db-badge-blue" style="font-size:.625rem;">students</span>
                    </div>
                </td>
                <td class="db-mono" style="font-weight:700;color:var(--db-green);">${{ number_format($course->course_revenue ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="db-empty">No published courses yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

{{-- Row 7: Recent Orders --}}
<div style="margin-bottom:1rem">
    <div class="db-card">
        <div class="db-card-header">
            <span class="db-card-title">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--db-blue)" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                Recent Orders
            </span>
            <a href="{{ route('filament.admin.pages.orders') }}" class="db-view-all">View All &rsaquo;</a>
        </div>
        <div style="overflow-x:auto">
        <table class="db-table" style="table-layout:fixed;width:100%;">
            <colgroup>
                <col style="width:16%">{{-- Order # --}}
                <col style="width:18%">{{-- Student --}}
                <col style="width:28%">{{-- Course --}}
                <col style="width:13%">{{-- Amount --}}
                <col style="width:14%">{{-- Status --}}
                <col style="width:11%">{{-- Gateway --}}
            </colgroup>
            <thead><tr>
                <th>Order #</th>
                <th>Student</th>
                <th>Course</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Gateway</th>
            </tr></thead>
            <tbody>
                @forelse($recentOrders as $order)
                @php
                    $payStatus = $order->payment_status?->value ?? 'pending';
                    [$bc, $lbl] = match($payStatus) {
                        'paid'        => ['db-badge-green',  'Paid'],
                        'completed'   => ['db-badge-green',  'Completed'],
                        'processing'  => ['db-badge-blue',   'Processing'],
                        'pending'     => ['db-badge-amber',  'Pending'],
                        'failed'      => ['db-badge-red',    'Failed'],
                        'expired'     => ['db-badge-gray',   'Expired'],
                        'cancelled'   => ['db-badge-gray',   'Cancelled'],
                        default       => ['db-badge-gray',   ucfirst($payStatus)],
                    };
                    $gw          = optional($order->payment)->payment_gateway?->value ?? '—';
                    $firstCourse = optional($order->items->first())->course?->title ?? '—';
                @endphp
                <tr>
                    <td class="db-trunc"><span class="db-link" style="font-size:.75rem;">ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                    <td class="db-trunc db-name">{{ optional($order->user)->name ?? 'Guest' }}</td>
                    <td class="db-trunc db-sub" title="{{ $firstCourse }}">{{ $firstCourse }}</td>
                    <td class="db-mono db-trunc" style="font-weight:600;">${{ number_format($order->total_amount ?? 0, 2) }}</td>
                    <td><span class="db-badge {{ $bc }}">{{ $lbl }}</span></td>
                    <td class="db-trunc" style="color:var(--db-t3);font-size:.75rem;">{{ strtoupper($gw) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="db-empty">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
{{-- Row 8: Low Rated Courses + System Health --}}
<div class="db-two-col">
    {{-- Low Rated --}}
    <div class="db-card">
        <div class="db-card-header">
            <span class="db-card-title">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--db-red)" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Low Rated Courses
                <span class="db-badge db-badge-red" style="font-size:.6rem;">&lt; 3 stars</span>
            </span>
            <a href="{{ route('filament.admin.pages.reviews') }}" class="db-view-all">View All &rsaquo;</a>
        </div>
        <div style="overflow-x:auto">
        <table class="db-table">
            <thead><tr>
                <th>Course</th>
                <th>Instructor</th>
                <th>Rating</th>
                <th>Reviews</th>
            </tr></thead>
            <tbody>
                @forelse($lowRatedCourses as $course)
                @php $rating = round($course->reviews_avg_rating ?? 0, 1); @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.625rem;">
                            <div class="db-course-thumb" style="background:var(--db-red-l);color:var(--db-red);width:2.25rem;height:2.25rem;">
                                {{ strtoupper(substr($course->title ?? 'C', 0, 2)) }}
                            </div>
                            <div class="db-name db-trunc" style="max-width:200px;" title="{{ $course->title }}">{{ $course->title }}</div>
                        </div>
                    </td>
                    <td><div class="db-sub db-trunc" style="max-width:120px;">{{ optional($course->instructor)->name ?? '—' }}</div></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.375rem;">
                            <span class="db-badge db-badge-red">{{ $rating }}</span>
                            <span style="color:var(--db-t4);font-size:.6875rem;">/ 5</span>
                        </div>
                    </td>
                    <td class="db-sub">{{ number_format($course->reviews_count) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="db-empty" style="color:var(--db-green);padding:2rem 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:block;margin:0 auto .5rem;"><polyline points="20 6 9 17 4 12"/></svg>
                    All courses are rated 3 stars or above
                </td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- System Health --}}
    <div class="db-card">
        <div class="db-card-header">
            <span class="db-card-title">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--db-teal)" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                System Health
            </span>
            <span class="db-badge {{ $failedJobsCount === 0 ? 'db-badge-green' : 'db-badge-amber' }}">
                <span class="db-dot {{ $failedJobsCount === 0 ? 'db-dot-green' : 'db-dot-amber' }}" style="margin-right:.25rem;"></span>
                {{ $failedJobsCount === 0 ? 'Healthy' : 'Attention' }}
            </span>
        </div>
        <div class="db-health-list">
            <div class="db-health-row">
                <div class="db-health-lbl">
                    <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-t3)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Queue Jobs Pending
                </div>
                <div class="db-health-val" style="{{ $queueJobsPending > 50 ? 'color:var(--db-amber);' : '' }}">{{ number_format($queueJobsPending) }}</div>
            </div>
            <div class="db-health-row">
                <div class="db-health-lbl">
                    <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-t3)" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
                    Failed Jobs
                </div>
                <div class="db-health-val" style="{{ $failedJobsCount > 0 ? 'color:var(--db-red);' : 'color:var(--db-green);' }}">{{ number_format($failedJobsCount) }}</div>
            </div>
            <div class="db-health-row">
                <div class="db-health-lbl">
                    <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-t3)" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                    Redis Status
                </div>
                <div class="db-health-val" style="{{ $redisStatus === 'connected' ? 'color:var(--db-green);' : 'color:var(--db-red);' }}">{{ ucfirst($redisStatus) }}</div>
            </div>
            <div class="db-health-row">
                <div class="db-health-lbl">
                    <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-t3)" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    New Users Today
                </div>
                <div class="db-health-val">{{ number_format($newUsersToday) }}</div>
            </div>
            <div class="db-health-row">
                <div class="db-health-lbl">
                    <svg viewBox="0 0 24 24" fill="none" stroke="var(--db-t3)" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    Server Uptime
                </div>
                <div class="db-health-val" style="color:var(--db-green);">{{ $serverUptime }}</div>
            </div>
        </div>
    </div>
</div>

</div>{{-- end .db-wrap --}}
</div>{{-- end wire:poll wrapper --}}
</x-filament-panels::page>

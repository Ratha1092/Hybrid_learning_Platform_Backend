@extends('admin.layout')

@section('title','Admin Settings — HybridLearn')
@section('page-heading','Settings')

@section('content')
    <p class="section-label">System settings and platform metrics</p>

    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-header">
            <div class="panel-title">Platform health summary</div>
            <span class="pill">Quick overview</span>
        </div>
        <div class="stat-grid" style="grid-template-columns:repeat(3,1fr);gap:14px;">
            <div class="stat-card c-blue">
                <div class="stat-label">Total Courses</div>
                <div class="stat-value">{{ number_format($stats['courses']) }}</div>
            </div>
            <div class="stat-card c-green">
                <div class="stat-label">Total Enrollments</div>
                <div class="stat-value">{{ number_format($stats['enrollments']) }}</div>
            </div>
            <div class="stat-card c-orange">
                <div class="stat-label">Total Orders</div>
                <div class="stat-value">{{ number_format($stats['orders']) }}</div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Platform configuration</div>
            <span class="pill">Static settings</span>
        </div>
        <p style="color:var(--muted);line-height:1.7;">Use this page to show global platform settings, feature toggles and administrative links for future system controls.</p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:20px;">
            <div style="background:#f5f2eb;border-radius:14px;padding:18px;">
                <div style="font-weight:700;margin-bottom:6px">Platform users</div>
                <div style="color:var(--muted)">Students, instructors and admins.</div>
            </div>
            <div style="background:#f5f2eb;border-radius:14px;padding:18px;">
                <div style="font-weight:700;margin-bottom:6px">Payments</div>
                <div style="color:var(--muted)">Gateway settings, refunds, and payouts.</div>
            </div>
            <div style="background:#f5f2eb;border-radius:14px;padding:18px;">
                <div style="font-weight:700;margin-bottom:6px">Course moderation</div>
                <div style="color:var(--muted)">Pending approvals, content review, instructor policies.</div>
            </div>
            <div style="background:#f5f2eb;border-radius:14px;padding:18px;">
                <div style="font-weight:700;margin-bottom:6px">System maintenance</div>
                <div style="color:var(--muted)">Scheduled downtime, logs, and diagnostics.</div>
            </div>
        </div>
    </div>
@endsection

@extends('admin.layout')

@section('title','Admin Enrollments — HybridLearn')
@section('page-heading','Enrollments')

@section('content')
    <p class="section-label">Monitor student enrollment activity</p>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Recent enrollments</div>
            <span class="pill">{{ $enrollments->total() }} records</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Enrolled</th>
                    <th>Status</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $enrollment)
                    <tr>
                        <td>{{ $enrollment->user?->name ?? 'Unknown' }}</td>
                        <td>{{ Str::limit($enrollment->course?->title ?? 'Unknown', 32) }}</td>
                        <td>{{ $enrollment->enrolled_at?->format('M d, Y') ?? 'N/A' }}</td>
                        <td><span class="pill {{ $enrollment->status ?? 'pending' }}">{{ ucfirst($enrollment->status ?? 'pending') }}</span></td>
                        <td>{{ $enrollment->progress_percentage ?? 0 }}%</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No enrollments were found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:18px">{{ $enrollments->links() }}</div>
    </div>
@endsection

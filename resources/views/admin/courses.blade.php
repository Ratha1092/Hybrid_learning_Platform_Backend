@extends('admin.layout')

@section('title','Admin Courses — HybridLearn')
@section('page-heading','Courses')

@section('content')
    <p class="section-label">Manage the course catalog</p>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Courses</div>
            <span class="pill">{{ $courses->total() }} total</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Instructor</th>
                    <th>Category</th>
                    <th>Level</th>
                    <th>Published</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                    <tr>
                        <td style="font-weight:600;">{{ $course->title }}</td>
                        <td>{{ $course->instructor?->name ?? 'Unknown' }}</td>
                        <td>{{ $course->category?->name ?? 'None' }}</td>
                        <td><span class="pill {{ $course->level ?? 'beginner' }}">{{ ucfirst($course->level ?? 'N/A') }}</span></td>
                        <td><span class="pill {{ $course->is_published ? 'active' : 'pending' }}">{{ $course->is_published ? 'Yes' : 'No' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No courses were found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:18px">{{ $courses->links() }}</div>
    </div>
@endsection

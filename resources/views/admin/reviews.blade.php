@extends('admin.layout')

@section('title','Admin Reviews — HybridLearn')
@section('page-heading','Reviews')

@section('content')
    <p class="section-label">Review and moderate learner feedback</p>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Course reviews</div>
            <span class="pill">{{ $reviews->total() }} reviews</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Rating</th>
                    <th>Approved</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr>
                        <td>{{ $review->user?->name ?? 'Unknown' }}</td>
                        <td>{{ Str::limit($review->course?->title ?? 'Unknown', 32) }}</td>
                        <td>{{ $review->rating }}/5</td>
                        <td><span class="pill {{ $review->is_approved ? 'active' : 'pending' }}">{{ $review->is_approved ? 'Yes' : 'No' }}</span></td>
                        <td>{{ Str::limit($review->comment, 80) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No reviews were found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:18px">{{ $reviews->links() }}</div>
    </div>
@endsection

@extends('admin.layout')

@section('title','Admin Users — HybridLearn')
@section('page-heading','Users')

@section('content')
    <p class="section-label">Manage registered accounts</p>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">User directory</div>
            <span class="pill">{{ $users->total() }} users</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td style="font-weight:600;">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td><span class="pill {{ $user->status ?? 'active' }}">{{ ucfirst($user->status ?? 'active') }}</span></td>
                        <td>{{ $user->created_at?->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No users were found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:18px">{{ $users->links() }}</div>
    </div>
@endsection

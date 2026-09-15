@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Notifications</h1>
        </div>
    </div>

    @if ($notifications->isEmpty())
        <div class="empty">Vous n'avez aucune notification pour le moment.</div>
    @else
        <div style="display:flex; flex-direction:column; gap:10px;">
            @foreach ($notifications as $notification)
                <div class="panel" style="padding:16px 20px;">
                    <div style="display:flex; justify-content:space-between; align-items:start; gap:12px;">
                        <div>
                            <strong>{{ $notification->title }}</strong>
                            <p style="margin:4px 0 0; color:var(--muted); font-size:.9rem;">{{ $notification->content }}</p>
                        </div>
                        <span style="font-size:.75rem; color:var(--muted); white-space:nowrap;">{{ $notification->sent_at?->diffForHumans() }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 20px;">
            {{ $notifications->links() }}
        </div>
    @endif
@endsection

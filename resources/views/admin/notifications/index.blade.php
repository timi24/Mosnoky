@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Notifications</h1>
        </div>
    </div>

    @if ($notifications->isEmpty())
        <div class="empty" id="live-notif-empty">Vous n'avez aucune notification pour le moment.</div>
        <div id="live-notif-list" class="notif-list" hidden></div>
    @else
        <div id="live-notif-list" class="notif-list">
            @foreach ($notifications as $notification)
                @php
                    $link = \App\Support\NotificationLink::url($notification, true)
                        ? route('live.notifications.open', $notification->id)
                        : null;
                @endphp
                <{!! $link ? 'a href="'.e($link).'"' : 'div' !!} class="notif-card {{ $notification->is_read ? '' : 'unread' }} {{ $link ? 'clickable' : '' }}" data-id="{{ $notification->id }}">
                    <span class="notif-dot"></span>
                    <div class="notif-body">
                        <strong>{{ $notification->title }}</strong>
                        <p>{{ $notification->content }}</p>
                    </div>
                    <span class="notif-when">{{ $notification->sent_at?->diffForHumans() }}</span>
                    @if ($link)<span class="notif-go">Voir ›</span>@endif
                </{{ $link ? 'a' : 'div' }}>
            @endforeach
        </div>

        <div style="margin-top: 20px;">
            {{ $notifications->links() }}
        </div>
    @endif

    <script>
    (function () {
        var list = document.getElementById('live-notif-list');
        var LIVE_URL = @json(route('live.counts', ['items' => 1]));

        function pollNotifications() {
            if (document.hidden) { return; }

            fetch(LIVE_URL, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            }).then(function (response) {
                if (!response.ok) { return null; }
                return response.json();
            }).then(function (data) {
                if (data && data.items) {
                    document.dispatchEvent(new CustomEvent('mosnoky:live', { detail: data }));
                }
            }).catch(function () {});
        }

        document.addEventListener('mosnoky:live', function (event) {
            var items = event.detail && event.detail.items;
            if (!items || !list) { return; }

            var empty = document.getElementById('live-notif-empty');
            var known = {};
            list.querySelectorAll('.notif-card').forEach(function (card) {
                known[card.dataset.id] = true;
            });

            var fresh = items.filter(function (item) { return !known[item.id]; });
            if (!fresh.length) { return; }

            if (empty) { empty.remove(); list.hidden = false; }

            fresh.reverse().forEach(function (item) {
                var card = document.createElement(item.open ? 'a' : 'div');
                if (item.open) { card.href = item.open; }
                card.className = 'notif-card new' + (item.is_read ? '' : ' unread') + (item.open ? ' clickable' : '');
                card.dataset.id = item.id;

                var dot = document.createElement('span');
                dot.className = 'notif-dot';
                var body = document.createElement('div');
                body.className = 'notif-body';
                var title = document.createElement('strong');
                title.textContent = item.title;
                var content = document.createElement('p');
                content.textContent = item.content;
                body.append(title, content);
                var when = document.createElement('span');
                when.className = 'notif-when';
                when.textContent = item.when;
                card.append(dot, body, when);

                if (item.open) {
                    var link = document.createElement('span');
                    link.className = 'notif-go';
                    link.textContent = 'Voir ›';
                    card.append(link);
                }

                list.insertBefore(card, list.firstChild);
            });
        });

        window.mosnokyLivePoll = pollNotifications;
        pollNotifications();
        window.setInterval(pollNotifications, 15000);
    })();
    </script>

    <style>
        .notif-list { display: flex; flex-direction: column; gap: 10px; max-width: 760px; }
        .notif-card { display: flex; align-items: flex-start; gap: 12px; padding: 16px 18px; background: var(--card, #fff); border: 1px solid var(--line); border-radius: 14px; box-shadow: 0 1px 6px rgba(34, 31, 29, .04); transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, background .6s ease; }
        .notif-card.unread { background: #fff7f5; border-color: #f0cfc7; }
        .notif-dot { flex-shrink: 0; width: 10px; height: 10px; margin-top: 6px; border-radius: 50%; background: transparent; }
        .notif-card.unread .notif-dot { background: var(--accent, #b1372f); box-shadow: 0 0 0 4px rgba(177, 55, 47, .15); }
        a.notif-card { text-decoration: none; color: inherit; }
        .notif-card.clickable { cursor: pointer; }
        .notif-card.clickable:hover { transform: translateY(-2px); border-color: var(--accent, #b1372f); box-shadow: 0 8px 22px rgba(34, 31, 29, .1); }
        .notif-body { flex: 1; min-width: 0; }
        .notif-body p { margin: 4px 0 0; color: var(--muted); font-size: .9rem; line-height: 1.5; }
        .notif-when { font-size: .75rem; color: var(--muted); white-space: nowrap; }
        .notif-go { align-self: center; flex-shrink: 0; padding: 6px 12px; border-radius: 999px; background: rgba(177, 55, 47, .1); color: var(--accent, #b1372f); font-size: .78rem; font-weight: 800; white-space: nowrap; }
        .notif-card.clickable:hover .notif-go { background: var(--accent, #b1372f); color: #fff; }
        .notif-card.new { animation: notifIn .5s ease; }
        @keyframes notifIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }
        @media (max-width: 560px) { .notif-card { flex-wrap: wrap; } .notif-when { margin-left: 22px; } }
        @media (prefers-reduced-motion: reduce) { .notif-card, .notif-card.new { animation: none; transition: none; } }
    </style>
@endsection

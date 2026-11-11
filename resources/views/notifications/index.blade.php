@extends('layouts.client')

@section('title', 'Notifications')

@section('content')
    <div class="page-head" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">Notifications</h1>
        </div>
        <button type="button" id="notif-read-all" class="notif-readall" hidden>✓ Tout marquer comme lu</button>
    </div>

    @if ($notifications->isEmpty())
        <div class="empty" id="live-notif-empty">Vous n'avez aucune notification pour le moment.</div>
        <div id="live-notif-list" class="notif-list" hidden></div>
    @else
        <div id="live-notif-list" class="notif-list">
            @foreach ($notifications as $notification)
                @php
                    $when = $notification->sent_at ?? $notification->created_at ?? null;
                    try {
                        $whenText = $when ? \Illuminate\Support\Carbon::parse($when)->diffForHumans() : '';
                    } catch (\Throwable $e) {
                        $whenText = '';
                    }
                @endphp
                @php
                    $link = \App\Support\NotificationLink::url($notification, false)
                        ? route('live.notifications.open', $notification->id)
                        : null;
                @endphp
                <{!! $link ? 'a href="'.e($link).'"' : 'div' !!} class="notif-card {{ $notification->is_read ? '' : 'unread' }} {{ $link ? 'clickable' : '' }}" data-id="{{ $notification->id }}">
                    <span class="notif-dot"></span>
                    <div class="notif-body">
                        <strong>{{ $notification->title }}</strong>
                        <p>{{ $notification->content }}</p>
                    </div>
                    <span class="notif-when">{{ $whenText }}</span>
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
        var readAllBtn = document.getElementById('notif-read-all');
        var READ_URL = @json(route('live.notifications.read'));
        var CSRF = @json(csrf_token());
        // Les nouvelles notifications ne s'ajoutent en direct que sur la 1re page
        var isFirstPage = @json($notifications->currentPage() === 1);
        var autoReadTimer = null;

        function esc(s) {
            var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML;
        }

        function refreshReadAll() {
            if (!readAllBtn) { return; }
            readAllBtn.hidden = !document.querySelector('.notif-card.unread');
        }

        function markAllRead() {
            fetch(READ_URL, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                credentials: 'same-origin'
            }).then(function () {
                document.querySelectorAll('.notif-card.unread').forEach(function (c) { c.classList.remove('unread'); });
                refreshReadAll();
                if (window.mosnokyLivePoll) { window.mosnokyLivePoll(); }
            });
        }

        function scheduleAutoRead() {
            clearTimeout(autoReadTimer);
            if (!document.querySelector('.notif-card.unread')) { return; }
            // Les notifications sont considérées lues après 6 secondes passées sur la page
            autoReadTimer = setTimeout(function () { if (!document.hidden) { markAllRead(); } else { scheduleAutoRead(); } }, 6000);
        }

        if (readAllBtn) { readAllBtn.addEventListener('click', markAllRead); }

        document.addEventListener('mosnoky:live', function (e) {
            var items = e.detail && e.detail.items;
            if (!items || !isFirstPage || !list) { return; }

            var empty = document.getElementById('live-notif-empty');
            var known = {};
            list.querySelectorAll('.notif-card').forEach(function (c) { known[c.dataset.id] = true; });

            var fresh = items.filter(function (n) { return !known[n.id]; });
            if (!fresh.length) { return; }

            if (empty) { empty.remove(); list.hidden = false; }

            // du plus ancien au plus récent : chaque nouveauté est placée tout en haut
            fresh.reverse().forEach(function (n) {
                var card = document.createElement(n.open ? 'a' : 'div');
                if (n.open) { card.href = n.open; }
                card.className = 'notif-card new' + (n.is_read ? '' : ' unread') + (n.open ? ' clickable' : '');
                card.dataset.id = n.id;
                card.innerHTML = '<span class="notif-dot"></span><div class="notif-body"><strong>' + esc(n.title) +
                    '</strong><p>' + esc(n.content) + '</p></div><span class="notif-when">' + esc(n.when) + '</span>' +
                    (n.open ? '<span class="notif-go">Voir ›</span>' : '');
                list.insertBefore(card, list.firstChild);
            });
            refreshReadAll();
            scheduleAutoRead();
        });

        refreshReadAll();
        scheduleAutoRead();
    })();
    </script>

    <style>
        .notif-list { display: flex; flex-direction: column; gap: 10px; max-width: 760px; }
        .notif-card { display: flex; align-items: flex-start; gap: 12px; padding: 16px 18px; background: var(--card, #fff); border: 1px solid var(--line); border-radius: 14px; box-shadow: 0 1px 6px rgba(34, 31, 29, .04); transition: background .6s ease, border-color .6s ease; }
        .notif-card.unread { background: #fff7f5; border-color: #f0cfc7; }
        .notif-dot { flex-shrink: 0; width: 10px; height: 10px; margin-top: 6px; border-radius: 50%; background: transparent; transition: background .6s ease; }
        .notif-card.unread .notif-dot { background: var(--accent, #b1372f); box-shadow: 0 0 0 4px rgba(177, 55, 47, .15); }
        a.notif-card { text-decoration: none; color: inherit; }
        .notif-card.clickable { cursor: pointer; }
        .notif-card.clickable:hover { transform: translateY(-2px); border-color: var(--accent, #b1372f); box-shadow: 0 8px 22px rgba(34, 31, 29, .1); }
        .notif-go { align-self: center; flex-shrink: 0; padding: 6px 12px; border-radius: 999px; background: rgba(177, 55, 47, .1); color: var(--accent, #b1372f); font-size: .78rem; font-weight: 800; white-space: nowrap; }
        .notif-card.clickable:hover .notif-go { background: var(--accent, #b1372f); color: #fff; }
        .notif-card { transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, background .6s ease; }
        .notif-body { flex: 1; min-width: 0; }
        .notif-body p { margin: 4px 0 0; color: var(--muted); font-size: .9rem; line-height: 1.5; }
        .notif-when { font-size: .75rem; color: var(--muted); white-space: nowrap; }
        .notif-card.new { animation: notifIn .5s ease; }
        @keyframes notifIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }
        .notif-readall { padding: 10px 18px; border: 1px solid var(--line); border-radius: 999px; background: var(--card, #fff); font-weight: 700; font-size: .85rem; font-family: inherit; cursor: pointer; transition: border-color .2s ease, color .2s ease; }
        .notif-readall:hover { border-color: var(--accent); color: var(--accent); }
        @media (max-width: 560px) { .notif-card { flex-wrap: wrap; } .notif-when { margin-left: 22px; } }
        @media (prefers-reduced-motion: reduce) { .notif-card.new { animation: none; } }
    </style>
@endsection
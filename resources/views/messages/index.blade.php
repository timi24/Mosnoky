@extends('layouts.client')

@section('title', 'Messages')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Discuter avec Mosnoky</h1>
            <p class="page-subtitle">Une question sur votre commande ou un produit ? Écrivez-nous directement.</p>
        </div>
    </div>

    <div class="chat-panel">
        <div class="chat-messages" id="chat-messages">
            @forelse ($messages as $message)
                <div class="chat-bubble {{ $message->sender_id === auth()->id() ? 'mine' : 'theirs' }}">
                    <p style="margin:0;">{{ $message->content }}</p>
                    <span class="chat-time">{{ $message->created_at->format('d/m H:i') }}</span>
                </div>
            @empty
                <div class="empty">Envoyez votre premier message à l'équipe Mosnoky.</div>
            @endforelse
        </div>

        <form method="POST" action="{{ route('messages.store') }}" class="chat-form">
            @csrf
            <input type="text" name="content" placeholder="Écrivez votre message…" required autocomplete="off">
            <button type="submit" class="button primary">Envoyer</button>
        </form>
    </div>

    <style>
        .chat-panel { border: 1px solid var(--line); border-radius: 10px; background: var(--card); display: flex; flex-direction: column; height: 560px; overflow: hidden; }
        .chat-messages { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 10px; }
        .chat-bubble { max-width: 70%; padding: 10px 14px; border-radius: 14px; font-size: .92rem; }
        .chat-bubble.mine { align-self: flex-end; background: var(--accent); color: #fff; border-bottom-right-radius: 4px; }
        .chat-bubble.theirs { align-self: flex-start; background: var(--paper); border: 1px solid var(--line); border-bottom-left-radius: 4px; }
        .chat-time { display: block; margin-top: 4px; font-size: .68rem; opacity: .7; }
        .chat-form { display: flex; gap: 10px; padding: 16px; border-top: 1px solid var(--line); }
        .chat-form input { flex: 1; padding: 11px 14px; border: 1px solid var(--line); border-radius: 999px; font-size: .92rem; }
    </style>

    <script>
        const box = document.getElementById('chat-messages');
        if (box) { box.scrollTop = box.scrollHeight; }
    </script>
@endsection

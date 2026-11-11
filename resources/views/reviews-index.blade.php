@extends('layouts.client')

@section('title', 'Mes avis')

@section('content')
    @php($statusLabels = ['EN_ATTENTE' => 'En attente de modération', 'APPROUVE' => 'Publié', 'MASQUE' => 'Non publié'])

    <div class="page-head">
        <div>
            <h1 class="page-title">Mes avis</h1>
            <p class="page-subtitle">Suivez la modération des avis que vous avez envoyés.</p>
        </div>
        <a class="button" href="{{ route('orders.index') }}">Mes commandes</a>
    </div>

    @if ($reviews->isEmpty())
        <div class="empty">Vous n’avez pas encore publié d’avis. Vous pourrez en laisser un après la livraison d’un produit.</div>
    @else
        <div class="reviews-list">
            @foreach ($reviews as $review)
                <article class="review-card">
                    <div class="review-card-head">
                        <div>
                            <span class="review-eyebrow">Produit</span>
                            <h2>
                                @if ($review->product)
                                    <a href="{{ route('products.show', $review->product) }}">{{ $review->product->name }}</a>
                                @else
                                    Produit indisponible
                                @endif
                            </h2>
                        </div>
                        <span class="pill {{ $review->moderation_status === 'APPROUVE' ? '' : 'muted' }}">
                            {{ $statusLabels[$review->moderation_status] ?? 'Statut indisponible' }}
                        </span>
                    </div>

                    <div class="review-card-meta">
                        <span class="review-stars" aria-label="Note : {{ $review->rating }} sur 5">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                        <span>{{ $review->created_at->format('d/m/Y') }}</span>
                        @if ($review->order)
                            <a href="{{ route('orders.show', $review->order) }}">Commande {{ $review->order->order_number }}</a>
                        @else
                            <span>Aucun achat associé</span>
                        @endif
                    </div>

                    @if ($review->comment)
                        <p class="review-card-comment">{{ $review->comment }}</p>
                    @endif

                    @if ($review->moderation_status === 'EN_ATTENTE')
                        <p class="review-card-note">Notre équipe examine votre avis avant sa publication.</p>
                    @elseif ($review->moderation_status === 'MASQUE')
                        <p class="review-card-note">Cet avis n’est pas affiché sur la boutique.</p>
                    @endif
                </article>
            @endforeach
        </div>

        <div style="margin-top:20px;">{{ $reviews->links() }}</div>
    @endif

    <style>
        .reviews-list { display: grid; gap: 14px; }
        .review-card { padding: 20px 22px; border: 1px solid var(--line); border-radius: 15px; background: var(--card); box-shadow: 0 8px 24px rgba(34,31,29,.045); }
        .review-card-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; }
        .review-eyebrow { color: var(--muted); font-size: .72rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .review-card h2 { margin: 3px 0 0; font-family: Georgia, serif; font-size: 1.2rem; font-weight: 500; }
        .review-card h2 a:hover { color: var(--accent); }
        .review-card-meta { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 12px; color: var(--muted); font-size: .82rem; }
        .review-card-meta a { color: var(--accent); font-weight: 700; }
        .review-stars { color: #dc9637; letter-spacing: .08em; }
        .review-card-comment { margin: 13px 0 0; line-height: 1.55; overflow-wrap: anywhere; }
        .review-card-note { margin: 12px 0 0; color: var(--muted); font-size: .83rem; }
        @media (max-width: 600px) { .review-card { padding: 17px; } .review-card-head { flex-direction: column; align-items: flex-start; } }
    </style>
@endsection

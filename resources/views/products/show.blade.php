@extends('layouts.client')

@section('title', $product->name)

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">{{ $product->name }}</h1>
            <p class="page-subtitle">
                {{ $product->category->name ?? '' }}
                @if ($averageRating)
                    · ⭐ {{ number_format($averageRating, 1) }}/5 ({{ $product->reviews->count() }} avis)
                @endif
            </p>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:28px; align-items:start; margin-bottom:40px;">
        <div class="product-image" style="border-radius:10px; aspect-ratio:1;">
            @php $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first(); @endphp
            @if ($primaryImage)
                <img id="main-product-image" src="{{ $primaryImage->image_url }}" alt="{{ $product->name }}">
            @else
                <span>Image bientôt disponible</span>
            @endif
        </div>

        <div>
            <p style="font-size:1.4rem; font-weight:800; margin-bottom:14px;">{{ number_format($product->price, 0, ',', ' ') }} FCFA</p>
            <p style="color:var(--muted); line-height:1.7; margin-bottom:20px;">{{ $product->description }}</p>
            <form method="POST" action="{{ route('cart.store', $product) }}">
                @csrf
                @include('partials.variant-picker', ['product' => $product])
                <button type="submit" class="button primary">Ajouter au panier</button>
            </form>
        </div>
    </div>

    @php
        $imagesByColor = $product->images->whereNotNull('color')->mapWithKeys(fn ($img) => [strtolower($img->color) => $img->image_url]);
    @endphp
    @if ($imagesByColor->isNotEmpty())
        <script>
            const colorImageMap = @json($imagesByColor);
            document.addEventListener('change', function (e) {
                if (e.target.matches('input[name="color"]')) {
                    const chosen = e.target.value.toLowerCase();
                    const mainImg = document.getElementById('main-product-image');
                    if (mainImg && colorImageMap[chosen]) {
                        mainImg.src = colorImageMap[chosen];
                    }
                }
            });
        </script>
    @endif

    <!-- Laisser un avis -->
    <div class="form-panel" style="margin-bottom:32px;">
        <h3 style="margin-top:0;">Donnez votre avis sur ce produit</h3>

        @if ($alreadyReviewed)
            <p style="color:var(--muted); margin:0;">Vous avez déjà laissé un avis pour ce produit. Merci pour votre retour !</p>
        @else
            <form method="POST" action="{{ route('reviews.store-product', $product) }}" style="display:flex; gap:12px; align-items:flex-start; flex-wrap:wrap;">
                @csrf
                <select name="rating" required style="padding:10px 12px; border:1px solid var(--line); border-radius:8px;">
                    <option value="">Votre note</option>
                    @for ($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}">{{ str_repeat('⭐', $i) }}</option>
                    @endfor
                </select>
                <input type="text" name="comment" placeholder="Votre commentaire (optionnel)" style="flex:1; min-width:220px; padding:10px 14px; border:1px solid var(--line); border-radius:8px;">
                <button type="submit" class="button primary">Publier mon avis</button>
            </form>
            @error('rating')
                <div class="error">{{ $message }}</div>
            @enderror
        @endif
    </div>

    <h3 style="margin-bottom:16px;">Avis clients ({{ $product->reviews->count() }})</h3>

    @if ($product->reviews->isEmpty())
        <div class="empty">Aucun avis pour ce produit pour le moment. Soyez le premier à donner votre avis !</div>
    @else
        <div class="feed">
            @foreach ($product->reviews as $review)
                @php
                    $reviewerName = $review->client->user->first_name ?? 'Client';
                    $initials = strtoupper(substr($reviewerName, 0, 1));
                @endphp
                <article class="feed-post">
                    <div class="feed-post-header">
                        <div class="feed-avatar">{{ $initials }}</div>
                        <div class="feed-post-meta">
                            <strong>{{ $reviewerName }}</strong>
                            <div class="feed-post-sub">
                                <span class="feed-stars">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                                <span class="feed-dot">·</span>
                                <span class="feed-time">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    @if ($review->comment)
                        <p class="feed-post-content">{{ $review->comment }}</p>
                    @endif
                    <div class="feed-post-footer">
                        <span>👍 Avis vérifié</span>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <style>
        .feed { display: flex; flex-direction: column; gap: 14px; max-width: 640px; }
        .feed-post { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 16px 18px; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
        .feed-post-header { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 10px; }
        .feed-avatar {
            flex-shrink: 0; width: 42px; height: 42px; border-radius: 50%;
            background: var(--accent); color: #fff; font-weight: 800; font-size: 1.05rem;
            display: flex; align-items: center; justify-content: center;
        }
        .feed-post-meta strong { font-size: .95rem; }
        .feed-post-sub { display: flex; align-items: center; gap: 6px; margin-top: 2px; font-size: .8rem; color: var(--muted); }
        .feed-stars { color: #e8a33d; letter-spacing: 1px; }
        .feed-dot { opacity: .6; }
        .feed-post-content { margin: 0 0 10px; padding-left: 54px; color: var(--ink); font-size: .94rem; line-height: 1.55; }
        .feed-post-footer { padding-left: 54px; padding-top: 8px; border-top: 1px solid var(--line); font-size: .8rem; color: var(--muted); font-weight: 600; }
    </style>
@endsection

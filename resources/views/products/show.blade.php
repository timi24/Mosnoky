@extends('layouts.client')

@section('title', $product->name)

@section('content')
    <div class="pdp-wrap">
        <!-- GALERIE PHOTO -->
        <div class="pdp-gallery">
            <div class="pdp-main-image">
                @php $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first(); @endphp
                @if ($primaryImage)
                    <img id="main-product-image" src="{{ $primaryImage->image_url }}" alt="{{ $product->name }}">
                @else
                    <span class="pdp-no-image">Image bientôt disponible</span>
                @endif
            </div>

            @if ($product->images->count() > 1)
                <div class="pdp-thumbs">
                    @foreach ($product->images as $image)
                        <button type="button"
                                class="pdp-thumb {{ $loop->first ? 'active' : '' }}"
                                data-image="{{ $image->image_url }}"
                                data-color="{{ $image->color ? strtolower($image->color) : '' }}"
                                onclick="pdpSetMainImage(this)">
                            <img src="{{ $image->image_url }}" alt="{{ $image->alternative_text }}">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- INFOS PRODUIT -->
        <div class="pdp-info">
            <h1 class="pdp-title">{{ $product->name }}</h1>

            <div class="pdp-meta">
                @if ($averageRating)
                    <span class="pdp-rating">
                        <span class="pdp-stars">{{ str_repeat('★', round($averageRating)) }}{{ str_repeat('☆', 5 - round($averageRating)) }}</span>
                        {{ number_format($averageRating, 1) }} · {{ $product->reviews->count() }} avis
                    </span>
                @else
                    <span class="pdp-rating pdp-muted">Aucun avis pour le moment</span>
                @endif
                <span class="pdp-sep">|</span>
                <span class="pdp-muted">{{ $soldCount }} vendu(s)</span>
                <span class="pdp-sep">|</span>
                <span class="pdp-muted">{{ $product->category->name ?? '' }}</span>
            </div>

            <div class="pdp-price-box">
                <span class="pdp-price">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                @if ($product->stock > 0)
                    <span class="pdp-stock ok">En stock ({{ $product->stock }} disponibles)</span>
                @else
                    <span class="pdp-stock out">Rupture de stock</span>
                @endif
            </div>

            <p class="pdp-desc-short">{{ Str::limit($product->description, 140) }}</p>

            <form method="POST" action="{{ route('cart.store', $product) }}" class="pdp-form">
                @csrf
                @include('partials.variant-picker', ['product' => $product])

                <div class="variant-group">
                    <span class="variant-label">Quantité</span>
                    <div class="pdp-qty-stepper">
                        <button type="button" onclick="pdpStepQty(-1)">−</button>
                        <input type="number" name="quantity" id="pdp-qty" value="1" min="1" max="{{ $product->stock }}" readonly>
                        <button type="button" onclick="pdpStepQty(1)">+</button>
                    </div>
                </div>

                <div class="pdp-actions">
                    <button type="submit" class="button primary pdp-btn-cart" {{ $product->stock < 1 ? 'disabled' : '' }}>Ajouter au panier</button>
                    <a href="{{ route('messages.index') }}" class="button pdp-btn-contact">Contacter la boutique</a>
                </div>
            </form>

            <div class="pdp-trust">
                <div class="pdp-trust-item">🚚 Livraison partout au Burkina Faso</div>
                <div class="pdp-trust-item">🔒 Paiement sécurisé (Orange Money, Moov Money, carte)</div>
                <div class="pdp-trust-item">✋ Fabriqué à la main, Made in Burkina Faso</div>
            </div>
        </div>
    </div>

    <!-- DESCRIPTION COMPLÈTE -->
    <div class="pdp-full-desc">
        <h3>Description</h3>
        <p>{{ $product->description }}</p>
    </div>

    <!-- AVIS -->
    <div class="pdp-review-form">
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

    @php
        $imagesByColor = $product->images->whereNotNull('color')->mapWithKeys(fn ($img) => [strtolower($img->color) => $img->image_url]);
    @endphp
    <script>
        const pdpColorImageMap = @json($imagesByColor);

        function pdpSetMainImage(btn) {
            document.getElementById('main-product-image').src = btn.dataset.image;
            document.querySelectorAll('.pdp-thumb').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');
        }

        function pdpStepQty(delta) {
            const input = document.getElementById('pdp-qty');
            const max = parseInt(input.max || 999, 10);
            let value = parseInt(input.value, 10) + delta;
            value = Math.max(1, Math.min(value, max));
            input.value = value;
        }

        document.addEventListener('change', function (e) {
            if (e.target.matches('input[name="color"]')) {
                const chosen = e.target.value.toLowerCase();
                const mainImg = document.getElementById('main-product-image');
                if (mainImg && pdpColorImageMap[chosen]) {
                    mainImg.src = pdpColorImageMap[chosen];
                    document.querySelectorAll('.pdp-thumb').forEach(t => {
                        t.classList.toggle('active', t.dataset.color === chosen);
                    });
                }
            }
        });
    </script>

    <style>
        .pdp-wrap { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .pdp-gallery { display: flex; flex-direction: column; gap: 12px; }
        .pdp-main-image { aspect-ratio: 1; border-radius: 12px; overflow: hidden; background: #eee7dc; display: flex; align-items: center; justify-content: center; border: 1px solid var(--line); }
        .pdp-main-image img { width: 100%; height: 100%; object-fit: cover; }
        .pdp-no-image { color: var(--muted); font-size: .9rem; }
        .pdp-thumbs { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 4px; scrollbar-width: thin; }
        .pdp-thumb { flex-shrink: 0; width: 66px; height: 66px; border-radius: 8px; overflow: hidden; border: 2px solid var(--line); padding: 0; cursor: pointer; background: none; transition: .15s ease; }
        .pdp-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .pdp-thumb:hover { border-color: var(--muted); }
        .pdp-thumb.active { border-color: var(--accent); box-shadow: 0 0 0 1px var(--accent); }

        .pdp-info { display: flex; flex-direction: column; }
        .pdp-title { font-family: Georgia, serif; font-size: 1.7rem; font-weight: 400; margin: 0 0 10px; line-height: 1.25; }
        .pdp-meta { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; font-size: .85rem; color: var(--muted); margin-bottom: 16px; }
        .pdp-rating { display: flex; align-items: center; gap: 6px; color: var(--ink); font-weight: 600; }
        .pdp-stars { color: #e8a33d; letter-spacing: 1px; }
        .pdp-sep { opacity: .4; }
        .pdp-muted { color: var(--muted); }

        .pdp-price-box { display: flex; align-items: center; gap: 16px; padding: 18px 20px; background: var(--paper); border-radius: 12px; margin-bottom: 16px; }
        .pdp-price { font-size: 1.9rem; font-weight: 800; color: var(--accent); }
        .pdp-stock { font-size: .82rem; font-weight: 700; padding: 4px 10px; border-radius: 999px; }
        .pdp-stock.ok { background: #eef1ea; color: #2f6f4e; }
        .pdp-stock.out { background: #fbe9e6; color: #b1372f; }

        .pdp-desc-short { color: var(--muted); font-size: .94rem; line-height: 1.6; margin-bottom: 20px; }

        .pdp-form { display: flex; flex-direction: column; gap: 4px; }
        .pdp-qty-stepper { display: inline-flex; align-items: center; border: 1.5px solid var(--line); border-radius: 10px; overflow: hidden; width: fit-content; }
        .pdp-qty-stepper button { width: 38px; height: 38px; border: none; background: var(--paper); font-size: 1.1rem; cursor: pointer; }
        .pdp-qty-stepper button:hover { background: var(--line); }
        .pdp-qty-stepper input { width: 50px; height: 38px; border: none; text-align: center; font-weight: 700; font-size: .95rem; -moz-appearance: textfield; }
        .pdp-qty-stepper input::-webkit-outer-spin-button, .pdp-qty-stepper input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

        .pdp-actions { display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap; }
        .pdp-btn-cart { flex: 1; min-width: 200px; padding: 14px 20px; font-size: 1rem; }
        .pdp-btn-contact { padding: 14px 20px; }

        .pdp-trust { margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--line); display: flex; flex-direction: column; gap: 8px; }
        .pdp-trust-item { font-size: .85rem; color: var(--muted); }

        .pdp-full-desc { max-width: 800px; margin: 10px 0 40px; padding-top: 20px; border-top: 1px solid var(--line); }
        .pdp-full-desc h3 { margin: 0 0 10px; }
        .pdp-full-desc p { color: var(--muted); line-height: 1.7; }

        .pdp-review-form { border: 1px solid var(--line); border-radius: 12px; background: var(--card); padding: 20px; margin-bottom: 24px; }

        .feed { display: flex; flex-direction: column; gap: 14px; max-width: 640px; }
        .feed-post { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 16px 18px; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
        .feed-post-header { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 10px; }
        .feed-avatar { flex-shrink: 0; width: 42px; height: 42px; border-radius: 50%; background: var(--accent); color: #fff; font-weight: 800; font-size: 1.05rem; display: flex; align-items: center; justify-content: center; }
        .feed-post-meta strong { font-size: .95rem; }
        .feed-post-sub { display: flex; align-items: center; gap: 6px; margin-top: 2px; font-size: .8rem; color: var(--muted); }
        .feed-stars { color: #e8a33d; letter-spacing: 1px; }
        .feed-dot { opacity: .6; }
        .feed-post-content { margin: 0 0 10px; padding-left: 54px; color: var(--ink); font-size: .94rem; line-height: 1.55; }
        .feed-post-footer { padding-left: 54px; padding-top: 8px; border-top: 1px solid var(--line); font-size: .8rem; color: var(--muted); font-weight: 600; }

        @media (max-width: 900px) {
            .pdp-wrap { grid-template-columns: 1fr; gap: 24px; }
        }
    </style>
@endsection

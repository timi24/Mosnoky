@extends('layouts.client')

@section('title', 'Résultats de recherche')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">
                @if ($query)
                    Résultats pour « {{ $query }} »
                @else
                    Rechercher un produit
                @endif
            </h1>
            <p class="page-subtitle">{{ $products->total() }} produit(s) trouvé(s)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('search.index') }}" style="margin-bottom:24px; display:flex; gap:10px; max-width:480px;">
        <input type="text" name="q" value="{{ $query }}" placeholder="Rechercher une paire de chaussures, un sac…"
               style="flex:1; padding:11px 14px; border:1px solid var(--line); border-radius:999px;">
        <button type="submit" class="button primary">Rechercher</button>
    </form>

    @if ($products->isEmpty())
        <div class="empty">Aucun produit ne correspond à votre recherche.</div>
    @else
        <div class="shop-grid">
            @foreach ($products as $product)
                <article class="shop-card">
                    <a href="{{ route('products.show', $product) }}">
                        <div class="shop-card-image">
                            @if ($product->images->isNotEmpty())
                                @foreach ($product->images as $i => $image)
                                    <img src="{{ $image->image_url }}" alt="{{ $image->alternative_text ?: $product->name }}" class="product-slide @if ($i === 0) active @endif">
                                @endforeach
                                @if ($product->images->count() > 1)
                                    <button type="button" class="thumb-arrow thumb-arrow-prev" aria-label="Photo précédente">‹</button>
                                    <button type="button" class="thumb-arrow thumb-arrow-next" aria-label="Photo suivante">›</button>
                                    <div class="thumb-dots">
                                        @foreach ($product->images as $i => $image)
                                            <button type="button" class="thumb-dot @if ($i === 0) active @endif" data-index="{{ $i }}" aria-label="Photo {{ $i + 1 }}"></button>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <span class="shop-no-image">Photo à venir</span>
                            @endif
                        </div>
                    </a>
                    <div class="shop-card-body">
                        <a href="{{ route('products.show', $product) }}" class="shop-card-title-link">
                            <h3 class="shop-card-title">{{ $product->name }}</h3>
                        </a>
                        <div class="shop-card-meta">
                            @if ($product->approved_reviews_count > 0)
                                <span class="shop-stars">{{ str_repeat('★', round($product->approved_reviews_avg_rating)) }}{{ str_repeat('☆', 5 - round($product->approved_reviews_avg_rating)) }}</span>
                                <span>{{ number_format($product->approved_reviews_avg_rating, 1) }}</span>
                            @else
                                <span class="shop-muted">Aucun avis</span>
                            @endif
                            <span class="shop-sep">·</span>
                            <span class="shop-muted">{{ $product->sold_count ?? 0 }} vendu(s)</span>
                        </div>
                        <div class="shop-card-footer">
                            <span class="shop-price">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                            <a href="{{ route('products.show', $product) }}" class="button primary small">Choisir le modèle</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div style="margin-top: 20px;">
            {{ $products->links() }}
        </div>
    @endif

    <style>
        /* Diaporama des photos produit (même principe que l'accueil client) */
        .shop-card-image { position: relative; touch-action: pan-y; }
        .product-slide { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0; transform: scale(1.04); transition: opacity 1.2s ease; will-change: opacity, transform; }
        .product-slide.active { opacity: 1; animation: kenburns 6s ease-in-out forwards; }
        @keyframes kenburns { from { transform: scale(1.08); } to { transform: scale(1); } }
        .thumb-dots { position: absolute; bottom: 10px; left: 0; right: 0; display: flex; justify-content: center; gap: 6px; z-index: 2; }
        .thumb-dot { position: relative; overflow: hidden; width: 6px; height: 6px; border-radius: 999px; border: none; background: rgba(255,255,255,.55); cursor: pointer; padding: 0; transition: width .3s ease, background .3s ease; }
        .thumb-dot.active { background: rgba(255,255,255,.45); width: 22px; }
        /* La barre se remplit pendant les 5 secondes de la photo */
        .thumb-dot.active::after { content: ""; position: absolute; inset: 0; background: #fff; transform-origin: left; animation: slide-progress 5s linear forwards; }
        .shop-card-image:hover .thumb-dot.active::after { animation-play-state: paused; }
        @keyframes slide-progress { from { transform: scaleX(0); } to { transform: scaleX(1); } }
        .thumb-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 34px;
            height: 34px;
            border: 1px solid rgba(255,255,255,.35);
            border-radius: 50%;
            background: rgba(255,255,255,.16);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
            color: #fff;
            font-size: 1.2rem;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 2;
            opacity: 0;
            transition: opacity .25s ease, background .2s ease;
        }
        .shop-card-image:hover .thumb-arrow, .thumb-arrow:focus-visible { opacity: 1; }
        .thumb-arrow:hover { background: rgba(255,255,255,.3); }
        .thumb-arrow-prev { left: 8px; }
        .thumb-arrow-next { right: 8px; }
        /* Sur téléphone/tablette il n'y a pas de survol : les flèches restent visibles */
        @media (hover: none) { .thumb-arrow { opacity: 1; } }
        @media (prefers-reduced-motion: reduce) {
            .product-slide.active { animation: none; transform: none; }
            .product-slide { transition-duration: .01s; }
            .thumb-dot.active::after { animation: none; transform: scaleX(1); }
        }
    </style>

    <script>
    (function () {
        document.querySelectorAll('.shop-card-image').forEach(function (thumb) {
            const slides = thumb.querySelectorAll('.product-slide');
            const dots = thumb.querySelectorAll('.thumb-dot');
            const prevBtn = thumb.querySelector('.thumb-arrow-prev');
            const nextBtn = thumb.querySelector('.thumb-arrow-next');
            if (slides.length <= 1) return;

            let current = 0;
            let timer = null;

            function show(index) {
                slides[current].classList.remove('active');
                dots[current] && dots[current].classList.remove('active');
                current = (index + slides.length) % slides.length;
                slides[current].classList.add('active');
                dots[current] && dots[current].classList.add('active');
            }

            function next() { show(current + 1); }
            function prev() { show(current - 1); }

            function start() { stop(); timer = setInterval(next, 5000); }
            function stop() { clearInterval(timer); }
            function restart() { stop(); start(); }
            start();

            // Pause au survol de la souris seulement (sur téléphone, le "survol"
            // resterait bloqué après un toucher et arrêterait le défilement).
            thumb.addEventListener('pointerenter', function (e) { if (e.pointerType === 'mouse') stop(); });
            thumb.addEventListener('pointerleave', function (e) { if (e.pointerType === 'mouse') start(); });

            // On économise la batterie quand l'onglet n'est pas visible
            document.addEventListener('visibilitychange', function () {
                if (document.hidden) { stop(); } else { start(); }
            });

            // Glisser du doigt (gauche/droite) pour changer de photo
            let startX = 0, startY = 0, swiped = false;
            thumb.addEventListener('touchstart', function (e) {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
                swiped = false;
            }, { passive: true });
            thumb.addEventListener('touchend', function (e) {
                const dx = e.changedTouches[0].clientX - startX;
                const dy = e.changedTouches[0].clientY - startY;
                if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) {
                    swiped = true;
                    dx < 0 ? next() : prev();
                    restart();
                }
            }, { passive: true });
            // Un glissement ne doit pas ouvrir la page du produit
            thumb.addEventListener('click', function (e) {
                if (swiped) { e.preventDefault(); e.stopPropagation(); swiped = false; }
            }, true);

            // Le client peut cliquer pour voir chaque photo lui-même, sans
            // que ça ouvre la page du produit.
            if (prevBtn) {
                prevBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    prev();
                    restart();
                });
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    next();
                    restart();
                });
            }
            dots.forEach(function (dot) {
                dot.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    show(parseInt(dot.dataset.index, 10));
                    restart();
                });
            });
        });
    })();
    </script>
@endsection
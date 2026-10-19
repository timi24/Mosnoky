@extends('layouts.client')

@section('title', 'Catégories')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Catégories</h1>
            <p class="page-subtitle">Parcourez notre collection par catégorie.</p>
        </div>
    </div>

    @if ($categories->isEmpty())
        <div class="empty">Aucune catégorie disponible pour le moment.</div>
    @else
        <div class="grid-cats">
            @foreach ($categories as $category)
                <a class="cat-card" href="{{ route('categories.show', $category) }}">
                    <div class="cat-card-image">
                        @if ($category->preview_images->isNotEmpty())
                            @foreach ($category->preview_images as $i => $image)
                                <img src="{{ $image->image_url }}" alt="{{ $category->name }}" class="cat-slide @if ($i === 0) active @endif">
                            @endforeach
                            @if ($category->preview_images->count() > 1)
                                <button type="button" class="cat-arrow cat-arrow-prev" aria-label="Photo précédente">‹</button>
                                <button type="button" class="cat-arrow cat-arrow-next" aria-label="Photo suivante">›</button>
                                <div class="cat-thumb-dots">
                                    @foreach ($category->preview_images as $i => $image)
                                        <button type="button" class="cat-thumb-dot @if ($i === 0) active @endif" data-index="{{ $i }}" aria-label="Photo {{ $i + 1 }}"></button>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <span class="cat-no-image">Photo à venir</span>
                        @endif
                    </div>
                    <div class="cat-card-body">
                        <h3>{{ $category->name }}</h3>
                        <p class="description">{{ Str::limit($category->description, 90) }}</p>
                        <span class="pill muted">{{ $category->products_count }} produit(s)</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <style>
        .cat-card-image {
            position: relative;
            aspect-ratio: 4/3;
            background: #eee7dc;
            overflow: hidden;
            border-radius: 12px 12px 0 0;
        }
        .cat-no-image {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--muted);
            font-size: .85rem;
        }
        .cat-slide {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transform: scale(1.04);
            transition: opacity 1.2s ease;
        }
        .cat-slide.active {
            opacity: 1;
            animation: cat-kenburns 6s ease-in-out forwards;
        }
        @keyframes cat-kenburns {
            from { transform: scale(1.08); }
            to { transform: scale(1); }
        }
        .cat-thumb-dots {
            position: absolute;
            bottom: 10px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 6px;
            z-index: 2;
        }
        .cat-thumb-dot {
            position: relative;
            overflow: hidden;
            width: 6px;
            height: 6px;
            border-radius: 999px;
            border: none;
            padding: 0;
            background: rgba(255,255,255,.55);
            cursor: pointer;
            transition: width .3s ease, background .3s ease;
        }
        .cat-thumb-dot.active { background: rgba(255,255,255,.45); width: 22px; }
        /* La barre se remplit pendant les 5 secondes de la photo */
        .cat-thumb-dot.active::after { content: ""; position: absolute; inset: 0; background: #fff; transform-origin: left; animation: cat-progress 5s linear forwards; }
        .cat-card-image:hover .cat-thumb-dot.active::after { animation-play-state: paused; }
        @keyframes cat-progress { from { transform: scaleX(0); } to { transform: scaleX(1); } }
        .cat-arrow {
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
        .cat-card-image:hover .cat-arrow, .cat-arrow:focus-visible { opacity: 1; }
        .cat-arrow:hover { background: rgba(255,255,255,.3); }
        /* Sur téléphone/tablette il n'y a pas de survol : les flèches restent visibles */
        @media (hover: none) { .cat-arrow { opacity: 1; } }
        .cat-slide { will-change: opacity, transform; }
        .cat-card-image { touch-action: pan-y; }
        @media (prefers-reduced-motion: reduce) {
            .cat-slide.active { animation: none; transform: none; }
            .cat-slide { transition-duration: .01s; }
            .cat-thumb-dot.active::after { animation: none; transform: scaleX(1); }
        }
        .cat-arrow-prev { left: 8px; }
        .cat-arrow-next { right: 8px; }
        .cat-card-body { padding: 16px; }
    </style>

    <script>
    (function () {
        document.querySelectorAll('.cat-card-image').forEach(function (thumb) {
            const slides = thumb.querySelectorAll('.cat-slide');
            const dots = thumb.querySelectorAll('.cat-thumb-dot');
            const prevBtn = thumb.querySelector('.cat-arrow-prev');
            const nextBtn = thumb.querySelector('.cat-arrow-next');
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
            // Un glissement ne doit pas ouvrir la page de la catégorie
            thumb.addEventListener('click', function (e) {
                if (swiped) { e.preventDefault(); e.stopPropagation(); swiped = false; }
            }, true);

            // Le client peut cliquer pour voir chaque photo lui-même, sans
            // que ça ouvre la page de la catégorie.
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
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
                                <div class="cat-thumb-dots">
                                    @foreach ($category->preview_images as $i => $image)
                                        <span class="cat-thumb-dot @if ($i === 0) active @endif"></span>
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
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,.55);
            transition: .25s ease;
        }
        .cat-thumb-dot.active {
            background: #fff;
            width: 16px;
            border-radius: 4px;
        }
        .cat-card-body { padding: 16px; }
    </style>

    <script>
    (function () {
        document.querySelectorAll('.cat-card-image').forEach(function (thumb) {
            const slides = thumb.querySelectorAll('.cat-slide');
            const dots = thumb.querySelectorAll('.cat-thumb-dot');
            if (slides.length <= 1) return;

            let current = 0;
            let timer = null;

            function next() {
                slides[current].classList.remove('active');
                dots[current] && dots[current].classList.remove('active');
                current = (current + 1) % slides.length;
                slides[current].classList.add('active');
                dots[current] && dots[current].classList.add('active');
            }

            function start() { timer = setInterval(next, 5000); }
            start();

            thumb.addEventListener('mouseenter', function () { clearInterval(timer); });
            thumb.addEventListener('mouseleave', start);
        });
    })();
    </script>
@endsection
@extends('layouts.client')

@section('title', 'Accueil')

@section('content')
    <!-- HERO -->
    <section class="dash-hero">
        <div class="dash-hero-content">
            <div class="dash-eyebrow">Espace client privilégié</div>
            <h1>Ravi de vous revoir, {{ auth()->user()->first_name ?? auth()->user()->name }}.</h1>
            <p>Découvrez nos créations artisanales — chaussures de luxe et maroquinerie, fabriquées à la main au Burkina Faso.</p>
            <a class="dash-button primary" href="{{ route('categories.index') }}">Découvrir les produits</a>
        </div>
    </section>

    <!-- STATS RAPIDES -->
    <section class="dash-stats">
        <a class="dash-stat-card" href="{{ route('orders.index') }}">
            <span class="label">Commandes</span>
            <span class="value">{{ $orderCount ?? 0 }}</span>
        </a>
        <a class="dash-stat-card" href="{{ route('cart.index') }}">
            <span class="label">Panier</span>
            <span class="value">{{ $cartItemCount ?? 0 }}</span>
        </a>
        <a class="dash-stat-card" href="{{ route('messages.index') }}">
            <span class="label">Messages</span>
            <span class="value">{{ $unreadMessageCount ?? 0 }}</span>
        </a>
        <a class="dash-stat-card" href="{{ route('categories.index') }}">
            <span class="label">Catégories</span>
            <span class="value">{{ $categoryCount ?? 0 }}</span>
        </a>
    </section>

    <!-- PRODUITS -->
    <section class="dash-section">
        <div class="page-head">
            <div>
                <div class="dash-eyebrow">Notre catalogue</div>
                <h2 class="dash-h2">Nos produits</h2>
            </div>
            <a class="dash-button" href="{{ route('categories.index') }}">Voir tout le catalogue →</a>
        </div>

        @if (!isset($products) || $products->isEmpty())
            <div class="empty">Les produits arrivent bientôt. Revenez découvrir la prochaine sélection.</div>
        @else
            <div class="shop-grid">
                @foreach ($products as $product)
                    <article class="shop-card">
                        <a href="{{ route('products.show', $product) }}">
                            <div class="shop-card-image">
                                @if ($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->alternative_text ?: $product->name }}">
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
        @endif
    </section>

    <style>
        .dash-hero {
            position: relative;
            margin-top: 6px;
            border-radius: 18px;
            overflow: hidden;
            min-height: 300px;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #221f1d, #3a332c);
        }
        .dash-hero-content { position: relative; z-index: 2; padding: 48px; color: #fff; max-width: 620px; }
        .dash-eyebrow { color: var(--accent); font-size: .78rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
        .dash-hero .dash-eyebrow { color: #f0b8ac; }
        .dash-hero h1 { font-family: Georgia, serif; font-size: clamp(1.9rem, 4vw, 2.8rem); font-weight: 400; line-height: 1.08; margin: 14px 0 16px; }
        .dash-hero p { font-size: 1rem; line-height: 1.7; color: #e8e3db; margin-bottom: 22px; max-width: 480px; }

        .dash-button { display: inline-flex; align-items: center; justify-content: center; gap: 6px; border: 1px solid var(--line); border-radius: 999px; padding: 10px 18px; font-size: .88rem; font-weight: 700; transition: .2s ease; cursor: pointer; background: transparent; color: inherit; }
        .dash-button:hover { border-color: var(--ink); transform: translateY(-1px); }
        .dash-button.primary { border-color: var(--accent); background: var(--accent); color: #fff; }
        .dash-hero .dash-button { border-color: rgba(255,255,255,.4); color: #fff; }
        .dash-hero .dash-button.primary { border-color: var(--accent); background: var(--accent); }

        .dash-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin: 28px 0 40px; }
        .dash-stat-card { display: flex; flex-direction: column; gap: 6px; padding: 20px; border: 1px solid var(--line); border-radius: 12px; background: var(--card); transition: .2s ease; }
        .dash-stat-card:hover { border-color: var(--accent); transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,0,0,.05); }
        .dash-stat-card .label { font-size: .76rem; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); }
        .dash-stat-card .value { font-size: 1.7rem; font-weight: 800; }

        .dash-section { padding: 20px 0 50px; border-top: 1px solid var(--line); }
        .dash-h2 { margin: 4px 0 0; font-family: Georgia, serif; font-size: 1.9rem; font-weight: 400; }

        /* Carte produit unifiée style Alibaba - réutilisée sur toutes les pages catalogue */
        .shop-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
        .shop-card { display: flex; flex-direction: column; border: 1px solid var(--line); border-radius: 12px; overflow: hidden; background: var(--card); transition: .2s ease; }
        .shop-card:hover { border-color: var(--accent); transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,.06); }
        .shop-card-image { aspect-ratio: 4/3; background: #eee7dc; overflow: hidden; }
        .shop-card-image img { width: 100%; height: 100%; object-fit: cover; }
        .shop-no-image { display: flex; align-items: center; justify-content: center; height: 100%; color: var(--muted); font-size: .85rem; }
        .shop-card-body { padding: 16px; display: flex; flex-direction: column; gap: 6px; flex: 1; }
        .shop-card-title-link { color: inherit; }
        .shop-card-title { margin: 0; font-size: 1rem; line-height: 1.3; }
        .shop-card-meta { display: flex; align-items: center; gap: 5px; font-size: .8rem; color: var(--muted); }
        .shop-stars { color: #e8a33d; letter-spacing: 1px; }
        .shop-sep { opacity: .5; }
        .shop-muted { color: var(--muted); }
        .shop-card-footer { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-top: auto; padding-top: 8px; }
        .shop-price { font-weight: 800; font-size: 1.05rem; color: var(--accent); }

        @media (max-width: 900px) {
            .dash-stats { grid-template-columns: repeat(2, 1fr); }
            .shop-grid { grid-template-columns: repeat(2, 1fr); }
            .dash-hero-content { padding: 32px; }
        }
        @media (max-width: 600px) {
            .shop-grid { grid-template-columns: 1fr; }
        }
    </style>
@endsection

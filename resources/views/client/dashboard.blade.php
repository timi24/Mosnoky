@extends('layouts.client')

@section('title', 'Accueil')

@section('content')
    <section style="padding: 20px 0 40px;">
        <div class="eyebrow" style="color:var(--accent); font-size:.78rem; font-weight:800; letter-spacing:.14em; text-transform:uppercase;">Espace Client Privilégié</div>
        <h1 style="max-width:700px; margin:14px 0 18px; font-family:Georgia, serif; font-size:clamp(2.2rem, 5vw, 3.6rem); font-weight:400; line-height:1;">
            Ravi de vous revoir, {{ auth()->user()->first_name ?? auth()->user()->name }}.
        </h1>
        <p style="max-width:540px; color:var(--muted); font-size:1.05rem; line-height:1.7;">
            Découvrez nos créations artisanales — chaussures de luxe et maroquinerie, fabriquées à la main au Burkina Faso.
        </p>
        <a class="button primary" href="{{ route('categories.index') }}">Découvrir les produits</a>
    </section>

    <section style="border-top:1px solid var(--line); border-bottom:1px solid var(--line); padding:28px 0; margin-bottom:36px;">
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:14px;">
            <a class="cat-card" href="{{ route('orders.index') }}">
                <span style="font-size:.78rem; font-weight:800; text-transform:uppercase; color:var(--muted);">Commandes</span>
                <span style="font-size:1.6rem; font-weight:800;">{{ $orderCount ?? 0 }}</span>
            </a>
            <a class="cat-card" href="{{ route('cart.index') }}">
                <span style="font-size:.78rem; font-weight:800; text-transform:uppercase; color:var(--muted);">Panier</span>
                <span style="font-size:1.6rem; font-weight:800;">{{ $cartItemCount ?? 0 }}</span>
            </a>
            <a class="cat-card" href="{{ route('messages.index') }}">
                <span style="font-size:.78rem; font-weight:800; text-transform:uppercase; color:var(--muted);">Messages</span>
                <span style="font-size:1.6rem; font-weight:800;">{{ $unreadMessageCount ?? 0 }}</span>
            </a>
            <a class="cat-card" href="{{ route('categories.index') }}">
                <span style="font-size:.78rem; font-weight:800; text-transform:uppercase; color:var(--muted);">Catégories</span>
                <span style="font-size:1.6rem; font-weight:800;">{{ $categoryCount ?? 0 }}</span>
            </a>
        </div>
    </section>

    <section>
        <div class="page-head">
            <h2 style="margin:0; font-family:Georgia, serif; font-size:2rem; font-weight:400;">Nos produits</h2>
        </div>

        @if (!isset($products) || $products->isEmpty())
            <div class="empty">Les produits arrivent bientôt. Revenez découvrir la prochaine sélection.</div>
        @else
            <div class="products">
                @foreach ($products as $product)
                    <article class="product">
                        <a href="{{ route('products.show', $product) }}">
                            <div class="product-image">
                                @if ($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->alternative_text ?: $product->name }}">
                                @else
                                    <span>Image bientôt disponible</span>
                                @endif
                            </div>
                        </a>
                        <div class="product-content">
                            <a href="{{ route('products.show', $product) }}" style="color:inherit;"><h3>{{ $product->name }}</h3></a>
                            <div style="font-size:.82rem; color:var(--muted);">
                                @if ($product->approved_reviews_count > 0)
                                    {{ str_repeat('⭐', round($product->approved_reviews_avg_rating)) }}
                                    <span>{{ number_format($product->approved_reviews_avg_rating, 1) }} ({{ $product->approved_reviews_count }} avis)</span>
                                @else
                                    <span>Aucun avis pour le moment</span>
                                @endif
                            </div>
                            <p class="description">{{ Str::limit($product->description, 90) }}</p>
                            <div class="product-footer">
                                <span class="price">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                <form method="POST" action="{{ route('cart.store', $product->id) }}">
                                    @csrf
                                    @include('partials.variant-picker', ['product' => $product])
                                    <button type="submit" class="button primary small">Ajouter</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection

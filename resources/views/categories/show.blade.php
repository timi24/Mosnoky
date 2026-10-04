@extends('layouts.client')

@section('title', $category->name)

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">{{ $category->name }}</h1>
            <p class="page-subtitle">{{ $category->description }}</p>
        </div>
        <a class="button small" href="{{ route('categories.index') }}">← Toutes les catégories</a>
    </div>

    @if ($products->isEmpty())
        <div class="empty">Aucun produit disponible dans cette catégorie pour le moment.</div>
    @else
        <div class="shop-grid">
            @foreach ($products as $product)
                <article class="shop-card">
                    <a href="{{ route('products.show', $product) }}">
                        <div class="shop-card-image">
                            @php $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first(); @endphp
                            @if ($primaryImage)
                                <img src="{{ $primaryImage->image_url }}" alt="{{ $primaryImage->alternative_text ?: $product->name }}">
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
@endsection

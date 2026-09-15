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
        <div class="products">
            @foreach ($products as $product)
                <article class="product">
                    <a href="{{ route('products.show', $product) }}">
                        <div class="product-image">
                            @php $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first(); @endphp
                            @if ($primaryImage)
                                <img src="{{ $primaryImage->image_url }}" alt="{{ $product->name }}">
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
                            <form method="POST" action="{{ route('cart.store', $product) }}">
                                @csrf
                                @include('partials.variant-picker', ['product' => $product])
                                <button type="submit" class="button primary small">Ajouter</button>
                            </form>
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

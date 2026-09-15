@extends('layouts.client')

@section('title', 'Mon panier')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Mon panier</h1>
        </div>
    </div>

    @if ($cart->items->isEmpty())
        <div class="empty">Votre panier est vide. <a href="{{ route('categories.index') }}" style="color:var(--accent); font-weight:700;">Parcourir les produits</a></div>
    @else
        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th>Sous-total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart->items as $item)
                        <tr>
                            <td>
                                {{ $item->product->name ?? 'Produit indisponible' }}
                                @if ($item->size || $item->color)
                                    <div style="font-size:.78rem; color:var(--muted); margin-top:2px;">
                                        @if ($item->size) Taille : {{ $item->size }} @endif
                                        @if ($item->size && $item->color) · @endif
                                        @if ($item->color) Couleur : {{ $item->color }} @endif
                                    </div>
                                @endif
                            </td>
                            <td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                            <td>
                                <form method="POST" action="{{ route('cart.update', $item) }}" style="display:flex; gap:8px; align-items:center;">
                                    @csrf
                                    @method('PUT')
                                    <input class="qty-input" type="number" name="quantity" min="1" value="{{ $item->quantity }}">
                                    <button type="submit" class="button small">Mettre à jour</button>
                                </form>
                            </td>
                            <td>{{ number_format($item->quantity * $item->unit_price, 0, ',', ' ') }} FCFA</td>
                            <td>
                                <form method="POST" action="{{ route('cart.destroy', $item) }}" onsubmit="return confirm('Retirer ce produit du panier ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button small danger">Retirer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="display:flex; justify-content:flex-end; margin-top:20px;">
            <div class="form-panel" style="width:320px;">
                <p style="display:flex; justify-content:space-between; margin:0 0 12px; font-weight:800; font-size:1.1rem;">
                    <span>Sous-total</span>
                    <span>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                </p>
                <a href="{{ route('orders.create') }}" class="button primary" style="width:100%;">Passer la commande</a>
            </div>
        </div>
    @endif
@endsection

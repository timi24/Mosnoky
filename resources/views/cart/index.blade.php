@extends('layouts.client')

@section('title', 'Mon panier')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Mon panier</h1>
            <p class="page-subtitle">Ajoutez d’autres articles ou passez directement à la commande. Votre panier est conservé sur ce compte.</p>
        </div>
    </div>

    @if ($cart->items->isEmpty())
        <div class="empty">Votre panier est vide. <a href="{{ route('categories.index') }}" style="color:var(--accent); font-weight:700;">Parcourir les produits</a></div>
    @else
        <div class="panel">
            <table class="cart-items-table">
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
                            <td data-label="Produit">
                                {{ $item->product->name ?? 'Produit indisponible' }}
                                @if ($item->size || $item->color)
                                    <div style="font-size:.78rem; color:var(--muted); margin-top:2px;">
                                        @if ($item->size) Taille : {{ $item->size }} @endif
                                        @if ($item->size && $item->color) · @endif
                                        @if ($item->color) Couleur : {{ $item->color }} @endif
                                    </div>
                                @endif
                            </td>
                            <td data-label="Prix unitaire">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                            <td data-label="Quantité">
                                <form method="POST" action="{{ route('cart.update', $item) }}" class="cart-quantity-form">
                                    @csrf
                                    @method('PUT')
                                    <input class="qty-input" type="number" name="quantity" min="1" max="{{ $item->product?->stock ?? 0 }}" value="{{ $item->quantity }}" aria-label="Quantité de {{ $item->product->name ?? 'ce produit' }}">
                                    <button type="submit" class="button small">Mettre à jour</button>
                                </form>
                                @error('quantity')<div class="error" style="margin-top:6px;">{{ $message }}</div>@enderror
                            </td>
                            <td data-label="Sous-total">{{ number_format($item->quantity * $item->unit_price, 0, ',', ' ') }} FCFA</td>
                            <td data-label="Action">
                                <form method="POST" action="{{ route('cart.destroy', $item) }}" class="cart-remove-form" onsubmit="return confirm('Retirer ce produit du panier ?');">
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

        <div class="cart-next-step">
            <div class="form-panel cart-summary">
                <p style="display:flex; justify-content:space-between; margin:0 0 12px; font-weight:800; font-size:1.1rem;">
                    <span>Sous-total</span>
                    <span>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                </p>
                <p class="cart-summary-line"><span>Livraison</span><span>{{ number_format($shippingFee, 0, ',', ' ') }} FCFA</span></p>
                <p class="cart-summary-total"><span>Total estimé</span><span>{{ number_format($subtotal + $shippingFee, 0, ',', ' ') }} FCFA</span></p>
                <div class="cart-next-actions">
                    <a href="{{ route('client.dashboard') }}" class="button cart-continue">Continuer mes achats</a>
                    <a href="{{ route('orders.create') }}" class="button primary cart-checkout">Passer la commande</a>
                </div>
            </div>
        </div>
    @endif

    <style>
        .cart-items-table { width: 100%; }
        .cart-items-table td { vertical-align: middle; }
        .cart-quantity-form { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .cart-quantity-form .qty-input { width: 76px; min-height: 38px; padding: 7px 9px; border: 1px solid var(--line); border-radius: 9px; font: inherit; }
        .cart-summary-line, .cart-summary-total { display: flex; justify-content: space-between; gap: 12px; margin: 8px 0; color: var(--muted); font-size: .9rem; }
        .cart-summary-total { margin: 12px 0 16px; padding-top: 12px; border-top: 1px solid var(--line); color: var(--ink); font-size: 1.05rem; font-weight: 800; }
        .cart-next-step { display: flex; justify-content: flex-end; margin-top: 20px; }
        .cart-summary { width: min(100%, 390px); padding: 22px; border-radius: 14px; box-shadow: 0 10px 26px rgba(34,31,29,.05); }
        .cart-next-actions { display: grid; grid-template-columns: 1fr; gap: 10px; }
        .cart-next-actions .button { width: 100%; min-height: 44px; }
        .cart-continue { color: var(--ink); background: #fff; }
        .cart-continue:hover { border-color: var(--accent); color: var(--accent); }
        .cart-checkout { box-shadow: 0 6px 15px rgba(177,55,47,.2); }
        @media (max-width: 760px) {
            .cart-items-table, .cart-items-table tbody { display: block; width: 100%; overflow: visible; }
            .cart-items-table thead { display: none; }
            .cart-items-table tbody tr { display: grid; grid-template-columns: 1fr; margin-bottom: 13px; padding: 8px 15px; border: 1px solid var(--line); border-radius: 14px; background: var(--card); }
            .cart-items-table tbody td { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--line); text-align: right; }
            .cart-items-table tbody td::before { content: attr(data-label); color: var(--muted); flex: 0 0 auto; font-size: .75rem; font-weight: 700; text-align: left; }
            .cart-items-table tbody td:first-child { display: block; text-align: left; font-weight: 700; }
            .cart-items-table tbody td:first-child::before { display: block; margin-bottom: 4px; }
            .cart-quantity-form { justify-content: flex-end; flex-wrap: nowrap; }
            .cart-quantity-form .button, .cart-remove-form .button { min-height: 38px; }
            .cart-items-table tbody td:last-child { border-bottom: 0; }
            .cart-next-step { justify-content: stretch; }
            .cart-summary { width: 100%; padding: 18px; }
            .cart-next-actions { grid-template-columns: 1fr; }
        }
    </style>
@endsection

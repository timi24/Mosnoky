@extends('layouts.client')

@section('title', 'Finaliser la commande')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Finaliser ma commande</h1>
        </div>
        <a class="button small" href="{{ route('cart.index') }}">← Retour au panier</a>
    </div>

    @if (! $cart || $cart->items->isEmpty())
        <div class="empty">Votre panier est vide.</div>
    @else
        <div style="display:grid; grid-template-columns: 1.3fr 1fr; gap:20px; align-items:start;">
            <div class="form-panel">
                <h3 style="margin-top:0;">Adresse de livraison</h3>
                <form method="POST" action="{{ route('orders.store') }}">
                    @csrf

                    <div class="field">
                        <label for="street">Adresse</label>
                        <input type="text" id="street" name="street" value="{{ old('street') }}" required>
                        @error('street')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="city">Ville</label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}" required>
                        @error('city')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="country">Pays</label>
                        <input type="text" id="country" name="country" value="{{ old('country', 'Burkina Faso') }}" required>
                        @error('country')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="postal_code">Code postal (optionnel)</label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="button primary">Confirmer la commande</button>
                    </div>
                </form>
            </div>

            <div class="form-panel">
                <h3 style="margin-top:0;">Récapitulatif</h3>
                @foreach ($cart->items as $item)
                    <p style="display:flex; justify-content:space-between; margin:6px 0; font-size:.9rem;">
                        <span>{{ $item->product->name ?? '—' }} × {{ $item->quantity }}</span>
                        <span>{{ number_format($item->quantity * $item->unit_price, 0, ',', ' ') }} FCFA</span>
                    </p>
                @endforeach
                <hr style="border:none; border-top:1px solid var(--line); margin:12px 0;">
                <p style="display:flex; justify-content:space-between; margin:4px 0;"><span>Sous-total</span><span>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span></p>
                <p style="display:flex; justify-content:space-between; margin:4px 0;"><span>Livraison</span><span>{{ number_format($shippingFee, 0, ',', ' ') }} FCFA</span></p>
                <p style="display:flex; justify-content:space-between; margin:10px 0 0; font-weight:800; border-top:1px solid var(--line); padding-top:10px;"><span>Total</span><span>{{ number_format($subtotal + $shippingFee, 0, ',', ' ') }} FCFA</span></p>
            </div>
        </div>
    @endif
@endsection

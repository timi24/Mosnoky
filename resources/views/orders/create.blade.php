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
        <div class="checkout-grid">
            <div class="form-panel">
                <h3 style="margin-top:0;">Adresse de livraison</h3>
                <p class="checkout-intro">Renseignez un numéro joignable pour organiser la remise de votre commande.</p>
                @error('cart')<div class="alert alert-danger">{{ $message }}</div>@enderror
                <form method="POST" action="{{ route('orders.store') }}">
                    @csrf

                    <div class="field">
                        <label for="phone">Téléphone pour la livraison</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="Ex. 70 12 34 56" inputmode="tel" autocomplete="tel" required>
                        @error('phone')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="street">Adresse</label>
                        <input type="text" id="street" name="street" value="{{ old('street', $deliveryAddress?->street) }}" autocomplete="street-address" required>
                        @error('street')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="city">Ville</label>
                        <input type="text" id="city" name="city" value="{{ old('city', $deliveryAddress?->city) }}" autocomplete="address-level2" required>
                        @error('city')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="country">Pays</label>
                        <input type="text" id="country" name="country" value="{{ old('country', $deliveryAddress?->country ?? 'Burkina Faso') }}" autocomplete="country-name" required>
                        @error('country')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="postal_code">Code postal (optionnel)</label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $deliveryAddress?->postal_code) }}" autocomplete="postal-code">
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
                <div class="checkout-delivery-note">
                    <strong>Livraison partout au Burkina Faso</strong>
                    <span>Le tarif est indiqué ci-dessus. La date estimée sera ajoutée au suivi dès confirmation par l’équipe.</span>
                </div>
                <p class="checkout-payment-note">Paiement par Orange Money ou Moov Money, vérifié par l’équipe après votre déclaration.</p>
            </div>
        </div>
    @endif

    <style>
        .checkout-grid { display: grid; grid-template-columns: minmax(0, 1.3fr) minmax(280px, 1fr); gap: 20px; align-items: start; }
        .checkout-intro, .checkout-payment-note { color: var(--muted); font-size: .9rem; line-height: 1.55; }
        .checkout-grid input[type="tel"] { width: 100%; padding: 10px 12px; border: 1px solid var(--line); border-radius: 8px; font: inherit; }
        .checkout-delivery-note { display: grid; gap: 4px; margin-top: 16px; padding: 12px; border-radius: 10px; background: var(--paper); font-size: .84rem; line-height: 1.5; }
        .checkout-delivery-note span { color: var(--muted); }
        .checkout-payment-note { margin-bottom: 0; }
        @media (max-width: 760px) { .checkout-grid { grid-template-columns: minmax(0, 1fr); } }
    </style>
@endsection

@extends('layouts.client')

@section('title', 'Commande ' . $order->order_number)

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Commande {{ $order->order_number }}</h1>
            <p class="page-subtitle">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <div style="display:flex; gap:8px;">
            @if ($order->status === 'EN_ATTENTE' && ! $order->payment?->submitted_at)
                <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Annuler cette commande ?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="button small danger">Annuler la commande</button>
                </form>
            @endif
            <a class="button small" href="{{ route('reports.create', ['order_id' => $order->id]) }}">Signaler un problème</a>
            <a class="button small" href="{{ route('orders.index') }}">← Mes commandes</a>
        </div>
    </div>

    <!-- Paiement en ligne -->
    @if ($order->status === 'EN_ATTENTE' && $order->payment?->isAwaitingVerification())
        <div class="order-payment-card order-payment-pending">
            <div>
                <span class="order-payment-eyebrow">Paiement déclaré</span>
                <h3>Vérification en cours</h3>
                <p>Votre déclaration a bien été reçue. L’équipe vérifie le transfert et vous informera dès qu’il sera confirmé.</p>
            </div>
            <a class="button" href="{{ route('payments.manual.show', $order) }}">Voir le suivi</a>
        </div>
    @elseif ($order->status === 'EN_ATTENTE' && (! $order->payment || $order->payment->status !== 'ACCEPTE'))
        <div class="order-payment-card">
            <div>
                <span class="order-payment-eyebrow">Paiement Mobile Money</span>
                <h3 style="margin:0 0 4px;">Payer cette commande en ligne</h3>
                <p style="margin:0; color:var(--muted); font-size:.9rem;">Orange Money ou Moov Money</p>
                @if ($order->payment?->status === 'REFUSE')
                    <p style="margin:6px 0 0; color:var(--danger); font-size:.85rem; font-weight:700;">Le dernier paiement a échoué. Vous pouvez réessayer.</p>
                @endif
            </div>
            <div class="order-payment-action">
                <strong>{{ number_format($order->total, 0, ',', ' ') }} FCFA</strong>
                <form method="POST" action="{{ route('payments.cinetpay.initiate', $order) }}">
                    @csrf
                    <button type="submit" class="button primary">Payer maintenant</button>
                </form>
            </div>
        </div>
    @elseif ($order->payment?->status === 'ACCEPTE')
        <div class="alert alert-success order-payment-confirmed" style="margin-bottom:24px;">
            <span>✅ Paiement reçu — {{ $order->payment->paid_at?->format('d/m/Y à H:i') }}</span>
            <a class="button small" href="{{ route('receipts.download', $order) }}">📄 Télécharger le reçu PDF</a>
        </div>
    @endif

    <!-- Timeline de livraison -->
    @php
        $steps = ['Commande reçue', 'Confirmée', 'Préparation', 'Expédiée', 'Livrée'];
        $deliveryStatus = $order->delivery?->status;
        $currentIndex = match ($deliveryStatus) {
            'LIVREE' => 4,
            'EXPEDIEE', 'EN_TRANSIT' => 3,
            'RETARDEE', 'ECHEC' => match ($order->status) {
                'EXPEDIEE' => 3,
                'EN_PREPARATION' => 2,
                'CONFIRMEE' => 1,
                default => 0,
            },
            'EN_PREPARATION' => 2,
            default => match ($order->status) {
                'LIVREE' => 4,
                'EXPEDIEE' => 3,
                'EN_PREPARATION' => 2,
                'CONFIRMEE' => 1,
                default => 0,
            },
        };
    @endphp
    @if ($order->status === 'ANNULEE')
        <div class="alert alert-danger" style="margin-bottom:24px;">Cette commande a été annulée. Si vous avez déjà payé, contactez-nous pour obtenir de l’aide.</div>
    @else
        <div class="form-panel order-tracking-panel" style="margin-bottom:24px;">
            <div class="order-tracking-head">
                <h3 style="margin:0;">Suivi de la commande</h3>
                @if ($order->delivery?->expected_delivery_at)
                    <span class="pill">Arrivée estimée le {{ $order->delivery->expected_delivery_at->format('d/m/Y') }}</span>
                @else
                    <span class="pill muted">Date estimée à confirmer</span>
                @endif
            </div>
            @if (in_array($deliveryStatus, ['RETARDEE', 'ECHEC'], true))
                <p class="order-tracking-alert">{{ $deliveryStatus === 'RETARDEE' ? 'La livraison prend du retard. Nous vous tiendrons informé.' : 'Un problème est survenu pendant la livraison. Contactez-nous pour obtenir de l’aide.' }}</p>
            @endif
            <div class="order-tracking-steps">
                @foreach ($steps as $index => $label)
                    <div class="order-tracking-step {{ $index <= $currentIndex ? 'is-complete' : '' }} {{ $index === $currentIndex ? 'is-current' : '' }}">
                        <span class="order-tracking-number">{{ $index + 1 }}</span>
                        <span class="order-tracking-label">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
            @if ($order->delivery?->tracking_number)
                <p class="order-tracking-detail">N° de suivi : <strong>{{ $order->delivery->tracking_number }}</strong> @if ($order->delivery->carrier) · {{ $order->delivery->carrier }} @endif</p>
            @endif
            @if ($order->delivery?->tracking_comment)
                <p class="order-tracking-detail">{{ $order->delivery->tracking_comment }}</p>
            @endif
        </div>
    @endif

    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Sous-total</th>
                    @if ($order->status === 'LIVREE')
                        <th>Avis</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
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
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 2, ',', ' ') }} FCFA</td>
                        <td>{{ number_format($item->subtotal, 2, ',', ' ') }} FCFA</td>
                        @if ($order->status === 'LIVREE' && $item->product)
                            <td>
                                @php
                                    $alreadyReviewed = \App\Models\Review::where('client_id', $order->client_id)
                                        ->where('product_id', $item->product_id)
                                        ->exists();
                                @endphp
                                @if ($alreadyReviewed)
                                    <span class="pill muted">Avis envoyé</span>
                                @else
                                    <button type="button" class="button small" onclick="document.getElementById('review-form-{{ $item->product_id }}').classList.toggle('open')">Laisser un avis</button>
                                @endif
                            </td>
                        @endif
                    </tr>
                    @if ($order->status === 'LIVREE' && $item->product && ! ($alreadyReviewed ?? false))
                        <tr id="review-form-{{ $item->product_id }}" class="review-row">
                            <td colspan="5" style="background:var(--paper);">
                                <form method="POST" action="{{ route('reviews.store', [$order, $item->product]) }}" style="display:flex; gap:12px; align-items:flex-start; padding:10px 0;">
                                    @csrf
                                    <select name="rating" required style="padding:8px; border:1px solid var(--line); border-radius:8px;">
                                        <option value="">Note</option>
                                        @for ($i = 5; $i >= 1; $i--)
                                            <option value="{{ $i }}">{{ str_repeat('⭐', $i) }}</option>
                                        @endfor
                                    </select>
                                    <input type="text" name="comment" placeholder="Votre commentaire (optionnel)" style="flex:1; padding:8px 12px; border:1px solid var(--line); border-radius:8px;">
                                    <button type="submit" class="button small primary">Envoyer</button>
                                </form>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <style>
        .review-row { display: none; }
        .review-row.open { display: table-row; }
        .order-payment-card { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 24px; padding: 23px 26px; border: 1px solid rgba(177,55,47,.2); border-radius: 16px; background: linear-gradient(125deg, #fff 0%, #fff8f3 100%); box-shadow: 0 10px 28px rgba(34,31,29,.05); }
        .order-payment-card h3 { margin: 3px 0 5px; font-family: Georgia, serif; font-size: 1.25rem; font-weight: 500; }
        .order-payment-card p { max-width: 620px; margin: 0; color: var(--muted); font-size: .9rem; line-height: 1.55; }
        .order-payment-eyebrow { color: var(--accent); font-size: .72rem; font-weight: 800; letter-spacing: .09em; text-transform: uppercase; }
        .order-payment-action { display: flex; align-items: center; gap: 18px; }
        .order-payment-action > strong { color: var(--accent); font-size: 1.15rem; white-space: nowrap; }
        .order-payment-action .button { min-height: 44px; white-space: nowrap; }
        .order-payment-pending { border-color: #e9d8af; background: linear-gradient(125deg, #fff 0%, #fffaf0 100%); }
        .order-payment-pending .order-payment-eyebrow { color: #8a641d; }
        .order-payment-confirmed { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        .order-tracking-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 18px; flex-wrap: wrap; }
        .order-tracking-steps { display: grid; grid-template-columns: repeat(5, minmax(90px, 1fr)); gap: 8px; overflow-x: auto; padding: 4px 0 10px; }
        .order-tracking-step { display: grid; justify-items: center; gap: 7px; text-align: center; color: var(--muted); }
        .order-tracking-number { display: grid; place-items: center; width: 32px; height: 32px; border-radius: 50%; background: #d8d2c6; color: #fff; font-size: .8rem; font-weight: 800; }
        .order-tracking-step.is-complete .order-tracking-number { background: var(--accent); }
        .order-tracking-step.is-current .order-tracking-label { color: var(--ink); font-weight: 800; }
        .order-tracking-label { font-size: .78rem; }
        .order-tracking-alert { margin: 0 0 14px; color: var(--danger); font-size: .9rem; font-weight: 700; }
        .order-tracking-detail { margin: 10px 0 0; color: var(--muted); font-size: .88rem; }
        @media (max-width: 700px) {
            .order-payment-card { align-items: stretch; flex-direction: column; padding: 19px; }
            .order-payment-action { align-items: stretch; flex-direction: column; gap: 11px; padding-top: 14px; border-top: 1px solid var(--line); }
            .order-payment-action .button, .order-payment-pending > .button { width: 100%; }
            .order-tracking-panel { padding: 16px; }
            .order-tracking-steps { grid-template-columns: repeat(5, minmax(76px, 1fr)); }
        }
    </style>

    <div style="display:flex; justify-content:flex-end; margin-top:20px;">
        <div class="form-panel" style="width:320px;">
            <p style="display:flex; justify-content:space-between; margin:4px 0;"><span>Sous-total</span><span>{{ number_format($order->subtotal, 2, ',', ' ') }} FCFA</span></p>
            <p style="display:flex; justify-content:space-between; margin:4px 0;"><span>Livraison</span><span>{{ number_format($order->shipping_fee, 2, ',', ' ') }} FCFA</span></p>
            <p style="display:flex; justify-content:space-between; margin:10px 0 0; font-weight:800; border-top:1px solid var(--line); padding-top:10px;"><span>Total</span><span>{{ number_format($order->total, 2, ',', ' ') }} FCFA</span></p>
        </div>
    </div>
@endsection

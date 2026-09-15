@extends('layouts.client')

@section('title', 'Commande ' . $order->order_number)

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Commande {{ $order->order_number }}</h1>
            <p class="page-subtitle">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <div style="display:flex; gap:8px;">
            @if ($order->status === 'EN_ATTENTE')
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
    @if ($order->status === 'EN_ATTENTE' && (! $order->payment || $order->payment->status !== 'ACCEPTE'))
        <div class="form-panel" style="margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
            <div>
                <h3 style="margin:0 0 4px;">Payer cette commande en ligne</h3>
                <p style="margin:0; color:var(--muted); font-size:.88rem;">Orange Money, Moov Money ou carte bancaire — {{ number_format($order->total, 0, ',', ' ') }} FCFA</p>
                @if ($order->payment?->status === 'REFUSE')
                    <p style="margin:6px 0 0; color:var(--danger); font-size:.85rem; font-weight:700;">Le dernier paiement a échoué. Vous pouvez réessayer.</p>
                @endif
            </div>
            <form method="POST" action="{{ route('payments.cinetpay.initiate', $order) }}">
                @csrf
                <button type="submit" class="button primary">Payer maintenant</button>
            </form>
        </div>
    @elseif ($order->payment?->status === 'ACCEPTE')
        <div class="alert alert-success" style="margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <span>✅ Paiement reçu — {{ $order->payment->paid_at?->format('d/m/Y à H:i') }}</span>
            <a class="button small" href="{{ route('receipts.download', $order) }}">📄 Télécharger le reçu PDF</a>
        </div>
    @endif

    <!-- Timeline de livraison -->
    @php
        $steps = ['EN_PREPARATION' => 'Préparation', 'EXPEDIEE' => 'Expédiée', 'EN_TRANSIT' => 'En transit', 'LIVREE' => 'Livrée'];
        $currentStep = $order->delivery->status ?? 'EN_PREPARATION';
        $stepKeys = array_keys($steps);
        $currentIndex = array_search($currentStep, $stepKeys) ?: 0;
    @endphp
    <div class="form-panel" style="margin-bottom:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h3 style="margin:0;">Suivi de livraison</h3>
            @if ($order->delivery?->expected_delivery_at)
                <span class="pill">Livraison estimée le {{ $order->delivery->expected_delivery_at->format('d/m/Y') }}</span>
            @endif
        </div>
        <div style="display:flex; align-items:center;">
            @foreach ($steps as $key => $label)
                <div style="flex:1; text-align:center; position:relative;">
                    <div style="width:28px; height:28px; border-radius:50%; margin:0 auto 6px; display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:800; color:#fff; background: {{ $loop->index <= $currentIndex ? 'var(--accent)' : '#d8d2c6' }};">
                        {{ $loop->index + 1 }}
                    </div>
                    <span style="font-size:.78rem; color: {{ $loop->index <= $currentIndex ? 'var(--ink)' : 'var(--muted)' }}; font-weight:{{ $loop->index === $currentIndex ? '800' : '600' }};">{{ $label }}</span>
                    @if (! $loop->last)
                        <div style="position:absolute; top:14px; left:50%; width:100%; height:2px; background: {{ $loop->index < $currentIndex ? 'var(--accent)' : '#d8d2c6' }}; z-index:-1;"></div>
                    @endif
                </div>
            @endforeach
        </div>
        @if ($order->delivery?->tracking_number)
            <p style="margin:16px 0 0; font-size:.88rem; color:var(--muted);">N° de suivi : <strong>{{ $order->delivery->tracking_number }}</strong> @if($order->delivery->carrier) via {{ $order->delivery->carrier }} @endif</p>
        @endif
        @if ($order->delivery?->tracking_comment)
            <p style="margin:6px 0 0; font-size:.85rem; color:var(--muted);">{{ $order->delivery->tracking_comment }}</p>
        @endif
    </div>

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
                        <td>{{ number_format($item->unit_price, 2, ',', ' ') }} €</td>
                        <td>{{ number_format($item->subtotal, 2, ',', ' ') }} €</td>
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

    <style>.review-row { display: none; } .review-row.open { display: table-row; }</style>

    <div style="display:flex; justify-content:flex-end; margin-top:20px;">
        <div class="form-panel" style="width:320px;">
            <p style="display:flex; justify-content:space-between; margin:4px 0;"><span>Sous-total</span><span>{{ number_format($order->subtotal, 2, ',', ' ') }} €</span></p>
            <p style="display:flex; justify-content:space-between; margin:4px 0;"><span>Livraison</span><span>{{ number_format($order->shipping_fee, 2, ',', ' ') }} €</span></p>
            <p style="display:flex; justify-content:space-between; margin:10px 0 0; font-weight:800; border-top:1px solid var(--line); padding-top:10px;"><span>Total</span><span>{{ number_format($order->total, 2, ',', ' ') }} €</span></p>
        </div>
    </div>
@endsection

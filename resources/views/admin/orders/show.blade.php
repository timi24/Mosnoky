@extends('layouts.admin')

@section('title', 'Commande ' . $order->order_number)

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Commande {{ $order->order_number }}</h1>
            <p class="page-subtitle">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <a class="button" href="{{ route('admin.orders.index') }}">← Retour aux commandes</a>
    </div>

    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:20px; align-items:start;">
        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>
                                {{ $item->product->name ?? 'Produit supprimé' }}
                                @if ($item->size || $item->color)
                                    <div style="font-size:.78rem; color:var(--muted); margin-top:2px;">
                                        @if ($item->size) Taille : {{ $item->size }} @endif
                                        @if ($item->size && $item->color) · @endif
                                        @if ($item->color) Couleur : {{ $item->color }} @endif
                                    </div>
                                @endif
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="display:flex; flex-direction:column; gap:16px;">
            <div class="form-panel" style="max-width:none;">
                <h3 style="margin-top:0;">Statut de la commande</h3>
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                    @csrf
                    @method('PATCH')
                    <div class="field">
                        <select name="status">
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="button primary">Mettre à jour</button>
                </form>
            </div>

            <div class="form-panel" style="max-width:none;">
                <h3 style="margin-top:0;">Client</h3>
                <p style="margin:4px 0;">{{ $order->client->user->first_name ?? '' }} {{ $order->client->user->last_name ?? '' }}</p>
                <p style="margin:4px 0; color:var(--muted);">{{ $order->client->user->email ?? '' }}</p>
                @if (Route::has('admin.clients.show') && $order->client)
                    <a class="button small" style="margin-top:10px;" href="{{ route('admin.clients.show', $order->client) }}">Voir la fiche client</a>
                @endif
            </div>

            <div class="form-panel" style="max-width:none;">
                <h3 style="margin-top:0;">Récapitulatif</h3>
                <p style="margin:4px 0; display:flex; justify-content:space-between;"><span>Sous-total</span> <span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span></p>
                <p style="margin:4px 0; display:flex; justify-content:space-between;"><span>Livraison</span> <span>{{ number_format($order->shipping_fee, 0, ',', ' ') }} FCFA</span></p>
                <p style="margin:8px 0 0; display:flex; justify-content:space-between; font-weight:800; border-top:1px solid var(--line); padding-top:8px;"><span>Total</span> <span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span></p>
            </div>
        </div>
    </div>
@endsection

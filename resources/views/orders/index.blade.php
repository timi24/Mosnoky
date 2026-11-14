@extends('layouts.client')

@section('title', 'Mes commandes')

@section('content')
    @php
        $statusLabels = [
            'EN_ATTENTE' => 'En attente de confirmation',
            'CONFIRMEE' => 'Confirmée',
            'EN_PREPARATION' => 'En préparation',
            'EXPEDIEE' => 'Expédiée',
            'LIVREE' => 'Livrée',
            'ANNULEE' => 'Annulée',
        ];
    @endphp
    <div class="page-head">
        <div>
            <h1 class="page-title">Mes commandes</h1>
        </div>
    </div>

    @if ($orders->isEmpty())
        <div class="empty">Vous n'avez pas encore passé de commande. <a href="{{ route('categories.index') }}" style="color:var(--accent); font-weight:700;">Découvrir les produits</a></div>
    @else
        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>N° commande</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                            <td><span class="pill {{ $order->status === 'LIVREE' ? '' : 'muted' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span></td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="row-actions">
                                    <a class="button small" href="{{ route('orders.show', $order) }}">Voir le détail</a>
                                    @if ($order->status === 'EN_ATTENTE' && ! $order->payment?->submitted_at && $order->payment?->status !== 'ACCEPTE')
                                        <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Voulez-vous vraiment annuler cette commande ?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="button small danger">Annuler</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $orders->links() }}
        </div>
    @endif
@endsection

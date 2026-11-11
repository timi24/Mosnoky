@extends('layouts.admin')

@section('title', 'Fiche client')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">{{ $client->user?->displayName() ?? 'Compte indisponible' }}</h1>
            <p class="page-subtitle">
                {{ $client->user?->email ?? 'Aucune adresse e-mail disponible' }}
                · Compte créé le {{ ($client->user?->created_at ?? $client->created_at)->format('d/m/Y') }}
                @if ($client->user?->phone)
                    · {{ $client->user->phone }}
                @endif
            </p>
        </div>
        <a class="button" href="{{ route('admin.clients.index') }}">← Retour aux clients</a>
    </div>

    <h3 style="margin-bottom:12px;">Historique des commandes</h3>
    @php($orderStatusLabels = [
        'EN_ATTENTE' => 'En attente de confirmation',
        'CONFIRMEE' => 'Confirmée',
        'EN_PREPARATION' => 'En préparation',
        'EXPEDIEE' => 'Expédiée',
        'LIVREE' => 'Livrée',
        'ANNULEE' => 'Annulée',
    ])

    @if ($orders->isEmpty())
        <div class="empty">Ce client n'a pas encore passé de commande.</div>
    @else
        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>N° commande</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                            <td>
                                <span class="pill muted">{{ $orderStatusLabels[$order->status] ?? 'Statut inconnu' }}</span>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a class="button small" href="{{ route('admin.orders.show', $order) }}">Voir</a>
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

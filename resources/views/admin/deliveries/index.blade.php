@extends('layouts.admin')

@section('title', 'Livraisons')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Livraisons</h1>
            <p class="page-subtitle">Suivez et mettez à jour les expéditions.</p>
        </div>
    </div>

    @if ($orders->isEmpty())
        <div class="empty">Aucune commande à livrer pour le moment.</div>
    @else
        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Commande</th>
                        <th>Client</th>
                        <th>Statut commande</th>
                        <th>Suivi</th>
                        <th>Livraison estimée</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->client->user->first_name ?? '—' }}</td>
                            <td><span class="pill muted">{{ $order->status }}</span></td>
                            <td>
                                @if ($order->delivery)
                                    <span class="pill">{{ $order->delivery->status }}</span>
                                @else
                                    <span class="pill muted">Non renseigné</span>
                                @endif
                            </td>
                            <td>{{ $order->delivery?->expected_delivery_at?->format('d/m/Y') ?? '—' }}</td>
                            <td>
                                <a class="button small" href="{{ route('admin.deliveries.edit', $order) }}">Gérer</a>
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

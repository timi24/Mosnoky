@extends('layouts.admin')

@section('title', 'Fiche client')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">{{ $client->user->first_name ?? '' }} {{ $client->user->last_name ?? '' }}</h1>
            <p class="page-subtitle">{{ $client->user->email ?? '' }} · Client depuis le {{ $client->created_at->format('d/m/Y') }}</p>
        </div>
        <a class="button" href="{{ route('admin.clients.index') }}">← Retour aux clients</a>
    </div>

    <h3 style="margin-bottom:12px;">Historique des commandes</h3>

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
                            <td><span class="pill muted">{{ $order->status }}</span></td>
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

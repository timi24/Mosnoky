@extends('layouts.client')

@section('title', 'Mes commandes')

@section('content')
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
                                <a class="button small" href="{{ route('orders.show', $order) }}">Voir le détail</a>
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

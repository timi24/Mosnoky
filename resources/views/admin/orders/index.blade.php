@extends('layouts.admin')

@section('title', 'Commandes')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Commandes</h1>
            <p class="page-subtitle">Suivez et mettez à jour le statut des commandes.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.orders.index') }}" style="margin-bottom: 18px; display:flex; gap:10px; align-items:center;">
        <select name="status" onchange="this.form.submit()" style="padding:9px 12px; border:1px solid var(--line); border-radius:8px;">
            <option value="">Tous les statuts</option>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" {{ $currentStatus === $status ? 'selected' : '' }}>{{ $status }}</option>
            @endforeach
        </select>
        @if ($currentStatus)
            <a class="button small" href="{{ route('admin.orders.index') }}">Réinitialiser</a>
        @endif
    </form>

    @if ($orders->isEmpty())
        <div class="empty">Aucune commande pour le moment.</div>
    @else
        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>N° commande</th>
                        <th>Client</th>
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
                            <td>{{ $order->client->user->first_name ?? '—' }} {{ $order->client->user->last_name ?? '' }}</td>
                            <td>{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                            <td>
                                <span class="pill {{ in_array($order->status, ['LIVREE', 'CONFIRMEE']) ? '' : 'muted' }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="row-actions">
                                    <a class="button small" href="{{ route('admin.orders.show', $order) }}">Voir le détail</a>
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

@extends('layouts.client')

@section('title', 'Mes paiements')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Historique de paiement</h1>
        </div>
    </div>

    @if ($payments->isEmpty())
        <div class="empty">Aucun paiement pour le moment.</div>
    @else
        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Commande</th>
                        <th>Référence</th>
                        <th>Montant</th>
                        <th>Moyen</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>{{ $payment->order->order_number ?? '—' }}</td>
                            <td style="font-size:.8rem; color:var(--muted);">{{ $payment->reference }}</td>
                            <td>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                            <td>{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td>
                                @if ($payment->status === 'ACCEPTE')
                                    <span class="pill">Payé</span>
                                @elseif ($payment->status === 'REFUSE')
                                    <span class="pill" style="background:#fbe9e6; color:#b1372f;">Échoué</span>
                                @else
                                    <span class="pill muted">En attente</span>
                                @endif
                            </td>
                            <td>{{ $payment->paid_at?->format('d/m/Y H:i') ?? $payment->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($payment->status === 'ACCEPTE')
                                    <a class="button small" href="{{ route('receipts.download', $payment->order) }}">Reçu PDF</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $payments->links() }}
        </div>
    @endif
@endsection

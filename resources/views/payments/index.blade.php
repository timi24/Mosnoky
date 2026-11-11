@extends('layouts.client')

@section('title', 'Mes paiements')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Historique de paiement</h1>
            <p class="page-subtitle">Retrouvez vos déclarations, leur statut et les reçus de vos commandes.</p>
        </div>
    </div>

    @if ($payments->isEmpty())
        <div class="empty">Aucun paiement pour le moment.</div>
    @else
        <div class="panel payment-history-panel">
            <table class="payment-history-table">
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
                            <td data-label="Commande">{{ $payment->order->order_number ?? '—' }}</td>
                            <td data-label="Référence" class="payment-reference">{{ $payment->customer_reference ?? $payment->reference ?? '—' }}</td>
                            <td data-label="Montant" class="payment-amount">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                            <td data-label="Moyen">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td data-label="Statut">
                                @if ($payment->status === 'ACCEPTE')
                                    <span class="pill">Payé</span>
                                @elseif ($payment->status === 'REFUSE')
                                    <span class="pill" style="background:#fbe9e6; color:#b1372f;">Non retrouvé</span>
                                @elseif ($payment->submitted_at)
                                    <span class="pill muted">En vérification</span>
                                @else
                                    <span class="pill muted">En attente</span>
                                @endif
                            </td>
                            <td data-label="Date">{{ $payment->paid_at?->format('d/m/Y H:i') ?? $payment->created_at->format('d/m/Y H:i') }}</td>
                            <td data-label="" class="payment-action">
                                @if ($payment->status === 'ACCEPTE')
                                    <a class="button small" href="{{ route('receipts.download', $payment->order) }}">Reçu PDF</a>
                                @elseif ($payment->order && $payment->order->status === 'EN_ATTENTE' && ($payment->status === 'REFUSE' || ! $payment->submitted_at))
                                    <a class="button primary small" href="{{ route('payments.manual.show', $payment->order) }}">Payer</a>
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

    <style>
        .payment-history-panel { overflow: hidden; border-radius: 16px; box-shadow: 0 12px 32px rgba(34, 31, 29, .05); }
        .payment-history-table { width: 100%; }
        .payment-history-table thead th { padding-top: 15px; padding-bottom: 15px; }
        .payment-history-table tbody tr { transition: background .18s ease; }
        .payment-history-table tbody tr:hover { background: #fffaf5; }
        .payment-history-table td { vertical-align: middle; }
        .payment-reference { color: var(--muted); font-size: .82rem; overflow-wrap: anywhere; }
        .payment-amount { font-weight: 800; white-space: nowrap; }
        .payment-action { text-align: right; white-space: nowrap; }
        .payment-action .button { min-height: 38px; }
        @media (max-width: 760px) {
            .payment-history-panel { overflow: visible; border: 0; background: transparent; box-shadow: none; }
            .payment-history-table, .payment-history-table tbody { display: block; width: 100%; overflow: visible; }
            .payment-history-table thead { display: none; }
            .payment-history-table tbody tr { display: grid; grid-template-columns: 1fr; gap: 0; margin-bottom: 14px; padding: 8px 16px; border: 1px solid var(--line); border-radius: 15px; background: var(--card); box-shadow: 0 8px 22px rgba(34, 31, 29, .045); }
            .payment-history-table tbody tr:hover { background: var(--card); }
            .payment-history-table tbody td { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 11px 0; border-bottom: 1px solid var(--line); text-align: right; font-size: .88rem; }
            .payment-history-table tbody td::before { content: attr(data-label); flex: 0 0 auto; color: var(--muted); font-size: .76rem; font-weight: 700; text-align: left; }
            .payment-history-table tbody td.payment-reference { max-width: none; }
            .payment-history-table tbody td.payment-action { justify-content: flex-end; border-bottom: 0; padding-top: 13px; }
            .payment-history-table tbody td.payment-action::before { display: none; }
        }
        @media (prefers-reduced-motion: reduce) { .payment-history-table tbody tr { transition: none; } }
    </style>
@endsection

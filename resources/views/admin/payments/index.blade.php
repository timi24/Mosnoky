@extends('layouts.admin')

@section('title', 'Paiements')

@section('content')
    @php($paymentMethodLabels = ['ORANGE_MONEY' => 'Orange Money', 'MOOV_MONEY' => 'Moov Money', 'CINETPAY' => 'CinetPay', 'CARTE_BANCAIRE' => 'Carte bancaire'])

    <div class="page-head">
        <div>
            <h1 class="page-title">Paiements à vérifier</h1>
            <p class="page-subtitle">Comparez chaque ligne avec le SMS reçu sur votre téléphone (montant + référence), puis confirmez ou rejetez.</p>
        </div>
    </div>

    @if ($pending->isEmpty())
        <div class="empty">Aucun paiement en attente de vérification.</div>
    @else
        <div class="panel admin-payment-panel">
            <table class="admin-payment-table">
                <thead>
                    <tr>
                        <th>Commande</th>
                        <th>Client</th>
                        <th>Montant attendu</th>
                        <th>Moyen</th>
                        <th>Payé depuis</th>
                        <th>Référence</th>
                        <th>Déclaré le</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pending as $payment)
                        <tr>
                            <td data-label="Commande">{{ $payment->order->order_number ?? '—' }}</td>
                            <td data-label="Client">{{ $payment->order->client->user?->displayName() ?? 'Compte indisponible' }}</td>
                            <td data-label="Montant attendu" class="admin-payment-amount"><strong>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</strong></td>
                            <td data-label="Moyen">{{ $paymentMethodLabels[$payment->payment_method] ?? str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td data-label="Payé depuis">{{ $payment->customer_phone }}</td>
                            <td data-label="Référence"><strong class="admin-payment-reference">{{ $payment->customer_reference }}</strong></td>
                            <td data-label="Déclaré le">{{ $payment->submitted_at->format('d/m/Y H:i') }}</td>
                            <td data-label="Actions" class="admin-payment-actions">
                                <div class="row-actions">
                                    <form method="POST" action="{{ route('admin.payments.confirm', $payment) }}" onsubmit="return confirm('Confirmer ce paiement ? Le montant et la référence correspondent bien au SMS reçu ?');">
                                        @csrf
                                        <button type="submit" class="button primary small">Confirmer</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" onsubmit="return confirm('Rejeter ce paiement ? Le client sera prévenu.');">
                                        @csrf
                                        <button type="submit" class="button danger small">Rejeter</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <h2 class="admin-payment-history-title">Derniers paiements traités</h2>

    @if ($recent->isEmpty())
        <div class="empty">Rien pour le moment.</div>
    @else
        <div class="panel admin-payment-panel">
            <table class="admin-payment-table admin-payment-recent-table">
                <thead>
                    <tr>
                        <th>Commande</th>
                        <th>Montant</th>
                        <th>Moyen</th>
                        <th>Référence</th>
                        <th>Résultat</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recent as $payment)
                        <tr>
                            <td data-label="Commande">{{ $payment->order->order_number ?? '—' }}</td>
                            <td data-label="Montant" class="admin-payment-amount">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                            <td data-label="Moyen">{{ $paymentMethodLabels[$payment->payment_method] ?? str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td data-label="Référence"><span class="admin-payment-reference">{{ $payment->customer_reference }}</span></td>
                            <td data-label="Résultat">
                                @if ($payment->status === 'ACCEPTE')
                                    <span class="pill">Confirmé</span>
                                @else
                                    <span class="pill" style="background:#fbe9e6; color:#b1372f;">Rejeté</span>
                                @endif
                            </td>
                            <td data-label="Date">{{ $payment->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <style>
        .admin-payment-history-title { margin: 36px 0 14px; font-family: Georgia, serif; font-size: 1.45rem; font-weight: 400; }
        .admin-payment-panel { border-radius: 16px; box-shadow: 0 10px 28px rgba(34,31,29,.045); }
        .admin-payment-table { width: 100%; }
        .admin-payment-table td { vertical-align: middle; }
        .admin-payment-amount strong { color: var(--accent); white-space: nowrap; }
        .admin-payment-reference { display: inline-block; max-width: 220px; overflow-wrap: anywhere; font-size: .82rem; }
        .admin-payment-actions .row-actions { justify-content: flex-start; flex-wrap: wrap; }
        .admin-payment-actions form { margin: 0; }
        @media (max-width: 760px) {
            .admin-payment-panel { overflow: visible; border: 0; background: transparent; box-shadow: none; }
            .admin-payment-table, .admin-payment-table tbody { display: block; width: 100%; overflow: visible; }
            .admin-payment-table thead { display: none; }
            .admin-payment-table tbody tr { display: grid; grid-template-columns: 1fr; gap: 0; margin-bottom: 14px; padding: 7px 15px; border: 1px solid var(--line); border-radius: 15px; background: var(--card); box-shadow: 0 8px 22px rgba(34,31,29,.045); }
            .admin-payment-table tbody td { display: flex; justify-content: space-between; align-items: center; gap: 14px; min-width: 0; padding: 10px 0; border-bottom: 1px solid var(--line); text-align: right; overflow-wrap: anywhere; }
            .admin-payment-table tbody td::before { content: attr(data-label); flex: 0 0 auto; color: var(--muted); font-size: .75rem; font-weight: 700; text-align: left; }
            .admin-payment-reference { max-width: 60%; }
            .admin-payment-actions { border-bottom: 0 !important; padding-top: 13px !important; }
            .admin-payment-actions::before { display: none; }
            .admin-payment-actions .row-actions { width: 100%; justify-content: stretch; }
            .admin-payment-actions form { flex: 1; }
            .admin-payment-actions .button { width: 100%; min-height: 40px; }
        }
    </style>
@endsection

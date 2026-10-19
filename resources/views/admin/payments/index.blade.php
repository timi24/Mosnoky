@extends('layouts.admin')

@section('title', 'Paiements')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Paiements à vérifier</h1>
            <p class="page-subtitle">Comparez chaque ligne avec le SMS reçu sur votre téléphone (montant + référence), puis confirmez ou rejetez.</p>
        </div>
    </div>

    @if ($pending->isEmpty())
        <div class="empty">Aucun paiement en attente de vérification.</div>
    @else
        <div class="panel">
            <table>
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
                            <td>{{ $payment->order->order_number ?? '—' }}</td>
                            <td>{{ $payment->order->client->user->name ?? '—' }}</td>
                            <td><strong>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</strong></td>
                            <td>{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td>{{ $payment->customer_phone }}</td>
                            <td><strong>{{ $payment->customer_reference }}</strong></td>
                            <td>{{ $payment->submitted_at->format('d/m/Y H:i') }}</td>
                            <td>
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

    <h2 style="font-family:Georgia,serif; font-weight:400; font-size:1.4rem; margin:36px 0 14px;">Derniers paiements traités</h2>

    @if ($recent->isEmpty())
        <div class="empty">Rien pour le moment.</div>
    @else
        <div class="panel">
            <table>
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
                            <td>{{ $payment->order->order_number ?? '—' }}</td>
                            <td>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                            <td>{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td>{{ $payment->customer_reference }}</td>
                            <td>
                                @if ($payment->status === 'ACCEPTE')
                                    <span class="pill">Confirmé</span>
                                @else
                                    <span class="pill" style="background:#fbe9e6; color:#b1372f;">Rejeté</span>
                                @endif
                            </td>
                            <td>{{ $payment->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection

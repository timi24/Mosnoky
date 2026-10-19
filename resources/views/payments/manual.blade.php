@extends('layouts.client')

@section('title', 'Payer ma commande')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Payer ma commande</h1>
            <p class="page-subtitle">Commande {{ $order->order_number }} · Paiement par Orange Money ou Moov Money</p>
        </div>
        <a class="button small" href="{{ route('orders.show', $order) }}">← Retour à la commande</a>
    </div>

    <div class="pay-total">
        <span>Montant à envoyer</span>
        <strong>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</strong>
    </div>

    @if ($payment->isAwaitingVerification())
        <div class="panel pay-box">
            <h3 style="margin:0 0 8px;">Paiement en cours de vérification</h3>
            <p class="pay-muted" style="margin:0;">
                Nous avons bien reçu votre déclaration (référence <strong>{{ $payment->customer_reference }}</strong>).
                Notre équipe vérifie le paiement et vous prévient dès qu'il est confirmé.
            </p>
        </div>
    @else
        <div class="panel pay-box">
            <h3 style="margin:0 0 14px;">1. Envoyez l'argent</h3>
            <p class="pay-muted" style="margin:0 0 14px;">
                Depuis votre téléphone, envoyez <strong>exactement {{ number_format($payment->amount, 0, ',', ' ') }} FCFA</strong>
                à l'un de ces numéros, au nom de <strong>{{ $accountName }}</strong> :
            </p>
            <div class="pay-numbers">
                <div class="pay-number">
                    <span class="pill">Orange Money</span>
                    @if ($orangeNumber)
                        <strong>{{ $orangeNumber }}</strong>
                    @else
                        <em class="pay-muted">Numéro bientôt disponible</em>
                    @endif
                </div>
                <div class="pay-number">
                    <span class="pill muted">Moov Money</span>
                    @if ($moovNumber)
                        <strong>{{ $moovNumber }}</strong>
                    @else
                        <em class="pay-muted">Numéro bientôt disponible</em>
                    @endif
                </div>
            </div>
            <p class="pay-muted" style="margin:14px 0 0; font-size:.85rem;">
                Vérifiez bien le nom affiché avant de valider le transfert. Gardez le SMS de confirmation : il contient la référence de la transaction.
            </p>
        </div>

        <div class="panel pay-box">
            <h3 style="margin:0 0 14px;">2. Déclarez votre paiement</h3>

            <form method="POST" action="{{ route('payments.manual.submit', $order) }}">
                @csrf

                <div class="field">
                    <label>Vous avez payé avec</label>
                    <div class="pay-choices">
                        <label class="pay-choice">
                            <input type="radio" name="payment_method" value="ORANGE_MONEY" @checked(old('payment_method', 'ORANGE_MONEY') === 'ORANGE_MONEY')>
                            <span>Orange Money</span>
                        </label>
                        <label class="pay-choice">
                            <input type="radio" name="payment_method" value="MOOV_MONEY" @checked(old('payment_method') === 'MOOV_MONEY')>
                            <span>Moov Money</span>
                        </label>
                    </div>
                    @error('payment_method') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="customer_phone">Numéro depuis lequel vous avez payé</label>
                    <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="Ex. 70 12 34 56" inputmode="tel" autocomplete="tel" required>
                    @error('customer_phone') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="customer_reference">Référence de la transaction (reçue par SMS)</label>
                    <input type="text" id="customer_reference" name="customer_reference" value="{{ old('customer_reference') }}" placeholder="Ex. MP240101.1234.A12345" autocomplete="off" required>
                    @error('customer_reference') <div class="error">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="button primary" @disabled(! $orangeNumber && ! $moovNumber)>J'ai payé, envoyer pour vérification</button>
            </form>
        </div>
    @endif

    <style>
        .pay-total { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 22px; margin-bottom: 18px; border: 1px solid var(--line); border-radius: 12px; background: var(--card); flex-wrap: wrap; }
        .pay-total span { color: var(--muted); font-size: .9rem; }
        .pay-total strong { font-size: 1.5rem; color: var(--accent); }
        .pay-box { padding: 22px; margin-bottom: 18px; }
        .pay-muted { color: var(--muted); font-size: .92rem; line-height: 1.6; }
        .pay-numbers { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .pay-number { display: flex; flex-direction: column; align-items: flex-start; gap: 8px; padding: 16px; border: 1px dashed #d9cfc0; border-radius: 10px; }
        .pay-number strong { font-size: 1.3rem; letter-spacing: .04em; }
        .pay-choices { display: flex; gap: 10px; flex-wrap: wrap; }
        .pay-choice { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1px solid var(--line); border-radius: 999px; cursor: pointer; font-weight: 700; font-size: .9rem; }
        .pay-choice:has(input:checked) { border-color: var(--accent); color: var(--accent); }
        .field input[type=text] { max-width: 420px; }
        @media (max-width: 600px) { .pay-numbers { grid-template-columns: 1fr; } }
    </style>
@endsection

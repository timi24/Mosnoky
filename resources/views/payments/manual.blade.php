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
                à l'un de ces numéros en tapant le code, au nom de <strong>{{ $accountName }}</strong> :
            </p>
            <div class="pay-numbers">
                <div class="pay-number">
                    <span class="pill">Orange Money</span>
                    @if ($orangeNumber)
                       <div>*144*2*1*<strong>{{ $orangeNumber }}</strong>*montant#</div>
                    @else
                        <em class="pay-muted">Numéro bientôt disponible</em>
                    @endif
                </div>
                <div class="pay-number">
                    <span class="pill muted">Moov Money</span>
                    @if ($moovNumber)
                        <div>*144*2*1*<strong>{{ $moovNumber }}</strong>*montant#</div>
                    @else
                        <em class="pay-muted">Numéro bientôt disponible</em>
                    @endif
                </div>
            </div>
            <p class="pay-muted" style="margin:14px 0 0; font-size:.85rem;">
                Vérifiez bien le nom affiché avant de valider le transfert. Gardez le SMS de confirmation : il contient la référence de la transaction.
            </p>
        </div>

        @php
            $orangeShot = file_exists(public_path('images/paiement/orange-sms.jpg')) ? asset('images/paiement/orange-sms.jpg') : null;
            $moovShot = file_exists(public_path('images/paiement/moov-sms.jpg')) ? asset('images/paiement/moov-sms.jpg') : null;
        @endphp
        <div class="panel pay-box">
            <h3 style="margin:0 0 6px;">Exemple : où trouver ma référence ?</h3>
            <p class="pay-muted" style="margin:0 0 14px; font-size:.85rem;">
                Après votre transfert, vous recevez un SMS de confirmation. Copiez-y la référence (« ID Trans » chez Orange, « TID » chez Moov) et reportez-la plus bas, exactement comme écrite.
            </p>
            <div class="pay-examples">
                <div class="pay-example">
                    <span class="pill">Orange Money</span>
                    @if ($orangeShot)
                        <img src="{{ $orangeShot }}" alt="Exemple de SMS Orange Money" class="pay-shot">
                    @else
                        <div class="sms-mock">
                            @php $orangeDigits = $orangeNumber ? substr(preg_replace('/\D/', '', $orangeNumber), -8) : '••••••••'; @endphp
                            <div class="sms-from">OrangeMoney</div>
                            <p>Cher client, vous avez transfere <b>{{ number_format($payment->amount, 2, '.', '') }} FCFA</b>, Frais: 0.0 FCFA, Taxe: FCFA au numero <b>{{ $orangeDigits }},{{ strtoupper($accountName) }}</b>. Votre solde est de ••• FCFA. ID Trans: <mark>PP261018.2238.45373385</mark>. Pour toute reclamation contactez par appel le 127 ou whatsapp 07000121. Orange Money BF</p>
                        </div>
                    @endif
                </div>
                <div class="pay-example">
                    <span class="pill muted">Moov Money</span>
                    @if ($moovShot)
                        <img src="{{ $moovShot }}" alt="Exemple de SMS Moov Money" class="pay-shot">
                    @else
                        @php $moovDigits = $moovNumber ? '226'.substr(preg_replace('/\D/', '', $moovNumber), -8) : '226••••••••'; @endphp
                        <div class="sms-mock">
                            <div class="sms-from">MOOV MONEY</div>
                            <p>Transfert National reussi pour {{ $accountName }}.<br>
                            Numéro de mobile: <b>{{ $moovDigits }}</b><br>
                            Montant: <b>{{ number_format($payment->amount, 2, ',', ' ') }} FCFA</b><br>
                            Frais: 0,00 FCFA<br>
                            Total: {{ number_format($payment->amount, 2, ',', ' ') }} FCFA<br>
                            Date: 24/09/2026 19:43:57<br>
                            TID: <mark>DIO7KPCUAP</mark><br>
                            Solde: ••• FCFA</p>
                        </div>
                    @endif
                </div>
            </div>
            @if (! $orangeShot || ! $moovShot)
                <p class="pay-muted" style="margin:12px 0 0; font-size:.78rem;">
                    La référence à copier est la partie surlignée. Le texte exact du SMS peut varier légèrement selon l'opérateur.
                </p>
            @endif
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
                    <label for="customer_reference">Référence de la transaction (« ID Trans » pour Orange, « TID » pour Moov)</label>
                    <input type="text" id="customer_reference" name="customer_reference" value="{{ old('customer_reference') }}" placeholder="Ex. PP261018.2238.45373385" autocomplete="off" required>
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
        .pay-examples { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .pay-example { display: flex; flex-direction: column; align-items: flex-start; gap: 10px; }
        .pay-shot { width: 100%; max-width: 260px; border: 1px solid var(--line); border-radius: 12px; }
        .sms-mock { width: 100%; max-width: 300px; padding: 12px 14px; border-radius: 14px 14px 14px 4px; background: var(--paper); border: 1px solid var(--line); font-size: .85rem; line-height: 1.5; }
        .sms-mock p { margin: 0 0 6px; }
        .sms-mock .sms-from { font-size: .72rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; }
        .sms-mock mark { background: #fff1b8; color: var(--ink); padding: 1px 5px; border-radius: 4px; font-weight: 800; }
        @media (max-width: 600px) { .pay-numbers, .pay-examples { grid-template-columns: 1fr; } }
    </style>
@endsection

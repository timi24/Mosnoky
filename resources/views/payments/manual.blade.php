@extends('layouts.client')

@section('title', 'Payer ma commande')

@section('content')
    <div class="page-head pay-page-head">
        <div>
            <h1 class="page-title">Payer ma commande</h1>
            <p class="page-subtitle">Commande {{ $order->order_number }} · Paiement par Orange Money ou Moov Money</p>
        </div>
        <a class="button small" href="{{ route('orders.show', $order) }}">← Retour à la commande</a>
    </div>

    <div class="pay-total">
        <span><small>Montant exact à envoyer</small>Ne payez pas un montant différent.</span>
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
                        <div>*555*2*1*<strong>{{ $moovNumber }}</strong>*montant#</div>
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
                            <input type="radio" name="payment_method" value="ORANGE_MONEY" @checked(old('payment_method') === 'ORANGE_MONEY') required>
                            <span>Orange Money</span>
                        </label>
                        <label class="pay-choice">
                            <input type="radio" name="payment_method" value="MOOV_MONEY" @checked(old('payment_method') === 'MOOV_MONEY')>
                            <span>Moov Money</span>
                        </label>
                    </div>
                    <p id="operator-detection" class="operator-detection" role="status" aria-live="polite">Saisissez votre numéro : le réseau sera estimé à partir de son préfixe.</p>
                    @error('payment_method') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="customer_phone">Numéro depuis lequel vous avez payé</label>
                    <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $order->shipping_address_snapshot['phone'] ?? auth()->user()->phone) }}" placeholder="Ex. 70 12 34 56" inputmode="tel" autocomplete="tel" aria-describedby="operator-detection" required>
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
        .pay-page-head { align-items: center; }
        .pay-total { position: relative; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 22px 26px; margin-bottom: 20px; border: 1px solid rgba(177,55,47,.2); border-radius: 18px; background: linear-gradient(125deg, #fff 0%, #fff8f3 100%); box-shadow: 0 12px 32px rgba(34,31,29,.06); flex-wrap: wrap; }
        .pay-total span { display: grid; gap: 4px; color: var(--muted); font-size: .88rem; }
        .pay-total span small { color: var(--ink); font-size: .76rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .pay-total strong { font-size: clamp(1.45rem, 4vw, 2rem); color: var(--accent); letter-spacing: -.03em; }
        .pay-box { padding: clamp(18px, 3vw, 26px); margin-bottom: 18px; border-radius: 16px; box-shadow: 0 8px 24px rgba(34,31,29,.04); }
        .pay-box h3 { font-family: Georgia, serif; font-size: 1.25rem; font-weight: 500; }
        .pay-muted { color: var(--muted); font-size: .92rem; line-height: 1.6; }
        .pay-numbers { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .pay-number { display: flex; flex-direction: column; align-items: flex-start; gap: 10px; min-width: 0; padding: 18px; border: 1px solid var(--line); border-radius: 14px; background: #fffdfa; }
        .pay-number > div { overflow-wrap: anywhere; color: var(--ink); font-size: .94rem; }
        .pay-number strong { font-size: 1.25rem; letter-spacing: .04em; color: var(--accent); }
        .pay-choices { display: flex; gap: 10px; flex-wrap: wrap; }
        .pay-choice { display: inline-flex; align-items: center; gap: 9px; padding: 12px 16px; border: 1px solid var(--line); border-radius: 12px; background: #fff; cursor: pointer; font-weight: 700; font-size: .9rem; transition: border-color .18s ease, background .18s ease, box-shadow .18s ease; }
        .pay-choice input { accent-color: var(--accent); }
        .pay-choice:has(input:checked) { border-color: var(--accent); color: var(--accent); background: #fff8f5; box-shadow: 0 0 0 3px rgba(177,55,47,.08); }
        .operator-detection { margin: 9px 0 0; color: var(--muted); font-size: .82rem; line-height: 1.45; }
        .operator-detection.is-detected { color: #286b4c; font-weight: 700; }
        .operator-detection.is-unknown { color: #8a641d; }
        .pay-box .field input[type=text], .pay-box .field input[type=tel] { width: 100%; max-width: 520px; min-height: 46px; border-radius: 10px; }
        .pay-box form > .button { min-height: 46px; padding-inline: 20px; }
        .pay-examples { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .pay-example { display: flex; flex-direction: column; align-items: flex-start; gap: 10px; }
        .pay-shot { display: block; width: 100%; max-width: 300px; border: 1px solid var(--line); border-radius: 14px; box-shadow: 0 8px 20px rgba(34,31,29,.08); }
        .sms-mock { width: 100%; max-width: 340px; padding: 14px 16px; border-radius: 14px 14px 14px 4px; background: #f5f3ef; border: 1px solid var(--line); font-size: .85rem; line-height: 1.55; overflow-wrap: anywhere; }
        .sms-mock p { margin: 0 0 6px; }
        .sms-mock .sms-from { font-size: .72rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; }
        .sms-mock mark { background: #fff1b8; color: var(--ink); padding: 1px 5px; border-radius: 4px; font-weight: 800; }
        @media (max-width: 600px) {
            .pay-page-head { align-items: flex-start; }
            .pay-page-head > a { width: 100%; }
            .pay-total { align-items: flex-start; flex-direction: column; padding: 19px; }
            .pay-total strong { width: 100%; padding-top: 13px; border-top: 1px solid var(--line); }
            .pay-box { padding: 17px; }
            .pay-numbers, .pay-examples { grid-template-columns: 1fr; }
            .pay-choices { display: grid; grid-template-columns: 1fr 1fr; }
            .pay-choice { justify-content: center; padding: 12px 8px; }
            .pay-box form > .button { width: 100%; white-space: normal; text-align: center; }
        }
        @media (prefers-reduced-motion: reduce) { .pay-choice { transition: none; } }
    </style>

    <script>
        (() => {
            const phoneInput = document.getElementById('customer_phone');
            const detectionMessage = document.getElementById('operator-detection');
            const previousMethod = @json(old('payment_method'));

            if (!phoneInput || !detectionMessage) {
                return;
            }

            const operatorPrefixes = {
                ORANGE_MONEY: new Set([
                    '04', '05', '06', '07', '14', '15', '16', '17', '24', '25', '26', '27',
                    '34', '35', '36', '37', '41', '42', '43', '44', '45', '46', '47', '48', '49',
                    '54', '55', '56', '57', '64', '65', '66', '67', '74', '75', '76', '77',
                ]),
                MOOV_MONEY: new Set([
                    '00', '01', '02', '03', '10', '11', '12', '13', '20', '21', '22', '23',
                    '30', '31', '32', '33', '40', '50', '51', '52', '53', '60', '61', '62', '63',
                    '70', '71', '72', '73',
                ]),
            };

            function detectOperator(shouldSelect) {
                let digits = phoneInput.value.replace(/\D/g, '');

                if (digits.startsWith('00226')) {
                    digits = digits.slice(5);
                } else if (digits.length > 8 && digits.startsWith('226')) {
                    digits = digits.slice(3);
                }

                const prefix = digits.slice(0, 2);
                const method = operatorPrefixes.ORANGE_MONEY.has(prefix)
                    ? 'ORANGE_MONEY'
                    : operatorPrefixes.MOOV_MONEY.has(prefix)
                        ? 'MOOV_MONEY'
                        : null;

                detectionMessage.classList.remove('is-detected', 'is-unknown');

                if (!method) {
                    detectionMessage.textContent = digits.length < 2
                        ? 'Saisissez votre numéro : le réseau sera estimé à partir de son préfixe.'
                        : 'Réseau non reconnu automatiquement. Choisissez le moyen utilisé pour le transfert.';
                    detectionMessage.classList.add('is-unknown');

                    return;
                }

                const operatorName = method === 'ORANGE_MONEY' ? 'Orange Money' : 'Moov Money';
                detectionMessage.textContent = `Réseau estimé : ${operatorName}. Vérifiez que c’est bien le compte utilisé pour le transfert.`;
                detectionMessage.classList.add('is-detected');

                if (shouldSelect) {
                    const radio = document.querySelector(`input[name="payment_method"][value="${method}"]`);

                    if (radio) {
                        radio.checked = true;
                    }
                }
            }

            detectOperator(!previousMethod);
            phoneInput.addEventListener('input', () => detectOperator(true));
        })();
    </script>
@endsection

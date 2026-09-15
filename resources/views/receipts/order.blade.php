<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #221f1d; font-size: 12px; }
        .header { width: 100%; margin-bottom: 30px; }
        .header td { vertical-align: top; }
        .brand { font-size: 22px; font-weight: bold; color: #b1372f; }
        .muted { color: #857b72; }
        .title { font-size: 18px; font-weight: bold; margin: 20px 0 4px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 16px; }
        table.items th { background: #f3efe8; text-align: left; padding: 8px; font-size: 11px; text-transform: uppercase; }
        table.items td { padding: 8px; border-bottom: 1px solid #e6ddd0; }
        .totals { width: 100%; margin-top: 10px; }
        .totals td { padding: 4px 8px; }
        .totals .label { text-align: right; color: #857b72; }
        .totals .value { text-align: right; width: 120px; }
        .totals .grand td { font-weight: bold; font-size: 14px; border-top: 2px solid #221f1d; padding-top: 8px; }
        .badge { display: inline-block; padding: 4px 10px; background: #eef1ea; color: #2f6f4e; border-radius: 10px; font-size: 11px; font-weight: bold; }
        .footer { margin-top: 40px; padding-top: 12px; border-top: 1px solid #e6ddd0; color: #857b72; font-size: 10px; text-align: center; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <div class="brand">MOSNOKY</div>
                <div class="muted">Chaussures de luxe & maroquinerie<br>Pouytenga, Burkina Faso<br>+226 74 91 91 33 · Mosnoky@gmail.com</div>
            </td>
            <td style="text-align:right;">
                <div class="title">REÇU DE PAIEMENT</div>
                <div class="muted">N° commande : {{ $order->order_number }}</div>
                <div class="muted">Date de paiement : {{ $payment->paid_at?->format('d/m/Y à H:i') }}</div>
                <div style="margin-top:8px;"><span class="badge">PAYÉ</span></div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th style="text-align:right;">Prix unitaire</th>
                <th style="text-align:right;">Sous-total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Produit' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td style="text-align:right;">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                    <td style="text-align:right;">{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Sous-total</td>
            <td class="value">{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td class="label">Livraison</td>
            <td class="value">{{ number_format($order->shipping_fee, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="grand">
            <td class="label">Total payé</td>
            <td class="value">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
        </tr>
    </table>

    <table style="width:100%; margin-top:24px;">
        <tr>
            <td>
                <strong>Moyen de paiement :</strong> {{ str_replace('_', ' ', $payment->payment_method) }}<br>
                <strong>Référence de transaction :</strong> {{ $payment->reference }}
            </td>
        </tr>
    </table>

    <div class="footer">
        Merci pour votre confiance — Mosnoky, Made in Burkina Faso.<br>
        Ce document fait office de reçu de paiement pour la commande {{ $order->order_number }}.
    </div>
</body>
</html>

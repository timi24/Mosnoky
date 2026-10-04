<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #221f1d; font-size: 12px; }
        .header { width: 100%; margin-bottom: 24px; }
        .header td { vertical-align: top; }
        .brand { font-size: 20px; font-weight: bold; color: #b1372f; }
        .muted { color: #857b72; }
        .title { font-size: 16px; font-weight: bold; margin: 16px 0 4px; }

        .kpi-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .kpi-table td { width: 25%; padding: 14px; border: 1px solid #e6ddd0; text-align: center; }
        .kpi-label { display: block; font-size: 9px; text-transform: uppercase; color: #857b72; margin-bottom: 4px; }
        .kpi-value { display: block; font-size: 16px; font-weight: bold; color: #221f1d; }

        h2.section { font-size: 13px; margin: 22px 0 8px; padding-bottom: 4px; border-bottom: 2px solid #221f1d; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.data th { background: #f3efe8; text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; }
        table.data td { padding: 6px 8px; border-bottom: 1px solid #e6ddd0; font-size: 11px; }

        .pill { display: inline-block; padding: 2px 8px; background: #eef1ea; color: #2f6f4e; border-radius: 8px; font-size: 10px; font-weight: bold; }
        .pill.low { background: #fbe9e6; color: #b1372f; }

        .footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #e6ddd0; color: #857b72; font-size: 9px; text-align: center; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <div class="brand">MOSNOKY</div>
                <div class="muted">Chaussures de luxe & maroquinerie · Pouytenga, Burkina Faso</div>
            </td>
            <td style="text-align:right;">
                <div class="title">RAPPORT MENSUEL DE GESTION</div>
                <div class="muted">Période : {{ $periodStart->translatedFormat('F Y') }}</div>
                <div class="muted">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
            </td>
        </tr>
    </table>

    <!-- KPI principaux -->
    <table class="kpi-table">
        <tr>
            <td>
                <span class="kpi-label">Chiffre d'affaires encaissé</span>
                <span class="kpi-value">{{ number_format($revenue, 0, ',', ' ') }} FCFA</span>
            </td>
            <td>
                <span class="kpi-label">Commandes</span>
                <span class="kpi-value">{{ $totalOrders }}</span>
            </td>
            <td>
                <span class="kpi-label">Panier moyen</span>
                <span class="kpi-value">{{ number_format($averageOrderValue, 0, ',', ' ') }} FCFA</span>
            </td>
            <td>
                <span class="kpi-label">Nouveaux clients</span>
                <span class="kpi-value">{{ $newClientsCount }}</span>
            </td>
        </tr>
    </table>

    <!-- Commandes par statut -->
    <h2 class="section">Commandes par statut</h2>
    @if ($orderCountByStatus->isEmpty())
        <p class="muted">Aucune commande sur cette période.</p>
    @else
        <table class="data">
            <thead>
                <tr><th>Statut</th><th>Nombre</th></tr>
            </thead>
            <tbody>
                @foreach ($orderCountByStatus as $status => $count)
                    <tr>
                        <td>{{ $status }}</td>
                        <td>{{ $count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Répartition des moyens de paiement -->
    <h2 class="section">Paiements encaissés par moyen</h2>
    @if ($paymentMethodBreakdown->isEmpty())
        <p class="muted">Aucun paiement confirmé sur cette période.</p>
    @else
        <table class="data">
            <thead>
                <tr><th>Moyen de paiement</th><th>Nombre</th><th>Montant total</th></tr>
            </thead>
            <tbody>
                @foreach ($paymentMethodBreakdown as $row)
                    <tr>
                        <td>{{ str_replace('_', ' ', $row->payment_method) }}</td>
                        <td>{{ $row->total }}</td>
                        <td>{{ number_format($row->amount_sum, 0, ',', ' ') }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Top produits -->
    <h2 class="section">Produits les plus vendus (top 10)</h2>
    @if ($topProducts->isEmpty())
        <p class="muted">Aucune vente sur cette période.</p>
    @else
        <table class="data">
            <thead>
                <tr><th>Produit</th><th>Quantité vendue</th><th>Chiffre d'affaires</th></tr>
            </thead>
            <tbody>
                @foreach ($topProducts as $row)
                    <tr>
                        <td>{{ $row->product->name ?? 'Produit supprimé' }}</td>
                        <td>{{ $row->qty }}</td>
                        <td>{{ number_format($row->revenue, 0, ',', ' ') }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Alerte stock -->
    <h2 class="section">Produits en stock bas ({{ $totalActiveProducts }} produits actifs au total)</h2>
    @if ($lowStockProducts->isEmpty())
        <p class="muted">Aucun produit en stock bas actuellement.</p>
    @else
        <table class="data">
            <thead>
                <tr><th>Produit</th><th>Stock actuel</th><th>Seuil d'alerte</th></tr>
            </thead>
            <tbody>
                @foreach ($lowStockProducts as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td><span class="pill low">{{ $product->stock }}</span></td>
                        <td>{{ $product->alert_threshold }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Rapport généré automatiquement par la plateforme Mosnoky · Confidentiel, usage interne uniquement.
    </div>
</body>
</html>

@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
    <section class="admin-dashboard-intro">
        <div class="admin-dashboard-eyebrow">Administration</div>
        <h1>Bonjour, {{ auth()->user()->first_name ?? auth()->user()->name }}.</h1>
        <p>Voici les points qui demandent votre attention.</p>
    </section>

    <section aria-labelledby="admin-dashboard-alerts-title" class="admin-dashboard-section">
        <div class="admin-dashboard-section-head">
            <div>
                <h2 id="admin-dashboard-alerts-title">À traiter</h2>
                <p>Les tâches urgentes de la boutique</p>
            </div>
        </div>

        <div class="admin-dashboard-alerts">
            <a class="admin-dashboard-alert {{ $pendingOrderCount > 0 ? 'has-pending' : '' }}" href="{{ route('admin.orders.index') }}">
                <span class="admin-dashboard-alert-label">Commandes en attente</span>
                <strong>{{ $pendingOrderCount }}</strong>
                <span class="admin-dashboard-alert-action">Voir les commandes <span aria-hidden="true">→</span></span>
            </a>
            <a class="admin-dashboard-alert {{ $pendingPaymentCount > 0 ? 'has-pending' : '' }}" href="{{ route('admin.payments.index') }}">
                <span class="admin-dashboard-alert-label">Paiements à vérifier</span>
                <strong>{{ $pendingPaymentCount }}</strong>
                <span class="admin-dashboard-alert-action">Vérifier les paiements <span aria-hidden="true">→</span></span>
            </a>
            <a class="admin-dashboard-alert {{ $unreadMessageCount > 0 ? 'has-pending' : '' }}" href="{{ route('admin.messages.index') }}">
                <span class="admin-dashboard-alert-label">Messages non lus</span>
                <strong>{{ $unreadMessageCount }}</strong>
                <span class="admin-dashboard-alert-action">Ouvrir les messages <span aria-hidden="true">→</span></span>
            </a>
            <a class="admin-dashboard-alert {{ $lowStockCount > 0 ? 'has-pending' : '' }}" href="{{ route('admin.products.index') }}">
                <span class="admin-dashboard-alert-label">Produits en stock bas</span>
                <strong>{{ $lowStockCount }}</strong>
                <span class="admin-dashboard-alert-action">Voir le catalogue <span aria-hidden="true">→</span></span>
            </a>
            <a class="admin-dashboard-alert {{ $pendingReviewCount > 0 ? 'has-pending' : '' }}" href="{{ route('admin.reviews.index') }}">
                <span class="admin-dashboard-alert-label">Avis à modérer</span>
                <strong>{{ $pendingReviewCount }}</strong>
                <span class="admin-dashboard-alert-action">Modérer les avis <span aria-hidden="true">→</span></span>
            </a>
        </div>
    </section>

    <section aria-labelledby="admin-dashboard-shortcuts-title" class="admin-dashboard-section admin-dashboard-shortcuts-section">
        <div class="admin-dashboard-section-head">
            <div>
                <h2 id="admin-dashboard-shortcuts-title">Accès rapides</h2>
                <p>Les autres outils d’administration</p>
            </div>
        </div>

        <div class="admin-dashboard-shortcuts">
            <a href="{{ route('admin.products.create') }}"><span aria-hidden="true">＋</span><span><strong>Ajouter un produit</strong><small>Créer une nouvelle fiche au catalogue</small></span></a>
            <a href="{{ route('admin.deliveries.index') }}"><span aria-hidden="true">↗</span><span><strong>Gérer les livraisons</strong><small>Suivre les commandes à expédier</small></span></a>
            <a href="{{ route('admin.business-report.form') }}"><span aria-hidden="true">▤</span><span><strong>Rapport mensuel</strong><small>Consulter les résultats de la boutique</small></span></a>
        </div>
    </section>

    <section aria-labelledby="admin-dashboard-reviews-title" class="admin-dashboard-section admin-dashboard-reviews-section">
        <div class="admin-dashboard-section-head">
            <div>
                <h2 id="admin-dashboard-reviews-title">Avis à modérer</h2>
                <p>Les cinq derniers avis en attente</p>
            </div>
            <a class="button small" href="{{ route('admin.reviews.index') }}">Tous les avis</a>
        </div>

        @if ($recentReviews->isEmpty())
            <div class="empty">Aucun avis en attente pour le moment.</div>
        @else
            <div class="panel admin-dashboard-review-panel">
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Produit</th>
                            <th>Note</th>
                            <th>Commentaire</th>
                            <th aria-label="Action"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentReviews as $review)
                            <tr>
                                <td>{{ $review->client->user?->displayName() ?? 'Compte indisponible' }}</td>
                                <td>{{ $review->product->name ?? '—' }}</td>
                                <td>{{ str_repeat('⭐', $review->rating) }}</td>
                                <td>{{ Str::limit($review->comment, 50) ?: '—' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="button small">Approuver</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <style>
        .admin-dashboard-intro { padding: 10px 0 24px; }
        .admin-dashboard-eyebrow { color: var(--brand-red); font-size: .78rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
        .admin-dashboard-intro h1 { margin: 10px 0 6px; font-family: Georgia, serif; font-size: clamp(2rem, 4vw, 3rem); font-weight: 400; }
        .admin-dashboard-intro p, .admin-dashboard-section-head p { margin: 0; color: var(--muted); line-height: 1.6; }
        .admin-dashboard-section { padding: 24px 0; border-top: 1px solid var(--line); }
        .admin-dashboard-section-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 16px; }
        .admin-dashboard-section-head h2 { margin: 0 0 3px; font-family: Georgia, serif; font-size: 1.55rem; font-weight: 400; }
        .admin-dashboard-section-head p { font-size: .88rem; }
        .admin-dashboard-alerts { display: grid; grid-template-columns: repeat(auto-fit, minmax(185px, 1fr)); gap: 12px; }
        .admin-dashboard-alert { display: flex; min-height: 152px; flex-direction: column; align-items: flex-start; padding: 18px; border: 1px solid var(--line); border-radius: 12px; background: var(--card); transition: border-color .2s ease, transform .2s ease; }
        .admin-dashboard-alert:hover { transform: translateY(-2px); border-color: var(--brand-red); }
        .admin-dashboard-alert.has-pending { border-left: 4px solid var(--brand-red); }
        .admin-dashboard-alert-label { color: var(--muted); font-size: .78rem; font-weight: 700; }
        .admin-dashboard-alert strong { margin: 8px 0; font-size: 2rem; line-height: 1; }
        .admin-dashboard-alert-action { margin-top: auto; color: var(--brand-red); font-size: .8rem; font-weight: 700; }
        .admin-dashboard-shortcuts { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; }
        .admin-dashboard-shortcuts a { display: flex; align-items: center; gap: 12px; min-height: 76px; padding: 14px; border: 1px solid var(--line); border-radius: 12px; background: var(--card); }
        .admin-dashboard-shortcuts a:hover { border-color: var(--brand-red); }
        .admin-dashboard-shortcuts a > span:first-child { display: grid; width: 38px; height: 38px; flex: none; place-items: center; border-radius: 10px; background: #f7e9e6; color: var(--brand-red); font-size: 1.3rem; }
        .admin-dashboard-shortcuts strong, .admin-dashboard-shortcuts small { display: block; }
        .admin-dashboard-shortcuts strong { font-size: .9rem; }
        .admin-dashboard-shortcuts small { margin-top: 3px; color: var(--muted); font-size: .76rem; }
        .admin-dashboard-reviews-section { padding-bottom: 40px; }
        @media (max-width: 520px) {
            .admin-dashboard-intro { padding-top: 2px; }
            .admin-dashboard-alerts { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
            .admin-dashboard-alert { min-height: 136px; padding: 13px; }
            .admin-dashboard-alert-label { font-size: .72rem; }
            .admin-dashboard-alert strong { font-size: 1.8rem; }
            .admin-dashboard-alert-action { font-size: .72rem; }
            .admin-dashboard-shortcuts { grid-template-columns: 1fr; }
            .admin-dashboard-section-head { align-items: flex-start; }
            .admin-dashboard-review-panel { overflow-x: auto; }
            .admin-dashboard-review-panel table { min-width: 680px; }
        }
        @media (prefers-reduced-motion: reduce) { .admin-dashboard-alert { transition: none; } }
    </style>
@endsection

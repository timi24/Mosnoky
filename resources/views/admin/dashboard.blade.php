@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
    <section style="padding: 10px 0 24px;">
        <div style="color:var(--brand-red); font-size:.78rem; font-weight:800; letter-spacing:.14em; text-transform:uppercase;">Administration</div>
        <h1 style="margin:10px 0 6px; font-family:Georgia, serif; font-size:clamp(2rem, 4vw, 3rem); font-weight:400;">Bienvenue, {{ auth()->user()->first_name ?? auth()->user()->name }}.</h1>
        <p style="max-width:620px; color:var(--muted); font-size:1rem; line-height:1.6;">Gérez votre catalogue, suivez les commandes et surveillez l'activité de la boutique en un coup d'œil.</p>
    </section>

    <section style="padding:12px 0 8px;">
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:14px;">
            <div class="panel" style="padding:20px;">
                <div style="font-size:.78rem; font-weight:800; text-transform:uppercase; color:var(--muted);">Produits</div>
                <div style="margin-top:6px; font-size:2rem; font-weight:800;">{{ $productCount ?? 0 }}</div>
                <div style="margin-top:4px; font-size:.8rem; color:var(--muted);">au catalogue</div>
            </div>
            <div class="panel" style="padding:20px;">
                <div style="font-size:.78rem; font-weight:800; text-transform:uppercase; color:var(--muted);">Commandes</div>
                <div style="margin-top:6px; font-size:2rem; font-weight:800;">{{ $orderCount ?? 0 }}</div>
                <div style="margin-top:4px; font-size:.8rem; color:var(--muted);">{{ $pendingOrderCount ?? 0 }} en attente</div>
            </div>
            <div class="panel" style="padding:20px;">
                <div style="font-size:.78rem; font-weight:800; text-transform:uppercase; color:var(--muted);">Clients</div>
                <div style="margin-top:6px; font-size:2rem; font-weight:800;">{{ $clientCount ?? 0 }}</div>
                <div style="margin-top:4px; font-size:.8rem; color:var(--muted);">comptes actifs</div>
            </div>
            <div class="panel" style="padding:20px;">
                <div style="font-size:.78rem; font-weight:800; text-transform:uppercase; color:var(--muted);">Messages non lus</div>
                <div style="margin-top:6px; font-size:2rem; font-weight:800;">{{ $unreadMessageCount ?? 0 }}</div>
                <div style="margin-top:4px; font-size:.8rem; color:var(--muted);">à traiter</div>
            </div>
        </div>
    </section>

    <section style="padding:36px 0 20px; border-top:1px solid var(--line); margin-top:24px;">
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px;">
            <a class="panel" style="padding:22px; display:block;" href="{{ route('admin.products.index') }}">
                <h3 style="margin:0 0 6px;">Produits</h3>
                <p style="margin:0; color:var(--muted); font-size:.9rem;">Ajouter, modifier ou retirer des produits.</p>
            </a>
            <a class="panel" style="padding:22px; display:block;" href="{{ route('admin.orders.index') }}">
                <h3 style="margin:0 0 6px;">Commandes</h3>
                <p style="margin:0; color:var(--muted); font-size:.9rem;">Suivre et mettre à jour le statut des commandes.</p>
            </a>
            <a class="panel" style="padding:22px; display:block;" href="{{ route('admin.deliveries.index') }}">
                <h3 style="margin:0 0 6px;">Livraisons</h3>
                <p style="margin:0; color:var(--muted); font-size:.9rem;">Suivre les expéditions et statuts de livraison.</p>
            </a>
            <a class="panel" style="padding:22px; display:block;" href="{{ route('admin.reviews.index') }}">
                <h3 style="margin:0 0 6px;">Avis clients</h3>
                <p style="margin:0; color:var(--muted); font-size:.9rem;">Modérer les avis laissés sur les produits.</p>
            </a>
            <a class="panel" style="padding:22px; display:block;" href="{{ route('admin.messages.index') }}">
                <h3 style="margin:0 0 6px;">Messages</h3>
                <p style="margin:0; color:var(--muted); font-size:.9rem;">Discuter directement avec vos clients.</p>
            </a>
            <a class="panel" style="padding:22px; display:block;" href="{{ route('admin.clients.index') }}">
                <h3 style="margin:0 0 6px;">Clients</h3>
                <p style="margin:0; color:var(--muted); font-size:.9rem;">Consulter les comptes et l'activité des clients.</p>
            </a>
        </div>
    </section>

    <!-- Derniers avis à modérer -->
    <section style="padding:12px 0 40px; border-top:1px solid var(--line); margin-top:12px;">
        <div class="page-head" style="margin-top:24px;">
            <h2 style="margin:0; font-family:Georgia, serif; font-size:1.6rem; font-weight:400;">Avis en attente de modération</h2>
            <a class="button small" href="{{ route('admin.reviews.index') }}">Voir tous les avis</a>
        </div>

        @if ($recentReviews->isEmpty())
            <div class="empty">Aucun avis en attente pour le moment.</div>
        @else
            <div class="panel">
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Produit</th>
                            <th>Note</th>
                            <th>Commentaire</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentReviews as $review)
                            <tr>
                                <td>{{ $review->client->user->first_name ?? '—' }}</td>
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
@endsection

@extends('layouts.admin')

@section('title', 'Avis clients')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Avis clients</h1>
            <p class="page-subtitle">Modérez les avis laissés sur vos produits.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.reviews.index') }}" style="margin-bottom: 18px;">
        <select name="status" onchange="this.form.submit()" style="padding:9px 12px; border:1px solid var(--line); border-radius:8px;">
            <option value="">Tous les statuts</option>
            <option value="EN_ATTENTE" {{ $currentStatus === 'EN_ATTENTE' ? 'selected' : '' }}>En attente</option>
            <option value="APPROUVE" {{ $currentStatus === 'APPROUVE' ? 'selected' : '' }}>Approuvés</option>
            <option value="MASQUE" {{ $currentStatus === 'MASQUE' ? 'selected' : '' }}>Masqués</option>
        </select>
    </form>

    @if ($reviews->isEmpty())
        <div class="empty">Aucun avis pour le moment.</div>
    @else
        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Produit</th>
                        <th>Note</th>
                        <th>Commentaire</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reviews as $review)
                        <tr>
                            <td>{{ $review->client->user->first_name ?? '—' }}</td>
                            <td>{{ $review->product->name ?? '—' }}</td>
                            <td>{{ str_repeat('⭐', $review->rating) }}</td>
                            <td>{{ Str::limit($review->comment, 50) ?: '—' }}</td>
                            <td>
                                @if ($review->moderation_status === 'APPROUVE')
                                    <span class="pill">Approuvé</span>
                                @elseif ($review->moderation_status === 'MASQUE')
                                    <span class="pill muted">Masqué</span>
                                @else
                                    <span class="pill muted">En attente</span>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions">
                                    @if ($review->moderation_status !== 'APPROUVE')
                                        <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="button small">Approuver</button>
                                        </form>
                                    @endif
                                    @if ($review->moderation_status !== 'MASQUE')
                                        <form method="POST" action="{{ route('admin.reviews.hide', $review) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="button small danger">Masquer</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $reviews->links() }}
        </div>
    @endif
@endsection

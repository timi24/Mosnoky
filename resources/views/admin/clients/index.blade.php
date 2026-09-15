@extends('layouts.admin')

@section('title', 'Clients')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Clients</h1>
            <p class="page-subtitle">Consultez les comptes clients et leur activité.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.clients.index') }}" style="margin-bottom: 18px;">
        <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher par nom ou email…"
               style="padding:9px 12px; border:1px solid var(--line); border-radius:8px; width:280px;">
        <button type="submit" class="button small">Rechercher</button>
        @if ($search)
            <a class="button small" href="{{ route('admin.clients.index') }}">Réinitialiser</a>
        @endif
    </form>

    @if ($clients->isEmpty())
        <div class="empty">Aucun client trouvé.</div>
    @else
        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Statut</th>
                        <th>Commandes</th>
                        <th>Client depuis</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clients as $client)
                        <tr>
                            <td>{{ $client->user->first_name ?? '—' }} {{ $client->user->last_name ?? '' }}</td>
                            <td>{{ $client->user->email ?? '—' }}</td>
                            <td>
                                @if (($client->user->account_status ?? null) === 'ACTIF')
                                    <span class="pill">Actif</span>
                                @else
                                    <span class="pill muted">{{ $client->user->account_status ?? '—' }}</span>
                                @endif
                            </td>
                            <td>{{ $client->orders_count }}</td>
                            <td>{{ $client->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a class="button small" href="{{ route('admin.clients.show', $client) }}">Voir la fiche</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $clients->links() }}
        </div>
    @endif
@endsection

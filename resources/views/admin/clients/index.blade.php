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
                        <th>Compte créé le</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clients as $client)
                        @php($user = $client->user)
                        @php($accountStatus = $user?->account_status)
                        <tr>
                            <td>{{ $user?->displayName() ?? 'Compte indisponible' }}</td>
                            <td>{{ $user?->email ?? '—' }}</td>
                            <td>
                                @if ($accountStatus === 'ACTIF')
                                    <span class="pill">Actif</span>
                                @elseif ($accountStatus === 'BLOQUE')
                                    <span class="pill muted">Bloqué</span>
                                @elseif ($accountStatus === 'SUSPENDU')
                                    <span class="pill muted">Suspendu</span>
                                @else
                                    <span class="pill muted">{{ $accountStatus ?? 'Compte indisponible' }}</span>
                                @endif
                            </td>
                            <td>{{ $client->orders_count }}</td>
                            <td>{{ ($user?->created_at ?? $client->created_at)->format('d/m/Y') }}</td>
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

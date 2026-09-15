@extends('layouts.admin')

@section('title', 'Signalements')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Signalements</h1>
            <p class="page-subtitle">Traitez les problèmes signalés par vos clients.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.reports.index') }}" style="margin-bottom: 18px;">
        <select name="status" onchange="this.form.submit()" style="padding:9px 12px; border:1px solid var(--line); border-radius:8px;">
            <option value="">Tous les statuts</option>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" {{ $currentStatus === $status ? 'selected' : '' }}>{{ $status }}</option>
            @endforeach
        </select>
    </form>

    @if ($reports->isEmpty())
        <div class="empty">Aucun signalement pour le moment.</div>
    @else
        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Commande</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $report)
                        <tr>
                            <td>{{ $report->client->user->first_name ?? '—' }}</td>
                            <td>{{ $report->report_type }}</td>
                            <td>{{ $report->order->order_number ?? '—' }}</td>
                            <td>{{ Str::limit($report->description, 50) }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.reports.update-status', $report) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" style="padding:6px 10px; border:1px solid var(--line); border-radius:6px; font-size:.82rem;">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}" {{ $report->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td>
                                @if ($report->client)
                                    <a class="button small" href="{{ route('admin.messages.show', $report->client->user) }}">Contacter</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $reports->links() }}
        </div>
    @endif
@endsection

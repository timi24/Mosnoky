@extends('layouts.client')

@section('title', 'Mes signalements')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Mes signalements</h1>
        </div>
        <a class="button primary" href="{{ route('reports.create') }}">+ Nouveau signalement</a>
    </div>

    @if ($reports->isEmpty())
        <div class="empty">Vous n'avez fait aucun signalement.</div>
    @else
        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Commande liée</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $report)
                        <tr>
                            <td>{{ $report->report_type }}</td>
                            <td>{{ $report->order->order_number ?? '—' }}</td>
                            <td>{{ Str::limit($report->description, 60) }}</td>
                            <td><span class="pill muted">{{ $report->status }}</span></td>
                            <td>{{ $report->created_at->format('d/m/Y') }}</td>
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

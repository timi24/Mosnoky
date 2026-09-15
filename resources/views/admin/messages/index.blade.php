@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Messages</h1>
            <p class="page-subtitle">Discutez directement avec vos clients.</p>
        </div>
    </div>

    @if ($conversations->isEmpty())
        <div class="empty">Aucune conversation pour le moment.</div>
    @else
        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Dernier message</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($conversations as $conv)
                        <tr>
                            <td>
                                {{ $conv['user']->first_name }} {{ $conv['user']->last_name }}
                                @if ($conv['unreadCount'] > 0)
                                    <span class="badge">{{ $conv['unreadCount'] }}</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($conv['lastMessage']?->content, 60) }}</td>
                            <td>{{ $conv['lastMessage']?->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a class="button small" href="{{ route('admin.messages.show', $conv['user']) }}">Ouvrir</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection

@extends('layouts.admin')

@section('title', 'Gérer la livraison')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Livraison — {{ $order->order_number }}</h1>
            <p class="page-subtitle">Client : {{ $order->client->user->first_name ?? '' }} {{ $order->client->user->last_name ?? '' }}</p>
        </div>
        <a class="button small" href="{{ route('admin.deliveries.index') }}">← Retour</a>
    </div>

    <div class="form-panel" style="max-width:560px;">
        <form method="POST" action="{{ route('admin.deliveries.update', $order) }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="tracking_number">Numéro de suivi</label>
                <input type="text" id="tracking_number" name="tracking_number" value="{{ old('tracking_number', $delivery->tracking_number ?? '') }}" required>
                @error('tracking_number')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="carrier">Transporteur</label>
                <input type="text" id="carrier" name="carrier" value="{{ old('carrier', $delivery->carrier ?? '') }}" placeholder="Ex : Coursier local, DHL…">
            </div>

            <div class="field">
                <label for="status">Statut de la livraison</label>
                <select id="status" name="status">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" {{ old('status', $delivery->status ?? 'EN_PREPARATION') === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="expected_delivery_at">Date de livraison estimée</label>
                <input type="date" id="expected_delivery_at" name="expected_delivery_at"
                       value="{{ old('expected_delivery_at', optional($delivery->expected_delivery_at ?? null)->format('Y-m-d')) }}">
                <div class="hint">Le client verra cette date sur sa commande.</div>
            </div>

            <div class="field">
                <label for="tracking_comment">Commentaire de suivi (optionnel)</label>
                <textarea id="tracking_comment" name="tracking_comment" style="width:100%; min-height:80px; padding:10px 12px; border:1px solid var(--line); border-radius:8px;">{{ old('tracking_comment', $delivery->tracking_comment ?? '') }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="button primary">Enregistrer</button>
            </div>
        </form>
    </div>
@endsection

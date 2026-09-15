@extends('layouts.client')

@section('title', 'Nouveau signalement')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Signaler un problème</h1>
            <p class="page-subtitle">Notre équipe traitera votre demande rapidement.</p>
        </div>
    </div>

    <div class="form-panel">
        <form method="POST" action="{{ route('reports.store') }}">
            @csrf

            <div class="field">
                <label for="report_type">Type de problème</label>
                <select id="report_type" name="report_type" required>
                    <option value="PROBLEME_LIVRAISON">Problème de livraison</option>
                    <option value="PRODUIT_DEFECTUEUX">Produit défectueux</option>
                    <option value="COMMENTAIRE_INAPPROPRIE">Commentaire inapproprié</option>
                    <option value="AUTRE">Autre</option>
                </select>
            </div>

            <div class="field">
                <label for="order_id">Commande concernée (optionnel)</label>
                <select id="order_id" name="order_id">
                    <option value="">— Aucune commande spécifique —</option>
                    @foreach ($orders as $order)
                        <option value="{{ $order->id }}" {{ $selectedOrderId == $order->id ? 'selected' : '' }}>
                            {{ $order->order_number }} — {{ $order->created_at->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="description">Décrivez le problème</label>
                <textarea id="description" name="description" required style="width:100%; min-height:120px; padding:10px 12px; border:1px solid var(--line); border-radius:8px;"></textarea>
                @error('description')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="button primary">Envoyer le signalement</button>
                <a class="button" href="{{ route('reports.index') }}">Annuler</a>
            </div>
        </form>
    </div>
@endsection

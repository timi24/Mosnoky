@extends('layouts.admin')

@section('title', 'Nouvelle catégorie')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Nouvelle catégorie</h1>
            <p class="page-subtitle">Ajoutez une catégorie à votre catalogue.</p>
        </div>
    </div>

    <div class="form-panel">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf

            <div class="field">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description') }}</textarea>
                @error('description')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field checkbox-field">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" style="margin: 0;">Catégorie active (visible côté client)</label>
            </div>

            <div class="form-actions">
                <button type="submit" class="button primary">Créer la catégorie</button>
                <a class="button" href="{{ route('admin.categories.index') }}">Annuler</a>
            </div>
        </form>
    </div>
@endsection

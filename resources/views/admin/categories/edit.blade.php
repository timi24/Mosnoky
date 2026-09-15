@extends('layouts.admin')

@section('title', 'Modifier la catégorie')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Modifier « {{ $category->name }} »</h1>
        </div>
    </div>

    <div class="form-panel">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field checkbox-field">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                <label for="is_active" style="margin: 0;">Catégorie active (visible côté client)</label>
            </div>

            <div class="form-actions">
                <button type="submit" class="button primary">Enregistrer les modifications</button>
                <a class="button" href="{{ route('admin.categories.index') }}">Annuler</a>
            </div>
        </form>
    </div>
@endsection

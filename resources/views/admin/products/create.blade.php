@extends('layouts.admin')

@section('title', 'Nouveau produit')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Nouveau produit</h1>
            <p class="page-subtitle">Ajoutez un produit à votre catalogue.</p>
        </div>
    </div>

    <div class="form-panel">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="field">
                <label for="name">Nom du produit</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="category_id">Catégorie</label>
                <select id="category_id" name="category_id" required>
                    <option value="">— Choisir une catégorie —</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description" required>{{ old('description') }}</textarea>
                @error('description')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="price">Prix (FCFA)</label>
                <input type="number" step="1" min="0" id="price" name="price" value="{{ old('price') }}" required>
                @error('price')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="stock">Stock initial</label>
                <input type="number" min="0" id="stock" name="stock" value="{{ old('stock', 0) }}" required>
                @error('stock')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="alert_threshold">Seuil d'alerte stock bas</label>
                <input type="number" min="0" id="alert_threshold" name="alert_threshold" value="{{ old('alert_threshold', 5) }}">
                <div class="hint">Un badge "Stock bas" s'affichera sous ce seuil.</div>
                @error('alert_threshold')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="status">Statut</label>
                <select id="status" name="status">
                    <option value="ACTIF" {{ old('status', 'ACTIF') === 'ACTIF' ? 'selected' : '' }}>Actif</option>
                    <option value="INACTIF" {{ old('status') === 'INACTIF' ? 'selected' : '' }}>Inactif</option>
                    <option value="EPUISE" {{ old('status') === 'EPUISE' ? 'selected' : '' }}>Épuisé</option>
                </select>
            </div>

            <div class="field">
                <label for="available_sizes">Tailles disponibles</label>
                <input type="text" id="available_sizes" name="available_sizes" value="{{ old('available_sizes') }}" placeholder="Ex : 38, 39, 40, 41, 42">
                <div class="hint">Séparez chaque taille par une virgule. Laissez vide si non applicable (ex : sacs).</div>
            </div>

            <div class="field">
                <label for="available_colors">Couleurs disponibles</label>
                <input type="text" id="available_colors" name="available_colors" value="{{ old('available_colors') }}" placeholder="Ex : Noir, Marron, Camel">
                <div class="hint">Séparez chaque couleur par une virgule.</div>
            </div>

            <div class="field">
                <label for="images">Photos du produit</label>
                <input type="file" id="images" name="images[]" accept="image/*" multiple>
                <div class="hint">Vous pouvez sélectionner plusieurs photos (JPG, PNG). La première sera utilisée comme photo principale. Vous pourrez en ajouter d'autres après création.</div>
                @error('images.*')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="button primary">Créer le produit</button>
                <a class="button" href="{{ route('admin.products.index') }}">Annuler</a>
            </div>
        </form>
    </div>
@endsection

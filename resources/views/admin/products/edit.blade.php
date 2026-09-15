@extends('layouts.admin')

@section('title', 'Modifier le produit')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Modifier « {{ $product->name }} »</h1>
        </div>
    </div>

    <!-- Galerie de photos existantes -->
    <div class="form-panel" style="max-width:none; margin-bottom:20px;">
        <h3 style="margin-top:0;">Photos du produit</h3>

        @if ($product->images->isEmpty())
            <p style="color:var(--muted); font-size:.9rem;">Aucune photo pour le moment. Ajoutez-en une ci-dessous.</p>
        @else
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(140px, 1fr)); gap:14px; margin-bottom:20px;">
                @foreach ($product->images as $image)
                    <div style="border:1px solid var(--line); border-radius:10px; overflow:hidden; background:#fff;">
                        <div style="aspect-ratio:1; overflow:hidden; background:#eee7dc;">
                            <img src="{{ $image->image_url }}" alt="{{ $image->alternative_text }}" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <div style="padding:8px; display:flex; flex-direction:column; gap:6px;">
                            @if ($image->is_primary)
                                <span class="pill" style="text-align:center;">Photo principale</span>
                            @else
                                <form method="POST" action="{{ route('admin.products.images.make-primary', [$product, $image]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="button small" style="width:100%;">Définir comme principale</button>
                                </form>
                            @endif

                            @if (!empty($product->available_colors))
                                <form method="POST" action="{{ route('admin.products.images.update-color', [$product, $image]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="color" onchange="this.form.submit()" style="width:100%; padding:6px 8px; border:1px solid var(--line); border-radius:6px; font-size:.78rem;">
                                        <option value="">— Couleur —</option>
                                        @foreach ($product->available_colors as $color)
                                            <option value="{{ $color }}" {{ $image->color === $color ? 'selected' : '' }}>{{ $color }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" onsubmit="return confirm('Supprimer cette photo ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button small danger" style="width:100%;">Supprimer</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="name" value="{{ $product->name }}">
            <input type="hidden" name="category_id" value="{{ $product->category_id }}">
            <input type="hidden" name="description" value="{{ $product->description }}">
            <input type="hidden" name="price" value="{{ $product->price }}">
            <input type="hidden" name="stock" value="{{ $product->stock }}">
            <input type="hidden" name="alert_threshold" value="{{ $product->alert_threshold }}">
            <input type="hidden" name="status" value="{{ $product->status }}">
            <input type="hidden" name="available_sizes" value="{{ is_array($product->available_sizes) ? implode(', ', $product->available_sizes) : '' }}">
            <input type="hidden" name="available_colors" value="{{ is_array($product->available_colors) ? implode(', ', $product->available_colors) : '' }}">

            <div class="field" style="margin-bottom:10px;">
                <label for="images">Ajouter des photos</label>
                <input type="file" id="images" name="images[]" accept="image/*" multiple>
                @error('images.*')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            @if (!empty($product->available_colors))
                <div class="field" style="margin-bottom:10px;">
                    <label for="photo_color">Couleur de ces photos (optionnel)</label>
                    <select id="photo_color" name="photo_color">
                        <option value="">— Aucune couleur spécifique —</option>
                        @foreach ($product->available_colors as $color)
                            <option value="{{ $color }}">{{ $color }}</option>
                        @endforeach
                    </select>
                    <div class="hint">Les photos que vous ajoutez maintenant seront associées à cette couleur. Pour une autre couleur, téléversez-les séparément.</div>
                </div>
            @endif

            <button type="submit" class="button primary small">Téléverser</button>
        </form>
    </div>

    <div class="form-panel">
        <form method="POST" action="{{ route('admin.products.update', $product) }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="name">Nom du produit</label>
                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="category_id">Catégorie</label>
                <select id="category_id" name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                <textarea id="description" name="description" required>{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="price">Prix (FCFA)</label>
                <input type="number" step="1" min="0" id="price" name="price" value="{{ old('price', $product->price) }}" required>
                @error('price')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="stock">Stock</label>
                <input type="number" min="0" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required>
                @error('stock')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="alert_threshold">Seuil d'alerte stock bas</label>
                <input type="number" min="0" id="alert_threshold" name="alert_threshold" value="{{ old('alert_threshold', $product->alert_threshold) }}">
                @error('alert_threshold')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="available_sizes">Tailles disponibles</label>
                <input type="text" id="available_sizes" name="available_sizes" value="{{ old('available_sizes', is_array($product->available_sizes) ? implode(', ', $product->available_sizes) : '') }}" placeholder="Ex : 38, 39, 40, 41, 42">
                <div class="hint">Séparez chaque taille par une virgule. Laissez vide si non applicable.</div>
            </div>

            <div class="field">
                <label for="available_colors">Couleurs disponibles</label>
                <input type="text" id="available_colors" name="available_colors" value="{{ old('available_colors', is_array($product->available_colors) ? implode(', ', $product->available_colors) : '') }}" placeholder="Ex : Noir, Marron, Camel">
                <div class="hint">Séparez chaque couleur par une virgule.</div>
            </div>

            <div class="field">
                <label for="status">Statut</label>
                <select id="status" name="status">
                    <option value="ACTIF" {{ old('status', $product->status) === 'ACTIF' ? 'selected' : '' }}>Actif</option>
                    <option value="INACTIF" {{ old('status', $product->status) === 'INACTIF' ? 'selected' : '' }}>Inactif</option>
                    <option value="EPUISE" {{ old('status', $product->status) === 'EPUISE' ? 'selected' : '' }}>Épuisé</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="button primary">Enregistrer les modifications</button>
                <a class="button" href="{{ route('admin.products.index') }}">Annuler</a>
            </div>
        </form>
    </div>
@endsection

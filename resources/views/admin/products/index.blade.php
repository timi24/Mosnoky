@extends('layouts.admin')

@section('title', 'Produits')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Produits</h1>
            <p class="page-subtitle">Gérez le catalogue de votre boutique.</p>
        </div>
        <a class="button primary" href="{{ route('admin.products.create') }}">+ Nouveau produit</a>
    </div>

    @if ($products->isEmpty())
        <div class="empty">Aucun produit pour le moment. Ajoutez votre premier produit.</div>
    @else
        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? '—' }}</td>
                            <td>{{ number_format($product->price, 0, ',', ' ') }} FCFA</td>
                            <td>
                                {{ $product->stock }}
                                @if ($product->stock <= $product->alert_threshold)
                                    <span class="pill" style="background:#fbe9e6; color:#b1432f;">Stock bas</span>
                                @endif
                            </td>
                            <td>
                                @if ($product->status === 'ACTIF')
                                    <span class="pill">Actif</span>
                                @elseif ($product->status === 'EPUISE')
                                    <span class="pill muted">Épuisé</span>
                                @else
                                    <span class="pill muted">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a class="button small" href="{{ route('admin.products.edit', $product) }}">Modifier</a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Supprimer ce produit ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button small danger">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $products->links() }}
        </div>
    @endif
@endsection

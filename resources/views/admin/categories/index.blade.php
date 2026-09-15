@extends('layouts.admin')

@section('title', 'Catégories')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Catégories</h1>
            <p class="page-subtitle">Organisez votre catalogue produits.</p>
        </div>
        <a class="button primary" href="{{ route('admin.categories.create') }}">+ Nouvelle catégorie</a>
    </div>

    @if ($categories->isEmpty())
        <div class="empty">Aucune catégorie pour le moment. Créez-en une pour commencer.</div>
    @else
        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Produits</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td>{{ Str::limit($category->description, 60) ?: '—' }}</td>
                            <td>{{ $category->products_count }}</td>
                            <td>
                                @if ($category->is_active)
                                    <span class="pill">Active</span>
                                @else
                                    <span class="pill muted">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a class="button small" href="{{ route('admin.categories.edit', $category) }}">Modifier</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Supprimer cette catégorie ?');">
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
            {{ $categories->links() }}
        </div>
    @endif
@endsection

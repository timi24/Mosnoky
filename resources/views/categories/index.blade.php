@extends('layouts.client')

@section('title', 'Catégories')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Catégories</h1>
            <p class="page-subtitle">Parcourez notre collection par catégorie.</p>
        </div>
    </div>

    @if ($categories->isEmpty())
        <div class="empty">Aucune catégorie disponible pour le moment.</div>
    @else
        <div class="grid-cats">
            @foreach ($categories as $category)
                <a class="cat-card" href="{{ route('categories.show', $category) }}">
                    <h3>{{ $category->name }}</h3>
                    <p class="description">{{ Str::limit($category->description, 90) }}</p>
                    <span class="pill muted">{{ $category->products_count }} produit(s)</span>
                </a>
            @endforeach
        </div>
    @endif
@endsection

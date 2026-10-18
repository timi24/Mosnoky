<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->with(['products' => function ($q) {
                $q->where('status', 'ACTIF')->with('images')->latest()->limit(6);
            }])
            ->orderBy('name')
            ->get()
            ->each(function ($category) {
                // Photos à faire défiler dans la carte catégorie : on prend les
                // photos des produits de la catégorie, toutes marques confondues.
                $category->preview_images = $category->products
                    ->flatMap->images
                    ->take(8)
                    ->values();
            });

        return view('categories.index', [
            'categories' => $categories,
        ]);
    }

    public function show(Category $category): View
    {
        $products = $category->products()
            ->where('status', 'ACTIF')
            ->with('images')
            ->withCount(['reviews as approved_reviews_count' => function ($q) {
                $q->where('moderation_status', 'APPROUVE');
            }])
            ->withAvg(['reviews as approved_reviews_avg_rating' => function ($q) {
                $q->where('moderation_status', 'APPROUVE');
            }], 'rating')
            ->withSum('orderItems as sold_count', 'quantity')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('categories.show', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
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
            ->orderBy('name')
            ->get();

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
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('categories.show', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}

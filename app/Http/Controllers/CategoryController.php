<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    public function show(Request $request, Category $category): View
    {
        abort_unless($category->is_active, 404);

        $maxPriceRules = ['nullable', 'numeric', 'min:0'];

        if ($request->filled('min_price')) {
            $maxPriceRules[] = 'gte:min_price';
        }

        $validated = $request->validate([
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => $maxPriceRules,
            'availability' => ['nullable', Rule::in(['available'])],
            'sort' => ['nullable', Rule::in(['recent', 'price_asc', 'price_desc', 'popular'])],
        ], [
            'max_price.gte' => 'Le prix maximum doit être supérieur ou égal au prix minimum.',
        ]);

        $products = $category->products()
            ->where('status', 'ACTIF')
            ->when($validated['min_price'] ?? null, fn ($q, $price) => $q->where('price', '>=', $price))
            ->when($validated['max_price'] ?? null, fn ($q, $price) => $q->where('price', '<=', $price))
            ->when(($validated['availability'] ?? null) === 'available', fn ($q) => $q->where('stock', '>', 0))
            ->with('images')
            ->withCount(['reviews as approved_reviews_count' => function ($q) {
                $q->where('moderation_status', 'APPROUVE');
            }])
            ->withAvg(['reviews as approved_reviews_avg_rating' => function ($q) {
                $q->where('moderation_status', 'APPROUVE');
            }], 'rating')
            ->withSum('orderItems as sold_count', 'quantity')
            ->when(($validated['sort'] ?? 'recent') === 'price_asc', fn ($q) => $q->orderBy('price'))
            ->when(($validated['sort'] ?? 'recent') === 'price_desc', fn ($q) => $q->orderByDesc('price'))
            ->when(($validated['sort'] ?? 'recent') === 'popular', fn ($q) => $q->orderByDesc('sold_count'))
            ->when(($validated['sort'] ?? 'recent') === 'recent', fn ($q) => $q->orderByDesc('created_at'))
            ->paginate(12);

        return view('categories.show', [
            'category' => $category,
            'products' => $products,
            'filters' => $validated,
        ]);
    }
}

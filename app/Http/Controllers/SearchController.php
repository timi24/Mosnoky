<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $activeCategories = Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $maxPriceRules = ['nullable', 'numeric', 'min:0'];

        if ($request->filled('min_price')) {
            $maxPriceRules[] = 'gte:min_price';
        }

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'category_id' => ['nullable', 'integer', Rule::in($activeCategories->pluck('id')->all())],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => $maxPriceRules,
            'availability' => ['nullable', Rule::in(['available'])],
            'sort' => ['nullable', Rule::in(['recent', 'price_asc', 'price_desc', 'popular'])],
        ], [
            'max_price.gte' => 'Le prix maximum doit être supérieur ou égal au prix minimum.',
        ]);

        $query = trim($validated['q'] ?? '');

        $products = Product::query()
            ->where('status', 'ACTIF')
            ->when($validated['category_id'] ?? null, fn ($q, $categoryId) => $q->where('category_id', $categoryId))
            ->when($validated['min_price'] ?? null, fn ($q, $price) => $q->where('price', '>=', $price))
            ->when($validated['max_price'] ?? null, fn ($q, $price) => $q->where('price', '<=', $price))
            ->when(($validated['availability'] ?? null) === 'available', fn ($q) => $q->where('stock', '>', 0))
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                });
            })
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
            ->paginate(12)
            ->withQueryString();

        return view('search.index', [
            'products' => $products,
            'query' => $query,
            'categories' => $activeCategories,
            'filters' => $validated,
        ]);
    }
}

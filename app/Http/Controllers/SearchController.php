<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->string('q')->toString();

        $products = Product::query()
            ->where('status', 'ACTIF')
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
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('search.index', [
            'products' => $products,
            'query' => $query,
        ]);
    }
}

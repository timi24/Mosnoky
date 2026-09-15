<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::where('status', 'ACTIF')
            ->with('images')
            ->withCount(['reviews as approved_reviews_count' => function ($q) {
                $q->where('moderation_status', 'APPROUVE');
            }])
            ->withAvg(['reviews as approved_reviews_avg_rating' => function ($q) {
                $q->where('moderation_status', 'APPROUVE');
            }], 'rating')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        return view('storefront.index', [
            'featuredProducts' => $featuredProducts,
        ]);
    }

    public function purchase(Product $product)
    {
        // Redirige vers la connexion si non authentifié, sinon vers l'ajout au panier
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        return redirect()->route('products.show', $product);
    }
}

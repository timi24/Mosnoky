<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
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
            ->withSum('orderItems as sold_count', 'quantity')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        // Photo de fond du hero : une image de produit choisie au hasard
        // dans la base de données, parmi les produits actifs. Change à
        // chaque chargement de page.
        $heroImage = ProductImage::whereHas('product', function ($q) {
            $q->where('status', 'ACTIF');
        })->inRandomOrder()->first();

        return view('storefront.index', [
            'featuredProducts' => $featuredProducts,
            'heroImage' => $heroImage,
        ]);
    }

    public function purchase(Product $product)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        return redirect()->route('products.show', $product);
    }
}
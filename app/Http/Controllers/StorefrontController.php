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

        // Diaporama du hero : plusieurs photos de produits choisies au
        // hasard, qui défilent côté client toutes les 5 secondes.
        $heroImages = ProductImage::whereHas('product', function ($q) {
            $q->where('status', 'ACTIF');
        })->inRandomOrder()->limit(8)->get();

        return view('storefront.index', [
            'featuredProducts' => $featuredProducts,
            'heroImages' => $heroImages,
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
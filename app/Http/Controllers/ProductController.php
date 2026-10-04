<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        $product->load([
            'category',
            'images',
            'reviews' => fn ($query) => $query->where('moderation_status', 'APPROUVE')->with('client.user')->latest(),
        ]);

        $averageRating = $product->reviews->avg('rating');
        $soldCount = OrderItem::where('product_id', $product->id)->sum('quantity');

        $alreadyReviewed = false;

        if (auth()->check()) {
            $client = Client::firstOrCreate(['user_id' => auth()->id()]);

            $alreadyReviewed = Review::where('client_id', $client->id)
                ->where('product_id', $product->id)
                ->exists();
        }

        return view('products.show', [
            'product' => $product,
            'averageRating' => $averageRating,
            'soldCount' => $soldCount,
            'alreadyReviewed' => $alreadyReviewed,
        ]);
    }
}

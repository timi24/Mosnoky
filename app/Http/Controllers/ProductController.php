<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
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

        $ownReview = null;
        $eligibleReviewOrder = null;

        if (auth()->check()) {
            $client = Client::firstOrCreate(['user_id' => auth()->id()]);

            $ownReview = Review::query()
                ->where('client_id', $client->id)
                ->where('product_id', $product->id)
                ->latest()
                ->first();

            $eligibleReviewOrder = Order::query()
                ->where('client_id', $client->id)
                ->where('status', 'LIVREE')
                ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
                ->latest()
                ->first();
        }

        return view('products.show', [
            'product' => $product,
            'averageRating' => $averageRating,
            'soldCount' => $soldCount,
            'ownReview' => $ownReview,
            'eligibleReviewOrder' => $eligibleReviewOrder,
        ]);
    }
}

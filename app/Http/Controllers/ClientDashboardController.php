<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\AppNotification;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Client;
use App\Models\Message;
use App\Models\Product;
use Illuminate\View\View;

class ClientDashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user();
        $client = Client::firstOrCreate(['user_id' => $user->id]);
        $adminIds = Administrator::pluck('user_id');

        $cart = Cart::where('client_id', $client->id)->where('status', 'OUVERT')->first();

        $products = Product::where('status', 'ACTIF')
            ->with('images')
            ->withCount(['reviews as approved_reviews_count' => function ($q) {
                $q->where('moderation_status', 'APPROUVE');
            }])
            ->withAvg(['reviews as approved_reviews_avg_rating' => function ($q) {
                $q->where('moderation_status', 'APPROUVE');
            }], 'rating')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(function ($product) {
                $primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                $product->image_url = $primary->image_url ?? null;
                $product->alternative_text = $primary->alternative_text ?? null;

                return $product;
            });

        return view('client.dashboard', [
            'products' => $products,
            'orderCount' => $client->orders()->count(),
            'cartItemCount' => $cart?->items()->count() ?? 0,
            'categoryCount' => Category::where('is_active', true)->count(),
            'unreadNotificationCount' => AppNotification::where('user_id', $user->id)->where('is_read', false)->count(),
            'unreadMessageCount' => Message::whereIn('sender_id', $adminIds)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->count(),
        ]);
    }
}

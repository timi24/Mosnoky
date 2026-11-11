<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Client;
use App\Models\Message;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $client = Client::firstOrCreate(['user_id' => $user->id]);
        $adminIds = User::query()->whereRaw('UPPER(TRIM(role)) = ?', ['ADMINISTRATEUR'])->pluck('id');
        $activeCategories = Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $maxPriceRules = ['nullable', 'numeric', 'min:0'];

        if ($request->filled('min_price')) {
            $maxPriceRules[] = 'gte:min_price';
        }

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'category_id' => ['nullable', 'integer', Rule::in($activeCategories->pluck('id')->all())],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => $maxPriceRules,
            'availability' => ['nullable', Rule::in(['available'])],
            'sort' => ['nullable', Rule::in(['recent', 'price_asc', 'price_desc', 'popular'])],
        ], [
            'max_price.gte' => 'Le prix maximum doit être supérieur ou égal au prix minimum.',
        ]);

        $cart = Cart::where('client_id', $client->id)->where('status', 'OUVERT')->first();

        $search = trim($filters['q'] ?? '');
        $products = Product::query()
            ->where('status', 'ACTIF')
            ->when($filters['category_id'] ?? null, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($filters['min_price'] ?? null, fn ($query, $price) => $query->where('price', '>=', $price))
            ->when($filters['max_price'] ?? null, fn ($query, $price) => $query->where('price', '<=', $price))
            ->when(($filters['availability'] ?? null) === 'available', fn ($query) => $query->where('stock', '>', 0))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
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
            ->when(($filters['sort'] ?? 'recent') === 'price_asc', fn ($query) => $query->orderBy('price'))
            ->when(($filters['sort'] ?? 'recent') === 'price_desc', fn ($query) => $query->orderByDesc('price'))
            ->when(($filters['sort'] ?? 'recent') === 'popular', fn ($query) => $query->orderByDesc('sold_count'))
            ->when(($filters['sort'] ?? 'recent') === 'recent', fn ($query) => $query->orderByDesc('created_at'))
            ->paginate(12)
            ->withQueryString();

        return view('client.dashboard', [
            'products' => $products,
            'categories' => $activeCategories,
            'filters' => $filters,
            'orderCount' => $client->orders()->count(),
            'cartItemCount' => $cart?->items()->count() ?? 0,
            'categoryCount' => $activeCategories->count(),
            'unreadNotificationCount' => AppNotification::where('user_id', $user->id)->where('is_read', false)->count(),
            'unreadMessageCount' => Message::whereIn('sender_id', $adminIds)->where('receiver_id', $user->id)->whereNull('read_at')->count(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\Client;
use App\Models\Message;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $adminIds = Administrator::pluck('user_id');

        return view('admin.dashboard', [
            'productCount' => Product::count(),
            'orderCount' => Order::count(),
            'pendingOrderCount' => Order::where('status', 'EN_ATTENTE')->count(),
            'clientCount' => Client::count(),
            'unreadMessageCount' => Message::whereIn('receiver_id', $adminIds)->whereNull('read_at')->count(),
            'recentReviews' => Review::where('moderation_status', 'EN_ATTENTE')
                ->with(['client.user', 'product'])
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}

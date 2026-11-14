<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $adminIds = User::query()->whereRaw('UPPER(TRIM(role)) = ?', ['ADMINISTRATEUR'])->pluck('id');

        return view('admin.dashboard', [
            'pendingOrderCount' => Order::where('status', 'EN_ATTENTE')->count(),
            'pendingPaymentCount' => Payment::where('status', 'EN_ATTENTE')->whereNotNull('submitted_at')->count(),
            'unreadMessageCount' => Message::whereIn('receiver_id', $adminIds)->whereNull('read_at')->count(),
            'lowStockCount' => Product::where('status', 'ACTIF')->whereColumn('stock', '<=', 'alert_threshold')->count(),
            'pendingReviewCount' => Review::where('moderation_status', 'EN_ATTENTE')->count(),
            'recentReviews' => Review::where('moderation_status', 'EN_ATTENTE')
                ->with(['client.user', 'product'])
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}

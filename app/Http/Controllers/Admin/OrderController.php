<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const STATUSES = [
        'EN_ATTENTE',
        'CONFIRMEE',
        'EN_PREPARATION',
        'EXPEDIEE',
        'LIVREE',
        'ANNULEE',
    ];

    public function index(Request $request): View
    {
        $orders = Order::with('client.user')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => self::STATUSES,
            'currentStatus' => $request->string('status')->toString(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['client.user', 'items.product', 'payment', 'delivery', 'invoice']);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => self::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', self::STATUSES)],
        ]);

        $order->update(['status' => $validated['status']]);
        $order->loadMissing('client.user');

        if ($order->client?->user) {
            AppNotification::create([
                'user_id' => $order->client->user->id,
                'title' => 'Mise à jour de votre commande',
                'content' => "Votre commande {$order->order_number} est maintenant : {$validated['status']}.",
            ]);
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('status', 'Statut de la commande mis à jour.');
    }
}

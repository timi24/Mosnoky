<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    private const STATUSES = [
        'EN_PREPARATION',
        'EXPEDIEE',
        'EN_TRANSIT',
        'LIVREE',
        'RETARDEE',
        'ECHEC',
    ];

    public function index(Request $request): View
    {
        $orders = Order::with(['client.user', 'delivery'])
            ->whereIn('status', ['CONFIRMEE', 'EN_PREPARATION', 'EXPEDIEE', 'LIVREE'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.deliveries.index', [
            'orders' => $orders,
        ]);
    }

    public function edit(Order $order): View
    {
        $order->load('delivery', 'client.user');

        return view('admin.deliveries.edit', [
            'order' => $order,
            'delivery' => $order->delivery,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'tracking_number' => ['required', 'string', 'max:150'],
            'carrier' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'in:'.implode(',', self::STATUSES)],
            'expected_delivery_at' => ['nullable', 'date'],
            'tracking_comment' => ['nullable', 'string', 'max:500'],
        ]);

        $delivery = $order->delivery ?? new Delivery(['order_id' => $order->id]);
        $delivery->fill($validated);

        if ($validated['status'] === 'EXPEDIEE' && ! $delivery->shipped_at) {
            $delivery->shipped_at = now();
        }

        if ($validated['status'] === 'LIVREE' && ! $delivery->delivered_at) {
            $delivery->delivered_at = now();
        }

        $delivery->order_id = $order->id;
        $delivery->save();

        if (in_array($validated['status'], ['EXPEDIEE', 'LIVREE'], true)) {
            $order->update(['status' => $validated['status'] === 'LIVREE' ? 'LIVREE' : 'EXPEDIEE']);
        }

        $order->loadMissing('client.user');

        if ($order->client?->user) {
            $messages = [
                'EXPEDIEE' => "Votre commande {$order->order_number} a été expédiée.",
                'EN_TRANSIT' => "Votre commande {$order->order_number} est en transit.",
                'LIVREE' => "Votre commande {$order->order_number} a été livrée. Merci pour votre confiance !",
                'RETARDEE' => "Votre commande {$order->order_number} est retardée, nous en sommes désolés.",
            ];

            if (isset($messages[$validated['status']])) {
                AppNotification::create([
                    'user_id' => $order->client->user->id,
                    'title' => 'Suivi de livraison',
                    'content' => $messages[$validated['status']],
                ]);
            }
        }

        return redirect()
            ->route('admin.deliveries.index')
            ->with('status', 'Informations de livraison mises à jour.');
    }
}

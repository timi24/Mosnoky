<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\AppNotification;
use App\Models\Cart;
use App\Models\Client;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const SHIPPING_FEE = 2000.00;

    public function index(Request $request): View
    {
        $client = Client::firstOrCreate(['user_id' => $request->user()->id]);

        $orders = $client->orders()
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->client->user_id === $request->user()->id, 403);

        $order->load(['items.product', 'payment', 'delivery']);

        return view('orders.show', [
            'order' => $order,
        ]);
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->client->user_id === $request->user()->id, 403);

        if ($order->status !== 'EN_ATTENTE') {
            return back()->with('status', 'Cette commande ne peut plus être annulée.');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $item->product?->increment('stock', $item->quantity);
            }

            $order->update(['status' => 'ANNULEE']);
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('status', 'Votre commande a été annulée.');
    }

    public function create(Request $request): View
    {
        $client = Client::firstOrCreate(['user_id' => $request->user()->id]);
        $cart = Cart::where('client_id', $client->id)->where('status', 'OUVERT')->first();
        $cart?->load('items.product');

        $subtotal = $cart?->items->sum(fn ($item) => $item->quantity * $item->unit_price) ?? 0;

        return view('orders.create', [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'shippingFee' => self::SHIPPING_FEE,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'street' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        $client = Client::firstOrCreate(['user_id' => $request->user()->id]);
        $cart = Cart::where('client_id', $client->id)->where('status', 'OUVERT')->first();
        $cart?->load('items.product');

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Votre panier est vide.');
        }

        $order = DB::transaction(function () use ($cart, $client, $validated, $request) {
            $subtotal = $cart->items->sum(fn ($item) => $item->quantity * $item->unit_price);
            $shippingFee = self::SHIPPING_FEE;

            $order = Order::create([
                'client_id' => $client->id,
                'order_number' => $this->generateOrderNumber(),
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total' => $subtotal + $shippingFee,
                'status' => 'EN_ATTENTE',
                'shipping_address_snapshot' => $validated,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->quantity * $item->unit_price,
                    'size' => $item->size,
                    'color' => $item->color,
                ]);

                $item->product?->decrement('stock', $item->quantity);
            }

            $cart->update(['status' => 'VALIDE']);

            $this->notifyAdminsOfNewOrder($order, $request->user()->first_name ?? $request->user()->name ?? 'Un client');

            return $order;
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('status', 'Commande passée avec succès !');
    }

    /**
     * Notifie tous les administrateurs qu'une nouvelle commande a été passée.
     */
    private function notifyAdminsOfNewOrder(Order $order, string $clientName): void
    {
        $now = now();

        $rows = Administrator::pluck('user_id')->map(fn ($userId) => [
            'user_id' => $userId,
            'title' => 'Nouvelle commande reçue !',
            'content' => "{$clientName} vient de passer la commande {$order->order_number} pour un total de ".number_format($order->total, 0, ',', ' ').' FCFA.',
            'is_read' => false,
            'sent_at' => $now,
        ])->all();

        if (! empty($rows)) {
            AppNotification::insert($rows);
        }
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'CMD-'.strtoupper(Str::random(8));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}

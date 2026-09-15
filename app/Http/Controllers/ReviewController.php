<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Laisser un avis directement depuis la fiche produit, sans commande liée.
     */
    public function storeForProduct(Request $request, Product $product): RedirectResponse
    {
        $client = Client::firstOrCreate(['user_id' => $request->user()->id]);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $alreadyReviewed = Review::where('client_id', $client->id)
            ->where('product_id', $product->id)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('status', 'Vous avez déjà laissé un avis pour ce produit.');
        }

        Review::create([
            'client_id' => $client->id,
            'product_id' => $product->id,
            'order_id' => null,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'moderation_status' => 'EN_ATTENTE',
        ]);

        return back()->with('status', 'Merci pour votre avis ! Il sera visible après modération.');
    }

    /**
     * Laisser un avis depuis une commande précise (conservé pour compatibilité).
     */
    public function store(Request $request, Order $order, Product $product): RedirectResponse
    {
        abort_unless($order->client->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $alreadyReviewed = Review::where('client_id', $order->client_id)
            ->where('product_id', $product->id)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('status', 'Vous avez déjà laissé un avis pour ce produit.');
        }

        Review::create([
            'client_id' => $order->client_id,
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'moderation_status' => 'EN_ATTENTE',
        ]);

        return back()->with('status', 'Merci pour votre avis ! Il sera visible après modération.');
    }
}

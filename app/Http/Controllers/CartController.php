<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $this->openCartFor($request);
        $cart->load('items.product');

        $subtotal = $cart->items->sum(fn ($item) => $item->quantity * $item->unit_price);
        $shippingFee = (float) config('services.shop.shipping_fee', 2000);

        return view('cart.index', [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'shippingFee' => $shippingFee,
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $sizeRules = [$product->available_sizes ? 'required' : 'nullable', 'string'];
        $colorRules = [$product->available_colors ? 'required' : 'nullable', 'string'];

        if (! empty($product->available_sizes)) {
            $sizeRules[] = Rule::in($product->available_sizes);
        }

        if (! empty($product->available_colors)) {
            $colorRules[] = Rule::in($product->available_colors);
        }

        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.$product->stock],
            'size' => $sizeRules,
            'color' => $colorRules,
        ], [
            'quantity.integer' => 'La quantité doit être un nombre entier.',
            'quantity.min' => 'La quantité doit être au moins égale à 1.',
            'quantity.max' => 'La quantité demandée dépasse le stock disponible.',
            'size.in' => 'La taille sélectionnée n’est plus disponible.',
            'color.in' => 'La couleur sélectionnée n’est plus disponible.',
        ]);

        $quantity = $validated['quantity'] ?? 1;
        $size = $validated['size'] ?? null;
        $color = $validated['color'] ?? null;

        if ($product->status !== 'ACTIF' || $product->stock < 1) {
            return back()->with('status', 'Ce produit n\'est pas disponible actuellement.');
        }

        $cart = $this->openCartFor($request);

        $item = $cart->items()
            ->where('product_id', $product->id)
            ->where('size', $size)
            ->where('color', $color)
            ->first();

        if ($item && $item->quantity + $quantity > $product->stock) {
            return back()
                ->withErrors(['quantity' => 'La quantité demandée dépasse le stock disponible.'])
                ->withInput();
        }

        if ($item) {
            $item->update(['quantity' => $item->quantity + $quantity]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'size' => $size,
                'color' => $color,
            ]);
        }

        return redirect()
            ->route('cart.index')
            ->with('status', 'Produit ajouté au panier.');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $this->authorizeCartItem($request, $cartItem);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:'.($cartItem->product?->stock ?? 0)],
        ], [
            'quantity.integer' => 'La quantité doit être un nombre entier.',
            'quantity.min' => 'La quantité doit être au moins égale à 1.',
            'quantity.max' => 'La quantité demandée dépasse le stock disponible.',
        ]);

        $cartItem->update(['quantity' => $validated['quantity']]);

        return redirect()->route('cart.index')->with('status', 'Panier mis à jour.');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        $this->authorizeCartItem($request, $cartItem);

        $cartItem->delete();

        return redirect()->route('cart.index')->with('status', 'Produit retiré du panier.');
    }

    private function openCartFor(Request $request): Cart
    {
        $client = Client::firstOrCreate(['user_id' => $request->user()->id]);

        return Cart::firstOrCreate(
            ['client_id' => $client->id, 'status' => 'OUVERT']
        );
    }

    private function authorizeCartItem(Request $request, CartItem $cartItem): void
    {
        abort_unless(
            $cartItem->cart->client->user_id === $request->user()->id,
            403
        );
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Administrator;
use App\Models\AppNotification;
use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.products.index', [
            'products' => $products,
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);

        $slug = $this->uniqueSlug($validated['name']);

        $administrator = Administrator::firstOrCreate(['user_id' => $request->user()->id]);

        $product = Product::create([
            ...collect($validated)->except(['images', 'available_sizes', 'available_colors'])->toArray(),
            'slug' => $slug,
            'created_by_admin_id' => $administrator->id,
            'status' => $validated['status'] ?? 'ACTIF',
            'available_sizes' => $this->parseListInput($validated['available_sizes'] ?? null),
            'available_colors' => $this->parseListInput($validated['available_colors'] ?? null),
        ]);

        $this->storeUploadedImages($request, $product);

        if ($product->status === 'ACTIF') {
            $this->notifyClientsOfNewProduct($product);
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', 'Produit créé avec succès. Vous pouvez continuer à ajouter des photos.');
    }

    public function edit(Product $product): View
    {
        $product->load('images');

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $wasActive = $product->status === 'ACTIF';

        $validated = $this->validateProduct($request);

        $fields = collect($validated)->except(['images', 'available_sizes', 'available_colors'])->toArray();
        $fields['available_sizes'] = $this->parseListInput($validated['available_sizes'] ?? null);
        $fields['available_colors'] = $this->parseListInput($validated['available_colors'] ?? null);

        if ($fields['name'] !== $product->name) {
            $fields['slug'] = $this->uniqueSlug($fields['name'], $product->id);
        }

        $product->update($fields);

        $this->storeUploadedImages($request, $product);

        // Si le produit vient tout juste de passer à ACTIF (n'était pas actif avant), on notifie les clients
        if (! $wasActive && $product->status === 'ACTIF') {
            $this->notifyClientsOfNewProduct($product);
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', 'Produit mis à jour avec succès.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orderItems()->exists()) {
            return redirect()
                ->route('admin.products.index')
                ->with('status', 'Impossible de supprimer : ce produit fait déjà partie de commandes existantes. Passez-le en statut "Inactif" ou "Épuisé" à la place, pour conserver l\'historique.');
        }

        // Le produit n'a jamais été commandé : on peut nettoyer ce qui y fait encore référence
        $product->cartItems()->delete();
        $product->reviews()->delete();

        foreach ($product->images as $image) {
            $this->deleteImageFile($image);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produit supprimé.');
    }

    public function destroyImage(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        $wasPrimary = $image->is_primary;
        $this->deleteImageFile($image);
        $image->delete();

        if ($wasPrimary) {
            $product->images()->first()?->update(['is_primary' => true]);
        }

        return back()->with('status', 'Photo supprimée.');
    }

    public function makeImagePrimary(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('status', 'Photo principale mise à jour.');
    }

    /**
     * Notifie tous les clients qu'un nouveau produit est disponible.
     */
    private function notifyClientsOfNewProduct(Product $product): void
    {
        $now = now();

        $rows = Client::pluck('user_id')->map(fn ($userId) => [
            'user_id' => $userId,
            'title' => 'Nouveau produit disponible !',
            'content' => "Découvrez « {$product->name} », maintenant disponible sur Mosnoky.",
            'is_read' => false,
            'sent_at' => $now,
        ])->all();

        if (! empty($rows)) {
            AppNotification::insert($rows);
        }
    }

    private function storeUploadedImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $hasExistingImages = $product->images()->exists();
        $photoColor = $request->input('photo_color') ?: null;

        foreach ($request->file('images') as $index => $file) {
            $path = $file->store('products', 'public');

            $product->images()->create([
                'image_url' => Storage::url($path),
                'alternative_text' => $product->name,
                'color' => $photoColor,
                'is_primary' => ! $hasExistingImages && $index === 0,
            ]);
        }
    }

    public function updateImageColor(Request $request, Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        $validated = $request->validate([
            'color' => ['nullable', 'string', 'max:100'],
        ]);

        $image->update(['color' => $validated['color'] ?: null]);

        return back()->with('status', 'Couleur de la photo mise à jour.');
    }

    private function deleteImageFile(ProductImage $image): void
    {
        if (str_starts_with($image->image_url, '/storage/')) {
            $relativePath = str_replace('/storage/', '', $image->image_url);
            Storage::disk('public')->delete($relativePath);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'alert_threshold' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'in:ACTIF,INACTIF,EPUISE'],
            'available_sizes' => ['nullable', 'string', 'max:500'],
            'available_colors' => ['nullable', 'string', 'max:500'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
        ]);
    }

    /**
     * Transforme une liste saisie en texte libre ("39, 40, 41") en tableau propre.
     */
    private function parseListInput(?string $input): ?array
    {
        if (blank($input)) {
            return null;
        }

        $items = array_map('trim', explode(',', $input));
        $items = array_filter($items, fn ($item) => $item !== '');

        return array_values($items) ?: null;
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            Product::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }
}

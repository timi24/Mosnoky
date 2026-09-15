<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Administrator;
use App\Models\Category;
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
            ...collect($validated)->except('images')->toArray(),
            'slug' => $slug,
            'created_by_admin_id' => $administrator->id,
            'status' => $validated['status'] ?? 'ACTIF',
        ]);

        $this->storeUploadedImages($request, $product);

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
        $validated = $this->validateProduct($request);

        $fields = collect($validated)->except('images')->toArray();

        if ($fields['name'] !== $product->name) {
            $fields['slug'] = $this->uniqueSlug($fields['name'], $product->id);
        }

        $product->update($fields);

        $this->storeUploadedImages($request, $product);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', 'Produit mis à jour avec succès.');
    }

    public function destroy(Product $product): RedirectResponse
    {
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

        // Si l'image supprimée était la principale, on en désigne une autre automatiquement
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

    private function storeUploadedImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $hasExistingImages = $product->images()->exists();

        foreach ($request->file('images') as $index => $file) {
            $path = $file->store('products', 'public');

            $product->images()->create([
                'image_url' => Storage::url($path),
                'alternative_text' => $product->name,
                'is_primary' => ! $hasExistingImages && $index === 0,
            ]);
        }
    }

    private function deleteImageFile(ProductImage $image): void
    {
        // On ne supprime le fichier physique que s'il est bien stocké localement (storage/app/public)
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
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
        ]);
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

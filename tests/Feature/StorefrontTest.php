<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

test('the storefront is available to visitors', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Nos produits');
});

test('guests are redirected to login when they want to buy a product', function () {
    $categoryId = DB::table('categories')->insertGetId([
        'name' => 'Test',
        'is_active' => true,
    ]);
    $user = User::factory()->create();
    $administratorId = DB::table('administrators')->insertGetId([
        'user_id' => $user->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $productId = DB::table('products')->insertGetId([
        'category_id' => $categoryId,
        'created_by_admin_id' => $administratorId,
        'name' => 'Produit test',
        'slug' => 'produit-test',
        'description' => 'Description test',
        'price' => 19.90,
        'stock' => 3,
        'alert_threshold' => 1,
        'status' => 'ACTIF',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->get(route('products.purchase', $productId))
        ->assertRedirect(route('login'))
        ->assertSessionHas('url.intended', route('products.purchase', $productId));
});

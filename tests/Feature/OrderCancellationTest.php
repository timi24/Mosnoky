<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->client = User::factory()->create();
    $this->admin = User::factory()->create(['role' => 'ADMINISTRATEUR']);

    $clientId = DB::table('clients')->insertGetId([
        'user_id' => $this->client->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $administratorId = DB::table('administrators')->insertGetId([
        'user_id' => $this->admin->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $categoryId = DB::table('categories')->insertGetId([
        'name' => 'Chaussures',
        'is_active' => true,
    ]);
    $this->productId = DB::table('products')->insertGetId([
        'category_id' => $categoryId,
        'created_by_admin_id' => $administratorId,
        'name' => 'Mocassin test',
        'slug' => 'mocassin-test',
        'description' => 'Produit de test',
        'price' => 500,
        'stock' => 3,
        'alert_threshold' => 1,
        'status' => 'ACTIF',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $this->orderId = DB::table('orders')->insertGetId([
        'client_id' => $clientId,
        'order_number' => 'CMD-TEST-12345678',
        'subtotal' => 1000,
        'shipping_fee' => 500,
        'total' => 1500,
        'status' => 'EN_ATTENTE',
        'shipping_address_snapshot' => json_encode([]),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    DB::table('order_items')->insert([
        'order_id' => $this->orderId,
        'product_id' => $this->productId,
        'quantity' => 2,
        'unit_price' => 500,
        'subtotal' => 1000,
        'size' => null,
        'color' => null,
        'created_at' => now(),
    ]);
});

test('clients can cancel their pending orders from the order list and stock is restored', function () {
    $this->actingAs($this->client)
        ->get(route('orders.index'))
        ->assertOk()
        ->assertSee('Annuler');

    $this->actingAs($this->client)
        ->patch(route('orders.cancel', $this->orderId))
        ->assertRedirect(route('orders.show', $this->orderId))
        ->assertSessionHas('status', 'Votre commande a été annulée.');

    $this->assertDatabaseHas('orders', [
        'id' => $this->orderId,
        'status' => 'ANNULEE',
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $this->productId,
        'stock' => 5,
    ]);
});

test('clients cannot cancel another clients order', function () {
    $otherClient = User::factory()->create();

    $this->actingAs($otherClient)
        ->patch(route('orders.cancel', $this->orderId))
        ->assertForbidden();

    $this->assertDatabaseHas('orders', [
        'id' => $this->orderId,
        'status' => 'EN_ATTENTE',
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $this->productId,
        'stock' => 3,
    ]);
});

test('clients cannot cancel an order after declaring a payment', function () {
    DB::table('payments')->insert([
        'order_id' => $this->orderId,
        'reference' => 'PAY-TEST-12345678',
        'amount' => 1500,
        'payment_method' => 'ORANGE_MONEY',
        'status' => 'EN_ATTENTE',
        'submitted_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($this->client)
        ->get(route('orders.index'))
        ->assertOk()
        ->assertDontSee('Annuler</button>', false);

    $this->actingAs($this->client)
        ->patch(route('orders.cancel', $this->orderId))
        ->assertRedirect()
        ->assertSessionHas('status');

    $this->assertDatabaseHas('orders', [
        'id' => $this->orderId,
        'status' => 'EN_ATTENTE',
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $this->productId,
        'stock' => 3,
    ]);
});
test('clients cannot cancel an order after it has been confirmed', function () {
    DB::table('orders')->where('id', $this->orderId)->update(['status' => 'CONFIRMEE']);

    $this->actingAs($this->client)
        ->get(route('orders.show', $this->orderId))
        ->assertOk()
        ->assertDontSee('Annuler la commande');

    $this->actingAs($this->client)
        ->patch(route('orders.cancel', $this->orderId))
        ->assertRedirect()
        ->assertSessionHas('status');

    $this->assertDatabaseHas('products', [
        'id' => $this->productId,
        'stock' => 3,
    ]);
});

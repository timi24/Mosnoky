<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->unique(['cart_id', 'product_id'], 'uq_cart_product');

            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });

        DB::statement('ALTER TABLE cart_items ADD CONSTRAINT chk_cart_quantity CHECK (quantity > 0)');
        DB::statement('ALTER TABLE cart_items ADD CONSTRAINT chk_cart_price CHECK (unit_price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};

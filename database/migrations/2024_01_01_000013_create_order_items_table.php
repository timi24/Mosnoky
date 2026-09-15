<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });

        DB::statement('ALTER TABLE order_items ADD CONSTRAINT chk_order_item_quantity CHECK (quantity > 0)');
        DB::statement('ALTER TABLE order_items ADD CONSTRAINT chk_order_item_price CHECK (unit_price >= 0)');
        DB::statement('ALTER TABLE order_items ADD CONSTRAINT chk_order_item_subtotal CHECK (subtotal = quantity * unit_price)');
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

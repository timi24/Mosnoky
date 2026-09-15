<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->string('order_number', 50)->unique();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('status', [
                'EN_ATTENTE', 'CONFIRMEE', 'EN_PREPARATION', 'EXPEDIEE', 'LIVREE', 'ANNULEE',
            ])->default('EN_ATTENTE');
            $table->json('shipping_address_snapshot');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('restrict');
        });

        DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_order_subtotal CHECK (subtotal >= 0)');
        DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_order_shipping_fee CHECK (shipping_fee >= 0)');
        DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_order_total CHECK (total >= 0)');
        DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_order_total_calculation CHECK (total = subtotal + shipping_fee)');
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

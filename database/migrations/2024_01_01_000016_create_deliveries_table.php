<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->unique();
            $table->string('tracking_number', 150)->unique();
            $table->string('carrier', 150)->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('expected_delivery_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->enum('status', [
                'EN_PREPARATION', 'EXPEDIEE', 'EN_TRANSIT', 'LIVREE', 'RETARDEE', 'ECHEC',
            ])->default('EN_PREPARATION');
            $table->text('tracking_comment')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};

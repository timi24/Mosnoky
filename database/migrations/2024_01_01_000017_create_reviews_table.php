<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->enum('moderation_status', ['EN_ATTENTE', 'APPROUVE', 'MASQUE'])->default('EN_ATTENTE');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->unique(['client_id', 'product_id', 'order_id'], 'uq_client_product_order');

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('restrict');
        });

        DB::statement('ALTER TABLE reviews ADD CONSTRAINT chk_review_rating CHECK (rating BETWEEN 1 AND 5)');
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

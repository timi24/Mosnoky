<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. On rend order_id optionnel (un avis n'a plus besoin d'être lié à une commande précise)
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->change();
        });

        // 2. On remplace la contrainte d'unicité : un avis unique par client + produit (peu importe la commande)
        try {
            DB::statement('ALTER TABLE reviews DROP INDEX uq_client_product_order');
        } catch (\Throwable $e) {
            // L'index n'existe peut-être pas sous ce nom exact, on continue sans bloquer
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->unique(['client_id', 'product_id'], 'uq_client_product');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('uq_client_product');
            $table->unsignedBigInteger('order_id')->nullable(false)->change();
        });
    }
};

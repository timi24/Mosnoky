<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('available_sizes')->nullable()->after('status');
            $table->json('available_colors')->nullable()->after('available_sizes');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('size')->nullable()->after('quantity');
            $table->string('color')->nullable()->after('size');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('size')->nullable()->after('quantity');
            $table->string('color')->nullable()->after('size');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['available_sizes', 'available_colors']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['size', 'color']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['size', 'color']);
        });
    }
};

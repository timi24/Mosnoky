<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('status', 'idx_products_status');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('status', 'idx_orders_status');
            $table->index('created_at', 'idx_orders_date');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->index('status', 'idx_deliveries_status');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->index('status', 'idx_reports_status');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_status');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_date');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropIndex('idx_deliveries_status');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('idx_reports_status');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE reviews ALTER COLUMN order_id DROP NOT NULL');
        DB::statement('ALTER TABLE reviews DROP CONSTRAINT IF EXISTS uq_client_product_order');
        DB::statement('DROP INDEX IF EXISTS uq_client_product_order');
        DB::statement('ALTER TABLE reviews ADD CONSTRAINT uq_client_product UNIQUE (client_id, product_id)');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE reviews DROP CONSTRAINT IF EXISTS uq_client_product');
        DB::statement('ALTER TABLE reviews ALTER COLUMN order_id SET NOT NULL');
        DB::statement('ALTER TABLE reviews ADD CONSTRAINT uq_client_product_order UNIQUE (client_id, product_id, order_id)');
    }
};

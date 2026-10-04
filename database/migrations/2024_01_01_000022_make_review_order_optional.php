<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rendre order_id nullable directement en PostgreSQL
        DB::statement('
            ALTER TABLE reviews
            ALTER COLUMN order_id DROP NOT NULL
        ');

        // 2. Supprimer l''ancienne contrainte UNIQUE si elle existe
        DB::statement('
            ALTER TABLE reviews
            DROP CONSTRAINT IF EXISTS uq_client_product_order
        ');

        // 3. Supprimer l''ancien index s''il existe encore
        DB::statement('
            DROP INDEX IF EXISTS uq_client_product_order
        ');

        // 4. Créer la nouvelle contrainte UNIQUE
        DB::statement('
            ALTER TABLE reviews
            ADD CONSTRAINT uq_client_product
            UNIQUE (client_id, product_id)
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE reviews
            DROP CONSTRAINT IF EXISTS uq_client_product
        ');

        DB::statement('
            ALTER TABLE reviews
            ALTER COLUMN order_id SET NOT NULL
        ');

        DB::statement('
            ALTER TABLE reviews
            ADD CONSTRAINT uq_client_product_order
            UNIQUE (client_id, product_id, order_id)
        ');
    }
};
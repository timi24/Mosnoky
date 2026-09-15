<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('created_by_admin_id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('alert_threshold')->default(0);
            $table->enum('status', ['ACTIF', 'INACTIF', 'EPUISE'])->default('ACTIF');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
            $table->foreign('created_by_admin_id')->references('id')->on('administrators')->onDelete('restrict');
        });

        DB::statement('ALTER TABLE products ADD CONSTRAINT chk_product_price CHECK (price >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

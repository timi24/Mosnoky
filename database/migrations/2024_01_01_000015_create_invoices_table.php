<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->unique();
            $table->string('invoice_number', 100)->unique();
            $table->timestamp('issued_at')->useCurrent();
            $table->decimal('total_amount', 12, 2);

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE invoices ADD CONSTRAINT chk_invoice_amount CHECK (total_amount >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

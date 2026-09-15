<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->unique();
            $table->string('reference', 150)->unique();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', [
                'ORANGE_MONEY', 'MOOV_MONEY', 'CARTE_BANCAIRE', 'VIREMENT',
            ]);
            $table->enum('status', ['EN_ATTENTE', 'ACCEPTE', 'REFUSE', 'REMBOURSE'])->default('EN_ATTENTE');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE payments ADD CONSTRAINT chk_payment_amount CHECK (amount >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

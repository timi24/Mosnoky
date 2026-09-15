<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('review_id')->nullable();
            $table->enum('report_type', [
                'PROBLEME_LIVRAISON', 'PRODUIT_DEFECTUEUX', 'COMMENTAIRE_INAPPROPRIE', 'AUTRE',
            ]);
            $table->text('description');
            $table->enum('status', ['OUVERT', 'EN_COURS', 'RESOLU', 'REJETE'])->default('OUVERT');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Numéro de téléphone depuis lequel le client a payé
            $table->string('customer_phone', 20)->nullable();
            // Référence de transaction reçue par SMS : unique, donc une même
            // référence ne peut jamais servir à valider deux commandes.
            $table->string('customer_reference', 40)->nullable()->unique();
            // Date à laquelle le client a déclaré son paiement (vide = pas encore déclaré)
            $table->timestamp('submitted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['customer_reference']);
            $table->dropColumn(['customer_phone', 'customer_reference', 'submitted_at']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->integer('numero_echeance'); // 1, 2, 3 ... N
            $table->date('date_echeance'); // Date limite de versement
            $table->integer('montant_du');
            $table->integer('montant_paye')->default(0);
            $table->enum('statut', ['a_payer', 'en_retard', 'paye', 'partiellement_paye'])->default('a_payer');
            $table->timestamp('date_paiement')->nullable();
            $table->string('recu_code', 10)->nullable(); // Code de quittance rattaché
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};

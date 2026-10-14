<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('numero_commande')->unique(); // ex: CP-CMD-2026-0042
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('formula_id')->nullable()->constrained('installment_formulas')->onDelete('set null');
            $table->integer('montant_total'); // Montant total de la commande
            $table->integer('apport_initial')->default(0); // Montant de l'apport payé à la commande
            $table->integer('montant_finance')->default(0); // Reste à payer échelonné
            $table->integer('montant_echeance')->default(0); // Montant de chaque mensualité/journée
            $table->integer('nombre_echeances')->default(1);
            $table->enum('type_paiement', ['cash', 'credit'])->default('credit');
            $table->enum('statut', ['en_attente_kyc', 'validee', 'en_cours_livraison', 'livree', 'soldee', 'annulee'])->default('en_attente_kyc');
            $table->enum('canal_paiement', ['wave', 'orange_money', 'mtn_momo', 'moov_money', 'cash', 'carte'])->default('wave');
            $table->string('adresse_livraison');
            $table->string('ville_livraison')->default('Abidjan');
            $table->string('telephone_contact');
            $table->foreignId('commercial_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installment_formulas', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // ex: "30 jours", "3 mois", "6 mois", "12 mois", "24 mois", "Paiement Cash"
            $table->enum('frequence', ['cash', 'daily', 'weekly', 'monthly'])->default('monthly');
            $table->integer('nombre_echeances')->default(1);
            $table->decimal('apport_min_pct', 5, 2)->default(0.00); // Pourcentage d'apport initial obligatoire (ex: 0.00%)
            $table->integer('montant_min')->default(20000); // Plafond min
            $table->integer('montant_max')->default(3000000); // Plafond max
            $table->integer('remise_cash_pct')->default(0); // 7% pour formule cash
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_formulas');
    }
};

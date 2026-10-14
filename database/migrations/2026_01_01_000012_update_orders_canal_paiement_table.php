<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modifier canal_paiement pour être un string afin de supporter tous les canaux (wave, orange_money, mtn_momo, moov_money, cash, carte, paypal, cash_delivery, etc.)
        Schema::table('orders', function (Blueprint $table) {
            $table->string('canal_paiement', 50)->default('wave')->change();
        });
    }

    public function down(): void
    {
        // En cas de rollback
    }
};

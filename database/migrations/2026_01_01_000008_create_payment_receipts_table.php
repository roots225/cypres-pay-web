<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('code_recu', 8)->unique(); // Code alphanumérique à 6 caractères, ex: "28B7S0"
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('installment_id')->nullable()->constrained('installments')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Client
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('set null'); // Caissier ou commercial ayant encaissé
            $table->integer('montant');
            $table->enum('canal', ['wave', 'orange_money', 'mtn_momo', 'moov_money', 'cash']);
            $table->string('reference_externe')->nullable(); // ID transaction Wave ou Orange
            $table->timestamp('date_versement');
            $table->boolean('est_valide')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};

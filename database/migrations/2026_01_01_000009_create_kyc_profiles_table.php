<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kyc_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->enum('type_piece', ['cni', 'passeport', 'attestation', 'permis'])->default('cni');
            $table->string('numero_piece')->nullable();
            $table->text('photo_piece_recto')->nullable();
            $table->text('photo_piece_verso')->nullable();
            $table->text('photo_facture_cie_sodeci')->nullable(); // Justificatif de domicile
            $table->longText('signature_electronique')->nullable(); // Base64 exportée du Canvas React
            $table->enum('statut', ['non_soumis', 'en_attente', 'valide', 'rejete'])->default('non_soumis');
            $table->text('motif_rejet')->nullable();
            $table->timestamp('valide_le')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_profiles');
    }
};

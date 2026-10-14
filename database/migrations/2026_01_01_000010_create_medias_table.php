<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name'); // Nom lisible / titre du média
            $table->string('file_name', 500); // Nom physique du fichier sur le disque
            $table->string('original_name', 500)->nullable(); // Nom d'origine lors de l'upload
            $table->string('disk')->default('public');
            $table->text('path'); // Chemin relatif sur le disque ou URL externe
            $table->string('mime_type'); // Ex: image/jpeg, application/pdf, video/mp4
            $table->string('extension', 20)->nullable(); // Ex: jpg, png, pdf, mp4
            $table->string('type', 30)->default('other'); // image, document, video, audio, archive, other
            $table->unsignedBigInteger('size')->default(0); // Taille en octets
            $table->unsignedInteger('width')->nullable(); // Pour les images
            $table->unsignedInteger('height')->nullable(); // Pour les images
            $table->string('alt')->nullable(); // Texte alternatif SEO / Accessibilité
            $table->text('caption')->nullable(); // Légende ou description optionnelle
            $table->string('folder')->default('general')->index(); // products, kyc, receipts, general, etc.
            $table->json('metadata')->nullable(); // Informations complémentaires (ex: crop info, exif)
            $table->timestamps();

            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medias');
    }
};

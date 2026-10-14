<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // ex: N°019, N°731, N°717
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('marque')->nullable();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->integer('prix_vente'); // Prix standard en FCFA (ex: 156000)
            $table->integer('prix_cash')->nullable(); // Prix remisé (-7% cash, ex: 145080)
            $table->integer('mensualite_indicative')->nullable(); // Mensualité dès X FCFA/m
            $table->integer('stock')->default(10);
            $table->text('image_principale');
            $table->json('galerie_images')->nullable();
            $table->text('description_courte')->nullable();
            $table->longText('description_complete')->nullable();
            $table->json('caracteristiques')->nullable(); // Spécifications techniques
            $table->boolean('is_kit')->default(false); // Kit Maison, Pack Cuisine, etc.
            $table->boolean('is_flash')->default(false); // Produit Vente Flash
            $table->boolean('is_vedette')->default(false);
            $table->boolean('en_stock')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

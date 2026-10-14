<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('prenom');
            $table->string('nom');
            $table->string('email')->unique();
            $table->string('telephone', 15)->unique(); // Format Côte d'Ivoire: 01/05/07XXXXXXXX
            $table->string('ville')->default('Abidjan');
            $table->string('commune')->nullable();
            $table->string('adresse')->nullable();
            $table->string('role')->default('client'); // client, commercial, caissier, admin
            $table->string('ref_code')->nullable(); // Code apporteur d'affaires / commercial rattaché
            $table->string('own_referral_code')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_token', 64)->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('status', ['active', 'abandoned', 'converted', 'cancelled'])->default('active')->index();
            $table->enum('step', ['cart', 'address', 'shipping', 'payment', 'completed'])->default('cart')->index();
            $table->foreignId('formula_id')->nullable()->constrained('installment_formulas')->onDelete('set null');
            $table->enum('type_achat', ['cash', 'credit'])->default('credit');
            
            // Coordonnées client saisies lors du tunnel
            $table->string('customer_firstname')->nullable();
            $table->string('customer_lastname')->nullable();
            $table->string('customer_phone')->nullable()->index();
            $table->string('customer_email')->nullable();
            
            // Livraison
            $table->string('shipping_city')->nullable();
            $table->string('shipping_commune')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_method')->nullable(); // express_abidjan, interieur_ci, retrait_agence
            $table->integer('shipping_cost')->default(0);
            
            // Paiement
            $table->string('payment_method')->nullable(); // wave, orange_money, mtn_momo, moov_money, cash_delivery, card, paypal, bnpl
            
            // Montants financiers
            $table->integer('subtotal')->default(0);
            $table->integer('discount_amount')->default(0);
            $table->integer('total')->default(0);
            $table->integer('monthly_amount')->default(0);
            $table->integer('downpayment_amount')->default(0);
            $table->integer('items_count')->default(0);
            
            // Notes & métadonnées (IP, signature numérique, User Agent, etc.)
            $table->text('customer_notes')->nullable();
            $table->json('metadata')->nullable();
            
            // Horodatages spécifiques de suivi
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->timestamp('abandoned_at')->nullable()->index();
            $table->timestamp('converted_at')->nullable();
            $table->timestamp('recovery_wa_sent_at')->nullable();
            $table->timestamp('recovery_email_sent_at')->nullable();
            
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantite')->default(1);
            $table->integer('prix_unitaire')->default(0);
            $table->integer('mensualite_unitaire')->default(0);
            $table->json('options')->nullable();
            $table->timestamps();
        });

        // Ajouter cart_id sur orders si absent
        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'cart_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('cart_id')->nullable()->after('user_id')->constrained('carts')->onDelete('set null');
                $table->string('mode_livraison')->nullable()->after('canal_paiement');
                $table->integer('frais_livraison')->default(0)->after('mode_livraison');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'cart_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['cart_id']);
                $table->dropColumn(['cart_id', 'mode_livraison', 'frais_livraison']);
            });
        }
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};

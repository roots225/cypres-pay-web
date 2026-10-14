<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_token',
        'user_id',
        'status',
        'step',
        'formula_id',
        'type_achat',
        'customer_firstname',
        'customer_lastname',
        'customer_phone',
        'customer_email',
        'shipping_city',
        'shipping_commune',
        'shipping_address',
        'shipping_method',
        'shipping_cost',
        'payment_method',
        'subtotal',
        'discount_amount',
        'total',
        'monthly_amount',
        'downpayment_amount',
        'items_count',
        'customer_notes',
        'metadata',
        'last_activity_at',
        'abandoned_at',
        'converted_at',
        'recovery_wa_sent_at',
        'recovery_email_sent_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_activity_at' => 'datetime',
        'abandoned_at' => 'datetime',
        'converted_at' => 'datetime',
        'recovery_wa_sent_at' => 'datetime',
        'recovery_email_sent_at' => 'datetime',
        'subtotal' => 'integer',
        'discount_amount' => 'integer',
        'total' => 'integer',
        'monthly_amount' => 'integer',
        'downpayment_amount' => 'integer',
        'shipping_cost' => 'integer',
        'items_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function formula()
    {
        return $this->belongsTo(InstallmentFormula::class, 'formula_id');
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'cart_id');
    }

    // ── Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAbandoned($query)
    {
        // Considéré comme abandonné si statut 'abandoned' OU statut 'active' sans conversion depuis plus de 15 minutes avec au moins 1 article
        return $query->where(function ($q) {
            $q->where('status', 'abandoned')
              ->orWhere(function ($sub) {
                  $sub->where('status', 'active')
                      ->where('items_count', '>', 0)
                      ->where('last_activity_at', '<', Carbon::now()->subMinutes(15));
              });
        });
    }

    public function scopeConverted($query)
    {
        return $query->where('status', 'converted');
    }

    // ── Recalcul des totaux
    public function recalculateTotals(): self
    {
        $this->load('items.product');

        $subtotal = 0;
        $monthly = 0;
        $itemsCount = 0;

        foreach ($this->items as $item) {
            $subtotal += ($item->prix_unitaire * $item->quantite);
            $monthly += ($item->mensualite_unitaire * $item->quantite);
            $itemsCount += $item->quantite;
        }

        $this->subtotal = $subtotal;
        $this->items_count = $itemsCount;

        // Calcul remise si achat comptant (-7%)
        if ($this->type_achat === 'cash') {
            $this->discount_amount = (int) round($subtotal * 0.07);
        } else {
            $this->discount_amount = 0;
        }

        // Frais de livraison
        $shippingCost = (int) ($this->shipping_cost ?? 0);

        $this->total = max(0, $subtotal - $this->discount_amount + $shippingCost);
        $this->monthly_amount = $monthly;

        // Apport initial
        if ($this->type_achat === 'cash') {
            $this->downpayment_amount = $this->total;
        } else {
            $pct = $this->formula ? $this->formula->pourcentage_apport : 0;
            $this->downpayment_amount = (int) round(($subtotal * $pct) / 100);
        }

        $this->last_activity_at = Carbon::now();
        $this->save();

        return $this;
    }

    // ── Accesseurs et aides
    public function getNomClientCompletAttribute(): string
    {
        $nomComplet = trim(($this->customer_firstname ?? '') . ' ' . ($this->customer_lastname ?? ''));
        if (!empty($nomComplet)) {
            return $nomComplet;
        }
        if ($this->user) {
            return $this->user->nom_complet;
        }
        return 'Visiteur Invité';
    }

    public function getTelephoneClientAttribute(): ?string
    {
        return $this->customer_phone ?: ($this->user ? $this->user->telephone : null);
    }

    public function getEmailClientAttribute(): ?string
    {
        return $this->customer_email ?: ($this->user ? $this->user->email : null);
    }

    public function getResumeUrlAttribute(): string
    {
        return route('cart.resume', ['token' => $this->session_token]);
    }

    public function getWhatsAppRecoveryUrlAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', (string) $this->telephone_client);
        if (!$phone) {
            return '#';
        }
        // Préfixer par 225 si 10 chiffres (standard ivoirien)
        if (strlen($phone) === 10) {
            $phone = '225' . $phone;
        }

        $prenom = $this->customer_firstname ?: 'Cher client';
        $totalFmt = number_format($this->total, 0, ',', ' ');
        $lien = $this->resume_url;

        $articles = $this->items->take(2)->map(fn($it) => $it->product ? $it->product->nom : 'Article')->implode(', ');
        if ($this->items->count() > 2) {
            $articles .= ' (+ ' . ($this->items->count() - 2) . ' autre(s))';
        }

        $message = "Bonjour {$prenom} 👋 !\nC'est Cypres Pay Côte d'Ivoire. Nous avons remarqué que votre commande ({$articles}) d'un montant de {$totalFmt} FCFA est en attente de finalisation.\n\n👉 Reprenez votre commande en 1 clic ici : {$lien}\n\nBesoin d'aide ou d'une facilité de paiement ? Répondez-nous directement !";

        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }

    public function getStepLabelAttribute(): string
    {
        return match ($this->step) {
            'cart' => '1. Panier & Offre',
            'address' => '2. Coordonnées & Livraison',
            'shipping' => '3. Mode d\'expédition',
            'payment' => '4. Moyen de paiement',
            'completed' => '5. Commande finalisée',
            default => 'Panier en cours',
        };
    }

    public function getShippingMethodLabelAttribute(): string
    {
        return match ($this->shipping_method) {
            'express_abidjan' => 'Express Abidjan (24h/48h)',
            'interieur_ci' => 'Expédition Intérieur CI',
            'retrait_agence' => 'Retrait Gratuit Agence',
            default => 'Non défini',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'wave' => 'Wave Mobile Money',
            'orange_money' => 'Orange Money CI',
            'mtn_momo' => 'MTN Mobile Money',
            'moov_money' => 'Moov Money CI',
            'cash_delivery' => 'Paiement Cash à la livraison',
            'card' => 'Carte Bancaire Visa / Mastercard',
            'paypal' => 'PayPal',
            'bnpl' => 'Financement BNPL Cypress Pay',
            default => 'En attente de sélection',
        };
    }
}

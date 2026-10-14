<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_commande',
        'user_id',
        'cart_id',
        'formula_id',
        'montant_total',
        'apport_initial',
        'montant_finance',
        'montant_echeance',
        'nombre_echeances',
        'type_paiement',
        'statut',
        'canal_paiement',
        'mode_livraison',
        'frais_livraison',
        'adresse_livraison',
        'ville_livraison',
        'telephone_contact',
        'commercial_id',
        'notes',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formula()
    {
        return $this->belongsTo(InstallmentFormula::class, 'formula_id');
    }

    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function receipts()
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    public function getMontantTotalFormateAttribute(): string
    {
        return number_format($this->montant_total, 0, ',', ' ') . ' FCFA';
    }

    public function getMontantEcheanceFormateAttribute(): string
    {
        return number_format($this->montant_echeance, 0, ',', ' ') . ' FCFA';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'numero_echeance',
        'date_echeance',
        'montant_du',
        'montant_paye',
        'statut',
        'date_paiement',
        'recu_code',
    ];

    protected $casts = [
        'date_echeance' => 'date',
        'date_paiement' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function receipt()
    {
        return $this->hasOne(PaymentReceipt::class, 'installment_id');
    }

    public function estSoldee(): bool
    {
        return $this->statut === 'paye';
    }
}

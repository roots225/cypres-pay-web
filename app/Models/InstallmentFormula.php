<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentFormula extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'frequence',
        'nombre_echeances',
        'apport_min_pct',
        'montant_min',
        'montant_max',
        'remise_cash_pct',
        'actif',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'formula_id');
    }

    public function getLabelFrequenceAttribute(): string
    {
        return match ($this->frequence) {
            'daily' => '/jour',
            'weekly' => '/semaine',
            'monthly' => '/mois',
            'cash' => 'en 1 fois',
            default => '',
        };
    }
}

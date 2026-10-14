<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantite',
        'prix_unitaire',
        'mensualite_unitaire',
        'options',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'prix_unitaire' => 'integer',
        'mensualite_unitaire' => 'integer',
        'options' => 'array',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalPrixAttribute(): int
    {
        return $this->prix_unitaire * $this->quantite;
    }

    public function getTotalMensualiteAttribute(): int
    {
        return $this->mensualite_unitaire * $this->quantite;
    }
}

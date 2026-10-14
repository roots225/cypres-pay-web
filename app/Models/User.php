<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'prenom',
        'nom',
        'email',
        'telephone',
        'ville',
        'commune',
        'adresse',
        'role',
        'ref_code',
        'own_referral_code',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function kycProfile()
    {
        return $this->hasOne(KycProfile::class);
    }

    public function paymentReceipts()
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isCommercial(): bool
    {
        return $this->role === 'commercial';
    }

    public function isCaissier(): bool
    {
        return $this->role === 'caissier';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}

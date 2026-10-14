<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type_piece',
        'numero_piece',
        'photo_piece_recto',
        'photo_piece_verso',
        'photo_facture_cie_sodeci',
        'signature_electronique',
        'statut',
        'motif_rejet',
        'valide_le',
    ];

    protected $casts = [
        'valide_le' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estValide(): bool
    {
        return $this->statut === 'valide';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'nom',
        'slug',
        'marque',
        'category_id',
        'prix_vente',
        'prix_cash',
        'mensualite_indicative',
        'stock',
        'image_principale',
        'galerie_images',
        'description_courte',
        'description_complete',
        'caracteristiques',
        'is_kit',
        'is_flash',
        'is_vedette',
        'en_stock',
    ];

    protected $casts = [
        'galerie_images' => 'array',
        'caracteristiques' => 'array',
        'is_kit' => 'boolean',
        'is_flash' => 'boolean',
        'is_vedette' => 'boolean',
        'en_stock' => 'boolean',
    ];

    protected $appends = [
        'image_url',
        'all_images',
        'secondary_images',
        'has_multiple_images',
        'images_count',
        'prix_formate',
        'prix_cash_formate',
        'mensualite_formatee',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * URL de l'image principale (avec repli sur la première de la galerie ou un placeholder).
     */
    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image_principale)) {
            return $this->image_principale;
        }

        if (is_array($this->galerie_images) && !empty($this->galerie_images[0])) {
            return $this->galerie_images[0];
        }

        return 'https://placehold.co/600x600/f8fafc/94a3b8?text=Image+Produit';
    }

    /**
     * Deuxième image pour l'effet de survol (hover preview).
     */
    public function getSecondaryImageUrlAttribute(): ?string
    {
        $all = $this->all_images;
        return $all[1] ?? null;
    }

    /**
     * Liste complète ordonnée de toutes les images du produit (principale + galerie).
     * @return string[]
     */
    public function getAllImagesAttribute(): array
    {
        $images = [];

        if (!empty($this->image_principale)) {
            $images[] = $this->image_principale;
        }

        if (is_array($this->galerie_images)) {
            foreach ($this->galerie_images as $img) {
                if (is_string($img) && !empty(trim($img)) && !in_array($img, $images)) {
                    $images[] = trim($img);
                }
            }
        }

        if (empty($images)) {
            $images[] = 'https://placehold.co/600x600/f8fafc/94a3b8?text=Image+Produit';
        }

        return $images;
    }

    /**
     * Images secondaires (hors image de couverture).
     * @return string[]
     */
    public function getSecondaryImagesAttribute(): array
    {
        $all = $this->all_images;
        return count($all) > 1 ? array_slice($all, 1) : [];
    }

    /**
     * Vrai si le produit possède plusieurs visuels.
     */
    public function getHasMultipleImagesAttribute(): bool
    {
        return count($this->all_images) > 1;
    }

    /**
     * Nombre total d'images associées au produit.
     */
    public function getImagesCountAttribute(): int
    {
        return count($this->all_images);
    }

    // ── Accessors de formatage & compatibilité
    public function getPrixFormateAttribute(): string
    {
        return number_format($this->prix_vente, 0, ',', ' ') . ' FCFA';
    }

    public function getPrixCashFormateAttribute(): string
    {
        $cash = $this->prix_cash ?? round($this->prix_vente * 0.93);
        return number_format($cash, 0, ',', ' ') . ' FCFA';
    }

    public function getMensualiteFormateeAttribute(): string
    {
        $monthly = $this->mensualite_indicative ?? round($this->prix_vente / 12);
        return number_format($monthly, 0, ',', ' ') . ' FCFA/mois';
    }

    public function getNumeroReferenceAttribute(): string
    {
        return $this->reference ?? '';
    }

    public function getEstVedetteAttribute(): bool
    {
        return (bool) $this->is_vedette;
    }

    public function getStockDisponibleAttribute(): int
    {
        return (int) $this->stock;
    }

    public function getDescriptionAttribute(): string
    {
        return $this->description_complete ?? $this->description_courte ?? '';
    }

    public function getPrixCreditAttribute(): int
    {
        return (int) $this->prix_vente;
    }

    public function getPrixAttribute(): int
    {
        return (int) $this->prix_vente;
    }

    public function getPrixMensuelAttribute(): int
    {
        return (int) ($this->mensualite_indicative ?? round($this->prix_vente / 12));
    }

    public function getMensualiteMinAttribute(): int
    {
        return (int) ($this->mensualite_indicative ?? round($this->prix_vente / 12));
    }
}

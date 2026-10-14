<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'slug',
        'icone',
        'description',
        'parent_id',
        'ordre',
        'is_featured',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Retourne l'icône Lucide équivalente pour affichage moderne.
     */
    public function getLucideIconAttribute(): string
    {
        $map = [
            'inventory_2' => 'package',
            'kitchen' => 'refrigerator',
            'smartphone' => 'smartphone',
            'styler' => 'sparkles',
            'child_care' => 'baby',
            'local_fire_department' => 'flame',
            'agriculture' => 'wheat',
            'foundation' => 'hammer',
            'solar_power' => 'sun',
            'category' => 'folder-tree',
            'shopping_bag' => 'shopping-bag',
            'bolt' => 'zap',
        ];

        return $map[$this->icone] ?? ($this->icone ?: 'folder-tree');
    }
}

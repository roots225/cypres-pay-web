<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nom' => 'Kits Maison & Cuisine',
                'slug' => 'kits-maison-et-cuisine',
                'icone' => 'package',
                'description' => 'Packs complets tout-en-un pour équiper votre cuisine et salon en paiement flexible.',
                'ordre' => 1,
                'is_featured' => true,
            ],
            [
                'nom' => 'Électroménager',
                'slug' => 'electromenager',
                'icone' => 'refrigerator',
                'description' => 'Réfrigérateurs, congélateurs, cuisinières, climatiseurs split et lave-linge.',
                'ordre' => 2,
                'is_featured' => true,
            ],
            [
                'nom' => 'Téléphonie & Tech',
                'slug' => 'telephonie-et-tech',
                'icone' => 'smartphone',
                'description' => 'Smartphones iPhone, Samsung, Tecno, ordinateurs portables et tablettes.',
                'ordre' => 3,
                'is_featured' => true,
            ],
            [
                'nom' => 'Mode & Beauté',
                'slug' => 'mode-et-beaute',
                'icone' => 'sparkles',
                'description' => 'Pagnes Super Wax Hollandais, Hollantex, soins capillaires et bien-être.',
                'ordre' => 4,
                'is_featured' => true,
            ],
            [
                'nom' => 'Bébé & Enfant',
                'slug' => 'bebe-et-enfant',
                'icone' => 'baby',
                'description' => 'Poussettes, baignoires pliables, lits et accessoires pour enfants.',
                'ordre' => 5,
                'is_featured' => false,
            ],
            [
                'nom' => 'Sécurité & Incendie',
                'slug' => 'securite-et-btp',
                'icone' => 'flame',
                'description' => 'Caméras de surveillance HD, alarmes et extincteurs de sécurité.',
                'ordre' => 6,
                'is_featured' => false,
            ],
            [
                'nom' => 'Agriculture & Élevage',
                'slug' => 'agriculture-et-elevage',
                'icone' => 'wheat',
                'description' => 'Matériels agricoles, pulvérisateurs et aliments de bétail.',
                'ordre' => 7,
                'is_featured' => false,
            ],
            [
                'nom' => 'Bâtiment & Travaux',
                'slug' => 'batiment-et-travaux',
                'icone' => 'hammer',
                'description' => 'Ciment Bélier / Cuirasse, briques, dalles et outillage professionnel.',
                'ordre' => 8,
                'is_featured' => false,
            ],
            [
                'nom' => 'Énergie Solaire & Électricité',
                'slug' => 'energie-solaire',
                'icone' => 'sun',
                'description' => 'Kits solaires autonomes 1.2 kVA à 10 kVA et générateurs inverter.',
                'ordre' => 9,
                'is_featured' => true,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}

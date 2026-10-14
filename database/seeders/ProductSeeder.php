<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catKits = Category::where('slug', 'kits-maison-et-cuisine')->first();
        $catElectro = Category::where('slug', 'electromenager')->first();
        $catTech = Category::where('slug', 'telephonie-et-tech')->first();
        $catMode = Category::where('slug', 'mode-et-beaute')->first();
        $catBtp = Category::where('slug', 'batiment-et-travaux')->first();
        $catSolaire = Category::where('slug', 'energie-solaire')->first();

        $products = [
            // ── Ventes Flash & En vedette
            [
                'reference' => 'N°731',
                'nom' => "Millennium Fontaine d'Eau Automatique avec Mini Frigo Conservateur",
                'slug' => 'n-731-millennium-fontaine-deau-automatique-distributeur',
                'marque' => 'Millennium',
                'category_id' => $catElectro->id,
                'prix_vente' => 192000,
                'prix_cash' => 178560,
                'mensualite_indicative' => 8000,
                'stock' => 14,
                'image_principale' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDpYGk8c6ksGWMUGb0AsvKQSEc62vG3OjKiJldG0MNcLJ-YGCOMEh9ZjUfG1DaDJ8bw2Tb8np9spu_Y2Zm7kNzKBZIdUFj8K1xy8jRqC-ym3pjNkxI4a5LBFyU1ru89YpZ2Q4MOSmnuNNojDkzMEAwADQx8kx8tQmClNYpDQMU5rGKMBVXvMBQUluLd1yDqniyURNDWvxttcTWw6B8M0J905nyM-_2SKTCGshrCilfSm7PEBY-UJ4k',
                'galerie_images' => [
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuBmBoJQBARsDM6WJT2iVa9zagrWt4_R_pH3Mu5J-dkJbDTrdPL17LXzFnGns3uBF3xgZHsuF_L53C4yFFhMT1oOcwo8us6n9md4mhFlBYWT5POVcu6tqLtvkQCqwAZ-TOIHaBkzDG-31n_YxSDdc4oekFBBYtduM6brw1AgbgfeK75D1fqipy90mkxnTpk6-LjvC4Vd0nA_xX6BpWyvpHR-h4jYLveYXILEgAkoh3i7puuNBjm3a5U',
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuDEHiIgSx-ssPWJWIx5WnB3puWbwymlwSczHAZOn9xAobwoW0OSkqpfAMjMxyFxF_xXjPFjlJEWseYJfC4F1MhArJsYz-Oeru8mSlSkF4C-PEX0g9XVIPhd3OvRfB36xa6oizy26cM9qbrCLYoPX5BBt2pnIohGGSD1rYRr2rUDDoStP04UGthDyVN6papqPdDu5AUNBWESJrKA4D92Qq9Bapazfag-4i84jJyEuaTyyLm7V5vh_vg',
                ],
                'description_courte' => "Distributeur automatique d'eau fraîche et tempérée avec compartiment mini réfrigérateur intégré.",
                'is_kit' => false,
                'is_flash' => true,
                'is_vedette' => true,
                'en_stock' => true,
            ],
            [
                'reference' => 'N°717',
                'nom' => "Pagne Grand Super Wax Hollandais Class et Beau Multicolore 6 Yards",
                'slug' => 'n-717-pagne-grand-super-wax-hollandais-class-et-beau',
                'marque' => 'Super Wax',
                'category_id' => $catMode->id,
                'prix_vente' => 72000,
                'prix_cash' => 66960,
                'mensualite_indicative' => 3000,
                'stock' => 28,
                'image_principale' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAqk4_QVLJRMZNg8IL2IjaAiqQxS1dZRJUVEmn9dGxjc4LxjxkMMvT7WVED5MukWMlkC6TNFU3sjhs2Fm-tjav8DSBR9CAW2VHE1lNjv4n5aodRSboLB5qS2UCk4929jz9Pk47aNciWjG_tYCRMjjdwVnelAWCqW0YYbx1gjlqT8Ct3yLXHScdSOJsH2BwDhh4ODUrnoE7aVuzlQ0rPyo9ih8wRzXYwO3Lj75f-ZPlX9ha5_tDWV0w',
                'galerie_images' => [
                    'https://lh3.googleusercontent.com/aida/AEtjO1WbVz91S0ldCmixdtkwRg_icrsX_cjEB0SGp-FpsIJFQtWmdfEpjTrUHJUvA08Iph0HxRmOaRPLrdCjFzHE4H6MzP6NejjwrqWZuQdF3j8A7Gg5eYPJikyQ7YuAdskZTK2U5nNW6P0uutG4WlVGwPIP3HZSdmK2fTrCXIoFO05bukNNOuKtTO19xYirWISa9XD6f3b3dBYjgYd0J2Q-xgxYYD3u-e8Rqkur6Jx4ijH_t54ujzVi9Jxq',
                ],
                'description_courte' => "Véritable tissu africain Super Wax haut de gamme, motifs prestigieux et tenue impeccable.",
                'is_kit' => false,
                'is_flash' => true,
                'is_vedette' => true,
                'en_stock' => true,
            ],
            [
                'reference' => 'N°730',
                'nom' => "Binatone Distributeur d'Eau avec Réfrigérateur et Compartiment Congélateur",
                'slug' => 'n-730-distributeur-deau-binatone-avec-congelateur',
                'marque' => 'Binatone',
                'category_id' => $catElectro->id,
                'prix_vente' => 132000,
                'prix_cash' => 122760,
                'mensualite_indicative' => 5500,
                'stock' => 9,
                'image_principale' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBmBoJQBARsDM6WJT2iVa9zagrWt4_R_pH3Mu5J-dkJbDTrdPL17LXzFnGns3uBF3xgZHsuF_L53C4yFFhMT1oOcwo8us6n9md4mhFlBYWT5POVcu6tqLtvkQCqwAZ-TOIHaBkzDG-31n_YxSDdc4oekFBBYtduM6brw1AgbgfeK75D1fqipy90mkxnTpk6-LjvC4Vd0nA_xX6BpWyvpHR-h4jYLveYXILEgAkoh3i7puuNBjm3a5U',
                'description_courte' => "Système double fonction eau réfrigérée / chaude avec espace conservateur bas.",
                'is_kit' => false,
                'is_flash' => true,
                'is_vedette' => false,
                'en_stock' => true,
            ],
            [
                'reference' => 'N°729',
                'nom' => "Ilux Fontaine à Eau 3 Robinets LXWD-805 Gris Métallisé",
                'slug' => 'n-729-ilux-fontaine-a-eau-3-robinets-lxwd-805-gris',
                'marque' => 'Ilux',
                'category_id' => $catElectro->id,
                'prix_vente' => 144000,
                'prix_cash' => 133920,
                'mensualite_indicative' => 6000,
                'stock' => 11,
                'image_principale' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDEHiIgSx-ssPWJWIx5WnB3puWbwymlwSczHAZOn9xAobwoW0OSkqpfAMjMxyFxF_xXjPFjlJEWseYJfC4F1MhArJsYz-Oeru8mSlSkF4C-PEX0g9XVIPhd3OvRfB36xa6oizy26cM9qbrCLYoPX5BBt2pnIohGGSD1rYRr2rUDDoStP04UGthDyVN6papqPdDu5AUNBWESJrKA4D92Qq9Bapazfag-4i84jJyEuaTyyLm7V5vh_vg',
                'description_courte' => "Distribution d'eau chaude, ambiante et fraîche à grand débit pour famille ou bureau.",
                'is_kit' => false,
                'is_flash' => true,
                'is_vedette' => false,
                'en_stock' => true,
            ],

            // ── Les Grands Kits Maison & Cuisine
            [
                'reference' => 'N°019',
                'nom' => "Kit TV Msart 32\" + Réfrigérateur 60L Double Porte Éco-Énergie",
                'slug' => 'n-019-kit-tv-msart-32-refrigerateur-60l-double-porte',
                'marque' => 'Msart / Smart Tech',
                'category_id' => $catKits->id,
                'prix_vente' => 156000,
                'prix_cash' => 145080,
                'mensualite_indicative' => 6500,
                'stock' => 20,
                'image_principale' => 'https://lh3.googleusercontent.com/aida/AEtjO1WKIfcW4_C9xDOAgn33b1esqTDNmDfZSduh3LYB-GPo-ViimVJsDJx-cUA3O26XDnEyjLdD4vcA13XYgpyJD4YZT3GxrQ_8UwuAHzGX_fr3XHM1UR-gJ1KmscgtyXzXhrjFFqrVTFxr1MomYD-NRZZxzWnOcN7P5vt-1N6hQ4Dqgk7VpXAv1LXY4h48LEKn_EohQGkGWjFA2gfgUCEzgVpRQP4ewzB2AVJxxy4po1mW2gL7b3R4ISndxQ',
                'galerie_images' => [
                    'https://lh3.googleusercontent.com/aida/AEtjO1VvXKmhupOAOoHhOdRtfJCzDxtTwUl0c-Ou2SNcHCuk-9GwEbAt9-p2rctN1oie1dojO91Z7xFxFvVeHb9qSDanBpXNBCMWMHga-VHuMbbRj8Bg9T5f_HG7QcH-exPleqCk-Ce94rWmLG3iHffFVmGce7Nn1SSy5w3oHaPuO1gIKfVMqb_DPI9TswsM5jsdVpmrt9i_xd-1iuNoSf7p1_tHvEt7W8WsF4B9buOysxkpAbT3LPl6qlCLBQ',
                    'https://lh3.googleusercontent.com/aida/AEtjO1W6SeegTWRIGE-pRHVcQ1Ze7eikVGmDQoTwMsTLvNLVIYsiuJbEnMuum9jZQJid6n0mnt8UwxkyFcJ2-62ldTeinqUt_A6ot0Fo1UIkn8LwnWtrKq-dax0W_YFhLoFiBwV5Hp7_0vqRPeEypKXbnq0sSeN991hXsBlLYdWT_wQ4aExxaaxh1i6YTqRSKsj3yYvUogyP2swjK5-Vhzcj9ndKDQk78uDkTdN-5EFwoY5NSxibr7crqm0D',
                ],
                'description_courte' => "L'ensemble idéal pour studio ou chambre principale : téléviseur HD et réfrigérateur compact silencieux.",
                'is_kit' => true,
                'is_flash' => false,
                'is_vedette' => true,
                'en_stock' => true,
            ],
            [
                'reference' => 'N°018',
                'nom' => "Kit Électroménager Complet : Réfrigérateur 130L + Gazinière 4 Feux Inox B12",
                'slug' => 'n-018-kit-refrigerateur-130l-gaziniere-4-feux-inox',
                'marque' => 'Smart Technology',
                'category_id' => $catKits->id,
                'prix_vente' => 300000,
                'prix_cash' => 279000,
                'mensualite_indicative' => 12500,
                'stock' => 15,
                'image_principale' => 'https://lh3.googleusercontent.com/aida/AEtjO1VvXKmhupOAOoHhOdRtfJCzDxtTwUl0c-Ou2SNcHCuk-9GwEbAt9-p2rctN1oie1dojO91Z7xFxFvVeHb9qSDanBpXNBCMWMHga-VHuMbbRj8Bg9T5f_HG7QcH-exPleqCk-Ce94rWmLG3iHffFVmGce7Nn1SSy5w3oHaPuO1gIKfVMqb_DPI9TswsM5jsdVpmrt9i_xd-1iuNoSf7p1_tHvEt7W8WsF4B9buOysxkpAbT3LPl6qlCLBQ',
                'galerie_images' => [
                    'https://lh3.googleusercontent.com/aida/AEtjO1WKIfcW4_C9xDOAgn33b1esqTDNmDfZSduh3LYB-GPo-ViimVJsDJx-cUA3O26XDnEyjLdD4vcA13XYgpyJD4YZT3GxrQ_8UwuAHzGX_fr3XHM1UR-gJ1KmscgtyXzXhrjFFqrVTFxr1MomYD-NRZZxzWnOcN7P5vt-1N6hQ4Dqgk7VpXAv1LXY4h48LEKn_EohQGkGWjFA2gfgUCEzgVpRQP4ewzB2AVJxxy4po1mW2gL7b3R4ISndxQ',
                    'https://lh3.googleusercontent.com/aida/AEtjO1WbVz91S0ldCmixdtkwRg_icrsX_cjEB0SGp-FpsIJFQtWmdfEpjTrUHJUvA08Iph0HxRmOaRPLrdCjFzHE4H6MzP6NejjwrqWZuQdF3j8A7Gg5eYPJikyQ7YuAdskZTK2U5nNW6P0uutG4WlVGwPIP3HZSdmK2fTrCXIoFO05bukNNOuKtTO19xYirWISa9XD6f3b3dBYjgYd0J2Q-xgxYYD3u-e8Rqkur6Jx4ijH_t54ujzVi9Jxq',
                ],
                'description_courte' => "Équipez votre cuisine clé en main : four à gaz vitré haute précision et réfrigérateur familial.",
                'is_kit' => true,
                'is_flash' => false,
                'is_vedette' => true,
                'en_stock' => true,
            ],
            [
                'reference' => 'N°013',
                'nom' => "Kit Cuisine Batterie 4 Casseroles Inox + Ensemble Soupières Émaillées",
                'slug' => 'n-013-kit-cuisine-batterie-casseroles-soupieres-emaillees',
                'marque' => 'Cuisine Pro',
                'category_id' => $catKits->id,
                'prix_vente' => 54000,
                'prix_cash' => 50220,
                'mensualite_indicative' => 2250,
                'stock' => 35,
                'image_principale' => 'https://lh3.googleusercontent.com/aida/AEtjO1W6SeegTWRIGE-pRHVcQ1Ze7eikVGmDQoTwMsTLvNLVIYsiuJbEnMuum9jZQJid6n0mnt8UwxkyFcJ2-62ldTeinqUt_A6ot0Fo1UIkn8LwnWtrKq-dax0W_YFhLoFiBwV5Hp7_0vqRPeEypKXbnq0sSeN991hXsBlLYdWT_wQ4aExxaaxh1i6YTqRSKsj3yYvUogyP2swjK5-Vhzcj9ndKDQk78uDkTdN-5EFwoY5NSxibr7crqm0D',
                'description_courte' => "Pack complet d'ustensiles culinaires durables avec couvercles hermétiques en verre trempé.",
                'is_kit' => true,
                'is_flash' => false,
                'is_vedette' => true,
                'en_stock' => true,
            ],
            [
                'reference' => 'N°012',
                'nom' => "Kit Alimentaire Maxi Famille (Riz Parfumé, Huile Dinor 5L, Tomate & Épices)",
                'slug' => 'n-012-kit-alimentaire-maxi-famille-riz-huile-dinor-tomate',
                'marque' => 'Dinor / Riz Parfumé',
                'category_id' => $catKits->id,
                'prix_vente' => 84000,
                'prix_cash' => 78120,
                'mensualite_indicative' => 3500,
                'stock' => 50,
                'image_principale' => 'https://lh3.googleusercontent.com/aida/AEtjO1WbVz91S0ldCmixdtkwRg_icrsX_cjEB0SGp-FpsIJFQtWmdfEpjTrUHJUvA08Iph0HxRmOaRPLrdCjFzHE4H6MzP6NejjwrqWZuQdF3j8A7Gg5eYPJikyQ7YuAdskZTK2U5nNW6P0uutG4WlVGwPIP3HZSdmK2fTrCXIoFO05bukNNOuKtTO19xYirWISa9XD6f3b3dBYjgYd0J2Q-xgxYYD3u-e8Rqkur6Jx4ijH_t54ujzVi9Jxq',
                'description_courte' => "Sécurisez les vivres de votre foyer pour le mois complet avec versement échelonné à 700 FCFA/jour.",
                'is_kit' => true,
                'is_flash' => false,
                'is_vedette' => true,
                'en_stock' => true,
            ],

            // ── Smartphones & Informatique
            [
                'reference' => 'N°226',
                'nom' => "Samsung Galaxy A16 - 128Go / 4Go RAM - Triple Caméra 50MP - 5000 mAh",
                'slug' => 'n-226-samsung-galaxy-a16-128g-4g-ram-50mp-noir',
                'marque' => 'Samsung',
                'category_id' => $catTech->id,
                'prix_vente' => 120000,
                'prix_cash' => 111600,
                'mensualite_indicative' => 5000,
                'stock' => 18,
                'image_principale' => 'https://lh3.googleusercontent.com/aida/AEtjO1V7SyyQgbRqYbHCfdWfFjMAkksGpC9VTikuPl1c5jr88wvbEKr7PAEapvwAnytF1mP9IHWZI_yNtcRgmRiGOe3BgLRyiXBiYYQeYQV4-FHPFRG7ZLGg3ihB9mQvTX9BCpr6omhX0cwEsAk0CQW6uEDTG92nYKjhFQy-O4lfOr_PNoiHx-3jjKN_POy9EcKtN8xJwMMIJOAS5Q5-OptXI5cQFj5K27DkG9gFdfzbyKV7zpwS4Zrl1Z1D',
                'description_courte' => "Écran AMOLED 90Hz, autonomie record 2 jours et garantie officielle Samsung Côte d'Ivoire 24 mois.",
                'is_kit' => false,
                'is_flash' => false,
                'is_vedette' => true,
                'en_stock' => true,
            ],
            [
                'reference' => 'N°199',
                'nom' => "PC Portable ASUS X515MA - 15.6\" HD - Intel Celeron N4020 - 4Go RAM / 256Go SSD Windows 11",
                'slug' => 'n-199-asus-x515ma-15-6-intel-celeron-4-256-go-ssd',
                'marque' => 'Asus',
                'category_id' => $catTech->id,
                'prix_vente' => 360000,
                'prix_cash' => 334800,
                'mensualite_indicative' => 15000,
                'stock' => 8,
                'image_principale' => 'https://lh3.googleusercontent.com/aida/AEtjO1VQbz7WGCHrXW9k6Yb25qB8mIp8MUm4IZXcVb-CK4U-botk5zPpqdez_GhSll443e2_iuAJRaPheKHDur35nzu-nzzNlQJS_CaqyzWOPT9R_unn90cLP_AQUVdgevBz0S9t9arwRBdCUCQGg0NOCjQBOL2WoVuonb8O37DkQjJHVOq-WECuDqxFRo5RXTSbLjdzA0NalCLksH94Jg37FlzjZZnYd8i5GTZdYUIgKoRmyfSMBscyexujeQ',
                'description_courte' => "Idéal pour étudiants, comptabilité et bureautique avec démarrage ultra-rapide en 7 secondes.",
                'is_kit' => false,
                'is_flash' => false,
                'is_vedette' => true,
                'en_stock' => true,
            ],

            // ── BTP & Matériaux de Construction
            [
                'reference' => 'N°461',
                'nom' => "Ciment Bélier Classic CPJ 32.5R Haute Résistance (La Tonne de 20 Sacs)",
                'slug' => 'n-461-ciment-belier-classic-cpj-32-5r-la-tonne',
                'marque' => 'Ciment Bélier',
                'category_id' => $catBtp->id,
                'prix_vente' => 95000,
                'prix_cash' => 88350,
                'mensualite_indicative' => 4000,
                'stock' => 40,
                'image_principale' => 'https://lh3.googleusercontent.com/aida/AEtjO1V59ffGLWR-HegSQMw0yF6_qcxzPIWkPaLFxRfvnMFby0h1aSwM1oC8U63EdE_gTKE0xA5ceIaQD2wVhSGqFCB7NV3onqovtaSNSfHRR-syjgTrayyFYG681vqUEqUfAJGusa8ek5Tjr82Yp_XnmKfI2NlYY0MCuXe5zHFfVACcBYIlcDgO906pYXiO3T58jYd0LS1Wx2FcIQds7gkhG1facCRuMnZrCZVMy4To1i3eeafQ1bAg_OQD',
                'description_courte' => "Livraison directe sur votre chantier à Abidjan et périphérie. Construisez sereinement à crédit.",
                'is_kit' => false,
                'is_flash' => false,
                'is_vedette' => false,
                'en_stock' => true,
            ],

            // ── Solaire & Énergie Autonome
            [
                'reference' => 'N°042',
                'nom' => "Kit Solaire Autonome 2.5 kVA Hybride Pure Sinus + 2 Batteries Gel 200Ah + 4 Panneaux",
                'slug' => 'n-042-kit-solaire-autonome-2-5-kva-hybride-pure-sinus',
                'marque' => 'SunPower CI',
                'category_id' => $catSolaire->id,
                'prix_vente' => 1250000,
                'prix_cash' => 1162500,
                'mensualite_indicative' => 52000,
                'stock' => 6,
                'image_principale' => 'https://lh3.googleusercontent.com/aida/AEtjO1W_My-uT8xN9AJ5LKc1cGC2uvc-CxBHoIbKiyl7KdZAtSs5mKydHvlOl3mJA6E9CUH7E6t7fDnfDOfpEmDnIFz8XaAqweDpyX88zRl8LyMRUwtkOVrSXASpo6w9ddaQ1eDy_NKbL9BJrJUeuAz0YE32hCxJacJqRrJNkLUJ186oEqsYwIBkBf8q1HOu0zOr1vU32j3NlHFPjk9vkISBIcbUQulywk-52qp6nVlJ6L7uZ5aZb8Tdcg44',
                'description_courte' => "Zéro délestage ! Alimentez réfrigérateur, téléviseur, ventilateurs et lumières 24h/24 en toute autonomie.",
                'is_kit' => true,
                'is_flash' => false,
                'is_vedette' => true,
                'en_stock' => true,
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['reference' => $p['reference']], $p);
        }
    }
}

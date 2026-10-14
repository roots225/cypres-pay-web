<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : 1;

        // 1. Médias issus des produits existants du catalogue
        $products = Product::all();
        foreach ($products as $product) {
            if (empty($product->image_principale)) continue;

            $slug = Str::slug($product->nom);
            Media::firstOrCreate(
                ['name' => "[{$product->reference}] {$product->nom}"],
                [
                    'user_id' => $adminId,
                    'name' => "[{$product->reference}] {$product->nom}",
                    'file_name' => "{$slug}.jpg",
                    'original_name' => "{$slug}.jpg",
                    'disk' => 'public',
                    'path' => $product->image_principale,
                    'mime_type' => 'image/jpeg',
                    'extension' => 'jpg',
                    'type' => 'image',
                    'size' => rand(150000, 750000),
                    'width' => 800,
                    'height' => 800,
                    'alt' => "Photo de {$product->nom} - Cypres Pay Côte d'Ivoire",
                    'caption' => $product->description_courte,
                    'folder' => 'products',
                ]
            );
        }

        // 2. Documents PDF & Contrats BNPL types
        $documents = [
            [
                'name' => "Contrat Cadre BNPL Cypres Pay - Modèle Standard CI",
                'file_name' => "contrat-cadre-bnpl-standard-2026.pdf",
                'path' => "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf",
                'mime_type' => "application/pdf",
                'extension' => "pdf",
                'type' => "document",
                'size' => 1450000,
                'folder' => "documents",
                'caption' => "Conditions générales de facilités de paiement échelonné de 30 jours à 24 mois."
            ],
            [
                'name' => "Guide Officiel des Pièces Justificatives KYC",
                'file_name' => "guide-pieces-kyc-cypres-pay.pdf",
                'path' => "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf",
                'mime_type' => "application/pdf",
                'extension' => "pdf",
                'type' => "document",
                'size' => 980000,
                'folder' => "kyc",
                'caption' => "Liste des CNI, attestations d'identité et justificatifs de revenus éligibles."
            ],
            [
                'name' => "Bordereau de Livraison & Quittance Type Cypres Pay",
                'file_name' => "bordereau-livraison-quittance-cypres.pdf",
                'path' => "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf",
                'mime_type' => "application/pdf",
                'extension' => "pdf",
                'type' => "document",
                'size' => 520000,
                'folder' => "receipts",
                'caption' => "Modèle d'accusé de réception émargé à la livraison avec code 6 caractères."
            ],
            [
                'name' => "Grille Tarifaire BNPL & Simulateur Taux 2026",
                'file_name' => "grille-tarifaire-formules-bnpl-2026.xlsx",
                'path' => "/storage/media/grille-tarifaire-2026.xlsx",
                'mime_type' => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                'extension' => "xlsx",
                'type' => "document",
                'size' => 340000,
                'folder' => "documents",
                'caption' => "Tableau Excel des échéanciers et commissions partenaires."
            ],
        ];

        foreach ($documents as $doc) {
            Media::firstOrCreate(
                ['name' => $doc['name']],
                array_merge($doc, [
                    'user_id' => $adminId,
                    'original_name' => $doc['file_name'],
                    'disk' => 'public',
                    'alt' => $doc['name'],
                ])
            );
        }

        // 3. Vidéos Démonstratives
        $videos = [
            [
                'name' => "Spot Vidéo : Comment acheter avec Cypres Pay sans banque",
                'file_name' => "spot-demo-cypres-pay-abidjan.mp4",
                'path' => "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4",
                'mime_type' => "video/mp4",
                'extension' => "mp4",
                'type' => "video",
                'size' => 15400000,
                'width' => 1920,
                'height' => 1080,
                'folder' => "marketing",
                'caption' => "Clip promotionnel montrant le parcours client et le paiement via Mobile Money (Wave, Orange, MTN)."
            ],
            [
                'name' => "Tutoriel KYC : Prise en photo de la CNI et Signature Électronique",
                'file_name' => "tuto-kyc-signature-digitale.mp4",
                'path' => "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WeAreGoingOnBullrun.mp4",
                'mime_type' => "video/mp4",
                'extension' => "mp4",
                'type' => "video",
                'size' => 12300000,
                'width' => 1920,
                'height' => 1080,
                'folder' => "kyc",
                'caption' => "Vidéo explicative pour la validation du dossier de crédit en moins de 15 minutes."
            ]
        ];

        foreach ($videos as $vid) {
            Media::firstOrCreate(
                ['name' => $vid['name']],
                array_merge($vid, [
                    'user_id' => $adminId,
                    'original_name' => $vid['file_name'],
                    'disk' => 'public',
                    'alt' => $vid['name'],
                ])
            );
        }
    }
}

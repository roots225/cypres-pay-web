<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Administrateur Central
        User::updateOrCreate(
            ['email' => 'admin@cypresspay.ci'],
            [
                'prenom' => 'Direction',
                'nom' => 'Générale',
                'telephone' => '0700000001',
                'ville' => 'Abidjan',
                'commune' => 'Plateau',
                'adresse' => 'Immeuble Cypress Pay, Plateau',
                'role' => 'admin',
                'password' => Hash::make('CypressAdmin2026!'),
                'own_referral_code' => 'DIR001',
            ]
        );

        // 2. Agent Commercial Terrain
        User::updateOrCreate(
            ['email' => 'commercial@cypresspay.ci'],
            [
                'prenom' => 'Kouamé',
                'nom' => 'Jean-Luc',
                'telephone' => '0586708161',
                'ville' => 'Abidjan',
                'commune' => 'Yopougon',
                'adresse' => 'Yopougon PMI, Secteur Saint-Brice',
                'role' => 'commercial',
                'password' => Hash::make('Commercial2026!'),
                'own_referral_code' => 'COM-YOP-01',
            ]
        );

        // 3. Agent de Caisse Physique
        User::updateOrCreate(
            ['email' => 'caisse@cypresspay.ci'],
            [
                'prenom' => 'Aya',
                'nom' => 'Fatou',
                'telephone' => '0100000003',
                'ville' => 'Abidjan',
                'commune' => 'Adjamé',
                'adresse' => 'Guichet Cypress Pay Adjamé Liberté',
                'role' => 'caissier',
                'password' => Hash::make('Caisse2026!'),
                'own_referral_code' => 'CAISSE-01',
            ]
        );

        // 4. Client Démonstration
        User::updateOrCreate(
            ['email' => 'client@cypresspay.ci'],
            [
                'prenom' => 'Moussa',
                'nom' => 'Diabaté',
                'telephone' => '0708091011',
                'ville' => 'Abidjan',
                'commune' => 'Cocody Angré',
                'adresse' => 'Résidence Les Manguiers',
                'role' => 'client',
                'ref_code' => 'COM-YOP-01',
                'password' => Hash::make('Client2026!'),
            ]
        );
    }
}

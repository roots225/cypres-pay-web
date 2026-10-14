<?php

namespace Database\Seeders;

use App\Models\InstallmentFormula;
use Illuminate\Database\Seeder;

class InstallmentFormulaSeeder extends Seeder
{
    public function run(): void
    {
        $formulas = [
            [
                'id' => 1,
                'nom' => 'Paiement Cash (Comptant)',
                'frequence' => 'cash',
                'nombre_echeances' => 1,
                'apport_min_pct' => 100.00,
                'montant_min' => 0,
                'montant_max' => 5000000,
                'remise_cash_pct' => 7,
                'actif' => true,
            ],
            [
                'id' => 2,
                'nom' => '30 jours (Quotidien)',
                'frequence' => 'daily',
                'nombre_echeances' => 30,
                'apport_min_pct' => 0.00,
                'montant_min' => 20000,
                'montant_max' => 500000,
                'remise_cash_pct' => 0,
                'actif' => true,
            ],
            [
                'id' => 3,
                'nom' => '60 jours (Quotidien)',
                'frequence' => 'daily',
                'nombre_echeances' => 60,
                'apport_min_pct' => 0.00,
                'montant_min' => 50000,
                'montant_max' => 800000,
                'remise_cash_pct' => 0,
                'actif' => true,
            ],
            [
                'id' => 4,
                'nom' => '90 jours (3 mois)',
                'frequence' => 'monthly',
                'nombre_echeances' => 3,
                'apport_min_pct' => 0.00,
                'montant_min' => 30000,
                'montant_max' => 1000000,
                'remise_cash_pct' => 0,
                'actif' => true,
            ],
            [
                'id' => 5,
                'nom' => '180 jours (6 mois)',
                'frequence' => 'monthly',
                'nombre_echeances' => 6,
                'apport_min_pct' => 0.00,
                'montant_min' => 50000,
                'montant_max' => 1500000,
                'remise_cash_pct' => 0,
                'actif' => true,
            ],
            [
                'id' => 6,
                'nom' => '12 mois (1 an)',
                'frequence' => 'monthly',
                'nombre_echeances' => 12,
                'apport_min_pct' => 0.00,
                'montant_min' => 100000,
                'montant_max' => 3000000,
                'remise_cash_pct' => 0,
                'actif' => true,
            ],
            [
                'id' => 7,
                'nom' => '18 mois',
                'frequence' => 'monthly',
                'nombre_echeances' => 18,
                'apport_min_pct' => 0.00,
                'montant_min' => 200000,
                'montant_max' => 3000000,
                'remise_cash_pct' => 0,
                'actif' => true,
            ],
            [
                'id' => 8,
                'nom' => '24 mois (2 ans)',
                'frequence' => 'monthly',
                'nombre_echeances' => 24,
                'apport_min_pct' => 0.00,
                'montant_min' => 300000,
                'montant_max' => 3000000,
                'remise_cash_pct' => 0,
                'actif' => true,
            ],
            [
                'id' => 9,
                'nom' => '8 semaines (Hebdo)',
                'frequence' => 'weekly',
                'nombre_echeances' => 8,
                'apport_min_pct' => 0.00,
                'montant_min' => 30000,
                'montant_max' => 600000,
                'remise_cash_pct' => 0,
                'actif' => true,
            ],
            [
                'id' => 10,
                'nom' => '12 semaines (Hebdo)',
                'frequence' => 'weekly',
                'nombre_echeances' => 12,
                'apport_min_pct' => 0.00,
                'montant_min' => 50000,
                'montant_max' => 900000,
                'remise_cash_pct' => 0,
                'actif' => true,
            ],
        ];

        foreach ($formulas as $f) {
            InstallmentFormula::updateOrCreate(['id' => $f['id']], $f);
        }
    }
}

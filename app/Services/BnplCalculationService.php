<?php

namespace App\Services;

use App\Models\InstallmentFormula;

class BnplCalculationService
{
    /**
     * Calcule le plan de financement pour un montant donné et une formule.
     */
    public function calculate(int $totalAmount, InstallmentFormula $formula): array
    {
        // 1. Si formule Cash : application de la remise de 7%
        if ($formula->frequence === 'cash') {
            $discountPct = $formula->remise_cash_pct > 0 ? $formula->remise_cash_pct : 7;
            $discountAmount = (int) round($totalAmount * ($discountPct / 100));
            $finalCashPrice = $totalAmount - $discountAmount;

            return [
                'type' => 'cash',
                'original_amount' => $totalAmount,
                'discount_percent' => $discountPct,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalCashPrice,
                'downpayment' => $finalCashPrice,
                'installment_amount' => 0,
                'number_of_installments' => 1,
                'frequency' => 'cash',
                'frequency_label' => 'Comptant à la commande',
                'schedule' => [],
            ];
        }

        // 2. Si formule Crédit Échelonné (30j à 24 mois)
        $downpaymentPct = (float) $formula->apport_min_pct;
        $downpayment = (int) floor($totalAmount * ($downpaymentPct / 100));
        $financedAmount = $totalAmount - $downpayment;

        $installmentsCount = max(1, $formula->nombre_echeances);
        $installmentAmount = (int) floor($financedAmount / $installmentsCount);

        // Ajustement des arrondis sur la dernière échéance
        $totalInstallmentsSum = $installmentAmount * $installmentsCount;
        $remainder = $financedAmount - $totalInstallmentsSum;

        // Génération de l'échéancier prévisionnel
        $schedule = [];
        $currentDate = now();

        for ($i = 1; $i <= $installmentsCount; $i++) {
            $dueDate = match ($formula->frequence) {
                'daily' => $currentDate->copy()->addDays($i),
                'weekly' => $currentDate->copy()->addWeeks($i),
                'monthly' => $currentDate->copy()->addMonths($i),
                default => $currentDate->copy()->addMonths($i),
            };

            $amountDue = $installmentAmount;
            if ($i === $installmentsCount && $remainder > 0) {
                $amountDue += $remainder; // Reste d'arrondi sur la dernière échéance
            }

            $schedule[] = [
                'number' => $i,
                'due_date' => $dueDate->format('Y-m-d'),
                'due_date_formatted' => $dueDate->translatedFormat('d F Y'),
                'amount' => $amountDue,
            ];
        }

        return [
            'type' => 'credit',
            'original_amount' => $totalAmount,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'final_amount' => $totalAmount,
            'downpayment' => $downpayment,
            'financed_amount' => $financedAmount,
            'installment_amount' => $installmentAmount,
            'number_of_installments' => $installmentsCount,
            'frequency' => $formula->frequence,
            'frequency_label' => $formula->label_frequence,
            'schedule' => $schedule,
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Installment;
use App\Models\Order;
use App\Models\PaymentReceipt;
use Illuminate\Support\Str;

class PaymentReceiptService
{
    /**
     * Génère un code de reçu alphanumérique unique à 6 caractères sans ambiguïté visuelle.
     * (Exclut 0, O, 1, I pour éviter toute confusion de lecture client/caissier).
     */
    public function generateUniqueReceiptCode(): string
    {
        $alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $codeLength = 6;

        do {
            $code = '';
            for ($i = 0; $i < $codeLength; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
        } while (PaymentReceipt::where('code_recu', $code)->exists());

        return $code;
    }

    /**
     * Enregistre un paiement et émet un reçu officiel.
     */
    public function createReceipt(
        Order $order,
        int $montant,
        string $canal,
        ?Installment $installment = null,
        ?int $agentId = null,
        ?string $referenceExterne = null
    ): PaymentReceipt {
        $receiptCode = $this->generateUniqueReceiptCode();

        $receipt = PaymentReceipt::create([
            'code_recu' => $receiptCode,
            'order_id' => $order->id,
            'installment_id' => $installment?->id,
            'user_id' => $order->user_id,
            'agent_id' => $agentId,
            'montant' => $montant,
            'canal' => $canal,
            'reference_externe' => $referenceExterne,
            'date_versement' => now(),
            'est_valide' => true,
        ]);

        // Mise à jour de l'échéance si applicable
        if ($installment) {
            $installment->update([
                'montant_paye' => $installment->montant_paye + $montant,
                'statut' => ($installment->montant_paye + $montant >= $installment->montant_du) ? 'paye' : 'partiellement_paye',
                'date_paiement' => now(),
                'recu_code' => $receiptCode,
            ]);
        }

        // Vérifier si la commande entière est soldée
        $allPaid = $order->installments()->where('statut', '!=', 'paye')->doesntExist();
        if ($allPaid && $order->installments()->count() > 0) {
            $order->update(['statut' => 'soldee']);
        }

        return $receipt;
    }

    /**
     * Recherche un reçu par son code à 6 caractères pour vérification publique.
     */
    public function verifyReceipt(string $code): ?PaymentReceipt
    {
        $cleanCode = strtoupper(trim($code));
        return PaymentReceipt::with(['order', 'installment', 'user', 'agent'])
            ->where('code_recu', $cleanCode)
            ->where('est_valide', true)
            ->first();
    }
}

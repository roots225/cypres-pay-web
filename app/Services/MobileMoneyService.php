<?php

namespace App\Services;

class MobileMoneyService
{
    /**
     * Initialise une intention de paiement (Mobile Money, Carte Bancaire, PayPal, Cash livraison, Guichet).
     */
    public function initiatePayment(int $amount, string $channel, string $clientPhone, string $orderReference): array
    {
        $transactionId = 'TXN-' . strtoupper(bin2hex(random_bytes(4)));

        return match ($channel) {
            'wave' => [
                'status' => 'initiated',
                'channel' => 'Wave CI',
                'transaction_id' => $transactionId,
                'checkout_url' => "https://pay.wave.com/c/test-session?amount={$amount}&order={$orderReference}",
                'instructions' => "Veuillez valider le paiement instantané dans votre application mobile Wave CI.",
                'qr_simulated' => true,
            ],
            'orange_money' => [
                'status' => 'initiated',
                'channel' => 'Orange Money CI',
                'transaction_id' => $transactionId,
                'ussd_code' => '#144*82#',
                'instructions' => "Composez le #144*82# ou validez le push sur votre mobile Orange pour autoriser le versement de " . number_format($amount, 0, ',', ' ') . " FCFA.",
            ],
            'mtn_momo' => [
                'status' => 'initiated',
                'channel' => 'MTN Mobile Money',
                'transaction_id' => $transactionId,
                'ussd_code' => '*133#',
                'instructions' => "Approuvez la notification de débit ou composez *133# sur votre ligne MTN pour confirmer.",
            ],
            'moov_money' => [
                'status' => 'initiated',
                'channel' => 'Moov Money CI',
                'transaction_id' => $transactionId,
                'ussd_code' => '*155#',
                'instructions' => "Composez *155# et confirmez avec votre code secret Moov pour autoriser le paiement.",
            ],
            'cash_delivery', 'cash' => [
                'status' => 'confirmed_cash',
                'channel' => 'Paiement Cash à la livraison',
                'transaction_id' => $transactionId,
                'instructions' => "Préparez la somme exacte de " . number_format($amount, 0, ',', ' ') . " FCFA en espèces lors de la livraison à votre adresse.",
            ],
            'card', 'carte' => [
                'status' => 'card_authorized',
                'channel' => 'Carte Bancaire (Visa / Mastercard)',
                'transaction_id' => $transactionId,
                'instructions' => "Paiement par carte sécurisé par 3D Secure. La transaction a été autorisée avec succès.",
            ],
            'paypal' => [
                'status' => 'paypal_pending',
                'channel' => 'PayPal',
                'transaction_id' => $transactionId,
                'checkout_url' => "https://www.paypal.com/checkoutnow?token=EC-{$transactionId}",
                'instructions' => "Connectez-vous à votre compte PayPal pour finaliser votre transaction internationale.",
            ],
            'caisse_agence' => [
                'status' => 'pending_agent',
                'channel' => 'Caisse Agence Cypress Pay',
                'transaction_id' => $transactionId,
                'instructions' => "Présentez-vous à notre agence Cypress Pay (Yopougon ou Cocody) muni de votre référence {$orderReference} pour régler.",
            ],
            default => [
                'status' => 'pending_agent',
                'channel' => 'Autre moyen',
                'transaction_id' => $transactionId,
                'instructions' => "Un conseiller Cypres Pay prendra contact avec vous sur le {$clientPhone} pour finaliser le versement.",
            ],
        };
    }
}

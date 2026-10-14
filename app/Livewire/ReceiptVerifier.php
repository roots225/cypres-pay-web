<?php

namespace App\Livewire;

use App\Services\PaymentReceiptService;
use Livewire\Component;

class ReceiptVerifier extends Component
{
    public string $code = '';
    public ?array $receiptDetails = null;
    public bool $hasSearched = false;
    public ?string $errorMessage = null;

    public function mount(?string $code = null): void
    {
        if ($code) {
            $this->code = strtoupper(trim($code));
            $this->verify();
        }
    }

    public function verify(): void
    {
        $this->errorMessage = null;
        $this->receiptDetails = null;
        $this->hasSearched = true;

        $clean = strtoupper(trim($this->code));
        if (empty($clean) || strlen($clean) < 5) {
            $this->errorMessage = "Veuillez entrer un code de versement valide à 6 caractères (ex: 28B7S0).";
            return;
        }

        $service = app(PaymentReceiptService::class);
        $receipt = $service->verifyReceipt($clean);

        if (!$receipt) {
            $this->errorMessage = "Aucun versement certifié ne correspond au code « {$clean} ». Veuillez vérifier votre reçu ou contacter le support.";
            return;
        }

        // Anonymisation partielle du nom du client (ex: K*** Lakota)
        $clientName = $receipt->user ? ($receipt->user->prenom[0] . '*** ' . ($receipt->user->ville ?? 'Côte d\'Ivoire')) : 'Client  Pay';

        $this->receiptDetails = [
            'code' => $receipt->code_recu,
            'montant' => $receipt->montant_formate,
            'canal' => $receipt->canal,
            'canal_label' => match($receipt->canal) {
                'wave' => 'Wave Côte d\'Ivoire',
                'orange_money' => 'Orange Money',
                'mtn_momo' => 'MTN Mobile Money',
                'moov_money' => 'Moov Africa Money',
                'cash' => 'Versement Espèces en Agence',
                default => 'Mobile Money'
            },
            'date' => $receipt->date_versement->translatedFormat('d F Y à H:i'),
            'commande' => $receipt->order->numero_commande ?? 'CP-CMD-2026',
            'client' => $clientName,
            'agent' => $receipt->agent ? ($receipt->agent->prenom . ' (' . $receipt->agent->commune . ')') : 'Serveur Automatique',
            'statut' => $receipt->est_valide ? 'Certifié Conforme' : 'Annulé',
        ];
    }

    public function render()
    {
        return view('livewire.receipt-verifier');
    }
}

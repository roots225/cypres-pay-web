<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Installment;
use App\Models\Order;
use App\Services\PaymentReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('statut');
        $search = $request->get('q');

        $query = Order::with(['user', 'formula', 'items.product', 'installments']);

        if ($status) {
            $query->where('statut', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_commande', 'like', "%{$search}%")
                  ->orWhere('telephone_contact', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('nom', 'like', "%{$search}%")
                         ->orWhere('prenom', 'like', "%{$search}%")
                         ->orWhere('telephone', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(12)->withQueryString();

        // Compteurs par statut pour les badges de filtre
        $counts = [
            'all' => Order::count(),
            'en_attente_kyc' => Order::where('statut', 'en_attente_kyc')->count(),
            'valide' => Order::where('statut', 'valide')->count(),
            'en_cours' => Order::where('statut', 'en_cours')->count(),
            'livre' => Order::where('statut', 'livre')->count(),
            'solde' => Order::where('statut', 'solde')->count(),
            'annule' => Order::where('statut', 'annule')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'status', 'search', 'counts'));
    }

    public function show(Order $order)
    {
        $order->load(['user.kycProfile', 'formula', 'items.product', 'installments', 'receipts']);

        $totalPaid = $order->installments->where('statut', 'paye')->sum('montant_paye');
        $totalRemaining = max(0, $order->montant_finance - $totalPaid);

        return view('admin.orders.show', compact('order', 'totalPaid', 'totalRemaining'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'statut' => 'required|in:en_attente_kyc,valide,en_cours,livre,solde,annule',
        ]);

        $oldStatus = $order->statut;
        $order->update(['statut' => $validated['statut']]);

        return back()->with('success', "Le statut de la commande {$order->numero_commande} est passé de '{$oldStatus}' à '{$validated['statut']}'.");
    }

    public function markInstallmentPaid(Request $request, Installment $installment, PaymentReceiptService $receiptService)
    {
        $order = $installment->order;
        $amount = (int) ($request->get('montant') ?: $installment->montant_du);
        $canal = $request->get('canal', 'cash');

        $receipt = $receiptService->createReceipt(
            order: $order,
            montant: $amount,
            canal: $canal,
            installment: $installment,
            agentId: Auth::id(),
            referenceExterne: $request->get('reference_externe')
        );

        // Si toutes les échéances sont payées, basculer la commande en 'solde'
        $unpaidCount = $order->installments()->where('statut', '!=', 'paye')->count();
        if ($unpaidCount === 0) {
            $order->update(['statut' => 'solde']);
        }

        return back()->with('success', "Échéance #{$installment->numero_echeance} réglée. Quittance officielle émise avec le code {$receipt->code_recu}.");
    }
}

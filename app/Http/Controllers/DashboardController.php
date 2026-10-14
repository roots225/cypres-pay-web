<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Order;
use App\Models\PaymentReceipt;
use App\Models\User;
use App\Services\PaymentReceiptService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Espace Client : suivi de ses commandes et échéances
     */
    public function client()
    {
        $user = User::where('role', 'client')->first() ?? User::first();
        $orders = Order::with(['installments', 'items.product', 'formula'])
            ->where('user_id', $user?->id)
            ->orderBy('id', 'desc')
            ->get();

        $activeOrder = $orders->first();
        $installments = $activeOrder ? $activeOrder->installments : collect();
        $totalPaid = $activeOrder ? $activeOrder->installments()->sum('montant_paye') : 0;
        $totalRemaining = $activeOrder ? ($activeOrder->montant_finance - $totalPaid) : 0;

        return view('client.dashboard', compact('user', 'orders', 'activeOrder', 'installments', 'totalPaid', 'totalRemaining'));
    }

    /**
     * Portail Commercial : suivi des clients et saisie de commande terrain
     */
    public function commercial()
    {
        $commercial = User::where('role', 'commercial')->first();
        $clients = User::where('ref_code', $commercial?->own_referral_code)->orWhere('role', 'client')->take(10)->get();
        $orders = Order::with('user')->orderBy('id', 'desc')->take(10)->get();

        $totalSales = Order::sum('montant_total');
        $commissionEarned = (int) round($totalSales * 0.03); // 3% de commission apporteur

        return view('commercial.dashboard', compact('commercial', 'clients', 'orders', 'totalSales', 'commissionEarned'));
    }

    /**
     * Portail Caisse : encaissement physique et émission de reçu
     */
    public function caisse(Request $request)
    {
        $caissier = User::where('role', 'caissier')->first();
        $recentReceipts = PaymentReceipt::with(['order', 'user'])
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        $todayTotal = PaymentReceipt::whereDate('created_at', today())->sum('montant');

        return view('caisse.dashboard', compact('caissier', 'recentReceipts', 'todayTotal'));
    }

    /**
     * Action Caisse : Enregistrer un versement physique et imprimer le code à 6 caractères
     */
    public function encaissement(Request $request, PaymentReceiptService $receiptService)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'montant' => 'required|integer|min:1000',
            'canal' => 'required|string',
        ]);

        $order = Order::findOrFail($request->order_id);
        $nextInstallment = $order->installments()->where('statut', '!=', 'paye')->orderBy('id')->first();
        $caissier = User::where('role', 'caissier')->first();

        $receipt = $receiptService->createReceipt(
            order: $order,
            montant: $request->montant,
            canal: $request->canal,
            installment: $nextInstallment,
            agentId: $caissier?->id,
            referenceExterne: $request->reference_externe
        );

        return back()->with('success', "Paiement de {$receipt->montant_formate} enregistré avec succès ! Reçu officiel émis : {$receipt->code_recu}");
    }
}

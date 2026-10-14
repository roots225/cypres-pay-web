<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentReceipt;
use App\Services\PaymentReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminReceiptController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q'); // code ou ref
        $canal = $request->get('canal');

        $query = PaymentReceipt::with(['order', 'user', 'agent']);

        if ($search) {
            $query->where('code_recu', 'like', "%{$search}%")
                  ->orWhere('reference_externe', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('nom', 'like', "%{$search}%")
                         ->orWhere('telephone', 'like', "%{$search}%");
                  });
        }

        if ($canal) {
            $query->where('canal', $canal);
        }

        $receipts = $query->latest('date_versement')->paginate(15)->withQueryString();

        $totalAmount = (int) PaymentReceipt::where('est_valide', true)->sum('montant');
        $todayAmount = (int) PaymentReceipt::where('est_valide', true)
            ->whereDate('date_versement', today())
            ->sum('montant');

        $orders = Order::whereIn('statut', ['valide', 'en_cours', 'livre'])->get();

        return view('admin.receipts.index', compact('receipts', 'search', 'canal', 'totalAmount', 'todayAmount', 'orders'));
    }

    public function store(Request $request, PaymentReceiptService $receiptService)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'montant' => 'required|integer|min:500',
            'canal' => 'required|in:wave,orange_money,mtn_momo,moov_money,cash',
            'reference_externe' => 'nullable|string|max:100',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $nextInstallment = $order->installments()->where('statut', '!=', 'paye')->orderBy('id')->first();

        $receipt = $receiptService->createReceipt(
            order: $order,
            montant: $validated['montant'],
            canal: $validated['canal'],
            installment: $nextInstallment,
            agentId: Auth::id(),
            referenceExterne: $validated['reference_externe']
        );

        return redirect()->route('admin.receipts.index')
            ->with('success', "Quittance émise avec succès ! Code officiel généré : {$receipt->code_recu} ({$receipt->montant_formate}).");
    }

    public function toggleValid(PaymentReceipt $receipt)
    {
        $receipt->est_valide = ! $receipt->est_valide;
        $receipt->save();

        $status = $receipt->est_valide ? 'réactivée' : 'annulée';
        return back()->with('success', "La quittance {$receipt->code_recu} a été {$status}.");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Installment;
use App\Models\KycProfile;
use App\Models\Order;
use App\Models\PaymentReceipt;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ── Métriques Financières Globales
        $totalSales = (int) Order::sum('montant_total');
        $downpaymentCollected = (int) Order::sum('apport_initial');
        $financedOutstanding = (int) Order::sum('montant_finance');
        $totalCashReceipts = (int) PaymentReceipt::sum('montant');
        $todayReceiptsTotal = (int) PaymentReceipt::whereDate('created_at', today())->sum('montant');

        // ── Métriques Commandes & Échéances
        $totalOrdersCount = Order::count();
        $pendingKycOrders = Order::where('statut', 'en_attente_kyc')->count();
        $activeOrders = Order::whereIn('statut', ['valide', 'en_cours'])->count();
        $deliveredOrders = Order::where('statut', 'livre')->count();
        $settledOrders = Order::where('statut', 'solde')->count();

        // ── Métriques Utilisateurs
        $clientsCount = User::where('role', 'client')->count();
        $commerciauxCount = User::where('role', 'commercial')->count();
        $caissiersCount = User::where('role', 'caissier')->count();
        $adminsCount = User::where('role', 'admin')->count();

        // ── Dossiers KYC en attente
        $pendingKycCount = KycProfile::where('statut', 'en_attente')->count();

        // ── Produits & Stocks
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock', '<=', 5)->count();

        // ── Activités Récentes
        $recentOrders = Order::with(['user', 'formula'])->latest()->take(5)->get();
        $recentReceipts = PaymentReceipt::with(['user', 'order'])->latest()->take(5)->get();
        $pendingKycs = KycProfile::with('user')->where('statut', 'en_attente')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalSales',
            'downpaymentCollected',
            'financedOutstanding',
            'totalCashReceipts',
            'todayReceiptsTotal',
            'totalOrdersCount',
            'pendingKycOrders',
            'activeOrders',
            'deliveredOrders',
            'settledOrders',
            'clientsCount',
            'commerciauxCount',
            'caissiersCount',
            'adminsCount',
            'pendingKycCount',
            'totalProducts',
            'lowStockProducts',
            'recentOrders',
            'recentReceipts',
            'pendingKycs'
        ));
    }
}

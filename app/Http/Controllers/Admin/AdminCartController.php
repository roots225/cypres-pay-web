<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AdminCartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Liste des paniers & gestion des paniers abandonnés
     */
    public function index(Request $request)
    {
        // 1. Détecter et marquer les paniers inactifs depuis plus de 15 minutes
        $this->cartService->flagAbandonedCarts(15);

        $filterStatus = $request->input('status', 'abandoned');
        $search = $request->input('q');
        $stepFilter = $request->input('step');

        // Requête principale
        $query = Cart::with(['items.product', 'user', 'formula', 'order'])
            ->where('items_count', '>', 0);

        if ($filterStatus === 'abandoned') {
            $query->where(function ($q) {
                $q->where('status', 'abandoned')
                  ->orWhere(function ($sub) {
                      $sub->where('status', 'active')
                          ->where('last_activity_at', '<', Carbon::now()->subMinutes(15));
                  });
            });
        } elseif ($filterStatus === 'active') {
            $query->where('status', 'active')
                  ->where('last_activity_at', '>=', Carbon::now()->subMinutes(15));
        } elseif ($filterStatus === 'converted') {
            $query->where('status', 'converted');
        } elseif ($filterStatus !== 'all') {
            $query->where('status', $filterStatus);
        }

        if ($stepFilter) {
            $query->where('step', $stepFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_firstname', 'like', "%{$search}%")
                  ->orWhere('customer_lastname', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('shipping_city', 'like', "%{$search}%")
                  ->orWhere('session_token', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%")
                        ->orWhere('telephone', 'like', "%{$search}%");
                  });
            });
        }

        $carts = $query->latest('last_activity_at')->paginate(15)->withQueryString();

        // ── KPIs Globaux
        $totalCartsCount = Cart::where('items_count', '>', 0)->count();
        $abandonedCount = Cart::where(function ($q) {
            $q->where('status', 'abandoned')
              ->orWhere(function ($sub) {
                  $sub->where('status', 'active')
                      ->where('items_count', '>', 0)
                      ->where('last_activity_at', '<', Carbon::now()->subMinutes(15));
              });
        })->count();

        $convertedCount = Cart::where('status', 'converted')->count();
        $activeCount = Cart::where('status', 'active')
            ->where('items_count', '>', 0)
            ->where('last_activity_at', '>=', Carbon::now()->subMinutes(15))
            ->count();

        $lostRevenue = Cart::where(function ($q) {
            $q->where('status', 'abandoned')
              ->orWhere(function ($sub) {
                  $sub->where('status', 'active')
                      ->where('items_count', '>', 0)
                      ->where('last_activity_at', '<', Carbon::now()->subMinutes(15));
              });
        })->sum('total');

        $abandonmentRate = $totalCartsCount > 0 ? round(($abandonedCount / $totalCartsCount) * 100, 1) : 0;

        $recoveredCount = Cart::where('status', 'converted')
            ->where(function ($q) {
                $q->whereNotNull('recovery_wa_sent_at')
                  ->orWhereNotNull('recovery_email_sent_at');
            })->count();

        $recoveredRevenue = Cart::where('status', 'converted')
            ->where(function ($q) {
                $q->whereNotNull('recovery_wa_sent_at')
                  ->orWhereNotNull('recovery_email_sent_at');
            })->sum('total');

        // Répartition par étape d'abandon
        $stepBreakdown = [
            'cart' => Cart::where('status', 'abandoned')->where('step', 'cart')->count(),
            'address' => Cart::where('status', 'abandoned')->where('step', 'address')->count(),
            'shipping' => Cart::where('status', 'abandoned')->where('step', 'shipping')->count(),
            'payment' => Cart::where('status', 'abandoned')->where('step', 'payment')->count(),
        ];

        return view('admin.carts.index', compact(
            'carts',
            'filterStatus',
            'search',
            'stepFilter',
            'abandonedCount',
            'convertedCount',
            'activeCount',
            'lostRevenue',
            'abandonmentRate',
            'recoveredCount',
            'recoveredRevenue',
            'stepBreakdown'
        ));
    }

    /**
     * Détail d'un panier
     */
    public function show(Cart $cart)
    {
        $cart->load(['items.product', 'user', 'formula', 'order']);
        return view('admin.carts.show', compact('cart'));
    }

    /**
     * Enregistrer l'envoi d'une relance WhatsApp
     */
    public function markWhatsAppSent(Request $request, Cart $cart)
    {
        $cart->recovery_wa_sent_at = Carbon::now();
        $cart->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Relance WhatsApp enregistrée',
                'recovery_wa_sent_at' => $cart->recovery_wa_sent_at->diffForHumans(),
            ]);
        }

        return back()->with('success', 'Relance WhatsApp enregistrée pour ' . $cart->nom_client_complet);
    }

    /**
     * Convertir manuellement un panier en commande (depuis un contact commercial ou appel)
     */
    public function convertToOrder(Request $request, Cart $cart)
    {
        if ($cart->items->isEmpty()) {
            return back()->with('error', 'Impossible de convertir un panier vide.');
        }

        // Trouver ou créer l'utilisateur
        $phone = $cart->telephone_client ?: '0700000000';
        $user = $cart->user ?: User::firstOrCreate(
            ['telephone' => $phone],
            [
                'prenom' => $cart->customer_firstname ?: 'Client',
                'nom' => $cart->customer_lastname ?: 'CypresPay',
                'email' => $cart->customer_email ?: ($phone . '@client.cypresspay.ci'),
                'ville' => $cart->shipping_city ?: 'Abidjan',
                'adresse' => $cart->shipping_address ?: 'Abidjan',
                'password' => bcrypt(Str::random(12)),
            ]
        );

        $orderNumber = 'CP-CMD-' . date('Y') . '-' . strtoupper(Str::random(6));

        $order = Order::create([
            'numero_commande' => $orderNumber,
            'user_id' => $user->id,
            'cart_id' => $cart->id,
            'formula_id' => $cart->formula_id,
            'montant_total' => $cart->total,
            'apport_initial' => $cart->downpayment_amount,
            'montant_finance' => max(0, $cart->total - $cart->downpayment_amount),
            'montant_echeance' => $cart->monthly_amount,
            'nombre_echeances' => ($cart->formula && $cart->formula->duree_mois) ? $cart->formula->duree_mois : 1,
            'type_paiement' => $cart->type_achat === 'cash' ? 'cash' : 'credit',
            'statut' => 'en_attente_kyc',
            'canal_paiement' => $cart->payment_method ?: 'wave',
            'mode_livraison' => $cart->shipping_method ?: 'express_abidjan',
            'frais_livraison' => $cart->shipping_cost ?: 0,
            'adresse_livraison' => $cart->shipping_address ?: 'Abidjan',
            'ville_livraison' => $cart->shipping_city ?: 'Abidjan',
            'telephone_contact' => $phone,
            'notes' => 'Converti manuellement depuis le panier abandonné par ' . auth()->user()->nom_complet,
        ]);

        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantite' => $item->quantite,
                'prix_unitaire' => $item->prix_unitaire,
                'mensualite_unitaire' => $item->mensualite_unitaire,
            ]);
        }

        // Marquer le panier comme converti
        $cart->status = 'converted';
        $cart->step = 'completed';
        $cart->converted_at = Carbon::now();
        $cart->save();

        return redirect()->route('admin.orders.show', $order)->with('success', 'Panier converti avec succès en commande ' . $orderNumber);
    }

    /**
     * Supprimer un panier
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();
        return back()->with('success', 'Panier supprimé avec succès.');
    }
}

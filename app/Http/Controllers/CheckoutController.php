<?php

namespace App\Http\Controllers;

use App\Models\InstallmentFormula;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\BnplCalculationService;
use App\Services\CartService;
use App\Services\MobileMoneyService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function show(Request $request)
    {
        $cart = $this->cartService->getOrCreateCart($request);
        $formulas = InstallmentFormula::where('actif', true)->get();

        // Si l'utilisateur est connecté et que le panier n'a pas encore de coordonnées, pré-remplir
        $user = Auth::user();
        if ($user) {
            $cart->customer_firstname = $cart->customer_firstname ?: $user->prenom;
            $cart->customer_lastname = $cart->customer_lastname ?: $user->nom;
            $cart->customer_phone = $cart->customer_phone ?: $user->telephone;
            $cart->customer_email = $cart->customer_email ?: $user->email;
            $cart->shipping_city = $cart->shipping_city ?: ($user->ville ?: 'Abidjan');
            $cart->shipping_address = $cart->shipping_address ?: $user->adresse;
            $cart->save();
        }

        return view('checkout', compact('cart', 'formulas'));
    }

    public function process(Request $request, BnplCalculationService $bnplService, MobileMoneyService $momoService)
    {
        $request->validate([
            'prenom' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'telephone' => 'required|string|max:15',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:100',
            'commune' => 'nullable|string|max:100',
            'shipping_method' => 'nullable|string|max:50',
            'formula_id' => 'required|exists:installment_formulas,id',
            'canal_paiement' => 'required|string',
            'type_achat' => 'nullable|string|in:cash,credit',
            'cart_data' => 'nullable|string',
        ]);

        $cart = $this->cartService->getOrCreateCart($request);

        // Si des items sont passés dans cart_data et que le cart en base est vide ou doit être synchronisé
        if ($request->filled('cart_data')) {
            $clientItems = json_decode($request->cart_data, true);
            if (!empty($clientItems) && is_array($clientItems)) {
                $this->cartService->syncFromClient($cart, $clientItems);
            }
        }

        $cart->loadMissing(['items.product']);

        if ($cart->items->isEmpty()) {
            return back()->with('error', 'Votre panier est vide. Veuillez ajouter un produit pour continuer.');
        }

        // Trouver ou créer l'utilisateur
        $phone = preg_replace('/[^0-9]/', '', $request->telephone);
        $user = User::firstOrCreate(
            ['telephone' => $phone],
            [
                'prenom' => $request->prenom,
                'nom' => $request->nom,
                'email' => $request->email ?? ($phone . '@client.cypresspay.ci'),
                'ville' => $request->ville,
                'adresse' => $request->adresse,
                'password' => bcrypt(Str::random(12)),
            ]
        );

        // Mise à jour des coordonnées sur le panier
        $shippingMethod = $request->input('shipping_method', $cart->shipping_method ?: 'express_abidjan');
        $shippingCost = match ($shippingMethod) {
            'express_abidjan' => 2000,
            'interieur_ci' => 3500,
            'retrait_agence' => 0,
            default => 2000,
        };

        $typeAchat = $request->input('type_achat', $cart->type_achat ?: 'credit');
        $formula = InstallmentFormula::findOrFail($request->formula_id);

        $cart->customer_firstname = $request->prenom;
        $cart->customer_lastname = $request->nom;
        $cart->customer_phone = $phone;
        $cart->customer_email = $request->email;
        $cart->shipping_city = $request->ville;
        $cart->shipping_commune = $request->commune;
        $cart->shipping_address = $request->adresse;
        $cart->shipping_method = $shippingMethod;
        $cart->shipping_cost = $shippingCost;
        $cart->payment_method = $request->canal_paiement;
        $cart->formula_id = $formula->id;
        $cart->type_achat = $typeAchat;
        $cart->user_id = $user->id;
        $cart->recalculateTotals();

        // Calcul BNPL avec BnplCalculationService
        $subtotal = $cart->subtotal;
        $calc = $bnplService->calculate($subtotal, $formula);

        // Ajout des frais de livraison au total
        $finalTotal = $calc['final_amount'] + $shippingCost;
        $downpayment = $calc['downpayment'];
        if ($typeAchat === 'cash' || $formula->frequence === 'cash') {
            $downpayment = $finalTotal;
        }

        // Création de la commande
        $orderNumber = 'CP-CMD-' . date('Y') . '-' . strtoupper(Str::random(6));
        $order = Order::create([
            'numero_commande' => $orderNumber,
            'user_id' => $user->id,
            'cart_id' => $cart->id,
            'formula_id' => $formula->id,
            'montant_total' => $finalTotal,
            'apport_initial' => $downpayment,
            'montant_finance' => $calc['financed_amount'] ?? 0,
            'montant_echeance' => $calc['installment_amount'] ?? 0,
            'nombre_echeances' => $calc['number_of_installments'],
            'type_paiement' => ($formula->frequence === 'cash' || $typeAchat === 'cash') ? 'cash' : 'credit',
            'statut' => 'en_attente_kyc',
            'canal_paiement' => $request->canal_paiement,
            'mode_livraison' => $shippingMethod,
            'frais_livraison' => $shippingCost,
            'adresse_livraison' => $request->adresse . ($request->commune ? ' (' . $request->commune . ')' : ''),
            'ville_livraison' => $request->ville,
            'telephone_contact' => $phone,
            'notes' => $request->notes,
        ]);

        // Sauvegarde des articles de la commande
        foreach ($cart->items as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'quantite' => $cartItem->quantite,
                'prix_unitaire' => $cartItem->prix_unitaire,
                'mensualite_unitaire' => $cartItem->mensualite_unitaire,
            ]);
        }

        // Initialisation de l'échéancier si crédit
        if (!empty($calc['schedule']) && $typeAchat !== 'cash' && $formula->frequence !== 'cash') {
            foreach ($calc['schedule'] as $sched) {
                $order->installments()->create([
                    'numero_echeance' => $sched['number'],
                    'date_echeance' => $sched['due_date'],
                    'montant_du' => $sched['amount'],
                    'montant_paye' => 0,
                    'statut' => 'a_payer',
                ]);
            }
        }

        // Initialisation de la passerelle de paiement
        $paymentIntent = $momoService->initiatePayment(
            $downpayment > 0 ? $downpayment : $finalTotal,
            $request->canal_paiement,
            $phone,
            $orderNumber
        );

        // Marquer le panier comme converti et complété
        $cart->status = 'converted';
        $cart->step = 'completed';
        $cart->converted_at = Carbon::now();
        $cart->total = $finalTotal;
        $cart->save();

        // Réinitialiser le cookie de panier pour les prochains achats
        Cookie::queue(Cookie::forget(CartService::COOKIE_NAME));

        return view('checkout-success', compact('order', 'paymentIntent', 'calc', 'cart'));
    }
}

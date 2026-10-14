<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\InstallmentFormula;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Page récapitulative du Panier
     */
    public function index(Request $request)
    {
        $cart = $this->cartService->getOrCreateCart($request);
        $formulas = InstallmentFormula::where('actif', true)->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'cart' => $this->formatCartResponse($cart),
            ]);
        }

        return view('cart', compact('cart', 'formulas'));
    }

    /**
     * Obtenir les données JSON du panier en cours
     */
    public function data(Request $request): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart($request);

        return response()->json([
            'success' => true,
            'cart' => $this->formatCartResponse($cart),
        ]);
    }

    /**
     * Ajouter un produit au panier
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantite' => 'nullable|integer|min:1|max:50',
            'options' => 'nullable|array',
        ]);

        $cart = $this->cartService->getOrCreateCart($request);
        $cart = $this->cartService->addItem(
            $cart,
            (int) $request->product_id,
            (int) ($request->quantite ?? 1),
            (array) ($request->options ?? [])
        );

        return response()->json([
            'success' => true,
            'message' => 'Produit ajouté avec succès à votre panier',
            'cart' => $this->formatCartResponse($cart),
        ]);
    }

    /**
     * Mettre à jour la quantité d'un article
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'required|integer',
            'quantite' => 'required|integer|min:0|max:50',
        ]);

        $cart = $this->cartService->getOrCreateCart($request);
        $cart = $this->cartService->updateItem(
            $cart,
            (int) $request->item_id,
            (int) $request->quantite
        );

        return response()->json([
            'success' => true,
            'cart' => $this->formatCartResponse($cart),
        ]);
    }

    /**
     * Supprimer un article
     */
    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'required|integer',
        ]);

        $cart = $this->cartService->getOrCreateCart($request);
        $cart = $this->cartService->removeItem($cart, (int) $request->item_id);

        return response()->json([
            'success' => true,
            'message' => 'Article retiré du panier',
            'cart' => $this->formatCartResponse($cart),
        ]);
    }

    /**
     * Vider le panier
     */
    public function clear(Request $request): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart($request);
        $cart = $this->cartService->clearCart($cart);

        return response()->json([
            'success' => true,
            'message' => 'Panier vidé',
            'cart' => $this->formatCartResponse($cart),
        ]);
    }

    /**
     * Sauvegarder l'avancement d'une étape (autosave temps réel)
     */
    public function updateStep(Request $request): JsonResponse
    {
        $request->validate([
            'step' => 'required|string|in:cart,address,shipping,payment,completed',
            'data' => 'nullable|array',
        ]);

        $cart = $this->cartService->getOrCreateCart($request);
        $cart = $this->cartService->updateStep($cart, $request->step, $request->input('data', []));

        return response()->json([
            'success' => true,
            'message' => 'Étape enregistrée avec succès',
            'cart' => $this->formatCartResponse($cart),
        ]);
    }

    /**
     * Synchronisation client (localStorage) vers serveur
     */
    public function sync(Request $request): JsonResponse
    {
        $items = $request->input('items', []);
        $cart = $this->cartService->getOrCreateCart($request);

        if (!empty($items) && is_array($items)) {
            $cart = $this->cartService->syncFromClient($cart, $items);
        }

        return response()->json([
            'success' => true,
            'cart' => $this->formatCartResponse($cart),
        ]);
    }

    /**
     * Reprendre un panier depuis n'importe quel device via son token (lien WhatsApp ou email)
     */
    public function resume(string $token)
    {
        $cart = Cart::with(['items.product'])->where('session_token', $token)->first();

        if (!$cart) {
            return redirect()->route('catalog')->with('error', 'Ce panier est introuvable ou a déjà expiré.');
        }

        if ($cart->status === 'converted') {
            return redirect()->route('catalog')->with('info', 'Cette commande a déjà été validée. Merci pour votre fidélité !');
        }

        // Réactiver le panier si marqué abandonné
        if ($cart->status === 'abandoned') {
            $cart->status = 'active';
            $cart->last_activity_at = now();
            $cart->save();
        }

        // Poser le cookie de session pour ce device (30 jours)
        Cookie::queue(CartService::COOKIE_NAME, $cart->session_token, CartService::COOKIE_LIFETIME);

        return redirect()->route('checkout.show')->with('success', 'Bon retour parmi nous ! Votre panier a été restauré à l\'étape exacte où vous l\'aviez laissé.');
    }

    /**
     * Formater le panier pour le frontend JSON
     */
    protected function formatCartResponse(Cart $cart): array
    {
        return [
            'id' => $cart->id,
            'session_token' => $cart->session_token,
            'status' => $cart->status,
            'step' => $cart->step,
            'type_achat' => $cart->type_achat,
            'formula_id' => $cart->formula_id,
            'formula' => $cart->formula ? [
                'id' => $cart->formula->id,
                'nom' => $cart->formula->nom,
                'duree_mois' => $cart->formula->duree_mois,
                'taux_majoration' => $cart->formula->taux_majoration,
                'pourcentage_apport' => $cart->formula->pourcentage_apport,
                'frequence' => $cart->formula->frequence,
            ] : null,
            'customer' => [
                'firstname' => $cart->customer_firstname,
                'lastname' => $cart->customer_lastname,
                'phone' => $cart->customer_phone,
                'email' => $cart->customer_email,
            ],
            'shipping' => [
                'city' => $cart->shipping_city,
                'commune' => $cart->shipping_commune,
                'address' => $cart->shipping_address,
                'method' => $cart->shipping_method,
                'cost' => $cart->shipping_cost,
                'method_label' => $cart->shipping_method_label,
            ],
            'payment' => [
                'method' => $cart->payment_method,
                'method_label' => $cart->payment_method_label,
            ],
            'notes' => $cart->customer_notes,
            'subtotal' => $cart->subtotal,
            'discount_amount' => $cart->discount_amount,
            'shipping_cost' => $cart->shipping_cost,
            'total' => $cart->total,
            'monthly_amount' => $cart->monthly_amount,
            'downpayment_amount' => $cart->downpayment_amount,
            'items_count' => $cart->items_count,
            'resume_url' => $cart->resume_url,
            'items' => $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'nom' => $item->product ? $item->product->nom : 'Produit retiré',
                    'image' => $item->product ? $item->product->image_url : null,
                    'prix' => $item->prix_unitaire,
                    'monthly' => $item->mensualite_unitaire,
                    'qty' => $item->quantite,
                    'total_prix' => $item->total_prix,
                    'total_monthly' => $item->total_mensualite,
                ];
            }),
        ];
    }
}

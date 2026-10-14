<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\InstallmentFormula;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class CartService
{
    const COOKIE_NAME = 'cypress_cart_token';
    const COOKIE_LIFETIME = 43200; // 30 jours en minutes

    /**
     * Récupérer ou initialiser le panier actif pour la session / utilisateur courant.
     */
    public function getOrCreateCart(?Request $request = null): Cart
    {
        $request = $request ?? request();
        $token = $request->cookie(self::COOKIE_NAME) 
            ?? $request->header('X-Cart-Token') 
            ?? $request->input('cart_token');

        $user = Auth::user();

        // 1. Si utilisateur connecté, chercher d'abord son panier actif le plus récent
        if ($user) {
            $userCart = Cart::with(['items.product', 'formula'])
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->latest('last_activity_at')
                ->first();

            if ($token) {
                $guestCart = Cart::with(['items.product'])
                    ->where('session_token', $token)
                    ->where('status', 'active')
                    ->whereNull('user_id')
                    ->first();

                // Si un panier invité existe avec le token, on fusionne dans le panier du user
                if ($guestCart && $userCart && $guestCart->id !== $userCart->id) {
                    $this->mergeCarts($guestCart, $userCart);
                    $cart = $userCart;
                } elseif ($guestCart) {
                    // On attribue le panier invité à l'utilisateur
                    $guestCart->user_id = $user->id;
                    $guestCart->customer_firstname = $guestCart->customer_firstname ?: $user->prenom;
                    $guestCart->customer_lastname = $guestCart->customer_lastname ?: $user->nom;
                    $guestCart->customer_phone = $guestCart->customer_phone ?: $user->telephone;
                    $guestCart->customer_email = $guestCart->customer_email ?: $user->email;
                    $guestCart->shipping_city = $guestCart->shipping_city ?: $user->ville;
                    $guestCart->shipping_address = $guestCart->shipping_address ?: $user->adresse;
                    $guestCart->save();
                    $cart = $guestCart;
                } else {
                    $cart = $userCart;
                }
            } else {
                $cart = $userCart;
            }

            if ($cart) {
                $cart->last_activity_at = Carbon::now();
                $cart->save();
                Cookie::queue(self::COOKIE_NAME, $cart->session_token, self::COOKIE_LIFETIME);
                return $cart;
            }
        }

        // 2. Visiteur invité : recherche par session_token
        if ($token) {
            $cart = Cart::with(['items.product', 'formula'])
                ->where('session_token', $token)
                ->where('status', 'active')
                ->first();

            if ($cart) {
                $cart->last_activity_at = Carbon::now();
                $cart->save();
                Cookie::queue(self::COOKIE_NAME, $cart->session_token, self::COOKIE_LIFETIME);
                return $cart;
            }
        }

        // 3. Nouveau panier
        $newToken = 'cpt_' . Str::random(40);
        $defaultFormula = InstallmentFormula::where('actif', true)->orderBy('id')->first();

        $cart = Cart::create([
            'session_token' => $newToken,
            'user_id' => $user?->id,
            'status' => 'active',
            'step' => 'cart',
            'formula_id' => $defaultFormula?->id,
            'type_achat' => 'credit',
            'customer_firstname' => $user?->prenom,
            'customer_lastname' => $user?->nom,
            'customer_phone' => $user?->telephone,
            'customer_email' => $user?->email,
            'shipping_city' => $user?->ville ?: 'Abidjan',
            'shipping_address' => $user?->adresse,
            'shipping_method' => 'express_abidjan',
            'shipping_cost' => 2000,
            'subtotal' => 0,
            'discount_amount' => 0,
            'total' => 2000,
            'monthly_amount' => 0,
            'downpayment_amount' => 0,
            'items_count' => 0,
            'payment_method' => 'wave',
            'last_activity_at' => Carbon::now(),
        ]);

        Cookie::queue(self::COOKIE_NAME, $newToken, self::COOKIE_LIFETIME);

        return $cart->load(['items.product', 'formula']);
    }

    /**
     * Fusionner un panier invité dans un panier utilisateur
     */
    protected function mergeCarts(Cart $source, Cart $destination): void
    {
        foreach ($source->items as $srcItem) {
            $destItem = CartItem::where('cart_id', $destination->id)
                ->where('product_id', $srcItem->product_id)
                ->first();

            if ($destItem) {
                $destItem->quantite += $srcItem->quantite;
                $destItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $destination->id,
                    'product_id' => $srcItem->product_id,
                    'quantite' => $srcItem->quantite,
                    'prix_unitaire' => $srcItem->prix_unitaire,
                    'mensualite_unitaire' => $srcItem->mensualite_unitaire,
                    'options' => $srcItem->options,
                ]);
            }
        }

        $source->status = 'cancelled';
        $source->save();

        $destination->recalculateTotals();
    }

    /**
     * Ajouter un produit au panier
     */
    public function addItem(Cart $cart, int $productId, int $quantity = 1, array $options = []): Cart
    {
        $product = Product::findOrFail($productId);
        $quantity = max(1, $quantity);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        $monthlyUnit = (int) ($product->prix_mensuel ?? round($product->prix / 12));

        if ($item) {
            $item->quantite += $quantity;
            $item->prix_unitaire = $product->prix;
            $item->mensualite_unitaire = $monthlyUnit;
            if (!empty($options)) {
                $item->options = array_merge($item->options ?? [], $options);
            }
            $item->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantite' => $quantity,
                'prix_unitaire' => $product->prix,
                'mensualite_unitaire' => $monthlyUnit,
                'options' => $options,
            ]);
        }

        $cart->recalculateTotals();

        return $cart->load(['items.product', 'formula']);
    }

    /**
     * Mettre à jour la quantité d'un article
     */
    public function updateItem(Cart $cart, int $itemId, int $quantity): Cart
    {
        $item = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->first();
        if ($item) {
            if ($quantity <= 0) {
                $item->delete();
            } else {
                $item->quantite = $quantity;
                $item->save();
            }
        }

        $cart->recalculateTotals();

        return $cart->load(['items.product', 'formula']);
    }

    /**
     * Supprimer un article
     */
    public function removeItem(Cart $cart, int $itemId): Cart
    {
        CartItem::where('cart_id', $cart->id)->where('id', $itemId)->delete();
        $cart->recalculateTotals();

        return $cart->load(['items.product', 'formula']);
    }

    /**
     * Vider le panier
     */
    public function clearCart(Cart $cart): Cart
    {
        CartItem::where('cart_id', $cart->id)->delete();
        $cart->recalculateTotals();

        return $cart->load(['items.product', 'formula']);
    }

    /**
     * Mettre à jour l'étape et les données associées
     */
    public function updateStep(Cart $cart, string $step, array $data = []): Cart
    {
        $validSteps = ['cart', 'address', 'shipping', 'payment', 'completed'];
        if (in_array($step, $validSteps)) {
            $cart->step = $step;
        }

        // Étape 1 : Formule ou type d'achat
        if (isset($data['formula_id'])) {
            $cart->formula_id = $data['formula_id'];
        }
        if (isset($data['type_achat'])) {
            $cart->type_achat = in_array($data['type_achat'], ['cash', 'credit']) ? $data['type_achat'] : 'credit';
        }

        // Étape 2 : Coordonnées et adresse
        if (isset($data['customer_firstname'])) $cart->customer_firstname = $data['customer_firstname'];
        if (isset($data['customer_lastname'])) $cart->customer_lastname = $data['customer_lastname'];
        if (isset($data['customer_phone'])) $cart->customer_phone = $data['customer_phone'];
        if (isset($data['customer_email'])) $cart->customer_email = $data['customer_email'];
        if (isset($data['shipping_city'])) $cart->shipping_city = $data['shipping_city'];
        if (isset($data['shipping_commune'])) $cart->shipping_commune = $data['shipping_commune'];
        if (isset($data['shipping_address'])) $cart->shipping_address = $data['shipping_address'];
        if (isset($data['customer_notes'])) $cart->customer_notes = $data['customer_notes'];

        // Étape 3 : Mode de livraison
        if (isset($data['shipping_method'])) {
            $cart->shipping_method = $data['shipping_method'];
            // Calcul automatique du coût de livraison selon la méthode
            $cart->shipping_cost = match ($data['shipping_method']) {
                'express_abidjan' => 2000,
                'interieur_ci' => 3500,
                'retrait_agence' => 0,
                default => (int) ($data['shipping_cost'] ?? 0),
            };
        }

        // Étape 4 : Mode de paiement
        if (isset($data['payment_method'])) {
            $cart->payment_method = $data['payment_method'];
        }

        if (isset($data['metadata'])) {
            $currentMeta = $cart->metadata ?? [];
            $cart->metadata = array_merge($currentMeta, $data['metadata']);
        }

        $cart->last_activity_at = Carbon::now();
        $cart->recalculateTotals();

        return $cart->load(['items.product', 'formula']);
    }

    /**
     * Synchroniser les articles du localStorage vers la base de données
     */
    public function syncFromClient(Cart $cart, array $items): Cart
    {
        if (empty($items)) {
            return $cart;
        }

        foreach ($items as $item) {
            $productId = $item['id'] ?? null;
            $qty = $item['qty'] ?? 1;

            if ($productId) {
                $product = Product::find($productId);
                if ($product) {
                    $existingItem = CartItem::where('cart_id', $cart->id)
                        ->where('product_id', $product->id)
                        ->first();

                    $monthlyUnit = (int) ($product->prix_mensuel ?? round($product->prix / 12));

                    if ($existingItem) {
                        $existingItem->quantite = max($existingItem->quantite, (int) $qty);
                        $existingItem->save();
                    } else {
                        CartItem::create([
                            'cart_id' => $cart->id,
                            'product_id' => $product->id,
                            'quantite' => (int) $qty,
                            'prix_unitaire' => $product->prix,
                            'mensualite_unitaire' => $monthlyUnit,
                        ]);
                    }
                }
            }
        }

        $cart->recalculateTotals();

        return $cart->load(['items.product', 'formula']);
    }

    /**
     * Marquer automatiquement les paniers inactifs comme abandonnés
     */
    public function flagAbandonedCarts(int $minutesInactivity = 15): int
    {
        return Cart::where('status', 'active')
            ->where('items_count', '>', 0)
            ->where('last_activity_at', '<', Carbon::now()->subMinutes($minutesInactivity))
            ->update([
                'status' => 'abandoned',
                'abandoned_at' => Carbon::now(),
            ]);
    }
}

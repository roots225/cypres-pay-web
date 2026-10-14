@extends('layouts.app')

@section('title', 'Mon Panier — Cypress Pay Côte d\'Ivoire')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12" x-data="cartPageComponent()">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-on-surface tracking-tight flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-primary-container text-white flex items-center justify-center shadow-sm">
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                </span>
                Mon Panier Électronique & BNPL
            </h1>
            <p class="text-[13px] text-text-muted mt-1">
                Vos articles sont sauvegardés automatiquement en base. Vous pouvez changer d'appareil sans rien perdre.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('catalog') }}" class="px-4 py-2 rounded-xl border border-border-subtle hover:bg-slate-50 text-xs font-bold text-slate-700 transition-colors flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Continuer mes achats
            </a>
        </div>
    </div>

    <!-- Si panier vide -->
    <div x-show="items.length === 0" class="bg-surface-container-lowest rounded-3xl border border-border-subtle p-12 text-center" style="display: none;">
        <div class="w-20 h-20 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="shopping-bag" class="w-10 h-10"></i>
        </div>
        <h2 class="text-xl font-bold text-on-surface">Votre panier est actuellement vide</h2>
        <p class="text-[13px] text-text-muted mt-2 mb-6 max-w-md mx-auto">
            Découvrez nos kits cuisine, électroménager, téléviseurs et smartphones disponibles à crédit ou au comptant.
        </p>
        <a href="{{ route('catalog') }}" class="px-6 py-3 bg-primary-container hover:bg-brand-red-hover text-white rounded-xl text-sm font-bold inline-flex items-center gap-2 transition-all shadow-md">
            <i data-lucide="grid" class="w-4.5 h-4.5"></i>
            Parcourir notre catalogue
        </a>
    </div>

    <!-- Grille Panier Plein -->
    <div x-show="items.length > 0" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- 1. Colonne Gauche : Liste des articles & Formules (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-surface-container-lowest rounded-3xl border border-border-subtle p-6 shadow-xs">
                <div class="flex items-center justify-between pb-4 border-b border-border-subtle">
                    <span class="text-sm font-bold text-on-surface" x-text="itemsCount + ' article(s) dans votre sélection'"></span>
                    <button type="button" @click="clearAll()" class="text-xs text-text-muted hover:text-red-600 font-semibold transition-colors flex items-center gap-1">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        Vider tout le panier
                    </button>
                </div>

                <div class="divide-y divide-border-subtle/70">
                    <template x-for="item in items" :key="item.id">
                        <div class="py-5 flex flex-col sm:flex-row sm:items-center gap-4">
                            <!-- Image -->
                            <img :src="item.image || 'https://via.placeholder.com/80'" 
                                 :alt="item.nom"
                                 class="w-20 h-20 object-contain rounded-2xl bg-slate-50 border border-border-subtle p-2 shrink-0"/>

                            <!-- Info Produit -->
                            <div class="flex-1 min-w-0">
                                <h3 class="font-extrabold text-[15px] text-on-surface truncate" x-text="item.nom"></h3>
                                <div class="mt-1 flex items-center gap-3 text-xs text-text-muted">
                                    <span>Prix unitaire : <strong class="text-on-surface" x-text="Number(item.prix).toLocaleString('fr-FR') + ' FCFA'"></strong></span>
                                    <span class="text-primary font-bold" x-text="Number(item.monthly).toLocaleString('fr-FR') + ' FCFA/m'"></span>
                                </div>
                            </div>

                            <!-- Contrôle Quantité -->
                            <div class="flex items-center gap-3 shrink-0">
                                <div class="flex items-center border border-border-subtle rounded-xl bg-slate-50 overflow-hidden">
                                    <button type="button" @click="updateQty(item.id, item.qty - 1)" 
                                            class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-200 transition-colors font-bold cursor-pointer">
                                        -
                                    </button>
                                    <span class="w-10 text-center font-bold text-xs text-on-surface" x-text="item.qty"></span>
                                    <button type="button" @click="updateQty(item.id, item.qty + 1)" 
                                            class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-200 transition-colors font-bold cursor-pointer">
                                        +
                                    </button>
                                </div>

                                <!-- Prix Total Ligne -->
                                <div class="text-right w-28 shrink-0">
                                    <div class="font-black text-on-surface text-[15px]" x-text="(item.prix * item.qty).toLocaleString('fr-FR') + ' FCFA'"></div>
                                    <div class="text-[11px] font-bold text-primary" x-text="(item.monthly * item.qty).toLocaleString('fr-FR') + ' FCFA/m'"></div>
                                </div>

                                <!-- Bouton supprimer -->
                                <button type="button" @click="removeItem(item.id)" 
                                        class="p-2 text-slate-300 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors cursor-pointer" title="Retirer">
                                    <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Avantages Cypress Pay -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-surface-container-lowest border border-border-subtle flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </span>
                    <div class="text-xs">
                        <span class="font-bold text-on-surface block">Paiement 100% Sécurisé</span>
                        <span class="text-text-muted">Wave, Orange, MTN, Carte</span>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-surface-container-lowest border border-border-subtle flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i data-lucide="truck" class="w-5 h-5"></i>
                    </span>
                    <div class="text-xs">
                        <span class="font-bold text-on-surface block">Livraison Partout en CI</span>
                        <span class="text-text-muted">Abidjan 24h & Villes Intérieur</span>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-surface-container-lowest border border-border-subtle flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <i data-lucide="smartphone" class="w-5 h-5"></i>
                    </span>
                    <div class="text-xs">
                        <span class="font-bold text-on-surface block">Sauvegarde Multi-Écrans</span>
                        <span class="text-text-muted">Reprise mobile ou ordinateur</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Colonne Droite : Formule, Totaux & Passer à la Caisse (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-surface-container-lowest rounded-3xl border border-border-subtle p-6 shadow-sm sticky top-40 space-y-5">
                <h3 class="text-base font-extrabold text-on-surface pb-3 border-b border-border-subtle flex items-center justify-between">
                    <span>Récapitulatif Financier</span>
                    <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">CI Garanti</span>
                </h3>

                <!-- Type d'achat : Crédit BNPL vs Comptant -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700">Mode d'acquisition :</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="setTypeAchat('credit')"
                                class="p-2.5 rounded-xl border text-xs font-extrabold transition-all text-center cursor-pointer"
                                :class="typeAchat === 'credit' ? 'border-primary bg-primary text-white shadow-xs' : 'border-border-subtle text-slate-700 hover:bg-slate-50'">
                            Crédit BNPL
                        </button>
                        <button type="button" @click="setTypeAchat('cash')"
                                class="p-2.5 rounded-xl border text-xs font-extrabold transition-all text-center cursor-pointer"
                                :class="typeAchat === 'cash' ? 'border-emerald-600 bg-emerald-600 text-white shadow-xs' : 'border-border-subtle text-slate-700 hover:bg-slate-50'">
                            Comptant (-7%)
                        </button>
                    </div>
                </div>

                <!-- Détails Calculs -->
                <div class="space-y-2.5 text-xs pt-2">
                    <div class="flex justify-between text-text-muted">
                        <span>Sous-total articles :</span>
                        <span class="font-bold text-on-surface" x-text="subtotal.toLocaleString('fr-FR') + ' FCFA'"></span>
                    </div>

                    <div class="flex justify-between text-emerald-600 font-bold" x-show="typeAchat === 'cash'">
                        <span>Remise Comptant (-7%) :</span>
                        <span x-text="'- ' + discountAmount.toLocaleString('fr-FR') + ' FCFA'"></span>
                    </div>

                    <div class="flex justify-between text-text-muted">
                        <span>Livraison estimée :</span>
                        <span class="font-bold text-slate-700">Dès 0 FCFA (Choix à l'étape suivante)</span>
                    </div>

                    <div class="pt-3 border-t border-border-subtle flex justify-between items-baseline">
                        <span class="font-extrabold text-sm text-on-surface">Total à payer :</span>
                        <div class="text-right">
                            <span class="text-xl font-black text-on-surface" x-text="finalTotal.toLocaleString('fr-FR')"></span>
                            <span class="text-xs font-bold text-slate-500"> FCFA</span>
                        </div>
                    </div>

                    <div x-show="typeAchat === 'credit'" class="p-3 rounded-2xl bg-brand-red-subtle/40 border border-primary/20 text-xs mt-2">
                        <div class="flex justify-between items-center text-primary font-bold">
                            <span>Mensualité estimée :</span>
                            <span class="text-base font-black" x-text="monthlyTotal.toLocaleString('fr-FR') + ' FCFA/m'"></span>
                        </div>
                        <span class="text-[10px] text-text-muted block mt-1">Échéances sur 3, 6, 9 ou 12 mois sans frais cachés.</span>
                    </div>
                </div>

                <!-- Bouton Valider -->
                <a href="{{ route('checkout.show') }}" 
                   class="w-full bg-primary-container hover:bg-brand-red-hover text-white py-3.5 rounded-2xl font-extrabold text-sm flex items-center justify-center gap-2 transition-all shadow-md hover:shadow-lg cursor-pointer">
                    <span>Passer la commande</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>

                <p class="text-[11px] text-center text-text-muted">
                    Livraison Abidjan & Intérieur · Support WhatsApp 7j/7
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function cartPageComponent() {
    return {
        items: [],
        typeAchat: '{{ $cart->type_achat ?? "credit" }}',
        init() {
            this.fetchCartData();
            window.addEventListener('cart-updated', () => {
                this.fetchCartData();
            });
        },
        async fetchCartData() {
            try {
                const res = await fetch('/cart/data', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success && data.cart) {
                    this.items = data.cart.items;
                    this.typeAchat = data.cart.type_achat;
                    // Mettre à jour localStorage pour cohérence
                    if (window.CypressCart) {
                        window.CypressCart.save(this.items.map(i => ({
                            id: i.product_id,
                            nom: i.nom,
                            prix: i.prix,
                            monthly: i.monthly,
                            img: i.image,
                            qty: i.qty
                        })));
                    }
                }
            } catch (e) {
                console.error(e);
            }
        },
        get itemsCount() {
            return this.items.reduce((sum, i) => sum + i.qty, 0);
        },
        get subtotal() {
            return this.items.reduce((sum, i) => sum + (i.prix * i.qty), 0);
        },
        get discountAmount() {
            return this.typeAchat === 'cash' ? Math.round(this.subtotal * 0.07) : 0;
        },
        get finalTotal() {
            return Math.max(0, this.subtotal - this.discountAmount);
        },
        get monthlyTotal() {
            return this.items.reduce((sum, i) => sum + ((i.monthly || Math.round(i.prix / 12)) * i.qty), 0);
        },
        async updateQty(itemId, newQty) {
            try {
                const res = await fetch('/cart/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ item_id: itemId, quantite: newQty })
                });
                const data = await res.json();
                if (data.success) {
                    this.fetchCartData();
                }
            } catch (e) {
                console.error(e);
            }
        },
        async removeItem(itemId) {
            try {
                const res = await fetch('/cart/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ item_id: itemId })
                });
                const data = await res.json();
                if (data.success) {
                    this.fetchCartData();
                }
            } catch (e) {
                console.error(e);
            }
        },
        async clearAll() {
            if (!confirm('Vider tout le panier ?')) return;
            try {
                const res = await fetch('/cart/clear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.items = [];
                    if (window.CypressCart) window.CypressCart.clear();
                }
            } catch (e) {
                console.error(e);
            }
        },
        async setTypeAchat(type) {
            this.typeAchat = type;
            try {
                await fetch('/cart/step', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        step: 'cart',
                        data: { type_achat: type }
                    })
                });
            } catch (e) {}
        }
    };
}
</script>
@endsection

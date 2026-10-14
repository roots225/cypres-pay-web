<div
    x-data="{
        isOpen: false,
        cartItems: [],
        init() {
            this.cartItems = window.CypressCart ? window.CypressCart.getItems() : [];
            window.addEventListener('cart-updated', (e) => {
                this.cartItems = e.detail;
            });
            window.addEventListener('open-cart', () => {
                this.isOpen = true;
            });
            window.addEventListener('close-cart', () => {
                this.isOpen = false;
            });
            if (typeof Livewire !== 'undefined') {
                Livewire.on('open-cart', () => { this.isOpen = true; });
                Livewire.on('close-cart', () => { this.isOpen = false; });
            }
        },
        removeItem(id) {
            window.CypressCart.remove(id);
            this.cartItems = window.CypressCart.getItems();
        },
        clearCart() {
            if (confirm('Vider l\'intégralité du panier ?')) {
                window.CypressCart.clear();
                this.cartItems = [];
            }
        },
        get totalCash() {
            return this.cartItems.reduce((acc, item) => acc + (item.prix * (item.qty || 1)), 0);
        },
        get totalMonthly() {
            return this.cartItems.reduce((acc, item) => acc + ((item.monthly || Math.round(item.prix / 12)) * (item.qty || 1)), 0);
        }
    }"
    x-show="isOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-hidden"
    style="display: none;"
>
    <!-- Backdrop sombre -->
    <div
        @click="isOpen = false"
        class="absolute inset-0 bg-cobalt-deep/60 backdrop-blur-xs transition-opacity"
    ></div>

    <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <div
            x-show="isOpen"
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="w-screen max-w-md bg-surface-container-lowest shadow-2xl flex flex-col justify-between"
        >
            <!-- Header Tiroir -->
            <div class="p-5 border-b border-border-subtle bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="shopping-bag" class="w-6 h-6 text-primary-container"></i>
                    <div>
                        <h3 class="font-headline-sm text-[16px] font-bold text-on-surface">Mon Panier BNPL</h3>
                        <span class="text-[11px] text-text-muted">Équipement échelonné sans avance</span>
                    </div>
                </div>
                <button
                    @click="isOpen = false"
                    type="button"
                    class="w-8 h-8 rounded-full bg-white border border-border-subtle flex items-center justify-center text-text-muted hover:text-on-surface"
                >
                    <i data-lucide="x" class="w-4.5 h-4.5"></i>
                </button>
            </div>

            <!-- Contenu : Liste des articles -->
            <div class="flex-1 overflow-y-auto p-5 divide-y divide-border-subtle/60">
                <template x-if="cartItems.length === 0">
                    <div class="py-16 text-center">
                        <i data-lucide="shopping-cart" class="w-14 h-14 text-slate-300 mb-2 inline-block"></i>
                        <h4 class="font-bold text-[16px] text-on-surface">Votre panier est vide</h4>
                        <p class="text-[13px] text-text-muted mt-1 mb-5">Découvrez nos kits cuisine, électroménager et smartphones.</p>
                        <a
                            href="/catalogue"
                            @click="isOpen = false"
                            class="bg-primary-container text-white px-5 py-2.5 rounded-lg font-bold text-[13px] inline-flex items-center gap-2"
                        >
                            <i data-lucide="grid" class="w-4.5 h-4.5"></i>
                            Parcourir le catalogue
                        </a>
                    </div>
                </template>

                <template x-for="item in cartItems" :key="item.id">
                    <div class="py-4 flex items-center gap-3">
                        <img
                            :src="item.img || item.image || 'https://lh3.googleusercontent.com/aida/AEtjO1WKIfcW4_C9xDOAgn33b1esqTDNmDfZSduh3LYB-GPo-ViimVJsDJx-cUA3O26XDnEyjLdD4vcA13XYgpyJD4YZT3GxrQ_8UwuAHzGX_fr3XHM1UR-gJ1KmscgtyXzXhrjFFqrVTFxr1MomYD-NRZZxzWnOcN7P5vt-1N6hQ4Dqgk7VpXAv1LXY4h48LEKn_EohQGkGWjFA2gfgUCEzgVpRQP4ewzB2AVJxxy4po1mW2gL7b3R4ISndxQ'"
                            :alt="item.nom"
                            class="w-16 h-16 object-contain rounded-lg bg-slate-50 border border-border-subtle p-1 shrink-0"
                        />
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-[13px] text-on-surface line-clamp-1" x-text="item.nom"></h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="font-extrabold text-[14px] text-primary-container" x-text="((item.monthly || Math.round(item.prix / 12)) * (item.qty || 1)).toLocaleString('fr-FR') + ' FCFA/m'"></span>
                                <span class="text-[11px] text-text-muted" x-text="'Qté: ' + (item.qty || 1)"></span>
                            </div>
                            <span class="text-[11px] text-slate-400 block" x-text="'Comptant : ' + (item.prix * (item.qty || 1)).toLocaleString('fr-FR') + ' FCFA'"></span>
                        </div>
                        <button
                            @click="removeItem(item.id)"
                            type="button"
                            class="text-slate-300 hover:text-red-600 p-1 transition-colors"
                            title="Supprimer"
                        >
                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Footer : Récapitulatif BNPL & Checkout -->
            <template x-if="cartItems.length > 0">
                <div class="p-5 border-t border-border-subtle bg-slate-50 space-y-4">
                    <div class="space-y-2">
                        <div class="flex justify-between text-[13px] text-slate-600">
                            <span>Prix comptant total :</span>
                            <span class="font-bold" x-text="totalCash.toLocaleString('fr-FR') + ' FCFA'"></span>
                        </div>
                        <div class="flex justify-between text-[13px] text-emerald-700 font-bold bg-emerald-50 p-2 rounded-lg">
                            <span>Apport initial requis :</span>
                            <span>0 FCFA (Sans avance)</span>
                        </div>
                        <div class="flex justify-between items-baseline pt-2 border-t border-border-subtle">
                            <span class="font-bold text-on-surface text-[14px]">Mensualité globale estimée :</span>
                            <div class="text-right">
                                <span class="text-[24px] font-black text-primary-container" x-text="totalMonthly.toLocaleString('fr-FR')"></span>
                                <span class="text-[12px] font-bold text-slate-600"> FCFA/m</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <a
                            href="/commander"
                            class="w-full bg-primary-container hover:bg-brand-red-hover text-white py-3.5 rounded-xl font-bold text-[14px] flex items-center justify-center gap-2 transition-all shadow-md hover:shadow-lg"
                        >
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                            Valider ma commande à crédit
                        </a>
                        <button
                            @click="clearCart()"
                            type="button"
                            class="w-full py-2 text-center text-[12px] text-text-muted hover:text-red-600 font-semibold transition-colors"
                        >
                            Vider le panier
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

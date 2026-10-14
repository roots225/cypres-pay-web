// Cypress Pay - Core Application JavaScript
import './react.jsx';
import { createIcons, icons } from 'lucide';

// Rendre Lucide accessible mondialement
window.lucide = window.lucide || { createIcons, icons };

export function refreshLucideIcons() {
    try {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        } else {
            createIcons({ icons, attrs: { 'stroke-width': 2 } });
        }
    } catch (e) {
        // Fallback
    }
}
window.refreshLucideIcons = refreshLucideIcons;
window.refreshIcons = refreshLucideIcons;

// ── Gestionnaire de Panier Local & Synchronisation Base de Données
window.CypressCart = {
    key: 'cypress_cart_v1',
    getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    },
    getItems() {
        try {
            return JSON.parse(localStorage.getItem(this.key) || '[]');
        } catch {
            return [];
        }
    },
    save(items) {
        localStorage.setItem(this.key, JSON.stringify(items));
        this.updateBadge();
        window.dispatchEvent(new CustomEvent('cart-updated', { detail: items }));
    },
    open() {
        if (window.Livewire) {
            try { window.Livewire.dispatch('open-cart'); } catch(e) {}
        }
        window.dispatchEvent(new CustomEvent('open-cart'));
    },
    close() {
        if (window.Livewire) {
            try { window.Livewire.dispatch('close-cart'); } catch(e) {}
        }
        window.dispatchEvent(new CustomEvent('close-cart'));
    },
    add(item) {
        if (!item) return;

        // Si l'argument est un élément HTML (ex: bouton avec data attributes)
        if ((typeof HTMLElement !== 'undefined' && item instanceof HTMLElement) || (item && item.dataset && (item.dataset.id || item.dataset.productId || item.dataset.name || item.dataset.nom))) {
            item = {
                id: item.dataset.id || item.dataset.productId || 1,
                nom: item.dataset.name || item.dataset.nom || item.dataset.productName || 'Produit Cypress Pay',
                prix: item.dataset.price || item.dataset.prix || item.dataset.prixVente || 0,
                monthly: item.dataset.monthly || item.dataset.mensualite || 0,
                img: item.dataset.image || item.dataset.img || '',
                image: item.dataset.image || item.dataset.img || '',
                qty: item.dataset.qty || 1
            };
        }

        // Normalisation de l'article (supporte id, nom, prix, monthly, img ou image)
        const id = parseInt(item.id, 10) || 1;
        const nom = item.nom || item.name || 'Produit Cypress Pay';
        const prix = parseInt(item.prix || item.price || item.prix_vente || item.prix_credit, 10) || 0;
        const monthly = parseInt(item.monthly || item.mensualite || item.mensualite_min, 10) || Math.round(prix / 12);
        const img = item.img || item.image || item.image_url || '';
        const qty = parseInt(item.qty || item.quantite, 10) || 1;

        const items = this.getItems();
        const existing = items.find(i => parseInt(i.id, 10) === id);
        if (existing) {
            existing.qty = (existing.qty || 1) + qty;
        } else {
            items.push({
                id: id,
                nom: nom,
                prix: prix,
                monthly: monthly,
                img: img,
                image: img,
                qty: qty
            });
        }

        this.save(items);
        this.showToast(nom, monthly);
        this.open(); // Ouvre immédiatement le tiroir du panier pour une confirmation visuelle instantanée

        // Synchronisation en direct avec la base de données
        if (id && id < 1000000000) {
            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: id, quantite: qty })
            }).catch(e => console.warn('Cart DB sync error:', e));
        }
    },
    addItem(item) {
        return this.add(item);
    },
    remove(id) {
        const targetId = parseInt(id, 10);
        const items = this.getItems().filter(i => parseInt(i.id, 10) !== targetId);
        this.save(items);

        // Suppression en base
        if (targetId && targetId < 1000000000) {
            fetch('/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ item_id: targetId })
            }).catch(() => {});
        }
    },
    clear() {
        localStorage.removeItem(this.key);
        this.updateBadge();
        window.dispatchEvent(new CustomEvent('cart-updated', { detail: [] }));

        // Vider en base
        fetch('/cart/clear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCsrfToken(),
                'Accept': 'application/json'
            }
        }).catch(() => {});
    },
    async syncWithServer() {
        try {
            const res = await fetch('/cart/data', { headers: { 'Accept': 'application/json' } });
            const json = await res.json();
            if (json.success && json.cart) {
                if (json.cart.items && json.cart.items.length > 0) {
                    const mapped = json.cart.items.map(i => ({
                        id: i.product_id,
                        nom: i.nom,
                        prix: i.prix,
                        monthly: i.monthly,
                        img: i.image,
                        qty: i.qty
                    }));
                    localStorage.setItem(this.key, JSON.stringify(mapped));
                    this.updateBadge();
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: mapped }));
                } else {
                    const localItems = this.getItems();
                    if (localItems.length > 0) {
                        fetch('/cart/sync', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ items: localItems })
                        }).catch(() => {});
                    }
                }
            }
        } catch (e) {}
    },
    count() {
        return this.getItems().reduce((total, i) => total + (i.qty || 1), 0);
    },
    totalCash() {
        return this.getItems().reduce((total, i) => total + (i.prix * (i.qty || 1)), 0);
    },
    totalMonthly() {
        return this.getItems().reduce((total, i) => total + ((i.monthly || Math.round(i.prix / 12)) * (i.qty || 1)), 0);
    },
    updateBadge() {
        const badges = document.querySelectorAll('.cart-count-badge');
        const count = this.count();
        badges.forEach(b => {
            b.textContent = count > 99 ? '99+' : count;
            b.style.display = count > 0 ? 'flex' : 'none';
        });
        const totalDisplays = document.querySelectorAll('.cart-total-display');
        totalDisplays.forEach(td => {
            td.textContent = this.totalMonthly().toLocaleString('fr-FR') + ' FCFA';
        });
    },
    showToast(title, monthly) {
        const toast = document.getElementById('live-toast');
        if (!toast) return;
        toast.classList.remove('hidden');
        toast.innerHTML = `
            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>
            </div>
            <div class="flex flex-col text-left">
                <span class="font-bold text-[12px] text-emerald-600">Ajouté au panier BNPL</span>
                <span class="text-[13px] font-semibold text-slate-800 line-clamp-1">${title}</span>
                <span class="font-extrabold text-[12px] text-primary-container">${(monthly || 0).toLocaleString('fr-FR')} FCFA/mois</span>
            </div>
        `;
        refreshLucideIcons();
        toast.style.transform = 'translateY(0)';
        setTimeout(() => {
            toast.style.transform = 'translateY(120%)';
            setTimeout(() => toast.classList.add('hidden'), 300);
        }, 4000);
    }
};

// ── Notifications Récentes de Commandes (Preuve Sociale Côte d'Ivoire)
const livePurchases = [
    { name: "K***", city: "Yopougon, Abidjan", product: "Pack Électro Msart N°019", monthly: "6 500 FCFA/m", time: "Il y a 4 min" },
    { name: "A***", city: "Cocody Angré, Abidjan", product: "iPhone 15 Pro 128Go", monthly: "32 500 FCFA/m", time: "Il y a 12 min" },
    { name: "T***", city: "Lakota", product: "Kit Cuisine Gazinière N°018", monthly: "12 500 FCFA/m", time: "Il y a 18 min" },
    { name: "S***", city: "Bouaké Commerce", product: "Fontaine d'Eau Millennium N°731", monthly: "8 000 FCFA/m", time: "Il y a 25 min" },
    { name: "O***", city: "Koumassi Remblais", product: "Kit Alimentaire Maxi Famille N°012", monthly: "3 500 FCFA/m", time: "Il y a 32 min" },
    { name: "M***", city: "San-Pedro", product: "Pagne Super Wax Hollandais N°717", monthly: "3 000 FCFA/m", time: "Il y a 45 min" }
];

let purchaseIndex = 0;
function triggerSocialProof() {
    const toast = document.getElementById('live-toast');
    if (!toast) return;
    const p = livePurchases[purchaseIndex % livePurchases.length];
    purchaseIndex++;

    toast.classList.remove('hidden');
    toast.innerHTML = `
        <div class="w-10 h-10 rounded-full bg-brand-red-subtle text-primary-container flex items-center justify-center shrink-0">
            <i data-lucide="shopping-bag" class="w-5 h-5 text-primary-container"></i>
        </div>
        <div class="flex flex-col text-left">
            <span class="font-bold text-[12px] text-slate-800">${p.name} (${p.city})</span>
            <span class="text-[12px] text-slate-500">A souscrit au <strong class="text-slate-700">${p.product}</strong></span>
            <span class="text-[11px] font-bold text-primary-container">${p.time} · ${p.monthly}</span>
        </div>
    `;
    refreshLucideIcons();
    toast.style.transform = 'translateY(0)';
    setTimeout(() => {
        toast.style.transform = 'translateY(120%)';
        setTimeout(() => toast.classList.add('hidden'), 350);
    }, 5500);
}

// ── Initialisation au chargement
document.addEventListener('DOMContentLoaded', () => {
    window.CypressCart.updateBadge();
    window.CypressCart.syncWithServer();
    refreshLucideIcons();
    setTimeout(triggerSocialProof, 7000);
    setInterval(triggerSocialProof, 28000);
});

// Hooks Livewire pour maintenir les icônes après mises à jour partielles du DOM
document.addEventListener('livewire:navigated', refreshLucideIcons);
document.addEventListener('livewire:initialized', () => {
    if (window.Livewire) {
        Livewire.hook('morph.updated', () => queueMicrotask(refreshLucideIcons));
        Livewire.hook('commit', ({ succeed }) => {
            succeed(() => queueMicrotask(refreshLucideIcons));
        });
    }
});

// Pont d'ajout au panier universel (supporte élément bouton, objet ou arguments positionnels)
window.addToCart = function(...args) {
    if (args.length === 0) return;

    if (typeof HTMLElement !== 'undefined' && (args[0] instanceof HTMLElement || (args[0] && args[0].dataset && (args[0].dataset.id || args[0].dataset.name || args[0].dataset.nom)))) {
        window.CypressCart.add(args[0]);
        return;
    }

    if (typeof args[0] === 'object' && args[0] !== null) {
        // Appelé avec un objet : window.addToCart({ id: 1, nom: '...', prix: 10000, ... })
        window.CypressCart.add(args[0]);
    } else if (typeof args[0] === 'string') {
        // Appelé avec signature : (nom, prix, monthly, img, id)
        const nom = args[0];
        const prix = args[1];
        const monthly = args[2];
        const img = args[3];
        const id = args[4] || 1;
        window.CypressCart.add({ id, nom, prix, monthly, img, image: img });
    } else if (typeof args[0] === 'number') {
        // Appelé avec signature : (id, nom, prix, monthly, img)
        const id = args[0];
        const nom = args[1];
        const prix = args[2];
        const monthly = args[3];
        const img = args[4];
        window.CypressCart.add({ id, nom, prix, monthly, img, image: img });
    }
};

window.addToCartFromButton = function(btn) {
    window.addToCart(btn);
};

// Écouteur délégué global pour tous les boutons avec l'attribut data-add-to-cart
document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-add-to-cart]');
    if (btn) {
        e.preventDefault();
        window.addToCart(btn);
    }
});

// Rejouer la file d'attente éventuelle des clics prématurés
if (window.cypressCartQueue && Array.isArray(window.cypressCartQueue)) {
    const queue = [...window.cypressCartQueue];
    window.cypressCartQueue = [];
    queue.forEach(args => {
        window.addToCart(...args);
    });
}

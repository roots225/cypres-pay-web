@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Fil d'Ariane -->
    <nav class="flex items-center gap-2 text-[12px] text-text-muted mb-6 font-semibold">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1.5">
            <i data-lucide="home" class="w-4 h-4"></i> Accueil
        </a>
        <span>/</span>
        <a href="{{ route('catalog') }}" class="hover:text-primary transition-colors">Catalogue</a>
        @if($product->category)
            <span>/</span>
            <a href="{{ route('catalog.category', $product->category->slug) }}" class="hover:text-primary transition-colors">
                {{ $product->category->nom }}
            </a>
        @endif
        <span>/</span>
        <span class="text-on-surface truncate">{{ $product->nom }}</span>
    </nav>

    <!-- Fiche Produit Principale -->
    <div class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 lg:p-10 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-border-subtle/60">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            
            <!-- Galerie Interactive Multi-Images du Produit -->
            <div 
                class="lg:col-span-5 flex flex-col gap-4"
                x-data="productPublicGallery({{ json_encode($product->all_images) }})"
            >
                <!-- Cadre Visuel Principal avec Zoom et Flèches de Navigation -->
                <div class="relative bg-canvas-background rounded-3xl p-6 flex items-center justify-center border border-border-subtle aspect-square overflow-hidden group shadow-xs">
                    <!-- Image Active -->
                    <img 
                        :src="currentImage" 
                        alt="{{ $product->nom }}"
                        class="max-h-full max-w-full object-contain transition-all duration-300 group-hover:scale-105 cursor-zoom-in"
                        @click="openLightbox()"
                    />

                    <!-- Badges Flottants Haut Gauche -->
                    <div class="absolute top-4 left-4 flex flex-col gap-1.5 z-10">
                        @if($product->numero_reference)
                            <span class="px-2.5 py-1 rounded-md bg-cobalt-deep text-white text-[11px] font-black tracking-wide shadow-xs">
                                {{ $product->numero_reference }}
                            </span>
                        @endif
                        @if($product->est_vedette)
                            <span class="px-2.5 py-1 rounded-md bg-provider-mtn text-cobalt-deep text-[11px] font-black shadow-xs">
                                Coup de Cœur
                            </span>
                        @endif
                        @if($product->stock_disponible > 0 && $product->stock_disponible <= 5)
                            <span class="px-2.5 py-1 rounded-md bg-amber-500 text-white text-[11px] font-extrabold flex items-center gap-1 shadow-xs">
                                <i data-lucide="zap" class="w-3.5 h-3.5"></i> Plus que {{ $product->stock_disponible }} en stock !
                            </span>
                        @endif
                    </div>

                    <!-- Compteur de photos & Bouton Zoom Haut Droite -->
                    <div class="absolute top-4 right-4 flex items-center gap-1.5 z-10">
                        <template x-if="images.length > 1">
                            <span class="px-2 py-0.5 rounded-full bg-slate-900/60 backdrop-blur-sm text-white text-[10px] font-black shadow-xs">
                                <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                            </span>
                        </template>
                        <button 
                            type="button" 
                            @click="openLightbox()"
                            class="w-8 h-8 rounded-full bg-white/80 hover:bg-white text-slate-700 hover:text-slate-950 flex items-center justify-center shadow-xs transition-transform hover:scale-110"
                            title="Agrandir la photo"
                        >
                            <i data-lucide="maximize-2" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Flèches Navigation Rapide (si plusieurs photos) -->
                    <template x-if="images.length > 1">
                        <div>
                            <button 
                                type="button" 
                                @click="prevImage()"
                                class="absolute left-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 hover:bg-white text-slate-800 shadow-md flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all hover:scale-110 cursor-pointer z-10"
                                title="Image précédente"
                            >
                                <i data-lucide="chevron-left" class="w-5 h-5"></i>
                            </button>
                            <button 
                                type="button" 
                                @click="nextImage()"
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 hover:bg-white text-slate-800 shadow-md flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all hover:scale-110 cursor-pointer z-10"
                                title="Image suivante"
                            >
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </template>

                    <!-- Badge Garantie Bas Droite -->
                    <div class="absolute bottom-4 right-4 z-10">
                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-tertiary text-[11px] font-bold flex items-center gap-1 border border-emerald-200 shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            Neuf avec Garantie
                        </span>
                    </div>
                </div>

                <!-- Bandeau des Vignettes (Thumbnails) Interactives -->
                <template x-if="images.length > 1">
                    <div class="flex items-center gap-2.5 overflow-x-auto pb-1 scrollbar-none">
                        <template x-for="(img, idx) in images" :key="idx">
                            <button 
                                type="button" 
                                @click="selectImage(idx)"
                                @mouseenter="selectImage(idx)"
                                class="relative w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-canvas-background border-2 overflow-hidden shrink-0 transition-all cursor-pointer p-1"
                                :class="currentIndex === idx ? 'border-primary ring-2 ring-primary/20 scale-105 shadow-sm' : 'border-border-subtle hover:border-slate-400 opacity-70 hover:opacity-100'"
                            >
                                <img :src="img" class="w-full h-full object-contain rounded-xl" />
                            </button>
                        </template>
                    </div>
                </template>

                <!-- Modal Lightbox Plein Écran -->
                <div 
                    x-show="lightbox" 
                    x-cloak
                    class="fixed inset-0 z-[100] bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4"
                    @keydown.escape.window="lightbox = false"
                    @keydown.left.window="prevImage()"
                    @keydown.right.window="nextImage()"
                >
                    <button 
                        @click="lightbox = false" 
                        class="absolute top-5 right-5 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer"
                    >
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>

                    <button 
                        x-show="images.length > 1"
                        @click="prevImage()" 
                        class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer"
                    >
                        <i data-lucide="chevron-left" class="w-7 h-7"></i>
                    </button>

                    <button 
                        x-show="images.length > 1"
                        @click="nextImage()" 
                        class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer"
                    >
                        <i data-lucide="chevron-right" class="w-7 h-7"></i>
                    </button>

                    <div class="max-w-4xl max-h-[85vh] flex flex-col items-center justify-center">
                        <img :src="currentImage" class="max-h-[80vh] max-w-full object-contain rounded-2xl shadow-2xl" />
                        <div class="mt-4 text-white text-[13px] font-bold" x-show="images.length > 1">
                            <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span> — {{ $product->nom }}
                        </div>
                    </div>
                </div>

                <!-- Points de Réassurance Rapides sous l'image -->
                <div class="grid grid-cols-3 gap-2 text-center text-[11px] font-semibold text-text-muted">
                    <div class="p-2.5 rounded-xl bg-canvas-background border border-border-subtle/80 flex flex-col items-center gap-1.5">
                        <i data-lucide="truck" class="w-5 h-5 text-primary-container"></i>
                        <span>Livraison 72h CI</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-canvas-background border border-border-subtle/80 flex flex-col items-center gap-1.5">
                        <i data-lucide="shield-check" class="w-5 h-5 text-tertiary"></i>
                        <span>Garantie 12 Mois</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-canvas-background border border-border-subtle/80 flex flex-col items-center gap-1.5">
                        <i data-lucide="banknote" class="w-5 h-5 text-secondary"></i>
                        <span>Wave & MoMo</span>
                    </div>
                </div>
            </div>

            <!-- Détails & Moteur d'Options BNPL -->
            <div class="lg:col-span-7 flex flex-col gap-6">
                <!-- En-tête Titre & Marque -->
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[12px] font-extrabold uppercase text-secondary tracking-wider">
                            {{ $product->marque ?? 'Cypress Pay Certifié' }}
                        </span>
                        <span class="text-text-muted">·</span>
                        <span class="text-[12px] text-emerald-600 font-bold flex items-center gap-1">
                            <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> En stock
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-on-surface leading-tight">
                        {{ $product->nom }}
                    </h1>
                    <p class="text-[14px] text-text-muted mt-2 leading-relaxed">
                        {{ $product->description }}
                    </p>
                </div>

                <!-- Composant React : Sélecteur Interactif Cash vs Crédit BNPL -->
                <div 
                    id="react-product-installment-selector"
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->nom }}"
                    data-cash-price="{{ $product->prix_cash }}"
                    data-credit-price="{{ $product->prix_credit }}"
                    data-min-monthly="{{ $product->mensualite_min }}"
                    data-image-url="{{ $product->image_url }}"
                >
                    <!-- Fallback Blade statique si JavaScript désactivé -->
                    <div class="p-5 rounded-2xl bg-canvas-background border border-border-subtle">
                        <div class="flex items-baseline justify-between mb-3">
                            <span class="text-2xl font-black text-primary-container">
                                {{ number_format($product->prix_cash, 0, ',', ' ') }} FCFA
                            </span>
                            <span class="text-[12px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                                Remise Cash -7%
                            </span>
                        </div>
                        <div class="text-[13px] text-slate-600">
                            Ou en paiement échelonné dès <strong class="text-secondary font-black">{{ number_format($product->mensualite_min, 0, ',', ' ') }} FCFA/mois</strong>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'Action & Commande Directe -->
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button
                        type="button"
                        data-add-to-cart
                        data-id="{{ $product->id }}"
                        data-name="{{ $product->nom }}"
                        data-price="{{ $product->prix_credit }}"
                        data-monthly="{{ $product->mensualite_min }}"
                        data-image="{{ $product->image_url }}"
                        onclick="window.addToCart(this)"
                        class="flex-1 bg-primary-container hover:bg-brand-red-hover text-white text-[14px] font-extrabold py-3.5 px-6 rounded-xl shadow-lg shadow-primary-container/20 flex items-center justify-center gap-2 transition-all active:scale-[0.98] cursor-pointer"
                    >
                        <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                        <span>Ajouter au Panier BNPL</span>
                    </button>

                    <a 
                        href="https://wa.me/2250586708161?text={{ urlencode('Bonjour Cypress Pay, je souhaite commander : ' . $product->nom . ' (Réf ' . $product->numero_reference . ') au prix de ' . number_format($product->prix_credit, 0, ',', ' ') . ' FCFA.') }}"
                        target="_blank"
                        rel="noopener"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-[14px] font-bold py-3.5 px-5 rounded-xl flex items-center justify-center gap-2 transition-all shrink-0"
                    >
                        <i data-lucide="message-circle" class="w-5 h-5"></i>
                        <span>Conseiller WhatsApp</span>
                    </a>
                </div>

                <!-- Encadré Conditions BNPL Côte d'Ivoire -->
                <div class="p-4 rounded-2xl bg-cobalt-subtle border border-blue-100 flex items-start gap-3">
                    <i data-lucide="info" class="w-6 h-6 text-secondary shrink-0 mt-0.5"></i>
                    <div class="text-[12px] text-cobalt-deep leading-relaxed">
                        <strong class="font-bold block mb-1">Pièces requises pour le paiement échelonné :</strong>
                        1. Carte Nationale d'Identité (CNI) ou Passeport valide.<br/>
                        2. Numéro de compte Mobile Money (Wave, Orange, MTN ou Moov) approvisionné pour l'apport.<br/>
                        3. Contact d'une personne de référence. Aucun relevé bancaire exigé !
                    </div>
                </div>

            </div>
        </div>

        <!-- Caractéristiques Techniques Détaillées -->
        @if(!empty($product->caracteristiques) && is_array($product->caracteristiques))
            <div class="mt-12 pt-8 border-t border-border-subtle">
                <h3 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
                    <i data-lucide="sliders" class="w-5 h-5 text-secondary"></i>
                    Fiche Technique & Caractéristiques
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($product->caracteristiques as $key => $value)
                        <div class="p-3.5 rounded-xl bg-canvas-background border border-border-subtle/80 flex justify-between items-center text-[13px]">
                            <span class="text-text-muted font-medium">{{ ucfirst($key) }}</span>
                            <span class="font-bold text-on-surface">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Produits Similaires Recommandés -->
    @if($relatedProducts->count() > 0)
        <div class="mt-16">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-extrabold text-on-surface">Vous pourriez aussi aimer</h2>
                    <p class="text-[13px] text-text-muted">Articles similaires dans la même catégorie</p>
                </div>
                <a href="{{ route('catalog') }}" class="text-primary font-bold text-[13px] hover:underline flex items-center gap-1.5">
                    Voir tout <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-7 lg:gap-8">
                @foreach($relatedProducts as $rel)
                    <div class="bg-surface-container-lowest rounded-2xl p-5 border border-border-subtle/80 hover:shadow-xl transition-all flex flex-col justify-between group">
                        <div>
                            <!-- Photo cliquable -->
                            <a href="{{ route('product.show', [$rel->id, $rel->slug]) }}" class="aspect-square bg-canvas-background rounded-xl p-3 flex items-center justify-center mb-3.5 overflow-hidden block group-hover:opacity-95 cursor-pointer" title="{{ $rel->nom }}">
                                <img src="{{ $rel->image_principale }}" alt="{{ $rel->nom }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300"/>
                            </a>
                            <span class="text-[11px] font-extrabold text-secondary">{{ $rel->reference }}</span>
                            <!-- Nom cliquable -->
                            <h3 class="font-bold text-[14px] text-on-surface line-clamp-2 mt-1 group-hover:text-primary transition-colors">
                                <a href="{{ route('product.show', [$rel->id, $rel->slug]) }}" class="hover:text-primary transition-colors block">
                                    {{ $rel->nom }}
                                </a>
                            </h3>
                        </div>
                        <div class="mt-4 pt-3 border-t border-border-subtle flex items-center justify-between">
                            <div>
                                <span class="text-[11px] text-text-muted block">Dès</span>
                                <span class="text-[15px] font-black text-primary-container">{{ $rel->mensualite_formatee }}</span>
                            </div>
                            <a href="{{ route('product.show', [$rel->id, $rel->slug]) }}" class="px-3.5 py-1.5 rounded-xl bg-surface-container-high hover:bg-brand-red-subtle hover:text-primary text-[12px] font-bold transition-colors">
                                Découvrir
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function productPublicGallery(imagesList) {
    const list = Array.isArray(imagesList) && imagesList.length > 0 ? imagesList : ['https://placehold.co/600x600/f8fafc/94a3b8?text=Image+Produit'];
    return {
        images: list,
        currentIndex: 0,
        lightbox: false,

        get currentImage() {
            return this.images[this.currentIndex] || this.images[0];
        },

        selectImage(index) {
            if (index >= 0 && index < this.images.length) {
                this.currentIndex = index;
            }
        },

        nextImage() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
        },

        prevImage() {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        },

        openLightbox() {
            this.lightbox = true;
        }
    };
}
</script>
@endpush
@endsection

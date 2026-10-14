@extends('layouts.app', ['title' => "Cypress Pay - Équipez votre maison dès aujourd'hui, payez à votre rythme !"])

@section('content')
<div class="flex flex-col w-full">

    <!-- ── 1. HERO SECTION PREMIUM ──────────────────────────────────────── -->
    <section class="relative w-full overflow-hidden bg-gradient-to-b from-surface-container-low to-canvas-background py-8 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                
                <!-- Texte Hero & Badges -->
                <div class="lg:col-span-7 flex flex-col gap-4 z-10">
                    <div class="inline-flex items-center gap-1.5 bg-brand-red-subtle text-primary font-bold text-[12px] px-3.5 py-1.5 rounded-full w-fit">
                        <i data-lucide="flame" class="w-4 h-4 text-amber-500 animate-pulse"></i>
                        <span>Nouveau facilitateur d'achats en Côte d'Ivoire</span>
                    </div>

                    <h1 class="font-extrabold text-[32px] md:text-[50px] text-on-surface tracking-tight leading-[1.12]">
                        Équipez votre maison dès aujourd’hui, <span class="text-primary-container">payez à votre rythme !</span>
                    </h1>

                    <p class="text-[15px] md:text-[17px] text-text-muted max-w-xl leading-relaxed">
                        Paiement échelonné jusqu’à 24 mois sans stress ni frais cachés. Livraison rapide en 72h chrono partout à Abidjan et à l'intérieur du pays.
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a class="bg-primary-container hover:bg-brand-red-hover text-on-primary font-bold text-[15px] px-6 py-3.5 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center gap-2" href="{{ route('kits') }}">
                            <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                            Découvrir les kits maison
                        </a>
                        <a class="bg-surface-container-lowest hover:bg-cobalt-subtle text-secondary font-bold text-[15px] px-6 py-3.5 rounded-xl border border-border-subtle shadow-xs transition-all flex items-center gap-2" href="{{ route('simulator') }}">
                            <i data-lucide="calculator" class="w-5 h-5"></i>
                            Simuler mes mensualités
                        </a>
                    </div>

                    <!-- Trust Badges sous CTA -->
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 pt-6 border-t border-border-subtle/80 max-w-lg">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                                <i data-lucide="shield-check" class="w-4.5 h-4.5"></i>
                            </div>
                            <span class="text-[11.5px] font-bold text-on-surface leading-tight">100% Produits Garantis</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-secondary flex items-center justify-center shrink-0">
                                <i data-lucide="wallet" class="w-4.5 h-4.5"></i>
                            </div>
                            <span class="text-[11.5px] font-bold text-on-surface leading-tight">Wave, Orange, MTN MoMo</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-red-50 text-primary-container flex items-center justify-center shrink-0">
                                <i data-lucide="truck" class="w-4.5 h-4.5"></i>
                            </div>
                            <span class="text-[11.5px] font-bold text-on-surface leading-tight">Livraison 72h Chrono</span>
                        </div>
                    </div>
                </div>

                <!-- Showcase Visuel Bento & Cartes Flottantes -->
                <div class="lg:col-span-5 relative">
                    <div class="relative bg-surface-container-lowest p-3 rounded-2xl shadow-xl overflow-hidden border border-border-subtle">
                        <div class="relative w-full h-[320px] md:h-[380px] rounded-xl overflow-hidden group">
                            <img
                                alt="Kits Maison et Cuisine Cypress Pay"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1W_My-uT8xN9AJ5LKc1cGC2uvc-CxBHoIbKiyl7KdZAtSs5mKydHvlOl3mJA6E9CUH7E6t7fDnfDOfpEmDnIFz8XaAqweDpyX88zRl8LyMRUwtkOVrSXASpo6w9ddaQ1eDy_NKbL9BJrJUeuAz0YE32hCxJacJqRrJNkLUJ186oEqsYwIBkBf8q1HOu0zOr1vU32j3NlHFPjk9vkISBIcbUQulywk-52qp6nVlJ6L7uZ5aZb8Tdcg44"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-cobalt-deep/85 via-cobalt-deep/20 to-transparent flex flex-col justify-end p-5 text-white">
                                <span class="bg-primary-container text-white font-extrabold text-[10px] px-2.5 py-1 rounded w-fit mb-1.5 uppercase tracking-wider">
                                    Offre Populaire
                                </span>
                                <h2 class="text-[20px] md:text-[22px] font-black leading-tight text-white">
                                    Packs Cuisine & Électroménager
                                </h2>
                                <p class="text-[13px] text-slate-200 mt-0.5">Dès 450 FCFA/jour ou versement mensuel allégé</p>
                            </div>
                        </div>

                        <!-- Badge Flottant Produit Mobile -->
                        <div class="absolute -bottom-3 -left-2 bg-surface-container-lowest p-3 rounded-xl shadow-2xl flex items-center gap-3 max-w-[260px] border border-border-subtle animate-bounce-subtle">
                            <img
                                alt="Smartphones & High-Tech"
                                class="w-12 h-12 object-cover rounded-lg"
                                src="https://lh3.googleusercontent.com/aida/AEtjO1V7SyyQgbRqYbHCfdWfFjMAkksGpC9VTikuPl1c5jr88wvbEKr7PAEapvwAnytF1mP9IHWZI_yNtcRgmRiGOe3BgLRyiXBiYYQeYQV4-FHPFRG7ZLGg3ihB9mQvTX9BCpr6omhX0cwEsAk0CQW6uEDTG92nYKjhFQy-O4lfOr_PNoiHx-3jjKN_POy9EcKtN8xJwMMIJOAS5Q5-OptXI5cQFj5K27DkG9gFdfzbyKV7zpwS4Zrl1Z1D"
                            />
                            <div class="flex flex-col text-left">
                                <span class="text-[10px] font-bold text-text-muted">High-Tech & Mobiles</span>
                                <span class="text-[13px] font-extrabold text-on-surface">iPhone & Samsung</span>
                                <span class="text-[11px] font-black text-primary-container">Dès 4 500 FCFA/m</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 2. TRUST BAR & MÉTRIQUES CLÉS ────────────────────────────────── -->
    <section class="w-full bg-surface-container-lowest py-8 shadow-xs border-y border-border-subtle">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-canvas-background transition-colors">
                    <div class="w-12 h-12 rounded-2xl bg-brand-red-subtle flex items-center justify-center shrink-0">
                        <i data-lucide="truck" class="w-6 h-6 text-primary-container"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-[15px] text-on-surface">Livraison rapide</span>
                        <span class="text-[12px] text-text-muted">Sous 72h à Abidjan & Intérieur</span>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-canvas-background transition-colors">
                    <div class="w-12 h-12 rounded-2xl bg-brand-red-subtle flex items-center justify-center shrink-0">
                        <i data-lucide="banknote" class="w-6 h-6 text-primary-container"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-[15px] text-on-surface">Paiement 100% sécurisé</span>
                        <span class="text-[12px] text-text-muted">Wave · Orange Money · MTN MoMo</span>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-canvas-background transition-colors">
                    <div class="w-12 h-12 rounded-2xl bg-brand-red-subtle flex items-center justify-center shrink-0">
                        <i data-lucide="badge-percent" class="w-6 h-6 text-primary-container"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-[15px] text-on-surface">Paiement flexible</span>
                        <span class="text-[12px] text-text-muted">De 30 jours à 24 mois sans frais</span>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-canvas-background transition-colors">
                    <div class="w-12 h-12 rounded-2xl bg-brand-red-subtle flex items-center justify-center shrink-0">
                        <i data-lucide="phone-call" class="w-6 h-6 text-primary-container"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-[15px] text-on-surface">Support 7j/7</span>
                        <span class="text-[12px] text-text-muted">+225 05 86 70 81 61 (WhatsApp)</span>
                    </div>
                </div>
            </div>

            <!-- Métriques de Confiance -->
            <div class="mt-8 pt-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center border-t border-border-subtle">
                <div class="flex flex-col">
                    <span class="text-[32px] md:text-[40px] font-black text-primary-container leading-none">5 000+</span>
                    <span class="text-[12px] font-bold text-text-muted mt-1">Clients actifs en Côte d'Ivoire</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[32px] md:text-[40px] font-black text-secondary leading-none">10 000+</span>
                    <span class="text-[12px] font-bold text-text-muted mt-1">Commandes et kits livrés</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[32px] md:text-[40px] font-black text-cobalt-deep leading-none">24 mois</span>
                    <span class="text-[12px] font-bold text-text-muted mt-1">Durée maximale d'échéances</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[32px] md:text-[40px] font-black text-emerald-700 leading-none">99%</span>
                    <span class="text-[12px] font-bold text-text-muted mt-1">Taux d'approbation et satisfaction</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 3. VENTES FLASH AVEC COMPTE À REBOURS ───────────────────────── -->
    <section class="w-full py-12 md:py-16 bg-canvas-background">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="zap" class="w-7 h-7 text-primary-container"></i>
                        <h2 class="text-[26px] md:text-[30px] font-black text-on-surface">Ventes Flash — Offres Limitées</h2>
                    </div>
                    <p class="text-[14px] text-text-muted mt-0.5">Saisissez les meilleures opportunités avec des mensualités réduites au minimum.</p>
                </div>

                <div class="flex items-center gap-4">
                    @if(class_exists(\App\Livewire\FlashSaleCountdown::class))
                        @livewire('flash-sale-countdown')
                    @endif
                    <a class="text-[14px] font-bold text-primary-container hover:text-brand-red-hover flex items-center gap-1.5" href="{{ route('flash') }}">
                        Tout voir <i data-lucide="arrow-right" class="w-4.5 h-4.5"></i>
                    </a>
                </div>
            </div>

            <!-- Grille Flash avec Espacement Généreux (Spacing) & Liens Fiche Produit -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-7 lg:gap-8">
                @foreach($flashProducts as $prod)
                    <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-xs hover:shadow-lg border border-border-subtle/80 transition-all flex flex-col justify-between group">
                        <div>
                            <!-- Photo cliquable avec aperçu multi-images -->
                            <a href="{{ route('product.show', [$prod->id, $prod->slug]) }}" class="relative w-full aspect-square bg-canvas-background rounded-xl overflow-hidden flex items-center justify-center mb-3.5 block cursor-pointer" title="{{ $prod->nom }}">
                                <img
                                    src="{{ $prod->image_principale }}"
                                    alt="{{ $prod->nom }}"
                                    class="object-contain p-2 max-h-full transition-all duration-300 {{ $prod->has_multiple_images ? 'group-hover:opacity-0 group-hover:scale-95' : 'group-hover:scale-105' }}"
                                    loading="lazy"
                                />
                                @if($prod->has_multiple_images && $prod->secondary_image_url)
                                    <img
                                        src="{{ $prod->secondary_image_url }}"
                                        alt="{{ $prod->nom }} - vue secondaire"
                                        class="absolute inset-0 w-full h-full object-contain p-2 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:scale-105"
                                        loading="lazy"
                                    />
                                @endif
                                <span class="absolute top-2 left-2 bg-emerald-600 text-white text-[10px] font-extrabold px-2 py-0.5 rounded shadow-xs z-10">
                                    -7% CASH
                                </span>
                                @if($prod->has_multiple_images)
                                    <span class="absolute bottom-2 right-2 bg-slate-900/70 backdrop-blur-xs text-white text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1 z-10 shadow-xs">
                                        <span class="material-symbols-outlined text-[12px]">photo_library</span>
                                        <span>{{ $prod->images_count }}</span>
                                    </span>
                                @endif
                            </a>

                            <div class="flex items-center gap-0.5 text-amber-400 mb-1">
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400"></i>
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400"></i>
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400"></i>
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400"></i>
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400"></i>
                                <span class="text-[11px] text-text-muted ml-1">(24 avis)</span>
                            </div>

                            <!-- Nom cliquable -->
                            <h3 class="font-bold text-[14px] text-on-surface line-clamp-2 mb-2 group-hover:text-primary transition-colors">
                                <a href="{{ route('product.show', [$prod->id, $prod->slug]) }}" class="hover:text-primary transition-colors block">
                                    {{ $prod->nom }}
                                </a>
                            </h3>

                            <div class="flex items-baseline gap-2 mb-2">
                                <span class="text-[12px] line-through text-text-light">{{ $prod->prix_formate }}</span>
                                <span class="text-[14px] font-extrabold text-on-surface">{{ $prod->prix_cash_formate }}</span>
                            </div>

                            <div class="bg-brand-red-subtle rounded-xl p-2.5 mb-3.5 border border-primary-container/15">
                                <span class="text-[11px] font-bold text-text-muted block">Mensualité flexible</span>
                                <span class="text-[18px] font-black text-primary-container">{{ $prod->mensualite_formatee }}</span>
                            </div>
                        </div>

                        <button
                            type="button"
                            data-add-to-cart
                            data-id="{{ $prod->id }}"
                            data-name="{{ $prod->nom }}"
                            data-price="{{ $prod->prix_vente }}"
                            data-monthly="{{ $prod->mensualite_indicative ?? round($prod->prix_vente / 12) }}"
                            data-image="{{ $prod->image_principale }}"
                            onclick="window.addToCart(this)"
                            class="w-full bg-primary-container hover:bg-brand-red-hover text-white py-2.5 rounded-xl font-bold text-[13px] flex items-center justify-center gap-2 transition-colors shadow-xs cursor-pointer"
                        >
                            <i data-lucide="shopping-cart" class="w-4.5 h-4.5"></i>
                            Ajouter au panier
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ── 4. SECTION VEDETTE : KITS MAISON & ALIMENTATION ─────────────── -->
    <section class="w-full py-12 md:py-16 bg-surface-container-low" id="kits-vedettes">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <div class="inline-flex items-center gap-1 text-primary-container text-[11px] font-extrabold uppercase tracking-wider mb-1">
                        <i data-lucide="infinity" class="w-4 h-4"></i> L'Exclusivité Cypress Pay
                    </div>
                    <h2 class="text-[26px] md:text-[30px] font-black text-on-surface">Nos Kits Tendances : Maison & Alimentation</h2>
                    <p class="text-[14px] text-text-muted mt-0.5">Des ensembles complets tout-en-un pour soulager votre quotidien, payables par jour ou par mois.</p>
                </div>

                <a class="bg-secondary text-white hover:bg-on-secondary-container text-[13px] font-bold px-5 py-2.5 rounded-xl flex items-center gap-2 transition-colors shrink-0 shadow-xs" href="{{ route('kits') }}">
                    <i data-lucide="package" class="w-4.5 h-4.5"></i>
                    Tous les kits de la boutique
                </a>
            </div>

            <!-- Bento des Kits Maison avec Espacement Généreux (Spacing) & Liens Fiche Produit -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-7 lg:gap-8">
                @foreach($kitProducts as $kit)
                    <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-xs hover:shadow-xl transition-all flex flex-col justify-between border-t-4 border-primary-container group">
                        <div>
                            <!-- Photo cliquable -->
                            <a href="{{ route('product.show', [$kit->id, $kit->slug]) }}" class="relative w-full aspect-[4/3] rounded-xl overflow-hidden mb-3.5 bg-canvas-background block group-hover:opacity-95 cursor-pointer" title="{{ $kit->nom }}">
                                <img src="{{ $kit->image_principale }}" alt="{{ $kit->nom }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"/>
                                <span class="absolute top-2 left-2 bg-cobalt-deep text-white text-[11px] font-extrabold px-2 py-0.5 rounded shadow-xs">
                                    {{ $kit->reference }}
                                </span>
                            </a>
                            <span class="text-[10px] font-black text-emerald-700 uppercase tracking-wider block">PACK TOUT-EN-UN</span>
                            
                            <!-- Nom cliquable -->
                            <h3 class="font-extrabold text-[15px] text-on-surface mt-1 mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                                <a href="{{ route('product.show', [$kit->id, $kit->slug]) }}" class="hover:text-primary transition-colors block">
                                    {{ $kit->nom }}
                                </a>
                            </h3>
                            <p class="text-[12.5px] text-text-muted line-clamp-2 mb-3">
                                {{ $kit->description_courte }}
                            </p>
                        </div>

                        <div class="pt-3 border-t border-border-subtle">
                            <div class="flex items-center justify-between mb-2 text-[12px]">
                                <span class="text-text-muted">Total : {{ $kit->prix_formate }}</span>
                                <span class="bg-brand-red-subtle text-primary font-bold px-2 py-0.5 rounded">
                                    {{ round(($kit->mensualite_indicative ?? 6500) / 30) }} FCFA/jour
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <div>
                                    <span class="text-[10px] text-text-muted block font-semibold">Mensualité</span>
                                    <span class="text-[17px] font-black text-primary-container">{{ $kit->mensualite_formatee }}</span>
                                </div>
                                <button
                                    type="button"
                                    data-add-to-cart
                                    data-id="{{ $kit->id }}"
                                    data-name="{{ $kit->nom }}"
                                    data-price="{{ $kit->prix_vente }}"
                                    data-monthly="{{ $kit->mensualite_indicative ?? 6500 }}"
                                    data-image="{{ $kit->image_principale }}"
                                    onclick="window.addToCart(this)"
                                    class="bg-primary-container hover:bg-brand-red-hover text-white px-3.5 py-2 rounded-xl text-[12.5px] font-bold transition-colors flex items-center gap-1 shadow-xs cursor-pointer"
                                >
                                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                                    Choisir
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ── 5. EXPLORER PAR CATÉGORIES POPULAIRES ──────────────────────── -->
    <section class="w-full py-12 md:py-16 bg-canvas-background">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <h2 class="text-[26px] md:text-[30px] font-black text-on-surface">Explorez par Catégories</h2>
                <p class="text-[14px] text-text-muted mt-1">Tous vos univers préférés accessibles sans avance intégrale avec nos partenaires agréés.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-5">
                @foreach($categories as $cat)
                    <a href="{{ route('catalog.category', $cat->slug) }}" class="bg-surface-container-lowest p-5 rounded-2xl shadow-xs hover:shadow-md border border-border-subtle/80 transition-all flex flex-col items-center text-center group">
                        <div class="w-20 h-20 mb-3 rounded-full bg-slate-50 flex items-center justify-center text-primary-container group-hover:scale-110 transition-transform">
                            <i data-lucide="{{ $cat->lucide_icon }}" class="w-9 h-9 text-primary-container"></i>
                        </div>
                        <span class="font-extrabold text-[15px] text-on-surface group-hover:text-primary transition-colors">
                            {{ $cat->nom }}
                        </span>
                        <span class="text-[12px] text-text-muted mt-0.5 line-clamp-1">
                            {{ $cat->description }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ── 6. COMMENT ÇA MARCHE ? (Guide Interactif 4 Étapes) ──────────── -->
    <section class="w-full py-14 bg-surface-container-lowest border-y border-border-subtle">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-primary-container">Transparence Totale</span>
                <h2 class="text-[26px] md:text-[30px] font-black text-on-surface mt-1">Comment ça marche ?</h2>
                <p class="text-[14px] text-text-muted mt-0.5">Achetez vos articles désirés en 4 étapes fluides sans contraintes bancaires lourdes.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-canvas-background border border-border-subtle">
                    <div class="w-14 h-14 rounded-2xl bg-brand-red-subtle text-primary-container text-[20px] font-black flex items-center justify-center mb-4 shadow-xs">
                        1
                    </div>
                    <span class="font-extrabold text-[16px] text-on-surface mb-2">Choisissez</span>
                    <p class="text-[13px] text-text-muted">Parcourez le catalogue et sélectionnez votre kit, smartphone ou électroménager.</p>
                </div>

                <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-canvas-background border border-border-subtle">
                    <div class="w-14 h-14 rounded-2xl bg-cobalt-subtle text-secondary text-[20px] font-black flex items-center justify-center mb-4 shadow-xs">
                        2
                    </div>
                    <span class="font-extrabold text-[16px] text-on-surface mb-2">Commandez</span>
                    <p class="text-[13px] text-text-muted">Validation simple en ligne avec votre pièce d'identité (CNI ou Passeport).</p>
                </div>

                <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-canvas-background border border-border-subtle">
                    <div class="w-14 h-14 rounded-2xl bg-brand-red-subtle text-primary-container text-[20px] font-black flex items-center justify-center mb-4 shadow-xs">
                        3
                    </div>
                    <span class="font-extrabold text-[16px] text-on-surface mb-2">Payez par tranches</span>
                    <p class="text-[13px] text-text-muted">Réglez vos échéances à votre rythme par Wave, Orange Money ou MTN MoMo.</p>
                </div>

                <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-canvas-background border border-border-subtle">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-700 text-[20px] font-black flex items-center justify-center mb-4 shadow-xs">
                        4
                    </div>
                    <span class="font-extrabold text-[16px] text-on-surface mb-2">Recevez chez vous</span>
                    <p class="text-[13px] text-text-muted">Livraison sécurisée sous 72h à Abidjan et expédition fiable en région.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── 7. BANNIÈRE PROMO & ENGAGEMENT RSE : OCTOBRE ROSE ────────────── -->
    <section class="w-full py-10 bg-canvas-background">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative bg-gradient-to-r from-[#ffe4e6] to-[#fce7f3] rounded-3xl p-6 md:p-10 overflow-hidden shadow-sm flex flex-col lg:flex-row items-center gap-8 border border-rose-200">
                <div class="w-full lg:w-1/2 flex flex-col gap-3 z-10">
                    <span class="inline-flex items-center gap-1.5 bg-white text-[#e11d48] text-[11px] font-extrabold px-3 py-1 rounded-full w-fit shadow-xs">
                        🌸 Engagement Solidaire
                    </span>
                    <h2 class="text-[24px] md:text-[28px] text-[#881337] font-black leading-tight">
                        Octobre Rose : Prenez soin de vous aujourd’hui, profitez de demain !
                    </h2>
                    <p class="text-[14px] text-[#4c0519] leading-relaxed">
                        Cypress Pay soutient toutes les femmes et encourage le dépistage précoce du cancer du sein. Prenez soin de votre santé et facilitez l'équipement de votre foyer avec nos formules échelonnées bienveillantes.
                    </p>
                    <div class="pt-2">
                        <a class="bg-[#e11d48] hover:bg-[#be123c] text-white font-bold text-[14px] px-6 py-3 rounded-xl shadow-md transition-colors inline-flex items-center gap-2" href="{{ route('catalog.category', 'mode-et-beaute') }}">
                            <i data-lucide="heart" class="w-4.5 h-4.5 text-rose-500 fill-rose-500"></i>
                            Voir la sélection dédiée aux femmes
                        </a>
                    </div>
                </div>
                <div class="w-full lg:w-1/2 flex justify-center lg:justify-end z-10">
                    <div class="relative rounded-2xl overflow-hidden shadow-xl max-w-md w-full bg-white">
                        <img alt="Campagne Octobre Rose Cypress Pay" class="w-full h-auto object-cover" src="https://lh3.googleusercontent.com/aida/AEtjO1UnnA887yG9bcp_kH-QjA5JrBOLNePknC89t9FDvwp40j5rrbLMZ8xecUh5bXOnR2MvYuXbwiWB0gOkKmp1Ws2Wn2iNqBBZuGYM7tTn0eYqoSU0xp2n4FtQPgJTp7j8w35Q8MXsMgpIjipkiMC0gHHkZz1bOEncA50QAwXKzGV_Dy9gmtl1NiM71ANdgTqxuRt1UJBj93VQVX8oRYScq95erLnaALIuq6BeARbEl3hXCYTDan1u_aBldg"/>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? "Cypres Pay - E-commerce BNPL Côte d'Ivoire" }}</title>

    <!-- Meta SEO & Social -->
    <meta name="description"
        content="Cypres Pay — Facilitateur d'achats et paiement échelonné en Côte d'Ivoire de 30 jours à 24 mois sans banque. Électroménager, smartphones, kits maison." />
    <meta name="theme-color" content="#b70011" />

    <!-- Favicon & PWA Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}" />

    <!-- Polices Google & Icônes Lucide -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap"
        rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .lucide {
            display: inline-block;
            vertical-align: middle;
        }
    </style>

    <!-- Tailwind Config injectée pour affichage instantané et haute fidélité -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-primary-fixed-variant": "#93000b",
                        "surface-container-lowest": "#ffffff",
                        "brand-red-hover": "#B91C1C",
                        "on-tertiary": "#ffffff",
                        "on-primary": "#ffffff",
                        "inverse-on-surface": "#eef0ff",
                        "on-tertiary-container": "#e1ffec",
                        "secondary-fixed-dim": "#b5c4ff",
                        "secondary-container": "#8aa5fe",
                        "cobalt-subtle": "#EFF4FF",
                        "provider-orange": "#FF7900",
                        "tertiary-container": "#00825a",
                        "tertiary-fixed": "#85f8c4",
                        "card-surface": "#FFFFFF",
                        "tertiary": "#006646",
                        "text-light": "#94A3B8",
                        "provider-wave": "#1DA1F2",
                        "background": "#faf8ff",
                        "error": "#ba1a1a",
                        "on-secondary-container": "#15378a",
                        "error-container": "#ffdad6",
                        "border-subtle": "#E2E8F0",
                        "on-tertiary-fixed": "#002114",
                        "brand-red-subtle": "#FEF2F2",
                        "secondary-fixed": "#dbe1ff",
                        "surface-container": "#eaedff",
                        "surface-dim": "#d2d9f4",
                        "inverse-surface": "#283044",
                        "primary-fixed": "#ffdad6",
                        "tertiary-fixed-dim": "#68dba9",
                        "inverse-primary": "#ffb4ab",
                        "outline": "#916f6b",
                        "primary": "#b70011",
                        "primary-fixed-dim": "#ffb4ab",
                        "secondary": "#3d5aad",
                        "on-secondary-fixed-variant": "#224194",
                        "on-surface-variant": "#5c403c",
                        "on-background": "#131b2e",
                        "surface-container-high": "#e2e7ff",
                        "surface-container-highest": "#dae2fd",
                        "cobalt-deep": "#081B4B",
                        "on-error-container": "#93000a",
                        "outline-variant": "#e6bdb8",
                        "text-muted": "#64748B",
                        "on-surface": "#131b2e",
                        "surface-container-low": "#f2f3ff",
                        "on-error": "#ffffff",
                        "on-tertiary-fixed-variant": "#005137",
                        "border-prominent": "#CBD5E1",
                        "on-secondary-fixed": "#00174c",
                        "on-primary-fixed": "#410002",
                        "surface": "#faf8ff",
                        "surface-bright": "#faf8ff",
                        "surface-variant": "#dae2fd",
                        "surface-tint": "#bf0715",
                        "on-secondary": "#ffffff",
                        "canvas-background": "#F8FAFC",
                        "on-primary-container": "#fff6f5",
                        "provider-mtn": "#FFCC00",
                        "primary-container": "#dc2626"
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        "display-hero": ['"Plus Jakarta Sans"', 'sans-serif'],
                        "headline-sm": ['"Plus Jakarta Sans"', 'sans-serif'],
                        "headline-lg": ['"Plus Jakarta Sans"', 'sans-serif'],
                        "label-lg": ['"Plus Jakarta Sans"', 'sans-serif'],
                        "price-installment": ['"Plus Jakarta Sans"', 'sans-serif']
                    }
                }
            }
        };
    </script>

    <style>
        @layer base {

            html,
            body {
                margin: 0;
                padding: 0;
            }

            body {
                overscroll-behavior: none;
            }
        }

        ::-webkit-scrollbar {
            display: none;
        }
    </style>

    <!-- Support Livewire -->
    @if(class_exists(\Livewire\Livewire::class))
        @livewireStyles
    @endif

    <!-- Early Cart Stub (Garantit que le clic fonctionne sans attendre la fin du chargement) -->
    <script>
        window.cypressCartQueue = window.cypressCartQueue || [];
        if (!window.addToCart) {
            window.addToCart = function(...args) {
                if (window.CypressCart && typeof window.CypressCart.add === 'function') {
                    return window.CypressCart.add(...args);
                }
                window.cypressCartQueue.push(args);
            };
        }
        if (!window.addToCartFromButton) {
            window.addToCartFromButton = function(btn) {
                if (!btn) return;
                const item = {
                    id: parseInt(btn.dataset.id || btn.dataset.productId || 1, 10),
                    nom: btn.dataset.name || btn.dataset.nom || btn.dataset.productName || 'Produit Cypress Pay',
                    prix: parseInt(btn.dataset.price || btn.dataset.prix || 0, 10),
                    monthly: parseInt(btn.dataset.monthly || btn.dataset.mensualite || 0, 10),
                    img: btn.dataset.image || btn.dataset.img || '',
                    image: btn.dataset.image || btn.dataset.img || '',
                    qty: 1
                };
                window.addToCart(item);
            };
        }
    </script>
</head>

<body class="bg-surface font-sans text-on-surface antialiased selection:bg-brand-red-subtle selection:text-primary">

    <!-- ════════════════════════════════════════════════════════════
      HEADER GLOBAL — STYLE CYPRES PAY
    ════════════════════════════════════════════════════════════ -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.06)]">
        <!-- 1. Barre Cobalt Supérieure (Support & Ventes Flash) -->
        <div class="bg-cobalt-deep text-on-primary">
            <div
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-9 flex items-center justify-between text-[11px] font-semibold">
                <div class="flex items-center gap-2 overflow-hidden whitespace-nowrap">
                    <span class="flex items-center gap-1 font-bold text-provider-mtn">
                        <i data-lucide="zap" class="w-3.5 h-3.5"></i> Ventes Flash
                    </span>
                    <span class="opacity-40">|</span>
                    <span class="hidden sm:inline">Livraison rapide 72h en Côte d'Ivoire</span>
                    <span class="hidden sm:inline opacity-40">|</span>
                    <span class="truncate">Paiement flexible de 30 jours à 24 mois sans frais cachés</span>
                </div>
                <div class="flex items-center gap-4 shrink-0">
                    <a class="flex items-center gap-1 text-white hover:text-tertiary-fixed transition-colors"
                        href="https://wa.me/2250586708161" target="_blank" rel="noopener">
                        <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                        <span class="hidden md:inline">Support WhatsApp :</span> +225 05 86 70 81 61
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Header Central (Logo, Recherche Livewire, Mon Compte, Favoris, Panier) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            <!-- Logo -->
            <div class="flex items-center gap-3 shrink-0">
                <a class="flex items-center gap-2.5" href="{{ route('home') }}">
                    <div
                        class="w-9 h-9 rounded-xl bg-primary-container flex items-center justify-center text-white font-black text-[20px] shadow-sm">
                        CP
                    </div>
                    <div class="flex flex-col leading-none">
                        <span class="text-[20px] font-extrabold tracking-tight text-on-surface uppercase">
                            Cypres<span class="text-primary-container">Pay</span>
                        </span>
                        <span class="text-[10px] font-bold text-text-muted tracking-wider">Facilitateur d'achats
                            CI</span>
                    </div>
                </a>
            </div>

            <!-- Barre de Recherche Livewire Instantanée -->
            <div class="hidden md:flex flex-1 max-w-2xl mx-4">
                @if(class_exists(\App\Livewire\SearchBar::class))
                    @livewire('search-bar')
                @else
                    <div
                        class="flex w-full items-center bg-canvas-background rounded-lg shadow-inner border border-border-subtle p-2">
                        <i data-lucide="search" class="w-5 h-5 text-text-muted mr-2 shrink-0"></i>
                        <input class="w-full bg-transparent border-none outline-none text-[14px]"
                            placeholder="Rechercher un iPhone, réfrigérateur, kit solaire..." type="text" />
                    </div>
                @endif
            </div>

            <!-- Liens d'Actions Rapides -->
            <div class="flex items-center gap-3 lg:gap-5 shrink-0">
                <!-- Portail / Mon Compte avec Menu Déroulant & Déconnexion -->
                <div class="relative">
                    @auth
                        @php
                            $authUser = auth()->user();
                            $dashboardRoute = match ($authUser->role) {
                                'admin' => route('admin.dashboard'),
                                'commercial' => route('commercial.dashboard'),
                                'caissier' => route('caisse.dashboard'),
                                default => route('client.dashboard'),
                            };
                            $roleLabel = match ($authUser->role) {
                                'admin' => 'Administrateur',
                                'commercial' => 'Commercial Terrain',
                                'caissier' => 'Caissier Agence',
                                default => 'Espace Client',
                            };
                            $avatarBg = match ($authUser->role) {
                                'admin' => 'bg-purple-700',
                                'commercial' => 'bg-secondary',
                                'caissier' => 'bg-tertiary',
                                default => 'bg-primary-container',
                            };
                            $roleBadgeColor = match ($authUser->role) {
                                'admin' => 'bg-purple-100 text-purple-700 border-purple-200',
                                'commercial' => 'bg-blue-100 text-secondary border-blue-200',
                                'caissier' => 'bg-emerald-100 text-tertiary border-emerald-200',
                                default => 'bg-brand-red-subtle text-primary border-primary/20',
                            };
                        @endphp

                        <div class="relative" x-data="{ open: false }" @click.outside="open = false"
                            @keydown.escape.window="open = false">
                            <button type="button" @click="open = !open"
                                class="flex items-center gap-2 py-1 px-1.5 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer group focus:outline-none">
                                <div
                                    class="w-8 h-8 rounded-full {{ $avatarBg }} flex items-center justify-center shrink-0 text-white font-black text-xs shadow-xs">
                                    {{ substr($authUser->prenom ?? $authUser->nom ?? 'U', 0, 1) }}
                                </div>
                                <div class="hidden xl:flex flex-col text-left leading-tight">
                                    <span
                                        class="text-[11px] text-text-muted font-bold truncate max-w-[110px]">{{ $authUser->prenom ?? $authUser->nom }}</span>
                                    <span class="text-[12px] font-extrabold text-on-surface flex items-center gap-0.5">
                                        {{ $roleLabel }}
                                        <i data-lucide="chevron-down"
                                            class="w-4 h-4 text-text-muted group-hover:text-primary transition-colors"></i>
                                    </span>
                                </div>
                            </button>

                            <!-- Menu Déroulant Profil & Déconnexion -->
                            <div x-show="open" x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                class="absolute right-0 mt-2 w-64 bg-surface-container-lowest rounded-2xl shadow-xl border border-border-subtle p-2 z-50 divide-y divide-border-subtle/70"
                                style="display: none;">
                                <!-- En-tête profil -->
                                <div class="p-3">
                                    <div class="flex items-center gap-2.5 mb-2">
                                        <div
                                            class="w-10 h-10 rounded-full {{ $avatarBg }} flex items-center justify-center text-white font-extrabold text-sm shrink-0">
                                            {{ substr($authUser->prenom ?? 'U', 0, 1) }}{{ substr($authUser->nom ?? '', 0, 1) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <span
                                                class="text-[13px] font-extrabold text-on-surface block truncate">{{ $authUser->nom_complet }}</span>
                                            <span class="text-[11px] text-text-muted truncate block">+225
                                                {{ $authUser->telephone }}</span>
                                        </div>
                                    </div>
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider border {{ $roleBadgeColor }}">
                                        {{ $roleLabel }}
                                    </span>
                                </div>

                                <!-- Liens de navigation du compte -->
                                <div class="py-1.5 text-[13px] font-bold space-y-0.5">
                                    <a href="{{ $dashboardRoute }}"
                                        class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-on-surface hover:bg-slate-100 hover:text-primary transition-colors">
                                        <i data-lucide="layout-dashboard" class="w-4.5 h-4.5 text-primary"></i>
                                        <span>Mon Tableau de bord</span>
                                    </a>

                                    @if($authUser->isClient())
                                        <a href="{{ route('client.dashboard') }}#mes-commandes"
                                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-on-surface hover:bg-slate-100 hover:text-primary transition-colors">
                                            <i data-lucide="receipt" class="w-4.5 h-4.5 text-emerald-600"></i>
                                            <span>Mes Commandes & Échéances</span>
                                        </a>
                                    @elseif($authUser->isAdmin())
                                        <a href="{{ route('admin.orders.index') }}"
                                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-on-surface hover:bg-slate-100 hover:text-primary transition-colors">
                                            <i data-lucide="package-check" class="w-4.5 h-4.5 text-purple-600"></i>
                                            <span>Gestion des Commandes</span>
                                        </a>
                                        <a href="{{ route('admin.products.index') }}"
                                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-on-surface hover:bg-slate-100 hover:text-primary transition-colors">
                                            <i data-lucide="package" class="w-4.5 h-4.5 text-amber-600"></i>
                                            <span>Catalogue & Produits</span>
                                        </a>
                                    @elseif($authUser->isCommercial())
                                        <a href="{{ route('checkout.show') }}"
                                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-on-surface hover:bg-slate-100 hover:text-primary transition-colors">
                                            <i data-lucide="plus-circle" class="w-4.5 h-4.5 text-secondary"></i>
                                            <span>Nouvelle Vente Terrain</span>
                                        </a>
                                    @elseif($authUser->isCaissier())
                                        <a href="{{ route('caisse.dashboard') }}"
                                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-on-surface hover:bg-slate-100 hover:text-primary transition-colors">
                                            <i data-lucide="circle-dollar-sign" class="w-4.5 h-4.5 text-emerald-600"></i>
                                            <span>Guichet d'encaissement</span>
                                        </a>
                                    @endif

                                    <a href="{{ route('verifier') }}"
                                        class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-on-surface hover:bg-slate-100 hover:text-primary transition-colors">
                                        <i data-lucide="badge-check" class="w-4.5 h-4.5 text-secondary"></i>
                                        <span>Vérifier une quittance</span>
                                    </a>
                                </div>

                                <!-- Déconnexion -->
                                <div class="p-1 pt-1.5">
                                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-red-600 hover:bg-red-50 hover:text-red-700 text-[13px] font-extrabold transition-colors cursor-pointer text-left">
                                            <i data-lucide="log-out" class="w-4.5 h-4.5"></i>
                                            <span>Se déconnecter</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a class="flex items-center gap-2 text-on-surface hover:text-primary transition-colors py-1 cursor-pointer"
                            href="{{ route('login') }}">
                            <div
                                class="w-8 h-8 rounded-full bg-primary flex items-center justify-center shrink-0 text-white">
                                <i data-lucide="user" class="w-4.5 h-4.5"></i>
                            </div>
                            <div class="hidden xl:flex flex-col text-left leading-tight">
                                <span class="text-[11px] text-text-muted font-semibold">Bienvenue</span>
                                <span class="text-[13px] font-bold">Connexion</span>
                            </div>
                        </a>
                    @endauth
                </div>

                <!-- Vérifier un Reçu -->
                <a class="hidden sm:flex items-center gap-1.5 text-on-surface hover:text-primary transition-colors text-[13px] font-bold py-1"
                    href="{{ route('verifier') }}">
                    <i data-lucide="badge-check" class="w-5 h-5 text-emerald-600"></i>
                    <span class="hidden lg:inline">Vérifier reçu</span>
                </a>

                <!-- Panier BNPL & Calculateur d'échéances -->
                <button type="button"
                    onclick="if(window.Livewire) { Livewire.dispatch('open-cart'); } window.dispatchEvent(new CustomEvent('open-cart'));"
                    class="flex items-center gap-2.5 bg-brand-red-subtle hover:bg-red-100 px-3.5 py-2 rounded-xl transition-all cursor-pointer border border-primary-container/10">
                    <div class="relative flex items-center justify-center">
                        <i data-lucide="shopping-bag" class="w-6 h-6 text-primary-container"></i>
                        <span
                            class="cart-count-badge absolute -top-1 -right-2 bg-primary-container text-white text-[10px] font-black w-4 h-4 rounded-full flex items-center justify-center"
                            style="display:none">
                            0
                        </span>
                    </div>
                    <div class="hidden lg:flex flex-col text-left leading-tight">
                        <span
                            class="text-[10px] text-primary-container font-extrabold uppercase tracking-wider">Calculateur
                            BNPL</span>
                        <span class="cart-total-display text-[13px] font-extrabold text-on-surface">0 FCFA</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- 3. Barre Catégories & Liens Rapides -->
        <div class="bg-surface-container-lowest border-t border-border-subtle/80 shadow-[0_1px_4px_rgba(0,0,0,0.03)]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center gap-2 md:gap-3 overflow-x-auto py-2 no-scrollbar text-[13px] font-bold">
                    <a class="shrink-0 px-3 py-1 rounded-lg text-primary-container bg-brand-red-subtle flex items-center gap-1.5"
                        href="{{ route('flash') }}">
                        <i data-lucide="zap" class="w-4 h-4"></i> Ventes Flash
                    </a>
                    <a class="shrink-0 px-3 py-1 rounded-lg text-slate-700 hover:text-primary hover:bg-slate-50 transition-colors"
                        href="{{ route('kits') }}">
                        Kits Maison & Cuisine
                    </a>
                    <a class="shrink-0 px-3 py-1 rounded-lg text-slate-700 hover:text-primary hover:bg-slate-50 transition-colors"
                        href="{{ route('catalog.category', 'electromenager') }}">
                        Électroménager
                    </a>
                    <a class="shrink-0 px-3 py-1 rounded-lg text-slate-700 hover:text-primary hover:bg-slate-50 transition-colors"
                        href="{{ route('catalog.category', 'telephonie-et-tech') }}">
                        Téléphonie & Tech
                    </a>
                    <a class="shrink-0 px-3 py-1 rounded-lg text-slate-700 hover:text-primary hover:bg-slate-50 transition-colors"
                        href="{{ route('catalog.category', 'mode-et-beaute') }}">
                        Mode & Beauté
                    </a>
                    <a class="shrink-0 px-3 py-1 rounded-lg text-slate-700 hover:text-primary hover:bg-slate-50 transition-colors"
                        href="{{ route('catalog.category', 'energie-solaire') }}">
                        Solaire & Énergie
                    </a>
                    <a class="shrink-0 px-3 py-1 rounded-lg text-slate-700 hover:text-primary hover:bg-slate-50 transition-colors"
                        href="{{ route('catalog.category', 'batiment-et-travaux') }}">
                        BTP & Matériaux
                    </a>
                    <a class="shrink-0 px-3 py-1 rounded-lg text-secondary hover:text-cobalt-deep hover:bg-cobalt-subtle transition-colors flex items-center gap-1.5"
                        href="{{ route('simulator') }}">
                        <i data-lucide="calculator" class="w-4 h-4"></i> Simulateur
                    </a>
                    <a class="shrink-0 px-3 py-1 rounded-lg text-slate-700 hover:text-primary hover:bg-slate-50 transition-colors flex items-center gap-1.5"
                        href="{{ route('how-it-works') }}">
                        <i data-lucide="help-circle" class="w-4 h-4"></i> Comment ça marche ?
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- ════════════════════════════════════════════════════════════
      CONTENU PRINCIPAL
    ════════════════════════════════════════════════════════════ -->
    <main class="w-full pt-[148px] bg-canvas-background min-h-screen">
        @yield('content')
    </main>

    <!-- ════════════════════════════════════════════════════════════
      FOOTER GLOBAL — STYLE CYPRES PAY & MOYENS DE PAIEMENT CI
    ════════════════════════════════════════════════════════════ -->
    <footer class="w-full bg-cobalt-deep text-on-primary pt-14 pb-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 pb-12 border-b border-white/10">
                <!-- Colonne Marque -->
                <div class="lg:col-span-2 flex flex-col gap-4">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-8 h-8 rounded-lg bg-primary-container flex items-center justify-center text-white font-black text-[16px]">
                            CP
                        </div>
                        <span class="text-[20px] font-black uppercase text-white">
                            Cypres<span class="text-primary-fixed-dim">Pay</span>
                        </span>
                    </div>
                    <p class="text-[13.5px] text-text-light max-w-sm leading-relaxed">
                        Votre facilitateur d'achats de confiance en Côte d'Ivoire. Électroménager, kits maison,
                        smartphones et matériaux : payez progressivement de 30 jours à 24 mois sans banque.
                    </p>
                    <div>
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-text-light block mb-2">
                            Modes de règlement acceptés
                        </span>
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="px-2.5 py-1 rounded bg-provider-wave text-white text-[11px] font-extrabold flex items-center gap-1.5">
                                <i data-lucide="banknote" class="w-3.5 h-3.5"></i> Wave
                            </span>
                            <span
                                class="px-2.5 py-1 rounded bg-provider-orange text-white text-[11px] font-extrabold flex items-center gap-1.5">
                                <i data-lucide="wallet" class="w-3.5 h-3.5"></i> Orange Money
                            </span>
                            <span
                                class="px-2.5 py-1 rounded bg-provider-mtn text-cobalt-deep text-[11px] font-extrabold flex items-center gap-1.5">
                                <i data-lucide="smartphone" class="w-3.5 h-3.5"></i> MTN MoMo
                            </span>
                            <span
                                class="px-2.5 py-1 rounded bg-white text-cobalt-deep text-[11px] font-extrabold flex items-center gap-1.5">
                                <i data-lucide="credit-card" class="w-3.5 h-3.5"></i> Moov Money
                            </span>
                            <span
                                class="px-2.5 py-1 rounded bg-white text-cobalt-deep text-[11px] font-extrabold flex items-center gap-1.5">
                                <i data-lucide="coins" class="w-3.5 h-3.5"></i> Espèces en caisse
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Colonne Acheter & BNPL -->
                <div class="flex flex-col gap-3">
                    <h4 class="text-[13px] font-extrabold uppercase tracking-wide text-white">Acheter & BNPL</h4>
                    <ul class="space-y-2 text-[13px] text-text-light font-medium">
                        <li><a class="hover:text-white transition-colors" href="{{ route('flash') }}">Ventes Flash (-7%
                                Cash)</a></li>
                        <li><a class="hover:text-white transition-colors" href="{{ route('kits') }}">Kits Maison &
                                Énergie</a></li>
                        <li><a class="hover:text-white transition-colors"
                                href="{{ route('catalog.category', 'electromenager') }}">Gros Électroménager</a></li>
                        <li><a class="hover:text-white transition-colors"
                                href="{{ route('catalog.category', 'telephonie-et-tech') }}">Smartphones & Tech</a></li>
                        <li><a class="hover:text-white transition-colors" href="{{ route('simulator') }}">Simulateur
                                d'échéances</a></li>
                    </ul>
                </div>

                <!-- Colonne Mon Espace -->
                <div class="flex flex-col gap-3">
                    <h4 class="text-[13px] font-extrabold uppercase tracking-wide text-white">Mon Espace</h4>
                    <ul class="space-y-2 text-[13px] text-text-light font-medium">
                        <li><a class="hover:text-white transition-colors" href="{{ route('client.dashboard') }}">Espace
                                Client</a></li>
                        <li><a class="hover:text-white transition-colors" href="{{ route('verifier') }}">Vérification de
                                versement</a></li>
                        <li><a class="hover:text-white transition-colors"
                                href="{{ route('commercial.dashboard') }}">Portail Commercial</a></li>
                        <li><a class="hover:text-white transition-colors" href="{{ route('caisse.dashboard') }}">Portail
                                Caisse Agence</a></li>
                        <li><a class="text-amber-400 hover:text-white transition-colors font-bold flex items-center gap-1.5"
                                href="{{ route('admin.dashboard') }}"><i data-lucide="shield" class="w-4 h-4"></i>
                                Console Admin</a></li>
                        @auth
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="text-red-400 hover:text-red-300 transition-colors font-bold flex items-center gap-1.5 text-[13px]">
                                        <i data-lucide="log-out" class="w-4 h-4"></i>
                                        Déconnexion ({{ auth()->user()->prenom }})
                                    </button>
                                </form>
                            </li>
                        @else
                            <li><a class="hover:text-white transition-colors" href="{{ route('login') }}">Connexion /
                                    Inscription</a></li>
                        @endauth
                    </ul>
                </div>

                <!-- Colonne Assistance -->
                <div class="flex flex-col gap-3">
                    <h4 class="text-[13px] font-extrabold uppercase tracking-wide text-white">Assistance & Contact</h4>
                    <ul class="space-y-2 text-[13px] text-text-light font-medium">
                        <li>
                            <a class="text-tertiary-fixed hover:underline flex items-center gap-1.5 font-bold"
                                href="https://wa.me/2250586708161" target="_blank">
                                <i data-lucide="message-circle" class="w-4 h-4"></i> WhatsApp: +225 05 86 70 81 61
                            </a>
                        </li>
                        <li><a class="hover:text-white transition-colors" href="{{ route('how-it-works') }}">Comment ça
                                marche ?</a></li>
                        <li><a class="hover:text-white transition-colors" href="#">Conditions de crédit</a></li>
                        <li><a class="hover:text-white transition-colors" href="#">Politique de confidentialité</a></li>
                        <li><a class="hover:text-white transition-colors" href="#">Siège : Yopougon, Abidjan</a></li>
                    </ul>
                </div>
            </div>

            <!-- Sous-footer -->
            <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-text-light text-[12px]">
                <p>© 2026 Cypres Pay CI — Ticou SARL. Abidjan, Côte d'Ivoire. Tous droits réservés.</p>
                <div class="flex items-center gap-6">
                    <span class="flex items-center gap-1.5"><i data-lucide="lock"
                            class="w-4 h-4 text-tertiary-fixed"></i> Paiements sécurisés Wave, Orange, MTN</span>
                    <span class="flex items-center gap-1.5"><i data-lucide="truck"
                            class="w-4 h-4 text-tertiary-fixed"></i> Livraison 72h Abidjan & Intérieur</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- ════════════════════════════════════════════════════════════
      COMPOSANTS GLOBAUX FLOTTANTS (PANIER TIROIR & PREUVE SOCIALE)
    ════════════════════════════════════════════════════════════ -->
    @if(class_exists(\App\Livewire\CartDrawer::class))
        @livewire('cart-drawer')
    @endif

    <!-- Toast Flottant de Preuve Sociale (Commandes en direct) -->
    <div id="live-toast"
        class="hidden fixed bottom-5 left-5 z-40 bg-surface-container-lowest p-3.5 rounded-2xl shadow-2xl border border-border-subtle flex items-center gap-3.5 max-w-sm transition-all duration-300">
        <div
            class="w-10 h-10 rounded-full bg-brand-red-subtle flex items-center justify-center text-primary-container shrink-0">
            <i data-lucide="shopping-bag" class="w-5 h-5 text-primary-container"></i>
        </div>
        <div class="flex flex-col text-left">
            <span class="text-[12px] font-bold text-on-surface">K*** (Yopougon, Abidjan)</span>
            <span class="text-[12px] text-text-muted">A souscrit au <strong class="text-on-surface">Pack Électro
                    N°019</strong></span>
            <span class="text-[11px] font-extrabold text-primary-container">Il y a 3 minutes · 6 500 FCFA/m</span>
        </div>
    </div>

    <!-- Scripts Core App & React Components via Vite -->
    @vite(['resources/js/app.js'])
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        });
    </script>

    @if(class_exists(\Livewire\Livewire::class))
        @livewireScripts
    @endif

    @yield('scripts')
    @stack('scripts')
</body>

</html>
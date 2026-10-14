<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $title ?? 'Administration - Cypres Pay Côte d\'Ivoire' }}</title>

    <!-- Meta & Polices Google & Icônes Lucide -->
    <meta name="theme-color" content="#081B4B" />

    <!-- Favicon & PWA Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .lucide {
            display: inline-block;
            vertical-align: middle;
        }
    </style>

    <!-- Tailwind Config injectée pour cohérence Cypres Pay -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#b70011",
                        "primary-container": "#dc2626",
                        "brand-red-hover": "#B91C1C",
                        "brand-red-subtle": "#FEF2F2",
                        "cobalt-deep": "#081B4B",
                        "cobalt-subtle": "#EFF4FF",
                        "secondary": "#3d5aad",
                        "tertiary": "#006646",
                        "tertiary-container": "#00825a",
                        "canvas-background": "#F8FAFC",
                        "provider-wave": "#1DA1F2",
                        "provider-orange": "#FF7900",
                        "provider-mtn": "#FFCC00"
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif']
                    }
                }
            }
        };
    </script>

    <!-- Cropper.js pour le rognage et recadrage d'images haute fidélité -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>

    <style>
        @layer base {

            html,
            body {
                height: 100%;
                margin: 0;
            }
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Styles additionnels Cropper pour design fluide */
        .cropper-view-box,
        .cropper-face {
            border-radius: 8px;
        }

        .cropper-line,
        .cropper-point {
            background-color: #b70011 !important;
        }

        .cropper-point.point-se {
            width: 8px;
            height: 8px;
        }
    </style>
</head>

<body class="h-full font-sans text-slate-800 antialiased bg-slate-50 flex flex-col md:flex-row" x-data="{ 
        sidebarOpen: false, 
        sidebarCollapsed: localStorage.getItem('cp_admin_collapsed') === 'false' ? false : true,
        toggleCollapse() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('cp_admin_collapsed', this.sidebarCollapsed);
            this.$nextTick(() => { 
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons(); 
                }
            });
        }
    }">

    <!-- ══════════════════════════════════════════════════════════════
      SIDEBAR GAUCHE — COBALT DEEP CYPRES PAY (REPLIABLE / ICONS-ONLY)
    ══════════════════════════════════════════════════════════════ -->
    <aside
        class="fixed inset-y-0 left-0 z-50 bg-cobalt-deep text-white flex flex-col justify-between transition-all duration-300 ease-in-out md:translate-x-0 md:static md:inset-0 shadow-2xl shrink-0"
        :class="[
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            sidebarCollapsed ? 'md:w-20 w-72' : 'md:w-72 w-72'
        ]">
        <div>
            <!-- Logo & Brand Header -->
            <div class="h-20 border-b border-white/10 flex items-center transition-all duration-300"
                :class="sidebarCollapsed ? 'px-0 justify-center' : 'px-5 justify-between'">

                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 overflow-hidden"
                    :title="sidebarCollapsed ? 'CypresPay Admin' : ''">
                    <div
                        class="w-10 h-10 rounded-xl bg-primary-container flex items-center justify-center text-white font-black text-xl shadow-md shrink-0">
                        CP
                    </div>
                    <div x-show="!sidebarCollapsed" x-transition.opacity
                        class="flex flex-col leading-none whitespace-nowrap">
                        <span class="text-lg font-black tracking-tight uppercase">
                            Cypres<span class="text-red-400">Pay</span>
                        </span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                            Console Admin CI
                        </span>
                    </div>
                </a>

                <!-- Fermeture Mobile -->
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white p-1">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>

                <!-- Bouton Replier sur Desktop -->
                <button @click="toggleCollapse()" x-show="!sidebarCollapsed" type="button"
                    class="hidden md:flex p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                    title="Replier la barre latérale">
                    <i data-lucide="panel-left-close" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Bouton Déplier quand replié -->
            <div x-show="sidebarCollapsed" class="hidden md:flex justify-center pt-2">
                <button @click="toggleCollapse()" type="button"
                    class="p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                    title="Déplier la barre latérale">
                    <i data-lucide="panel-left-open" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Navigation Principale -->
            <nav class="py-4 space-y-1.5 text-[13px] font-semibold overflow-y-auto max-h-[calc(100vh-175px)]"
                :class="sidebarCollapsed ? 'px-2.5' : 'px-4'">

                <!-- 1. Tableau de bord -->
                <a href="{{ route('admin.dashboard') }}" title="Vue d'ensemble"
                    class="flex items-center rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-primary-container text-white font-bold shadow-md' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    :class="sidebarCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3.5 py-2.5'">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0"></i>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Vue d'ensemble</span>
                </a>

                <!-- 2. Section Utilisateurs (Séparée par Rôles) -->
                <div x-show="!sidebarCollapsed" class="pt-4 pb-1">
                    <span class="px-3.5 text-[10px] font-black uppercase tracking-wider text-slate-400">
                        Gestion Utilisateurs
                    </span>
                </div>
                <div x-show="sidebarCollapsed" class="my-3 border-t border-white/10 mx-2"></div>

                <a href="{{ route('admin.users.clients') }}" title="Clients"
                    class="flex items-center rounded-xl transition-all {{ request()->routeIs('admin.users.clients') ? 'bg-white/15 text-white font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    :class="sidebarCollapsed ? 'justify-center p-2.5 relative' : 'justify-between px-3.5 py-2'">
                    <div class="flex items-center gap-3">
                        <i data-lucide="users" class="w-5 h-5 text-emerald-400 shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Clients</span>
                    </div>
                    <span x-show="!sidebarCollapsed"
                        class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-slate-300">
                        {{ \App\Models\User::where('role', 'client')->count() }}
                    </span>
                    <span x-show="sidebarCollapsed"
                        class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-emerald-400"></span>
                </a>

                <a href="{{ route('admin.users.commerciaux') }}" title="Commerciaux Terrain"
                    class="flex items-center rounded-xl transition-all {{ request()->routeIs('admin.users.commerciaux') ? 'bg-white/15 text-white font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    :class="sidebarCollapsed ? 'justify-center p-2.5 relative' : 'justify-between px-3.5 py-2'">
                    <div class="flex items-center gap-3">
                        <i data-lucide="id-card" class="w-5 h-5 text-amber-400 shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Commerciaux Terrain</span>
                    </div>
                    <span x-show="!sidebarCollapsed"
                        class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-slate-300">
                        {{ \App\Models\User::where('role', 'commercial')->count() }}
                    </span>
                    <span x-show="sidebarCollapsed"
                        class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-amber-400"></span>
                </a>

                <a href="{{ route('admin.users.caissiers') }}" title="Caissiers Agence"
                    class="flex items-center rounded-xl transition-all {{ request()->routeIs('admin.users.caissiers') ? 'bg-white/15 text-white font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    :class="sidebarCollapsed ? 'justify-center p-2.5 relative' : 'justify-between px-3.5 py-2'">
                    <div class="flex items-center gap-3">
                        <i data-lucide="circle-dollar-sign" class="w-5 h-5 text-sky-400 shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Caissiers Agence</span>
                    </div>
                    <span x-show="!sidebarCollapsed"
                        class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-slate-300">
                        {{ \App\Models\User::where('role', 'caissier')->count() }}
                    </span>
                    <span x-show="sidebarCollapsed"
                        class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-sky-400"></span>
                </a>

                <a href="{{ route('admin.users.admins') }}" title="Administrateurs"
                    class="flex items-center rounded-xl transition-all {{ request()->routeIs('admin.users.admins') ? 'bg-white/15 text-white font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    :class="sidebarCollapsed ? 'justify-center p-2.5 relative' : 'justify-between px-3.5 py-2'">
                    <div class="flex items-center gap-3">
                        <i data-lucide="shield" class="w-5 h-5 text-purple-400 shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Administrateurs</span>
                    </div>
                    <span x-show="!sidebarCollapsed"
                        class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-slate-300">
                        {{ \App\Models\User::where('role', 'admin')->count() }}
                    </span>
                    <span x-show="sidebarCollapsed"
                        class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-purple-400"></span>
                </a>

                <!-- 3. Section Catalogue & Vente -->
                <div x-show="!sidebarCollapsed" class="pt-4 pb-1">
                    <span class="px-3.5 text-[10px] font-black uppercase tracking-wider text-slate-400">
                        Catalogue & Offres
                    </span>
                </div>
                <div x-show="sidebarCollapsed" class="my-3 border-t border-white/10 mx-2"></div>

                <a href="{{ route('admin.products.index') }}" title="Produits & Kits"
                    class="flex items-center rounded-xl transition-all {{ request()->routeIs('admin.products.*') ? 'bg-white/15 text-white font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    :class="sidebarCollapsed ? 'justify-center p-2.5' : 'justify-between px-3.5 py-2'">
                    <div class="flex items-center gap-3">
                        <i data-lucide="package" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Produits & Kits</span>
                    </div>
                    <span x-show="!sidebarCollapsed"
                        class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-slate-300">
                        {{ \App\Models\Product::count() }}
                    </span>
                </a>

                <a href="{{ route('admin.categories.index') }}" title="Catégories"
                    class="flex items-center rounded-xl transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-white/15 text-white font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    :class="sidebarCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3.5 py-2'">
                    <i data-lucide="folder-tree" class="w-5 h-5 shrink-0"></i>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Catégories</span>
                </a>

                <a href="{{ route('admin.medias.index') }}" title="Médiathèque & Fichiers"
                    class="flex items-center rounded-xl transition-all {{ request()->routeIs('admin.medias.*') ? 'bg-primary-container text-white font-bold shadow-md' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    :class="sidebarCollapsed ? 'justify-center p-2.5' : 'justify-between px-3.5 py-2'">
                    <div class="flex items-center gap-3">
                        <i data-lucide="images" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Médiathèque & Fichiers</span>
                    </div>
                    @php $mediaTotalCount = \App\Models\Media::count(); @endphp
                    <span x-show="!sidebarCollapsed"
                        class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-slate-300">
                        {{ $mediaTotalCount }}
                    </span>
                </a>

                <a href="{{ route('admin.formulas.index') }}" title="Formules BNPL & Taux"
                    class="flex items-center rounded-xl transition-all {{ request()->routeIs('admin.formulas.*') ? 'bg-white/15 text-white font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    :class="sidebarCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3.5 py-2'">
                    <i data-lucide="percent" class="w-5 h-5 shrink-0"></i>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Formules BNPL & Taux</span>
                </a>

                <!-- 4. Section Finances & Opérations -->
                <div x-show="!sidebarCollapsed" class="pt-4 pb-1">
                    <span class="px-3.5 text-[10px] font-black uppercase tracking-wider text-slate-400">
                        Opérations & Risque
                    </span>
                </div>
                <div x-show="sidebarCollapsed" class="my-3 border-t border-white/10 mx-2"></div>

                <a href="{{ route('admin.orders.index') }}" title="Commandes & Échéanciers"
                    class="flex items-center rounded-xl transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-white/15 text-white font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    :class="sidebarCollapsed ? 'justify-center p-2.5 relative' : 'justify-between px-3.5 py-2'">
                    <div class="flex items-center gap-3">
                        <i data-lucide="shopping-bag" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Commandes</span>
                    </div>
                    @php $pendingOrders = \App\Models\Order::where('statut', 'en_attente_kyc')->count(); @endphp
                    @if($pendingOrders > 0)
                        <span x-show="!sidebarCollapsed"
                            class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500 text-slate-900">
                            {{ $pendingOrders }}
                        </span>
                        <span x-show="sidebarCollapsed"
                            class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    @endif
                </a>

                <a href="{{ route('admin.carts.index') }}" title="Paniers & Abandons"
                    class="flex items-center rounded-xl transition-all {{ request()->routeIs('admin.carts.*') ? 'bg-primary-container text-white font-bold shadow-md' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    :class="sidebarCollapsed ? 'justify-center p-2.5 relative' : 'justify-between px-3.5 py-2'">
                    <div class="flex items-center gap-3">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-amber-400 shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Paniers</span>
                    </div>
                    @php 
                                                                $adminAbandonedCount = \App\Models\Cart::where('status', 'abandoned')
                        ->orWhere(function ($q) {
                            $q->where('status', 'active')
                                ->where('items_count', '>', 0)
                                ->where('last_activity_at', '<', now()->subMinutes(15));
                        })->count(); 
                    @endphp
                    @if($adminAbandonedCount > 0)
                        <span x-show="!sidebarCollapsed"
                            class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-400 text-slate-900">
                            {{ $adminAbandonedCount }}
                        </span>
                        <span x-show="sidebarCollapsed"
                            class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-amber-400"></span>
                    @endif
                </a>

                <a href="{{ route('admin.kyc.index') }}" title="Dossiers KYC"
                    class="flex items-center rounded-xl transition-all {{ request()->routeIs('admin.kyc.*') ? 'bg-white/15 text-white font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    :class="sidebarCollapsed ? 'justify-center p-2.5 relative' : 'justify-between px-3.5 py-2'">
                    <div class="flex items-center gap-3">
                        <i data-lucide="shield-check" class="w-5 h-5 shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Dossiers KYC</span>
                    </div>
                    @php $pendingKyc = \App\Models\KycProfile::where('statut', 'en_attente')->count(); @endphp
                    @if($pendingKyc > 0)
                        <span x-show="!sidebarCollapsed"
                            class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-red-500 text-white animate-pulse">
                            {{ $pendingKyc }}
                        </span>
                        <span x-show="sidebarCollapsed"
                            class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    @endif
                </a>

                <a href="{{ route('admin.receipts.index') }}" title="Quittances (Code 6 Car.)"
                    class="flex items-center rounded-xl transition-all {{ request()->routeIs('admin.receipts.*') ? 'bg-white/15 text-white font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    :class="sidebarCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3.5 py-2'">
                    <i data-lucide="receipt" class="w-5 h-5 shrink-0"></i>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Quittances (Code 6 Car.)</span>
                </a>

            </nav>
        </div>

        <!-- Profil Admin en bas de Sidebar -->
        <div class="border-t border-white/10 bg-black/20 transition-all duration-300"
            :class="sidebarCollapsed ? 'p-3 flex justify-center' : 'p-4'">

            <div x-show="!sidebarCollapsed" class="flex items-center justify-between">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div
                        class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-300 flex items-center justify-center font-bold text-sm shrink-0 border border-purple-500/30">
                        {{ substr(auth()->user()->prenom ?? 'A', 0, 1) }}
                    </div>
                    <div class="truncate">
                        <span
                            class="text-[12px] font-bold text-white block truncate">{{ auth()->user()->nom_complet ?? 'Administrateur' }}</span>
                        <span class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Super Admin
                        </span>
                    </div>
                </div>

                <button type="button" @click="window.confirmAction({
                        title: 'Confirmer la déconnexion',
                        message: 'Êtes-vous sûr de vouloir vous déconnecter de la console d\'administration ?',
                        confirmText: 'Oui, me déconnecter',
                        cancelText: 'Annuler',
                        type: 'danger',
                        icon: 'log-out',
                        form: document.getElementById('admin-logout-form')
                    })" title="Se déconnecter"
                    class="p-2 text-slate-400 hover:text-red-400 transition-colors cursor-pointer">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Version icône seule quand replié -->
            <button x-show="sidebarCollapsed" type="button" @click="$dispatch('open-admin-profile')"
                class="w-10 h-10 rounded-xl bg-purple-500/20 text-purple-300 flex items-center justify-center font-bold text-sm border border-purple-500/30 hover:border-purple-400 transition-colors cursor-pointer"
                title="{{ auth()->user()->nom_complet }} (Super Admin) - Cliquez pour gérer votre profil">
                {{ substr(auth()->user()->prenom ?? 'A', 0, 1) }}
            </button>
        </div>
    </aside>

    <!-- Overlay sombre pour mobile -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm md:hidden" style="display: none;"></div>

    <!-- ══════════════════════════════════════════════════════════════
      ZONE DE CONTENU PRINCIPALE
    ══════════════════════════════════════════════════════════════ -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

        <!-- Header Supérieur avec Profil Connecté & Dropdown en Haut à Droite -->
        <header
            class="h-20 bg-white border-b border-slate-200/80 px-4 sm:px-8 flex items-center justify-between gap-4 sticky top-0 z-30 shadow-xs">
            <div class="flex items-center gap-3 sm:gap-4">
                <!-- Toggle Menu Mobile -->
                <button @click="sidebarOpen = !sidebarOpen"
                    class="md:hidden p-2 text-slate-600 hover:text-slate-900 rounded-xl hover:bg-slate-100 transition-colors">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>

                <!-- Toggle Replier/Déplier Desktop (Avec icône dynamique) -->
                <button @click="toggleCollapse()" type="button"
                    class="hidden md:flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-all border border-slate-200/80 cursor-pointer focus:outline-none"
                    :title="sidebarCollapsed ? 'Déplier la barre latérale' : 'Replier la barre latérale'">
                    <i :data-lucide="sidebarCollapsed ? 'panel-left-open' : 'panel-left-close'" class="w-5 h-5"></i>
                </button>

                <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 truncate">
                    @yield('page-title', 'Administration')
                </h1>
            </div>

            <!-- Actions Rapides & Profil Connecté en Haut à Droite -->
            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('home') }}" target="_blank"
                    class="hidden sm:flex items-center gap-1.5 px-3 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 text-[12px] font-bold border border-slate-200 transition-colors">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                    <span>Voir la Boutique</span>
                </a>

                <a href="{{ route('verifier') }}" target="_blank"
                    class="hidden md:flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-50 text-tertiary text-[12px] font-bold border border-emerald-200 transition-colors">
                    <i data-lucide="badge-check" class="w-4 h-4 text-emerald-600"></i>
                    <span>Vérificateur Public</span>
                </a>

                <!-- DROPDOWN DU USER CONNECTÉ EN HAUT À DROITE -->
                <div class="relative" x-data="{ userDropdownOpen: false }" @click.outside="userDropdownOpen = false"
                    @keydown.escape.window="userDropdownOpen = false">
                    <button type="button"
                        @click="userDropdownOpen = !userDropdownOpen; $nextTick(() => { if (window.lucide) window.lucide.createIcons(); });"
                        class="flex items-center gap-2.5 p-1.5 sm:px-3 sm:py-2 rounded-2xl hover:bg-slate-100 border border-slate-200/80 hover:border-slate-300 transition-all cursor-pointer group focus:outline-none bg-white shadow-xs">
                        <div
                            class="w-9 h-9 rounded-xl bg-cobalt-deep text-white flex items-center justify-center font-black text-xs shadow-sm ring-2 ring-primary/20 group-hover:ring-primary/40 transition-all shrink-0">
                            {{ substr(auth()->user()->prenom ?? 'A', 0, 1) }}{{ substr(auth()->user()->nom ?? 'D', 0, 1) }}
                        </div>
                        <div class="hidden lg:flex flex-col text-left leading-tight">
                            <span
                                class="text-[13px] font-bold text-slate-900 group-hover:text-primary transition-colors truncate max-w-[130px]">
                                {{ auth()->user()->nom_complet ?? 'Administrateur' }}
                            </span>
                            <span class="text-[10px] font-extrabold text-emerald-600 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Super Admin
                            </span>
                        </div>
                        <i data-lucide="chevron-down"
                            class="w-4 h-4 text-slate-400 group-hover:text-slate-700 transition-transform duration-200"
                            :class="userDropdownOpen ? 'rotate-180' : ''"></i>
                    </button>

                    <!-- Menu Déroulant Profil -->
                    <div x-show="userDropdownOpen" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                        class="absolute right-0 mt-2 w-72 bg-white rounded-3xl shadow-2xl border border-slate-100 p-2.5 z-50 divide-y divide-slate-100"
                        style="display: none;">
                        <!-- Carte Profil Header -->
                        <div class="p-3 bg-slate-50/80 rounded-2xl mb-1">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-cobalt-deep text-white flex items-center justify-center font-black text-sm shrink-0 shadow-sm">
                                    {{ substr(auth()->user()->prenom ?? 'A', 0, 1) }}{{ substr(auth()->user()->nom ?? 'D', 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span
                                        class="text-[13px] font-black text-slate-900 block truncate">{{ auth()->user()->nom_complet }}</span>
                                    <span
                                        class="text-[11px] text-slate-500 block truncate">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                            <div
                                class="mt-2.5 pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                                <span class="px-2 py-0.5 rounded-full font-bold bg-purple-100 text-purple-700">Super
                                    Admin</span>
                                <span class="text-slate-500 font-medium">+225 {{ auth()->user()->telephone }}</span>
                            </div>
                        </div>

                        <!-- Options du menu -->
                        <div class="py-1 text-[13px] font-bold space-y-0.5">
                            <button type="button" @click="userDropdownOpen = false; $dispatch('open-admin-profile')"
                                class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-slate-700 hover:text-primary hover:bg-slate-100 transition-colors text-left cursor-pointer">
                                <i data-lucide="user-cog" class="w-4 h-4 text-primary"></i>
                                <span>Gérer mon profil</span>
                            </button>

                            <a href="{{ route('admin.profile.edit') }}"
                                class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-slate-700 hover:text-primary hover:bg-slate-100 transition-colors">
                                <i data-lucide="settings" class="w-4 h-4 text-slate-400"></i>
                                <span>Paramètres du compte</span>
                            </a>

                            <a href="{{ route('home') }}" target="_blank"
                                class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-slate-700 hover:text-primary hover:bg-slate-100 transition-colors">
                                <i data-lucide="external-link" class="w-4 h-4 text-slate-400"></i>
                                <span>Aller sur la boutique</span>
                            </a>
                        </div>

                        <!-- Bouton Déconnexion avec Confirmation -->
                        <div class="pt-1.5">
                            <button type="button" @click="userDropdownOpen = false; window.confirmAction({
                                    title: 'Confirmer la déconnexion',
                                    message: 'Êtes-vous sûr de vouloir vous déconnecter de votre session administrateur ? Vous devrez vous ré-authentifier pour accéder au panneau d\'administration.',
                                    confirmText: 'Oui, me déconnecter',
                                    cancelText: 'Rester connecté',
                                    type: 'danger',
                                    icon: 'log-out',
                                    form: document.getElementById('admin-logout-form')
                                })"
                                class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-red-600 hover:bg-red-50 hover:text-red-700 text-[13px] font-extrabold transition-colors cursor-pointer text-left">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span>Se déconnecter</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Formulaire caché de déconnexion -->
                <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </header>

        <!-- Messages Flash (Succès / Erreur) -->
        <div class="px-4 sm:px-8 pt-6">
            @if(session('success'))
                <div
                    class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13px] font-bold flex items-center justify-between shadow-sm animate-fade-in">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()"
                        class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div
                    class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-[13px] font-bold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div
                    class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-[13px] font-bold shadow-sm">
                    <div class="flex items-center gap-2 mb-1 text-red-700">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                        <span>Veuillez corriger les erreurs suivantes :</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-[12px] font-medium text-red-600">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Contenu de la Page -->
        <main class="flex-1 px-4 sm:px-8 pb-12">
            @yield('content')
        </main>
    </div>

    <!-- Alpine.js pour l'interactivité légère des dropdowns et modales -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <!-- Dialog de Confirmation Réutilisable Moderne -->
    @include('components.confirm-dialog')

    <!-- Modale de Gestion du Profil Administrateur -->
    @include('components.admin-profile-modal')

    <!-- Modale Globale du Sélecteur Universel de Médias & Outil de Rognage -->
    @include('components.media-picker-modal')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        });
    </script>

    @yield('scripts')
</body>

</html>
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Fil d'Ariane & Titre -->
    <div class="mb-6">
        <nav class="flex items-center gap-2 text-[12px] text-text-muted mb-3 font-semibold">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1.5">
                <i data-lucide="home" class="w-4 h-4"></i> Accueil
            </a>
            <span>/</span>
            <span class="text-on-surface">
                @if(!empty($isFlashPage))
                    Ventes Flash Côte d'Ivoire
                @elseif(!empty($isKitsPage))
                    Kits Équipement Maison & Cuisine
                @elseif(!empty($currentCategory))
                    {{ $currentCategory->nom }}
                @else
                    Tous les Produits & Formules
                @endif
            </span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-4 border-b border-border-subtle">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-on-surface tracking-tight flex items-center gap-2.5">
                    @if(!empty($isFlashPage))
                        <span class="w-8 h-8 rounded-lg bg-red-100 text-primary-container flex items-center justify-center">
                            <i data-lucide="zap" class="w-5 h-5"></i>
                        </span>
                        Ventes Flash avec Remise Cash -7%
                    @elseif(!empty($isKitsPage))
                        <span class="w-8 h-8 rounded-lg bg-emerald-100 text-tertiary flex items-center justify-center">
                            <i data-lucide="package" class="w-5 h-5"></i>
                        </span>
                        Kits Équipement Maison & Cuisine
                    @elseif(!empty($currentCategory))
                        <span class="w-8 h-8 rounded-lg bg-blue-100 text-secondary flex items-center justify-center">
                            <i data-lucide="{{ $currentCategory->lucide_icon }}" class="w-5 h-5"></i>
                        </span>
                        {{ $currentCategory->nom }}
                    @else
                        <span class="w-8 h-8 rounded-lg bg-brand-red-subtle text-primary-container flex items-center justify-center">
                            <i data-lucide="store" class="w-5 h-5"></i>
                        </span>
                        Catalogue Cypress Pay Côte d'Ivoire
                    @endif
                </h1>
                <p class="text-[13px] text-text-muted mt-1.5 max-w-2xl">
                    Commandez avec apport flexible et payez de 30 jours à 24 mois par Wave, Orange Money, MTN ou en caisse. Tous nos articles sont certifiés neufs avec garantie constructeur.
                </p>
            </div>

            <!-- Badge Sécurité FinTech CI -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-cobalt-subtle border border-blue-100 text-cobalt-deep text-[11px] font-bold shrink-0">
                <i data-lucide="shield-check" class="w-4 h-4 text-secondary"></i>
                <span>Paiement échelonné garanti sans caution bancaire</span>
            </div>
        </div>
    </div>

    <!-- Composant Livewire Catalogue complet avec filtres de prix, recherche, catégories et tri -->
    @if(class_exists(\App\Livewire\ProductCatalog::class))
        @livewire('product-catalog', [
            'categorySlug' => $categorySlug ?? null,
            'isKitsOnly' => !empty($isKitsPage),
            'isFlashOnly' => !empty($isFlashPage)
        ])
    @endif
</div>
@endsection

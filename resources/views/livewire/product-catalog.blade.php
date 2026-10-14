<div class="w-full">
    <!-- Barre Supérieure de Filtres & Tri (Sticky Style  Pay) -->
    <div class="bg-surface-container-lowest border-y border-border-subtle py-3 px-4 mb-6 shadow-xs rounded-xl">
        <div class="flex flex-col md:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="font-bold text-on-surface text-[15px]">
                    {{ $products->total() }} articles disponibles
                </span>
                @if(!empty($selectedCategory) || !empty($selectedBrand) || $priceMin || $priceMax || $onlyInStock || !empty($search))
                    <button
                        wire:click="resetFilters"
                        class="text-[12px] font-bold text-primary-container bg-brand-red-subtle px-2.5 py-1 rounded-full flex items-center gap-1 hover:bg-brand-red-hover hover:text-white transition-colors"
                    >
                        <i data-lucide="rotate-cw" class="w-3.5 h-3.5"></i>
                        Réinitialiser les filtres
                    </button>
                @endif
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                <!-- Tri -->
                <div class="flex items-center gap-2">
                    <label class="text-[12px] font-bold text-text-muted shrink-0">Trier par :</label>
                    <select
                        wire:model.live="sort"
                        class="bg-canvas-background border border-border-subtle text-[13px] font-bold text-on-surface rounded-lg px-3 py-1.5 outline-none focus:border-primary-container"
                    >
                        <option value="recent">Nouveautés d'abord</option>
                        <option value="popular">Articles populaires</option>
                        <option value="prix_asc">Prix croissant</option>
                        <option value="prix_desc">Prix décroissant</option>
                    </select>
                </div>

                <!-- Bascule Vue Grille / Vue Liste -->
                <div class="flex items-center bg-canvas-background p-1 rounded-lg border border-border-subtle shrink-0">
                    <button
                        type="button"
                        wire:click="$set('viewMode', 'grid')"
                        class="p-1 rounded {{ $viewMode === 'grid' ? 'bg-surface-container-lowest text-primary-container shadow-xs font-bold' : 'text-text-muted hover:text-on-surface' }}"
                        title="Vue grille"
                    >
                        <i data-lucide="grid" class="w-4.5 h-4.5"></i>
                    </button>
                    <button
                        type="button"
                        wire:click="$set('viewMode', 'list')"
                        class="p-1 rounded {{ $viewMode === 'list' ? 'bg-surface-container-lowest text-primary-container shadow-xs font-bold' : 'text-text-muted hover:text-on-surface' }}"
                        title="Vue liste"
                    >
                        <i data-lucide="list" class="w-4.5 h-4.5"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter-lg items-start">
        <!-- ── Colonne Gauche : Filtres Latéraux ── -->
        <div class="lg:col-span-3 bg-surface-container-lowest p-5 rounded-xl border border-border-subtle shadow-xs space-y-6">
            <!-- Catégories -->
            <div>
                <h4 class="font-headline-sm text-[15px] font-bold text-on-surface mb-3 flex items-center gap-2">
                    <i data-lucide="layout-grid" class="w-4.5 h-4.5 text-primary-container"></i>
                    Catégories
                </h4>
                <div class="space-y-1">
                    <button
                        wire:click="filterCategory('')"
                        class="w-full text-left px-3 py-1.5 rounded-lg text-[13px] font-medium transition-colors flex items-center justify-between {{ empty($selectedCategory) ? 'bg-brand-red-subtle text-primary-container font-bold' : 'text-on-surface hover:bg-canvas-background' }}"
                    >
                        <span>Tout le catalogue</span>
                        <span class="text-[11px] opacity-70">730+</span>
                    </button>
                    @foreach($categories as $cat)
                        <button
                            wire:click="filterCategory('{{ $cat->slug }}')"
                            class="w-full text-left px-3 py-1.5 rounded-lg text-[13px] font-medium transition-colors flex items-center justify-between {{ $selectedCategory === $cat->slug ? 'bg-brand-red-subtle text-primary-container font-bold' : 'text-on-surface hover:bg-canvas-background' }}"
                        >
                            <span class="truncate">{{ $cat->nom }}</span>
                            <span class="text-[11px] opacity-70 bg-slate-100 px-1.5 py-0.2 rounded">{{ $cat->products_count }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Tranche de Prix -->
            <div class="pt-4 border-t border-border-subtle">
                <h4 class="font-headline-sm text-[15px] font-bold text-on-surface mb-3 flex items-center gap-2">
                    <i data-lucide="banknote" class="w-4.5 h-4.5 text-secondary"></i>
                    Fourchette de prix
                </h4>
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <input
                        wire:model.live.debounce.300ms="priceMin"
                        type="number"
                        placeholder="Min (FCFA)"
                        class="bg-canvas-background border border-border-subtle text-[12px] p-2 rounded-lg outline-none"
                    />
                    <input
                        wire:model.live.debounce.300ms="priceMax"
                        type="number"
                        placeholder="Max (FCFA)"
                        class="bg-canvas-background border border-border-subtle text-[12px] p-2 rounded-lg outline-none"
                    />
                </div>
                <div class="flex flex-wrap gap-1.5">
                    <button wire:click="setPriceRange(0, 100000)" class="text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-md text-slate-700">
                        &lt; 100k
                    </button>
                    <button wire:click="setPriceRange(100000, 300000)" class="text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-md text-slate-700">
                        100k - 300k
                    </button>
                    <button wire:click="setPriceRange(300000, 600000)" class="text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-md text-slate-700">
                        300k - 600k
                    </button>
                    <button wire:click="setPriceRange(600000, null)" class="text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-md text-slate-700">
                        &gt; 600k
                    </button>
                </div>
            </div>

            <!-- Marques -->
            @if($brands->isNotEmpty())
                <div class="pt-4 border-t border-border-subtle">
                    <h4 class="font-headline-sm text-[15px] font-bold text-on-surface mb-3 flex items-center gap-2">
                        <i data-lucide="badge-check" class="w-4.5 h-4.5 text-provider-orange"></i>
                        Marques
                    </h4>
                    <div class="flex flex-wrap gap-1.5 max-h-48 overflow-y-auto">
                        @foreach($brands as $brand)
                            <button
                                wire:click="filterBrand('{{ $brand }}')"
                                class="text-[12px] font-semibold px-2.5 py-1 rounded-md border transition-all {{ $selectedBrand === $brand ? 'bg-cobalt-deep text-white border-cobalt-deep font-bold' : 'bg-slate-50 border-slate-200 text-slate-700 hover:border-slate-300' }}"
                            >
                                {{ $brand }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Filtre En Stock -->
            <div class="pt-4 border-t border-border-subtle">
                <label class="flex items-center gap-2 cursor-pointer select-none text-[13px] font-bold text-on-surface">
                    <input
                        wire:model.live="onlyInStock"
                        type="checkbox"
                        class="w-4 h-4 accent-primary-container rounded"
                    />
                    <span>Uniquement les articles en stock</span>
                </label>
            </div>
        </div>

        <!-- ── Colonne Droite : Grille de Produits ── -->
        <div class="lg:col-span-9 pl-2">
            <div wire:loading.class="opacity-50 pointer-events-none" class="transition-opacity duration-200">
                @if($products->isEmpty())
                    <div class="bg-surface-container-lowest p-12 rounded-2xl border border-border-subtle text-center">
                        <i data-lucide="package-x" class="w-12 h-12 text-text-light mb-2 inline-block"></i>
                        <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Aucun article ne correspond à vos critères</h3>
                        <p class="text-text-muted text-body-md mt-1 mb-4">Modifiez ou supprimez vos filtres pour découvrir nos autres offres de crédit.</p>
                        <button
                            wire:click="resetFilters"
                            class="bg-primary-container hover:bg-brand-red-hover text-on-primary font-label-md text-label-md px-6 py-2.5 rounded-lg font-bold transition-colors inline-flex items-center gap-2"
                        >
                            <i data-lucide="rotate-cw" class="w-4.5 h-4.5"></i>
                            Afficher tous les articles
                        </button>
                    </div>
                @else
                    @if($viewMode === 'grid')
                        <!-- Vue Grille avec Espacement Généreux (Spacing) & Liens Fiche Produit -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3  gap-6 sm:gap-7 lg:gap-8">
                            @foreach($products as $prod)
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
                                            @if($prod->prix_cash)
                                                <span class="absolute top-2 left-2 bg-emerald-600 text-white font-label-sm text-label-sm px-2 py-0.5 rounded font-extrabold shadow-xs z-10">
                                                    -7% CASH
                                                </span>
                                            @endif
                                            @if($prod->is_kit)
                                                <span class="absolute top-2 right-2 bg-cobalt-deep text-white font-label-sm text-label-sm px-2 py-0.5 rounded font-extrabold shadow-xs z-10">
                                                    {{ $prod->reference }}
                                                </span>
                                            @endif
                                            @if($prod->has_multiple_images)
                                                <span class="absolute bottom-2 right-2 bg-slate-900/70 backdrop-blur-xs text-white text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1 z-10 shadow-xs">
                                                    <span class="material-symbols-outlined text-[12px]">photo_library</span>
                                                    <span>{{ $prod->images_count }}</span>
                                                </span>
                                            @endif
                                        </a>

                                        <div class="flex items-center gap-1 text-[11px] font-bold text-text-muted uppercase tracking-wider mb-1">
                                            <span>{{ $prod->category->nom ?? 'Article' }}</span>
                                            @if($prod->marque)
                                                <span>· {{ $prod->marque }}</span>
                                            @endif
                                        </div>

                                        <!-- Nom cliquable -->
                                        <h3 class="font-headline-sm text-[15px] font-bold text-on-surface line-clamp-2 mb-2 group-hover:text-primary transition-colors">
                                            <a href="{{ route('product.show', [$prod->id, $prod->slug]) }}" class="hover:text-primary transition-colors block">
                                                {{ $prod->nom }}
                                            </a>
                                        </h3>

                                        <div class="flex items-baseline gap-2 mb-3">
                                            <span class="text-[12px] line-through text-text-light">{{ $prod->prix_formate }}</span>
                                            <span class="text-[15px] font-extrabold text-emerald-700 font-bold">
                                                {{ $prod->prix_cash_formate }} <span class="text-[10px]">CASH</span>
                                            </span>
                                        </div>

                                        <!-- Badge Financement BNPL -->
                                        <div class="bg-brand-red-subtle/80 rounded-xl p-2.5 mb-3.5 border border-primary-container/15">
                                            <span class="text-[11px] font-semibold text-text-muted block">Mensualité dès :</span>
                                            <span class="font-price-installment text-[18px] text-primary-container font-black">
                                                {{ $prod->mensualite_formatee }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 pt-2 border-t border-border-subtle/50">
                                        <button
                                            type="button"
                                            data-add-to-cart
                                            data-id="{{ $prod->id }}"
                                            data-name="{{ $prod->nom }}"
                                            data-price="{{ $prod->prix_vente }}"
                                            data-monthly="{{ $prod->mensualite_indicative ?? round($prod->prix_vente / 12) }}"
                                            data-image="{{ $prod->image_principale }}"
                                            onclick="window.addToCart(this)"
                                            class="flex-1 bg-primary-container hover:bg-brand-red-hover text-on-primary font-label-md text-[13px] py-2.5 rounded-xl font-bold flex items-center justify-center gap-1.5 transition-colors shadow-xs cursor-pointer"
                                        >
                                            <i data-lucide="shopping-cart" class="w-4.5 h-4.5"></i>
                                            Ajouter au panier
                                        </button>
                                        <a
                                            href="{{ route('product.show', [$prod->id, $prod->slug]) }}"
                                            class="w-10 h-10 rounded-xl border border-border-subtle bg-slate-50 hover:bg-slate-100 flex items-center justify-center text-text-muted hover:text-on-surface transition-colors cursor-pointer"
                                            title="Voir la fiche détaillée"
                                        >
                                            <i data-lucide="eye" class="w-4.5 h-4.5"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Vue Liste avec Espacement Généreux (space-y-5) & Liens Fiche Produit -->
                        <div class="space-y-5">
                            @foreach($products as $prod)
                                <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-xs hover:shadow-md border border-border-subtle flex flex-col sm:flex-row items-center gap-5 group">
                                    <!-- Photo cliquable -->
                                    <a href="{{ route('product.show', [$prod->id, $prod->slug]) }}" class="w-28 h-28 aspect-square bg-canvas-background rounded-xl p-2 shrink-0 border border-border-subtle block overflow-hidden group-hover:opacity-95 cursor-pointer flex items-center justify-center" title="{{ $prod->nom }}">
                                        <img
                                            src="{{ $prod->image_principale }}"
                                            alt="{{ $prod->nom }}"
                                            class="max-h-full max-w-full object-contain transition-transform duration-300 group-hover:scale-105"
                                        />
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="bg-cobalt-subtle text-cobalt-deep text-[11px] font-extrabold px-2 py-0.5 rounded">
                                                {{ $prod->reference }}
                                            </span>
                                            @if($prod->is_kit)
                                                <span class="bg-primary-container text-white text-[11px] font-extrabold px-2 py-0.5 rounded">
                                                    KIT MAISON
                                                </span>
                                            @endif
                                            <span class="text-[12px] text-text-muted">{{ $prod->category->nom ?? '' }}</span>
                                        </div>
                                        <!-- Nom cliquable -->
                                        <h3 class="font-bold text-[16px] text-on-surface truncate group-hover:text-primary transition-colors">
                                            <a href="{{ route('product.show', [$prod->id, $prod->slug]) }}" class="hover:text-primary transition-colors block">
                                                {{ $prod->nom }}
                                            </a>
                                        </h3>
                                        <p class="text-[13px] text-text-muted line-clamp-1 mt-1">
                                            {{ $prod->description_courte }}
                                        </p>
                                    </div>
                                    <div class="text-right sm:border-l sm:border-border-subtle sm:pl-5 shrink-0 flex flex-col items-end">
                                        <span class="text-[20px] font-black text-primary-container">
                                            {{ $prod->mensualite_formatee }}
                                        </span>
                                        <span class="text-[12px] text-emerald-700 font-bold block mb-2">
                                            ou {{ $prod->prix_cash_formate }} au comptant
                                        </span>
                                        <button
                                            type="button"
                                            data-add-to-cart
                                            data-id="{{ $prod->id }}"
                                            data-name="{{ $prod->nom }}"
                                            data-price="{{ $prod->prix_vente }}"
                                            data-monthly="{{ $prod->mensualite_indicative ?? round($prod->prix_vente / 12) }}"
                                            data-image="{{ $prod->image_principale }}"
                                            onclick="window.addToCart(this)"
                                            class="bg-primary-container hover:bg-brand-red-hover text-on-primary font-bold text-[13px] px-4 py-2.5 rounded-xl flex items-center gap-1.5 transition-colors shadow-xs cursor-pointer"
                                        >
                                            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                                            Ajouter
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Pagination Livewire -->
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

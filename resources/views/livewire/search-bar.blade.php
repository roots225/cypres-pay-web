<div class="relative w-full" x-data="{ open: @entangle('isOpen') }" @click.outside="open = false">
    <div class="flex w-full items-center bg-canvas-background rounded-xl shadow-inner overflow-hidden border border-border-subtle focus-within:border-primary-container">
        <div class="flex items-center px-space-md py-space-sm text-text-muted px-1">
            <i data-lucide="search" class="w-5 h-5"></i>
        </div>
        <input
            wire:model.live.debounce.150ms="query"
            @focus="if ($wire.results.length > 0) open = true"
            class="w-full bg-transparent border-none outline-none font-body-md text-body-md text-on-surface placeholder:text-text-light py-2"
            placeholder="Rechercher un kit, iPhone, réfrigérateur, ciment..."
            type="text"
            autocomplete="off"
        />
        @if(!empty($query))
            <button wire:click="resetSearch" type="button" class="px-2 text-text-muted hover:text-on-surface">
                <i data-lucide="x" class="w-4.5 h-4.5"></i>
            </button>
        @endif
        <a
            href="/catalogue?q={{ urlencode($query) }}"
            class="bg-primary-container hover:bg-brand-red-hover text-on-primary font-label-md text-label-md px-space-lg py-2 px-2 flex items-center gap-1 transition-colors shrink-0"
        >
            <i data-lucide="search" class="w-4.5 h-4.5"></i>
            <span class="hidden lg:inline">Chercher</span>
        </a>
    </div>

    <!-- Dropdown Résultats Prédictifs -->
    @if($isOpen && !empty($results))
        <div class="absolute top-full left-0 right-0 mt-2 bg-surface-container-lowest rounded-xl shadow-2xl border border-border-subtle z-50 overflow-hidden animate-cp-slide">
            <div class="p-2 border-b border-border-subtle bg-surface-container-low flex items-center justify-between text-label-sm text-text-muted">
                <span>{{ count($results) }} résultats suggérés pour « {{ $query }} »</span>
                <span class="text-[11px] font-bold text-primary-container">Taper Entrée pour tout voir</span>
            </div>
            <div class="max-h-[360px] overflow-y-auto divide-y divide-border-subtle/60">
                @foreach($results as $res)
                    <a
                        href="{{ route('product.show', [$res['id'], $res['slug']]) }}"
                        class="p-3 flex items-center gap-3 hover:bg-brand-red-subtle/50 transition-colors group"
                    >
                        <img
                            src="{{ $res['image_principale'] }}"
                            alt="{{ $res['nom'] }}"
                            class="w-12 h-12 rounded-lg object-contain bg-canvas-background p-1 border border-border-subtle shrink-0"
                        />
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-[11px] text-cobalt-deep px-1.5 py-0.2 rounded bg-cobalt-subtle">{{ $res['reference'] }}</span>
                                @if($res['is_kit'])
                                    <span class="bg-primary-container text-white text-[10px] font-extrabold px-1.5 rounded">KIT</span>
                                @endif
                                <span class="font-bold text-body-md text-on-surface truncate group-hover:text-primary transition-colors">
                                    {{ $res['nom'] }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="font-extrabold text-[13px] text-primary-container">
                                    {{ number_format($res['prix_vente'], 0, ',', ' ') }} FCFA
                                </span>
                                <span class="text-[11px] text-text-muted">
                                    ou dès {{ number_format($res['mensualite_indicative'] ?? round($res['prix_vente']/12), 0, ',', ' ') }} FCFA/m
                                </span>
                            </div>
                        </div>
                        <i data-lucide="arrow-right" class="w-4.5 h-4.5 text-text-muted group-hover:text-primary group-hover:translate-x-1 transition-all"></i>
                    </a>
                @endforeach
            </div>
            <a
                href="/catalogue?q={{ urlencode($query) }}"
                class="block p-2.5 text-center bg-slate-50 hover:bg-slate-100 text-primary-container font-label-md text-label-md font-bold transition-colors border-t border-border-subtle"
            >
                Voir tous les résultats du catalogue &rarr;
            </a>
        </div>
    @elseif($isOpen && strlen(trim($query)) >= 2 && empty($results))
        <div class="absolute top-full left-0 right-0 mt-2 bg-surface-container-lowest p-6 rounded-xl shadow-xl border border-border-subtle z-50 text-center">
            <i data-lucide="search-x" class="w-8 h-8 text-text-light mb-1 inline-block"></i>
            <p class="font-bold text-on-surface text-body-md">Aucun article trouvé pour « {{ $query }} »</p>
            <p class="text-text-muted text-body-sm mt-0.5">Essayez avec un autre mot-clé (ex: TV, frigo, kit, Samsung, ciment...)</p>
        </div>
    @endif
</div>

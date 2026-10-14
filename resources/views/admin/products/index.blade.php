@extends('layouts.admin')

@section('page-title', 'Gestion du Catalogue & Produits')

@section('content')
    <div class="space-y-6">

        <!-- En-tête & Bouton Créer -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Produits & Kits</h2>
                <p class="text-[12px] text-slate-500 mt-0.5">Catalogue officiel Cypress Pay Côte d'Ivoire avec double
                    tarification</p>
            </div>

            <a href="{{ route('admin.products.create') }}"
                class="bg-primary hover:bg-brand-red-hover text-white text-[13px] font-extrabold px-4 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-2 self-start sm:self-auto">
                <i data-lucide="plus-circle" class="w-4.5 h-4.5"></i>
                <span>Nouveau Produit / Kit</span>
            </a>
        </div>

        <!-- Barre de Recherche & Filtres Rapides -->
        <div
            class="bg-white p-4 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">

            <!-- Formulaire de Recherche & Catégorie -->
            <form action="{{ route('admin.products.index') }}" method="GET"
                class="flex-1 flex flex-wrap items-center gap-3 w-full">
                <div
                    class="flex-1 min-w-[200px] flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 border border-slate-200">
                    <i data-lucide="search" class="w-4.5 h-4.5 text-slate-400"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Réf (N°XXX), nom ou marque..."
                        class="w-full bg-transparent border-none outline-none text-[13px] text-slate-800 placeholder-slate-400" />
                </div>

                <select name="category_id"
                    class="px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-[13px] font-semibold text-slate-700 outline-none">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nom }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[12px] font-bold hover:bg-slate-800 transition-colors">
                    Filtrer
                </button>
                @if($search || $categoryId || $filter)
                    <a href="{{ route('admin.products.index') }}"
                        class="text-xs text-slate-400 hover:text-slate-600">Réinitialiser</a>
                @endif
            </form>

            <!-- Filtres Rapides (Badges) -->
            <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto text-[11px] font-bold">
                <a href="{{ route('admin.products.index', ['filter' => 'flash']) }}"
                    class="px-3 py-1.5 rounded-lg {{ $filter === 'flash' ? 'bg-red-100 text-primary font-black' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    ⚡ Ventes Flash
                </a>
                <a href="{{ route('admin.products.index', ['filter' => 'kit']) }}"
                    class="px-3 py-1.5 rounded-lg {{ $filter === 'kit' ? 'bg-emerald-100 text-tertiary font-black' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    📦 Kits Maison
                </a>
                <a href="{{ route('admin.products.index', ['filter' => 'low_stock']) }}"
                    class="px-3 py-1.5 rounded-lg {{ $filter === 'low_stock' ? 'bg-amber-100 text-amber-900 font-black' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    ⚠️ Stock Faible
                </a>
            </div>

        </div>

        <!-- Tableau des Produits -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-4">Article</th>
                            <th class="p-4">Catégorie</th>
                            <th class="p-4">Tarification (Cash vs BNPL)</th>
                            <th class="p-4">Stock</th>
                            <th class="p-4">Badges & Mise en Avant</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($products as $p)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="relative w-12 h-12 rounded-xl bg-slate-100 p-1.5 border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden">
                                            <img src="{{ $p->image_url }}" alt="{{ $p->nom }}"
                                                class="max-h-full max-w-full object-contain" />
                                            @if($p->has_multiple_images)
                                                <span
                                                    class="absolute bottom-0.5 right-0.5 px-1 py-0.2 rounded bg-slate-900/80 text-white text-[8px] font-black"
                                                    title="{{ $p->images_count }} photos">
                                                    +{{ $p->images_count - 1 }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="max-w-xs">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="px-2 py-0.5 rounded font-mono font-black text-[11px] bg-slate-900 text-white">
                                                    {{ $p->reference }}
                                                </span>
                                                <span
                                                    class="text-[11px] font-bold text-slate-400 uppercase">{{ $p->marque }}</span>
                                            </div>
                                            <span class="font-bold text-slate-900 line-clamp-1 mt-0.5" title="{{ $p->nom }}">
                                                {{ $p->nom }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                        {{ $p->category?->nom ?? 'Non catégorisé' }}
                                    </span>
                                </td>

                                <td class="p-4">
                                    <div>
                                        <span
                                            class="font-black text-slate-900 block">{{ number_format($p->prix_vente, 0, ',', ' ') }}
                                            FCFA</span>
                                        <span class="text-[11px] text-emerald-600 font-bold block">
                                            Cash (-7%) : {{ number_format($p->prix_cash, 0, ',', ' ') }} F
                                        </span>
                                        <span class="text-[10px] text-slate-400 block font-semibold">
                                            Dès {{ number_format($p->mensualite_indicative, 0, ',', ' ') }} F/mois
                                        </span>
                                    </div>
                                </td>

                                <td class="p-4">
                                    @if($p->stock <= 0)
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-800">
                                            Rupture (0)
                                        </span>
                                    @elseif($p->stock <= 5)
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                            {{ $p->stock }} restants
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800">
                                            {{ $p->stock }} en stock
                                        </span>
                                    @endif
                                </td>

                                <!-- Toggles en direct -->
                                <td class="p-4">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <!-- Flash Sale Toggle -->
                                        <form action="{{ route('admin.products.toggle', [$p, 'is_flash']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="Basculer Vente Flash"
                                                class="px-2 py-0.5 rounded text-[10px] font-extrabold transition-all {{ $p->is_flash ? 'bg-red-600 text-white shadow-sm' : 'bg-slate-100 text-slate-400 hover:bg-slate-200' }}">
                                                Flash
                                            </button>
                                        </form>

                                        <!-- Kit Toggle -->
                                        <form action="{{ route('admin.products.toggle', [$p, 'is_kit']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="Basculer Kit Maison"
                                                class="px-2 py-0.5 rounded text-[10px] font-extrabold transition-all {{ $p->is_kit ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-400 hover:bg-slate-200' }}">
                                                Kit
                                            </button>
                                        </form>

                                        <!-- Vedette Toggle -->
                                        <form action="{{ route('admin.products.toggle', [$p, 'is_vedette']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="Basculer Coup de Cœur"
                                                class="px-2 py-0.5 rounded text-[10px] font-extrabold transition-all {{ $p->is_vedette ? 'bg-amber-500 text-slate-900 shadow-sm' : 'bg-slate-100 text-slate-400 hover:bg-slate-200' }}">
                                                Vedette
                                            </button>
                                        </form>
                                    </div>
                                </td>

                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('product.show', [$p->id, $p->slug]) }}" target="_blank"
                                            class="p-2 rounded-xl text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors"
                                            title="Voir fiche boutique">
                                            <i data-lucide="external-link" class="w-4.5 h-4.5"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $p) }}"
                                            class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                                            title="Modifier">
                                            <i data-lucide="square-pen" class="w-4.5 h-4.5"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $p) }}" method="POST"
                                            onsubmit="return confirm('Confirmez-vous la suppression de ce produit ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                                title="Supprimer">
                                                <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center text-slate-400">
                                    Aucun produit ne correspond aux critères sélectionnés.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
@extends('layouts.admin')

@section('page-title', 'Gestion des Catégories du Catalogue')

@section('content')
    <div class="space-y-6"
        x-data="{ modalOpen: false, isEdit: false, editId: null, formNom: '', formSlug: '', formIcone: 'category', formDescription: '', formOrdre: 0, formFeatured: false }">

        <!-- En-tête & Bouton Créer -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Catégories</h2>
                <p class="text-[12px] text-slate-500 mt-0.5">Organisation thématique des articles vendus sur Cypress Pay</p>
            </div>

            <button
                @click="isEdit = false; editId = null; formNom = ''; formSlug = ''; formIcone = 'category'; formDescription = ''; formOrdre = 0; formFeatured = false; modalOpen = true"
                class="bg-primary hover:bg-brand-red-hover text-white text-[13px] font-extrabold px-4 py-2.5 rounded-lg shadow-md transition-all flex items-center gap-2 self-start sm:self-auto cursor-pointer">
                <i data-lucide="plus" class="w-4.5 h-4.5"></i>
                <span>Nouvelle catégorie</span>
            </button>
        </div>

        <!-- Tableau des Catégories -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="p-4">Ordre</th>
                            <th class="p-4">Catégorie</th>
                            <th class="p-4">Slug URL</th>
                            <th class="p-4">Articles Rattachés</th>
                            <th class="p-4">Mise en Avant</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($categories as $cat)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="p-4 font-mono font-bold text-slate-400 text-xs">
                                    #{{ $cat->ordre }}
                                </td>

                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold shrink-0">
                                            <i data-lucide="{{ $cat->lucide_icon }}" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 block">{{ $cat->nom }}</span>
                                            @if($cat->description)
                                                <span
                                                    class="text-[11px] text-slate-400 block line-clamp-1">{{ $cat->description }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="p-4 font-mono text-[11px] text-slate-500">
                                    /catalogue/{{ $cat->slug }}
                                </td>

                                <td class="p-4">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[11px] font-black {{ $cat->products_count > 0 ? 'bg-blue-50 text-secondary' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $cat->products_count }} article(s)
                                    </span>
                                </td>

                                <td class="p-4">
                                    @if($cat->is_featured)
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                            Accueil
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>

                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('catalog.category', $cat->slug) }}" target="_blank"
                                            class="p-2 rounded-xl text-slate-400 hover:text-slate-900 hover:bg-slate-100"
                                            title="Voir sur la boutique">
                                            <i data-lucide="external-link" class="w-4.5 h-4.5"></i>
                                        </a>

                                        <button
                                            @click="isEdit = true; editId = {{ $cat->id }}; formNom = '{{ addslashes($cat->nom) }}'; formSlug = '{{ $cat->slug }}'; formIcone = '{{ $cat->icone }}'; formDescription = '{{ addslashes($cat->description ?? '') }}'; formOrdre = {{ $cat->ordre }}; formFeatured = {{ $cat->is_featured ? 'true' : 'false' }}; modalOpen = true"
                                            class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 cursor-pointer"
                                            title="Modifier">
                                            <i data-lucide="square-pen" class="w-4.5 h-4.5"></i>
                                        </button>

                                        @if($cat->products_count === 0)
                                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST"
                                                onsubmit="return confirm('Confirmez-vous la suppression de cette catégorie ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 cursor-pointer"
                                                    title="Supprimer">
                                                    <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center text-slate-400">
                                    Aucune catégorie enregistrée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modale Alpine.js de Création / Modification de Catégorie -->
        <div x-show="modalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            style="display: none;">
            <div @click.away="modalOpen = false"
                class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 animate-scale-up">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <h3 class="font-extrabold text-base text-slate-900"
                        x-text="isEdit ? 'Modifier la catégorie' : 'Nouvelle catégorie'"></h3>
                    <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form :action="isEdit ? '/admin/categories/' + editId : '{{ route('admin.categories.store') }}'"
                    method="POST" class="space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT" />
                    </template>

                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Nom *</label>
                        <input type="text" name="nom" x-model="formNom" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]"
                            placeholder="Ex: Électroménager, Énergie Solaire..." />
                    </div>

                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Slug URL (facultatif)</label>
                        <input type="text" name="slug" x-model="formSlug"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px] font-mono"
                            placeholder="Ex: electromenager" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[12px] font-bold text-slate-700 mb-1">Icône Material Symbol</label>
                            <input type="text" name="icone" x-model="formIcone"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px] font-mono"
                                placeholder="kitchen, bolt, etc." />
                        </div>
                        <div>
                            <label class="block text-[12px] font-bold text-slate-700 mb-1">Ordre d'affichage</label>
                            <input type="number" name="ordre" x-model="formOrdre"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Description</label>
                        <textarea name="description" x-model="formDescription" rows="2"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]"
                            placeholder="Courte description d'en-tête..."></textarea>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 cursor-pointer text-[13px] font-bold text-slate-700">
                            <input type="checkbox" name="is_featured" value="1" x-model="formFeatured"
                                class="rounded text-primary" />
                            <span>Mettre en avant sur la page d'accueil</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="modalOpen = false"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-[13px] font-bold">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-5 py-2 rounded-xl bg-primary hover:bg-brand-red-hover text-white text-[13px] font-extrabold shadow-sm">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
@extends('layouts.admin')

@section('page-title', 'Formules BNPL & Paramétrage des Taux')

@section('content')
<div class="space-y-6" x-data="{ modalOpen: false, isEdit: false, editId: null, formNom: '', formDuree: 3, formEcheances: 3, formFreq: 'mensuel', formApport: 20, formTaux: 15, formFrais: 0, formDesc: '', formActif: true }">
    
    <!-- En-tête & Bouton Créer -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Moteur Financier d'Échéancier</h2>
            <p class="text-[12px] text-slate-500 mt-0.5">Configuration des règles d'apport initial, des durées et des taux de majoration</p>
        </div>

        <button 
            @click="isEdit = false; editId = null; formNom = ''; formDuree = 3; formEcheances = 3; formFreq = 'mensuel'; formApport = 20; formTaux = 15; formFrais = 0; formDesc = ''; formActif = true; modalOpen = true"
            class="bg-primary hover:bg-brand-red-hover text-white text-[13px] font-extrabold px-4 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-2 self-start sm:self-auto cursor-pointer"
        >
            <i data-lucide="plus" class="w-4.5 h-4.5"></i>
            <span>Nouvelle Formule BNPL</span>
        </button>
    </div>

    <!-- Tableau des Formules -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[13px]">
                <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="p-4">Nom de la Formule</th>
                        <th class="p-4">Durée & Échéances</th>
                        <th class="p-4">Fréquence</th>
                        <th class="p-4">Apport Initial Requis</th>
                        <th class="p-4">Taux de Majoration</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($formulas as $f)
                        <tr class="hover:bg-slate-50/70 transition-colors {{ ! $f->actif ? 'opacity-50' : '' }}">
                            <td class="p-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ $f->duree_mois }}m
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $f->nom }}</span>
                                        <span class="text-[11px] text-slate-400 block">{{ $f->description }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="p-4 font-semibold text-slate-800">
                                @if($f->duree_mois === 0)
                                    <span class="text-emerald-700 font-bold">Comptant (Cash)</span>
                                @else
                                    <span>{{ $f->duree_mois }} mois ({{ $f->nombre_echeances }} échéances)</span>
                                @endif
                            </td>

                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-semibold text-[11px] capitalize">
                                    {{ $f->frequence }}
                                </span>
                            </td>

                            <td class="p-4 font-black text-slate-900">
                                {{ $f->pourcentage_apport }}%
                            </td>

                            <td class="p-4">
                                @if($f->taux_majoration == 0)
                                    <span class="px-2 py-0.5 rounded text-[11px] font-black bg-emerald-100 text-emerald-800">0% (Cash -7%)</span>
                                @else
                                    <span class="font-mono font-bold text-slate-800">+{{ $f->taux_majoration }}%</span>
                                @endif
                            </td>

                            <td class="p-4">
                                <form action="{{ route('admin.formulas.toggle', $f) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-extrabold cursor-pointer transition-all {{ $f->actif ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                        {{ $f->actif ? 'Actif' : 'Désactivé' }}
                                    </button>
                                </form>
                            </td>

                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button 
                                        @click="isEdit = true; editId = {{ $f->id }}; formNom = '{{ addslashes($f->nom) }}'; formDuree = {{ $f->duree_mois }}; formEcheances = {{ $f->nombre_echeances }}; formFreq = '{{ $f->frequence }}'; formApport = {{ $f->pourcentage_apport }}; formTaux = {{ $f->taux_majoration }}; formFrais = {{ $f->frais_dossier }}; formDesc = '{{ addslashes($f->description ?? '') }}'; formActif = {{ $f->actif ? 'true' : 'false' }}; modalOpen = true"
                                        class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 cursor-pointer" 
                                        title="Modifier"
                                    >
                                        <i data-lucide="pencil" class="w-4.5 h-4.5"></i>
                                    </button>

                                    @if($f->orders()->count() === 0)
                                        <form action="{{ route('admin.formulas.destroy', $f) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cette formule ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 cursor-pointer" title="Supprimer">
                                                <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-400">
                                Aucune formule enregistrée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modale Alpine.js de Création / Modification de Formule -->
    <div 
        x-show="modalOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        style="display: none;"
    >
        <div 
            @click.away="modalOpen = false" 
            class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 animate-scale-up"
        >
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <h3 class="font-extrabold text-base text-slate-900" x-text="isEdit ? 'Modifier la formule BNPL' : 'Créer une formule BNPL'"></h3>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form :action="isEdit ? '/admin/formules/' + editId : '{{ route('admin.formulas.store') }}'" method="POST" class="space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT"/>
                </template>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Nom de la formule *</label>
                    <input type="text" name="nom" x-model="formNom" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]" placeholder="Ex: 3 Mois (90 Jours), 60 Jours Bi-mensuel..."/>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Durée (en mois) *</label>
                        <input type="number" name="duree_mois" x-model="formDuree" required min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]"/>
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Nombre d'échéances *</label>
                        <input type="number" name="nombre_echeances" x-model="formEcheances" required min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]"/>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Fréquence *</label>
                        <select name="frequence" x-model="formFreq" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px] bg-white">
                            <option value="mensuel">Mensuel</option>
                            <option value="bimensuel">Bi-mensuel</option>
                            <option value="hebdomadaire">Hebdo</option>
                            <option value="quotidien">Journalier</option>
                            <option value="cash">Cash Comptant</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Apport (%) *</label>
                        <input type="number" name="pourcentage_apport" x-model="formApport" required min="0" max="100" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]"/>
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Majoration (%) *</label>
                        <input type="number" step="0.5" name="taux_majoration" x-model="formTaux" required min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]"/>
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Courte description explicative</label>
                    <textarea name="description" x-model="formDesc" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]" placeholder="Ex: 20% d'apport, 3 mensualités espacées de 30 jours..."></textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer text-[13px] font-bold text-slate-700">
                        <input type="checkbox" name="actif" value="1" x-model="formActif" class="rounded text-primary"/>
                        <span>Formule active et visible au simulateur et checkout</span>
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-[13px] font-bold">
                        Annuler
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-brand-red-hover text-white text-[13px] font-extrabold shadow-sm">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@extends('layouts.admin')

@section('page-title', 'Conformité & Dossiers KYC Client')

@section('content')
<div class="space-y-6" x-data="{ viewModal: false, rejectModal: false, activeKyc: null, rejectId: null, rejectMotif: '' }">
    
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Vérification des Identités & Pièces Justificatives</h2>
            <p class="text-[12px] text-slate-500 mt-0.5">Examen des CNI, passeports et signatures avant déblocage de la livraison</p>
        </div>
    </div>

    <!-- Filtres par Statut KYC -->
    <div class="flex items-center gap-2 overflow-x-auto text-[12px] font-bold">
        <a href="{{ route('admin.kyc.index', ['statut' => 'en_attente']) }}" class="px-3.5 py-2 rounded-xl {{ $status === 'en_attente' ? 'bg-amber-500 text-slate-900 font-extrabold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            En Attente de Validation ({{ $counts['en_attente'] }})
        </a>
        <a href="{{ route('admin.kyc.index', ['statut' => 'valide']) }}" class="px-3.5 py-2 rounded-xl {{ $status === 'valide' ? 'bg-emerald-600 text-white font-extrabold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Validés ({{ $counts['valide'] }})
        </a>
        <a href="{{ route('admin.kyc.index', ['statut' => 'rejete']) }}" class="px-3.5 py-2 rounded-xl {{ $status === 'rejete' ? 'bg-red-600 text-white font-extrabold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Rejetés ({{ $counts['rejete'] }})
        </a>
        <a href="{{ route('admin.kyc.index', ['statut' => 'all']) }}" class="px-3.5 py-2 rounded-xl {{ $status === 'all' ? 'bg-slate-900 text-white font-extrabold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Tous les Dossiers ({{ $counts['all'] }})
        </a>
    </div>

    <!-- Tableau des Dossiers KYC -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[13px]">
                <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="p-4">Client</th>
                        <th class="p-4">Type de Pièce</th>
                        <th class="p-4">N° de Pièce</th>
                        <th class="p-4">Signature Tactile</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4">Date de Soumission</th>
                        <th class="p-4 text-right">Contrôle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($profiles as $kyc)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 font-black text-xs flex items-center justify-center shrink-0">
                                        {{ substr($kyc->user?->prenom ?? 'C', 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $kyc->user?->nom_complet }}</span>
                                        <span class="text-[11px] font-mono text-slate-500">+225 {{ $kyc->user?->telephone }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 font-bold uppercase text-[11px]">
                                    {{ $kyc->type_piece }}
                                </span>
                            </td>

                            <td class="p-4 font-mono font-bold text-slate-800">
                                {{ $kyc->numero_piece ?? 'Non renseigné' }}
                            </td>

                            <td class="p-4">
                                @if($kyc->signature_electronique)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800 flex items-center gap-1 w-max">
                                        <i data-lucide="pen-tool" class="w-3.5 h-3.5"></i> Signé
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">Non signée</span>
                                @endif
                            </td>

                            <td class="p-4">
                                @php
                                    $badge = match($kyc->statut) {
                                        'valide' => 'bg-emerald-100 text-emerald-800',
                                        'en_attente' => 'bg-amber-100 text-amber-800 animate-pulse',
                                        'rejete' => 'bg-red-100 text-red-800',
                                        default => 'bg-slate-100 text-slate-600',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold capitalize {{ $badge }}">
                                    {{ str_replace('_', ' ', $kyc->statut) }}
                                </span>
                                @if($kyc->motif_rejet)
                                    <span class="text-[10px] text-red-600 block mt-0.5 italic">Motif : {{ $kyc->motif_rejet }}</span>
                                @endif
                            </td>

                            <td class="p-4 text-slate-500 text-[12px]">
                                {{ $kyc->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Valider en 1-clic -->
                                    @if($kyc->statut !== 'valide')
                                        <form action="{{ route('admin.kyc.validate', $kyc) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition-colors flex items-center justify-center" title="Valider le dossier">
                                                <i data-lucide="check" class="w-4.5 h-4.5"></i>
                                            </button>
                                        </form>

                                        <!-- Rejeter -->
                                        <button 
                                            @click="rejectId = {{ $kyc->id }}; rejectModal = true" 
                                            class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 transition-colors flex items-center justify-center" 
                                            title="Rejeter le dossier"
                                        >
                                            <i data-lucide="x" class="w-4.5 h-4.5"></i>
                                        </button>
                                    @endif

                                    <!-- Voir les pièces -->
                                    <button 
                                        @click="activeKyc = {{ json_encode($kyc) }}; viewModal = true"
                                        class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors flex items-center justify-center" 
                                        title="Voir les pièces justificatives"
                                    >
                                        <i data-lucide="eye" class="w-4.5 h-4.5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-400">
                                Aucun dossier KYC ne correspond à ce filtre.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($profiles->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $profiles->links() }}
            </div>
        @endif
    </div>

    <!-- Modale Alpine.js : Visualiseur de Pièces Justificatives -->
    <div 
        x-show="viewModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        style="display: none;"
    >
        <div 
            @click.away="viewModal = false" 
            class="bg-white rounded-3xl p-6 sm:p-8 max-w-xl w-full shadow-2xl border border-slate-200 animate-scale-up space-y-4 max-h-[90vh] overflow-y-auto"
        >
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-extrabold text-base text-slate-900">Dossier de Pièces Justificatives</h3>
                <button @click="viewModal = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <template x-if="activeKyc">
                <div class="space-y-4 text-[13px]">
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-slate-400 text-xs block">Type de pièce & Numéro :</span>
                        <strong class="text-slate-900 uppercase" x-text="activeKyc.type_piece"></strong>
                        <span class="font-mono font-bold" x-text="activeKyc.numero_piece ? '· N° ' + activeKyc.numero_piece : ''"></span>
                    </div>

                    <div x-show="activeKyc.signature_electronique">
                        <span class="text-xs font-bold text-slate-700 block mb-1">Signature Numérique Contractuelle :</span>
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center">
                            <img :src="activeKyc.signature_electronique" class="max-h-28 object-contain" />
                        </div>
                    </div>

                    <div x-show="activeKyc.photo_piece_recto">
                        <span class="text-xs font-bold text-slate-700 block mb-1">Photo Recto de la Pièce :</span>
                        <img :src="activeKyc.photo_piece_recto" class="w-full rounded-2xl border border-slate-200" />
                    </div>

                    <div x-show="activeKyc.photo_piece_verso">
                        <span class="text-xs font-bold text-slate-700 block mb-1">Photo Verso de la Pièce :</span>
                        <img :src="activeKyc.photo_piece_verso" class="w-full rounded-2xl border border-slate-200" />
                    </div>

                    <div x-show="activeKyc.photo_facture_cie_sodeci">
                        <span class="text-xs font-bold text-slate-700 block mb-1">Justificatif Domicile (Facture CIE/SODECI) :</span>
                        <img :src="activeKyc.photo_facture_cie_sodeci" class="w-full rounded-2xl border border-slate-200" />
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Modale Rejet avec Motif -->
    <div 
        x-show="rejectModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        style="display: none;"
    >
        <div 
            @click.away="rejectModal = false" 
            class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 animate-scale-up"
        >
            <h3 class="font-extrabold text-base text-red-600 mb-2">Rejeter le dossier KYC</h3>
            <p class="text-[12px] text-slate-500 mb-4">Veuillez indiquer la raison du rejet (ex: CNI expirée, image floue, facture non conforme).</p>

            <form :action="'/admin/kyc/' + rejectId + '/rejeter'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Motif du rejet *</label>
                    <textarea name="motif_rejet" required rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-red-500 outline-none text-[13px]" placeholder="Ex: Pièce d'identité illisible ou expirée..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="rejectModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-[12px] font-bold">
                        Annuler
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-[12px] font-extrabold">
                        Confirmer le Rejet
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

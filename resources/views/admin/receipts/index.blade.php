@extends('layouts.admin')

@section('page-title', 'Registre des Quittances & Reçus Officiels')

@section('content')
<div class="space-y-6" x-data="{ modalOpen: false }">
    
    <!-- En-tête & Bouton Émettre Quittance -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Registre Infalsifiable des Versements</h2>
            <p class="text-[12px] text-slate-500 mt-0.5">Codes cryptographiques à 6 caractères générés sans caractères ambigus (0/O, 1/I)</p>
        </div>

        <button 
            @click="modalOpen = true"
            class="bg-emerald-600 hover:bg-emerald-700 text-white text-[13px] font-extrabold px-4 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-2 self-start sm:self-auto cursor-pointer"
        >
            <i data-lucide="receipt" class="w-4.5 h-4.5"></i>
            <span>Émettre une Quittance Manuelle</span>
        </button>
    </div>

    <!-- 2 Cartes Résumé Financier du Registre -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-500 uppercase block">Total Quittancé (Cumul)</span>
                <span class="text-2xl font-black text-slate-900 mt-0.5 block">{{ number_format($totalAmount, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                <i data-lucide="landmark" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-500 uppercase block">Encaissé Aujourd'hui</span>
                <span class="text-2xl font-black text-emerald-600 mt-0.5 block">{{ number_format($todayAmount, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="calendar" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Barre de Recherche par Code & Canal -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
        <form action="{{ route('admin.receipts.index') }}" method="GET" class="flex-1 flex flex-wrap items-center gap-3 w-full">
            <div class="flex-1 min-w-[200px] flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 border border-slate-200">
                <i data-lucide="search" class="w-4.5 h-4.5 text-slate-400"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $search }}"
                    placeholder="Code à 6 caractères (ex: 28B7S0), nom ou réf..." 
                    class="w-full bg-transparent border-none outline-none text-[13px] text-slate-800 placeholder-slate-400 font-mono font-bold uppercase"
                />
            </div>

            <select name="canal" class="px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-[13px] font-semibold text-slate-700 outline-none">
                <option value="">Tous les canaux</option>
                <option value="wave" {{ $canal === 'wave' ? 'selected' : '' }}>Wave CI</option>
                <option value="orange_money" {{ $canal === 'orange_money' ? 'selected' : '' }}>Orange Money</option>
                <option value="mtn_momo" {{ $canal === 'mtn_momo' ? 'selected' : '' }}>MTN MoMo</option>
                <option value="cash" {{ $canal === 'cash' ? 'selected' : '' }}>Espèces en Caisse</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[12px] font-bold hover:bg-slate-800 transition-colors">
                Filtrer
            </button>
            @if($search || $canal)
                <a href="{{ route('admin.receipts.index') }}" class="text-xs text-slate-400 hover:text-slate-600">Effacer</a>
            @endif
        </form>
    </div>

    <!-- Tableau des Quittances -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[13px]">
                <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="p-4">Code Reçu (6 Car.)</th>
                        <th class="p-4">Montant Versé</th>
                        <th class="p-4">Client & N° Commande</th>
                        <th class="p-4">Canal & Réf</th>
                        <th class="p-4">Agent Encaisseur</th>
                        <th class="p-4">Date & Heure</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($receipts as $r)
                        <tr class="hover:bg-slate-50/70 transition-colors {{ ! $r->est_valide ? 'opacity-40 bg-red-50/20' : '' }}">
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-lg bg-red-50 text-primary font-mono font-black text-sm border border-red-200 block w-max">
                                    {{ $r->code_recu }}
                                </span>
                            </td>

                            <td class="p-4 font-black text-slate-900 text-sm">
                                {{ number_format($r->montant, 0, ',', ' ') }} FCFA
                            </td>

                            <td class="p-4">
                                <span class="font-bold text-slate-900 block">{{ $r->user?->nom_complet }}</span>
                                <a href="{{ route('admin.orders.show', $r->order_id) }}" class="text-[11px] font-mono text-secondary hover:underline">
                                    {{ $r->order?->numero_commande }}
                                </a>
                            </td>

                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase block w-max 
                                    {{ $r->canal === 'wave' ? 'bg-sky-100 text-sky-800' : '' }}
                                    {{ $r->canal === 'orange_money' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $r->canal === 'mtn_momo' ? 'bg-yellow-100 text-yellow-900' : '' }}
                                    {{ $r->canal === 'cash' ? 'bg-slate-100 text-slate-800' : '' }}">
                                    {{ str_replace('_', ' ', $r->canal) }}
                                </span>
                                @if($r->reference_externe)
                                    <span class="text-[10px] font-mono text-slate-400 block mt-0.5">{{ $r->reference_externe }}</span>
                                @endif
                            </td>

                            <td class="p-4 text-slate-600 text-[12px]">
                                {{ $r->agent?->nom_complet ?? 'Automatique (API Webhook)' }}
                            </td>

                            <td class="p-4 text-slate-500 text-[12px]">
                                {{ $r->date_versement->format('d/m/Y H:i') }}
                            </td>

                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('verifier', ['code' => $r->code_recu]) }}" target="_blank" class="p-2 rounded-xl text-slate-400 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center" title="Vérifier sur la page publique">
                                        <i data-lucide="shield-check" class="w-4.5 h-4.5"></i>
                                    </a>

                                    <form action="{{ route('admin.receipts.toggle', $r) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 flex items-center justify-center" title="{{ $r->est_valide ? 'Invalider quittance' : 'Réactiver quittance' }}">
                                            @if($r->est_valide)
                                                <i data-lucide="ban" class="w-4.5 h-4.5"></i>
                                            @else
                                                <i data-lucide="rotate-ccw" class="w-4.5 h-4.5"></i>
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-400">
                                Aucune quittance trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($receipts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $receipts->links() }}
            </div>
        @endif
    </div>

    <!-- Modale Émission Quittance Manuelle -->
    <div 
        x-show="modalOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        style="display: none;"
    >
        <div 
            @click.away="modalOpen = false" 
            class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 animate-scale-up"
        >
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-extrabold text-base text-slate-900">Émettre une Quittance Manuelle</h3>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('admin.receipts.store') }}" method="POST" class="space-y-4 mt-4">
                @csrf
                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Commande Rattachée *</label>
                    <select name="order_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px] bg-white">
                        <option value="">-- Sélectionner une commande --</option>
                        @foreach($orders as $o)
                            <option value="{{ $o->id }}">
                                {{ $o->numero_commande }} · {{ $o->user?->nom_complet }} ({{ number_format($o->montant_total, 0, ',', ' ') }} F)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Montant Encaissé (FCFA) *</label>
                    <input type="number" name="montant" required step="500" min="500" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[14px] font-black" placeholder="Ex: 25000"/>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Canal de Paiement *</label>
                    <select name="canal" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px] bg-white">
                        <option value="cash">Espèces en caisse agence</option>
                        <option value="wave">Wave CI</option>
                        <option value="orange_money">Orange Money</option>
                        <option value="mtn_momo">MTN Mobile Money</option>
                        <option value="moov_money">Moov Money</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Référence Transactionnelle Externe</label>
                    <input type="text" name="reference_externe" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]" placeholder="Ex: TX-WV-991204..."/>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-[13px] font-bold">
                        Annuler
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[13px] font-extrabold shadow-sm">
                        Émettre & Générer Code 6 Caractères
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

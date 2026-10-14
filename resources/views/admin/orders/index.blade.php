@extends('layouts.admin')

@section('page-title', 'Gestion des Commandes & Échéanciers')

@section('content')
<div class="space-y-6">
    
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Dossiers de Crédit & Commandes</h2>
            <p class="text-[12px] text-slate-500 mt-0.5">Suivi de l'approbation KYC, des expéditions et du recouvrement des échéances</p>
        </div>
    </div>

    <!-- Filtres par Statut (Badges) -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-[12px] font-bold">
        <a href="{{ route('admin.orders.index') }}" class="px-3.5 py-2 rounded-xl {{ empty($status) ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Toutes ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.orders.index', ['statut' => 'en_attente_kyc']) }}" class="px-3.5 py-2 rounded-xl {{ $status === 'en_attente_kyc' ? 'bg-amber-500 text-slate-900 font-extrabold' : 'bg-white border border-slate-200 text-amber-700 hover:bg-amber-50' }}">
            En Attente KYC ({{ $counts['en_attente_kyc'] }})
        </a>
        <a href="{{ route('admin.orders.index', ['statut' => 'valide']) }}" class="px-3.5 py-2 rounded-xl {{ $status === 'valide' ? 'bg-blue-600 text-white font-extrabold' : 'bg-white border border-slate-200 text-blue-700 hover:bg-blue-50' }}">
            Validées ({{ $counts['valide'] }})
        </a>
        <a href="{{ route('admin.orders.index', ['statut' => 'en_cours']) }}" class="px-3.5 py-2 rounded-xl {{ $status === 'en_cours' ? 'bg-purple-600 text-white font-extrabold' : 'bg-white border border-slate-200 text-purple-700 hover:bg-purple-50' }}">
            En Cours BNPL ({{ $counts['en_cours'] }})
        </a>
        <a href="{{ route('admin.orders.index', ['statut' => 'livre']) }}" class="px-3.5 py-2 rounded-xl {{ $status === 'livre' ? 'bg-sky-600 text-white font-extrabold' : 'bg-white border border-slate-200 text-sky-700 hover:bg-sky-50' }}">
            Livrées ({{ $counts['livre'] }})
        </a>
        <a href="{{ route('admin.orders.index', ['statut' => 'solde']) }}" class="px-3.5 py-2 rounded-xl {{ $status === 'solde' ? 'bg-emerald-600 text-white font-extrabold' : 'bg-white border border-slate-200 text-emerald-700 hover:bg-emerald-50' }}">
            Soldées 100% ({{ $counts['solde'] }})
        </a>
    </div>

    <!-- Barre de Recherche -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex-1 flex items-center gap-3">
            @if($status)
                <input type="hidden" name="statut" value="{{ $status }}"/>
            @endif
            <i data-lucide="search" class="w-5 h-5 text-slate-400"></i>
            <input 
                type="text" 
                name="q" 
                value="{{ $search }}"
                placeholder="N° Commande (ex: CP-CMD-...), nom client ou téléphone (+225)..." 
                class="w-full bg-transparent border-none outline-none text-[13px] text-slate-800 placeholder-slate-400"
            />
            @if($search)
                <a href="{{ route('admin.orders.index', ['statut' => $status]) }}" class="text-xs text-slate-400 hover:text-slate-600">Effacer</a>
            @endif
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[12px] font-bold hover:bg-slate-800 transition-colors">
                Rechercher
            </button>
        </form>
    </div>

    <!-- Tableau des Commandes -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[13px]">
                <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="p-4">N° Commande</th>
                        <th class="p-4">Client Bénéficiaire</th>
                        <th class="p-4">Formule Choisie</th>
                        <th class="p-4">Total & Apport</th>
                        <th class="p-4">Échéances Payées</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $o)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-4">
                                <span class="font-mono font-black text-slate-900 block">{{ $o->numero_commande }}</span>
                                <span class="text-[11px] text-slate-400">{{ $o->created_at->format('d/m/Y H:i') }}</span>
                            </td>

                            <td class="p-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ substr($o->user?->prenom ?? 'C', 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $o->user?->nom_complet }}</span>
                                        <span class="text-[11px] font-mono text-slate-500">+225 {{ $o->telephone_contact }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 font-bold text-[11px] block w-max">
                                    {{ $o->formula?->nom ?? 'Standard' }}
                                </span>
                                <span class="text-[10px] text-slate-400 capitalize block mt-0.5">Canal : {{ str_replace('_', ' ', $o->canal_paiement) }}</span>
                            </td>

                            <td class="p-4">
                                <span class="font-black text-slate-900 block">{{ number_format($o->montant_total, 0, ',', ' ') }} F</span>
                                <span class="text-[11px] text-emerald-600 font-bold block">
                                    Apport : {{ number_format($o->apport_initial, 0, ',', ' ') }} F
                                </span>
                            </td>

                            <td class="p-4">
                                @php
                                    $paidCount = $o->installments->where('statut', 'paye')->count();
                                    $totalCount = $o->installments->count();
                                @endphp
                                <span class="font-bold text-slate-800">{{ $paidCount }} / {{ $totalCount }} payées</span>
                                @if($totalCount > 0)
                                    <div class="w-24 bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ round(($paidCount / $totalCount) * 100) }}%"></div>
                                    </div>
                                @endif
                            </td>

                            <td class="p-4">
                                @php
                                    $badgeColor = match($o->statut) {
                                        'en_attente_kyc' => 'bg-amber-100 text-amber-800',
                                        'valide' => 'bg-blue-100 text-blue-800',
                                        'en_cours' => 'bg-purple-100 text-purple-800',
                                        'livre' => 'bg-sky-100 text-sky-800',
                                        'solde' => 'bg-emerald-100 text-emerald-800',
                                        'annule' => 'bg-red-100 text-red-800',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold capitalize {{ $badgeColor }}">
                                    {{ str_replace('_', ' ', $o->statut) }}
                                </span>
                            </td>

                            <td class="p-4 text-right">
                                <a href="{{ route('admin.orders.show', $o) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-[12px] transition-colors">
                                    <span>Gérer</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-400">
                                Aucune commande ne correspond aux critères.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

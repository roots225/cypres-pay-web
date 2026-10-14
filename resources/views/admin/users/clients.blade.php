@extends('layouts.admin')

@section('page-title', 'Gestion des Clients Particuliers')

@section('content')
<div class="space-y-6">
    
    <!-- En-tête avec Navigation par Rôles & Bouton Créer -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <!-- Onglets des 4 Rôles -->
        <div class="flex items-center gap-2 overflow-x-auto">
            <a href="{{ route('admin.users.clients') }}" class="px-4 py-2 rounded-xl text-[13px] font-extrabold bg-primary text-white shadow-sm flex items-center gap-1.5">
                <i data-lucide="users" class="w-4.5 h-4.5"></i>
                <span>Clients ({{ $totalClients }})</span>
            </a>
            <a href="{{ route('admin.users.commerciaux') }}" class="px-4 py-2 rounded-xl text-[13px] font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center gap-1.5 transition-colors">
                <i data-lucide="id-card" class="w-4.5 h-4.5"></i>
                <span>Commerciaux</span>
            </a>
            <a href="{{ route('admin.users.caissiers') }}" class="px-4 py-2 rounded-xl text-[13px] font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center gap-1.5 transition-colors">
                <i data-lucide="banknote" class="w-4.5 h-4.5"></i>
                <span>Caissiers</span>
            </a>
            <a href="{{ route('admin.users.admins') }}" class="px-4 py-2 rounded-xl text-[13px] font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center gap-1.5 transition-colors">
                <i data-lucide="shield-check" class="w-4.5 h-4.5"></i>
                <span>Admins</span>
            </a>
        </div>

        <a href="{{ route('admin.users.create', ['role' => 'client']) }}" class="bg-primary-container hover:bg-brand-red-hover text-white text-[13px] font-bold px-4 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-2 self-start md:self-auto">
            <i data-lucide="user-plus" class="w-4.5 h-4.5"></i>
            <span>Nouveau Client</span>
        </a>
    </div>

    <!-- Barre de Recherche -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3">
        <form action="{{ route('admin.users.clients') }}" method="GET" class="flex-1 flex items-center gap-3">
            <i data-lucide="search" class="w-5 h-5 text-slate-400"></i>
            <input 
                type="text" 
                name="q" 
                value="{{ $search }}"
                placeholder="Rechercher par nom, téléphone (+225), email ou ville..." 
                class="w-full bg-transparent border-none outline-none text-[13px] text-slate-800 placeholder-slate-400"
            />
            @if($search)
                <a href="{{ route('admin.users.clients') }}" class="text-xs text-slate-400 hover:text-slate-600">Effacer</a>
            @endif
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[12px] font-bold hover:bg-slate-800 transition-colors">
                Filtrer
            </button>
        </form>
    </div>

    <!-- Tableau des Clients -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[13px]">
                <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="p-4">Client</th>
                        <th class="p-4">Contact & Ville</th>
                        <th class="p-4">Parrain Commercial</th>
                        <th class="p-4">Dossier KYC</th>
                        <th class="p-4">Commandes Passées</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($clients as $c)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 font-black text-xs flex items-center justify-center shrink-0">
                                        {{ substr($c->prenom ?? 'C', 0, 1) }}{{ substr($c->nom ?? 'L', 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $c->nom_complet }}</span>
                                        <span class="text-[11px] text-slate-400">{{ $c->email }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="p-4">
                                <div class="flex flex-col">
                                    <span class="font-mono font-bold text-slate-800 flex items-center gap-1">
                                        <i data-lucide="phone" class="w-3.5 h-3.5 text-emerald-600"></i>
                                        +225 {{ $c->telephone }}
                                    </span>
                                    <span class="text-[11px] text-slate-500">{{ $c->ville ?? 'Abidjan' }} @if($c->commune) ({{ $c->commune }}) @endif</span>
                                </div>
                            </td>

                            <td class="p-4">
                                @if($c->ref_code)
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 font-mono text-[11px] font-bold border border-amber-200">
                                        {{ $c->ref_code }}
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Vente directe</span>
                                @endif
                            </td>

                            <td class="p-4">
                                @php
                                    $kycStatus = $c->kycProfile?->statut ?? 'non_soumis';
                                    $badge = match($kycStatus) {
                                        'valide' => 'bg-emerald-100 text-emerald-800',
                                        'en_attente' => 'bg-amber-100 text-amber-800 animate-pulse',
                                        'rejete' => 'bg-red-100 text-red-800',
                                        default => 'bg-slate-100 text-slate-500',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold capitalize {{ $badge }}">
                                    {{ str_replace('_', ' ', $kycStatus) }}
                                </span>
                            </td>

                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-slate-900">{{ $c->orders->count() }} cmd</span>
                                    @if($c->orders->count() > 0)
                                        <span class="text-[11px] text-slate-500">({{ number_format($c->orders->sum('montant_total'), 0, ',', ' ') }} F)</span>
                                    @endif
                                </div>
                            </td>

                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.edit', $c) }}" class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors flex items-center justify-center" title="Modifier">
                                        <i data-lucide="pencil" class="w-4.5 h-4.5"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $c) }}" method="POST" onsubmit="return confirm('Confirmez-vous la suppression de ce client ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors flex items-center justify-center" title="Supprimer">
                                            <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-400">
                                Aucun client trouvé correspondant à votre recherche.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $clients->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

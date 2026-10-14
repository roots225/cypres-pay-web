@extends('layouts.admin')

@section('page-title', 'Gestion des Commerciaux & Apporteurs d\'Affaires')

@section('content')
<div class="space-y-6">
    
    <!-- En-tête avec Navigation par Rôles & Bouton Créer -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div class="flex items-center gap-2 overflow-x-auto">
            <a href="{{ route('admin.users.clients') }}" class="px-4 py-2 rounded-xl text-[13px] font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center gap-1.5 transition-colors">
                <i data-lucide="users" class="w-4.5 h-4.5"></i>
                <span>Clients</span>
            </a>
            <a href="{{ route('admin.users.commerciaux') }}" class="px-4 py-2 rounded-xl text-[13px] font-extrabold bg-amber-500 text-slate-900 shadow-sm flex items-center gap-1.5">
                <i data-lucide="id-card" class="w-4.5 h-4.5"></i>
                <span>Commerciaux ({{ $totalCommerciaux }})</span>
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

        <a href="{{ route('admin.users.create', ['role' => 'commercial']) }}" class="bg-amber-500 hover:bg-amber-600 text-slate-900 text-[13px] font-extrabold px-4 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-2 self-start md:self-auto">
            <i data-lucide="user-plus" class="w-4.5 h-4.5"></i>
            <span>Nouveau Commercial</span>
        </a>
    </div>

    <!-- Barre de Recherche -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3">
        <form action="{{ route('admin.users.commerciaux') }}" method="GET" class="flex-1 flex items-center gap-3">
            <i data-lucide="search" class="w-5 h-5 text-slate-400"></i>
            <input 
                type="text" 
                name="q" 
                value="{{ $search }}"
                placeholder="Rechercher par nom, téléphone (+225) ou code parrainage..." 
                class="w-full bg-transparent border-none outline-none text-[13px] text-slate-800 placeholder-slate-400"
            />
            @if($search)
                <a href="{{ route('admin.users.commerciaux') }}" class="text-xs text-slate-400 hover:text-slate-600">Effacer</a>
            @endif
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[12px] font-bold hover:bg-slate-800 transition-colors">
                Filtrer
            </button>
        </form>
    </div>

    <!-- Tableau des Commerciaux -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[13px]">
                <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="p-4">Commercial / Agent</th>
                        <th class="p-4">Code Parrainage</th>
                        <th class="p-4">Zone & Commune</th>
                        <th class="p-4">Clients Rattachés</th>
                        <th class="p-4">Volume d'Affaires</th>
                        <th class="p-4">Commission Due (3%)</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($commerciaux as $com)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-900 font-black text-xs flex items-center justify-center shrink-0">
                                        {{ substr($com->prenom ?? 'C', 0, 1) }}{{ substr($com->nom ?? 'O', 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $com->nom_complet }}</span>
                                        <span class="text-[11px] font-mono text-slate-500">+225 {{ $com->telephone }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-900 font-mono text-[12px] font-extrabold border border-amber-200">
                                    {{ $com->own_referral_code ?? 'NON DÉFINI' }}
                                </span>
                            </td>

                            <td class="p-4">
                                <span class="font-semibold text-slate-700 block">{{ $com->ville ?? 'Abidjan' }}</span>
                                <span class="text-[11px] text-slate-400">{{ $com->commune ?? 'Toutes zones' }}</span>
                            </td>

                            <td class="p-4">
                                <span class="font-black text-slate-900 text-sm">{{ $com->referred_clients_count }}</span>
                                <span class="text-[11px] text-slate-400 block">clients parrainés</span>
                            </td>

                            <td class="p-4 font-black text-slate-900">
                                {{ number_format($com->sales_volume, 0, ',', ' ') }} FCFA
                            </td>

                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-800 font-black text-[12px] border border-emerald-200">
                                    {{ number_format($com->commission_earned, 0, ',', ' ') }} FCFA
                                </span>
                            </td>

                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.edit', $com) }}" class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors flex items-center justify-center" title="Modifier">
                                        <i data-lucide="pencil" class="w-4.5 h-4.5"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $com) }}" method="POST" onsubmit="return confirm('Confirmez-vous la suppression de ce commercial ?');">
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
                            <td colspan="7" class="p-12 text-center text-slate-400">
                                Aucun commercial trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($commerciaux->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $commerciaux->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

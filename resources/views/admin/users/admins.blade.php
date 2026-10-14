@extends('layouts.admin')

@section('page-title', 'Gestion des Administrateurs Système')

@section('content')
<div class="space-y-6">
    
    <!-- En-tête avec Navigation par Rôles & Bouton Créer -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div class="flex items-center gap-2 overflow-x-auto">
            <a href="{{ route('admin.users.clients') }}" class="px-4 py-2 rounded-xl text-[13px] font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center gap-1.5 transition-colors">
                <i data-lucide="users" class="w-4.5 h-4.5"></i>
                <span>Clients</span>
            </a>
            <a href="{{ route('admin.users.commerciaux') }}" class="px-4 py-2 rounded-xl text-[13px] font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center gap-1.5 transition-colors">
                <i data-lucide="id-card" class="w-4.5 h-4.5"></i>
                <span>Commerciaux</span>
            </a>
            <a href="{{ route('admin.users.caissiers') }}" class="px-4 py-2 rounded-xl text-[13px] font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center gap-1.5 transition-colors">
                <i data-lucide="banknote" class="w-4.5 h-4.5"></i>
                <span>Caissiers</span>
            </a>
            <a href="{{ route('admin.users.admins') }}" class="px-4 py-2 rounded-xl text-[13px] font-extrabold bg-purple-700 text-white shadow-sm flex items-center gap-1.5">
                <i data-lucide="shield-check" class="w-4.5 h-4.5"></i>
                <span>Admins ({{ $totalAdmins }})</span>
            </a>
        </div>

        <a href="{{ route('admin.users.create', ['role' => 'admin']) }}" class="bg-purple-700 hover:bg-purple-800 text-white text-[13px] font-bold px-4 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-2 self-start md:self-auto">
            <i data-lucide="user-plus" class="w-4.5 h-4.5"></i>
            <span>Nouvel Administrateur</span>
        </a>
    </div>

    <!-- Barre de Recherche -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3">
        <form action="{{ route('admin.users.admins') }}" method="GET" class="flex-1 flex items-center gap-3">
            <i data-lucide="search" class="w-5 h-5 text-slate-400"></i>
            <input 
                type="text" 
                name="q" 
                value="{{ $search }}"
                placeholder="Rechercher par nom, email ou téléphone (+225)..." 
                class="w-full bg-transparent border-none outline-none text-[13px] text-slate-800 placeholder-slate-400"
            />
            @if($search)
                <a href="{{ route('admin.users.admins') }}" class="text-xs text-slate-400 hover:text-slate-600">Effacer</a>
            @endif
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[12px] font-bold hover:bg-slate-800 transition-colors">
                Filtrer
            </button>
        </form>
    </div>

    <!-- Tableau des Administrateurs -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[13px]">
                <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="p-4">Administrateur</th>
                        <th class="p-4">Email de Connexion</th>
                        <th class="p-4">Téléphone Contact</th>
                        <th class="p-4">Statut de Privilège</th>
                        <th class="p-4">Créé le</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($admins as $adm)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-900 font-black text-xs flex items-center justify-center shrink-0">
                                        {{ substr($adm->prenom ?? 'A', 0, 1) }}{{ substr($adm->nom ?? 'D', 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $adm->nom_complet }}</span>
                                        @if($adm->id === auth()->id())
                                            <span class="text-[10px] font-extrabold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Votre compte</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="p-4 font-mono text-[12px] text-slate-700">
                                {{ $adm->email }}
                            </td>

                            <td class="p-4 font-mono font-bold text-slate-800">
                                +225 {{ $adm->telephone }}
                            </td>

                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-purple-100 text-purple-800 flex items-center gap-1 w-max">
                                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                    Super Admin
                                </span>
                            </td>

                            <td class="p-4 text-slate-500 text-[12px]">
                                {{ $adm->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.edit', $adm) }}" class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors flex items-center justify-center" title="Modifier">
                                        <i data-lucide="pencil" class="w-4.5 h-4.5"></i>
                                    </a>
                                    @if($adm->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $adm) }}" method="POST" onsubmit="return confirm('Confirmez-vous la révocation des accès de cet administrateur ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors flex items-center justify-center" title="Supprimer">
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
                                Aucun administrateur trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($admins->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $admins->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

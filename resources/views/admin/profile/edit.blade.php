@extends('layouts.admin')

@section('title', 'Mon Profil Administrateur - Cypres Pay')
@section('page-title', 'Paramètres de mon Profil')

@section('content')
<div class="max-w-3xl mx-auto py-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-4 pb-6 mb-6 border-b border-slate-100">
            <div class="w-16 h-16 rounded-2xl bg-cobalt-deep text-white flex items-center justify-center font-black text-2xl shadow-md">
                {{ substr($user->prenom ?? 'A', 0, 1) }}{{ substr($user->nom ?? 'D', 0, 1) }}
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">{{ $user->nom_complet }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-100 text-purple-700">
                        Super Administrateur
                    </span>
                    <span class="text-[12px] text-slate-500">{{ $user->email }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Prénom *</label>
                    <input 
                        type="text" 
                        name="prenom" 
                        value="{{ old('prenom', $user->prenom) }}" 
                        required 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px] font-medium"
                    />
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Nom de famille *</label>
                    <input 
                        type="text" 
                        name="nom" 
                        value="{{ old('nom', $user->nom) }}" 
                        required 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px] font-medium"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Adresse Email *</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email', $user->email) }}" 
                        required 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px] font-medium"
                    />
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Numéro de Téléphone *</label>
                    <input 
                        type="tel" 
                        name="telephone" 
                        value="{{ old('telephone', $user->telephone) }}" 
                        required 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px] font-medium"
                    />
                </div>
            </div>

            <!-- Changement de mot de passe -->
            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-sm font-extrabold text-slate-900 mb-3 flex items-center gap-2">
                    <i data-lucide="key-round" class="w-4 h-4 text-primary"></i>
                    <span>Changer de mot de passe</span>
                </h3>
                <p class="text-[12px] text-slate-500 mb-4">Laissez ces champs vides si vous ne souhaitez pas modifier votre mot de passe.</p>

                <div class="space-y-4 max-w-xl">
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Mot de passe actuel</label>
                        <input 
                            type="password" 
                            name="current_password" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]"
                            placeholder="Requis uniquement pour changer de mot de passe"
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[12px] font-bold text-slate-700 mb-1">Nouveau mot de passe</label>
                            <input 
                                type="password" 
                                name="new_password" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]"
                                placeholder="Min. 6 caractères"
                            />
                        </div>

                        <div>
                            <label class="block text-[12px] font-bold text-slate-700 mb-1">Confirmation</label>
                            <input 
                                type="password" 
                                name="new_password_confirmation" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]"
                                placeholder="Confirmer mot de passe"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-xl text-[13px] font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-[13px] font-extrabold text-white bg-primary hover:bg-brand-red-hover shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Enregistrer</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

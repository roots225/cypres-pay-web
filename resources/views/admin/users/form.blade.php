@extends('layouts.admin')

@section('page-title', $isEdit ? "Modifier l'utilisateur : {$user->nom_complet}" : "Créer un nouvel utilisateur")

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <a href="javascript:history.back()" class="flex items-center gap-1.5 text-[13px] font-bold text-slate-500 hover:text-slate-900 transition-colors">
            <i data-lucide="arrow-left" class="w-4.5 h-4.5"></i>
            <span>Retour</span>
        </a>
        <span class="text-[12px] font-bold uppercase tracking-wider text-slate-400">
            Rôle ciblé : <strong class="text-slate-800">{{ ucfirst($role) }}</strong>
        </span>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-[13px] font-medium">
            <span class="font-bold block mb-1">Veuillez corriger les erreurs suivantes :</span>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm" x-data="{ currentRole: '{{ old('role', $user->role ?? $role) }}' }">
        <form action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <!-- 1. Sélection du Rôle -->
            <div>
                <label class="block text-[12px] font-extrabold uppercase tracking-wide text-slate-700 mb-2">
                    Type de Compte / Rôle *
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <label class="p-3 rounded-2xl border cursor-pointer text-center transition-all flex flex-col items-center gap-1.5"
                           :class="currentRole === 'client' ? 'border-primary bg-red-50/50 ring-1 ring-primary font-bold text-primary' : 'border-slate-200 hover:bg-slate-50 text-slate-600'">
                        <input type="radio" name="role" value="client" x-model="currentRole" class="sr-only"/>
                        <i data-lucide="users" class="w-6 h-6"></i>
                        <span class="text-[12px]">Client</span>
                    </label>

                    <label class="p-3 rounded-2xl border cursor-pointer text-center transition-all flex flex-col items-center gap-1.5"
                           :class="currentRole === 'commercial' ? 'border-amber-500 bg-amber-50/50 ring-1 ring-amber-500 font-bold text-amber-900' : 'border-slate-200 hover:bg-slate-50 text-slate-600'">
                        <input type="radio" name="role" value="commercial" x-model="currentRole" class="sr-only"/>
                        <i data-lucide="id-card" class="w-6 h-6"></i>
                        <span class="text-[12px]">Commercial</span>
                    </label>

                    <label class="p-3 rounded-2xl border cursor-pointer text-center transition-all flex flex-col items-center gap-1.5"
                           :class="currentRole === 'caissier' ? 'border-sky-600 bg-sky-50/50 ring-1 ring-sky-600 font-bold text-sky-900' : 'border-slate-200 hover:bg-slate-50 text-slate-600'">
                        <input type="radio" name="role" value="caissier" x-model="currentRole" class="sr-only"/>
                        <i data-lucide="banknote" class="w-6 h-6"></i>
                        <span class="text-[12px]">Caissier</span>
                    </label>

                    <label class="p-3 rounded-2xl border cursor-pointer text-center transition-all flex flex-col items-center gap-1.5"
                           :class="currentRole === 'admin' ? 'border-purple-600 bg-purple-50/50 ring-1 ring-purple-600 font-bold text-purple-900' : 'border-slate-200 hover:bg-slate-50 text-slate-600'">
                        <input type="radio" name="role" value="admin" x-model="currentRole" class="sr-only"/>
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                        <span class="text-[12px]">Admin</span>
                    </label>
                </div>
            </div>

            <!-- 2. Informations Personnelles -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Prénom *</label>
                    <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary focus:ring-1 focus:ring-primary outline-none text-[13px]" placeholder="Ex: Jean"/>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Nom de famille *</label>
                    <input type="text" name="nom" value="{{ old('nom', $user->nom) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary focus:ring-1 focus:ring-primary outline-none text-[13px]" placeholder="Ex: Kouamé"/>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Numéro Téléphone Mobile Money *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-[12px] font-bold text-slate-400">+225</span>
                        <input type="tel" name="telephone" value="{{ old('telephone', $user->telephone) }}" required maxlength="15" class="w-full pl-14 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary focus:ring-1 focus:ring-primary outline-none text-[13px] font-mono font-bold" placeholder="0700000000"/>
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">Format 10 chiffres (Orange, MTN, Moov, Wave)</span>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Adresse Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary focus:ring-1 focus:ring-primary outline-none text-[13px]" placeholder="utilisateur@domaine.ci"/>
                </div>
            </div>

            <!-- 3. Mot de Passe -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">
                        {{ $isEdit ? 'Nouveau mot de passe (laisser vide pour conserver)' : 'Mot de passe initial *' }}
                    </label>
                    <input type="password" name="password" {{ $isEdit ? '' : 'required' }} minlength="6" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]" placeholder="Minimum 6 caractères"/>
                </div>
            </div>

            <!-- 4. Champs Spécifiques selon le Rôle -->
            <div class="pt-4 border-t border-slate-100 space-y-4">
                
                <!-- Spécifique Commercial : Code Parrainage Personnel -->
                <div x-show="currentRole === 'commercial'" class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200/80">
                    <label class="block text-[12px] font-extrabold text-amber-900 mb-1">
                        Code Parrainage Commercial Unique
                    </label>
                    <input type="text" name="own_referral_code" value="{{ old('own_referral_code', $user->own_referral_code) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-amber-300 focus:border-amber-500 outline-none text-[13px] font-mono uppercase font-bold" placeholder="Ex: COM-YOP-01 (Laisser vide pour auto-générer)"/>
                    <span class="text-[11px] text-amber-800 mt-1 block">Ce code permet de tracker automatiquement les 3% de commissions apporteur d'affaires.</span>
                </div>

                <!-- Spécifique Client : Rattaché à un Commercial & Adresse de livraison -->
                <div x-show="currentRole === 'client'" class="space-y-4">
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Commercial Parrain Rattaché (facultatif)</label>
                        <select name="ref_code" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px] bg-white">
                            <option value="">-- Aucun commercial (Vente en direct) --</option>
                            @foreach($commerciaux as $com)
                                <option value="{{ $com->own_referral_code }}" {{ old('ref_code', $user->ref_code) === $com->own_referral_code ? 'selected' : '' }}>
                                    {{ $com->nom_complet }} ({{ $com->own_referral_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Ville & Commune & Adresse physique -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Ville</label>
                        <input type="text" name="ville" value="{{ old('ville', $user->ville ?? 'Abidjan') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]" placeholder="Abidjan, Bouaké, Yamoussoukro..."/>
                    </div>

                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Commune / Agence</label>
                        <input type="text" name="commune" value="{{ old('commune', $user->commune) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]" placeholder="Ex: Yopougon, Cocody, Adjamé..."/>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Adresse Précise</label>
                        <input type="text" name="adresse" value="{{ old('adresse', $user->adresse) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]" placeholder="Quartier, carrefour, repère visuel..."/>
                    </div>
                </div>
            </div>

            <!-- Boutons de soumission -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="javascript:history.back()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-[13px] font-bold transition-colors">
                    Annuler
                </a>
                <button type="submit" class="bg-primary hover:bg-brand-red-hover text-white text-[13px] font-extrabold px-6 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer">
                    <i data-lucide="save" class="w-4.5 h-4.5"></i>
                    <span>{{ $isEdit ? 'Mettre à jour' : 'Enregistrer le compte' }}</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection

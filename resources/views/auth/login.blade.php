@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 py-12">
    <div class="bg-surface-container-lowest rounded-3xl p-8 border border-border-subtle shadow-xl">
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-brand-red-subtle text-primary-container flex items-center justify-center mx-auto mb-3">
                <i data-lucide="lock" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-on-surface">Espace Membre</h1>
            <p class="text-[13px] text-text-muted mt-1">Connectez-vous à votre compte Cypress Pay CI</p>
        </div>

        @if(session('error'))
            <div class="mb-4 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-[12px] font-bold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4.5 h-4.5"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-[12px] font-bold flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-4.5 h-4.5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1">Numéro Téléphone ou Email *</label>
                <input 
                    type="text" 
                    name="identifiant" 
                    value="{{ old('identifiant', 'admin@cypresspay.ci') }}" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-[13px]" 
                    placeholder="Ex: 0700000001 ou admin@cypresspay.ci"
                />
            </div>

            <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1">Mot de passe *</label>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-[13px]" 
                    value="CypressAdmin2026!"
                />
            </div>

            <div class="flex items-center justify-between text-[12px]">
                <label class="flex items-center gap-2 cursor-pointer text-text-muted">
                    <input type="checkbox" name="remember" value="1" checked class="text-primary rounded"/>
                    <span>Se souvenir de moi</span>
                </label>
                <a href="#" class="text-primary font-bold hover:underline">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="w-full bg-primary-container hover:bg-brand-red-hover text-white font-extrabold text-[13px] py-3.5 rounded-xl shadow-lg shadow-primary-container/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span>Se Connecter</span>
                <i data-lucide="arrow-right" class="w-4.5 h-4.5"></i>
            </button>
        </form>

        <!-- Comptes de démonstration rapides en 1-clic -->
        <div class="mt-8 pt-6 border-t border-border-subtle">
            <span class="text-[11px] font-bold text-text-muted uppercase tracking-wider block mb-3 text-center">
                Connexion Démo Rapide (1-Clic)
            </span>
            <div class="grid grid-cols-2 gap-2 text-center text-[11px] font-extrabold">
                <a href="{{ route('login.demo', 'admin') }}" class="p-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-900 border border-purple-200 transition-colors flex items-center justify-center gap-1.5">
                    <i data-lucide="shield" class="w-4 h-4"></i>
                    <span>Admin Central</span>
                </a>
                <a href="{{ route('login.demo', 'commercial') }}" class="p-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 transition-colors flex items-center justify-center gap-1.5">
                    <i data-lucide="id-card" class="w-4 h-4"></i>
                    <span>Commercial</span>
                </a>
                <a href="{{ route('login.demo', 'caissier') }}" class="p-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200 transition-colors flex items-center justify-center gap-1.5">
                    <i data-lucide="circle-dollar-sign" class="w-4 h-4"></i>
                    <span>Caisse Agence</span>
                </a>
                <a href="{{ route('login.demo', 'client') }}" class="p-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-900 border border-blue-200 transition-colors flex items-center justify-center gap-1.5">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    <span>Client Acheteur</span>
                </a>
            </div>
        </div>

        <div class="mt-6 text-center text-[12px] text-text-muted">
            Pas encore de compte ?
            <a href="{{ route('register') }}" class="text-primary font-bold hover:underline">Inscrivez-vous ici</a>
        </div>
    </div>
</div>
@endsection

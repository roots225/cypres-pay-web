@extends('layouts.app')

@section('content')
    <div class="max-w-lg mx-auto px-4 sm:px-6 py-12">
        <div class="bg-surface-container-lowest rounded-3xl p-8 border border-border-subtle shadow-xl">
            <div class="text-center mb-6">
                <div
                    class="w-12 h-12 rounded-2xl bg-brand-red-subtle text-primary-container flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="user-plus" class="w-7 h-7"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-on-surface">Créer un Compte</h1>
                <p class="text-[13px] text-text-muted mt-1">Rejoignez Cypres Pay et financez vos équipements</p>
            </div>

            <form action="#" method="GET" class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Prénom *</label>
                        <input type="text" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-[13px]"
                            placeholder="Kouamé" />
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Nom *</label>
                        <input type="text" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-[13px]"
                            placeholder="Yao" />
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Téléphone Mobile Money (10 chiffres)
                        *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-[12px] font-bold text-text-muted">+225</span>
                        <input type="tel" required maxlength="10"
                            class="w-full pl-14 pr-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-[13px] font-mono font-bold"
                            placeholder="07 00 00 00 00" />
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Ville & Quartier *</label>
                    <input type="text" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-[13px]"
                        placeholder="Abidjan, Cocody Angré" />
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Code Parrainage Commercial
                        (facultatif)</label>
                    <input type="text"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-[13px] uppercase font-mono"
                        placeholder="CP-COM-01" />
                </div>

                <div class="text-[11px] text-text-muted leading-relaxed">
                    En créant un compte, vous acceptez nos conditions générales de vente et notre politique de paiement
                    échelonné en Côte d'Ivoire.
                </div>

                <a href="{{ route('client.dashboard') }}"
                    class="w-full bg-primary-container hover:bg-brand-red-hover text-white font-extrabold text-[13px] py-3.5 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                    <span>Créer mon Compte</span>
                    <i data-lucide="check" class="w-4.5 h-4.5"></i>
                </a>
            </form>

            <div class="mt-6 text-center text-[12px] text-text-muted pt-4 border-t border-border-subtle">
                Vous avez déjà un compte ?
                <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Connectez-vous</a>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête de la page Simulateur -->
    <div class="text-center max-w-3xl mx-auto mb-10">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 text-secondary text-[12px] font-extrabold uppercase tracking-wider mb-3 border border-blue-100">
            <i data-lucide="calculator" class="w-4 h-4"></i>
            Moteur Financier Transparent CI
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-on-surface tracking-tight">
            Simulateur d'Échéances BNPL
        </h1>
        <p class="text-[14px] sm:text-[15px] text-text-muted mt-2 leading-relaxed">
            Calculez votre apport initial et vos mensualités exactes en fonction du montant de votre panier ou de votre budget mensuel. De 30 jours à 24 mois sans mauvaise surprise.
        </p>
    </div>

    <!-- Composant React Simulateur BNPL -->
    <div class="max-w-4xl mx-auto mb-16">
        <div id="react-bnpl-simulator" data-initial-amount="{{ $amount ?? 150000 }}">
            <!-- Fallback Blade statique au cas où le JavaScript n'a pas fini de charger -->
            <div class="bg-surface-container-lowest p-8 rounded-3xl border border-border-subtle shadow-lg text-center">
                <i data-lucide="rotate-cw" class="w-10 h-10 text-secondary animate-spin mx-auto"></i>
                <p class="text-[14px] text-text-muted mt-2 font-semibold">Chargement du simulateur interactif Cypress Pay...</p>
            </div>
        </div>
    </div>

    <!-- Grille Explicative : Comment fonctionne le paiement échelonné ? -->
    <div class="max-w-5xl mx-auto">
        <h2 class="text-xl font-extrabold text-on-surface text-center mb-8">
            Pourquoi choisir le crédit responsable Cypress Pay ?
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-border-subtle shadow-sm flex flex-col gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-100 text-primary-container flex items-center justify-center">
                    <i data-lucide="percent" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-[16px] text-on-surface">Option Cash à -7% de remise</h3>
                <p class="text-[13px] text-text-muted leading-relaxed">
                    Vous avez la totalité disponible ? Bénéficiez automatiquement d'une remise immédiate de 7% sur le prix normal en réglant comptant par Wave ou Orange Money.
                </p>
            </div>

            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-border-subtle shadow-sm flex flex-col gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-secondary flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-[16px] text-on-surface">Durée sur-mesure (30j à 24 mois)</h3>
                <p class="text-[13px] text-text-muted leading-relaxed">
                    Adaptez vos prélèvements à votre rythme de rémunération : journalier, hebdomadaire ou mensuel. Aucun compte bancaire ni fiche de paie exigée.
                </p>
            </div>

            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-border-subtle shadow-sm flex flex-col gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-tertiary flex items-center justify-center">
                    <i data-lucide="receipt" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-[16px] text-on-surface">Reçu officiel avec code à 6 caractères</h3>
                <p class="text-[13px] text-text-muted leading-relaxed">
                    Chaque versement déclenche un SMS instantané et un reçu officiel infalsifiable comportant un code unique (ex: 28B7S0) vérifiable publiquement en temps réel.
                </p>
            </div>
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 bg-primary-container hover:bg-brand-red-hover text-white font-extrabold text-[14px] px-8 py-3.5 rounded-xl shadow-lg shadow-primary-container/20 transition-all">
                <span>Explorer le catalogue et commander</span>
                <i data-lucide="arrow-right" class="w-4.5 h-4.5"></i>
            </a>
        </div>
    </div>
</div>
@endsection

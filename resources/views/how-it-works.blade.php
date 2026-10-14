@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Hero Guide -->
    <div class="text-center max-w-3xl mx-auto mb-16">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-red-50 text-primary text-[12px] font-extrabold uppercase tracking-wider mb-3 border border-red-100">
            <i data-lucide="shield-check" class="w-4 h-4"></i>
            Le Paiement Échelonné Réinventé
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-on-surface tracking-tight">
            Comment fonctionne Cypress Pay en Côte d'Ivoire ?
        </h1>
        <p class="text-[15px] text-text-muted mt-3 leading-relaxed">
            Équipez votre maison ou boostez votre activité sans vous délester de toute votre épargne. Découvrez notre processus en 4 étapes simples et transparentes.
        </p>
    </div>

    <!-- 4 Étapes Clés -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-20">
        <!-- Étape 1 -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-border-subtle shadow-sm flex flex-col justify-between relative group hover:border-primary transition-all">
            <span class="absolute top-5 right-5 text-4xl font-black text-slate-100 group-hover:text-red-50 transition-colors">01</span>
            <div>
                <div class="w-12 h-12 rounded-2xl bg-brand-red-subtle text-primary-container flex items-center justify-center mb-5">
                    <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                </div>
                <h3 class="text-lg font-bold text-on-surface mb-2">1. Choisissez vos articles</h3>
                <p class="text-[13px] text-text-muted leading-relaxed">
                    Parcourez notre catalogue d'électroménager, kits solaires, matériaux ou smartphones certifiés neufs avec garantie constructeur.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-border-subtle text-[12px] font-semibold text-secondary">
                Plus de 1 000 références en stock
            </div>
        </div>

        <!-- Étape 2 -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-border-subtle shadow-sm flex flex-col justify-between relative group hover:border-secondary transition-all">
            <span class="absolute top-5 right-5 text-4xl font-black text-slate-100 group-hover:text-blue-50 transition-colors">02</span>
            <div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-secondary flex items-center justify-center mb-5">
                    <i data-lucide="calculator" class="w-6 h-6"></i>
                </div>
                <h3 class="text-lg font-bold text-on-surface mb-2">2. Simulez votre formule</h3>
                <p class="text-[13px] text-text-muted leading-relaxed">
                    Optez pour le Cash à -7% de remise immédiate, ou étalez vos paiements de 30 jours à 24 mois avec l'apport initial qui vous convient.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-border-subtle text-[12px] font-semibold text-emerald-600">
                Amortissement clair sans frais cachés
            </div>
        </div>

        <!-- Étape 3 -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-border-subtle shadow-sm flex flex-col justify-between relative group hover:border-emerald-500 transition-all">
            <span class="absolute top-5 right-5 text-4xl font-black text-slate-100 group-hover:text-emerald-50 transition-colors">03</span>
            <div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-tertiary flex items-center justify-center mb-5">
                    <i data-lucide="id-card" class="w-6 h-6"></i>
                </div>
                <h3 class="text-lg font-bold text-on-surface mb-2">3. KYC en 5 minutes</h3>
                <p class="text-[13px] text-text-muted leading-relaxed">
                    Téléversez votre pièce d'identité (CNI, Passeport, Attestation) et indiquez votre numéro Mobile Money. Aucune caution bancaire requise.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-border-subtle text-[12px] font-semibold text-slate-700">
                Validation sous 24 heures ouvrées
            </div>
        </div>

        <!-- Étape 4 -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-border-subtle shadow-sm flex flex-col justify-between relative group hover:border-amber-500 transition-all">
            <span class="absolute top-5 right-5 text-4xl font-black text-slate-100 group-hover:text-amber-50 transition-colors">04</span>
            <div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-5">
                    <i data-lucide="truck" class="w-6 h-6"></i>
                </div>
                <h3 class="text-lg font-bold text-on-surface mb-2">4. Livraison & Échéancier</h3>
                <p class="text-[13px] text-text-muted leading-relaxed">
                    Recevez vos articles chez vous en 72h. Réglez vos mensualités par Wave ou Orange Money et suivez vos quittances en direct.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-border-subtle text-[12px] font-semibold text-tertiary">
                Reçu officiel 6 caractères par SMS
            </div>
        </div>
    </div>

    <!-- Comparatif BNPL vs Banques Classiques -->
    <div class="bg-surface-container-lowest rounded-3xl p-8 sm:p-10 border border-border-subtle shadow-sm mb-20">
        <h2 class="text-2xl font-extrabold text-on-surface mb-6 text-center">
            Pourquoi Cypress Pay vs Crédit Bancaire Classique ?
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[14px]">
                <thead>
                    <tr class="border-b border-border-subtle text-text-muted text-[12px] uppercase">
                        <th class="pb-3 font-bold">Critères</th>
                        <th class="pb-3 font-bold text-primary">Cypress Pay CI</th>
                        <th class="pb-3 font-bold">Crédit Bancaire Classique</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    <tr>
                        <td class="py-3.5 font-bold text-on-surface">Compte bancaire obligatoire ?</td>
                        <td class="py-3.5 font-bold text-emerald-600 flex items-center gap-1.5">
                            <i data-lucide="check-circle-2" class="w-4.5 h-4.5 text-emerald-600"></i> NON (Wave, Orange, MTN acceptés)
                        </td>
                        <td class="py-3.5 text-text-muted">OUI (Obligation de domicilier le salaire)</td>
                    </tr>
                    <tr>
                        <td class="py-3.5 font-bold text-on-surface">Délai d'approbation</td>
                        <td class="py-3.5 font-bold text-emerald-600">Moins de 24 heures</td>
                        <td class="py-3.5 text-text-muted">2 à 4 semaines</td>
                    </tr>
                    <tr>
                        <td class="py-3.5 font-bold text-on-surface">Frais de dossier cachés</td>
                        <td class="py-3.5 font-bold text-emerald-600">0 FCFA</td>
                        <td class="py-3.5 text-text-muted">Entre 20 000 et 50 000 FCFA</td>
                    </tr>
                    <tr>
                        <td class="py-3.5 font-bold text-on-surface">Échéances flexibles</td>
                        <td class="py-3.5 font-bold text-emerald-600">30j, 60j, 3m, 6m, 12m, 24m</td>
                        <td class="py-3.5 text-text-muted">Rigide et avec pénalités de remboursement anticipé</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Foire Aux Questions (FAQ) -->
    <div class="max-w-3xl mx-auto">
        <h2 class="text-2xl font-extrabold text-on-surface text-center mb-8">
            Foire Aux Questions (FAQ)
        </h2>

        <div class="space-y-4">
            <details class="bg-surface-container-lowest p-5 rounded-2xl border border-border-subtle group" open>
                <summary class="font-bold text-[15px] text-on-surface cursor-pointer flex items-center justify-between">
                    <span>Quels sont les documents requis pour souscrire ?</span>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-text-muted group-open:rotate-180 transition-transform"></i>
                </summary>
                <p class="text-[13px] text-text-muted mt-3 leading-relaxed">
                    Il vous suffit d'une pièce d'identité valide (CNI, Passeport ou Attestation d'identité ivoirienne), d'un numéro de téléphone actif avec compte Mobile Money, et d'un justificatif de domicile ou contact d'une personne de référence.
                </p>
            </details>

            <details class="bg-surface-container-lowest p-5 rounded-2xl border border-border-subtle group">
                <summary class="font-bold text-[15px] text-on-surface cursor-pointer flex items-center justify-between">
                    <span>Comment payer mes mensualités ?</span>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-text-muted group-open:rotate-180 transition-transform"></i>
                </summary>
                <p class="text-[13px] text-text-muted mt-3 leading-relaxed">
                    Vous recevrez un rappel par SMS 3 jours avant chaque échéance. Vous pouvez régler en 1 clic via Wave ou Orange Money depuis votre espace client, ou bien directement en espèces dans notre agence principale à Abidjan.
                </p>
            </details>

            <details class="bg-surface-container-lowest p-5 rounded-2xl border border-border-subtle group">
                <summary class="font-bold text-[15px] text-on-surface cursor-pointer flex items-center justify-between">
                    <span>La livraison est-elle assurée hors d'Abidjan ?</span>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-text-muted group-open:rotate-180 transition-transform"></i>
                </summary>
                <p class="text-[13px] text-text-muted mt-3 leading-relaxed">
                    Oui ! Nous livrons dans tout le district d'Abidjan sous 48h à 72h, ainsi que dans les grandes villes de l'intérieur (Bouaké, San Pedro, Yamoussoukro, Korhogo, Daloa) via nos partenaires logistiques certifiés.
                </p>
            </details>
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 bg-primary-container hover:bg-brand-red-hover text-white font-extrabold text-[14px] px-8 py-3.5 rounded-xl shadow-lg transition-all">
                <span>Commencer mes achats</span>
                <i data-lucide="arrow-right" class="w-4.5 h-4.5"></i>
            </a>
        </div>
    </div>
</div>
@endsection

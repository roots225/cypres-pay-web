@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête Client -->
    <div class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 border border-border-subtle shadow-sm mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-brand-red-subtle text-primary-container font-black text-2xl flex items-center justify-center shrink-0">
                {{ substr($user->prenom ?? 'C', 0, 1) }}{{ substr($user->nom ?? 'P', 0, 1) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-on-surface">
                        {{ $user->prenom ?? 'Client' }} {{ $user->nom ?? 'Cypres Pay' }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-tertiary">
                        Compte Vérifié
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-[12px] text-text-muted mt-1">
                    <span class="flex items-center gap-1"><i data-lucide="phone" class="w-3.5 h-3.5"></i> +225 {{ $user->telephone }}</span>
                    <span>·</span>
                    <span class="flex items-center gap-1"><i data-lucide="map-pin" class="w-3.5 h-3.5"></i> {{ $user->ville ?? 'Abidjan' }} ({{ $user->adresse ?? 'Côte d\'Ivoire' }})</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('catalog') }}" class="bg-primary-container hover:bg-brand-red-hover text-white text-[13px] font-bold px-4 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-1.5">
                <i data-lucide="shopping-cart" class="w-4.5 h-4.5"></i>
                <span>Nouvel Achat BNPL</span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-surface hover:bg-red-50 text-red-600 hover:text-red-700 border border-red-200 hover:border-red-300 text-[13px] font-bold px-3.5 py-2.5 rounded-xl transition-all flex items-center gap-1.5 cursor-pointer shadow-xs" title="Se déconnecter de votre espace client">
                    <i data-lucide="log-out" class="w-4.5 h-4.5"></i>
                    <span class="hidden sm:inline">Déconnexion</span>
                </button>
            </form>
        </div>
    </div>

    <!-- 3 Cartes de Métriques Financières -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <div class="bg-surface-container-lowest p-5 rounded-2xl border border-border-subtle shadow-sm">
            <span class="text-[12px] text-text-muted font-bold block mb-1">Total Crédit Accordé</span>
            <span class="text-2xl font-black text-on-surface">
                {{ number_format($activeOrder?->montant_finance ?? 0, 0, ',', ' ') }} FCFA
            </span>
            <span class="text-[11px] text-secondary font-semibold block mt-1">Sur {{ $activeOrder?->nombre_echeances ?? 0 }} échéances</span>
        </div>

        <div class="bg-surface-container-lowest p-5 rounded-2xl border border-border-subtle shadow-sm">
            <span class="text-[12px] text-text-muted font-bold block mb-1">Montant Déjà Réglé</span>
            <span class="text-2xl font-black text-emerald-600">
                {{ number_format($totalPaid ?? 0, 0, ',', ' ') }} FCFA
            </span>
            <span class="text-[11px] text-emerald-700 font-semibold block mt-1">Quittances enregistrées</span>
        </div>

        <div class="bg-surface-container-lowest p-5 rounded-2xl border border-border-subtle shadow-sm">
            <span class="text-[12px] text-text-muted font-bold block mb-1">Solde Restant à Payer</span>
            <span class="text-2xl font-black text-primary-container">
                {{ number_format(max(0, $totalRemaining ?? 0), 0, ',', ' ') }} FCFA
            </span>
            <span class="text-[11px] text-primary-container font-semibold block mt-1">À solder selon l'échéancier</span>
        </div>
    </div>

    <!-- Grille Principale : Échéancier Actif & Historique Commandes -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Échéancier en cours (8 cols) -->
        <div class="lg:col-span-8 bg-surface-container-lowest p-6 rounded-3xl border border-border-subtle shadow-sm">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-border-subtle">
                <div>
                    <h2 class="text-lg font-bold text-on-surface">Échéancier Actif & Règlements</h2>
                    <p class="text-[12px] text-text-muted">Commande ref : <strong class="text-on-surface">{{ $activeOrder?->numero_commande ?? 'Aucune commande active' }}</strong></p>
                </div>
                @if($activeOrder)
                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-100 text-secondary">
                        {{ $activeOrder->formula?->nom ?? 'Formule standard' }}
                    </span>
                @endif
            </div>

            @if($installments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[13px]">
                        <thead class="text-[11px] uppercase text-text-muted border-b border-border-subtle">
                            <tr>
                                <th class="pb-2.5">N° Échéance</th>
                                <th class="pb-2.5">Date limite</th>
                                <th class="pb-2.5">Montant dû</th>
                                <th class="pb-2.5">Statut</th>
                                <th class="pb-2.5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-subtle">
                            @foreach($installments as $inst)
                                <tr>
                                    <td class="py-3 font-bold text-on-surface">Échéance #{{ $inst->numero_echeance }}</td>
                                    <td class="py-3 text-text-muted">{{ $inst->date_echeance->format('d/m/Y') }}</td>
                                    <td class="py-3 font-extrabold text-on-surface">{{ number_format($inst->montant_du, 0, ',', ' ') }} FCFA</td>
                                    <td class="py-3">
                                        @if($inst->statut === 'paye')
                                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1 w-max">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i> Réglé
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800 w-max block">
                                                En attente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right">
                                        @if($inst->statut !== 'paye')
                                            <a 
                                                href="https://wa.me/2250586708161?text={{ urlencode('Bonjour Cypres Pay, je souhaite régler mon échéance #' . $inst->numero_echeance . ' de ' . number_format($inst->montant_du, 0, ',', ' ') . ' FCFA pour la commande ' . $activeOrder?->numero_commande) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-provider-wave hover:bg-blue-500 text-white text-[11px] font-bold transition-colors"
                                            >
                                                <span>Payer par Wave</span>
                                            </a>
                                        @else
                                            <span class="text-[11px] text-text-muted font-mono">Reçu certifié</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-text-muted">
                    <i data-lucide="shopping-bag" class="w-10 h-10 mb-2 inline-block"></i>
                    <p class="text-[13px]">Vous n'avez pas d'échéances actives en cours.</p>
                    <a href="{{ route('catalog') }}" class="text-primary font-bold text-[13px] inline-block mt-2">Découvrir les offres</a>
                </div>
            @endif
        </div>

        <!-- Colonne Droite : Commandes & Quittances (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Historique des Commandes -->
            <div class="bg-surface-container-lowest p-6 rounded-3xl border border-border-subtle shadow-sm">
                <h3 class="text-base font-bold text-on-surface mb-4">Mes Commandes</h3>
                <div class="space-y-3">
                    @forelse($orders as $o)
                        <div class="p-3.5 rounded-2xl bg-canvas-background border border-border-subtle text-[12px]">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-mono font-bold text-on-surface">{{ $o->numero_commande }}</span>
                                <span class="text-primary font-extrabold">{{ number_format($o->montant_total, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex justify-between items-center text-text-muted text-[11px]">
                                <span>{{ $o->created_at->format('d/m/Y') }}</span>
                                <span class="capitalize font-semibold">{{ str_replace('_', ' ', $o->statut) }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-text-muted text-[12px]">Aucune commande passée.</p>
                    @endforelse
                </div>
            </div>

            <!-- Outil Rapide : Vérifier une Quittance -->
            <div class="bg-cobalt-subtle p-6 rounded-3xl border border-blue-100 text-cobalt-deep">
                <h4 class="font-extrabold text-[14px] mb-2 flex items-center gap-1.5">
                    <i data-lucide="shield-check" class="w-4.5 h-4.5"></i>
                    Vérifier une quittance
                </h4>
                <p class="text-[12px] leading-relaxed mb-4 opacity-90">
                    Chaque versement produit un code unique de 6 caractères. Vérifiez immédiatement la conformité de vos reçus.
                </p>
                <a href="{{ route('verifier') }}" class="block text-center py-2.5 px-4 rounded-xl bg-cobalt-deep text-white text-[12px] font-bold hover:bg-slate-800 transition-colors">
                    Accéder au vérificateur public
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

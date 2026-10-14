@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- En-tête Commercial -->
        <div
            class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 border border-border-subtle shadow-sm mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 rounded-2xl bg-secondary/10 text-secondary font-black text-2xl flex items-center justify-center shrink-0">
                    <i data-lucide="circle-dollar-sign" class="w-8 h-8"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-extrabold text-on-surface">
                            Portail Commercial & Terrain
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-secondary">
                            Agent Agréé CI
                        </span>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-[12px] text-text-muted mt-1">
                        <span>Agent : <strong
                                class="text-on-surface">{{ $commercial?->nom_complet ?? 'Agent Cypres Pay' }}</strong></span>
                        <span>·</span>
                        <span>Code Parrainage : <strong
                                class="font-mono text-primary font-bold">{{ $commercial?->own_referral_code ?? 'CP-COM-01' }}</strong></span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('checkout.show') }}"
                    class="bg-primary-container hover:bg-brand-red-hover text-white text-[13px] font-bold px-4 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-1.5">
                    <i data-lucide="plus-circle" class="w-4.5 h-4.5"></i>
                    <span>Nouvelle Vente Terrain</span>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-surface hover:bg-red-50 text-red-600 hover:text-red-700 border border-red-200 hover:border-red-300 text-[13px] font-bold px-3.5 py-2.5 rounded-xl transition-all flex items-center gap-1.5 cursor-pointer shadow-xs"
                        title="Se déconnecter de votre session commerciale">
                        <i data-lucide="log-out" class="w-4.5 h-4.5"></i>
                        <span class="hidden sm:inline">Déconnexion</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Métriques Commerciales & Commissions -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-border-subtle shadow-sm">
                <span class="text-[12px] text-text-muted font-bold block mb-1">Volume de Ventes Généré</span>
                <span class="text-2xl font-black text-on-surface">
                    {{ number_format($totalSales ?? 0, 0, ',', ' ') }} FCFA
                </span>
                <span class="text-[11px] text-secondary font-semibold block mt-1">Sur l'ensemble de votre
                    portefeuille</span>
            </div>

            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-border-subtle shadow-sm">
                <span class="text-[12px] text-text-muted font-bold block mb-1">Commissions Acquises (3%)</span>
                <span class="text-2xl font-black text-emerald-600">
                    {{ number_format($commissionEarned ?? 0, 0, ',', ' ') }} FCFA
                </span>
                <span class="text-[11px] text-emerald-700 font-semibold block mt-1">Payable par Wave chaque vendredi</span>
            </div>

            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-border-subtle shadow-sm">
                <span class="text-[12px] text-text-muted font-bold block mb-1">Clients Parrainés Actifs</span>
                <span class="text-2xl font-black text-secondary">
                    {{ $clients->count() }}
                </span>
                <span class="text-[11px] text-text-muted font-semibold block mt-1">Inscrits avec votre code agent</span>
            </div>
        </div>

        <!-- Tableaux Portefeuille Clients & Commandes Récentes -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Commandes Récentes Enregistrées (7 cols) -->
            <div class="lg:col-span-7 bg-surface-container-lowest p-6 rounded-3xl border border-border-subtle shadow-sm">
                <h2 class="text-base font-bold text-on-surface mb-4 flex items-center justify-between">
                    <span>Dernières commandes</span>
                    <span class="text-[12px] text-text-muted">{{ $orders->count() }} commandes</span>
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[12px]">
                        <thead class="text-[11px] uppercase text-text-muted border-b border-border-subtle">
                            <tr>
                                <th class="pb-2.5">Réf Commande</th>
                                <th class="pb-2.5">Client</th>
                                <th class="pb-2.5">Montant</th>
                                <th class="pb-2.5">Com. Estimée</th>
                                <th class="pb-2.5 text-right">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-subtle">
                            @forelse($orders as $o)
                                <tr>
                                    <td class="py-3 font-mono font-bold text-on-surface">{{ $o->numero_commande }}</td>
                                    <td class="py-3 text-text-muted">{{ $o->user?->prenom }} {{ $o->user?->nom }}</td>
                                    <td class="py-3 font-bold text-on-surface">
                                        {{ number_format($o->montant_total, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="py-3 font-bold text-emerald-600">
                                        {{ number_format((int) round($o->montant_total * 0.03), 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="py-3 text-right">
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 capitalize">
                                            {{ str_replace('_', ' ', $o->statut) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-text-muted">Aucune commande encore enregistrée.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Clients Portefeuille (5 cols) -->
            <div class="lg:col-span-5 bg-surface-container-lowest p-6 rounded-3xl border border-border-subtle shadow-sm">
                <h3 class="text-base font-bold text-on-surface mb-4">Clients Rattachés</h3>
                <div class="space-y-3">
                    @forelse($clients as $c)
                        <div
                            class="p-3 rounded-2xl bg-canvas-background border border-border-subtle text-[12px] flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold">
                                    {{ substr($c->prenom ?? 'C', 0, 1) }}
                                </div>
                                <div>
                                    <span class="font-bold text-on-surface block">{{ $c->prenom }} {{ $c->nom }}</span>
                                    <span class="text-[11px] text-text-muted">+225 {{ $c->telephone }}</span>
                                </div>
                            </div>
                            <span class="text-[11px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded">
                                {{ $c->ville ?? 'Abidjan' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-text-muted text-[12px]">Aucun client rattaché.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
@endsection
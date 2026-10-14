@extends('layouts.admin')

@section('page-title', 'Vue d\'ensemble & Pilotage Financier')

@section('content')
    <div class="space-y-8">

        <!-- ══════════════════════════════════════════════════════════════
                  1. KPI'S FINANCIERS PRINCIPAUX
                ══════════════════════════════════════════════════════════════ -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <!-- Volume Total des Ventes -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">Volume de Ventes</span>
                    <div class="w-10 h-10 rounded-2xl bg-brand-red-subtle text-primary flex items-center justify-center">
                        <i data-lucide="banknote" class="w-5 h-5"></i>
                    </div>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-black text-slate-900 block tracking-tight">
                        {{ number_format($totalSales, 0, ',', ' ') }} <span
                            class="text-sm font-bold text-slate-500">FCFA</span>
                    </span>
                    <span class="text-[11px] text-slate-500 font-semibold mt-1 block">
                        Total brut engagé sur {{ $totalOrdersCount }} commandes
                    </span>
                </div>
            </div>

            <!-- Apports Initiaux Encaissés -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">Apports Encaissés</span>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="wallet" class="w-5 h-5"></i>
                    </div>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-black text-emerald-600 block tracking-tight">
                        {{ number_format($downpaymentCollected, 0, ',', ' ') }} <span
                            class="text-sm font-bold text-slate-500">FCFA</span>
                    </span>
                    <span class="text-[11px] text-emerald-700 font-semibold mt-1 block">
                        Cash liquide perçu à la commande
                    </span>
                </div>
            </div>

            <!-- Encours Crédit BNPL Restant -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">Encours BNPL Financé</span>
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-secondary flex items-center justify-center">
                        <i data-lucide="hourglass" class="w-5 h-5"></i>
                    </div>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-black text-slate-900 block tracking-tight">
                        {{ number_format($financedOutstanding, 0, ',', ' ') }} <span
                            class="text-sm font-bold text-slate-500">FCFA</span>
                    </span>
                    <span class="text-[11px] text-secondary font-semibold mt-1 block">
                        Créances échelonnées en cours d'amortissement
                    </span>
                </div>
            </div>

            <!-- Quittances Emises Aujourd'hui -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">Versements du Jour</span>
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i data-lucide="circle-dollar-sign" class="w-5 h-5"></i>
                    </div>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-black text-amber-600 block tracking-tight">
                        {{ number_format($todayReceiptsTotal, 0, ',', ' ') }} <span
                            class="text-sm font-bold text-slate-500">FCFA</span>
                    </span>
                    <span class="text-[11px] text-slate-500 font-semibold mt-1 block">
                        Wave, Orange Money & Caisse (Aujourd'hui)
                    </span>
                </div>
            </div>

        </div>

        <!-- ══════════════════════════════════════════════════════════════
                  2. COMPTEURS CLÉS PAR ENTITÉS & ACTIONS RAPIDES
                ══════════════════════════════════════════════════════════════ -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Répartition Utilisateurs -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-900 flex items-center gap-2">
                        <i data-lucide="users" class="w-5 h-5 text-secondary"></i>
                        Utilisateurs par Type
                    </h2>
                    <a href="{{ route('admin.users.create') }}"
                        class="text-[11px] font-bold text-primary hover:underline flex items-center gap-1">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i> Créer un compte
                    </a>
                </div>

                <div class="space-y-3">
                    <a href="{{ route('admin.users.clients') }}"
                        class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                                CL
                            </div>
                            <div>
                                <span class="text-[13px] font-bold text-slate-900 block">Clients Particuliers</span>
                                <span class="text-[11px] text-slate-500">Acheteurs BNPL enregistrés</span>
                            </div>
                        </div>
                        <span class="text-base font-black text-slate-900">{{ $clientsCount }}</span>
                    </a>

                    <a href="{{ route('admin.users.commerciaux') }}"
                        class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs">
                                CO
                            </div>
                            <div>
                                <span class="text-[13px] font-bold text-slate-900 block">Commerciaux & Apporteurs</span>
                                <span class="text-[11px] text-slate-500">Équipes terrain avec code agent</span>
                            </div>
                        </div>
                        <span class="text-base font-black text-slate-900">{{ $commerciauxCount }}</span>
                    </a>

                    <a href="{{ route('admin.users.caissiers') }}"
                        class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center font-bold text-xs">
                                CA
                            </div>
                            <div>
                                <span class="text-[13px] font-bold text-slate-900 block">Caissiers d'Agence</span>
                                <span class="text-[11px] text-slate-500">Guichets physiques de paiement</span>
                            </div>
                        </div>
                        <span class="text-base font-black text-slate-900">{{ $caissiersCount }}</span>
                    </a>

                    <a href="{{ route('admin.users.admins') }}"
                        class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-xl bg-purple-100 text-purple-800 flex items-center justify-center font-bold text-xs">
                                AD
                            </div>
                            <div>
                                <span class="text-[13px] font-bold text-slate-900 block">Administrateurs</span>
                                <span class="text-[11px] text-slate-500">Direction & Superviseurs</span>
                            </div>
                        </div>
                        <span class="text-base font-black text-slate-900">{{ $adminsCount }}</span>
                    </a>
                </div>
            </div>

            <!-- État des Commandes & Risques -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-900 flex items-center gap-2">
                        <i data-lucide="shopping-bag" class="w-5 h-5 text-primary"></i>
                        Cycle des Commandes
                    </h2>
                    <a href="{{ route('admin.orders.index') }}" class="text-[11px] font-bold text-primary hover:underline">
                        Voir tout ({{ $totalOrdersCount }})
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-100 text-amber-900">
                        <span class="text-[11px] font-bold block">En Attente KYC</span>
                        <span class="text-2xl font-black mt-1 block">{{ $pendingKycOrders }}</span>
                        <span class="text-[10px] text-amber-700">Pièces à valider</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-100 text-blue-900">
                        <span class="text-[11px] font-bold block">En Cours d'Échéance</span>
                        <span class="text-2xl font-black mt-1 block">{{ $activeOrders }}</span>
                        <span class="text-[10px] text-blue-700">Règlements actifs</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900">
                        <span class="text-[11px] font-bold block">Livrées</span>
                        <span class="text-2xl font-black mt-1 block">{{ $deliveredOrders }}</span>
                        <span class="text-[10px] text-slate-500">Chez le client</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-900">
                        <span class="text-[11px] font-bold block">Soldées à 100%</span>
                        <span class="text-2xl font-black mt-1 block">{{ $settledOrders }}</span>
                        <span class="text-[10px] text-emerald-700">Crédit remboursé</span>
                    </div>
                </div>

                <div
                    class="p-3 rounded-2xl bg-cobalt-subtle text-cobalt-deep text-[12px] flex items-center justify-between">
                    <span>Dossiers KYC en attente de validation :</span>
                    <a href="{{ route('admin.kyc.index') }}"
                        class="px-2.5 py-1 rounded-xl bg-red-600 text-white font-extrabold text-[11px]">
                        {{ $pendingKycCount }} à traiter
                    </a>
                </div>
            </div>

            <!-- Raccourcis & Opérations Directes -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-900 mb-4 flex items-center gap-2">
                        <i data-lucide="zap" class="w-5 h-5 text-emerald-600"></i>
                        Raccourcis & Gestion Rapide
                    </h2>

                    <div class="space-y-2.5">
                        <a href="{{ route('admin.products.create') }}"
                            class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-colors text-[13px] font-bold text-slate-800">
                            <span class="w-8 h-8 rounded-xl bg-red-100 text-primary flex items-center justify-center">
                                <i data-lucide="plus-circle" class="w-4.5 h-4.5"></i>
                            </span>
                            <span>Ajouter un Produit / Kit Maison</span>
                        </a>

                        <a href="{{ route('admin.categories.index') }}"
                            class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-colors text-[13px] font-bold text-slate-800">
                            <span class="w-8 h-8 rounded-xl bg-blue-100 text-secondary flex items-center justify-center">
                                <i data-lucide="layout-grid" class="w-4.5 h-4.5"></i>
                            </span>
                            <span>Gérer les Catégories</span>
                        </a>

                        <a href="{{ route('admin.formulas.index') }}"
                            class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-colors text-[13px] font-bold text-slate-800">
                            <span class="w-8 h-8 rounded-xl bg-emerald-100 text-tertiary flex items-center justify-center">
                                <i data-lucide="percent" class="w-4.5 h-4.5"></i>
                            </span>
                            <span>Ajuster les Formules & Taux BNPL</span>
                        </a>

                        <a href="{{ route('admin.receipts.index') }}"
                            class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-colors text-[13px] font-bold text-slate-800">
                            <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">
                                <i data-lucide="receipt" class="w-4.5 h-4.5"></i>
                            </span>
                            <span>Émettre ou Vérifier une Quittance</span>
                        </a>
                    </div>
                </div>

                <div
                    class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 font-semibold">
                    <span>Catalogue total : <strong>{{ $totalProducts }} articles</strong></span>
                    @if($lowStockProducts > 0)
                        <span class="text-red-600 font-bold flex items-center gap-1">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i> {{ $lowStockProducts }} stocks faibles
                        </span>
                    @endif
                </div>
            </div>

        </div>

        <!-- ══════════════════════════════════════════════════════════════
                  3. DERNIÈRES COMMANDES & DERNIÈRES QUITTANCES
                ══════════════════════════════════════════════════════════════ -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Commandes Récentes (7 cols) -->
            <div class="lg:col-span-7 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2">
                        <i data-lucide="shopping-bag" class="w-5 h-5 text-primary"></i>
                        Dernières commandes
                    </h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-[12px] font-bold text-primary hover:underline">
                        Toutes les commandes
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[12px]">
                        <thead class="text-[11px] uppercase text-slate-400 border-b border-slate-100 pb-2">
                            <tr>
                                <th class="pb-2.5">Réf Commande</th>
                                <th class="pb-2.5">Client</th>
                                <th class="pb-2.5">Total</th>
                                <th class="pb-2.5">Apport</th>
                                <th class="pb-2.5">Statut</th>
                                <th class="pb-2.5 text-right">Détails</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 font-mono font-bold text-slate-900">
                                        {{ $order->numero_commande }}
                                    </td>
                                    <td class="py-3 font-semibold text-slate-700">
                                        {{ $order->user?->nom_complet }}
                                    </td>
                                    <td class="py-3 font-extrabold text-slate-900">
                                        {{ number_format($order->montant_total, 0, ',', ' ') }} F
                                    </td>
                                    <td class="py-3 font-bold text-emerald-600">
                                        {{ number_format($order->apport_initial, 0, ',', ' ') }} F
                                    </td>
                                    <td class="py-3">
                                        @php
                                            $badgeColor = match ($order->statut) {
                                                'en_attente_kyc' => 'bg-amber-100 text-amber-800',
                                                'valide' => 'bg-blue-100 text-blue-800',
                                                'en_cours' => 'bg-purple-100 text-purple-800',
                                                'livre' => 'bg-sky-100 text-sky-800',
                                                'solde' => 'bg-emerald-100 text-emerald-800',
                                                default => 'bg-slate-100 text-slate-700'
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $badgeColor }}">
                                            {{ str_replace('_', ' ', $order->statut) }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 inline-flex items-center">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">Aucune commande enregistrée.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quittances de Paiement Récentes (5 cols) -->
            <div class="lg:col-span-5 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-5 h-5 text-emerald-600"></i>
                        Derniers reçus vérifiés
                    </h3>
                    <a href="{{ route('admin.receipts.index') }}"
                        class="text-[12px] font-bold text-primary hover:underline">
                        Registre complet
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($recentReceipts as $rec)
                        <div
                            class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-[12px] flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="font-mono font-black text-primary text-[13px] bg-red-50 px-2 py-0.5 rounded border border-red-100">
                                        {{ $rec->code_recu }}
                                    </span>
                                    <span class="font-bold text-slate-900">{{ number_format($rec->montant, 0, ',', ' ') }}
                                        FCFA</span>
                                </div>
                                <span class="text-[11px] text-slate-500 block mt-1">
                                    Client : {{ $rec->user?->nom_complet }} · Canal : <strong
                                        class="capitalize">{{ str_replace('_', ' ', $rec->canal) }}</strong>
                                </span>
                            </div>
                            <a href="{{ route('verifier', ['code' => $rec->code_recu]) }}" target="_blank"
                                title="Contrôle public"
                                class="p-2 rounded-xl bg-white hover:bg-slate-200 text-slate-600 transition-colors shadow-sm">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                            </a>
                        </div>
                    @empty
                        <p class="text-slate-400 text-[12px] py-4 text-center">Aucune quittance émise.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
@endsection
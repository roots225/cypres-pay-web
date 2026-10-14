@extends('layouts.admin')

@section('page-title', "Commande {$order->numero_commande}")

@section('content')
<div class="space-y-6">
    
    <!-- En-tête avec Statut & Changement de Statut -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.orders.index') }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center">
                    <i data-lucide="arrow-left" class="w-4.5 h-4.5"></i>
                </a>
                <h2 class="text-xl font-mono font-black text-slate-900">{{ $order->numero_commande }}</h2>
                
                @php
                    $badgeColor = match($order->statut) {
                        'en_attente_kyc' => 'bg-amber-100 text-amber-800',
                        'valide' => 'bg-blue-100 text-blue-800',
                        'en_cours' => 'bg-purple-100 text-purple-800',
                        'livre' => 'bg-sky-100 text-sky-800',
                        'solde' => 'bg-emerald-100 text-emerald-800',
                        'annule' => 'bg-red-100 text-red-800',
                        default => 'bg-slate-100 text-slate-700'
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-[12px] font-black uppercase {{ $badgeColor }}">
                    {{ str_replace('_', ' ', $order->statut) }}
                </span>
            </div>
            <p class="text-[12px] text-slate-500 mt-1">Passée le {{ $order->created_at->format('d/m/Y à H:i') }} · Formule : <strong class="text-slate-800">{{ $order->formula?->nom }}</strong></p>
        </div>

        <!-- Formulaire de Modification Rapide de Statut -->
        <form action="{{ route('admin.orders.update_status', $order) }}" method="POST" class="flex items-center gap-2">
            @csrf
            @method('PATCH')
            <label class="text-[12px] font-bold text-slate-500">Changer statut :</label>
            <select name="statut" class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-[12px] font-bold text-slate-800 outline-none">
                <option value="en_attente_kyc" {{ $order->statut === 'en_attente_kyc' ? 'selected' : '' }}>En attente KYC</option>
                <option value="valide" {{ $order->statut === 'valide' ? 'selected' : '' }}>Validé (Prêt à livrer)</option>
                <option value="en_cours" {{ $order->statut === 'en_cours' ? 'selected' : '' }}>En cours d'échéances</option>
                <option value="livre" {{ $order->statut === 'livre' ? 'selected' : '' }}>Livre au client</option>
                <option value="solde" {{ $order->statut === 'solde' ? 'selected' : '' }}>Solde (Payé à 100%)</option>
                <option value="annule" {{ $order->statut === 'annule' ? 'selected' : '' }}>Annulé</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[12px] font-bold hover:bg-slate-800 transition-colors">
                Appliquer
            </button>
        </form>
    </div>

    <!-- 4 Métriques Financières de la Commande -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-slate-500 uppercase block mb-1">Montant Total</span>
            <span class="text-xl sm:text-2xl font-black text-slate-900 block">{{ number_format($order->montant_total, 0, ',', ' ') }} F</span>
            <span class="text-[10px] text-slate-400">Total panier + majoration</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-slate-500 uppercase block mb-1">Apport Initial</span>
            <span class="text-xl sm:text-2xl font-black text-emerald-600 block">{{ number_format($order->apport_initial, 0, ',', ' ') }} F</span>
            <span class="text-[10px] text-emerald-700 font-semibold">{{ $order->formula?->pourcentage_apport }}% versé</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-slate-500 uppercase block mb-1">Total Déjà Réglé</span>
            <span class="text-xl sm:text-2xl font-black text-secondary block">{{ number_format($totalPaid, 0, ',', ' ') }} F</span>
            <span class="text-[10px] text-slate-400">Échéances acquittées</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-slate-500 uppercase block mb-1">Solde Restant Dû</span>
            <span class="text-xl sm:text-2xl font-black text-primary block">{{ number_format($totalRemaining, 0, ',', ' ') }} F</span>
            <span class="text-[10px] text-slate-400">À recouvrer</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Colonne Gauche : Échéancier & Quittances (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Échéancier d'Amortissement -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2">
                        <i data-lucide="calendar" class="w-5 h-5 text-secondary"></i>
                        Échéancier de Remboursement BNPL
                    </h3>
                    <span class="text-[12px] font-bold text-slate-500">
                        {{ $order->installments->count() }} échéances prévues
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[12px]">
                        <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400">
                            <tr>
                                <th class="p-3">N°</th>
                                <th class="p-3">Date Limite</th>
                                <th class="p-3">Montant Dû</th>
                                <th class="p-3">Payé</th>
                                <th class="p-3">Statut</th>
                                <th class="p-3 text-right">Encaissement</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($order->installments as $inst)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-3 font-bold text-slate-900">#{{ $inst->numero_echeance }}</td>
                                    <td class="p-3 text-slate-600 font-medium">{{ $inst->date_echeance->format('d/m/Y') }}</td>
                                    <td class="p-3 font-black text-slate-900">{{ number_format($inst->montant_du, 0, ',', ' ') }} FCFA</td>
                                    <td class="p-3 font-bold text-emerald-600">{{ number_format($inst->montant_paye, 0, ',', ' ') }} FCFA</td>
                                    <td class="p-3">
                                        @if($inst->statut === 'paye')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Réglé</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">À payer</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right">
                                        @if($inst->statut !== 'paye')
                                            <!-- Bouton Encaisser & Émettre Quittance 6 Caractères -->
                                            <form action="{{ route('admin.orders.mark_installment_paid', $inst) }}" method="POST" class="inline-flex items-center gap-1.5" onsubmit="return confirm('Confirmez-vous l\'encaissement de {{ number_format($inst->montant_du, 0, ',', ' ') }} FCFA ? Un reçu 6 caractères sera généré.');">
                                                @csrf
                                                <select name="canal" class="text-[10px] font-bold border border-slate-200 rounded p-1 bg-white">
                                                    <option value="cash">Espèces</option>
                                                    <option value="wave">Wave</option>
                                                    <option value="orange_money">Orange</option>
                                                    <option value="mtn_momo">MTN</option>
                                                </select>
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] shadow-sm transition-colors">
                                                    Encaisser
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[11px] text-slate-400 font-mono">Quittance émise</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Articles Commandés -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <h3 class="font-extrabold text-base text-slate-900 mb-4 flex items-center gap-2">
                    <i data-lucide="package" class="w-5 h-5 text-primary"></i>
                    Articles Inclus dans la Commande
                </h3>

                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-[13px]">
                            <div class="flex items-center gap-3">
                                <img src="{{ $item->product?->image_principale }}" class="w-12 h-12 rounded-xl object-contain bg-white p-1 border border-slate-200"/>
                                <div>
                                    <span class="font-mono text-[11px] font-bold text-slate-400">[{{ $item->product?->reference }}]</span>
                                    <span class="font-bold text-slate-900 block">{{ $item->product?->nom }}</span>
                                    <span class="text-[11px] text-slate-500">Quantité : {{ $item->quantite }}</span>
                                </div>
                            </div>
                            <span class="font-black text-slate-900">{{ number_format($item->prix_unitaire * $item->quantite, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Quittances Émises pour cette commande -->
            @if($order->receipts->count() > 0)
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                    <h3 class="font-extrabold text-base text-slate-900 mb-3 flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-5 h-5 text-emerald-600"></i>
                        Quittances & Reçus Officiels Émis
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($order->receipts as $rec)
                            <div class="p-3.5 rounded-2xl bg-emerald-50/50 border border-emerald-200 text-[12px] flex items-center justify-between">
                                <div>
                                    <span class="font-mono font-black text-primary text-[13px] block">Code : {{ $rec->code_recu }}</span>
                                    <span class="font-bold text-slate-800">{{ number_format($rec->montant, 0, ',', ' ') }} FCFA</span>
                                    <span class="text-[10px] text-slate-500 block mt-0.5">Émis le {{ $rec->date_versement->format('d/m/Y H:i') }} ({{ $rec->canal }})</span>
                                </div>
                                <a href="{{ route('verifier', ['code' => $rec->code_recu]) }}" target="_blank" class="p-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 shadow-sm flex items-center justify-center" title="Vérifier quittance">
                                    <i data-lucide="external-link" class="w-4 h-4"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <!-- Colonne Droite : Coordonnées Client, Livraison & Signature (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Informations Client -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="font-extrabold text-base text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i data-lucide="user" class="w-5 h-5 text-slate-600"></i>
                    Client Bénéficiaire
                </h3>

                <div class="space-y-2.5 text-[12px]">
                    <div>
                        <span class="text-slate-400 block">Nom complet :</span>
                        <strong class="text-slate-900 text-[13px]">{{ $order->user?->nom_complet }}</strong>
                    </div>

                    <div>
                        <span class="text-slate-400 block">Téléphone contact :</span>
                        <strong class="font-mono text-slate-900">+225 {{ $order->telephone_contact }}</strong>
                    </div>

                    <div>
                        <span class="text-slate-400 block">Adresse de livraison :</span>
                        <strong class="text-slate-900">{{ $order->ville_livraison }}</strong>, {{ $order->adresse_livraison }}
                    </div>

                    <div>
                        <span class="text-slate-400 block">Canal de versement apport :</span>
                        <span class="px-2 py-0.5 rounded font-bold uppercase text-[11px] bg-slate-100 text-slate-800">
                            {{ $order->canal_paiement }}
                        </span>
                    </div>

                    @if($order->notes)
                        <div>
                            <span class="text-slate-400 block">Notes client :</span>
                            <p class="text-slate-600 italic bg-slate-50 p-2.5 rounded-xl">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>

                <div class="pt-3 border-t border-slate-100">
                    <a href="{{ route('admin.users.edit', $order->user) }}" class="text-primary font-bold text-[12px] hover:underline flex items-center gap-1">
                        <span>Voir la fiche client complète</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>

            <!-- Signature Numérique du Contrat -->
            @if($order->user?->kycProfile?->signature_electronique)
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                    <h3 class="font-extrabold text-base text-slate-900 mb-2 flex items-center gap-2">
                        <i data-lucide="pen-tool" class="w-5 h-5 text-purple-600"></i>
                        Signature Numérique du Client
                    </h3>
                    <p class="text-[11px] text-slate-500 mb-3">Signée tactilement sur écran lors du checkout.</p>
                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 flex items-center justify-center">
                        <img src="{{ $order->user->kycProfile->signature_electronique }}" alt="Signature Client" class="max-h-24 max-w-full object-contain" />
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
